# 🚀 Master Blueprint: Gamified Real-Time Quiz Platform (Quizizz, Kahoot & Wayground Clone)

**Target Environment:** Antigravity IDE  
**Stack Utama:** Laravel 13 (PHP 8.4), MySQL 8.0+, Redis 7.x, Laravel Reverb (WebSocket), Livewire 3 / Alpine.js, Tailwind CSS v3/v4.

---

## 📌 1. Ikhtisar & Visi Proyek

Aplikasi ini adalah platform kuis interaktif edukatif multi-mode yang menggabungkan:
1. **Mekanik Quizizz:** Siswa mengerjakan soal dengan ritme mandiri (*student-paced*), dilengkapi animasi avatar, sistem *power-ups*, dan papan peringkat langsung (*live leaderboard*).
2. **Mekanik Kahoot:** Mode kendali guru (*host-paced*), di mana semua siswa melihat pertanyaan di layar utama dan menjawab serempak melalui perangkat masing-masing dengan batas waktu ketat.
3. **Mekanik Wayground:** Jalur petualangan visual (*quest map*) berbentuk node atau peta jalan, di mana setiap level membuka rintangan baru berdasarkan akurasi jawaban.

### Prinsip Desain UI/UX (Tactile & Gamified)
* **Karakter Visual:** Modern, playful, kontras tinggi, bergaya arcade 3D datar (*flat tactile 3D*).
* **Palet Warna Induk:**
  * Latar Utama (*Arena Background*): Indigo Pekat / Deep Purple (`#2d0f59` hingga `#1a0736`).
  * Opsi Jawaban 1 (Merah/Coral): `#e21b3c` (Border bawah: `#a01229`)
  * Opsi Jawaban 2 (Biru Cerah): `#1368ce` (Border bawah: `#0a4387`)
  * Opsi Jawaban 3 (Kuning Emas): `#d89e00` (Border bawah: `#966e00`)
  * Opsi Jawaban 4 (Hijau Zamrud): `#26890c` (Border bawah: `#175707`)
* **Efek Tombol 3D:** Menggunakan Tailwind classes seperti `border-b-4 active:border-b-0 active:translate-y-1 transition-all`.
* **Audio & Mikro-interaksi:** Terintegrasi dengan Web Audio API / Howler.js untuk efek suara lobi, detak mundur waktu (*ticking clock*), *sound effect* jawaban benar/salah, serta animasi *confetti canvas* di podium penutup.

---

## 🏗️ 2. Arsitektur Sistem

```
[ Siswa (Mobile/Web) ] <----\ 
                             \   WebSocket (Reverb: 8080)
[ Layar Guru (Host)  ] <-----> [ Laravel Reverb Server ]
                             /          ^
[ Siswa (Mobile/Web) ] <----/           | Broadcasting Events
                                        v
                            [ Laravel 13 App Engine ]
                               /                 \
                     Cache / In-Memory      Persistent Storage
                             v                     v
                      [ Redis 7.x ]         [ MySQL 8.0+ ]
```

1. **HTTP/REST Tier:** Menangani registrasi akun, manajemen kuis, upload media, ekspor laporan nilai (Excel/PDF), dan konfigurasi umum.
2. **WebSocket Tier (Laravel Reverb):** Mengalirkan *state machine* permainan secara langsung tanpa *polling*. Siswa terhubung ke *presence channel* `presence-game.{pin}`.
3. **In-Memory Tier (Redis):** Menyimpan status lobi pemain, urutan ranking, dan kunci sementara untuk mencegah *race condition* saat ratusan siswa mengirim jawaban di detik yang sama.
4. **Persistent Tier (MySQL):** Menyimpan data relasional jangka panjang seperti bank soal, riwayat sesi, dan metrik analitik siswa.

---

## 🗄️ 3. Skema Database MySQL (Lengkap & Terindeks)

