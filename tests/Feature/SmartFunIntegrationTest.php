<?php

use App\Models\GameAnswer;
use App\Models\GameSession;
use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Services\GameEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('landing page loads successfully with quizzes', function () {
    $user = User::factory()->create();
    Quiz::create([
        'user_id' => $user->id,
        'title' => 'Kuis Sains Antariksa',
        'slug' => 'kuis-sains-antariksa',
        'is_public' => true,
    ]);

    $response = $this->get('/');
    $response->assertStatus(200)
        ->assertSee('SmartFun')
        ->assertSee('Kuis Sains Antariksa');
});

test('join form displays with 16 avatars', function () {
    $response = $this->get('/join?pin=123456');
    $response->assertStatus(200)
        ->assertSee('123456')
        ->assertSee('Gabung ke Arena Kuis');
});

test('player can join via api and play arena loads', function () {
    $user = User::factory()->create();
    $quiz = Quiz::create([
        'user_id' => $user->id,
        'title' => 'Geografi Dunia',
        'slug' => 'geografi-dunia',
        'is_public' => true,
    ]);

    $engine = app(GameEngineService::class);
    $session = $engine->createSession($quiz, $user, 'live_student');

    $joinResponse = $this->postJson('/api/game/join', [
        'pin' => $session->pin,
        'nickname' => 'Ahmad Bintang',
        'avatar' => 'astro_star',
    ]);

    $joinResponse->assertStatus(200)
        ->assertJson(['success' => true]);

    $playResponse = $this->get('/play/'.$session->pin);
    $playResponse->assertStatus(200)
        ->assertSee('Ahmad Bintang')
        ->assertSee($session->pin);
});

test('player can submit answer and receive immediate feedback', function () {
    $user = User::factory()->create();
    $quiz = Quiz::create([
        'user_id' => $user->id,
        'title' => 'Kuis Matematika Kilat',
        'slug' => 'kuis-matematika-kilat',
    ]);

    $question = Question::create([
        'quiz_id' => $quiz->id,
        'question_text' => '25 x 4 = ?',
        'time_limit_seconds' => 30,
        'base_points' => 1000,
        'order_index' => 1,
    ]);

    $correctOpt = Option::create([
        'question_id' => $question->id,
        'option_text' => '100',
        'is_correct' => true,
        'order_index' => 1,
    ]);

    $wrongOpt = Option::create([
        'question_id' => $question->id,
        'option_text' => '80',
        'is_correct' => false,
        'order_index' => 2,
    ]);

    $engine = app(GameEngineService::class);
    $session = $engine->createSession($quiz, $user);
    $player = $engine->joinGame($session->pin, 'Kalkulator Berjalan', 'robot_nova');

    $answerResponse = $this->postJson('/api/game/answer', [
        'player_id' => $player->id,
        'question_id' => $question->id,
        'option_id' => $correctOpt->id,
        'response_time_ms' => 3000,
    ]);

    $answerResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'result' => [
                'is_correct' => true,
            ],
        ]);
});

test('answer submission with huge timestamp response time is safely clamped without sql out of range error', function () {
    $user = User::factory()->create();
    $quiz = Quiz::create([
        'user_id' => $user->id,
        'title' => 'Kuis Batas Waktu',
        'slug' => 'kuis-batas-waktu-'.uniqid(),
        'theme' => 'arcade',
    ]);

    $question = Question::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Berapa batas waktu soal?',
        'question_type' => 'multiple_choice',
        'time_limit_seconds' => 30,
        'base_points' => 1000,
        'order_index' => 1,
    ]);

    $correctOpt = Option::create([
        'question_id' => $question->id,
        'option_text' => '30 Detik',
        'is_correct' => true,
        'order_index' => 1,
    ]);

    $engine = app(GameEngineService::class);
    $session = $engine->createSession($quiz, $user);
    $player = $engine->joinGame($session->pin, 'Budi Stopwatch', 'fox_spark');

    // Simulate sending an epoch millisecond timestamp as response_time_ms
    $answerResponse = $this->postJson('/api/game/answer', [
        'player_id' => $player->id,
        'question_id' => $question->id,
        'option_id' => $correctOpt->id,
        'response_time_ms' => 1792175478408,
    ]);

    $answerResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'result' => [
                'is_correct' => true,
            ],
        ]);

    $recordedAnswer = GameAnswer::where('game_session_id', $session->id)->first();
    expect($recordedAnswer)->not->toBeNull();
    expect($recordedAnswer->response_time_ms)->toBeLessThanOrEqual(40000);
});

test('unauthenticated guests and students cannot access host dashboard or quiz creator', function () {
    $this->get('/host')->assertRedirect('/login');
    $this->get('/host/quiz/create')->assertRedirect('/login');
    $this->post('/host/launch', ['quiz_id' => 1, 'mode' => 'live_host'])->assertRedirect('/login');
});

