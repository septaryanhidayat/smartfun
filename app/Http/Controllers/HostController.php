<?php

namespace App\Http\Controllers;

use App\Models\GamePlayer;
use App\Models\GameSession;
use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Services\GameEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HostController extends Controller
{
    public function __construct(
        protected GameEngineService $gameEngine
    ) {}

    /**
     * Teacher Dashboard with Quiz List & Quick Launch.
     */
    public function index(): View
    {
        $user = Auth::user();

        $quizzes = Quiz::with('questions.options')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('is_public', true);
            })
            ->latest()
            ->get();

        $recentSessions = GameSession::with(['quiz', 'players'])
            ->where('host_user_id', $user->id)
            ->latest()
            ->take(6)
            ->get();

        $totalStudents = GamePlayer::whereHas('gameSession', fn ($q) => $q->where('host_user_id', $user->id))->count();
        $totalSessions = GameSession::where('host_user_id', $user->id)->count();

        return view('host.index', compact('quizzes', 'recentSessions', 'totalStudents', 'totalSessions', 'user'));
    }

    /**
     * Show Quiz Creator page.
     */
    public function createQuiz(): View
    {
        return view('host.quiz-form', [
            'quiz' => null,
            'isEdit' => false,
        ]);
    }

    /**
     * Store new quiz with questions & options.
     */
    public function storeQuiz(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|url',
            'theme' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|string|in:multiple_choice,true_false,poll',
            'questions.*.time_limit' => 'required|integer|min:5|max:120',
            'questions.*.base_points' => 'required|integer|min:100|max:3000',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.correct_option_index' => 'required|integer',
        ]);

        $quiz = Quiz::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']).'-'.Str::random(5),
            'description' => $validated['description'] ?? null,
            'cover_image' => $validated['cover_image'] ?? 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80',
            'theme' => $validated['theme'] ?? 'space',
            'is_public' => true,
        ]);

        foreach ($validated['questions'] as $qIdx => $qData) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $qData['text'],
                'question_type' => $qData['type'],
                'time_limit_seconds' => $qData['time_limit'],
                'base_points' => $qData['base_points'],
                'explanation' => $qData['explanation'] ?? null,
                'order_index' => $qIdx + 1,
            ]);

            $correctIndex = (int) $qData['correct_option_index'];
            foreach ($qData['options'] as $oIdx => $oData) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $oData['text'],
                    'is_correct' => ($oIdx === $correctIndex),
                    'order_index' => $oIdx + 1,
                ]);
            }
        }

        return redirect()->route('host.index')->with('success', 'Kuis "'.$quiz->title.'" berhasil dibuat!');
    }

    /**
     * Show edit quiz page.
     */
    public function editQuiz(Quiz $quiz): View
    {
        $quiz->load('questions.options');

        return view('host.quiz-form', [
            'quiz' => $quiz,
            'isEdit' => true,
        ]);
    }

    /**
     * Delete a quiz.
     */
    public function destroyQuiz(Quiz $quiz): RedirectResponse
    {
        if ($quiz->user_id !== Auth::id()) {
            return redirect()->route('host.index')->with('error', 'Anda hanya dapat menghapus kuis milik Anda sendiri.');
        }

        $quiz->delete();

        return redirect()->route('host.index')->with('success', 'Kuis berhasil dihapus.');
    }

    /**
     * Launch a new live game session.
     */
    public function launch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'mode' => 'required|string|in:live_host,live_student,wayground_map,homework',
            'question_count' => 'nullable|string|in:5,10,20,25,40,50,all',
            'theme' => 'nullable|string|in:space,arcade,fantasy,cyberpunk,lava',
            'powerups' => 'nullable|boolean',
            'sfx' => 'nullable|boolean',
            'bgm' => 'nullable|boolean',
        ]);

        $quiz = Quiz::with('questions')->findOrFail($validated['quiz_id']);
        $host = Auth::user();

        $session = $this->gameEngine->createSession(
            $quiz,
            $host,
            $validated['mode'],
            [
                'question_count' => $validated['question_count'] ?? 'all',
                'theme' => $validated['theme'] ?? $quiz->theme ?? 'arcade',
                'powerups' => (bool) ($request->input('powerups', true)),
                'sfx' => (bool) ($request->input('sfx', true)),
                'bgm' => (bool) ($request->input('bgm', true)),
            ]
        );

        return redirect()->route('host.arena', ['pin' => $session->pin]);
    }

    /**
     * Host Big Screen Arena (Lobby, Questions, Results, Podium).
     */
    public function arena(string $pin): View
    {
        $session = GameSession::with(['quiz.questions.options', 'players'])
            ->where('pin', $pin)
            ->firstOrFail();

        $sessionQuestions = $this->gameEngine->getSessionQuestions($session);
        $totalSessionQuestions = $sessionQuestions->count();
        $joinUrl = url('/join?pin='.$pin);

        return view('host.arena', compact('session', 'sessionQuestions', 'totalSessionQuestions', 'joinUrl'));
    }

    /**
     * Host starts the game session from the lobby.
     */
    public function start(string $pin): JsonResponse
    {
        try {
            $session = $this->gameEngine->startGame($pin);

            return response()->json([
                'success' => true,
                'status' => $session->status,
                'current_question_index' => $session->current_question_index,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Host advances to next question or finishes the game.
     */
    public function next(string $pin): JsonResponse
    {
        try {
            $result = $this->gameEngine->nextQuestion($pin);

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
     * Host ends current question early to show answer distribution.
     */
    public function endQuestion(string $pin): JsonResponse
    {
        try {
            $result = $this->gameEngine->endCurrentQuestion($pin);

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
     * Export session report to CSV.
     */
    public function export(string $pin): StreamedResponse
    {
        $session = GameSession::with(['quiz', 'players.answers.question', 'answers'])->where('pin', $pin)->firstOrFail();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"laporan-smartfun-{$pin}.csv\"",
        ];

        return response()->stream(function () use ($session) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['SMARTFUN REPORT', $session->quiz->title, 'PIN: '.$session->pin, 'MODE: '.$session->mode]);
            fputcsv($file, []);
            fputcsv($file, ['Peringkat', 'Nama Siswa', 'Avatar', 'Skor Akhir', 'Streak Tertinggi', 'Jumlah Jawaban Benar']);

            $players = $session->players()->orderByDesc('total_score')->get();
            $rank = 1;

            foreach ($players as $p) {
                $correctCount = $p->answers()->where('is_correct', true)->count();
                fputcsv($file, [
                    $p->final_rank ?? $rank++,
                    $p->nickname,
                    $p->avatar_url,
                    $p->total_score,
                    $p->current_streak,
                    $correctCount,
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    /**
     * Helper to get or create demo host user.
     */
    protected function getOrCreateHostUser(): User
    {
        return User::firstOrCreate(
            ['email' => 'guru@smartfun.test'],
            [
                'name' => 'Guru SmartFun',
                'password' => bcrypt('password123'),
            ]
        );
    }
}
