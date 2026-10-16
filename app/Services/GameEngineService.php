<?php

namespace App\Services;

use App\Events\AnswerSubmitted;
use App\Events\GameFinished;
use App\Events\GameStarted;
use App\Events\PlayerJoined;
use App\Events\QuestionEnded;
use App\Events\QuestionStarted;
use App\Models\GameAnswer;
use App\Models\GamePlayer;
use App\Models\GameSession;
use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GameEngineService
{
    /**
     * Get the ordered questions for this game session based on settings (e.g. question_count / question_ids).
     *
     * @return Collection<int, Question>
     */
    public function getSessionQuestions(GameSession $session)
    {
        $selectedIds = $session->settings['question_ids'] ?? null;
        if (! empty($selectedIds) && is_array($selectedIds)) {
            $questions = $session->quiz->questions;

            return $questions->whereIn('id', $selectedIds)
                ->sortBy(fn ($q) => array_search($q->id, $selectedIds))
                ->values();
        }

        return $session->quiz->questions->sortBy('order_index')->values();
    }

    /**
     * Create a new game session.
     *
     * @param  array<string, mixed>  $settings
     */
    public function createSession(Quiz $quiz, User $host, string $mode = 'live_student', array $settings = []): GameSession
    {
        $pin = $this->generateUniquePin();

        $questionCount = $settings['question_count'] ?? 'all';
        $theme = $settings['theme'] ?? $quiz->theme ?? 'arcade';
        if (! in_array($theme, ['space', 'arcade', 'fantasy', 'cyberpunk', 'lava'])) {
            $theme = 'arcade';
        }

        $allQuestions = $quiz->questions()->orderBy('order_index')->get();
        if ($questionCount !== 'all' && is_numeric($questionCount) && (int) $questionCount > 0) {
            $count = min((int) $questionCount, $allQuestions->count());
            $selectedQuestions = $allQuestions->take($count);
        } else {
            $selectedQuestions = $allQuestions;
        }

        $selectedQuestionIds = $selectedQuestions->pluck('id')->all();

        $sessionSettings = array_merge([
            'show_memes' => true,
            'powerups' => true,
            'sfx' => true,
            'bgm' => true,
        ], $settings);
        $sessionSettings['theme'] = $theme;
        $sessionSettings['question_count'] = count($selectedQuestionIds);
        $sessionSettings['question_ids'] = $selectedQuestionIds;

        $session = GameSession::create([
            'quiz_id' => $quiz->id,
            'host_user_id' => $host->id,
            'pin' => $pin,
            'mode' => $mode,
            'status' => 'lobby',
            'current_question_index' => 0,
            'settings' => $sessionSettings,
        ]);

        try {
            Cache::put("game:{$pin}:session", $session->id, 86400);
        } catch (\Throwable $e) {
        }

        return $session;
    }

    /**
     * Join a player into the game session.
     */
    public function joinGame(string $pin, string $nickname, string $avatar): GamePlayer
    {
        $session = GameSession::where('pin', $pin)->firstOrFail();

        if ($session->status === 'finished') {
            throw new \RuntimeException('Sesi kuis sudah berakhir.');
        }

        // Clean nickname
        $cleanNickname = trim(strip_tags($nickname));
        if ($cleanNickname === '') {
            $cleanNickname = 'Pemain '.rand(100, 999);
        }

        // Ensure unique nickname for this session
        $existing = GamePlayer::where('game_session_id', $session->id)
            ->where('nickname', $cleanNickname)
            ->first();

        if ($existing) {
            $cleanNickname .= '_'.rand(10, 99);
        }

        $player = GamePlayer::create([
            'game_session_id' => $session->id,
            'nickname' => $cleanNickname,
            'avatar_url' => $avatar,
            'total_score' => 0,
            'current_streak' => 0,
            'is_connected' => true,
        ]);

        $totalPlayers = GamePlayer::where('game_session_id', $session->id)->count();

        // Update Cache/Redis (safe fallback)
        try {
            Cache::put("game:{$pin}:player:{$player->id}", [
                'id' => $player->id,
                'nickname' => $player->nickname,
                'avatar' => $player->avatar_url,
                'score' => 0,
                'streak' => 0,
            ], 86400);
        } catch (\Throwable $e) {
        }

        // Broadcast PlayerJoined
        try {
            broadcast(new PlayerJoined($pin, [
                'playerId' => $player->id,
                'nickname' => $player->nickname,
                'avatar' => $player->avatar_url,
                'totalPlayers' => $totalPlayers,
            ]));
        } catch (\Throwable $e) {
            // Silently catch if Reverb is not running
        }

        return $player;
    }

    /**
     * Start the game from lobby to active.
     */
    public function startGame(string $pin): GameSession
    {
        $session = GameSession::with('quiz.questions.options')->where('pin', $pin)->firstOrFail();
        $session->update([
            'status' => 'active',
            'started_at' => now(),
            'current_question_index' => 1,
        ]);

        $questions = $this->getSessionQuestions($session);
        $totalQuestions = $questions->count();
        $activeTheme = $session->settings['theme'] ?? $session->quiz->theme ?? 'arcade';

        try {
            broadcast(new GameStarted($pin, [
                'totalQuestions' => $totalQuestions,
                'mode' => $session->mode,
                'initialTheme' => $activeTheme,
            ]));
        } catch (\Throwable $e) {
        }

        // Start question 1
        $this->startQuestion($pin, 1);

        return $session;
    }

    /**
     * Start a specific question by 1-based order index.
     *
     * @return array<string, mixed>|null
     */
    public function startQuestion(string $pin, int $orderIndex): ?array
    {
        $session = GameSession::with('quiz.questions.options')->where('pin', $pin)->firstOrFail();
        $questions = $this->getSessionQuestions($session);
        $totalQuestions = $questions->count();

        // 1-based index from session questions
        $question = $questions->get($orderIndex - 1);

        if (! $question) {
            return null;
        }

        $session->update(['current_question_index' => $orderIndex]);

        // Anti-cheat: strip is_correct from client options
        $sanitizedOptions = $question->options->map(fn ($opt) => [
            'id' => $opt->id,
            'text' => $opt->option_text,
            'media' => $opt->media_url,
            'order_index' => $opt->order_index,
        ])->values()->all();

        $payload = [
            'questionIndex' => $orderIndex,
            'totalQuestions' => $totalQuestions,
            'questionId' => $question->id,
            'text' => $question->question_text,
            'questionType' => $question->question_type,
            'media' => $question->media_url,
            'mediaType' => $question->media_type,
            'options' => $sanitizedOptions,
            'timeLimit' => $question->time_limit_seconds,
            'basePoints' => $question->base_points,
            'startedAt' => now()->timestamp,
        ];

        try {
            Cache::put("game:{$pin}:current_question", $payload, 86400);
        } catch (\Throwable $e) {
        }

        try {
            broadcast(new QuestionStarted($pin, $payload));
        } catch (\Throwable $e) {
        }

        return $payload;
    }

    /**
     * Submit an answer for a question.
     *
     * @return array<string, mixed>
     */
    public function submitAnswer(
        int $playerId,
        int $questionId,
        ?int $optionId,
        int $timeMs,
        ?string $textAnswer = null,
        ?string $activePowerUp = null
    ): array {
        $player = GamePlayer::with('gameSession.quiz.questions.options')->findOrFail($playerId);
        $session = $player->gameSession;
        $session->loadMissing('quiz.questions.options');
        $pin = $session->pin;

        // Idempotency check: check database first to prevent duplicate answers
        $existing = GameAnswer::where('game_session_id', $session->id)
            ->where('game_player_id', $playerId)
            ->where('question_id', $questionId)
            ->first();

        if ($existing) {
            $question = Question::with('options')->find($questionId);
            $correctOption = $question?->options->firstWhere('is_correct', true);
            $questions = $this->getSessionQuestions($session);
            $totalQuestions = $questions->count();
            $currentSessionQIdx = $questions->search(fn ($q) => $q->id === $questionId);
            $nextQuestion = ($currentSessionQIdx !== false && $currentSessionQIdx + 1 < $totalQuestions)
                ? $questions->get($currentSessionQIdx + 1)
                : null;

            $nextQuestionPayload = null;
            if ($nextQuestion) {
                $nextQuestionPayload = [
                    'questionIndex' => $currentSessionQIdx + 2,
                    'totalQuestions' => $totalQuestions,
                    'questionId' => $nextQuestion->id,
                    'text' => $nextQuestion->question_text,
                    'questionType' => $nextQuestion->question_type,
                    'media' => $nextQuestion->media_url,
                    'mediaType' => $nextQuestion->media_type,
                    'options' => $nextQuestion->options->map(fn ($opt) => [
                        'id' => $opt->id,
                        'text' => $opt->option_text,
                        'media' => $opt->media_url,
                        'order_index' => $opt->order_index,
                    ])->values()->all(),
                    'timeLimit' => $nextQuestion->time_limit_seconds,
                    'basePoints' => $nextQuestion->base_points,
                ];
            }

            $playerRank = GamePlayer::where('game_session_id', $session->id)
                ->where('total_score', '>', $player->total_score)
                ->count() + 1;
            $totalPlayers = GamePlayer::where('game_session_id', $session->id)->count();

            return [
                'already_answered' => true,
                'is_correct' => (bool) $existing->is_correct,
                'points_awarded' => (int) $existing->points_awarded,
                'total_score' => $player->total_score,
                'current_streak' => $player->current_streak,
                'correct_option_id' => $correctOption ? $correctOption->id : null,
                'explanation' => $question?->explanation,
                'player_rank' => $playerRank,
                'total_players' => $totalPlayers,
                'leaderboard' => $this->getLiveLeaderboard($pin),
                'next_question' => $nextQuestionPayload,
                'is_last_question' => $nextQuestion === null,
            ];
        }

        try {
            $lockKey = "game:{$pin}:answered:{$playerId}:{$questionId}";
            Cache::put($lockKey, 1, 300);
        } catch (\Throwable $e) {
        }

        $question = Question::with('options')->findOrFail($questionId);

        // Clamp response_time_ms to realistic question time limit to prevent integer overflow
        $maxAllowedMs = max(10000, (($question->time_limit_seconds ?? 30) + 10) * 1000);
        $timeMs = (int) max(0, min($timeMs, $maxAllowedMs));

        $isCorrect = false;
        $correctOption = $question->options->firstWhere('is_correct', true);

        if ($optionId) {
            $chosenOption = $question->options->firstWhere('id', $optionId);
            $isCorrect = $chosenOption ? (bool) $chosenOption->is_correct : false;
        } elseif ($textAnswer !== null && $correctOption) {
            $isCorrect = strtolower(trim($textAnswer)) === strtolower(trim($correctOption->option_text));
        }

        // Apply time freeze power-up (if active, simulate 0ms response time for max speed bonus)
        $effectiveTimeMs = ($activePowerUp === 'time_freeze') ? 0 : $timeMs;

        // Calculate score
        $points = 0;
        $newStreak = $player->current_streak;

        if ($isCorrect) {
            $isDouble = ($activePowerUp === '2x_multiplier');
            $points = $this->calculateScore(
                $question->base_points,
                $question->time_limit_seconds,
                $effectiveTimeMs,
                $player->current_streak,
                $isDouble
            );
            $newStreak++;
        } else {
            // Check streak shield power-up
            if ($activePowerUp === 'streak_shield') {
                // Streak is protected!
            } else {
                $newStreak = 0;
            }
        }

        // Check power-up unlocked reward (e.g. at 3 streak, 6 streak, etc.)
        $unlockedPowerUp = null;
        if ($isCorrect && $newStreak > 0 && ($newStreak % 3 === 0)) {
            $powerUps = ['2x_multiplier', '50_50', 'streak_shield', 'time_freeze'];
            $unlockedPowerUp = $powerUps[array_rand($powerUps)];
        }

        // Save answer to database
        $answer = GameAnswer::create([
            'game_session_id' => $session->id,
            'game_player_id' => $player->id,
            'question_id' => $question->id,
            'option_id' => $optionId,
            'text_answer' => $textAnswer,
            'is_correct' => $isCorrect,
            'response_time_ms' => $timeMs,
            'points_awarded' => $points,
            'streak_multiplier' => min(2.0, 1.0 + ($player->current_streak * 0.1)),
        ]);

        // Update player score & streak
        $player->total_score += $points;
        $player->current_streak = $newStreak;
        $player->save();

        // Count answers for this question
        $answeredCount = GameAnswer::where('game_session_id', $session->id)
            ->where('question_id', $question->id)
            ->count();
        $totalPlayers = GamePlayer::where('game_session_id', $session->id)->count();

        // Broadcast AnswerSubmitted event
        try {
            broadcast(new AnswerSubmitted($pin, [
                'playerId' => $player->id,
                'questionId' => $question->id,
                'answeredCount' => $answeredCount,
                'totalPlayers' => $totalPlayers,
            ]));
        } catch (\Throwable $e) {
        }

        // Find next question in session for student-paced progression
        $questions = $this->getSessionQuestions($session);
        $totalQuestions = $questions->count();
        $currentSessionQIdx = $questions->search(fn ($q) => $q->id === $question->id);
        $nextQuestion = ($currentSessionQIdx !== false && $currentSessionQIdx + 1 < $totalQuestions)
            ? $questions->get($currentSessionQIdx + 1)
            : null;

        $nextQuestionPayload = null;
        if ($nextQuestion) {
            $nextQuestionPayload = [
                'questionIndex' => $currentSessionQIdx + 2,
                'totalQuestions' => $totalQuestions,
                'questionId' => $nextQuestion->id,
                'text' => $nextQuestion->question_text,
                'questionType' => $nextQuestion->question_type,
                'media' => $nextQuestion->media_url,
                'mediaType' => $nextQuestion->media_type,
                'options' => $nextQuestion->options->map(fn ($opt) => [
                    'id' => $opt->id,
                    'text' => $opt->option_text,
                    'media' => $opt->media_url,
                    'order_index' => $opt->order_index,
                ])->values()->all(),
                'timeLimit' => $nextQuestion->time_limit_seconds,
                'basePoints' => $nextQuestion->base_points,
            ];
        }

        $playerRank = GamePlayer::where('game_session_id', $session->id)
            ->where('total_score', '>', $player->total_score)
            ->count() + 1;
        $liveLeaderboard = $this->getLiveLeaderboard($pin);

        return [
            'is_correct' => $isCorrect,
            'points_awarded' => $points,
            'total_score' => $player->total_score,
            'current_streak' => $player->current_streak,
            'correct_option_id' => $correctOption ? $correctOption->id : null,
            'explanation' => $question->explanation,
            'unlocked_power_up' => $unlockedPowerUp,
            'player_rank' => $playerRank,
            'total_players' => $totalPlayers,
            'leaderboard' => $liveLeaderboard,
            'next_question' => $nextQuestionPayload,
            'is_last_question' => $nextQuestion === null,
        ];
    }

    /**
     * Calculate score based on formula from blueprint:
     * TimeBonus = max(0, 1 - (ResponseTimeMs / (TimeLimitMs * 2)))
     * FinalPoints = BasePoints * TimeBonus * min(2.0, 1 + (Streak * 0.1))
     */
    public function calculateScore(
        int $basePoints,
        int $timeLimitSeconds,
        int $timeMs,
        int $streak,
        bool $isDouble = false
    ): int {
        $timeLimitMs = max(1, $timeLimitSeconds * 1000);
        $timeBonus = max(0.0, 1.0 - ($timeMs / ($timeLimitMs * 2)));
        $streakMultiplier = min(2.0, 1.0 + ($streak * 0.1));

        $points = (int) round($basePoints * $timeBonus * $streakMultiplier);

        if ($isDouble) {
            $points *= 2;
        }

        return max(50, $points); // Minimum 50 points for correct answer
    }

    /**
     * End current question on host screen, show answer stats.
     *
     * @return array<string, mixed>
     */
    public function endCurrentQuestion(string $pin, ?int $questionId = null): array
    {
        $session = GameSession::with('quiz.questions.options')->where('pin', $pin)->firstOrFail();
        $questions = $this->getSessionQuestions($session);

        $question = null;
        if ($questionId) {
            $question = $questions->firstWhere('id', $questionId) ?? Question::with('options')->find($questionId);
        }
        if (! $question && $session->current_question_index > 0 && $session->current_question_index <= $questions->count()) {
            $question = $questions->get($session->current_question_index - 1);
        }
        if (! $question) {
            $question = $questions->first();
        }

        if (! $question) {
            return [];
        }

        $correctOption = $question->options->firstWhere('is_correct', true);

        // Calculate distribution of answers
        $distribution = [];
        $totalAnswers = 0;
        foreach ($question->options as $opt) {
            $count = GameAnswer::where('game_session_id', $session->id)
                ->where('question_id', $question->id)
                ->where('option_id', $opt->id)
                ->count();
            $distribution[$opt->id] = $count;
            $totalAnswers += $count;
        }

        $topFive = $this->getLiveLeaderboard($pin, 5);

        $payload = [
            'questionId' => $question->id,
            'correctOptionId' => $correctOption ? $correctOption->id : null,
            'explanation' => $question->explanation,
            'topFive' => $topFive,
            'answerDistribution' => $distribution,
            'totalAnswers' => $totalAnswers,
        ];

        try {
            broadcast(new QuestionEnded($pin, $payload));
        } catch (\Throwable $e) {
        }

        return $payload;
    }

    /**
     * Advance to next question or finish game.
     *
     * @return array<string, mixed>
     */
    public function nextQuestion(string $pin): array
    {
        $session = GameSession::with('quiz.questions')->where('pin', $pin)->firstOrFail();
        $questions = $this->getSessionQuestions($session);
        $totalQuestions = $questions->count();
        $nextIndex = $session->current_question_index + 1;

        if ($nextIndex > $totalQuestions) {
            return $this->finishGame($pin);
        }

        $questionPayload = $this->startQuestion($pin, $nextIndex);

        return [
            'action' => 'next_question',
            'data' => $questionPayload,
        ];
    }

    /**
     * Finish the game session, rank players, build podium.
     *
     * @return array<string, mixed>
     */
    public function finishGame(string $pin): array
    {
        $session = GameSession::where('pin', $pin)->firstOrFail();
        $session->update([
            'status' => 'finished',
            'ended_at' => now(),
        ]);

        // Rank all players
        $players = GamePlayer::where('game_session_id', $session->id)
            ->orderByDesc('total_score')
            ->orderBy('created_at')
            ->get();

        $rank = 1;
        $podium = [];
        $leaderboard = [];
        foreach ($players as $p) {
            $p->final_rank = $rank;
            $p->save();

            $pData = [
                'rank' => $rank,
                'id' => $p->id,
                'nickname' => $p->nickname,
                'avatar' => $p->avatar_url,
                'score' => $p->total_score,
                'streak' => $p->current_streak,
            ];

            if ($rank <= 3) {
                $podium[] = $pData;
            }
            $leaderboard[] = $pData;
            $rank++;
        }

        // Summary Analytics
        $totalAnswers = GameAnswer::where('game_session_id', $session->id)->count();
        $correctAnswers = GameAnswer::where('game_session_id', $session->id)->where('is_correct', true)->count();
        $accuracyRate = $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 1) : 0.0;
        $highestStreak = GamePlayer::where('game_session_id', $session->id)->max('current_streak') ?? 0;

        $summary = [
            'totalPlayers' => $players->count(),
            'totalQuestions' => $this->getSessionQuestions($session)->count(),
            'accuracyRate' => $accuracyRate,
            'highestStreak' => $highestStreak,
        ];

        $payload = [
            'podium' => $podium,
            'leaderboard' => $leaderboard,
            'summaryAnalytics' => $summary,
        ];

        try {
            broadcast(new GameFinished($pin, $payload));
        } catch (\Throwable $e) {
        }

        return [
            'action' => 'game_finished',
            'data' => $payload,
        ];
    }

    /**
     * Get live leaderboard for all players in the game session.
     *
     * @return array<int, array{rank: int, id: int, nickname: string, avatar: ?string, score: int, streak: int}>
     */
    public function getLiveLeaderboard(string $pin, ?int $limit = null): array
    {
        $session = GameSession::where('pin', $pin)->first();
        if (! $session) {
            return [];
        }

        $query = GamePlayer::where('game_session_id', $session->id)
            ->orderByDesc('total_score')
            ->orderBy('created_at');

        if ($limit !== null) {
            $query->limit($limit);
        }

        $players = $query->get();

        $ranked = [];
        $rank = 1;
        foreach ($players as $p) {
            $ranked[] = [
                'rank' => $rank++,
                'id' => $p->id,
                'nickname' => $p->nickname,
                'avatar' => $p->avatar_url,
                'score' => $p->total_score,
                'streak' => $p->current_streak,
            ];
        }

        return $ranked;
    }

    /**
     * Get initial or next unanswered question for a player.
     *
     * @return array<string, mixed>|null
     */
    public function getInitialQuestionForPlayer(GameSession $session, GamePlayer $player): ?array
    {
        $answeredQuestionIds = $player->answers()->pluck('question_id')->all();
        $questions = $this->getSessionQuestions($session);
        $totalQuestions = $questions->count();

        // In Host-paced mode (Kahoot), question is strictly synchronized with host
        if ($session->mode === 'live_host') {
            if ($session->status !== 'active' || $session->current_question_index <= 0 || $session->current_question_index > $totalQuestions) {
                return null;
            }

            $currentQ = $questions->get($session->current_question_index - 1);
            if (! $currentQ || in_array($currentQ->id, $answeredQuestionIds)) {
                return null;
            }

            return [
                'questionIndex' => $session->current_question_index,
                'totalQuestions' => $totalQuestions,
                'questionId' => $currentQ->id,
                'text' => $currentQ->question_text,
                'questionType' => $currentQ->question_type,
                'media' => $currentQ->media_url,
                'mediaType' => $currentQ->media_type,
                'options' => $currentQ->options->map(fn ($opt) => [
                    'id' => $opt->id,
                    'text' => $opt->option_text,
                    'media' => $opt->media_url,
                    'order_index' => $opt->order_index,
                ])->values()->all(),
                'timeLimit' => $currentQ->time_limit_seconds,
                'basePoints' => $currentQ->base_points,
            ];
        }

        $unansweredQuestion = $questions->first(fn ($q) => ! in_array($q->id, $answeredQuestionIds));

        if (! $unansweredQuestion) {
            return null;
        }

        $orderIndex = $questions->search(fn ($q) => $q->id === $unansweredQuestion->id) + 1;

        return [
            'questionIndex' => $orderIndex,
            'totalQuestions' => $totalQuestions,
            'questionId' => $unansweredQuestion->id,
            'text' => $unansweredQuestion->question_text,
            'questionType' => $unansweredQuestion->question_type,
            'media' => $unansweredQuestion->media_url,
            'mediaType' => $unansweredQuestion->media_type,
            'options' => $unansweredQuestion->options->map(fn ($opt) => [
                'id' => $opt->id,
                'text' => $opt->option_text,
                'media' => $opt->media_url,
                'order_index' => $opt->order_index,
            ])->values()->all(),
            'timeLimit' => $unansweredQuestion->time_limit_seconds,
            'basePoints' => $unansweredQuestion->base_points,
        ];
    }

    /**
     * Get 50-50 options to eliminate for a student.
     *
     * @return array<int, int> Two wrong option IDs
     */
    public function getFiftyFiftyEliminations(int $questionId): array
    {
        $question = Question::with('options')->findOrFail($questionId);
        $wrongOptions = $question->options->where('is_correct', false)->pluck('id')->all();
        shuffle($wrongOptions);

        return array_slice($wrongOptions, 0, 2);
    }

    /**
     * Get current status and state of the session for syncing.
     *
     * @return array<string, mixed>
     */
    public function getGameStatus(string $pin): array
    {
        $session = GameSession::with(['quiz.questions.options', 'players'])->where('pin', $pin)->first();

        if (! $session) {
            return ['error' => 'Sesi tidak ditemukan'];
        }

        $questions = $this->getSessionQuestions($session);
        $totalQuestions = $questions->count();

        $currentQuestion = null;
        if ($session->status === 'active' && $session->current_question_index > 0 && $session->current_question_index <= $totalQuestions) {
            $q = $questions->get($session->current_question_index - 1);
            if ($q) {
                $currentQuestion = [
                    'questionIndex' => $session->current_question_index,
                    'totalQuestions' => $totalQuestions,
                    'questionId' => $q->id,
                    'text' => $q->question_text,
                    'questionType' => $q->question_type,
                    'media' => $q->media_url,
                    'mediaType' => $q->media_type,
                    'options' => $q->options->map(fn ($o) => [
                        'id' => $o->id,
                        'text' => $o->option_text,
                        'media' => $o->media_url,
                        'order_index' => $o->order_index,
                    ])->values()->all(),
                    'timeLimit' => $q->time_limit_seconds,
                    'basePoints' => $q->base_points,
                ];
            }
        }

        $answeredCount = 0;
        if ($currentQuestion) {
            $answeredCount = GameAnswer::where('game_session_id', $session->id)
                ->where('question_id', $currentQuestion['questionId'])
                ->count();
        }

        return [
            'pin' => $session->pin,
            'status' => $session->status,
            'mode' => $session->mode,
            'theme' => $session->settings['theme'] ?? $session->quiz->theme ?? 'arcade',
            'settings' => $session->settings,
            'quizTitle' => $session->quiz->title,
            'currentQuestionIndex' => $session->current_question_index,
            'totalQuestions' => $totalQuestions,
            'currentQuestion' => $currentQuestion,
            'answeredCount' => $answeredCount,
            'players' => $session->players->map(fn ($p) => [
                'id' => $p->id,
                'nickname' => $p->nickname,
                'avatar' => $p->avatar_url,
                'score' => $p->total_score,
                'streak' => $p->current_streak,
            ])->values()->all(),
            'leaderboard' => $this->getLiveLeaderboard($pin),
        ];
    }

    /**
     * Generate unique 6-digit PIN.
     */
    protected function generateUniquePin(): string
    {
        do {
            $pin = (string) rand(100000, 999999);
        } while (GameSession::where('pin', $pin)->whereIn('status', ['lobby', 'active'])->exists());

        return $pin;
    }
}
