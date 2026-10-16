@extends('layouts.app')

@section('content')
<div 
    class="min-h-[calc(100vh-4rem)] flex flex-col justify-between py-4 px-3 sm:px-6 max-w-4xl mx-auto w-full relative"
    x-data="studentArenaComponent()">

    <!-- Ambient Theme Backdrop -->
    <div class="fixed inset-0 pointer-events-none transition-all duration-1000 -z-10" :class="getThemeAmbianceClass()"></div>

    <!-- Top Status Bar (HUD) -->
    <div class="w-full bg-[#1e0a3c]/90 rounded-2xl border border-purple-800/60 p-3 sm:p-4 backdrop-blur-md shadow-lg flex items-center justify-between gap-2 mb-4">
        <!-- Player Info -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-purple-900/80 border border-purple-700/60 flex items-center justify-center text-2xl sm:text-3xl shadow">
                <span x-text="player.avatarEmoji"></span>
            </div>
            <div>
                <h2 class="text-sm sm:text-base font-black text-white leading-tight" x-text="player.nickname"></h2>
                <div class="flex items-center gap-2 text-[11px] text-purple-300">
                    <span class="font-bold text-amber-400">PIN: {{ $session->pin }}</span>
                    <span>•</span>
                    <span x-show="player.streak > 0" class="text-orange-400 font-extrabold flex items-center gap-0.5 animate-pulse">
                        🔥 <span x-text="player.streak"></span> Streak
                    </span>
                </div>
            </div>
        </div>

        <!-- Controls, Score & Rank Indicator -->
        <div class="flex items-center gap-2 sm:gap-3 text-right">
            <!-- Student BGM Audio Button -->
            <button 
                type="button" 
                @click="toggleBgm()" 
                class="px-2.5 py-1.5 rounded-xl border text-[11px] font-black flex items-center gap-1 transition-all cursor-pointer shadow-sm"
                :class="isBgmPlaying ? 'bg-emerald-500/25 text-emerald-300 border-emerald-500/50 shadow-emerald-500/20' : 'bg-purple-950/70 text-purple-400 border-purple-800 hover:text-purple-200'"
                :title="isBgmPlaying ? 'Klik untuk Jeda Musik' : 'Klik untuk Putar Musik Backsound Seru'">
                <span x-text="isBgmPlaying ? '🎵 Musik' : '🔇 Mute'"></span>
            </button>

            <div>
                <span class="text-[10px] uppercase tracking-wider font-extrabold text-purple-400 block">Total Skor</span>
                <span class="text-base sm:text-2xl font-black text-amber-300" x-text="player.score.toLocaleString('id-ID')"></span>
            </div>
            <div class="px-2.5 sm:px-3 py-1.5 rounded-xl bg-purple-950 border border-purple-800 text-center">
                <span class="text-[9px] uppercase font-bold text-purple-400 block">Peringkat</span>
                <span class="text-xs sm:text-base font-black text-purple-200" x-text="'#' + (player.rank || '-')"></span>
            </div>
        </div>
    </div>

    <!-- Power-Ups Bar (Quizizz Style) -->
    <div class="w-full bg-[#180630]/80 rounded-2xl border border-purple-900/60 p-2.5 mb-4 flex items-center justify-between gap-2 overflow-x-auto">
        <span class="text-[11px] font-black uppercase tracking-wider text-purple-300 flex items-center gap-1.5 shrink-0">
            <span>⚡</span> Power-Ups:
        </span>

        <div class="flex items-center gap-2 shrink-0">
            <!-- 50-50 Eliminator -->
            <button 
                type="button"
                @click="activatePowerUp('50_50')"
                :disabled="!hasPowerUp('50_50') || activePowerUp === '50_50' || currentStep !== 'active'"
                :class="{
                    'ring-2 ring-amber-400 bg-amber-500/20 text-amber-300 border-amber-400': activePowerUp === '50_50',
                    'opacity-40 cursor-not-allowed bg-purple-950/40 text-purple-500 border-purple-900': !hasPowerUp('50_50'),
                    'hover:scale-105 bg-purple-900/60 text-purple-200 border-purple-700': hasPowerUp('50_50') && activePowerUp !== '50_50'
                }"
                class="px-3 py-1.5 rounded-xl border text-xs font-black flex items-center gap-1.5 transition-all">
                <span>✂️</span> 50-50
            </button>

            <!-- 2x Multiplier -->
            <button 
                type="button"
                @click="activatePowerUp('2x_multiplier')"
                :disabled="!hasPowerUp('2x_multiplier') || activePowerUp === '2x_multiplier' || currentStep !== 'active'"
                :class="{
                    'ring-2 ring-emerald-400 bg-emerald-500/20 text-emerald-300 border-emerald-400': activePowerUp === '2x_multiplier',
                    'opacity-40 cursor-not-allowed bg-purple-950/40 text-purple-500 border-purple-900': !hasPowerUp('2x_multiplier'),
                    'hover:scale-105 bg-purple-900/60 text-purple-200 border-purple-700': hasPowerUp('2x_multiplier') && activePowerUp !== '2x_multiplier'
                }"
                class="px-3 py-1.5 rounded-xl border text-xs font-black flex items-center gap-1.5 transition-all">
                <span>✖️2</span> 2x Skor
            </button>

            <!-- Streak Shield -->
            <button 
                type="button"
                @click="activatePowerUp('streak_shield')"
                :disabled="!hasPowerUp('streak_shield') || activePowerUp === 'streak_shield' || currentStep !== 'active'"
                :class="{
                    'ring-2 ring-cyan-400 bg-cyan-500/20 text-cyan-300 border-cyan-400': activePowerUp === 'streak_shield',
                    'opacity-40 cursor-not-allowed bg-purple-950/40 text-purple-500 border-purple-900': !hasPowerUp('streak_shield'),
                    'hover:scale-105 bg-purple-900/60 text-purple-200 border-purple-700': hasPowerUp('streak_shield') && activePowerUp !== 'streak_shield'
                }"
                class="px-3 py-1.5 rounded-xl border text-xs font-black flex items-center gap-1.5 transition-all">
                <span>🛡️</span> Shield
            </button>

            <!-- Time Freeze -->
            <button 
                type="button"
                @click="activatePowerUp('time_freeze')"
                :disabled="!hasPowerUp('time_freeze') || activePowerUp === 'time_freeze' || currentStep !== 'active'"
                :class="{
                    'ring-2 ring-blue-400 bg-blue-500/20 text-blue-300 border-blue-400': activePowerUp === 'time_freeze',
                    'opacity-40 cursor-not-allowed bg-purple-950/40 text-purple-500 border-purple-900': !hasPowerUp('time_freeze'),
                    'hover:scale-105 bg-purple-900/60 text-purple-200 border-purple-700': hasPowerUp('time_freeze') && activePowerUp !== 'time_freeze'
                }"
                class="px-3 py-1.5 rounded-xl border text-xs font-black flex items-center gap-1.5 transition-all">
                <span>⏳</span> Freeze
            </button>
        </div>
    </div>

    <!-- Active Power-Up Banner Alert -->
    <div x-show="activePowerUp" x-cloak class="mb-3 py-2 px-4 rounded-xl bg-gradient-to-r from-amber-500/30 to-purple-500/30 border border-amber-400/50 text-amber-200 text-xs font-bold flex items-center justify-between animate-pop-in">
        <span class="flex items-center gap-2">
            <span>✨</span> Power-up aktif untuk soal ini: <strong x-text="activePowerUpName" class="text-white"></strong>!
        </span>
        <span class="text-[10px] bg-amber-400 text-slate-950 px-2 py-0.5 rounded-md font-black">AKTIF</span>
    </div>

    <!-- ========================================== -->
    <!-- VIEW 1: LOBBY STATE                       -->
    <!-- ========================================== -->
    <div x-show="sessionStatus === 'lobby'" class="flex-1 flex flex-col items-center justify-center text-center py-10">
        <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-3xl bg-gradient-to-tr from-purple-700 via-rose-600 to-amber-500 p-1 flex items-center justify-center shadow-2xl shadow-purple-900/80 mb-6 animate-pulse-glow">
            <div class="w-full h-full bg-[#180630] rounded-[22px] flex items-center justify-center text-6xl sm:text-7xl">
                <span x-text="player.avatarEmoji"></span>
            </div>
        </div>

        <h2 class="text-2xl sm:text-4xl font-black text-white mb-2">Kamu Sudah Terhubung!</h2>
        <p class="text-sm sm:text-base text-purple-300 max-w-md mx-auto mb-6">
            Lihat namamu di layar depan. Bersiaplah, guru akan segera memulai permainan!
        </p>

        <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-full bg-purple-950/80 border border-purple-700/60 text-purple-300 text-xs font-bold">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
            <span x-text="connectedPlayersCount + ' Pemain Bersiap di Lobi'"></span>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- VIEW 2: ACTIVE QUESTION STATE             -->
    <!-- ========================================== -->
    <div x-show="sessionStatus === 'active' && currentStep === 'active'" class="flex-1 flex flex-col justify-between">
        <!-- Question Header & Smooth Countdown Bar -->
        <div class="mb-4">
            <div class="flex items-center justify-between text-xs font-extrabold text-purple-300 mb-2">
                <span>Soal <span x-text="currentQuestion.questionIndex"></span> / <span x-text="totalQuestions"></span></span>
                <span class="text-amber-400 font-mono text-sm" x-text="timeLeft + 's'"></span>
            </div>

            <!-- Animated Countdown Bar -->
            <div class="w-full h-3.5 bg-purple-950/90 rounded-full overflow-hidden border border-purple-800/60 p-0.5">
                <div 
                    class="h-full rounded-full transition-all duration-100 ease-linear"
                    :class="{
                        'bg-emerald-500': timeLeftPercent > 50,
                        'bg-amber-500': timeLeftPercent <= 50 && timeLeftPercent > 20,
                        'bg-rose-500 animate-pulse': timeLeftPercent <= 20
                    }"
                    :style="'width: ' + timeLeftPercent + '%'"></div>
            </div>
        </div>

        <!-- Question Card -->
        <div class="bg-[#1c083b]/90 rounded-3xl border border-purple-700/50 p-5 sm:p-7 shadow-xl mb-6 backdrop-blur-md">
            <template x-if="currentQuestion.media">
                <div class="w-full max-h-52 overflow-hidden rounded-2xl mb-4 border border-purple-900/60">
                    <img :src="currentQuestion.media" alt="Media Soal" class="w-full h-full object-cover">
                </div>
            </template>
            <h1 class="text-lg sm:text-2xl font-black text-white text-center leading-snug" x-text="currentQuestion.text"></h1>
        </div>

        <!-- 4 Tactile 3D Answer Options Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4">
            <template x-for="(opt, idx) in currentQuestion.options" :key="opt.id">
                <button 
                    type="button"
                    @click="chooseOption(opt.id)"
                    :disabled="isOptionEliminated(opt.id) || isSubmittingAnswer"
                    :class="[
                        getOptionColorClass(idx),
                        isOptionEliminated(opt.id) ? 'opacity-20 pointer-events-none line-through filter grayscale' : ''
                    ]"
                    class="btn-3d p-4 sm:p-5 rounded-2xl flex items-center gap-3 text-left cursor-pointer font-bold text-sm sm:text-base relative min-h-[72px]">
                    
                    <!-- Tactile Shape Symbol -->
                    <span class="w-9 h-9 rounded-xl bg-black/20 flex items-center justify-center text-lg shrink-0 font-black" x-text="getOptionShape(idx)"></span>
                    
                    <span class="flex-1 leading-snug" x-text="opt.text"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- VIEW 3: ANSWER LOCKED / WAITING OTHERS    -->
    <!-- ========================================== -->
    <div x-show="sessionStatus === 'active' && currentStep === 'waiting'" class="flex-1 flex flex-col items-center justify-center text-center py-10 animate-pop-in">
        <div class="w-20 h-20 rounded-2xl bg-purple-900/60 border border-purple-700 flex items-center justify-center text-4xl mb-4 animate-bounce">
            🔒
        </div>
        <h3 class="text-2xl font-black text-white mb-2">Jawaban Terkunci!</h3>
        <p class="text-sm text-purple-300 max-w-sm mx-auto mb-4">
            Waktumu tercatat! Memproses hasil dan klasemen...
        </p>
        <div class="w-8 h-8 border-4 border-amber-400 border-t-transparent rounded-full animate-spin mb-6"></div>

        <button 
            type="button" 
            @click="checkAnswerStatus()"
            class="px-5 py-2.5 rounded-xl bg-purple-800/80 hover:bg-purple-700 text-purple-100 text-xs font-black border border-purple-600 transition-all shadow-lg flex items-center gap-2 cursor-pointer">
            <span>⚡</span> Tampilkan Hasil & Klasemen
        </button>
    </div>

    <!-- ======================================================== -->
    <!-- VIEW 4: QUESTION RESULT FEEDBACK & KLASEMEN SEMENTARA    -->
    <!-- ======================================================== -->
    <div x-show="sessionStatus === 'active' && !isPlayerFinished && currentStep === 'feedback'" class="flex-1 flex flex-col justify-between py-2 sm:py-4 animate-pop-in">
        
        <div class="w-full max-w-xl mx-auto space-y-4">
            
            <!-- Result Splash Banner (Correct vs Incorrect) -->
            <template x-if="lastAnswerResult && lastAnswerResult.is_correct">
                <div class="bg-emerald-950/90 border-2 border-emerald-500/80 p-5 rounded-3xl shadow-2xl backdrop-blur-md text-center">
                    <div class="flex items-center justify-center gap-3 mb-2">
                        <span class="w-10 h-10 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center text-2xl font-black shadow-lg">✓</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-white">LUAR BIASA! BENAR</h2>
                    </div>
                    <div class="flex items-center justify-center gap-4 text-sm font-extrabold text-emerald-200">
                        <span class="text-2xl font-black text-amber-300" x-text="'+' + lastAnswerResult.points_awarded + ' Poin'"></span>
                        <span class="px-3 py-1 rounded-xl bg-emerald-900/80 border border-emerald-700 text-orange-400">
                            🔥 Streak: <span x-text="lastAnswerResult.current_streak"></span>
                        </span>
                    </div>

                    <template x-if="lastAnswerResult.unlocked_power_up">
                        <div class="mt-3 p-2.5 rounded-xl bg-gradient-to-r from-amber-500/30 to-purple-500/30 border border-amber-400 text-amber-200 text-xs font-black flex items-center justify-center gap-2">
                            <span>🎁</span> Bonus Kartu Power-Up: <strong x-text="lastAnswerResult.unlocked_power_up"></strong>!
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="lastAnswerResult && !lastAnswerResult.is_correct">
                <div class="bg-rose-950/90 border-2 border-rose-500/80 p-5 rounded-3xl shadow-2xl backdrop-blur-md text-center">
                    <div class="flex items-center justify-center gap-3 mb-1">
                        <span class="w-10 h-10 rounded-full bg-rose-600 text-white flex items-center justify-center text-2xl font-black shadow-lg">✕</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-white">KURANG TEPAT!</h2>
                    </div>
                    <span class="text-base font-bold text-rose-300 block">+0 Poin</span>
                </div>
            </template>

            <!-- Graceful Fallback Banner if lastAnswerResult is null/reloaded -->
            <template x-if="!lastAnswerResult">
                <div class="bg-purple-950/90 border border-purple-700/80 p-5 rounded-3xl shadow-2xl backdrop-blur-md text-center">
                    <div class="flex items-center justify-center gap-3 mb-1">
                        <span class="w-10 h-10 rounded-full bg-purple-700 text-white flex items-center justify-center text-xl font-black shadow-lg">✓</span>
                        <h2 class="text-xl sm:text-2xl font-black text-white">JAWABAN TELAH TERCATAT</h2>
                    </div>
                    <p class="text-xs text-purple-300">Hasil dan posisimu telah terdaftar di klasemen sementara.</p>
                </div>
            </template>

            <!-- KLASEMEN SEMENTARA (LIVE STANDINGS) CARD -->
            <div class="bg-[#180630]/95 rounded-3xl border border-purple-800/80 p-5 shadow-2xl backdrop-blur-md">
                <div class="flex items-center justify-between pb-3 border-b border-purple-900/70 mb-3">
                    <h3 class="text-xs sm:text-sm font-black uppercase tracking-wider text-purple-300 flex items-center gap-2">
                        <span>🏆</span> Klasemen Sementara
                    </h3>
                    
                    <!-- My Rank Badge -->
                    <span class="px-3 py-1 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 font-black text-xs shadow-md">
                        Kamu di Peringkat #<span x-text="player.rank"></span>
                    </span>
                </div>

                <!-- Full Mini Leaderboard (All Participants) -->
                <div class="space-y-2 max-h-52 overflow-y-auto pr-1 custom-scrollbar">
                    <template x-for="(p, idx) in leaderboard" :key="p.id">
                        <div 
                            :class="{
                                'ring-2 ring-amber-400 bg-amber-500/20 border-amber-400/60 shadow-lg': p.id === player.id,
                                'bg-purple-950/50 border-purple-800/50': p.id !== player.id
                            }"
                            class="flex items-center justify-between p-2.5 sm:p-3 rounded-2xl border transition-all text-xs sm:text-sm">
                            
                            <div class="flex items-center gap-3">
                                <span 
                                    :class="{
                                        'text-amber-400 font-black': idx === 0,
                                        'text-slate-300 font-bold': idx === 1,
                                        'text-amber-600 font-bold': idx === 2,
                                        'text-purple-400': idx > 2
                                    }"
                                    class="w-5 text-center font-black"
                                    x-text="'#' + (idx + 1)"></span>
                                <span class="text-xl" x-text="getAvatarEmoji(p.avatar)"></span>
                                <span class="font-extrabold text-white" x-text="p.nickname + (p.id === player.id ? ' (Kamu)' : '')"></span>
                            </div>

                            <div class="text-right">
                                <span class="font-black text-amber-300" x-text="p.score.toLocaleString('id-ID') + ' Poin'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Explanation Box (if any) -->
            <template x-if="lastAnswerResult && lastAnswerResult.explanation">
                <div class="p-3.5 rounded-2xl bg-purple-950/70 border border-purple-800 text-xs text-purple-200 leading-relaxed text-left">
                    <strong class="text-amber-300">💡 Pembahasan:</strong>
                    <span x-text="lastAnswerResult.explanation"></span>
                </div>
            </template>

            <!-- Auto-Advance Countdown Bar & Manual Action Button -->
            <!-- Auto-Advance Countdown Bar & Action Button -->
            <div class="pt-2">
                <!-- If it's the last question (in any mode) -->
                <template x-if="lastAnswerResult && lastAnswerResult.is_last_question">
                    <div>
                        <!-- Auto-advance progress bar -->
                        <div class="w-full h-2 bg-purple-950 rounded-full overflow-hidden border border-purple-800 mb-2">
                            <div 
                                class="h-full bg-gradient-to-r from-amber-400 via-rose-500 to-purple-500 transition-all duration-100 ease-linear rounded-full"
                                :style="'width: ' + ((autoAdvanceSeconds / 4) * 100) + '%'"></div>
                        </div>

                        <button 
                            type="button" 
                            @click="advanceNext()"
                            class="w-full py-4 rounded-2xl font-black text-sm sm:text-base btn-3d btn-3d-green flex items-center justify-center gap-2 cursor-pointer shadow-xl shadow-emerald-950">
                            <span>🏆 Lihat Hasil Akhir & Podium (<span x-text="autoAdvanceSeconds"></span>s)</span>
                            <span>➔</span>
                        </button>
                    </div>
                </template>

                <!-- If not the last question and student-paced -->
                <template x-if="sessionMode !== 'live_host' && (!lastAnswerResult || !lastAnswerResult.is_last_question)">
                    <div>
                        <!-- Auto-advance progress bar -->
                        <div class="w-full h-2 bg-purple-950 rounded-full overflow-hidden border border-purple-800 mb-2">
                            <div 
                                class="h-full bg-gradient-to-r from-amber-400 via-rose-500 to-purple-500 transition-all duration-100 ease-linear rounded-full"
                                :style="'width: ' + ((autoAdvanceSeconds / 4) * 100) + '%'"></div>
                        </div>

                        <button 
                            type="button" 
                            @click="advanceNext()"
                            class="w-full py-4 rounded-2xl font-black text-sm sm:text-base btn-3d btn-3d-purple flex items-center justify-center gap-2 cursor-pointer shadow-xl shadow-purple-950">
                            <span>Lanjut ke Soal Berikutnya (<span x-text="autoAdvanceSeconds"></span>s)</span>
                            <span>➔</span>
                        </button>
                    </div>
                </template>

                <!-- If not the last question and live host (Kahoot) -->
                <template x-if="sessionMode === 'live_host' && (!lastAnswerResult || !lastAnswerResult.is_last_question)">
                    <div class="p-4 rounded-2xl bg-purple-900/50 border border-purple-700/60 text-center">
                        <div class="flex items-center justify-center gap-2 text-amber-300 font-extrabold text-sm mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
                            <span>Menunggu Guru Melanjutkan di Proyektor...</span>
                        </div>
                        <p class="text-xs text-purple-300">
                            Perhatikan layar proyektor di depan kelas untuk pembahasan dan pertanyaan berikutnya.
                        </p>
                    </div>
                </template>
            </div>

        </div>

    </div>

    <!-- ========================================== -->
    <!-- VIEW 5: WAYGROUND ADVENTURE QUEST MAP      -->
    <!-- ========================================== -->
    <template x-if="sessionMode === 'wayground_map'">
        <div class="w-full bg-[#190633]/90 rounded-3xl border border-purple-800/60 p-4 sm:p-6 mb-4 shadow-xl mt-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm sm:text-base font-black text-white flex items-center gap-2">
                    <span>🗺️</span> Peta Petualangan Wayground
                </h3>
                <span class="text-xs font-bold text-amber-400">Level Bertingkat</span>
            </div>

            <!-- SVG Winding Quest Map -->
            <div class="relative py-4 overflow-x-auto">
                <div class="flex items-center justify-between min-w-[500px] relative px-6">
                    <!-- Progress Line -->
                    <div class="absolute left-10 right-10 top-1/2 -translate-y-1/2 h-2 bg-purple-950 rounded-full border border-purple-800 z-0"></div>

                    <!-- Milestone Nodes (1 to 5) -->
                    <template x-for="node in [1, 2, 3, 4, 5]" :key="node">
                        <div class="relative z-10 flex flex-col items-center">
                            <div 
                                :class="{
                                    'bg-gradient-to-tr from-amber-400 to-yellow-500 text-slate-950 ring-4 ring-amber-400/50 scale-110 shadow-lg shadow-amber-400/40': currentQuestionIndex >= node,
                                    'bg-purple-950 text-purple-500 border-2 border-purple-800': currentQuestionIndex < node
                                }"
                                class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-base transition-all">
                                <span x-text="node === 5 ? '👑' : node"></span>
                            </div>

                            <!-- Star Rating -->
                            <div class="flex items-center gap-0.5 mt-1 text-[10px]">
                                <span :class="currentQuestionIndex >= node ? 'text-amber-400' : 'text-purple-800'">★</span>
                                <span :class="currentQuestionIndex >= node && player.streak >= 2 ? 'text-amber-400' : 'text-purple-800'">★</span>
                                <span :class="currentQuestionIndex >= node && player.streak >= 3 ? 'text-amber-400' : 'text-purple-800'">★</span>
                            </div>

                            <!-- Avatar Token if on this node -->
                            <template x-if="currentQuestionIndex === node">
                                <span class="absolute -top-7 text-xl animate-bounce" x-text="player.avatarEmoji"></span>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <!-- ========================================== -->
    <!-- VIEW 6: GAME FINISHED / PODIUM SUMMARY    -->
    <!-- ========================================== -->
    <div x-show="sessionStatus === 'finished' || isPlayerFinished" class="flex-1 flex flex-col items-center justify-center text-center py-6 sm:py-10 animate-pop-in w-full max-w-2xl mx-auto">
        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-tr from-amber-400 via-rose-500 to-purple-600 p-1 flex items-center justify-center shadow-2xl mb-4">
            <div class="w-full h-full bg-[#180630] rounded-[22px] flex items-center justify-center text-4xl sm:text-5xl">
                🏆
            </div>
        </div>

        <h1 class="text-2xl sm:text-4xl font-black text-white mb-2">Permainan Selesai!</h1>
        <p class="text-xs sm:text-sm text-purple-300 mb-6">Kerja luar biasa! Berikut adalah podium pemenang dan hasil akhir kamu.</p>

        <!-- 3D Podium Display on Student Finished Screen -->
        <template x-if="podium && podium.length > 0">
            <div class="flex items-end justify-center gap-2 sm:gap-4 mb-8 w-full max-w-md px-2">
                <!-- Juara 2 (Perak) -->
                <div class="flex-1 flex flex-col items-center">
                    <template x-if="podium[1]">
                        <div class="flex flex-col items-center mb-2">
                            <span class="text-2xl sm:text-3xl" x-text="getAvatarEmoji(podium[1].avatar)"></span>
                            <span class="text-[11px] sm:text-xs font-black text-white mt-1 text-center line-clamp-1" x-text="podium[1].nickname"></span>
                            <span class="text-[10px] font-bold text-slate-300" x-text="podium[1].score + ' Poin'"></span>
                        </div>
                    </template>
                    <div class="w-full h-28 sm:h-32 bg-gradient-to-t from-slate-600 to-slate-400 rounded-t-2xl border-t-2 border-slate-300 flex items-center justify-center shadow-lg">
                        <span class="text-2xl sm:text-3xl font-black text-slate-900">2</span>
                    </div>
                </div>

                <!-- Juara 1 (Emas) -->
                <div class="flex-1 flex flex-col items-center -mt-4">
                    <template x-if="podium[0]">
                        <div class="flex flex-col items-center mb-2">
                            <span class="text-lg animate-bounce">👑</span>
                            <span class="text-3xl sm:text-4xl" x-text="getAvatarEmoji(podium[0].avatar)"></span>
                            <span class="text-xs sm:text-sm font-black text-amber-300 mt-0.5 text-center line-clamp-1" x-text="podium[0].nickname"></span>
                            <span class="text-[11px] font-bold text-white" x-text="podium[0].score + ' Poin'"></span>
                        </div>
                    </template>
                    <div class="w-full h-36 sm:h-44 bg-gradient-to-t from-amber-600 via-amber-500 to-yellow-400 rounded-t-2xl border-t-2 border-yellow-200 flex items-center justify-center shadow-xl">
                        <span class="text-3xl sm:text-4xl font-black text-slate-950">1</span>
                    </div>
                </div>

                <!-- Juara 3 (Perunggu) -->
                <div class="flex-1 flex flex-col items-center">
                    <template x-if="podium[2]">
                        <div class="flex flex-col items-center mb-2">
                            <span class="text-2xl sm:text-3xl" x-text="getAvatarEmoji(podium[2].avatar)"></span>
                            <span class="text-[11px] sm:text-xs font-black text-white mt-1 text-center line-clamp-1" x-text="podium[2].nickname"></span>
                            <span class="text-[10px] font-bold text-amber-400" x-text="podium[2].score + ' Poin'"></span>
                        </div>
                    </template>
                    <div class="w-full h-24 sm:h-28 bg-gradient-to-t from-amber-800 to-amber-700 rounded-t-2xl border-t-2 border-amber-500 flex items-center justify-center shadow-lg">
                        <span class="text-2xl sm:text-3xl font-black text-amber-950">3</span>
                    </div>
                </div>
            </div>
        </template>

        <!-- Personal Player Stats Card -->
        <div class="w-full max-w-sm bg-[#1c083b]/90 border border-purple-700/60 p-5 rounded-3xl shadow-xl backdrop-blur-md mb-6">
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="p-3.5 rounded-2xl bg-purple-950/70 border border-purple-800">
                    <span class="text-[10px] uppercase font-bold text-purple-400 block mb-1">Peringkat Akhir</span>
                    <span class="text-2xl sm:text-3xl font-black text-amber-300" x-text="'#' + (player.rank || 1)"></span>
                </div>
                <div class="p-3.5 rounded-2xl bg-purple-950/70 border border-purple-800">
                    <span class="text-[10px] uppercase font-bold text-purple-400 block mb-1">Total Skor</span>
                    <span class="text-2xl sm:text-3xl font-black text-purple-200" x-text="player.score"></span>
                </div>
            </div>
        </div>

        <!-- Full Class Standings on Finished Screen -->
        <div class="w-full max-w-md bg-[#180630]/90 border border-purple-800/80 rounded-3xl p-5 mb-6 text-left shadow-2xl backdrop-blur-md">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-purple-900/60">
                <h3 class="text-sm font-black text-white flex items-center gap-2">
                    <span>🏆</span> Peringkat Lengkap Seluruh Peserta (<span x-text="leaderboard.length"></span>)
                </h3>
                <span class="text-[11px] text-purple-400 font-bold">SmartFun Standings</span>
            </div>

            <div class="space-y-2 max-h-64 overflow-y-auto pr-1 custom-scrollbar">
                <template x-for="(p, idx) in leaderboard" :key="p.id">
                    <div 
                        :class="p.id === player.id ? 'bg-purple-900/80 border-amber-400 ring-2 ring-amber-400/50' : 'bg-purple-950/60 border-purple-800/70'"
                        class="flex items-center justify-between p-3 rounded-2xl border transition-all">
                        <div class="flex items-center gap-3">
                            <span 
                                :class="{
                                    'text-amber-300 font-black text-base': idx === 0,
                                    'text-slate-300 font-black text-sm': idx === 1,
                                    'text-amber-600 font-black text-sm': idx === 2,
                                    'text-purple-400 font-bold text-xs': idx > 2
                                }"
                                class="w-6 text-center" 
                                x-text="'#' + (idx + 1)"></span>
                            
                            <span class="text-2xl" x-text="getAvatarEmoji(p.avatar)"></span>
                            
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-xs sm:text-sm text-white" x-text="p.nickname"></span>
                                    <span x-show="p.id === player.id" class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-amber-400 text-slate-950">Kamu</span>
                                </div>
                                <span class="text-[10px] text-orange-400 font-bold" x-show="p.streak > 1" x-text="'🔥 ' + p.streak + ' Streak'"></span>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-sm font-black text-amber-300" x-text="p.score.toLocaleString('id-ID')"></span>
                            <span class="text-[9px] text-purple-400 block uppercase font-bold">Poin</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <a href="{{ route('home') }}" class="py-3 px-8 rounded-2xl font-black text-base btn-3d btn-3d-purple">
            Kembali ke Beranda
        </a>
    </div>