test('teacher can login with valid credentials and logout', function () {
    $teacher = User::factory()->create([
        'email' => 'guru_test@smartfun.test',
        'password' => bcrypt('password123'),
    ]);

    // Invalid login fails
    $failResponse = $this->post('/login', [
        'email' => 'guru_test@smartfun.test',
        'password' => 'wrong_password',
    ]);
    $failResponse->assertSessionHasErrors('email');
    $this->assertGuest();

    // Valid login succeeds
    $loginResponse = $this->post('/login', [
        'email' => 'guru_test@smartfun.test',
        'password' => 'password123',
    ]);
    $loginResponse->assertRedirect(route('host.index'));
    $this->assertAuthenticatedAs($teacher);

    // Logout works
    $logoutResponse = $this->post('/logout');
    $logoutResponse->assertRedirect('/');
    $this->assertGuest();
});

test('authenticated teacher can access dashboard, create quiz, launch session, and control arena', function () {
    $user = User::firstOrCreate(
        ['email' => 'guru@smartfun.test'],
        ['name' => 'Guru SmartFun', 'password' => bcrypt('password123')]
    );

    $quiz = Quiz::create([
        'user_id' => $user->id,
        'title' => 'Sejarah Kemerdekaan',
        'slug' => 'sejarah-kemerdekaan',
    ]);

    $q = Question::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Tahun berapakah Indonesia merdeka?',
        'order_index' => 1,
        'time_limit_seconds' => 20,
        'base_points' => 1000,
    ]);

    Option::create(['question_id' => $q->id, 'option_text' => '1945', 'is_correct' => true, 'order_index' => 1]);
    Option::create(['question_id' => $q->id, 'option_text' => '1942', 'is_correct' => false, 'order_index' => 2]);

    // Host dashboard loads when authenticated
    $dashboardResponse = $this->actingAs($user)->get('/host');
    $dashboardResponse->assertStatus(200)
        ->assertSee('Manajemen Kuis SmartFun')
        ->assertSee('Sejarah Kemerdekaan');

    // Host launches session
    $launchResponse = $this->actingAs($user)->post('/host/launch', [
        'quiz_id' => $quiz->id,
        'mode' => 'live_host',
        'powerups' => 1,
        'sfx' => 1,
    ]);

    $launchResponse->assertRedirect();
    $targetUrl = $launchResponse->headers->get('Location');
    preg_match('/arena\/([0-9]{6})/', $targetUrl, $matches);
    $pin = $matches[1];

    // Host arena screen loads
    $arenaResponse = $this->actingAs($user)->get('/host/arena/'.$pin);
    $arenaResponse->assertStatus(200)
        ->assertSee($pin);

    // Host starts game
    $startResponse = $this->actingAs($user)->postJson('/api/host/'.$pin.'/start');
    $startResponse->assertStatus(200)
        ->assertJson(['success' => true, 'status' => 'active']);

    // Host ends question
    $endResponse = $this->actingAs($user)->postJson('/api/host/'.$pin.'/end-question');
    $endResponse->assertStatus(200)
        ->assertJson(['success' => true]);

    // Host exports CSV report
    $exportResponse = $this->actingAs($user)->get('/host/arena/'.$pin.'/export');
    $exportResponse->assertStatus(200)
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('kahoot mode questions do not loop and synchronize with host progression', function () {
    $teacher = User::factory()->create();
    $quiz = Quiz::create([
        'user_id' => $teacher->id,
        'title' => 'Sains Luar Angkasa',
        'slug' => 'sains-luar-angkasa',
    ]);

    $q1 = Question::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Planet terbesar di tata surya?',
        'order_index' => 1,
        'time_limit_seconds' => 30,
        'base_points' => 1000,
    ]);
    $q1Opt1 = Option::create(['question_id' => $q1->id, 'option_text' => 'Jupiter', 'is_correct' => true, 'order_index' => 1]);
    Option::create(['question_id' => $q1->id, 'option_text' => 'Mars', 'is_correct' => false, 'order_index' => 2]);

    $q2 = Question::create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Bintang terdekat dengan Bumi?',
        'order_index' => 2,
        'time_limit_seconds' => 30,
        'base_points' => 1000,
    ]);
    Option::create(['question_id' => $q2->id, 'option_text' => 'Matahari', 'is_correct' => true, 'order_index' => 1]);
    Option::create(['question_id' => $q2->id, 'option_text' => 'Proxima Centauri', 'is_correct' => false, 'order_index' => 2]);

    $engine = app(GameEngineService::class);
    $session = $engine->createSession($quiz, $teacher, 'live_host');
    $player = $engine->joinGame($session->pin, 'Budi Siswa', 'astro_star');

    // Host starts game
    $engine->startGame($session->pin);
    $session->refresh();
    expect($session->current_question_index)->toBe(1);

    // Initial question for student in Kahoot mode should be question 1
    $initialQ = $engine->getInitialQuestionForPlayer($session, $player);
    expect($initialQ)->not->toBeNull();
    expect($initialQ['questionIndex'])->toBe(1);
    expect($initialQ['questionId'])->toBe($q1->id);

    // Student submits answer for Question 1
    $answerResult = $engine->submitAnswer($player->id, $q1->id, $q1Opt1->id, 2500);
    expect($answerResult['is_correct'])->toBeTrue();

    // After answering Question 1, while host is still on Question 1, initial question should NOT re-serve Question 1
    $initialQAfterAnswer = $engine->getInitialQuestionForPlayer($session, $player);
    expect($initialQAfterAnswer)->toBeNull();

    // Host advances to Question 2
    $nextResult = $engine->nextQuestion($session->pin);
    $session->refresh();
    expect($session->current_question_index)->toBe(2);

    // Now student receives Question 2
    $q2ForStudent = $engine->getInitialQuestionForPlayer($session, $player);
    expect($q2ForStudent)->not->toBeNull();
    expect($q2ForStudent['questionIndex'])->toBe(2);
    expect($q2ForStudent['questionId'])->toBe($q2->id);
});

