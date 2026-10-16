@extends('layouts.app')

@section('content')
<div class="py-8 sm:py-12 px-4 max-w-xl mx-auto" x-data="joinComponent()">
    <!-- Card Container -->
    <div class="bg-[#190633]/90 rounded-3xl border-2 border-purple-600/40 p-6 sm:p-8 shadow-2xl backdrop-blur-xl relative">
        
        <div class="text-center mb-6">
            <span class="inline-block text-4xl mb-2 animate-bounce">👾</span>
            <h1 class="text-2xl sm:text-3xl font-black text-white">Gabung ke Arena Kuis</h1>
            <p class="text-xs sm:text-sm text-purple-300">Pilih karakter avatarmu dan masukkan nama panggilan keren!</p>
        </div>

        @if(isset($rejoinMessage))
            <div class="mb-6 p-4 rounded-2xl bg-amber-500/20 border border-amber-500/40 text-amber-200 text-xs font-semibold text-center">
                {{ $rejoinMessage }}
            </div>
        @endif

        <!-- Error Notification Banner -->
        <div x-show="errorMessage" x-cloak class="mb-6 p-4 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-200 text-xs font-bold text-center animate-pop-in">
            <span x-text="errorMessage"></span>
        </div>

        <form @submit.prevent="submitJoin()" class="space-y-6">
            <!-- Game PIN -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                    Game PIN (6 Angka)
                </label>
                <input 
                    type="text" 
                    x-model="pin" 
                    maxlength="6"
                    required
                    placeholder="123456" 
                    class="w-full text-center text-2xl font-black tracking-widest py-3 px-4 bg-purple-950/80 text-amber-300 rounded-2xl border-2 border-purple-500/40 focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-400/20 transition-all uppercase"
                >
            </div>

            <!-- Nickname -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                    Nama Panggilanmu
                </label>
                <input 
                    type="text" 
                    x-model="nickname" 
                    maxlength="25"
                    required
                    placeholder="Contoh: Sang Juara" 
                    class="w-full text-center text-lg font-bold py-3 px-4 bg-purple-950/80 text-white rounded-2xl border-2 border-purple-500/40 focus:border-amber-400 focus:outline-none focus:ring-4 focus:ring-amber-400/20 transition-all placeholder:text-purple-600"
                >
            </div>

            <!-- Avatar Grid Picker -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-3 text-center">
                    Pilih Avatar Karakter (16 Pilihan)
                </label>
                <div class="grid grid-cols-4 gap-3 max-h-64 overflow-y-auto p-2 bg-purple-950/40 rounded-2xl border border-purple-900/50">
                    <template x-for="item in avatars" :key="item.id">
                        <button 
                            type="button" 
                            @click="selectAvatar(item)"
                            :class="{
                                'ring-4 ring-amber-400 scale-105 border-transparent shadow-lg shadow-amber-400/30': selectedAvatar === item.id,
                                'border-purple-800/60 opacity-80 hover:opacity-100 hover:scale-102': selectedAvatar !== item.id
                            }"
                            class="flex flex-col items-center justify-center p-2 rounded-2xl border bg-gradient-to-b transition-all duration-150 cursor-pointer text-center relative"
                            :class="item.bg">
                            <span class="text-3xl sm:text-4xl filter drop-shadow" x-text="item.emoji"></span>
                            <span class="text-[10px] font-extrabold text-white mt-1 leading-tight line-clamp-1" x-text="item.name"></span>
                            
                            <!-- Active Checkmark Indicator -->
                            <template x-if="selectedAvatar === item.id">
                                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-amber-400 text-slate-950 rounded-full flex items-center justify-center text-[10px] font-black shadow">
                                    ✓
                                </span>
                            </template>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Join Button -->
            <button 
                type="submit" 
                :disabled="isSubmitting"
                class="w-full py-4 rounded-2xl font-black text-lg btn-3d btn-3d-purple flex items-center justify-center gap-2 tracking-wide cursor-pointer disabled:opacity-50">
                <span x-show="!isSubmitting">🎮 MASUK ARENA SEKARANG</span>
                <span x-show="isSubmitting">Menghubungkan...</span>
            </button>
        </form>

    </div>
</div>

<script>
    function joinComponent() {
        return {
            pin: '{{ $pin ?? "" }}',
            nickname: '',
            avatars: @json($avatars),
            selectedAvatar: 'fox_spark',
            isSubmitting: false,
            errorMessage: '',

            selectAvatar(item) {
                this.selectedAvatar = item.id;
                if (window.SmartFunAudio) {
                    window.SmartFunAudio.playPop();
                }
            },

            async submitJoin() {
                this.errorMessage = '';
                this.isSubmitting = true;

                try {
                    const res = await fetch('{{ route("game.join") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            pin: this.pin,
                            nickname: this.nickname,
                            avatar: this.selectedAvatar
                        })
                    });

                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        this.errorMessage = data.message || 'Gagal bergabung ke sesi kuis.';
                        if (window.SmartFunAudio) window.SmartFunAudio.playWrong();
                        return;
                    }

                    if (window.SmartFunAudio) window.SmartFunAudio.playCorrect();
                    window.location.href = data.redirect;
                } catch (err) {
                    this.errorMessage = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                } finally {
                    this.isSubmitting = false;
                }
            }
        };
    }
</script>
@endsection
