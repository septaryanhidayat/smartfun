<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\HostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web & Game Routes for SmartFun
|--------------------------------------------------------------------------
*/

// Public & Student Routes
Route::get('/', [GameController::class, 'index'])->name('home');
Route::get('/join', [GameController::class, 'joinForm'])->name('game.join.form');
Route::post('/api/game/join', [GameController::class, 'join'])->name('game.join');
Route::get('/play/{pin}', [GameController::class, 'play'])->name('game.play');
Route::post('/api/game/answer', [GameController::class, 'submitAnswer'])->name('game.answer');
Route::post('/api/game/powerup/fifty-fifty', [GameController::class, 'useFiftyFifty'])->name('game.powerup.fifty_fifty');
Route::get('/api/game/{pin}/state', [GameController::class, 'state'])->name('game.state');
Route::get('/api/game/{pin}/stream', [GameController::class, 'stream'])->name('game.stream');

// Teacher Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Teacher & Host Dashboard Routes (Students cannot access)
Route::prefix('host')->name('host.')->middleware('auth')->group(function () {
    Route::get('/', [HostController::class, 'index'])->name('index');
    Route::get('/quiz/create', [HostController::class, 'createQuiz'])->name('quiz.create');
    Route::post('/quiz', [HostController::class, 'storeQuiz'])->name('quiz.store');
    Route::get('/quiz/{quiz}/edit', [HostController::class, 'editQuiz'])->name('quiz.edit');
    Route::delete('/quiz/{quiz}', [HostController::class, 'destroyQuiz'])->name('quiz.destroy');
    Route::post('/launch', [HostController::class, 'launch'])->name('launch');
    Route::get('/arena/{pin}', [HostController::class, 'arena'])->name('arena');
    Route::get('/arena/{pin}/export', [HostController::class, 'export'])->name('export');
});

// Host Screen & Control APIs (Protected for Teachers/Host)
Route::prefix('api/host/{pin}')->name('api.host.')->middleware('auth')->group(function () {
    Route::post('/start', [HostController::class, 'start'])->name('start');
    Route::post('/next', [HostController::class, 'next'])->name('next');
    Route::post('/end-question', [HostController::class, 'endQuestion'])->name('end_question');
});
