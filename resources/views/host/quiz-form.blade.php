@extends('layouts.app')

@section('content')
<div class="py-8 sm:py-12 px-4 max-w-5xl mx-auto" x-data="quizBuilderComponent()">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('host.index') }}" class="text-xs font-bold text-purple-400 hover:text-purple-300 flex items-center gap-1 mb-2">
                ← Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-black text-white">
                {{ $isEdit ? 'Edit Kuis' : 'Buat Kuis Baru' }}
            </h1>
            <p class="text-sm text-purple-300">Rancang pertanyaan menarik, pilihan warna 3D, dan batas waktu untuk siswa.</p>
        </div>
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

    <form action="{{ route('host.quiz.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Quiz Metadata Card -->
        <div class="bg-[#180630]/90 rounded-3xl border border-purple-800/60 p-6 sm:p-8 shadow-xl backdrop-blur-md">
            <h2 class="text-lg font-black text-white mb-4 flex items-center gap-2">
                <span>⚙️</span> Detail Utama Kuis
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                        Judul Kuis *
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        required
                        placeholder="Contoh: Kuis Cerdas Tangkas Antariksa"
                        value="{{ old('title', $quiz->title ?? '') }}"
                        class="w-full py-3 px-4 rounded-2xl bg-purple-950/80 border border-purple-700/60 text-white font-bold focus:border-amber-400 focus:outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                        Deskripsi Singkat
                    </label>
                    <textarea 
                        name="description" 
                        rows="2"
                        placeholder="Berikan gambaran topik kuis ini kepada para pemain..."
                        class="w-full py-3 px-4 rounded-2xl bg-purple-950/80 border border-purple-700/60 text-white text-sm focus:border-amber-400 focus:outline-none">{{ old('description', $quiz->description ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                        URL Cover Image
                    </label>
                    <input 
                        type="url" 
                        name="cover_image" 
                        placeholder="https://images.unsplash.com/..."
                        value="{{ old('cover_image', $quiz->cover_image ?? 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80') }}"
                        class="w-full py-3 px-4 rounded-2xl bg-purple-950/80 border border-purple-700/60 text-white text-sm focus:border-amber-400 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                        Tema Visual
                    </label>
                    <select 
                        name="theme" 
                        class="w-full py-3 px-4 rounded-2xl bg-purple-950/80 border border-purple-700/60 text-white font-bold focus:border-amber-400 focus:outline-none">
                        <option value="space">🚀 Antariksa (Space)</option>
                        <option value="adventure">🗺️ Petualangan (Adventure)</option>
                        <option value="cyberpunk">⚡ Cyberpunk Arcade</option>
                        <option value="nature">🌿 Hutan Rimba (Nature)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Questions List Builder -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black text-white flex items-center gap-2">
                    <span>❓</span> Daftar Pertanyaan (<span x-text="questions.length"></span>)
                </h2>
                <button 
                    type="button" 
                    @click="addQuestion()"
                    class="py-2.5 px-4 rounded-xl font-bold text-xs btn-3d btn-3d-purple flex items-center gap-1.5">
                    <span>➕</span> Tambah Pertanyaan
                </button>
            </div>

            <!-- Loop Over Questions -->
            <template x-for="(q, qIndex) in questions" :key="qIndex">
                <div class="bg-[#180630]/90 rounded-3xl border border-purple-800/60 p-6 shadow-xl relative animate-pop-in">
                    <!-- Question Header Bar -->
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-purple-900/60">
                        <span class="text-sm font-black text-amber-300" x-text="'Soal #' + (qIndex + 1)"></span>
                        <div class="flex items-center gap-2">
                            <button 
                                type="button" 
                                @click="removeQuestion(qIndex)" 
                                x-show="questions.length > 1"
                                class="text-xs text-rose-400 hover:text-rose-300 px-2 py-1 rounded-lg hover:bg-rose-950/40">
                                🗑️ Hapus Soal
                            </button>
                        </div>
                    </div>

                    <!-- Question Input -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1">
                            Teks Pertanyaan *
                        </label>
                        <input 
                            type="text" 
                            :name="'questions[' + qIndex + '][text]'" 
                            x-model="q.text"
                            required
                            placeholder="Contoh: Berapakah jumlah planet dalam tata surya kita?"
                            class="w-full py-3 px-4 rounded-2xl bg-purple-950/80 border border-purple-700/60 text-white font-bold focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Meta Options: Type, Time Limit, Points -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1">
                                Tipe Soal
                            </label>
                            <select 
                                :name="'questions[' + qIndex + '][type]'" 
                                x-model="q.type"
                                class="w-full py-2.5 px-3 rounded-xl bg-purple-950/80 border border-purple-700/60 text-white text-xs font-bold">
                                <option value="multiple_choice">Pilihan Ganda (4 Opsi)</option>
                                <option value="true_false">Benar / Salah (2 Opsi)</option>
                                <option value="poll">Polling / Survey</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1">
                                Batas Waktu
                            </label>
                            <select 
                                :name="'questions[' + qIndex + '][time_limit]'" 
                                x-model="q.time_limit"
                                class="w-full py-2.5 px-3 rounded-xl bg-purple-950/80 border border-purple-700/60 text-white text-xs font-bold">
                                <option value="10">10 Detik (Super Cepat)</option>
                                <option value="20">20 Detik</option>
                                <option value="30">30 Detik (Standar)</option>
                                <option value="60">60 Detik (Hitungan)</option>
                                <option value="90">90 Detik</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1">
                                Poin Dasar
                            </label>
                            <select 
                                :name="'questions[' + qIndex + '][base_points]'" 
                                x-model="q.base_points"
                                class="w-full py-2.5 px-3 rounded-xl bg-purple-950/80 border border-purple-700/60 text-white text-xs font-bold">
                                <option value="500">500 Poin</option>
                                <option value="1000">1.000 Poin (Standar)</option>
                                <option value="1500">1.500 Poin</option>
                                <option value="2000">2.000 Poin (Bonus)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Explanation -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-1">
                            Penjelasan Pembahasan (Tampil Setelah Siswa Menjawab)
                        </label>
                        <input 
                            type="text" 
                            :name="'questions[' + qIndex + '][explanation]'" 
                            x-model="q.explanation"
                            placeholder="Contoh: Tata surya memiliki 8 planet resmi setelah Pluto direklasifikasi pada 2006."
                            class="w-full py-2.5 px-3 rounded-xl bg-purple-950/60 border border-purple-800 text-purple-200 text-xs focus:border-amber-400 focus:outline-none">
                    </div>

                    <!-- Options Grid -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-purple-300 mb-2">
                            Pilihan Jawaban & Kunci Benar (Klik Lingkaran untuk Kunci Jawaban) *
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="(opt, optIndex) in q.options" :key="optIndex">
                                <div class="flex items-center gap-2 p-3 rounded-2xl border" :class="getOptionBadgeBorder(optIndex)">
                                    <!-- Radio Correct Selector -->
                                    <input 
                                        type="radio" 
                                        :name="'questions[' + qIndex + '][correct_option_index]'" 
                                        :value="optIndex" 
                                        x-model="q.correct_option_index"
                                        title="Jadikan Jawaban Benar"
                                        class="w-5 h-5 text-emerald-500 focus:ring-0 cursor-pointer">

                                    <!-- Option Text Input -->
                                    <input 
                                        type="text" 
                                        :name="'questions[' + qIndex + '][options][' + optIndex + '][text]'" 
                                        x-model="opt.text"
                                        required
                                        :placeholder="'Pilihan ' + (optIndex + 1)"
                                        class="flex-1 py-1 px-2 bg-transparent text-white font-bold text-sm focus:outline-none">

                                    <span class="text-xs px-2 py-0.5 rounded-md font-black" :class="getOptionColorBadge(optIndex)" x-text="['▲ Merah', '◆ Biru', '● Kuning', '◼ Hijau'][optIndex]"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Submit Button Bar -->
        <div class="pt-6 border-t border-purple-900/60 flex items-center justify-end gap-4">
            <a href="{{ route('host.index') }}" class="py-3 px-6 rounded-2xl font-bold text-sm bg-purple-950 text-purple-300 border border-purple-800">
                Batal
            </a>
            <button type="submit" class="py-3.5 px-8 rounded-2xl font-black text-base btn-3d btn-3d-purple">
                💾 Simpan & Terbitkan Kuis
            </button>
        </div>
    </form>

</div>

<script>
    function quizBuilderComponent() {
        return {
            questions: [
                {
                    text: 'Planet apakah yang paling dekat dengan Matahari?',
                    type: 'multiple_choice',
                    time_limit: 30,
                    base_points: 1000,
                    explanation: 'Merkurius adalah planet terdekat dengan matahari dengan jarak rata-rata 58 juta km.',
                    correct_option_index: 0,
                    options: [
                        { text: 'Merkurius' },
                        { text: 'Venus' },
                        { text: 'Bumi' },
                        { text: 'Mars' }
                    ]
                },
                {
                    text: 'Apakah air mendidih pada suhu 100 derajat Celsius pada tekanan udara standar 1 atm?',
                    type: 'true_false',
                    time_limit: 20,
                    base_points: 1000,
                    explanation: 'Ya, pada tekanan udara normal (1 atm), titik didih air adalah tepat 100°C.',
                    correct_option_index: 0,
                    options: [
                        { text: 'Benar' },
                        { text: 'Salah' }
                    ]
                }
            ],

            addQuestion() {
                this.questions.push({
                    text: '',
                    type: 'multiple_choice',
                    time_limit: 30,
                    base_points: 1000,
                    explanation: '',
                    correct_option_index: 0,
                    options: [
                        { text: '' },
                        { text: '' },
                        { text: '' },
                        { text: '' }
                    ]
                });
            },

            removeQuestion(index) {
                if (this.questions.length > 1) {
                    this.questions.splice(index, 1);
                }
            },

            getOptionBadgeBorder(idx) {
                const borders = [
                    'bg-[#e21b3c]/20 border-[#e21b3c]/60',
                    'bg-[#1368ce]/20 border-[#1368ce]/60',
                    'bg-[#d89e00]/20 border-[#d89e00]/60',
                    'bg-[#26890c]/20 border-[#26890c]/60'
                ];
                return borders[idx % 4];
            },

            getOptionColorBadge(idx) {
                const badges = [
                    'bg-[#e21b3c] text-white',
                    'bg-[#1368ce] text-white',
                    'bg-[#d89e00] text-slate-950 font-black',
                    'bg-[#26890c] text-white'
                ];
                return badges[idx % 4];
            }
        };
    }
</script>
@endsection