```sql
-- 1. Tabel Quizzes
CREATE TABLE quizzes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    cover_image VARCHAR(255) NULL,
    theme VARCHAR(50) DEFAULT 'default',
    is_public BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 2. Tabel Questions
CREATE TABLE questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quiz_id BIGINT UNSIGNED NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'true_false', 'open_ended', 'poll') DEFAULT 'multiple_choice',
    media_url VARCHAR(255) NULL,
    media_type ENUM('image', 'audio', 'video') NULL,
    time_limit_seconds INT UNSIGNED DEFAULT 30,
    base_points INT UNSIGNED DEFAULT 1000,
    order_index INT UNSIGNED DEFAULT 1,
    explanation TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- 3. Tabel Options (Pilihan Jawaban)
CREATE TABLE options (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id BIGINT UNSIGNED NOT NULL,
    option_text TEXT NOT NULL,
    media_url VARCHAR(255) NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    order_index INT UNSIGNED DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

-- 4. Tabel Game Sessions (Sesi Kuis Berjalan)
CREATE TABLE game_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quiz_id BIGINT UNSIGNED NOT NULL,
    host_user_id BIGINT UNSIGNED NOT NULL,
    pin VARCHAR(8) UNIQUE NOT NULL,
    mode ENUM('live_host', 'live_student', 'wayground_map', 'homework') DEFAULT 'live_student',
    status ENUM('lobby', 'active', 'paused', 'finished') DEFAULT 'lobby',
    current_question_index INT UNSIGNED DEFAULT 0,
    settings JSON NULL, -- { "show_memes": true, "powerups": true, "sfx": true }
    started_at TIMESTAMP NULL,
    ended_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (host_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_pin_status (pin, status)
);

-- 5. Tabel Game Players (Peserta dalam Sesi)
CREATE TABLE game_players (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    game_session_id BIGINT UNSIGNED NOT NULL,
    nickname VARCHAR(50) NOT NULL,
    avatar_url VARCHAR(255) NULL,
    total_score INT DEFAULT 0,
    current_streak INT DEFAULT 0,
    final_rank INT NULL,
    is_connected BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_session_id) REFERENCES game_sessions(id) ON DELETE CASCADE,
    UNIQUE KEY uq_session_player (game_session_id, nickname)
);

-- 6. Tabel Game Answers (Log Setiap Jawaban)
CREATE TABLE game_answers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    game_session_id BIGINT UNSIGNED NOT NULL,
    game_player_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    option_id BIGINT UNSIGNED NULL,
    text_answer TEXT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    response_time_ms INT UNSIGNED NOT NULL,
    points_awarded INT UNSIGNED DEFAULT 0,
    streak_multiplier DECIMAL(3,2) DEFAULT 1.00,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_session_id) REFERENCES game_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (game_player_id) REFERENCES game_players(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_session_question (game_session_id, question_id)
);
```

---

## ⚡ 4. Arsitektur Redis & Manajemen State Real-Time

Untuk menjaga latensi serendah mungkin saat 100+ pemain berinteraksi sekaligus, data *live* disimpan pada Redis:

### Struktur Kunci Redis:
1. **Lobi Pemain:**  
   `HSET game:{pin}:players {player_id} '{"nickname":"Budi","avatar":"alien_1","streak":0}'`
2. **Papan Peringkat (Live Leaderboard):**  
   Menggunakan Sorted Set (ZSET) untuk pengurutan instan dengan kompleksitas $O(\log N)$:  
   `ZADD game:{pin}:leaderboard {total_score} {player_id}`  
   Mengambil Top 5: `ZREVRANGE game:{pin}:leaderboard 0 4 WITHSCORES`
3. **Status Soal yang Aktif:**  
   `SET game:{pin}:current_question '{"id":14,"started_at":1710000000,"time_limit":30}'`
4. **Kunci Cegah Dobel Submit (Idempotency):**  
   `SET game:{pin}:answered:{player_id}:{question_id} 1 EX 60 NX`

### Rumus Algoritma Penilaian Skor
Skor dihitung secara matematis dengan memperhitungkan akurasi, waktu respons, dan rentetan jawaban benar (*streak*):

$$\text{TimeBonus} = \max\left(0, 1 - \frac{\text{ResponseTimeMs}}{\text{TimeLimitMs} \times 2}\right)$$

$$\text{FinalPoints} = \text{BasePoints} \times \text{TimeBonus} \times \min(2.0, 1 + (\text{Streak} \times 0.1))$$

*Jika jawaban salah, $\text{FinalPoints} = 0$, dan nilai $\text{Streak}$ kembali ke $0$.*

---

## 🎮 5. Mekanik Khusus Wayground & Quizizz

### A. Power-Up System (Quizizz Style)
Siswa mendapatkan kartu kekuatan acak setiap mencapai rentetan 3 jawaban benar:
* **2x Multiplier:** Menggandakan poin pada pertanyaan berikutnya.
* **50-50 Eliminator:** Menghilangkan 2 opsi jawaban yang salah secara visual di layar siswa.
* **Streak Shield:** Menjaga agar rentetan *streak* tidak reset jika menjawab salah satu kali.
* **Time Freeze:** Menghentikan pengurangan bonus waktu selama 10 detik pertama.

### B. Mode Peta Petualangan (Wayground Adventure Map)
* Peta kuis ditampilkan dalam bentuk jalur linear berkelok dengan node milestone (Level 1 sampai Level N).
* Setiap node merepresentasikan klaster soal tertentu dengan target bintang (1-3 Bintang berdasarkan akurasi).
* Komponen kanvas SVG menampilkan avatar siswa yang bergerak maju melintasi jalur ketika sebuah level terselesaikan.

---

## 📡 6. Skema Kanal & Event WebSocket (Laravel Reverb)