</div>

<script>
    function studentArenaComponent() {
        return {
            pin: '{{ $session->pin }}',
            sessionStatus: '{{ $session->status }}',
            sessionMode: '{{ $session->mode }}',
            currentQuestionIndex: {{ $session->current_question_index ?? 0 }},
            totalQuestions: {{ $totalQuestionsCount ?? $session->quiz->questions->count() }},
            currentTheme: '{{ $session->settings["theme"] ?? $session->quiz->theme ?? "arcade" }}',
            isBgmPlaying: false,
            bgmConfigEnabled: {{ ($session->settings["bgm"] ?? true) ? 'true' : 'false' }},
            currentStep: 'active', // 'active', 'waiting', 'feedback'
            connectedPlayersCount: {{ $session->players->count() }},

            getThemeAmbianceClass() {
                const ambiance = {
                    arcade: 'bg-[radial-gradient(ellipse_at_top,_rgba(236,72,153,0.18),_rgba(107,33,168,0.14),_transparent_70%)]',
                    space: 'bg-[radial-gradient(ellipse_at_top,_rgba(99,102,241,0.22),_rgba(88,28,135,0.14),_transparent_70%)]',
                    fantasy: 'bg-[radial-gradient(ellipse_at_top,_rgba(16,185,129,0.18),_rgba(13,148,136,0.14),_transparent_70%)]',
                    cyberpunk: 'bg-[radial-gradient(ellipse_at_top,_rgba(6,182,212,0.18),_rgba(37,99,235,0.14),_transparent_70%)]',
                    lava: 'bg-[radial-gradient(ellipse_at_top,_rgba(244,63,94,0.22),_rgba(245,158,11,0.15),_transparent_70%)]'
                };
                return ambiance[this.currentTheme] || ambiance.arcade;
            },

            toggleBgm() {
                if (!window.SmartFunAudio) return;
                if (this.isBgmPlaying) {
                    window.SmartFunAudio.stopBgm();
                    this.isBgmPlaying = false;
                } else {
                    window.SmartFunAudio.startBgm(this.currentTheme);
                    this.isBgmPlaying = true;
                }
            },

            player: {
                id: {{ $player->id }},
                nickname: '{{ $player->nickname }}',
                avatar: '{{ $player->avatar_url }}',
                avatarEmoji: '🦊',
                score: {{ $player->total_score }},
                streak: {{ $player->current_streak }},
                rank: {{ $initialRank ?? 1 }}
            },

            currentQuestion: @json($initialQuestion) || {
                questionIndex: 1,
                questionId: 0,
                text: '',
                media: null,
                timeLimit: 30,
                options: []
            },

            leaderboard: @json($initialLeaderboard ?? []),
            podium: @json(array_slice($initialLeaderboard ?? [], 0, 3)),
            isPlayerFinished: {{ $isCompleted ? 'true' : 'false' }},

            // Timer state
            timeLeft: 30,
            timeLimitSeconds: 30,
            questionStartTime: 0,
            timerInterval: null,

            // Auto-advance countdown state
            autoAdvanceSeconds: 4,
            autoAdvanceInterval: null,

            // Power-ups state
            inventory: ['50_50', '2x_multiplier', 'streak_shield', 'time_freeze'],
            activePowerUp: null,
            eliminatedOptionIds: [],

            answeredQuestionIds: @json($answeredQuestionIds ?? []),
            isSubmittingAnswer: false,
            lastAnswerResult: null,

            init() {
                this.resolveAvatarEmoji();
                this.podium = (this.leaderboard || []).slice(0, 3);

                // If all questions already finished for this player
                if ({{ $isCompleted ? 'true' : 'false' }}) {
                    this.isPlayerFinished = true;
                    this.sessionStatus = 'finished';
                    if (window.confetti) window.confetti({ particleCount: 100, spread: 80 });
                } else if (this.currentQuestion && this.currentQuestion.questionId && this.sessionStatus === 'active') {
                    if (this.answeredQuestionIds.includes(this.currentQuestion.questionId)) {
                        this.currentStep = 'feedback';
                    } else {
                        this.currentStep = 'active';
                        this.startTimer(this.currentQuestion.timeLimit || 30);
                    }
                } else if (this.sessionStatus === 'active') {
                    this.currentStep = 'waiting';
                }

                if (this.bgmConfigEnabled && window.SmartFunAudio) {
                    window.SmartFunAudio.setTheme(this.currentTheme);
                }

                this.connectRealtime();
                this.pollState();
            },

            resolveAvatarEmoji() {
                const map = {
                    fox_spark: '🦊', robot_nova: '🤖', astro_star: '🚀', wizard_cloud: '🧙‍♂️',
                    cat_ninja: '🐱', lion_gold: '🦁', panda_zen: '🐼', alien_neon: '👾',
                    dragon_fire: '🐉', owl_wise: '🦉', thunder_bolt: '⚡', unicorn_prism: '🦄',
                    monkey_wit: '🐵', shark_wave: '🦈', dino_rex: '🦖', tiger_brave: '🐯'
                };
                this.player.avatarEmoji = map[this.player.avatar] || '👾';
            },

            getAvatarEmoji(avatarId) {
                const map = {
                    fox_spark: '🦊', robot_nova: '🤖', astro_star: '🚀', wizard_cloud: '🧙‍♂️',
                    cat_ninja: '🐱', lion_gold: '🦁', panda_zen: '🐼', alien_neon: '👾',
                    dragon_fire: '🐉', owl_wise: '🦉', thunder_bolt: '⚡', unicorn_prism: '🦄',
                    monkey_wit: '🐵', shark_wave: '🦈', dino_rex: '🦖', tiger_brave: '🐯'
                };
                return map[avatarId] || '👾';
            },

            get timeLeftPercent() {
                return Math.max(0, Math.min(100, (this.timeLeft / this.timeLimitSeconds) * 100));
            },

            get activePowerUpName() {
                const names = {
                    '50_50': '50-50 Eliminator',
                    '2x_multiplier': '2x Poin Pengganda',
                    'streak_shield': 'Perisai Streak',
                    'time_freeze': 'Waktu Beku'
                };
                return names[this.activePowerUp] || this.activePowerUp;
            },

            hasPowerUp(type) {
                return this.inventory.includes(type);
            },

            activatePowerUp(type) {
                if (this.activePowerUp === type) return;
                this.activePowerUp = type;

                if (window.SmartFunAudio) {
                    window.SmartFunAudio.playPowerup();
                }

                if (type === '50_50' && this.currentQuestion.questionId) {
                    this.fetchFiftyFifty();
                }
            },

            async fetchFiftyFifty() {
                try {
                    const res = await fetch('{{ route("game.powerup.fifty_fifty") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ question_id: this.currentQuestion.questionId })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.eliminatedOptionIds = data.eliminated_options;
                    }
                } catch (e) {}
            },

            isOptionEliminated(optId) {
                return this.eliminatedOptionIds.includes(optId);
            },

            getOptionColorClass(idx) {
                const colors = ['btn-3d-red', 'btn-3d-blue', 'btn-3d-yellow', 'btn-3d-green'];
                return colors[idx % 4];
            },

            getOptionShape(idx) {
                const shapes = ['▲', '◆', '●', '◼'];
                return shapes[idx % 4];
            },

            startTimer(seconds) {
                clearInterval(this.timerInterval);
                this.timeLimitSeconds = seconds;
                this.timeLeft = seconds;
                this.questionStartTime = Date.now();

                this.timerInterval = setInterval(() => {
                    this.timeLeft--;
                    if (this.timeLeft <= 5 && this.timeLeft > 0 && window.SmartFunAudio) {
                        window.SmartFunAudio.playTick();
                    }
                    if (this.timeLeft <= 0) {
                        clearInterval(this.timerInterval);
                        this.timeLeft = 0;
                        // Auto-submit empty if time runs out
                        this.chooseOption(null);
                    }
                }, 1000);
            },

            async chooseOption(optionId) {
                if (this.isSubmittingAnswer) return;
                this.isSubmittingAnswer = true;
                this.currentStep = 'waiting';

                const responseTimeMs = Date.now() - this.questionStartTime;
                clearInterval(this.timerInterval);

                const qId = this.currentQuestion ? (this.currentQuestion.questionId || this.currentQuestion.id) : null;
                if (!qId) {
                    console.error('No question ID found to submit');
                    this.currentStep = 'active';
                    this.isSubmittingAnswer = false;
                    return;
                }

                // Safety watchdog: ensure player is never permanently stuck in 'waiting'
                const safetyTimer = setTimeout(() => {
                    if (this.currentStep === 'waiting') {
                        if (this.lastAnswerResult || this.answeredQuestionIds.includes(qId)) {
                            this.currentStep = 'feedback';
                        } else {
                            this.currentStep = 'active';
                        }
                        this.isSubmittingAnswer = false;
                    }
                }, 4000);

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

                try {
                    const res = await fetch('{{ route("game.answer") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            player_id: this.player.id,
                            question_id: qId,
                            option_id: optionId,
                            response_time_ms: responseTimeMs,
                            active_powerup: this.activePowerUp
                        })
                    });

                    clearTimeout(safetyTimer);

                    let data = null;
                    try {
                        data = await res.json();
                    } catch (parseErr) {
                        console.error('Failed to parse response as JSON:', parseErr);
                    }

                    if (data && data.success && data.result) {
                        this.lastAnswerResult = data.result;
                        this.player.score = data.result.total_score;
                        this.player.streak = data.result.current_streak;
                        this.player.rank = data.result.player_rank || this.player.rank;

                        if (data.result.leaderboard) {
                            this.leaderboard = data.result.leaderboard;
                            this.podium = data.result.leaderboard.slice(0, 3);
                        }

                        if (data.result.is_correct) {
                            if (window.SmartFunAudio) window.SmartFunAudio.playCorrect();
                            if (window.confetti) window.confetti({ particleCount: 60, spread: 60 });
                        } else {
                            if (window.SmartFunAudio) window.SmartFunAudio.playWrong();
                        }

                        if (!this.answeredQuestionIds.includes(qId)) {
                            this.answeredQuestionIds.push(qId);
                        }

                        // Consume active powerup
                        if (this.activePowerUp) {
                            this.inventory = this.inventory.filter(p => p !== this.activePowerUp);
                            this.activePowerUp = null;
                        }

                        this.currentStep = 'feedback';

                        // In self-paced modes or if last question, auto-advance countdown starts
                        if (this.sessionMode !== 'live_host' || data.result.is_last_question) {
                            this.startAutoAdvanceCountdown();
                        }
                    } else {
                        console.error('Answer submission returned unsuccessful:', data);

                        // If already answered, gracefully transition to feedback
                        if (data && data.result) {
                            this.lastAnswerResult = data.result;
                            this.player.score = data.result.total_score;
                            this.player.streak = data.result.current_streak;
                            this.player.rank = data.result.player_rank || this.player.rank;
                            if (data.result.leaderboard) {
                                this.leaderboard = data.result.leaderboard;
                                this.podium = data.result.leaderboard.slice(0, 3);
                            }
                            if (!this.answeredQuestionIds.includes(qId)) {
                                this.answeredQuestionIds.push(qId);
                            }
                            this.currentStep = 'feedback';
                            if (this.sessionMode !== 'live_host' || data.result.is_last_question) {
                                this.startAutoAdvanceCountdown();
                            }
                        } else {
                            // Revert step back to active so user can retry
                            this.currentStep = 'active';
                            alert(data?.message || 'Gagal mengirim jawaban (Status: ' + res.status + '). Silakan coba pilih kembali.');
                        }
                    }
                } catch (e) {
                    clearTimeout(safetyTimer);
                    console.error('Answer submission network error:', e);
                    this.currentStep = 'active';
                    alert('Terjadi gangguan jaringan saat mengirim jawaban. Silakan coba pilih kembali.');
                } finally {
                    this.isSubmittingAnswer = false;
                }
            },

            checkAnswerStatus() {
                if (this.lastAnswerResult) {
                    this.currentStep = 'feedback';
                    return;
                }
                const qId = this.currentQuestion ? (this.currentQuestion.questionId || this.currentQuestion.id) : null;
                if (qId && this.answeredQuestionIds.includes(qId)) {
                    this.currentStep = 'feedback';
                } else {
                    this.currentStep = 'active';
                }
                this.pollState();
            },

            startAutoAdvanceCountdown() {
                clearInterval(this.autoAdvanceInterval);
                this.autoAdvanceSeconds = 4;

                this.autoAdvanceInterval = setInterval(() => {
                    this.autoAdvanceSeconds--;
                    if (this.autoAdvanceSeconds <= 0) {
                        clearInterval(this.autoAdvanceInterval);
                        this.advanceNext();
                    }
                }, 1000);
            },

            advanceNext() {
                clearInterval(this.autoAdvanceInterval);

                // If last question was answered (in any mode), finish immediately and show podium
                if ((this.lastAnswerResult && this.lastAnswerResult.is_last_question) || !this.lastAnswerResult?.next_question) {
                    this.isPlayerFinished = true;
                    this.sessionStatus = 'finished';
                    this.podium = (this.leaderboard || []).slice(0, 3);
                    if (window.SmartFunAudio) window.SmartFunAudio.playFanfare();
                    if (window.confetti) window.confetti({ particleCount: 150, spread: 90 });
                    return;
                }

                // In Kahoot mode, student cannot advance ahead of the host to next question!
                if (this.sessionMode === 'live_host') {
                    this.currentStep = 'feedback';
                    return;
                }

                // If we have next_question in lastAnswerResult (Student-paced / Quizizz / Wayground)
                if (this.lastAnswerResult && this.lastAnswerResult.next_question) {
                    this.currentQuestion = this.lastAnswerResult.next_question;
                    this.currentQuestionIndex = this.lastAnswerResult.next_question.questionIndex;
                    this.currentStep = 'active';
                    this.eliminatedOptionIds = [];
                    this.activePowerUp = null;
                    this.startTimer(this.currentQuestion.timeLimit || 30);
                    if (window.SmartFunAudio) window.SmartFunAudio.playPop();
                    return;
                }
            },

            connectRealtime() {
                try {
                    const evtSource = new EventSource('/api/game/' + this.pin + '/stream');
                    evtSource.onmessage = (event) => {
                        const state = JSON.parse(event.data);
                        this.handleStateUpdate(state);
                    };
                    evtSource.onerror = () => {
                        evtSource.close();
                        setInterval(() => this.pollState(), 2000);
                    };
                } catch (e) {
                    setInterval(() => this.pollState(), 2000);
                }
            },

            async pollState() {
                try {
                    const res = await fetch('/api/game/' + this.pin + '/state');
                    const state = await res.json();
                    this.handleStateUpdate(state);
                } catch (e) {}
            },

            handleStateUpdate(state) {
                if (!state || state.error) return;

                if (!this.isPlayerFinished) {
                    this.sessionStatus = state.status;
                }
                this.sessionMode = state.mode;
                this.connectedPlayersCount = state.players ? state.players.length : this.connectedPlayersCount;

                if (state.theme && state.theme !== this.currentTheme) {
                    this.currentTheme = state.theme;
                    if (this.isBgmPlaying && window.SmartFunAudio) {
                        window.SmartFunAudio.setTheme(state.theme);
                    }
                }

                // Update leaderboard & podium
                if (state.leaderboard) {
                    this.leaderboard = state.leaderboard;
                    this.podium = state.leaderboard.slice(0, 3);
                    const me = state.leaderboard.find(p => p.id === this.player.id);
                    if (me) {
                        this.player.rank = me.rank;
                        this.player.score = me.score;
                        this.player.streak = me.streak;
                    }
                }

                // Auto-advance if answer result exists while waiting
                if (this.currentStep === 'waiting' && this.lastAnswerResult) {
                    this.currentStep = 'feedback';
                }

                // Handle Game Finish from Server
                if (state.status === 'finished') {
                    this.isPlayerFinished = true;
                    this.sessionStatus = 'finished';
                    this.podium = (this.leaderboard || []).slice(0, 3);
                    if (window.SmartFunAudio) window.SmartFunAudio.playFanfare();
                    if (window.confetti) window.confetti({ particleCount: 150, spread: 90 });
                    return;
                }

                // In Host-paced mode (Kahoot): Question changed by Host
                if (!this.isPlayerFinished && this.sessionMode === 'live_host' && state.status === 'active' && state.currentQuestion) {
                    const hostQ = state.currentQuestion;
                    const isNewQuestion = (!this.currentQuestion || !this.currentQuestion.questionId) 
                        || hostQ.questionIndex > this.currentQuestionIndex
                        || (hostQ.questionId !== this.currentQuestion.questionId && !this.answeredQuestionIds.includes(hostQ.questionId));

                    if (isNewQuestion) {
                        clearInterval(this.autoAdvanceInterval);
                        clearInterval(this.timerInterval);
                        this.currentQuestion = hostQ;
                        this.currentQuestionIndex = hostQ.questionIndex;
                        this.totalQuestions = state.totalQuestions;
                        this.eliminatedOptionIds = [];
                        this.activePowerUp = null;
                        this.lastAnswerResult = null;

                        if (this.answeredQuestionIds.includes(hostQ.questionId)) {
                            this.currentStep = 'feedback';
                        } else {
                            this.currentStep = 'active';
                            this.startTimer(hostQ.timeLimit || 30);
                            if (window.SmartFunAudio) window.SmartFunAudio.playPop();
                        }
                    }
                }

                // If Game Started from Lobby
                if (!this.isPlayerFinished && state.status === 'active' && (!this.currentQuestion || !this.currentQuestion.questionId) && state.currentQuestion) {
                    this.currentQuestion = state.currentQuestion;
                    this.currentQuestionIndex = state.currentQuestion.questionIndex;
                    this.totalQuestions = state.totalQuestions;
                    if (!this.answeredQuestionIds.includes(state.currentQuestion.questionId)) {
                        this.currentStep = 'active';
                        this.startTimer(state.currentQuestion.timeLimit || 30);
                    } else {
                        this.currentStep = 'feedback';
                    }
                }
            }
        };
    }
</script>
@endsection
