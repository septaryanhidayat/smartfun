@extends('layouts.app')

@section('content')
<div class="py-8 sm:py-12 px-4 max-w-7xl mx-auto" x-data="{ launchModal: false, selectedQuiz: null }">

    <!-- Dashboard Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-900/60 border border-purple-700/50 text-purple-300 text-xs font-bold mb-2">
                <span>👨‍🏫</span> Dashboard Pendidik & Host
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-white">Manajemen Kuis SmartFun</h1>
            <p class="text-sm text-purple-300">Pilih kuis untuk diluncurkan ke layar proyektor atau buat bank soal baru.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('host.quiz.create') }}" class="py-3 px-5 rounded-2xl font-extrabold text-sm btn-3d btn-3d-purple flex items-center gap-2 shadow-lg shadow-purple-900/50">
                <span>➕</span> Buat Kuis Baru
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-10">
        <div class="bg-[#1c083b]/80 border border-purple-800/50 p-6 rounded-3xl flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-purple-900/70 border border-purple-700/60 flex items-center justify-center text-3xl">
                📚
            </div>
            <div>
                <span class="text-xs uppercase font-extrabold text-purple-400">Total Kuis Saya</span>
                <span class="text-3xl font-black text-white block">{{ $quizzes->count() }}</span>
            </div>
        </div>

        <div class="bg-[#1c083b]/80 border border-purple-800/50 p-6 rounded-3xl flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-950/70 border border-amber-800/60 flex items-center justify-center text-3xl">
                🎮
            </div>
            <div>
                <span class="text-xs uppercase font-extrabold text-amber-400">Sesi Game Digelar</span>
                <span class="text-3xl font-black text-white block">{{ $totalSessions }}</span>
            </div>
        </div>

        <div class="bg-[#1c083b]/80 border border-purple-800/50 p-6 rounded-3xl flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-950/70 border border-emerald-800/60 flex items-center justify-center text-3xl">
                👥
            </div>
            <div>
                <span class="text-xs uppercase font-extrabold text-emerald-400">Total Peserta Siswa</span>
                <span class="text-3xl font-black text-white block">{{ $totalStudents }}</span>
            </div>
        </div>
    </div>

    <!-- Success Flash Message -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 text-sm font-bold flex items-center gap-3">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    <!-- Quizzes Table / Grid -->
    <div class="bg-[#180630]/90 rounded-3xl border border-purple-800/60 p-6 mb-12 shadow-2xl backdrop-blur-md">
        <h2 class="text-xl font-black text-white mb-4 flex items-center gap-2">
            <span>📋</span> Koleksi Kuis Anda
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-purple-200">
                <thead class="text-xs uppercase bg-purple-950/60 text-purple-400 border-b border-purple-900/60">
                    <tr>
                        <th class="py-3 px-4">Kuis</th>
                        <th class="py-3 px-4 text-center">Jumlah Soal</th>
                        <th class="py-3 px-4 text-center">Tema</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-950/60">
                    @forelse($quizzes as $quiz)
                        <tr class="hover:bg-purple-950/30 transition-colors">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    @if($quiz->cover_image)
                                        <img src="{{ $quiz->cover_image }}" alt="" class="w-12 h-12 rounded-xl object-cover border border-purple-800">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-purple-900 flex items-center justify-center text-xl border border-purple-700">
                                            📝
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-bold text-white block">{{ $quiz->title }}</span>
                                        <span class="text-xs text-purple-400 line-clamp-1">{{ $quiz->description ?? 'Tanpa deskripsi' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center font-extrabold text-amber-300">
                                {{ $quiz->questions->count() }} Soal
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-purple-950 border border-purple-800 uppercase">
                                    {{ $quiz->theme ?? 'default' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-950/80 text-emerald-300 border border-emerald-800">
                                    Publik
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button 
                                        type="button" 
                                        @click="selectedQuiz = {{ json_encode($quiz) }}; launchModal = true;"
                                        class="py-2 px-3.5 rounded-xl font-black text-xs btn-3d btn-3d-purple">
                                        🚀 Luncurkan Game
                                    </button>

                                    <form action="{{ route('host.quiz.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kuis ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl text-purple-400 hover:text-rose-400 hover:bg-rose-950/30 transition-colors" title="Hapus Kuis">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-purple-400">
                                Belum ada kuis. Klik tombol "Buat Kuis Baru" di atas!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Sessions History -->
    @if($recentSessions->count() > 0)
        <div class="bg-[#180630]/90 rounded-3xl border border-purple-800/60 p-6 shadow-2xl backdrop-blur-md">
            <h2 class="text-xl font-black text-white mb-4 flex items-center gap-2">
                <span>🕒</span> Riwayat Sesi Kuis Terbaru
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentSessions as $sess)
                    <div class="p-4 rounded-2xl bg-purple-950/50 border border-purple-800/50 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-black text-amber-400 block tracking-widest">PIN: {{ $sess->pin }}</span>
                            <span class="font-bold text-white text-sm line-clamp-1">{{ $sess->quiz->title }}</span>
                            <span class="text-[11px] text-purple-400 block">{{ $sess->players->count() }} Siswa • Mode: {{ $sess->mode }}</span>
                        </div>
                        <a href="{{ route('host.arena', $sess->pin) }}" class="py-1.5 px-3 rounded-xl text-xs font-bold bg-purple-900 text-purple-200 hover:bg-purple-800 border border-purple-700">
                            Buka Arena →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Game Launcher Modal -->
    <div x-show="launchModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md animate-pop-in">
        <div @click.away="launchModal = false" class="bg-[#1a0636] border-2 border-purple-500/50 rounded-3xl p-6 sm:p-8 max-w-xl w-full max-h-[92vh] overflow-y-auto shadow-2xl relative custom-scrollbar">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-purple-900/80 border border-purple-700/60 flex items-center justify-center text-xl shadow">
                        🚀
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white">Luncurkan Sesi Kuis</h3>
                        <p class="text-xs text-purple-300" x-text="'Kuis: ' + (selectedQuiz ? selectedQuiz.title : '')"></p>
                    </div>
                </div>
                <button type="button" @click="launchModal = false" class="w-8 h-8 rounded-full bg-purple-950 hover:bg-purple-900 border border-purple-800 text-purple-300 hover:text-white flex items-center justify-center text-sm transition-colors">✕</button>
            </div>

            <form action="{{ route('host.launch') }}" method="POST" class="space-y-5" x-data="{ qCount: 'all', currentTheme: 'arcade' }" x-init="$watch('selectedQuiz', q => { if (q && q.theme) currentTheme = q.theme; })">
                @csrf
                <input type="hidden" name="quiz_id" :value="selectedQuiz ? selectedQuiz.id : ''">

                <!-- 1. Mode Permainan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                        1. Pilih Mode Permainan
                    </label>
                    <div class="grid grid-cols-1 gap-2">
                        <label class="flex items-center gap-3 p-3 rounded-2xl border border-purple-700/60 bg-purple-950/60 cursor-pointer hover:border-purple-400 transition-all">
                            <input type="radio" name="mode" value="live_student" checked class="text-purple-600 focus:ring-0">
                            <div>
                                <span class="text-xs sm:text-sm font-black text-white block">⚡ Mode Quizizz (Student-Paced)</span>
                                <span class="text-[11px] text-purple-300">Siswa menjawab mandiri, avatar dinamis, kartu power-up, live leaderboard.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-2xl border border-purple-700/60 bg-purple-950/60 cursor-pointer hover:border-purple-400 transition-all">
                            <input type="radio" name="mode" value="live_host" class="text-purple-600 focus:ring-0">
                            <div>
                                <span class="text-xs sm:text-sm font-black text-white block">🎯 Mode Kahoot (Host-Paced Proyektor)</span>
                                <span class="text-[11px] text-purple-300">Pertanyaan di proyektor, siswa adu cepat serempak tombol bentuk warna.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 rounded-2xl border border-purple-700/60 bg-purple-950/60 cursor-pointer hover:border-purple-400 transition-all">
                            <input type="radio" name="mode" value="wayground_map" class="text-purple-600 focus:ring-0">
                            <div>
                                <span class="text-xs sm:text-sm font-black text-white block">🗺️ Mode Wayground Quest Map</span>
                                <span class="text-[11px] text-purple-300">Peta petualangan visual bercabang, rintangan level & target 3 bintang!</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 2. Pilihan Jumlah Soal (5, 10, 20, 25, 40, 50, Semua) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-purple-300">
                            2. Pilih Banyak Soal Dimainkan
                        </label>
                        <span class="text-[11px] text-amber-300 font-extrabold" x-show="selectedQuiz">
                            Tersedia: <span x-text="selectedQuiz?.questions ? selectedQuiz.questions.length : 0"></span> Soal
                        </span>
                    </div>

                    <div class="grid grid-cols-4 sm:grid-cols-7 gap-1.5">
                        <template x-for="count in ['5', '10', '20', '25', '40', '50', 'all']" :key="count">
                            <label 
                                class="flex flex-col items-center justify-center py-2 px-1 rounded-xl border text-center cursor-pointer transition-all"
                                :class="qCount === count ? 'bg-gradient-to-br from-purple-600 to-indigo-600 border-amber-400 text-white font-black shadow-lg scale-105' : 'bg-purple-950/70 border-purple-800 text-purple-300 hover:border-purple-600 font-bold'">
                                <input type="radio" name="question_count" :value="count" x-model="qCount" class="hidden">
                                <span class="text-sm leading-none" x-text="count === 'all' ? 'Semua' : count"></span>
                                <span class="text-[9px] uppercase tracking-tighter opacity-80 mt-0.5" x-text="count === 'all' ? 'Full' : 'Soal'"></span>
                            </label>
                        </template>
                    </div>
                    <p class="text-[11px] text-purple-400 mt-1.5">
                        💡 Sistem akan memilih soal pertama secara teratur / acak seimbang sesuai jumlah yang dipilih.
                    </p>
                </div>

                <!-- 3. Pilihan Suasana & Backsound Musik (Harmonized Themes) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                        3. Suasana Background & Backsound Musik Seru
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <!-- Arcade Neon Theme -->
                        <label 
                            class="flex items-center gap-3 p-2.5 rounded-2xl border cursor-pointer transition-all"
                            :class="currentTheme === 'arcade' ? 'bg-pink-950/60 border-pink-400 ring-1 ring-pink-400 shadow-md' : 'bg-purple-950/40 border-purple-800 hover:border-purple-600'">
                            <input type="radio" name="theme" value="arcade" x-model="currentTheme" class="hidden">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center text-lg shrink-0">
                                🕹️
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-black text-white block truncate">Arcade Neon Party</span>
                                <span class="text-[10px] text-pink-300 block truncate">🎵 Chiptune Funky EDM Beat</span>
                            </div>
                        </label>

                        <!-- Galaksi Antariksa Theme -->
                        <label 
                            class="flex items-center gap-3 p-2.5 rounded-2xl border cursor-pointer transition-all"
                            :class="currentTheme === 'space' ? 'bg-indigo-950/60 border-indigo-400 ring-1 ring-indigo-400 shadow-md' : 'bg-purple-950/40 border-purple-800 hover:border-purple-600'">
                            <input type="radio" name="theme" value="space" x-model="currentTheme" class="hidden">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-700 flex items-center justify-center text-lg shrink-0">
                                🚀
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-black text-white block truncate">Galaksi Antariksa</span>
                                <span class="text-[10px] text-indigo-300 block truncate">🎵 Synthwave Cosmic Groove</span>
                            </div>
                        </label>

                        <!-- Hutan Kristal Theme -->
                        <label 
                            class="flex items-center gap-3 p-2.5 rounded-2xl border cursor-pointer transition-all"
                            :class="currentTheme === 'fantasy' ? 'bg-emerald-950/60 border-emerald-400 ring-1 ring-emerald-400 shadow-md' : 'bg-purple-950/40 border-purple-800 hover:border-purple-600'">
                            <input type="radio" name="theme" value="fantasy" x-model="currentTheme" class="hidden">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-lg shrink-0">
                                🌿
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-black text-white block truncate">Hutan Kristal Mistis</span>
                                <span class="text-[10px] text-emerald-300 block truncate">🎵 Celtic Adventure Beat</span>
                            </div>
                        </label>

                        <!-- Cyberpunk 2099 Theme -->
                        <label 
                            class="flex items-center gap-3 p-2.5 rounded-2xl border cursor-pointer transition-all"
                            :class="currentTheme === 'cyberpunk' ? 'bg-cyan-950/60 border-cyan-400 ring-1 ring-cyan-400 shadow-md' : 'bg-purple-950/40 border-purple-800 hover:border-purple-600'">
                            <input type="radio" name="theme" value="cyberpunk" x-model="currentTheme" class="hidden">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-lg shrink-0">
                                ⚡
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-black text-white block truncate">Cyberpunk 2099</span>
                                <span class="text-[10px] text-cyan-300 block truncate">🎵 Driving High-Octane Techno</span>
                            </div>
                        </label>

                        <!-- Lava Showdown Theme -->
                        <label 
                            class="flex items-center gap-3 p-2.5 rounded-2xl border cursor-pointer transition-all sm:col-span-2"
                            :class="currentTheme === 'lava' ? 'bg-rose-950/60 border-rose-400 ring-1 ring-rose-400 shadow-md' : 'bg-purple-950/40 border-purple-800 hover:border-purple-600'">
                            <input type="radio" name="theme" value="lava" x-model="currentTheme" class="hidden">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-rose-600 flex items-center justify-center text-lg shrink-0">
                                🔥
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-black text-white block truncate">Lava Showdown (Tensi Tinggi)</span>
                                <span class="text-[10px] text-rose-300 block truncate">🎵 Fiery Rock-Synth Showdown (Adu Cepat & Menegangkan)</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 4. Settings Toggles (BGM, SFX, PowerUps) -->
                <div class="p-3 rounded-2xl bg-purple-950/60 border border-purple-800/80 space-y-2 text-xs font-bold text-purple-200">
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="flex items-center gap-2">
                            <span>🎵</span> Musik Latar Seru (BGM Ramai)
                        </span>
                        <input type="checkbox" name="bgm" value="1" checked class="w-4 h-4 rounded bg-purple-900 border-purple-700 text-purple-500 focus:ring-0">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="flex items-center gap-2">
                            <span>🔊</span> Efek Audio & SFX (Ticking, Benar/Salah)
                        </span>
                        <input type="checkbox" name="sfx" value="1" checked class="w-4 h-4 rounded bg-purple-900 border-purple-700 text-purple-500 focus:ring-0">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="flex items-center gap-2">
                            <span>⚡</span> Kartu Power-Ups Acak Siswa
                        </span>
                        <input type="checkbox" name="powerups" value="1" checked class="w-4 h-4 rounded bg-purple-900 border-purple-700 text-purple-500 focus:ring-0">
                    </label>
                </div>

                <div class="pt-2 flex items-center gap-3">
                    <button type="button" @click="launchModal = false" class="flex-1 py-3 rounded-xl font-bold text-xs bg-purple-950 text-purple-300 border border-purple-800 hover:bg-purple-900 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-3 rounded-xl font-black text-sm btn-3d btn-3d-purple shadow-xl shadow-purple-950">
                        🚀 Mulai Arena Sekarang!
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