### Channel Name: `presence-game.{pin}`
Setiap mutasi permainan dikirimkan melalui event Laravel Broadcasting:

| Nama Event | Pengirim | Target | Payload |
|---|---|---|---|
| `PlayerJoined` | Siswa | Host & Pemain Lain | `{ playerId, nickname, avatar, totalPlayers }` |
| `GameStarted` | Host | Semua Pemain | `{ totalQuestions, mode, initialTheme }` |
| `QuestionStarted` | Host/Server | Siswa | `{ questionId, text, media, options, timeLimit }` *(Catatan: `is_correct` TIDAK dikirim)* |
| `AnswerSubmitted` | Siswa | Server | `{ playerId, questionId, optionId, responseTimeMs }` |
| `QuestionEnded` | Server | Semua Pemain | `{ correctOptionId, explanation, topFive }` |
| `LeaderboardUpdated` | Server | Host & Pemain | `[ { rank: 1, nickname: "Ani", score: 4200 }, ... ]` |
| `GameFinished` | Host/Server | Semua Pemain | `{ podium: [Juara 1, 2, 3], summaryAnalytics }` |

---

## 📋 7. Panduan Master Prompt untuk Antigravity IDE

Gunakan panduan berikut per tahapan (*Prompt Slices*) saat memberi perintah eksekusi ke Antigravity IDE:

### 🔹 Prompt 1: Inisialisasi Dasar & Database Schema
```text
Buatkan migrasi Laravel 13, model Eloquent lengkap dengan relasinya, serta factory dan seeder untuk aplikasi kuis interaktif sesuai dengan spesifikasi berikut:
- Model: Quiz, Question, Option, GameSession, GamePlayer, GameAnswer.
- Buatkan relasi lengkap (hasMany, belongsTo, dsb.) dengan cascade delete yang benar.
- Pastikan indeks komposit pada pin game_sessions dan foreign keys terpasang sempurna.
- Gunakan tipe data ENUM dan JSON sesuai dengan spesifikasi di skema database.
```

### 🔹 Prompt 2: Konfigurasi Laravel Reverb & Redis State Service
```text
Buatkan service class di Laravel bernama GameEngineService yang menangani state kuis di Redis:
1. joinGame(string $pin, string $nickname, string $avatar): GamePlayer
2. submitAnswer(int $playerId, int $questionId, ?int $optionId, int $timeMs): array
3. calculateScore(int $basePoints, int $timeLimit, int $timeMs, int $streak): int
4. getLiveLeaderboard(string $pin, int $limit = 10): array
Konfigurasikan event broadcasting Reverb (PlayerJoined, QuestionStarted, LeaderboardUpdated, GameFinished) yang mengimplementasikan ShouldBroadcastNow.
```

### 🔹 Prompt 3: UI Lobi & Game Engine untuk Siswa (Livewire 3 + Alpine.js + Tailwind)
```text
Buatkan halaman Arena Pemain menggunakan Livewire 3 dan Tailwind CSS:
1. Desain tactile 3D bergaya Quizizz/Kahoot dengan 4 kotak jawaban warna-warni kontras tinggi yang responsif di ponsel.
2. Integrasikan Alpine.js untuk menghitung mundur bar waktu (smooth countdown bar) dan audio effects sederhana (Web Audio oscillator / Howler.js).
3. Sambungkan ke WebSocket Laravel Echo via Reverb untuk mendengarkan event perubahan status kuis secara instan tanpa reload halaman.
```

### 🔹 Prompt 4: Host Control Screen & Dashboard Analitik Guru
```text
Buatkan tampilan Host Screen untuk layar proyektor guru:
1. Tampilan lobi tunggu dengan PIN besar, QR code bergabung, dan daftar nama siswa yang melayang masuk dengan animasi halus.
2. Tampilan pertanyaan aktif dengan visual bar jumlah jawaban yang sudah masuk secara real-time.
3. Podium pemenang 3D (Juara 1, 2, dan 3) dengan efek animasi confetti di akhir permainan.
4. Tabel rekapitulasi analitik pasca-kuis untuk mengekspor skor ke format CSV/Excel.
```

---

## 🛡️ 8. Aspek Keamanan & Optimasi Produksi

1. **Anti-Cheat:** Kunci jawaban (`is_correct`) tidak boleh dikirimkan ke peramban siswa melalui WebSocket sebelum waktu pertanyaan habis. Validasi kebenaran jawaban wajib dilakukan di sisi server (*server-side verification*).
2. **Rate Limiting:** Pasang *throttle middleware* pada rute `submit-answer` maksimal 1 request per soal per pemain untuk menghindari eksploitasi *bot clicker*.
3. **Auto-Cleanup Redis:** Seluruh entri Redis untuk sesi permainan yang selesai wajib diberikan batas waktu kedaluwarsa (*TTL / expiration*) selama 24 jam (`EXPIRE game:{pin} 86400`).