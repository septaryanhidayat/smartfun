@extends('layouts.app')

@section('content')
<div class="py-10 sm:py-16 px-4 max-w-md mx-auto" x-data="{ email: '', password: '' }">
    <div class="bg-[#190633]/90 rounded-3xl border-2 border-purple-600/40 p-6 sm:p-8 shadow-2xl backdrop-blur-xl relative">
        
        <div class="text-center mb-6">
            <span class="inline-block text-4xl mb-2">👨‍🏫</span>
            <h1 class="text-2xl sm:text-3xl font-black text-white">Area Masuk Guru</h1>
            <p class="text-xs sm:text-sm text-purple-300">Masuk untuk membuat kuis, mengedit soal, dan memimpin kelas.</p>
        </div>

        <!-- Demo Account Shortcut Box -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-200 text-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="font-extrabold flex items-center gap-1.5">
                    <span>💡</span> Akun Guru Bawaan
                </span>
                <button 
                    type="button" 
                    @click="email = 'guru@smartfun.test'; password = 'password123';"
                    class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg bg-amber-400 text-slate-950 hover:bg-amber-300 transition-colors">
                    Isi Otomatis
                </button>
            </div>
            <p class="text-amber-300/80 font-mono text-[11px]">
                Email: guru@smartfun.test<br>
                Sandi: password123
            </p>
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

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                    Email Guru
                </label>
                <input 
                    type="email" 
                    name="email" 
                    x-model="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="guru@sekolah.sch.id" 
                    class="w-full text-base font-bold py-3 px-4 bg-purple-950/80 text-white rounded-2xl border-2 border-purple-500/40 focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-400/20 transition-all placeholder:text-purple-600">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                    Kata Sandi
                </label>
                <input 
                    type="password" 
                    name="password" 
                    x-model="password"
                    required
                    placeholder="••••••••" 
                    class="w-full text-base font-bold py-3 px-4 bg-purple-950/80 text-white rounded-2xl border-2 border-purple-500/40 focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-400/20 transition-all placeholder:text-purple-600">
            </div>

            <div class="flex items-center justify-between text-xs text-purple-300 pt-1">
                <label class="flex items-center gap-2 cursor-pointer font-bold">
                    <input type="checkbox" name="remember" class="rounded bg-purple-950 border-purple-700 text-purple-600">
                    Ingat Saya
                </label>
            </div>

            <button 
                type="submit" 
                class="w-full py-4 rounded-2xl font-black text-lg btn-3d btn-3d-purple flex items-center justify-center gap-2 tracking-wide cursor-pointer mt-4">
                <span>🔐</span> MASUK SEBAGAI GURU
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-purple-400">
            Belum punya akun guru? 
            <a href="{{ route('register') }}" class="font-black text-amber-300 hover:text-amber-200 underline ml-1">
                Daftar Akun Baru
            </a>
        </div>

    </div>
</div>
@endsection
