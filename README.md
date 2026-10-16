# 🚀 SmartFun - Platform Kuis Interaktif & Gamifikasi Seru

SmartFun adalah platform web kuis edukasi gamifikasi interaktif modern (terinspirasi dari perpaduan terbaik **Quizizz**, **Kahoot**, dan **Wayground**) yang dibangun dengan **Laravel 13**, **PHP 8.4**, **Tailwind CSS**, **Alpine.js**, dan mesin audio sintetis **Web Audio API**.

---

## 🌟 Fitur Utama

### 1. 🎮 3 Mode Permainan Berbeda
- **⚡ Mode Quizizz (Student-Paced)**: Siswa menjawab mandiri dengan ritme masing-masing, kartu power-up acak (*50-50, 2x Multiplier, Streak Shield, Time Freeze*), avatar dinamis, dan pembaruan klasemen live.
- **🎯 Mode Kahoot (Host-Paced Proyektor)**: Pertanyaan tampil di layar utama proyektor kelas, siswa adu cepat serempak menggunakan tombol bentuk warna (*Merah ▲, Biru ◆, Kuning ●, Hijau ◼*).
- **🗺️ Mode Wayground Quest Map**: Peta petualangan visual bercabang dengan level rintangan dan target 3 bintang (*1–3 Stars*).

### 2. 🧠 Bank Soal Siap Pakai (90 Soal Berkualitas)
- **🧠 Mega Bank 50 Soal Pengetahuan Umum**: Geografi dunia & nusantara, sejarah penemuan, sains alam, biologi, seni, dan budaya dengan penjelasan edukatif lengkap.
- **🚀 Sains Galaksi & Rahasia Alam Semesta**: 20 Soal eksplorasi kosmik & astronomi.
- **🗺️ Wayground: Ekspedisi Hutan Kristal**: 20 Soal petualangan quest bertahap.

### 3. 🎛️ Pilihan Fleksibel Jumlah Soal
Host/Guru dapat memilih berapa banyak soal yang ingin dimainkan per sesi:
- **5 Soal** (Kilat / Ice Breaking)
- **10 Soal** (Standar)
- **20 Soal** (Tantangan)
- **25 Soal** (Setengah Bank)
- **40 Soal** (Marathon)
- **50 Soal** (Mega Bank Penuh)
- **Semua Soal** (Sesuai total koleksi)

### 4. 🎵 5 Suasana Visual & Backsound Musik Seru (Web Audio API)
Mesin audio sintetis procedural multi-track (Kick, Snare, Hi-Hat, Bassline, dan Lead Arpeggio) tanpa beban file audio MP3 berat:
- 🕹️ **Arcade Neon Party** (128 BPM Chiptune Funky EDM)
- 🚀 **Galaksi Antariksa** (118 BPM Synthwave Cosmic Groove)
- 🌿 **Hutan Kristal** (122 BPM Celtic Folk Adventure Beat)
- ⚡ **Cyberpunk 2099** (132 BPM Driving High-Octane Techno)
- 🔥 **Lava Showdown** (138 BPM Fiery Rock-Synth Tensi Tinggi)

### 5. 🏆 Klasemen & Peringkat Lengkap Seluruh Peserta
- Menampilkan ranking seluruh peserta yang bergabung (tidak terpotong hanya 3 atau 5 besar), baik di layar proyektor host maupun layar smartphone siswa.
- Tabel podium 3D + rekapitulasi nilai lengkap seluruh peserta di akhir sesi kuis.
- Fitur ekspor laporan nilai ke file Excel/CSV.

---

## 🛠️ Instalasi & Menjalankan Lokal

### 1. Klon Repositori
```bash
git clone https://github.com/septaryanhidayat/smartfun.git
cd smartfun
```

### 2. Pasang Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan (.env)
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database & Seeding
Untuk SQLite (default lokal):
```bash
touch database/database.sqlite
php artisan migrate --seed
```

Untuk MySQL (Server / Hosting):
```bash
# Pastikan database 'smartfun' sudah dibuat di MySQL
# Atur kredensial DB_CONNECTION=mysql di file .env
php artisan migrate --seed
```
*Atau impor langsung file dump SQL lengkap yang sudah disediakan di:*  
`database/smartfun_mysql.sql`

### 5. Kompilasi Frontend & Jalankan Server
```bash
npm run build
php artisan serve
```
Akses di browser: `http://localhost:8000`

---

## 👨‍🏫 Akun Demo Pendidik / Guru
- **Email**: `guru@smartfun.test`
- **Password**: `password123`

---

## 📄 Lisensi
Open-source di bawah lisensi [MIT](LICENSE).
