-- ========================================================
-- SmartFun Complete MySQL Database Dump (Schema + Data)
-- Siap diimpor ke database cPanel (misal: pesonaas_smartfun)
-- Generated: 2026-10-16 16:31:57
-- ========================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `game_answers`;
DROP TABLE IF EXISTS `game_players`;
DROP TABLE IF EXISTS `game_sessions`;
DROP TABLE IF EXISTS `options`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `quizzes`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------
-- Struktur tabel `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `quizzes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `theme` varchar(50) NOT NULL DEFAULT 'default',
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quizzes_slug_unique` (`slug`),
  KEY `quizzes_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `question_type` varchar(30) NOT NULL DEFAULT 'multiple_choice',
  `media_url` varchar(255) DEFAULT NULL,
  `media_type` varchar(20) DEFAULT NULL,
  `time_limit_seconds` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `base_points` int(10) UNSIGNED NOT NULL DEFAULT 1000,
  `order_index` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `explanation` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_quiz_id_foreign` (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `options` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option_text` text NOT NULL,
  `media_url` varchar(255) DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `order_index` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `options_question_id_foreign` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `game_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) UNSIGNED NOT NULL,
  `host_user_id` bigint(20) UNSIGNED NOT NULL,
  `pin` varchar(8) NOT NULL,
  `mode` varchar(30) NOT NULL DEFAULT 'live_student',
  `status` varchar(30) NOT NULL DEFAULT 'lobby',
  `current_question_index` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `game_sessions_pin_unique` (`pin`),
  KEY `game_sessions_quiz_id_foreign` (`quiz_id`),
  KEY `game_sessions_host_user_id_foreign` (`host_user_id`),
  KEY `idx_pin_status` (`pin`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `game_players` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_session_id` bigint(20) UNSIGNED NOT NULL,
  `nickname` varchar(50) NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `total_score` int(11) NOT NULL DEFAULT 0,
  `current_streak` int(11) NOT NULL DEFAULT 0,
  `final_rank` int(11) DEFAULT NULL,
  `is_connected` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_session_player` (`game_session_id`, `nickname`),
  KEY `game_players_game_session_id_foreign` (`game_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `game_answers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_session_id` bigint(20) UNSIGNED NOT NULL,
  `game_player_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option_id` bigint(20) UNSIGNED DEFAULT NULL,
  `text_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `response_time_ms` int(10) UNSIGNED NOT NULL,
  `points_awarded` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `streak_multiplier` decimal(3,2) NOT NULL DEFAULT 1.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game_answers_game_session_id_foreign` (`game_session_id`),
  KEY `game_answers_game_player_id_foreign` (`game_player_id`),
  KEY `game_answers_question_id_foreign` (`question_id`),
  KEY `game_answers_option_id_foreign` (`option_id`),
  KEY `idx_session_question` (`game_session_id`, `question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `users`
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES (1, 'Guru SmartFun', 'guru@smartfun.test', NULL, '$2y$12$Cbg74VBTDHXPSooHLtdQeu7t8BF5unh6Q1sHqKULIzpUZ2/LPNKne', '6YOfuhOXDZ8ZHqPiDmT6YGSv6MHOzkPq4dplK2If2BtLFqGnPdK51wObSK1i', '2026-10-16 14:16:44', '2026-10-16 14:16:44');

-- Dumping data for `quizzes`
INSERT INTO `quizzes` (`id`, `user_id`, `title`, `slug`, `description`, `cover_image`, `theme`, `is_public`, `created_at`, `updated_at`) VALUES (1, 1, '🚀 Sains Galaksi & Rahasia Alam Semesta', 'sains-galaksi-pengetahuan-seru', 'Eksplorasi 20 rahasia luar angkasa, tata surya, lubang hitam, dan fisika modern!', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&auto=format&fit=crop&q=80', 'space', 1, '2026-10-16 14:16:44', '2026-10-16 15:20:02');
INSERT INTO `quizzes` (`id`, `user_id`, `title`, `slug`, `description`, `cover_image`, `theme`, `is_public`, `created_at`, `updated_at`) VALUES (2, 1, '🗺️ Wayground: Ekspedisi Hutan Kristal (20 Quest)', 'wayground-ekspedisi-hutan-kristal', 'Taklukkan tiap pos tantangan petualangan ajaib dari gerbang rimba hingga puncak kuil kristal!', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=800&auto=format&fit=crop&q=80', 'fantasy', 1, '2026-10-16 14:16:45', '2026-10-16 15:20:03');
INSERT INTO `quizzes` (`id`, `user_id`, `title`, `slug`, `description`, `cover_image`, `theme`, `is_public`, `created_at`, `updated_at`) VALUES (3, 1, '🧠 Mega Bank: 50 Soal Pengetahuan Umum Cerdas & Seru', '50-soal-pengetahuan-umum', 'Bank kuis serbaguna berisi 50 pertanyaan umum seputar sains, sejarah, geografi, budaya, dan teknologi. Dapat dimainkan 5, 10, 20, 25, 40, hingga 50 soal!', 'https://images.unsplash.com/photo-1606326608606-aa0b62935f2b?w=800&auto=format&fit=crop&q=80', 'arcade', 1, '2026-10-16 15:20:01', '2026-10-16 15:20:01');

-- Dumping data for `questions`
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (1, 1, 'Planet apakah yang memiliki julukan \"Planet Merah\" di tata surya kita?', 'multiple_choice', NULL, NULL, 20, 1000, 1, 'Mars tampak kemerahan karena permukaannya kaya akan besi(III) oksida atau karat besi.', '2026-10-16 14:16:44', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (2, 1, 'Berapa perkiraan kecepatan cahaya di ruang hampa udara?', 'multiple_choice', NULL, NULL, 20, 1000, 2, 'Kecepatan cahaya di ruang hampa adalah tepat 299.792.458 meter per detik (~300.000 km/detik).', '2026-10-16 14:16:45', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (3, 1, 'Matahari adalah sebuah planet raksasa yang berada di pusat tata surya.', 'true_false', NULL, NULL, 15, 800, 3, 'Salah! Matahari adalah Bintang tipe deret utama (G-type) yang melakukan fusi nuklir hidrogen.', '2026-10-16 14:16:45', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (4, 1, 'Apakah nama gunung berapi tertinggi di tata surya yang berada di planet Mars?', 'multiple_choice', NULL, NULL, 20, 1200, 4, 'Olympus Mons di Mars memiliki ketinggian hampir 22 kilometer!', '2026-10-16 14:16:45', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (5, 1, 'Gas apakah yang paling melimpah di atmosfer bumi yang kita hirup sehari-hari?', 'multiple_choice', NULL, NULL, 15, 800, 5, 'Atmosfer bumi terdiri dari 78% Nitrogen (N2) dan 21% Oksigen.', '2026-10-16 14:16:45', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (6, 1, 'Berapa lama waktu yang dibutuhkan cahaya matahari untuk sampai ke permukaan Bumi?', 'multiple_choice', NULL, NULL, 20, 1000, 6, 'Jarak 150 juta km ditempuh cahaya matahari dalam waktu sekitar 8 menit 20 detik.', '2026-10-16 14:16:45', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (7, 2, 'Pos 1 [Pintu Rimba]: Mamalia manakah yang memiliki kemampuan terbang sejati dengan mengepakkan sayap?', 'multiple_choice', NULL, NULL, 20, 1000, 1, 'Kelelawar adalah satu-satunya mamalia sejati yang mampu terbang.', '2026-10-16 14:16:45', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (8, 2, 'Pos 2 [Jembatan Nil]: Sungai terpanjang di benua Afrika yang melintasi 11 negara adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 2, 'Sungai Nil membentang sejauh 6.650 km menuju Laut Tengah.', '2026-10-16 14:16:45', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (9, 2, 'Pos 3 [Gua Kristal]: Lambang unsur kimia Emas murni pada tabel periodik adalah Au (Aurum).', 'true_false', NULL, NULL, 15, 800, 3, 'Benar, Au berasal dari bahasa Latin \"Aurum\" yang berarti kilau fajar.', '2026-10-16 14:16:45', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (10, 2, 'Pos 4 [Lembah Benua]: Berapakah jumlah total benua utama yang diakui di peta dunia?', 'multiple_choice', NULL, NULL, 15, 1000, 4, '7 Benua: Asia, Afrika, Amerika Utara, Amerika Selatan, Antartika, Eropa, dan Australia.', '2026-10-16 14:16:45', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (11, 2, 'Pos 5 [Mata Air Murni]: Proses penguapan air laut ke udara membentuk awan disebut...?', 'multiple_choice', NULL, NULL, 20, 1000, 5, 'Evaporasi adalah perubahan wujud air cair menjadi uap gas yang naik ke atmosfer.', '2026-10-16 14:16:45', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (12, 3, 'Ibukota resmi dari negara Australia adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 1, 'Ibukota Australia adalah Canberra, bukan Sydney ataupun Melbourne.', '2026-10-16 15:20:01', '2026-10-16 15:20:01');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (13, 3, 'Hewan mamalia darat tercepat di dunia yang mampu berlari hingga 120 km/jam adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 2, 'Cheetah dapat berakselerasi dari 0 hingga 100 km/jam hanya dalam waktu sekitar 3 detik.', '2026-10-16 15:20:01', '2026-10-16 15:20:01');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (14, 3, 'Organ tubuh manusia terbesar berdasarkan luas permukaan dan beratnya adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 3, 'Kulit manusia memiliki luas sekitar 2 meter persegi dan melindungi seluruh organ dalam.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (15, 3, 'Menara Eiffel yang terkenal di dunia terletak di kota mana?', 'multiple_choice', NULL, NULL, 15, 1000, 4, 'Menara Eiffel dibangun di Paris, Prancis oleh Gustave Eiffel pada tahun 1889.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (16, 3, 'Apakah samudra paling luas dan terdalam di planet Bumi?', 'multiple_choice', NULL, NULL, 20, 1000, 5, 'Samudra Pasifik mencakup lebih dari 30% permukaan Bumi dan memuat Palung Mariana.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (17, 3, 'Candi Borobudur yang megah di Magelang dibangun oleh dinasti apa?', 'multiple_choice', NULL, NULL, 25, 1200, 6, 'Candi Borobudur dibangun sekitar abad ke-8 dan ke-9 Masehi oleh Dinasti Syailendra penganut Buddha Mahayana.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (18, 3, 'Siapakah penemu bola lampu pijar praktis yang terkenal dengan ribuan eksperimennya?', 'multiple_choice', NULL, NULL, 20, 1000, 7, 'Thomas Alva Edison mempatenkan desain lampu pijar komersial pertama yang tahan lama pada 1879.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (19, 3, 'Bahasa dengan jumlah penutur asli (native speaker) terbanyak di dunia adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 8, 'Bahasa Mandarin memiliki lebih dari 900 juta penutur asli di seluruh dunia.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (20, 3, 'Berapakah jumlah nada pokok dalam tangga nada diatonis standar (Do sampai Si)?', 'multiple_choice', NULL, NULL, 15, 800, 9, 'Tangga nada diatonis terdiri dari 7 nada pokok: Do, Re, Mi, Fa, Sol, La, Si.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (21, 3, 'Gas apakah yang dihirup manusia saat bernapas untuk membakar energi dalam sel tubuh?', 'multiple_choice', NULL, NULL, 15, 800, 10, 'Oksigen (O2) digunakan dalam respirasi sel untuk memproduksi adenosin trifosfat (ATP).', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (22, 3, 'Negara manakah yang dijuluki sebagai \"Negeri Tirai Bambu\"?', 'multiple_choice', NULL, NULL, 15, 1000, 11, 'Tiongkok (China) dikenal dengan julukan Negeri Tirai Bambu, sedangkan Uni Soviet dahulu dijuluki Tirai Besi.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (23, 3, 'Hewan mamalia laut terbesar di dunia yang beratnya bisa mencapai 150 ton adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 12, 'Paus Biru (Blue Whale) adalah hewan terbesar yang pernah ada di muka bumi, bahkan melebihi dinosaurus.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (24, 3, 'Planet apakah yang memiliki orbit paling dekat dengan Matahari?', 'multiple_choice', NULL, NULL, 15, 1000, 13, 'Merkurius adalah planet terkecil dan terdekat dengan Matahari di tata surya kita.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (25, 3, 'Apakah nama mata uang resmi dari negara Jepang?', 'multiple_choice', NULL, NULL, 15, 800, 14, 'Mata uang resmi Jepang adalah Yen (¥), sedangkan Won adalah mata uang Korea dan Yuan adalah Tiongkok.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (26, 3, 'Berapakah hasil dari perhitungan matematika: (15 x 6) + 10 ?', 'multiple_choice', NULL, NULL, 20, 1000, 15, '15 x 6 = 90, kemudian 90 + 10 = 100.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (27, 3, 'Siapakah maestro legendaris yang melukis mahakarya \"Mona Lisa\"?', 'multiple_choice', NULL, NULL, 20, 1000, 16, 'Leonardo da Vinci melukis Mona Lisa pada masa Renaisans Italia sekitar awal abad ke-16.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (28, 3, 'Danau air tawar terluas di dunia berdasarkan luas permukaan adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 17, 'Danau Superior di perbatasan AS dan Kanada adalah danau air tawar terluas di dunia.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (29, 3, 'Gunung tertinggi di dunia yang puncaknya mencapai 8.848 meter di atas permukaan laut adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 18, 'Gunung Everest terletak di pegunungan Himalaya di perbatasan Nepal dan Tibet.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (30, 3, 'Alat musik tradisional Indonesia berbahan bambu yang dimainkan dengan cara digoyangkan adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 19, 'Angklung adalah alat musik tradisional Sunda (Jawa Barat) yang telah diakui UNESCO sebagai warisan dunia.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (31, 3, 'Vitamin apakah yang diproduksi secara alami oleh kulit dengan bantuan paparan sinar matahari pagi?', 'multiple_choice', NULL, NULL, 15, 1000, 20, 'Sinar UVB dari matahari mengubah senyawa 7-dehidrokolesterol di kulit menjadi Vitamin D3.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (32, 3, 'Berapakah jumlah pemain inti sepak bola dari satu tim di lapangan saat pertandingan resmi?', 'multiple_choice', NULL, NULL, 15, 800, 21, 'Satu tim sepak bola terdiri dari 11 pemain di lapangan, termasuk 1 penjaga gawang.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (33, 3, 'Benua terkecil di dunia berdasarkan luas daratannya adalah...?', 'multiple_choice', NULL, NULL, 15, 1000, 22, 'Benua Australia adalah benua terkecil di dunia dengan luas sekitar 7,7 juta km persegi.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (34, 3, 'Hewan marsupial berkantung khas Australia yang melompat menggunakan kaki belakangnya adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 23, 'Kanguru membawa anaknya di dalam kantung perut (marsupium) hingga cukup dewasa.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (35, 3, 'Rumus senyawa kimia untuk air murni yang esensial bagi kehidupan adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 24, 'Air terdiri dari dua atom Hidrogen dan satu atom Oksigen (H2O).', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (36, 3, 'Teks Proklamasi Kemerdekaan Indonesia dibacakan oleh Ir. Soekarno pada tahun...?', 'multiple_choice', NULL, NULL, 15, 800, 25, 'Indonesia memproklamasikan kemerdekaannya pada hari Jumat, 17 Agustus 1945.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (37, 3, 'Burung apakah yang dijadikan sebagai lambang negara kesatuan Republik Indonesia?', 'multiple_choice', NULL, NULL, 15, 800, 26, 'Garuda Pancasila adalah lambang negara Indonesia dengan semboyan Bhinneka Tunggal Ika.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (38, 3, 'Gas apakah yang diserap oleh tumbuhan hijau dari udara saat melakukan fotosintesis?', 'multiple_choice', NULL, NULL, 20, 1000, 27, 'Tumbuhan menyerap Karbon Dioksida (CO2) dan melepaskan Oksigen (O2).', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (39, 3, 'Piramida Agung Giza dan patung Sphinx yang berusia ribuan tahun terletak di negara...?', 'multiple_choice', NULL, NULL, 15, 800, 28, 'Piramida Giza berada di pinggiran kota Kairo, Mesir.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (40, 3, 'Zat pigmen hijau pada sel daun yang berfungsi menyerap energi sinar matahari disebut...?', 'multiple_choice', NULL, NULL, 20, 1000, 29, 'Klorofil berada di dalam kloroplas dan memberikan warna hijau khas pada tumbuhan.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (41, 3, 'Siapakah ilmuwan fisika genius yang merumuskan persamaan kesetaraan massa dan energi E=mc²?', 'multiple_choice', NULL, NULL, 20, 1000, 30, 'Albert Einstein mempublikasikan teori relativitas khusus ini pada tahun 1905.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (42, 3, 'Kereta peluru super cepat berkecepatan lebih dari 300 km/jam di Jepang dikenal dengan nama...?', 'multiple_choice', NULL, NULL, 15, 800, 31, 'Shinkansen (新幹線) pertama kali beroperasi pada tahun 1964 menyambut Olimpiade Tokyo.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (43, 3, 'Bagian otak manusia yang bertanggung jawab mengatur koordinasi motorik dan keseimbangan tubuh adalah...?', 'multiple_choice', NULL, NULL, 20, 1200, 32, 'Otak kecil (Cerebellum) terletak di bagian belakang kepala di bawah otak besar.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (44, 3, 'Selat yang menghubungkan Laut Jawa dengan Samudra Hindia di antara pulau Jawa dan Sumatra adalah...?', 'multiple_choice', NULL, NULL, 15, 1000, 33, 'Selat Sunda memisahkan Anyer (Banten, Jawa) dan Bakauheni (Lampung, Sumatra).', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (45, 3, 'Berapakah total jumlah tulang rusuk pada kerangka dada manusia dewasa normal?', 'multiple_choice', NULL, NULL, 20, 1200, 34, 'Manusia memiliki 24 tulang rusuk (12 pasang di sisi kanan dan kiri).', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (46, 3, 'Cabang olahraga yang mempertandingkan lari, lompat, dan lempar dikenal sebagai \"Induk dari Segala Olahraga\", yaitu...?', 'multiple_choice', NULL, NULL, 15, 800, 35, 'Atletik dijuluki \"Mother of Sports\" karena gerakannya merupakan dasar gerak fisik manusia.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (47, 3, 'Logam berat berwujud cair pada suhu ruangan yang biasa digunakan pada termometer adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 36, 'Air raksa atau merkuri (Hg) memiliki titik lebur sangat rendah (-38,8°C).', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (48, 3, 'Tokoh yang mempatenkan pesawat telepon pertama di dunia pada tahun 1876 adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 37, 'Alexander Graham Bell mengucapkan kata legendaris pertama: \"Mr. Watson, come here, I want to see you.\"', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (49, 3, 'Kota di Indonesia yang terkenal memiliki julukan \"Kota Pelajar\" dan \"Kota Gudeg\" adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 38, 'D.I. Yogyakarta memiliki ratusan perguruan tinggi dan pusat kebudayaan Jawa yang kental.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (50, 3, 'Satu-satunya satelit alami yang mengitari planet Bumi kita adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 39, 'Bulan (Moon) mengitari Bumi dalam waktu sekitar 27,3 hari dan mempengaruhi pasang surut air laut.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (51, 3, 'Lapisan gas di atmosfer stratosfer yang melindungi bumi dari radiasi sinar ultraviolet berbahaya adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 40, 'Lapisan Ozon (O3) menyerap sebagian besar radiasi UVB dari matahari.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (52, 3, 'Negara dengan populasi penduduk terbanyak di kawasan Asia Tenggara adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 41, 'Indonesia memiliki lebih dari 280 juta jiwa, menjadikannya negara terpadat ke-4 di dunia.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (53, 3, 'Bunga raksasa tanpa daun dan batang \"Rafflesia Arnoldii\" pertama kali ditemukan di hutan pulau...?', 'multiple_choice', NULL, NULL, 20, 1000, 42, 'Rafflesia Arnoldii ditemukan di Bengkulu, Sumatra pada tahun 1818 oleh Dr. Joseph Arnold dan Thomas Stamford Raffles.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (54, 3, 'Satuan unit terkecil dalam penyimpanan data digital komputer yang bernilai 0 atau 1 adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 43, 'Bit (Binary Digit) adalah unit dasar, di mana 8 bit membentuk 1 Byte.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (55, 3, 'Hewan golongan amfibi mampu beradaptasi dan hidup di dua habitat, yaitu perairan dan daratan.', 'true_false', NULL, NULL, 15, 800, 44, 'Benar, contoh amfibi adalah katak dan salamander yang bernapas dengan insang saat berudu dan paru-paru/kulit saat dewasa.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (56, 3, 'Rumus kimia dari Garam Dapur yang lazim digunakan sebagai bumbu masakan sehari-hari adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 45, 'Natrium Klorida (NaCl) adalah senyawa garam mineral yang memberi rasa asin.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (57, 3, 'Gurun pasir terluas dan terpanas di dunia yang membentang melintasi benua Afrika bagian utara adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 46, 'Gurun Sahara memiliki luas sekitar 9,2 juta kilometer persegi, hampir seukuran benua Amerika Serikat!', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (58, 3, 'Siapakah presiden pertama Amerika Serikat yang wajahnya diabadikan di Gunung Rushmore dan uang kertas 1 Dolar?', 'multiple_choice', NULL, NULL, 20, 1000, 47, 'George Washington menjabat sebagai presiden pertama AS dari tahun 1789 hingga 1797.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (59, 3, 'Tiga warna primer dasar dalam teori seni rupa pigmen tradisional yang tidak dapat dibuat dari campuran warna lain adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 48, 'Merah, Kuning, dan Biru adalah warna primer pigmen RYB klasik.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (60, 3, 'Hewan yang mengonsumsi daging sebagai sumber nutrisi utamanya digolongkan sebagai...?', 'multiple_choice', NULL, NULL, 15, 800, 49, 'Karnivora adalah pemakan daging, herbivora pemakan tumbuhan, dan omnivora pemakan segala.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (61, 3, 'Menara Lonceng miring yang sangat tersohor di dunia terletak di kota Pisa, negara...?', 'multiple_choice', NULL, NULL, 15, 800, 50, 'Menara Miring Pisa dibangun di kota Pisa, wilayah Toskana, Italia.', '2026-10-16 15:20:02', '2026-10-16 15:20:02');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (62, 1, 'Gaya tarik gravitasi apakah yang menyebabkan peristiwa pasang dan surut air laut di Bumi?', 'multiple_choice', NULL, NULL, 20, 1000, 7, 'Gravitasi Bulan memiliki pengaruh paling kuat terhadap pasang surut laut di Bumi.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (63, 1, 'Planet manakah yang terkenal dengan sistem cincin es dan batu paling spektakuler?', 'multiple_choice', NULL, NULL, 15, 800, 8, 'Saturnus memiliki cincin es paling megah yang membentang hingga ratusan ribu kilometer.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (64, 1, 'Apakah nama galaksi tempat tata surya kita berada?', 'multiple_choice', NULL, NULL, 15, 800, 9, 'Tata surya kita berada di dalam Galaksi Bima Sakti (Milky Way).', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (65, 1, 'Objek antariksa dengan gravitasi sangat kuat sehingga cahaya pun tidak dapat lolos disebut...?', 'multiple_choice', NULL, NULL, 20, 1000, 10, 'Lubang Hitam (Black Hole) memiliki batas cakrawala peristiwa (event horizon).', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (66, 1, 'Siapakah manusia pertama yang berhasil mendarat dan berjalan di permukaan Bulan pada 1969?', 'multiple_choice', NULL, NULL, 20, 1000, 11, 'Neil Armstrong mengucapkan kalimat: \"That\'s one small step for man, one giant leap for mankind.\"', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (67, 1, 'Planet terpanas di tata surya kita karena efek rumah kaca ekstrem atmosfernya adalah...?', 'multiple_choice', NULL, NULL, 20, 1200, 12, 'Venus memiliki suhu permukaan mencapai 465°C karena atmosfer tebal CO2.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (68, 1, 'Benda langit berbatu yang mengorbit matahari dan sebagian besar berada di antara Mars dan Jupiter disebut...?', 'multiple_choice', NULL, NULL, 20, 1000, 13, 'Sabuk Asteroid (Asteroid Belt) memisahkan planet terestrial dan raksasa gas.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (69, 1, 'Bintang fajar atau bintang kejora yang sering terlihat terang saat senja atau fajar adalah planet...?', 'multiple_choice', NULL, NULL, 15, 800, 14, 'Planet Venus memantulkan cahaya matahari dengan sangat terang karena awan asam sulfatnya.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (70, 1, 'Teleskop luar angkasa canggih penerus Hubble yang diluncurkan pada akhir tahun 2021 bernama...?', 'multiple_choice', NULL, NULL, 20, 1000, 15, 'James Webb Space Telescope (JWST) mengamati alam semesta dalam spektrum inframerah.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (71, 1, 'Ledakan dahsyat pada akhir siklus hidup sebuah bintang bermassa besar disebut...?', 'multiple_choice', NULL, NULL, 20, 1200, 16, 'Supernova melepaskan energi yang sangat besar dan menyebarkan unsur-unsur berat ke alam semesta.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (72, 1, 'Planet terbesar di tata surya kita yang memiliki massa lebih besar dari gabungan semua planet lain adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 17, 'Jupiter adalah raksasa gas dengan Bintik Merah Raksasa (Great Red Spot).', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (73, 1, 'Cahaya aurora yang indah di langit kutub bumi terjadi akibat interaksi partikel angin matahari dengan medan magnet bumi.', 'true_false', NULL, NULL, 15, 800, 18, 'Benar, Aurora Borealis (utara) dan Aurora Australis (selatan) terjadi di atmosfer atas dekat kutub.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (74, 1, 'Bintang yang menjadi penunjuk arah utara sejati di belahan bumi utara adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 19, 'Polaris (North Star) berada hampir sejajar dengan poros rotasi bumi bagian utara.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (75, 1, 'Berapa perkiraan usia alam semesta kita sejak peristiwa Dentuman Besar (Big Bang)?', 'multiple_choice', NULL, NULL, 20, 1200, 20, 'Pengukuran radiasi latar kosmik menunjukkan usia alam semesta sekitar 13,8 miliar tahun.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (76, 2, 'Pos 6 [Pohon Kehidupan]: Organ tumbuhan yang berfungsi menyerap air dan garam mineral dari dalam tanah adalah...?', 'multiple_choice', NULL, NULL, 15, 800, 6, 'Akar menyerap air dan zat hara melalui bulu-bulu akar.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (77, 2, 'Pos 7 [Kuil Gravitasi]: Siapakah ilmuwan yang terinspirasi apel jatuh dan merumuskan Hukum Gravitasi Universal?', 'multiple_choice', NULL, NULL, 20, 1000, 7, 'Sir Isaac Newton menerbitkan karyanya \"Philosophiae Naturalis Principia Mathematica\" pada 1687.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (78, 2, 'Pos 8 [Batu Magnet]: Kutub magnet yang senama jika didekatkan akan saling tarik-menarik.', 'true_false', NULL, NULL, 15, 800, 8, 'Salah! Kutub senama (U-U atau S-S) akan tolak-menolak, sedangkan kutub tidak senama akan tarik-menarik.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (79, 2, 'Pos 9 [Danau Es]: Pada suhu berapakah air murni mulai membeku menjadi es pada tekanan standar 1 atm?', 'multiple_choice', NULL, NULL, 15, 800, 9, 'Air membeku pada 0°C (32°F) dan mendidih pada 100°C (212°F).', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (80, 2, 'Pos 10 [Gua Api]: Gas alam utama yang tidak berbau dan mudah terbakar pada kompor gas elpiji adalah...?', 'multiple_choice', NULL, NULL, 20, 1000, 10, 'LPG (Liquefied Petroleum Gas) utamanya terdiri dari Propana dan Butana.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (81, 2, 'Pos 11 [Menara Waktu]: Berapakah jumlah detik dalam waktu 1 jam penuh?', 'multiple_choice', NULL, NULL, 20, 1000, 11, '1 jam = 60 menit x 60 detik = 3.600 detik.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (82, 2, 'Pos 12 [Kawah Kristal]: Batuan yang terbentuk dari pendinginan dan pemadatan magma pijar disebut batuan...?', 'multiple_choice', NULL, NULL, 20, 1200, 12, 'Batuan beku (Igneous rock) seperti basal, granit, dan obsidian terbentuk dari magma/lava.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (83, 2, 'Pos 13 [Jalur Kilat]: Apakah partikel subatomik bermuatan listrik negatif yang mengelilingi inti atom?', 'multiple_choice', NULL, NULL, 20, 1000, 13, 'Elektron bermuatan negatif, proton bermuatan positif, dan neutron netral.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (84, 2, 'Pos 14 [Samudra Dalam]: Selat tersibuk di dunia yang melintasi Indonesia, Malaysia, dan Singapura adalah...?', 'multiple_choice', NULL, NULL, 15, 1000, 14, 'Selat Malaka merupakan jalur pelayaran perdagangan internasional terpadat di dunia.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (85, 2, 'Pos 15 [Candi Prisma]: Pembiasan cahaya putih melalui prisma kaca menghasilkan spektrum warna pelangi yang disebut dispersi cahaya.', 'true_false', NULL, NULL, 15, 800, 15, 'Benar, dispersi menguraikan cahaya polikromatik menjadi monokromatik (Me-Ji-Ku-Hi-Bi-Ni-U).', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (86, 2, 'Pos 16 [Kubah Udara]: Tekanan udara di puncak gunung yang tinggi lebih rendah dibandingkan di pantai.', 'true_false', NULL, NULL, 15, 1000, 16, 'Benar, semakin tinggi suatu tempat, kerapatan dan tekanan udara semakin berkurang.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (87, 2, 'Pos 17 [Kandang Fabel]: Hewan yang berkembang biak dengan cara bertelur sekaligus melahirkan anaknya disebut...?', 'multiple_choice', NULL, NULL, 20, 1000, 17, 'Ovovivipar menyimpan telur di dalam tubuh induk hingga menetas lalu dilahirkan (contoh beberapa jenis hiu dan ular).', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (88, 2, 'Pos 18 [Cahaya Suci]: Apakah alat optik yang digunakan untuk mengamati benda-benda renik mikroskopis seperti bakteri?', 'multiple_choice', NULL, NULL, 15, 800, 18, 'Mikroskop menggunakan kombinasi lensa objektif dan okuler untuk memperbesar objek ribuan kali.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (89, 2, 'Pos 19 [Benteng Intan]: Mineral alami paling keras di planet bumi yang sering digunakan sebagai mata bor dan perhiasan adalah...?', 'multiple_choice', NULL, NULL, 15, 1000, 19, 'Intan (Berlian) memiliki skala kekerasan Mohs tertinggi yaitu 10.', '2026-10-16 15:20:03', '2026-10-16 15:20:03');
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `media_url`, `media_type`, `time_limit_seconds`, `base_points`, `order_index`, `explanation`, `created_at`, `updated_at`) VALUES (90, 2, 'Pos 20 [Singgasana Boss Quest]: Negara maritim kepulauan terbesar di dunia dengan lebih dari 17.000 pulau adalah...?', 'multiple_choice', NULL, NULL, 15, 1500, 20, 'Indonesia adalah negara kepulauan terbesar di dunia dengan garis pantai terpanjang kedua di dunia!', '2026-10-16 15:20:03', '2026-10-16 15:20:03');

-- Dumping data for `options`
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (385, 12, 'Canberra', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (386, 12, 'Sydney', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (387, 12, 'Melbourne', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (388, 12, 'Brisbane', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (389, 13, 'Cheetah', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (390, 13, 'Singa Afrika', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (391, 13, 'Kuda Pacu', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (392, 13, 'Kijang Thomson', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (393, 14, 'Kulit', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (394, 14, 'Hati (Liver)', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (395, 14, 'Paru-paru', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (396, 14, 'Otak', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (397, 15, 'Paris', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (398, 15, 'Roma', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (399, 15, 'London', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (400, 15, 'Berlin', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (401, 16, 'Samudra Pasifik', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (402, 16, 'Samudra Atlantik', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (403, 16, 'Samudra Hindia', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (404, 16, 'Samudra Arktik', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (405, 17, 'Dinasti Syailendra', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (406, 17, 'Dinasti Sanjaya', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (407, 17, 'Kerajaan Majapahit', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (408, 17, 'Kerajaan Singasari', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (409, 18, 'Thomas Alva Edison', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (410, 18, 'Nikola Tesla', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (411, 18, 'Alexander Graham Bell', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (412, 18, 'Guglielmo Marconi', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (413, 19, 'Bahasa Mandarin', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (414, 19, 'Bahasa Spanyol', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (415, 19, 'Bahasa Inggris', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (416, 19, 'Bahasa Hindi', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (417, 20, '7 Nada', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (418, 20, '8 Nada', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (419, 20, '5 Nada', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (420, 20, '12 Nada', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (421, 21, 'Oksigen (O2)', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (422, 21, 'Karbon Monoksida (CO)', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (423, 21, 'Nitrogen (N2)', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (424, 21, 'Helium (He)', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (425, 22, 'Tiongkok (China)', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (426, 22, 'Jepang', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (427, 22, 'Korea Selatan', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (428, 22, 'Vietnam', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (429, 23, 'Paus Biru', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (430, 23, 'Hiu Megalodon', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (431, 23, 'Paus Pembunuh (Orca)', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (432, 23, 'Gajah Laut', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (433, 24, 'Merkurius', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (434, 24, 'Venus', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (435, 24, 'Bumi', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (436, 24, 'Mars', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (437, 25, 'Yen (¥)', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (438, 25, 'Won (₩)', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (439, 25, 'Yuan (元)', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (440, 25, 'Ringgit (RM)', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (441, 26, '100', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (442, 26, '90', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (443, 26, '110', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (444, 26, '85', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (445, 27, 'Leonardo da Vinci', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (446, 27, 'Vincent van Gogh', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (447, 27, 'Pablo Picasso', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (448, 27, 'Michelangelo', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (449, 28, 'Danau Superior', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (450, 28, 'Danau Toba', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (451, 28, 'Danau Baikal', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (452, 28, 'Danau Victoria', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (453, 29, 'Gunung Everest', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (454, 29, 'Gunung K2', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (455, 29, 'Gunung Kilimanjaro', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (456, 29, 'Gunung Denali', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (457, 30, 'Angklung', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (458, 30, 'Kolintang', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (459, 30, 'Gamelan', NULL, 0, 3, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (460, 30, 'Sasando', NULL, 0, 4, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (461, 31, 'Vitamin D', NULL, 1, 1, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (462, 31, 'Vitamin C', NULL, 0, 2, '2026-10-16 15:31:40', '2026-10-16 15:31:40');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (463, 31, 'Vitamin A', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (464, 31, 'Vitamin B12', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (465, 32, '11 Pemain', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (466, 32, '10 Pemain', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (467, 32, '12 Pemain', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (468, 32, '9 Pemain', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (469, 33, 'Benua Australia', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (470, 33, 'Benua Eropa', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (471, 33, 'Benua Antartika', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (472, 33, 'Benua Amerika Selatan', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (473, 34, 'Kanguru', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (474, 34, 'Koala', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (475, 34, 'Platipus', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (476, 34, 'Trenggiling', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (477, 35, 'H2O', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (478, 35, 'CO2', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (479, 35, 'NaCl', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (480, 35, 'CH4', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (481, 36, '1945', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (482, 36, '1928', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (483, 36, '1908', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (484, 36, '1950', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (485, 37, 'Garuda', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (486, 37, 'Cendrawasih', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (487, 37, 'Elang Jawa', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (488, 37, 'Merak Emas', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (489, 38, 'Karbon Dioksida (CO2)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (490, 38, 'Oksigen (O2)', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (491, 38, 'Nitrogen (N2)', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (492, 38, 'Argon (Ar)', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (493, 39, 'Mesir', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (494, 39, 'Yunani', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (495, 39, 'Turki', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (496, 39, 'Irak', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (497, 40, 'Klorofil', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (498, 40, 'Melanin', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (499, 40, 'Karotenoid', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (500, 40, 'Hemoglobin', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (501, 41, 'Albert Einstein', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (502, 41, 'Sir Isaac Newton', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (503, 41, 'Stephen Hawking', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (504, 41, 'Galileo Galilei', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (505, 42, 'Shinkansen', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (506, 42, 'TGV', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (507, 42, 'Eurostar', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (508, 42, 'Transrapid', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (509, 43, 'Otak Kecil (Cerebellum)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (510, 43, 'Otak Besar (Cerebrum)', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (511, 43, 'Batang Otak (Brainstem)', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (512, 43, 'Hipotalamus', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (513, 44, 'Selat Sunda', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (514, 44, 'Selat Malaka', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (515, 44, 'Selat Bali', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (516, 44, 'Selat Makassar', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (517, 45, '24 tulang (12 pasang)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (518, 45, '20 tulang (10 pasang)', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (519, 45, '28 tulang (14 pasang)', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (520, 45, '16 tulang (8 pasang)', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (521, 46, 'Atletik', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (522, 46, 'Senam Artistik', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (523, 46, 'Renang', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (524, 46, 'Pencak Silat', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (525, 47, 'Raksa (Merkuri)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (526, 47, 'Timbal', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (527, 47, 'Tembaga', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (528, 47, 'Seng', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (529, 48, 'Alexander Graham Bell', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (530, 48, 'Samuel Morse', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (531, 48, 'Guglielmo Marconi', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (532, 48, 'Steve Jobs', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (533, 49, 'Yogyakarta', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (534, 49, 'Bandung', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (535, 49, 'Malang', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (536, 49, 'Semarang', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (537, 50, 'Bulan', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (538, 50, 'Titan', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (539, 50, 'Phobos', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (540, 50, 'Europa', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (541, 51, 'Lapisan Ozon', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (542, 51, 'Troposfer Bawah', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (543, 51, 'Mesosfer', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (544, 51, 'Ionosfer', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (545, 52, 'Indonesia', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (546, 52, 'Filipina', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (547, 52, 'Vietnam', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (548, 52, 'Thailand', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (549, 53, 'Sumatra', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (550, 53, 'Jawa', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (551, 53, 'Kalimantan', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (552, 53, 'Sulawesi', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (553, 54, 'Bit', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (554, 54, 'Byte', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (555, 54, 'Kilobyte', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (556, 54, 'Pixel', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (557, 55, 'Benar', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (558, 55, 'Salah', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (559, 56, 'NaCl (Natrium Klorida)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (560, 56, 'KCl (Kalium Klorida)', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (561, 56, 'CaCO3 (Kalsium Karbonat)', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (562, 56, 'NaOH (Natrium Hidroksida)', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (563, 57, 'Gurun Sahara', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (564, 57, 'Gurun Gobi', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (565, 57, 'Gurun Kalahari', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (566, 57, 'Gurun Atacama', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (567, 58, 'George Washington', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (568, 58, 'Abraham Lincoln', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (569, 58, 'Thomas Jefferson', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (570, 58, 'Benjamin Franklin', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (571, 59, 'Merah, Kuning, Biru', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (572, 59, 'Merah, Hijau, Biru', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (573, 59, 'Cyan, Magenta, Kuning', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (574, 59, 'Hitam, Putih, Abu-abu', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (575, 60, 'Karnivora', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (576, 60, 'Herbivora', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (577, 60, 'Omnivora', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (578, 60, 'Insektivora', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (579, 61, 'Italia', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (580, 61, 'Spanyol', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (581, 61, 'Prancis', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (582, 61, 'Yunani', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (583, 1, 'Mars', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (584, 1, 'Venus', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (585, 1, 'Jupiter', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (586, 1, 'Merkurius', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (587, 2, '300.000 km/detik', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (588, 2, '150.000 km/detik', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (589, 2, '1.000.000 km/detik', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (590, 2, '30.000 km/detik', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (591, 3, 'Salah (Matahari adalah Bintang)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (592, 3, 'Benar', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (593, 4, 'Olympus Mons', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (594, 4, 'Mauna Kea', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (595, 4, 'Mount Everest', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (596, 4, 'Krakatau Prime', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (597, 5, 'Nitrogen (N2)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (598, 5, 'Oksigen (O2)', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (599, 5, 'Karbon Dioksida', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (600, 5, 'Hidrogen', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (601, 6, 'Sekitar 8 Menit', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (602, 6, 'Instan (0 detik)', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (603, 6, '1 Jam', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (604, 6, '24 Jam', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (605, 62, 'Gravitasi Bulan', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (606, 62, 'Gravitasi Jupiter', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (607, 62, 'Gravitasi Mars', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (608, 62, 'Angin Matahari', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (609, 63, 'Saturnus', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (610, 63, 'Uranus', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (611, 63, 'Neptunus', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (612, 63, 'Jupiter', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (613, 64, 'Bima Sakti (Milky Way)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (614, 64, 'Andromeda', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (615, 64, 'Sombrero', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (616, 64, 'Triangulum', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (617, 65, 'Lubang Hitam (Black Hole)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (618, 65, 'Bintang Neutron', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (619, 65, 'Nebula Gas', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (620, 65, 'Katai Putih', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (621, 66, 'Neil Armstrong', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (622, 66, 'Buzz Aldrin', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (623, 66, 'Yuri Gagarin', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (624, 66, 'Michael Collins', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (625, 67, 'Venus', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (626, 67, 'Merkurius', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (627, 67, 'Mars', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (628, 67, 'Jupiter', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (629, 68, 'Asteroid', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (630, 68, 'Komet', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (631, 68, 'Meteor', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (632, 68, 'Supernova', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (633, 69, 'Venus', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (634, 69, 'Mars', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (635, 69, 'Merkurius', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (636, 69, 'Saturnus', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (637, 70, 'James Webb (JWST)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (638, 70, 'Kepler Observatory', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (639, 70, 'Spitzer', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (640, 70, 'Galileo Orbiter', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (641, 71, 'Supernova', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (642, 71, 'Protostar', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (643, 71, 'Pulsar', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (644, 71, 'Aurora', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (645, 72, 'Jupiter', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (646, 72, 'Saturnus', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (647, 72, 'Uranus', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (648, 72, 'Neptunus', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (649, 73, 'Benar', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (650, 73, 'Salah', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (651, 74, 'Polaris', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (652, 74, 'Sirius', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (653, 74, 'Betelgeuse', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (654, 74, 'Alpha Centauri', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (655, 75, '13,8 Miliar Tahun', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (656, 75, '4,5 Miliar Tahun', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (657, 75, '100 Miliar Tahun', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (658, 75, '1 Miliar Tahun', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (659, 7, 'Kelelawar', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (660, 7, 'Tupai Terbang', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (661, 7, 'Lemur Melayang', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (662, 7, 'Burung Hantu', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (663, 8, 'Sungai Nil', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (664, 8, 'Sungai Amazon', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (665, 8, 'Sungai Kongo', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (666, 8, 'Sungai Zambezi', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (667, 9, 'Benar (Au)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (668, 9, 'Salah (Ag)', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (669, 10, '7 Benua', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (670, 10, '5 Benua', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (671, 10, '6 Benua', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (672, 10, '8 Benua', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (673, 11, 'Evaporasi', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (674, 11, 'Kondensasi', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (675, 11, 'Presipitasi', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (676, 11, 'Infiltrasi', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (677, 76, 'Akar', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (678, 76, 'Batang', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (679, 76, 'Daun', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (680, 76, 'Bunga', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (681, 77, 'Sir Isaac Newton', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (682, 77, 'Galileo Galilei', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (683, 77, 'Johannes Kepler', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (684, 77, 'Robert Hooke', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (685, 78, 'Salah (Tolak-menolak)', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (686, 78, 'Benar', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (687, 79, '0° Celsius', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (688, 79, '-5° Celsius', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (689, 79, '4° Celsius', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (690, 79, '-10° Celsius', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (691, 80, 'Propana & Butana', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (692, 80, 'Oksigen & Nitrogen', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (693, 80, 'Karbon Monoksida', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (694, 80, 'Klorin', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (695, 81, '3.600 Detik', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (696, 81, '1.800 Detik', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (697, 81, '6.000 Detik', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (698, 81, '2.400 Detik', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (699, 82, 'Batuan Beku', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (700, 82, 'Batuan Sedimen', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (701, 82, 'Batuan Metamorf', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (702, 82, 'Batuan Fosil', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (703, 83, 'Elektron', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (704, 83, 'Proton', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (705, 83, 'Neutron', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (706, 83, 'Foton', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (707, 84, 'Selat Malaka', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (708, 84, 'Selat Gibraltar', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (709, 84, 'Selat Hormuz', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (710, 84, 'Selat Bosporus', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (711, 85, 'Benar', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (712, 85, 'Salah', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (713, 86, 'Benar', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (714, 86, 'Salah', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (715, 87, 'Ovovivipar', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (716, 87, 'Vivipar', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (717, 87, 'Ovipar', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (718, 87, 'Mamalia', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (719, 88, 'Mikroskop', NULL, 1, 1, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (720, 88, 'Teleskop', NULL, 0, 2, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (721, 88, 'Periskop', NULL, 0, 3, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (722, 88, 'Lup (Kaca Pembesar)', NULL, 0, 4, '2026-10-16 15:31:41', '2026-10-16 15:31:41');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (723, 89, 'Intan (Berlian)', NULL, 1, 1, '2026-10-16 15:31:42', '2026-10-16 15:31:42');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (724, 89, 'Batu Safir', NULL, 0, 2, '2026-10-16 15:31:42', '2026-10-16 15:31:42');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (725, 89, 'Batu Kuarsa', NULL, 0, 3, '2026-10-16 15:31:42', '2026-10-16 15:31:42');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (726, 89, 'Batu Giok', NULL, 0, 4, '2026-10-16 15:31:42', '2026-10-16 15:31:42');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (727, 90, 'Indonesia', NULL, 1, 1, '2026-10-16 15:31:42', '2026-10-16 15:31:42');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (728, 90, 'Filipina', NULL, 0, 2, '2026-10-16 15:31:42', '2026-10-16 15:31:42');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (729, 90, 'Jepang', NULL, 0, 3, '2026-10-16 15:31:42', '2026-10-16 15:31:42');
INSERT INTO `options` (`id`, `question_id`, `option_text`, `media_url`, `is_correct`, `order_index`, `created_at`, `updated_at`) VALUES (730, 90, 'Inggris Raya', NULL, 0, 4, '2026-10-16 15:31:42', '2026-10-16 15:31:42');

SET FOREIGN_KEY_CHECKS=1;
