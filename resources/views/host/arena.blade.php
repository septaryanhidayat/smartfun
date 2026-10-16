@extends('layouts.app')

@section('content')
<div 
    class="min-h-[calc(100vh-4rem)] flex flex-col justify-between py-4 px-4 sm:px-8 max-w-7xl mx-auto w-full relative"
    x-data="hostArenaComponent()">

    <!-- Ambient Theme Backdrop -->
    <div class="fixed inset-0 pointer-events-none transition-all duration-1000 -z-10" :class="getThemeAmbianceClass()"></div>

    <!-- Host HUD Bar -->
    <div class="w-full bg-[#1c083b]/90 rounded-3xl border border-purple-800/60 p-4 sm:p-5 backdrop-blur-md shadow-2xl flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider" :class="getModeBadgeClass()">
                <span x-text="getModeLabel()"></span>
            </span>
            <h1 class="text-lg sm:text-2xl font-black text-white leading-tight">
                {{ $session->quiz->title }}
            </h1>
        </div>

        <div class="flex items-center gap-2.5 sm:gap-4 flex-wrap">
            <!-- Theme & BGM Audio Widget -->
            <div class="flex items-center gap-2 bg-purple-950/90 px-3 py-1.5 rounded-2xl border border-purple-700/80 shadow-md">
                <!-- Theme Switcher Dropdown -->
                <div class="relative" x-data="{ openThemeMenu: false }">
                    <button 
                        type="button" 
                        @click="openThemeMenu = !openThemeMenu" 
                        class="flex items-center gap-1.5 text-xs font-black text-amber-300 hover:text-white transition-colors cursor-pointer"
                        title="Ganti Suasana & Musik">
                        <span x-text="getThemeIcon(currentTheme)"></span>
                        <span class="hidden md:inline" x-text="getThemeName(currentTheme)"></span>
                        <span class="text-[9px] opacity-70">▼</span>
                    </button>

                    <div 
                        x-show="openThemeMenu" 
                        @click.away="openThemeMenu = false"
                        x-cloak
                        class="absolute right-0 mt-2 w-52 bg-[#180630] border border-purple-700/80 rounded-2xl shadow-2xl p-2 z-50 space-y-1 backdrop-blur-md">
                        <div class="px-2 py-1 text-[10px] uppercase font-black text-purple-400">Pilih Suasana & BGM:</div>
                        <template x-for="t in [
                            { id: 'arcade', name: '🕹️ Arcade Neon', desc: 'Chiptune EDM Party' },
                            { id: 'space', name: '🚀 Galaksi Space', desc: 'Synthwave Cosmic' },
                            { id: 'fantasy', name: '🌿 Hutan Kristal', desc: 'Celtic Adventure' },
                            { id: 'cyberpunk', name: '⚡ Cyberpunk 2099', desc: 'High-Octane Techno' },
                            { id: 'lava', name: '🔥 Lava Showdown', desc: 'Fiery Rock-Synth' }
                        ]" :key="t.id">
                            <button 
                                type="button" 
                                @click="switchTheme(t.id); openThemeMenu = false;"
                                class="w-full text-left px-2.5 py-1.5 rounded-xl text-xs font-bold hover:bg-purple-900/70 transition-colors flex items-center justify-between cursor-pointer"
                                :class="currentTheme === t.id ? 'text-amber-300 bg-purple-900/60 font-black' : 'text-purple-200'">
                                <div>
                                    <span x-text="t.name" class="block"></span>
                                    <span x-text="t.desc" class="text-[9px] opacity-75 block"></span>
                                </div>
                                <span x-show="currentTheme === t.id" class="text-xs">✓</span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="h-4 w-px bg-purple-800"></div>

                <!-- BGM Toggle Button -->
                <button 
                    type="button" 
                    @click="toggleBgm()" 
                    class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-black transition-all cursor-pointer"
                    :class="isBgmPlaying ? 'bg-emerald-500/25 text-emerald-300 border border-emerald-500/50 shadow-sm shadow-emerald-500/20' : 'bg-purple-900/40 text-purple-400 border border-purple-800 hover:text-purple-200'"
                    :title="isBgmPlaying ? 'Klik untuk Jeda Musik Backsound' : 'Klik untuk Putar Musik Backsound Seru'">
                    <span x-text="isBgmPlaying ? '🎵 BGM: ON' : '🔇 BGM: OFF'"></span>
                </button>
            </div>

            <!-- Joined Players Count -->
            <div class="flex items-center gap-2 bg-purple-950/80 px-3.5 py-2 rounded-2xl border border-purple-800">
                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="text-xs sm:text-sm font-black text-purple-200">
                    <span x-text="players.length"></span> Siswa
                </span>
            </div>

            <!-- PIN Badge -->
            <div class="bg-gradient-to-r from-amber-400 to-yellow-500 text-slate-950 px-4 py-1.5 rounded-2xl font-black tracking-widest text-lg sm:text-xl shadow-lg shadow-amber-400/20">
                PIN: {{ $session->pin }}
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STAGE 1: LOBBY SCREEN (PROJECTOR READY)   -->
    <!-- ========================================== -->
    <div x-show="sessionStatus === 'lobby'" class="flex-1 flex flex-col justify-between py-6">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center mb-8">
            <!-- Left: QR Code & Join Instructions -->
            <div class="bg-[#180630]/90 rounded-3xl border border-purple-800/60 p-6 sm:p-8 flex flex-col items-center text-center shadow-xl backdrop-blur-md">
                <span class="text-xs font-bold uppercase tracking-wider text-purple-400 mb-2">Scan untuk Bergabung</span>
                
                <!-- QR Code Canvas -->
                <div class="p-3 bg-white rounded-2xl shadow-xl mb-4">
                    <canvas id="qrCodeCanvas" class="w-48 h-48"></canvas>
                </div>

                <div class="text-xs text-purple-300">
                    Buka peramban di HP: <br>
                    <strong class="text-amber-300 text-sm font-black">{{ url('/join') }}</strong>
                </div>
            </div>

            <!-- Center: Giant PIN & Start Action -->
            <div class="lg:col-span-2 flex flex-col items-center justify-center text-center p-6 sm:p-8">
                <span class="text-xs uppercase font-extrabold tracking-widest text-purple-300 mb-2">Game PIN Layar Utama</span>
                
                <div class="text-6xl sm:text-8xl font-black tracking-[0.2em] text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-rose-300 to-purple-300 mb-6 drop-shadow-lg">
                    {{ $session->pin }}
                </div>

                <button 
                    type="button" 
                    @click="startGame()"
                    :disabled="isStarting || players.length === 0"
                    class="py-5 px-12 rounded-3xl font-black text-xl sm:text-2xl btn-3d btn-3d-purple flex items-center gap-3 tracking-wide cursor-pointer disabled:opacity-40 shadow-2xl shadow-purple-900/80 animate-pulse">
                    <span>🚀</span> MULAI PERMAINAN SEKARANG
                </button>

                <p class="text-xs text-purple-400 mt-4" x-show="players.length === 0">
                    Menunggu siswa memasukkan PIN dan memilih avatar...
                </p>
            </div>
        </div>

        <!-- Connected Players Roster Grid -->
        <div class="bg-[#180630]/70 rounded-3xl border border-purple-800/50 p-6">
            <h3 class="text-sm font-extrabold uppercase tracking-wider text-purple-300 mb-4 flex items-center justify-between">
                <span>Pemain Bergabung (<span x-text="players.length"></span>)</span>
                <span class="text-xs text-purple-400 font-normal">Nama akan melayang masuk otomatis</span>
            </h3>

            <div class="flex flex-wrap gap-3 max-h-56 overflow-y-auto p-2">
                <template x-for="p in players" :key="p.id">
                    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-purple-950/90 border border-purple-700/60 shadow animate-pop-in">
                        <span class="text-2xl" x-text="getAvatarEmoji(p.avatar)"></span>
                        <span class="font-extrabold text-sm text-white" x-text="p.nickname"></span>
                    </div>
                </template>
            </div>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- STAGE 2: ACTIVE QUESTION SCREEN            -->
    <!-- ========================================== -->
    <div x-show="sessionStatus === 'active' && hostStep === 'question'" class="flex-1 flex flex-col justify-between py-4">
        
        <!-- Question Meta & Controls Bar -->
        <div class="flex items-center justify-between mb-4">
            <div class="text-sm sm:text-base font-black text-purple-300">
                Soal <span x-text="currentQuestion.questionIndex"></span> / <span x-text="totalQuestions"></span>
            </div>

            <!-- Host Actions -->
            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    @click="endQuestionNow()"
                    class="py-2.5 px-4 rounded-xl text-xs font-black btn-3d btn-3d-yellow">
                    ⏱️ Tutup Waktu & Tampilkan Jawaban
                </button>
            </div>
        </div>

        <!-- Big Question Card -->
        <div class="bg-[#1c083b]/90 rounded-3xl border border-purple-700/50 p-6 sm:p-10 shadow-2xl text-center mb-6 backdrop-blur-md">
            <template x-if="currentQuestion.media">
                <div class="max-w-md mx-auto max-h-64 overflow-hidden rounded-2xl mb-6 border border-purple-900/60 shadow-lg">
                    <img :src="currentQuestion.media" alt="Media Soal" class="w-full h-full object-cover">
                </div>
            </template>
            <h2 class="text-2xl sm:text-4xl font-black text-white leading-tight" x-text="currentQuestion.text"></h2>
        </div>

        <!-- Live Answer Progress Bar -->
        <div class="w-full bg-[#180630]/90 rounded-2xl border border-purple-800/60 p-4 mb-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-1">
                <span class="text-2xl">⚡</span>
                <div class="flex-1">
                    <div class="flex justify-between text-xs font-extrabold text-purple-300 mb-1">
                        <span>Respon Siswa Masuk</span>
                        <span x-text="answeredCount + ' / ' + players.length + ' Siswa'"></span>
                    </div>
                    <div class="w-full h-3 bg-purple-950 rounded-full overflow-hidden border border-purple-800">
                        <div 
                            class="h-full bg-gradient-to-r from-amber-400 to-emerald-400 rounded-full transition-all duration-300"
                            :style="'width: ' + ((players.length > 0 ? (answeredCount / players.length) : 0) * 100) + '%'"></div>
                    </div>
                </div>
            </div>

            <div class="text-right pl-4 border-l border-purple-800">
                <span class="text-xs uppercase font-extrabold text-purple-400 block">Sisa Waktu</span>
                <span class="text-2xl sm:text-3xl font-mono font-black text-amber-400" x-text="timeLeft + 's'"></span>
            </div>
        </div>

        <!-- 4 Options Grid (Teacher view) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <template x-for="(opt, idx) in currentQuestion.options" :key="opt.id">
                <div 
                    :class="getOptionColorClass(idx)"
                    class="btn-3d p-4 sm:p-5 rounded-2xl flex items-center gap-4 text-left font-bold text-base min-h-[70px]">
                    <span class="w-10 h-10 rounded-xl bg-black/20 flex items-center justify-center text-xl shrink-0 font-black" x-text="['▲', '◆', '●', '◼'][idx]"></span>
                    <span class="flex-1" x-text="opt.text"></span>
                </div>
            </template>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- STAGE 3: RESULT & ANSWER DISTRIBUTION     -->
    <!-- ========================================== -->
    <div x-show="sessionStatus === 'active' && hostStep === 'result'" class="flex-1 flex flex-col justify-between py-4 animate-pop-in">
        
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-black text-white">Distribusi Jawaban Siswa</h2>
            <button 
                type="button" 
                @click="showLeaderboardScreen()"
                class="py-2.5 px-6 rounded-xl font-black text-sm btn-3d btn-3d-purple">
                Papan Peringkat Sementara →
            </button>
        </div>

        <!-- Bar Chart Distribution -->
        <div class="bg-[#180630]/90 rounded-3xl border border-purple-800/60 p-6 sm:p-8 mb-6 shadow-2xl backdrop-blur-md">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 items-end h-64 pb-4 border-b border-purple-900/60">
                <template x-for="(opt, idx) in currentQuestion.options" :key="opt.id">
                    <div class="flex flex-col items-center h-full justify-end gap-2">
                        <!-- Vote Count -->
                        <span class="text-lg font-black text-white" x-text="getOptionVotes(opt.id)"></span>

                        <!-- Column Bar -->
                        <div 
                            :class="[
                                getOptionColorClass(idx),
                                (questionResult && questionResult.correctOptionId === opt.id) ? 'ring-4 ring-amber-400 shadow-xl shadow-amber-400/40' : 'opacity-80'
                            ]"
                            class="w-full rounded-2xl transition-all duration-700 min-h-[30px]"
                            :style="'height: ' + getVoteHeightPercent(opt.id) + '%'"></div>

                        <!-- Option Title -->
                        <div class="text-center">
                            <span class="text-xs font-black block" x-text="['▲ Merah', '◆ Biru', '● Kuning', '◼ Hijau'][idx]"></span>
                            <template x-if="questionResult && questionResult.correctOptionId === opt.id">
                                <span class="text-[10px] font-black text-amber-300 block">✓ BENAR</span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Explanation Card -->
            <template x-if="questionResult && questionResult.explanation">
                <div class="mt-4 p-4 rounded-2xl bg-purple-950/60 border border-purple-800 text-purple-200 text-sm">
                    <strong class="text-amber-300">💡 Penjelasan Jawaban:</strong>
                    <span x-text="questionResult.explanation"></span>
                </div>
            </template>
        </div>

        <button 
            type="button" 
            @click="nextQuestion()"
            class="w-full py-4 rounded-2xl font-black text-lg btn-3d btn-3d-purple">
            Lanjut ke Soal Berikutnya →
        </button>
    </div>

    <!-- ========================================== -->
    <!-- STAGE 4: LIVE LEADERBOARD SCREEN          -->
    <!-- ========================================== -->
    <div x-show="sessionStatus === 'active' && hostStep === 'leaderboard'" class="flex-1 flex flex-col justify-between py-4 animate-pop-in">
        
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl sm:text-3xl font-black text-white flex items-center gap-2">
                <span>🔥</span> Papan Peringkat Peserta (<span x-text="leaderboard.length"></span> Siswa)
            </h2>
            <button 
                type="button" 
                @click="nextQuestion()"
                class="py-2.5 px-6 rounded-xl font-black text-sm btn-3d btn-3d-purple">
                Lanjut ke Soal Berikutnya →
            </button>
        </div>

        <div class="space-y-2.5 mb-6 max-w-3xl mx-auto w-full max-h-[55vh] overflow-y-auto pr-2 custom-scrollbar">
            <template x-for="(p, idx) in leaderboard" :key="p.id">
                <div 
                    :class="{
                        'bg-purple-900/70 border-amber-400/80 ring-1 ring-amber-400/30': idx === 0,
                        'bg-purple-950/80 border-purple-700/60': idx > 0
                    }"
                    class="flex items-center justify-between p-3.5 sm:p-4 rounded-2xl border shadow-lg animate-pop-in">
                    <div class="flex items-center gap-3.5 sm:gap-4">
                        <span 
                            :class="{
                                'text-amber-300 text-2xl font-black': idx === 0,
                                'text-slate-300 text-xl font-black': idx === 1,
                                'text-amber-600 text-xl font-black': idx === 2,
                                'text-purple-400 text-base font-bold': idx > 2
                            }"
                            class="font-black w-8 text-center" 
                            x-text="'#' + (idx + 1)"></span>

                        <span class="text-2xl sm:text-3xl" x-text="getAvatarEmoji(p.avatar)"></span>

                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-white text-sm sm:text-base" x-text="p.nickname"></span>
                                <span x-show="idx === 0" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950">👑 Pemimpin</span>
                            </div>
                            <span class="text-[11px] text-orange-400 font-bold" x-show="p.streak > 1" x-text="'🔥 ' + p.streak + ' Streak'"></span>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-lg sm:text-xl font-black text-amber-300" x-text="p.score.toLocaleString('id-ID')"></span>
                        <span class="text-[10px] text-purple-400 block uppercase font-bold">Poin</span>
                    </div>
                </div>
            </template>
        </div>

        <button 
            type="button" 
            @click="nextQuestion()"
            class="w-full py-4 rounded-2xl font-black text-lg btn-3d btn-3d-purple">
            Lanjut ke Soal Berikutnya →
        </button>
    </div>

    <!-- ========================================== -->
    <!-- STAGE 5: GRAND PODIUM & FINALE SCREEN     -->
    <!-- ========================================== -->
    <div x-show="sessionStatus === 'finished'" class="flex-1 flex flex-col justify-between py-6 animate-pop-in">
        
        <div class="text-center mb-8">
            <span class="text-5xl animate-bounce inline-block mb-2">🎉</span>
            <h1 class="text-3xl sm:text-5xl font-black text-white">Podium Kejuaraan SmartFun!</h1>
            <p class="text-sm text-purple-300">Selamat kepada para juara dan semua pejuang ilmu!</p>
        </div>

        <!-- 3D Podium Display -->
        <div class="flex items-end justify-center gap-3 sm:gap-6 mb-10 max-w-2xl mx-auto w-full px-4">
            
            <!-- Juara 2 (Perak) -->
            <div class="flex-1 flex flex-col items-center">
                <template x-if="podium[1]">
                    <div class="flex flex-col items-center mb-2">
                        <span class="text-3xl sm:text-4xl" x-text="getAvatarEmoji(podium[1].avatar)"></span>
                        <span class="text-xs sm:text-sm font-black text-white mt-1 text-center line-clamp-1" x-text="podium[1].nickname"></span>
                        <span class="text-xs font-bold text-amber-300" x-text="podium[1].score + ' Poin'"></span>
                    </div>
                </template>
                <div class="w-full h-40 bg-gradient-to-t from-slate-600 to-slate-400 rounded-t-3xl border-t-4 border-slate-300 flex items-center justify-center shadow-xl">
                    <span class="text-4xl font-black text-slate-900">2</span>
                </div>
            </div>

            <!-- Juara 1 (Emas - Podium Tertinggi) -->
            <div class="flex-1 flex flex-col items-center -mt-6">
                <template x-if="podium[0]">
                    <div class="flex flex-col items-center mb-2">
                        <span class="text-2xl animate-bounce">👑</span>
                        <span class="text-4xl sm:text-5xl" x-text="getAvatarEmoji(podium[0].avatar)"></span>
                        <span class="text-sm sm:text-base font-black text-amber-300 mt-1 text-center line-clamp-1" x-text="podium[0].nickname"></span>
                        <span class="text-sm font-bold text-white" x-text="podium[0].score + ' Poin'"></span>
                    </div>
                </template>
                <div class="w-full h-56 bg-gradient-to-t from-amber-600 via-amber-500 to-yellow-400 rounded-t-3xl border-t-4 border-yellow-200 flex items-center justify-center shadow-2xl">
                    <span class="text-5xl font-black text-slate-950">1</span>
                </div>
            </div>

            <!-- Juara 3 (Perunggu) -->
            <div class="flex-1 flex flex-col items-center">
                <template x-if="podium[2]">
                    <div class="flex flex-col items-center mb-2">
                        <span class="text-3xl sm:text-4xl" x-text="getAvatarEmoji(podium[2].avatar)"></span>
                        <span class="text-xs sm:text-sm font-black text-white mt-1 text-center line-clamp-1" x-text="podium[2].nickname"></span>
                        <span class="text-xs font-bold text-amber-300" x-text="podium[2].score + ' Poin'"></span>
                    </div>
                </template>
                <div class="w-full h-32 bg-gradient-to-t from-amber-800 to-amber-700 rounded-t-3xl border-t-4 border-amber-500 flex items-center justify-center shadow-xl">
                    <span class="text-4xl font-black text-amber-950">3</span>
                </div>
            </div>

        </div>

        <!-- Complete Ranking Table for ALL Participants -->
        <div class="bg-[#180630]/90 rounded-3xl border border-purple-800/60 p-6 sm:p-8 mb-8 shadow-2xl backdrop-blur-md max-w-4xl mx-auto w-full">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-purple-900/60">
                <h3 class="text-lg sm:text-xl font-black text-white flex items-center gap-2">
                    <span>🏆</span> Peringkat Lengkap Seluruh Peserta (<span x-text="leaderboard.length"></span> Siswa)
                </h3>
                <span class="text-xs font-bold text-amber-300">Hasil Akhir Sesi</span>
            </div>

            <div class="space-y-2.5 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                <template x-for="(p, idx) in leaderboard" :key="p.id">
                    <div 
                        :class="{
                            'bg-gradient-to-r from-amber-500/20 via-purple-950/60 to-purple-950/60 border-amber-400/80 ring-1 ring-amber-400/40': idx === 0,
                            'bg-gradient-to-r from-slate-400/15 via-purple-950/60 to-purple-950/60 border-slate-400/60': idx === 1,
                            'bg-gradient-to-r from-amber-700/15 via-purple-950/60 to-purple-950/60 border-amber-700/60': idx === 2,
                            'bg-purple-950/60 border-purple-800/60': idx > 2
                        }"
                        class="flex items-center justify-between p-3.5 rounded-2xl border transition-all">
                        <div class="flex items-center gap-3.5">
                            <span 
                                :class="{
                                    'text-amber-300 text-xl font-black': idx === 0,
                                    'text-slate-300 text-lg font-black': idx === 1,
                                    'text-amber-600 text-lg font-black': idx === 2,
                                    'text-purple-400 text-sm font-bold': idx > 2
                                }"
                                class="w-8 text-center" 
                                x-text="'#' + (idx + 1)"></span>

                            <span class="text-2xl sm:text-3xl" x-text="getAvatarEmoji(p.avatar)"></span>

                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-white text-sm sm:text-base" x-text="p.nickname"></span>
                                    <span x-show="idx === 0" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950">Juara 1 👑</span>
                                    <span x-show="idx === 1" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-300 text-slate-950">Juara 2 🥈</span>
                                    <span x-show="idx === 2" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-700 text-white">Juara 3 🥉</span>
                                </div>
                                <span class="text-[11px] text-orange-400 font-bold" x-show="p.streak > 1" x-text="'🔥 ' + p.streak + ' Streak Berturut-turut'"></span>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-lg sm:text-xl font-black text-amber-300" x-text="p.score.toLocaleString('id-ID')"></span>
                            <span class="text-[10px] text-purple-400 block uppercase font-bold tracking-wider">Total Poin</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Post-Game Actions & Export -->
        <div class="bg-[#180630]/90 rounded-3xl border border-purple-800/60 p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-purple-400">Analisis Hasil Sesi</span>
                <p class="text-sm text-purple-200">Unduh data nilai dan akurasi jawaban seluruh siswa ke Excel / CSV.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('host.export', $session->pin) }}" class="py-3 px-6 rounded-2xl font-black text-sm btn-3d btn-3d-green flex items-center gap-2">
                    <span>📊</span> Unduh Rekap Nilai (.CSV)
                </a>
                <a href="{{ route('host.index') }}" class="py-3 px-5 rounded-2xl font-bold text-sm bg-purple-950 text-purple-200 border border-purple-800">
                    Selesai
                </a>
            </div>
        </div>

    </div>

