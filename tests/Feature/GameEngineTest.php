<?php

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Services\GameEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('game engine can create session and join players', function () {
    $engine = app(GameEngineService::class);
    $user = User::factory()->create();
    $quiz = Quiz::create([
        'user_id' => $user->id,
        'title' => 'Test Quiz',
        'slug' => 'test-quiz-'.uniqid(),
    ]);

    $session = $engine->createSession($quiz, $user, 'live_student');
    expect($session->pin)->not->toBeEmpty()
        ->and($session->status)->toBe('lobby');

    $player = $engine->joinGame($session->pin, 'Budi Super', 'astronaut_1');
    expect($player->nickname)->toBe('Budi Super')
        ->and($player->total_score)->toBe(0);

    $leaderboard = $engine->getLiveLeaderboard($session->pin);
    expect($leaderboard)->toHaveCount(1)
        ->and($leaderboard[0]['nickname'])->toBe('Budi Super');
});

test('game engine calculates score correctly with formula', function () {
    $engine = app(GameEngineService::class);

    // 1000 base, 30s limit, 0ms time (instant), 0 streak => 1000 * 1.0 * 1.0 = 1000
    $instantScore = $engine->calculateScore(1000, 30, 0, 0);
    expect($instantScore)->toBe(1000);

    // 1000 base, 30s limit, 15000ms (half time), 0 streak => 1000 * (1 - 15000/60000) = 1000 * 0.75 = 750
    $halfTimeScore = $engine->calculateScore(1000, 30, 15000, 0);
    expect($halfTimeScore)->toBe(750);

    // With 2x multiplier
    $doubleScore = $engine->calculateScore(1000, 30, 0, 0, true);
    expect($doubleScore)->toBe(2000);
});

test('player can submit answer and update score and streak', function () {
    $engine = app(GameEngineService::class);
    $user = User::factory()->create();
    $quiz = Quiz::create([
        'user_id' => $user->id,
        'title' => 'Test Quiz 2',
        'slug' => 'test-quiz-'.uniqid(),
    ]);

    $question = Question::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Bumi bulat atau datar?',
        'time_limit_seconds' => 30,
        'base_points' => 1000,
        'order_index' => 1,
    ]);

    $opt1 = Option::create(['question_id' => $question->id, 'option_text' => 'Bulat', 'is_correct' => true, 'order_index' => 1]);
    $opt2 = Option::create(['question_id' => $question->id, 'option_text' => 'Datar', 'is_correct' => false, 'order_index' => 2]);

    $session = $engine->createSession($quiz, $user);
    $player = $engine->joinGame($session->pin, 'Ksatria Pintar', 'robot_1');

    $result = $engine->submitAnswer($player->id, $question->id, $opt1->id, 5000);

    expect($result['is_correct'])->toBeTrue()
        ->and($result['points_awarded'])->toBeGreaterThan(0)
        ->and($result['current_streak'])->toBe(1)
        ->and($result['player_rank'])->toBe(1)
        ->and($result['leaderboard'])->toHaveCount(1)
        ->and($result['is_last_question'])->toBeTrue();

    // Repeated answer submission should return already_answered gracefully
    $repeatedResult = $engine->submitAnswer($player->id, $question->id, $opt1->id, 5000);
    expect($repeatedResult['already_answered'])->toBeTrue()
        ->and($repeatedResult['is_correct'])->toBeTrue()
        ->and($repeatedResult['total_score'])->toBe($result['total_score']);
});
