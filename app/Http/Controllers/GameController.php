<?php

namespace App\Http\Controllers;

use App\Models\GamePlayer;
use App\Models\GameSession;
use App\Models\Quiz;
use App\Services\GameEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GameController extends Controller
{
    public function __construct(
        protected GameEngineService $gameEngine
    ) {}

    /**
     * Landing page with PIN input & featured quizzes.
     */
    public function index(): View
    {
        $featuredQuizzes = Quiz::with('questions')
            ->where('is_public', true)
            ->latest()
            ->take(6)
            ->get();

        return view('landing', compact('featuredQuizzes'));
    }

    /**
     * Join game form (PIN & Avatar picker).
     */
    public function joinForm(Request $request): View
    {
        $pin = $request->query('pin', '');

        $avatars = $this->getAvatarList();

        return view('game.join', compact('pin', 'avatars'));
    }

    /**
     * Process player joining a session.
     */
    public function join(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pin' => 'required|string|size:6',
            'nickname' => 'required|string|min:2|max:30',
            'avatar' => 'required|string|max:50',
        ]);

        $session = GameSession::where('pin', $validated['pin'])->first();

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'PIN permainan tidak ditemukan. Periksa kembali PIN Anda.',
            ], 404);
        }

        if ($session->status === 'finished') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi permainan ini sudah selesai.',
            ], 400);
        }

        try {
            $player = $this->gameEngine->joinGame(
                $validated['pin'],
                $validated['nickname'],
                $validated['avatar']
            );

            // Store in session for web requests
            session([
                'smartfun_player_id' => $player->id,
                'smartfun_pin' => $validated['pin'],
            ]);

            return response()->json([
                'success' => true,
                'player' => [
                    'id' => $player->id,
                    'nickname' => $player->nickname,
                    'avatar' => $player->avatar_url,
                ],
                'redirect' => route('game.play', ['pin' => $validated['pin']]),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Student Game Arena View.
     */
    public function play(string $pin): View
    {
        $session = GameSession::with(['quiz.questions.options', 'players'])
            ->where('pin', $pin)
            ->firstOrFail();

        $playerId = session('smartfun_player_id');
        $player = null;

        if ($playerId) {
            $player = GamePlayer::find($playerId);
        }

        // If no player in session, fallback to latest or prompt rejoin
        if (! $player || $player->game_session_id !== $session->id) {
            return view('game.join', [
                'pin' => $pin,
                'avatars' => $this->getAvatarList(),
                'rejoinMessage' => 'Silakan masukkan nama Anda untuk bergabung ke dalam sesi ini.',
            ]);
        }

        $avatars = $this->getAvatarList();
        $initialQuestion = $this->gameEngine->getInitialQuestionForPlayer($session, $player);
        $answeredQuestionIds = $player->answers()->pluck('question_id')->all();
        $initialRank = GamePlayer::where('game_session_id', $session->id)
            ->where('total_score', '>', $player->total_score)
            ->count() + 1;
        $initialLeaderboard = $this->gameEngine->getLiveLeaderboard($pin, 5);
        $sessionQuestions = $this->gameEngine->getSessionQuestions($session);
        $totalQuestionsCount = $sessionQuestions->count();
        $isCompleted = ($session->status === 'finished') || ($session->mode !== 'live_host' && $initialQuestion === null && count($answeredQuestionIds) >= $totalQuestionsCount);

        return view('game.arena', compact(
            'session',
            'player',
            'avatars',
            'initialQuestion',
            'answeredQuestionIds',
            'initialRank',
            'initialLeaderboard',
            'isCompleted',
            'totalQuestionsCount'
        ));
    }

    /**
     * Submit an answer from the student arena.
     */
    public function submitAnswer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_id' => 'required|integer',
            'question_id' => 'required|integer',
            'option_id' => 'nullable|integer',
            'response_time_ms' => 'required|integer|min:0',
            'text_answer' => 'nullable|string',
            'active_powerup' => 'nullable|string',
        ]);

        try {
            $result = $this->gameEngine->submitAnswer(
                $validated['player_id'],
                $validated['question_id'],
                $validated['option_id'] ?? null,
                $validated['response_time_ms'],
                $validated['text_answer'] ?? null,
                $validated['active_powerup'] ?? null
            );

            return response()->json([
                'success' => true,
                'result' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Use 50-50 Eliminator power-up.
     */
    public function useFiftyFifty(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => 'required|integer',
        ]);

        $eliminatedOptionIds = $this->gameEngine->getFiftyFiftyEliminations($validated['question_id']);

        return response()->json([
            'success' => true,
            'eliminated_options' => $eliminatedOptionIds,
        ]);
    }

    /**
     * Get live session state for client polling.
     */
    public function state(string $pin): JsonResponse
    {
        $status = $this->gameEngine->getGameStatus($pin);

        return response()->json($status);
    }

    /**
     * Real-time Server-Sent Events (SSE) Stream for zero-dependency instant pushes.
     */
    public function stream(string $pin): StreamedResponse
    {
        return new StreamedResponse(function () use ($pin) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');

            $lastStateHash = '';
            $startTime = time();

            // Stream for up to 25 seconds per request (then browser auto-reconnects smoothly)
            while (time() - $startTime < 25) {
                if (connection_aborted()) {
                    break;
                }

                $state = $this->gameEngine->getGameStatus($pin);
                $stateHash = md5(json_encode($state));

                if ($stateHash !== $lastStateHash) {
                    $lastStateHash = $stateHash;
                    echo 'data: '.json_encode($state)."\n\n";
                    ob_flush();
                    flush();
                }

                usleep(800000); // 800ms heartbeat
            }
        });
    }

    /**
     * List of tactile arcade avatars.
     *
     * @return array<int, array{id: string, name: string, emoji: string, bg: string}>
     */
    protected function getAvatarList(): array
    {
        return [
            ['id' => 'fox_spark', 'name' => 'Rubah Kilat', 'emoji' => '🦊', 'bg' => 'from-amber-500 to-orange-600'],
            ['id' => 'robot_nova', 'name' => 'Robo Nova', 'emoji' => '🤖', 'bg' => 'from-cyan-500 to-blue-600'],
            ['id' => 'astro_star', 'name' => 'Kosmonot Bintang', 'emoji' => '🚀', 'bg' => 'from-purple-500 to-indigo-600'],
            ['id' => 'wizard_cloud', 'name' => 'Penyihir Awan', 'emoji' => '🧙‍♂️', 'bg' => 'from-violet-500 to-fuchsia-600'],
            ['id' => 'cat_ninja', 'name' => 'Kucing Ninja', 'emoji' => '🐱', 'bg' => 'from-emerald-500 to-teal-600'],
            ['id' => 'lion_gold', 'name' => 'Singa Emas', 'emoji' => '🦁', 'bg' => 'from-yellow-400 to-amber-600'],
            ['id' => 'panda_zen', 'name' => 'Panda Sakti', 'emoji' => '🐼', 'bg' => 'from-slate-600 to-slate-800'],
            ['id' => 'alien_neon', 'name' => 'Alien Neon', 'emoji' => '👾', 'bg' => 'from-lime-400 to-emerald-600'],
            ['id' => 'dragon_fire', 'name' => 'Naga Cilik', 'emoji' => '🐉', 'bg' => 'from-red-500 to-rose-700'],
            ['id' => 'owl_wise', 'name' => 'Burung Hantu Bijak', 'emoji' => '🦉', 'bg' => 'from-amber-600 to-stone-700'],
            ['id' => 'thunder_bolt', 'name' => 'Petir Sonic', 'emoji' => '⚡', 'bg' => 'from-blue-400 to-indigo-600'],
            ['id' => 'unicorn_prism', 'name' => 'Unicorn Pelangi', 'emoji' => '🦄', 'bg' => 'from-pink-400 to-purple-600'],
            ['id' => 'monkey_wit', 'name' => 'Monyet Cerdik', 'emoji' => '🐵', 'bg' => 'from-amber-700 to-yellow-600'],
            ['id' => 'shark_wave', 'name' => 'Hiu Gelombang', 'emoji' => '🦈', 'bg' => 'from-blue-600 to-cyan-700'],
            ['id' => 'dino_rex', 'name' => 'Dino Perkasa', 'emoji' => '🦖', 'bg' => 'from-emerald-600 to-green-800'],
            ['id' => 'tiger_brave', 'name' => 'Harimau Berani', 'emoji' => '🐯', 'bg' => 'from-orange-500 to-red-600'],
        ];
    }
}