</div>

<script>
    function hostArenaComponent() {
        return {
            pin: '{{ $session->pin }}',
            sessionStatus: '{{ $session->status }}',
            sessionMode: '{{ $session->mode }}',
            totalQuestions: {{ $totalSessionQuestions ?? $session->quiz->questions->count() }},
            currentTheme: '{{ $session->settings["theme"] ?? $session->quiz->theme ?? "arcade" }}',
            isBgmPlaying: false,
            bgmConfigEnabled: {{ ($session->settings["bgm"] ?? true) ? 'true' : 'false' }},
            
            hostStep: 'question', // 'question', 'result', 'leaderboard'
            players: @json($session->players),
            leaderboard: [],
            podium: [],
            
            currentQuestion: {
                questionIndex: 1,
                questionId: 0,
                text: '',
                media: null,
                options: []
            },

            answeredCount: 0,
            timeLeft: 30,
            timerInterval: null,
            isStarting: false,
            questionResult: null,

            init() {
                this.renderQRCode();
                this.connectRealtime();
                this.pollState();

                // If BGM is enabled and audio engine is ready, allow click to start
                if (this.bgmConfigEnabled && window.SmartFunAudio) {
                    window.SmartFunAudio.setTheme(this.currentTheme);
                }

                if (this.sessionStatus === 'finished') {
                    this.triggerConfetti();
                }
            },

            getThemeIcon(theme) {
                const icons = {
                    arcade: '🕹️',
                    space: '🚀',
                    fantasy: '🌿',
                    cyberpunk: '⚡',
                    lava: '🔥'
                };
                return icons[theme] || '🕹️';
            },

            getThemeName(theme) {
                const names = {
                    arcade: 'Arcade Neon',
                    space: 'Galaksi Space',
                    fantasy: 'Hutan Kristal',
                    cyberpunk: 'Cyberpunk',
                    lava: 'Lava Showdown'
                };
                return names[theme] || 'Arcade Neon';
            },

            getThemeAmbianceClass() {
                const ambiance = {
                    arcade: 'bg-[radial-gradient(ellipse_at_top,_rgba(236,72,153,0.22),_rgba(107,33,168,0.18),_transparent_70%)]',
                    space: 'bg-[radial-gradient(ellipse_at_top,_rgba(99,102,241,0.28),_rgba(88,28,135,0.18),_transparent_70%)]',
                    fantasy: 'bg-[radial-gradient(ellipse_at_top,_rgba(16,185,129,0.22),_rgba(13,148,136,0.18),_transparent_70%)]',
                    cyberpunk: 'bg-[radial-gradient(ellipse_at_top,_rgba(6,182,212,0.22),_rgba(37,99,235,0.18),_transparent_70%)]',
                    lava: 'bg-[radial-gradient(ellipse_at_top,_rgba(244,63,94,0.28),_rgba(245,158,11,0.20),_transparent_70%)]'
                };
                return ambiance[this.currentTheme] || ambiance.arcade;
            },

            switchTheme(newTheme) {
                this.currentTheme = newTheme;
                if (window.SmartFunAudio) {
                    window.SmartFunAudio.setTheme(newTheme);
                }
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

            getModeBadgeClass() {
                if (this.sessionMode === 'live_host') return 'bg-rose-950 text-rose-300 border border-rose-800';
                if (this.sessionMode === 'wayground_map') return 'bg-emerald-950 text-emerald-300 border border-emerald-800';
                return 'bg-purple-950 text-purple-300 border border-purple-800';
            },

            getModeLabel() {
                if (this.sessionMode === 'live_host') return '🎯 Mode Kahoot (Host-Paced)';
                if (this.sessionMode === 'wayground_map') return '🗺️ Mode Wayground Quest';
                return '⚡ Mode Quizizz (Student-Paced)';
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

            getOptionColorClass(idx) {
                const colors = ['btn-3d-red', 'btn-3d-blue', 'btn-3d-yellow', 'btn-3d-green'];
                return colors[idx % 4];
            },

            renderQRCode() {
                const canvas = document.getElementById('qrCodeCanvas');
                if (canvas && window.QRCode) {
                    window.QRCode.toCanvas(canvas, '{{ $joinUrl }}', {
                        width: 180,
                        margin: 1,
                        color: {
                            dark: '#1e0a3c',
                            light: '#ffffff'
                        }
                    });
                }
            },

            async startGame() {
                this.isStarting = true;
                try {
                    const res = await fetch('/api/host/' + this.pin + '/start', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.sessionStatus = 'active';
                        this.hostStep = 'question';
                        if (this.bgmConfigEnabled && window.SmartFunAudio && !this.isBgmPlaying) {
                            window.SmartFunAudio.startBgm(this.currentTheme);
                            this.isBgmPlaying = true;
                        }
                        this.pollState();
                    }
                } catch (e) {
                    console.error('Error starting game:', e);
                } finally {
                    this.isStarting = false;
                }
            },

            startTimer(seconds) {
                clearInterval(this.timerInterval);
                this.timeLeft = seconds;

                this.timerInterval = setInterval(() => {
                    this.timeLeft--;
                    if (this.timeLeft <= 5 && this.timeLeft > 0 && window.SmartFunAudio) {
                        window.SmartFunAudio.playTick();
                    }
                    if (this.timeLeft <= 0) {
                        clearInterval(this.timerInterval);
                        this.timeLeft = 0;
                        this.endQuestionNow();
                    }
                }, 1000);
            },

            async endQuestionNow() {
                clearInterval(this.timerInterval);
                try {
                    const res = await fetch('/api/host/' + this.pin + '/end-question', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.questionResult = data.result;
                        this.hostStep = 'result';
                        if (window.SmartFunAudio) window.SmartFunAudio.playCorrect();
                    }
                } catch (e) {
                    console.error(e);
                }
            },

            showLeaderboardScreen() {
                this.hostStep = 'leaderboard';
            },

            async nextQuestion() {
                try {
                    const res = await fetch('/api/host/' + this.pin + '/next', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        if (data.result.action === 'game_finished') {
                            this.sessionStatus = 'finished';
                            this.podium = data.result.data.podium;
                            this.triggerConfetti();
                        } else {
                            this.hostStep = 'question';
                            this.answeredCount = 0;
                            this.pollState();
                        }
                    }
                } catch (e) {
                    console.error(e);
                }
            },

            getOptionVotes(optId) {
                if (this.questionResult && this.questionResult.answerDistribution) {
                    return this.questionResult.answerDistribution[optId] || 0;
                }
                return 0;
            },

            getVoteHeightPercent(optId) {
                const votes = this.getOptionVotes(optId);
                const maxVotes = Math.max(1, ...Object.values(this.questionResult?.answerDistribution || { 0: 1 }));
                return Math.max(10, (votes / maxVotes) * 100);
            },

            triggerConfetti() {
                if (window.SmartFunAudio) window.SmartFunAudio.playFanfare();
                if (window.confetti) {
                    window.confetti({ particleCount: 150, spread: 80, origin: { y: 0.6 } });
                    setTimeout(() => {
                        window.confetti({ particleCount: 100, spread: 100, origin: { y: 0.5 } });
                    }, 500);
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

                const prevPlayersCount = this.players.length;
                this.sessionStatus = state.status;
                this.players = state.players || [];
                this.leaderboard = state.leaderboard || [];
                if (state.answeredCount !== undefined) {
                    this.answeredCount = state.answeredCount;
                }

                // Audio pop when new player joins lobby
                if (this.sessionStatus === 'lobby' && this.players.length > prevPlayersCount && window.SmartFunAudio) {
                    window.SmartFunAudio.playPop();
                }

                if (state.status === 'active' && state.currentQuestion) {
                    if (this.currentQuestion.questionId !== state.currentQuestion.questionId) {
                        this.currentQuestion = state.currentQuestion;
                        this.hostStep = 'question';
                        this.answeredCount = 0;
                        this.startTimer(state.currentQuestion.timeLimit || 30);
                    }
                }

                if (state.status === 'finished') {
                    this.sessionStatus = 'finished';
                    // Build podium from leaderboard
                    this.podium = this.leaderboard.slice(0, 3);
                }
            }
        };
    }
</script>
@endsection
