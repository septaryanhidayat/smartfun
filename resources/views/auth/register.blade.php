@extends('layouts.app')

@section('content')
<div class="py-10 sm:py-16 px-4 max-w-md mx-auto">
    <div class="bg-[#190633]/90 rounded-3xl border-2 border-purple-600/40 p-6 sm:p-8 shadow-2xl backdrop-blur-xl relative">
        
        <div class="text-center mb-6">
            <span class="inline-block text-4xl mb-2">🎓</span>
            <h1 class="text-2xl sm:text-3xl font-black text-white">Daftar Akun Guru</h1>
            <p class="text-xs sm:text-sm text-purple-300">Buat akun untuk mengelola kuis dan meluncurkan sesi kelas.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-200 text-xs font-bold">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                    Nama Lengkap & Gelar
                </label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}"
                    required
                    placeholder="Contoh: Ibu Rina Kartika, S.Pd" 
                    class="w-full text-base font-bold py-3 px-4 bg-purple-950/80 text-white rounded-2xl border-2 border-purple-500/40 focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-400/20 transition-all placeholder:text-purple-600">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                    Alamat Email
                </label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required
                    placeholder="nama@sekolah.sch.id" 
                    class="w-full text-base font-bold py-3 px-4 bg-purple-950/80 text-white rounded-2xl border-2 border-purple-500/40 focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-400/20 transition-all placeholder:text-purple-600">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                    Kata Sandi (Min. 6 Karakter)
                </label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    placeholder="••••••••" 
                    class="w-full text-base font-bold py-3 px-4 bg-purple-950/80 text-white rounded-2xl border-2 border-purple-500/40 focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-400/20 transition-all placeholder:text-purple-600">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                    Konfirmasi Kata Sandi
                </label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required
                    placeholder="••••••••" 
                    class="w-full text-base font-bold py-3 px-4 bg-purple-950/80 text-white rounded-2xl border-2 border-purple-500/40 focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-400/20 transition-all placeholder:text-purple-600">
            </div>

            <button 
                type="submit" 
                class="w-full py-4 rounded-2xl font-black text-lg btn-3d btn-3d-purple flex items-center justify-center gap-2 tracking-wide cursor-pointer mt-4">
                <span>🚀</span> DAFTAR & BUAT AKUN GURU
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-purple-400">
            Sudah punya akun guru? 
            <a href="{{ route('login') }}" class="font-black text-amber-300 hover:text-amber-200 underline ml-1">
                Masuk di Sini
            </a>
        </div>

    </div>
</div>
@endsection
