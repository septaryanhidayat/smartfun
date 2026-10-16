<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SmartFun - Platform Kuis Interaktif & Gamifikasi' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#120426] text-white min-h-screen font-sans selection:bg-purple-500 selection:text-white flex flex-col justify-between overflow-x-hidden">
    <!-- Navbar / Header -->
    <header class="w-full bg-[#1c083b]/80 backdrop-blur-md border-b border-purple-900/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-400 via-rose-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-900/50 group-hover:scale-105 transition-transform">
                    <span class="text-2xl">⚡</span>
                </div>
                <div>
                    <span class="text-2xl font-black tracking-tight bg-gradient-to-r from-amber-300 via-rose-300 to-purple-300 bg-clip-text text-transparent">SmartFun</span>
                    <span class="text-[10px] font-bold text-amber-400 uppercase tracking-widest block -mt-1">Arena Kuis</span>
                </div>
            </a>

            <nav class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('game.join.form') }}" class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-purple-950/80 hover:bg-purple-900 text-purple-200 border border-purple-700/50 transition-colors">
                    🎮 Masuk PIN
                </a>

                @auth
                    <a href="{{ route('host.index') }}" class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white shadow-md shadow-purple-900/40 transition-all flex items-center gap-1.5">
                        <span>👨‍🏫 Dashboard</span>
                        <span class="hidden md:inline text-[11px] bg-purple-900/80 px-2 py-0.5 rounded-full text-amber-300 font-extrabold max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button 
                            type="submit" 
                            title="Keluar dari akun guru"
                            class="px-3 py-2 rounded-xl text-xs sm:text-sm font-bold bg-rose-950/70 hover:bg-rose-900 text-rose-200 border border-rose-800/50 transition-colors cursor-pointer">
                            🚪 Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white shadow-md shadow-purple-900/40 transition-all">
                        👨‍🏫 Masuk Guru
                    </a>
                @endauth
                
                <!-- Sound Toggle -->
                <button 
                    type="button" 
                    id="soundToggleBtn" 
                    onclick="toggleAppSound()"
                    title="Aktifkan/Matikan Suara"
                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-purple-950/80 border border-purple-800 flex items-center justify-center text-base sm:text-lg hover:bg-purple-900 transition-colors cursor-pointer">
                    🔊
                </button>
            </nav>
        </div>
    </header>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500/60 text-emerald-200 text-xs sm:text-sm font-bold flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2">
                    <span class="text-base">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="p-4 rounded-2xl bg-rose-950/80 border border-rose-500/60 text-rose-200 text-xs sm:text-sm font-bold flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2">
                    <span class="text-base">✕</span>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1 w-full">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="w-full bg-[#0d021c] border-t border-purple-950/70 py-6 text-center text-xs text-purple-400">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>© {{ date('Y') }} <strong class="text-purple-300 font-bold">SmartFun</strong> - Gamified Real-Time Quiz Platform (Quizizz, Kahoot & Wayground Style)</p>
            <div class="flex items-center gap-4 text-purple-400">
                <span>Laravel 13 • PHP 8.4 • Tailwind CSS</span>
            </div>
        </div>
    </footer>

    <script>
        function toggleAppSound() {
            if (window.SmartFunAudio) {
                window.SmartFunAudio.enabled = !window.SmartFunAudio.enabled;
                const btn = document.getElementById('soundToggleBtn');
                if (btn) {
                    btn.innerText = window.SmartFunAudio.enabled ? '🔊' : '🔇';
                }
            }
        }
    </script>
</body>
</html>
