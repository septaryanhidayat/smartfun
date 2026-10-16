@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden py-10 lg:py-16">
    <!-- Ambient Background Glows -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-600/20 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute bottom-10 left-10 w-[400px] h-[400px] bg-rose-600/15 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Hero Section -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple-900/60 border border-purple-500/40 text-purple-200 text-xs font-bold tracking-wide uppercase mb-6 animate-pulse-glow">
                <span>✨</span> Platform Kuis Interaktif Masa Depan
            </div>
            
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white leading-tight mb-6">
                Belajar Jadi <span class="bg-gradient-to-r from-amber-400 via-rose-500 to-purple-400 bg-clip-text text-transparent">Petualangan Seru</span> Bersama SmartFun!
            </h1>
            
            <p class="text-base sm:text-lg text-purple-200/80 leading-relaxed max-w-2xl mx-auto">
                Kombinasi terbaik sensasi <strong class="text-amber-400 font-bold">Quizizz</strong>, tensi seru <strong class="text-rose-400 font-bold">Kahoot</strong>, dan peta quest <strong class="text-emerald-400 font-bold">Wayground</strong> dengan audio interaktif & power-ups!
            </p>
        </div>

        <!-- Student Join PIN Box (Hero Card) -->
        <div class="max-w-md mx-auto mb-20">
            <div class="bg-[#1e0a3c]/90 p-6 sm:p-8 rounded-3xl border-2 border-purple-600/40 shadow-2xl shadow-purple-950 backdrop-blur-xl relative group">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-amber-500 to-rose-500 text-slate-950 font-black text-xs px-4 py-1 rounded-full uppercase tracking-wider shadow-md">
                    🕹️ Masuk Arena Siswa
                </div>

                <form action="{{ route('game.join.form') }}" method="GET" class="space-y-4 pt-2">
                    <div>
                        <label for="pin" class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2 text-center">
                            Masukkan 6-Digit Game PIN
                        </label>
                        <input 
                            type="text" 
                            name="pin" 
                            id="pin" 
                            maxlength="6" 
                            pattern="[0-9]*" 
                            inputmode="numeric"
                            placeholder="Contoh: 123456" 
                            required
                            autocomplete="off"
                            class="w-full text-center text-3xl font-black tracking-[0.3em] py-3.5 px-4 bg-purple-950/90 text-amber-300 placeholder:text-purple-700/60 rounded-2xl border-2 border-purple-500/50 focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-400/20 transition-all uppercase shadow-inner"
                        >
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-4 rounded-2xl font-black text-lg btn-3d btn-3d-purple flex items-center justify-center gap-2 tracking-wide cursor-pointer">
                        <span>🚀</span> GABUNG SEKARANG
                    </button>
                </form>

                <p class="text-center text-[11px] text-purple-400/80 mt-4">
                    Belum punya PIN? Minta guru Anda untuk memulai sesi kuis di layar depan!
                </p>
            </div>
        </div>

        <!-- 3 Core Modes Highlight -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20">
            <!-- Mode 1: Quizizz -->
            <div class="bg-[#1c083b]/70 border border-purple-800/40 p-6 rounded-3xl relative overflow-hidden group hover:border-purple-500/60 transition-all hover:-translate-y-1">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-2xl mb-4 shadow-lg">
                    ⚡
                </div>
                <h3 class="text-xl font-black text-white mb-2">Mekanik Quizizz</h3>
                <p class="text-sm text-purple-200/70 mb-4">
                    Ritme mandiri (student-paced), kartu power-ups acak (2x Multiplier, 50-50, Streak Shield), dan papan peringkat dinamis!
                </p>
                <span class="inline-block px-3 py-1 bg-purple-900/60 text-purple-300 text-xs font-semibold rounded-lg border border-purple-700/50">
                    Solo & Multi-Pemain
                </span>
            </div>

            <!-- Mode 2: Kahoot -->
            <div class="bg-[#1c083b]/70 border border-purple-800/40 p-6 rounded-3xl relative overflow-hidden group hover:border-purple-500/60 transition-all hover:-translate-y-1">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-2xl mb-4 shadow-lg">
                    🎯
                </div>
                <h3 class="text-xl font-black text-white mb-2">Mekanik Kahoot</h3>
                <p class="text-sm text-purple-200/70 mb-4">
                    Mode kendali layar guru (host-paced). Pertanyaan tampil di proyektor, siswa adu kecepatan respon serempak di ponsel!
                </p>
                <span class="inline-block px-3 py-1 bg-rose-950/60 text-rose-300 text-xs font-semibold rounded-lg border border-rose-800/50">
                    Layar Proyektor Kelas
                </span>
            </div>

            <!-- Mode 3: Wayground -->
            <div class="bg-[#1c083b]/70 border border-purple-800/40 p-6 rounded-3xl relative overflow-hidden group hover:border-purple-500/60 transition-all hover:-translate-y-1">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-2xl mb-4 shadow-lg">
                    🗺️
                </div>
                <h3 class="text-xl font-black text-white mb-2">Mekanik Wayground</h3>
                <p class="text-sm text-purple-200/70 mb-4">
                    Peta quest visual bercabang! Setiap level membuka rintangan baru berdasarkan skor akurasi dan target bintang (1-3 ⭐).
                </p>
                <span class="inline-block px-3 py-1 bg-emerald-950/60 text-emerald-300 text-xs font-semibold rounded-lg border border-emerald-800/50">
                    Adventure Quest Map
                </span>
            </div>
        </div>

        <!-- Featured Quizzes to Play -->
        <div class="mb-16">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-white">Bank Kuis Unggulan</h2>
                    <p class="text-sm text-purple-300">Pilih kuis di bawah ini dan langsung mulai bermain bersama kelas!</p>
                </div>
                @auth
                    <a href="{{ route('host.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-purple-900/80 hover:bg-purple-800 text-purple-200 border border-purple-700/60 transition-colors">
                        Kelola Semua Kuis →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-purple-900/80 hover:bg-purple-800 text-purple-200 border border-purple-700/60 transition-colors">
                        👨‍🏫 Masuk Guru →
                    </a>
                @endauth
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($featuredQuizzes as $quiz)
                    <div class="bg-[#190633] rounded-3xl border border-purple-800/40 overflow-hidden flex flex-col justify-between hover:border-purple-500/50 transition-all group">
                        @if($quiz->cover_image)
                            <div class="h-44 w-full overflow-hidden relative">
                                <img src="{{ $quiz->cover_image }}" alt="{{ $quiz->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-[#190633] via-transparent to-transparent"></div>
                                <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-extrabold bg-purple-950/80 backdrop-blur-md text-amber-300 border border-purple-700/50">
                                    {{ $quiz->questions->count() }} Soal
                                </span>
                            </div>
                        @endif

                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-xl font-black text-white mb-2 leading-snug">{{ $quiz->title }}</h3>
                                <p class="text-xs text-purple-300/80 line-clamp-2 mb-4 leading-relaxed">{{ $quiz->description }}</p>
                            </div>

                            @auth
                                <form action="{{ route('host.launch') }}" method="POST" class="pt-4 border-t border-purple-900/50 flex flex-col gap-2.5">
                                    @csrf
                                    <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                                    <input type="hidden" name="bgm" value="1">
                                    <input type="hidden" name="sfx" value="1">
                                    <input type="hidden" name="powerups" value="1">
                                    
                                    <!-- Question Count & Theme Selectors -->
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <label class="block text-[10px] uppercase font-bold text-purple-400 mb-1">Banyak Soal:</label>
                                            <select name="question_count" class="w-full py-1.5 px-2 rounded-xl bg-purple-950 border border-purple-800 text-white font-bold text-xs focus:ring-0">
                                                <option value="all">Semua Soal ({{ $quiz->questions->count() }})</option>
                                                <option value="5">5 Soal (Kilat)</option>
                                                <option value="10">10 Soal (Standar)</option>
                                                <option value="20">20 Soal (Tantangan)</option>
                                                <option value="25">25 Soal</option>
                                                <option value="40">40 Soal (Marathon)</option>
                                                <option value="50">50 Soal (Mega)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] uppercase font-bold text-purple-400 mb-1">Suasana & BGM:</label>
                                            <select name="theme" class="w-full py-1.5 px-2 rounded-xl bg-purple-950 border border-purple-800 text-white font-bold text-xs focus:ring-0">
                                                <option value="arcade" {{ ($quiz->theme ?? '') === 'arcade' ? 'selected' : '' }}>🕹️ Arcade Neon</option>
                                                <option value="space" {{ ($quiz->theme ?? '') === 'space' ? 'selected' : '' }}>🚀 Galaksi Space</option>
                                                <option value="fantasy" {{ ($quiz->theme ?? '') === 'fantasy' ? 'selected' : '' }}>🌿 Hutan Kristal</option>
                                                <option value="cyberpunk" {{ ($quiz->theme ?? '') === 'cyberpunk' ? 'selected' : '' }}>⚡ Cyberpunk</option>
                                                <option value="lava" {{ ($quiz->theme ?? '') === 'lava' ? 'selected' : '' }}>🔥 Lava Rock</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 pt-1">
                                        <button 
                                            type="submit" 
                                            name="mode" 
                                            value="live_student"
                                            class="py-2.5 px-3 rounded-xl text-xs font-extrabold btn-3d btn-3d-purple text-center cursor-pointer">
                                            ⚡ Mode Quizizz
                                        </button>
                                        <button 
                                            type="submit" 
                                            name="mode" 
                                            value="live_host"
                                            class="py-2.5 px-3 rounded-xl text-xs font-extrabold btn-3d btn-3d-red text-center cursor-pointer">
                                            🎯 Mode Kahoot
                                        </button>
                                    </div>
                                    <button 
                                        type="submit" 
                                        name="mode" 
                                        value="wayground_map"
                                        class="w-full py-2.5 px-3 rounded-xl text-xs font-extrabold btn-3d btn-3d-green text-center cursor-pointer">
                                        🗺️ Mode Wayground Quest
                                    </button>
                                </form>
                            @else
                                <div class="pt-4 border-t border-purple-900/50">
                                    <a href="{{ route('login') }}" class="block w-full py-3 px-3 rounded-xl text-xs font-black btn-3d btn-3d-purple text-center">
                                        👨‍🏫 Masuk Guru untuk Mulai Sesi
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-purple-400">
                        Belum ada kuis tersedia. Jalankan database seeder atau buat kuis baru di Area Guru.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