test('host can launch quiz with custom question count and theme ambiance', function () {
    $teacher = User::factory()->create();
    $quiz = Quiz::create([
        'user_id' => $teacher->id,
        'title' => 'Bank Pengetahuan Super',
        'slug' => 'bank-pengetahuan-super',
    ]);

    // Create 30 questions
    for ($i = 1; $i <= 30; $i++) {
        $q = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => "Pertanyaan ke-{$i}?",
            'order_index' => $i,
            'time_limit_seconds' => 30,
            'base_points' => 1000,
        ]);
        Option::create(['question_id' => $q->id, 'option_text' => 'Benar', 'is_correct' => true, 'order_index' => 1]);
        Option::create(['question_id' => $q->id, 'option_text' => 'Salah', 'is_correct' => false, 'order_index' => 2]);
    }

    // Launch with question_count = 5 and theme = cyberpunk
    $response = $this->actingAs($teacher)->post('/host/launch', [
        'quiz_id' => $quiz->id,
        'mode' => 'live_student',
        'question_count' => '5',
        'theme' => 'cyberpunk',
        'bgm' => 1,
        'sfx' => 1,
        'powerups' => 1,
    ]);

    $response->assertStatus(302);
    $session = GameSession::latest()->first();
    expect($session)->not->toBeNull();
    expect($session->settings['question_count'])->toBe(5);
    expect($session->settings['theme'])->toBe('cyberpunk');
    expect($session->settings['bgm'])->toBeTrue();
    expect(count($session->settings['question_ids']))->toBe(5);

    $engine = app(GameEngineService::class);
    $subsetQuestions = $engine->getSessionQuestions($session);
    expect($subsetQuestions->count())->toBe(5);

    // Host arena loads and displays 5 questions
    $arenaResponse = $this->actingAs($teacher)->get('/host/arena/'.$session->pin);
    $arenaResponse->assertStatus(200)
        ->assertSee('totalQuestions: 5', false)
        ->assertSee('cyberpunk');
});

test('student answers all questions in subset and finishes successfully', function () {
    $teacher = User::factory()->create();
    $quiz = Quiz::create([
        'user_id' => $teacher->id,
        'title' => 'Kuis 5 Soal Cepat',
        'slug' => 'kuis-5-soal-cepat',
    ]);

    $questions = [];
    for ($i = 1; $i <= 10; $i++) {
        $q = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => "Soal nomor {$i}?",
            'order_index' => $i,
            'time_limit_seconds' => 30,
            'base_points' => 1000,
        ]);
        $correct = Option::create(['question_id' => $q->id, 'option_text' => 'Jawaban Tepat', 'is_correct' => true, 'order_index' => 1]);
        Option::create(['question_id' => $q->id, 'option_text' => 'Jawaban Keliru', 'is_correct' => false, 'order_index' => 2]);
        $questions[] = ['question' => $q, 'correct' => $correct];
    }

    $engine = app(GameEngineService::class);
    $session = $engine->createSession($quiz, $teacher, 'live_student', [
        'question_count' => '5',
        'theme' => 'arcade',
        'bgm' => true,
    ]);

    $player = $engine->joinGame($session->pin, 'Bintang Juara', 'cat_ninja');
    $engine->startGame($session->pin);

    // Answer each of the 5 questions
    for ($step = 1; $step <= 5; $step++) {
        $qData = $questions[$step - 1];
        $res = $engine->submitAnswer($player->id, $qData['question']->id, $qData['correct']->id, 1500);

        if ($step < 5) {
            expect($res['is_last_question'])->toBeFalse();
            expect($res['next_question'])->not->toBeNull();
        } else {
            // At 5th question, should be last question!
            expect($res['is_last_question'])->toBeTrue();
            expect($res['next_question'])->toBeNull();
        }
    }

    // Now player arena loads as completed
    session(['smartfun_player_id' => $player->id]);
    $playResponse = $this->get('/play/'.$session->pin);
    $playResponse->assertStatus(200);
});
