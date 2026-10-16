<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::firstOrCreate(
            ['email' => 'guru@smartfun.test'],
            [
                'name' => 'Pak Budi SmartFun',
                'password' => bcrypt('password123'),
            ]
        );

        // 1. MEGA QUIZ: 50 SOAL PENGETAHUAN UMUM
        $quizUmum = Quiz::updateOrCreate(
            ['slug' => '50-soal-pengetahuan-umum'],
            [
                'user_id' => $teacher->id,
                'title' => '🧠 Mega Bank: 50 Soal Pengetahuan Umum Cerdas & Seru',
                'description' => 'Bank kuis serbaguna berisi 50 pertanyaan umum seputar sains, sejarah, geografi, budaya, dan teknologi. Dapat dimainkan 5, 10, 20, 25, 40, hingga 50 soal!',
                'cover_image' => 'https://images.unsplash.com/photo-1606326608606-aa0b62935f2b?w=800&auto=format&fit=crop&q=80',
                'theme' => 'arcade',
                'is_public' => true,
            ]
        );

        $soalUmum50 = [
            [
                'text' => 'Ibukota resmi dari negara Australia adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Ibukota Australia adalah Canberra, bukan Sydney ataupun Melbourne.',
                'options' => [
                    ['text' => 'Canberra', 'correct' => true],
                    ['text' => 'Sydney', 'correct' => false],
                    ['text' => 'Melbourne', 'correct' => false],
                    ['text' => 'Brisbane', 'correct' => false],
                ],
            ],
            [
                'text' => 'Hewan mamalia darat tercepat di dunia yang mampu berlari hingga 120 km/jam adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Cheetah dapat berakselerasi dari 0 hingga 100 km/jam hanya dalam waktu sekitar 3 detik.',
                'options' => [
                    ['text' => 'Cheetah', 'correct' => true],
                    ['text' => 'Singa Afrika', 'correct' => false],
                    ['text' => 'Kuda Pacu', 'correct' => false],
                    ['text' => 'Kijang Thomson', 'correct' => false],
                ],
            ],
            [
                'text' => 'Organ tubuh manusia terbesar berdasarkan luas permukaan dan beratnya adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Kulit manusia memiliki luas sekitar 2 meter persegi dan melindungi seluruh organ dalam.',
                'options' => [
                    ['text' => 'Kulit', 'correct' => true],
                    ['text' => 'Hati (Liver)', 'correct' => false],
                    ['text' => 'Paru-paru', 'correct' => false],
                    ['text' => 'Otak', 'correct' => false],
                ],
            ],
            [
                'text' => 'Menara Eiffel yang terkenal di dunia terletak di kota mana?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 1000,
                'explanation' => 'Menara Eiffel dibangun di Paris, Prancis oleh Gustave Eiffel pada tahun 1889.',
                'options' => [
                    ['text' => 'Paris', 'correct' => true],
                    ['text' => 'Roma', 'correct' => false],
                    ['text' => 'London', 'correct' => false],
                    ['text' => 'Berlin', 'correct' => false],
                ],
            ],
            [
                'text' => 'Apakah samudra paling luas dan terdalam di planet Bumi?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Samudra Pasifik mencakup lebih dari 30% permukaan Bumi dan memuat Palung Mariana.',
                'options' => [
                    ['text' => 'Samudra Pasifik', 'correct' => true],
                    ['text' => 'Samudra Atlantik', 'correct' => false],
                    ['text' => 'Samudra Hindia', 'correct' => false],
                    ['text' => 'Samudra Arktik', 'correct' => false],
                ],
            ],
            [
                'text' => 'Candi Borobudur yang megah di Magelang dibangun oleh dinasti apa?',
                'type' => 'multiple_choice',
                'time' => 25,
                'points' => 1200,
                'explanation' => 'Candi Borobudur dibangun sekitar abad ke-8 dan ke-9 Masehi oleh Dinasti Syailendra penganut Buddha Mahayana.',
                'options' => [
                    ['text' => 'Dinasti Syailendra', 'correct' => true],
                    ['text' => 'Dinasti Sanjaya', 'correct' => false],
                    ['text' => 'Kerajaan Majapahit', 'correct' => false],
                    ['text' => 'Kerajaan Singasari', 'correct' => false],
                ],
            ],
            [
                'text' => 'Siapakah penemu bola lampu pijar praktis yang terkenal dengan ribuan eksperimennya?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Thomas Alva Edison mempatenkan desain lampu pijar komersial pertama yang tahan lama pada 1879.',
                'options' => [
                    ['text' => 'Thomas Alva Edison', 'correct' => true],
                    ['text' => 'Nikola Tesla', 'correct' => false],
                    ['text' => 'Alexander Graham Bell', 'correct' => false],
                    ['text' => 'Guglielmo Marconi', 'correct' => false],
                ],
            ],
            [
                'text' => 'Bahasa dengan jumlah penutur asli (native speaker) terbanyak di dunia adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Bahasa Mandarin memiliki lebih dari 900 juta penutur asli di seluruh dunia.',
                'options' => [
                    ['text' => 'Bahasa Mandarin', 'correct' => true],
                    ['text' => 'Bahasa Spanyol', 'correct' => false],
                    ['text' => 'Bahasa Inggris', 'correct' => false],
                    ['text' => 'Bahasa Hindi', 'correct' => false],
                ],
            ],
            [
                'text' => 'Berapakah jumlah nada pokok dalam tangga nada diatonis standar (Do sampai Si)?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Tangga nada diatonis terdiri dari 7 nada pokok: Do, Re, Mi, Fa, Sol, La, Si.',
                'options' => [
                    ['text' => '7 Nada', 'correct' => true],
                    ['text' => '8 Nada', 'correct' => false],
                    ['text' => '5 Nada', 'correct' => false],
                    ['text' => '12 Nada', 'correct' => false],
                ],
            ],
            [
                'text' => 'Gas apakah yang dihirup manusia saat bernapas untuk membakar energi dalam sel tubuh?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Oksigen (O2) digunakan dalam respirasi sel untuk memproduksi adenosin trifosfat (ATP).',
                'options' => [
                    ['text' => 'Oksigen (O2)', 'correct' => true],
                    ['text' => 'Karbon Monoksida (CO)', 'correct' => false],
                    ['text' => 'Nitrogen (N2)', 'correct' => false],
                    ['text' => 'Helium (He)', 'correct' => false],
                ],
            ],
            [
                'text' => 'Negara manakah yang dijuluki sebagai "Negeri Tirai Bambu"?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 1000,
                'explanation' => 'Tiongkok (China) dikenal dengan julukan Negeri Tirai Bambu, sedangkan Uni Soviet dahulu dijuluki Tirai Besi.',
                'options' => [
                    ['text' => 'Tiongkok (China)', 'correct' => true],
                    ['text' => 'Jepang', 'correct' => false],
                    ['text' => 'Korea Selatan', 'correct' => false],
                    ['text' => 'Vietnam', 'correct' => false],
                ],
            ],
            [
                'text' => 'Hewan mamalia laut terbesar di dunia yang beratnya bisa mencapai 150 ton adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Paus Biru (Blue Whale) adalah hewan terbesar yang pernah ada di muka bumi, bahkan melebihi dinosaurus.',
                'options' => [
                    ['text' => 'Paus Biru', 'correct' => true],
                    ['text' => 'Hiu Megalodon', 'correct' => false],
                    ['text' => 'Paus Pembunuh (Orca)', 'correct' => false],
                    ['text' => 'Gajah Laut', 'correct' => false],
                ],
            ],
            [
                'text' => 'Planet apakah yang memiliki orbit paling dekat dengan Matahari?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 1000,
                'explanation' => 'Merkurius adalah planet terkecil dan terdekat dengan Matahari di tata surya kita.',
                'options' => [
                    ['text' => 'Merkurius', 'correct' => true],
                    ['text' => 'Venus', 'correct' => false],
                    ['text' => 'Bumi', 'correct' => false],
                    ['text' => 'Mars', 'correct' => false],
                ],
            ],
            [
                'text' => 'Apakah nama mata uang resmi dari negara Jepang?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Mata uang resmi Jepang adalah Yen (¥), sedangkan Won adalah mata uang Korea dan Yuan adalah Tiongkok.',
                'options' => [
                    ['text' => 'Yen (¥)', 'correct' => true],
                    ['text' => 'Won (₩)', 'correct' => false],
                    ['text' => 'Yuan (元)', 'correct' => false],
                    ['text' => 'Ringgit (RM)', 'correct' => false],
                ],
            ],
            [
                'text' => 'Berapakah hasil dari perhitungan matematika: (15 x 6) + 10 ?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => '15 x 6 = 90, kemudian 90 + 10 = 100.',
                'options' => [
                    ['text' => '100', 'correct' => true],
                    ['text' => '90', 'correct' => false],
                    ['text' => '110', 'correct' => false],
                    ['text' => '85', 'correct' => false],
                ],
            ],
            [
                'text' => 'Siapakah maestro legendaris yang melukis mahakarya "Mona Lisa"?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Leonardo da Vinci melukis Mona Lisa pada masa Renaisans Italia sekitar awal abad ke-16.',
                'options' => [
                    ['text' => 'Leonardo da Vinci', 'correct' => true],
                    ['text' => 'Vincent van Gogh', 'correct' => false],
                    ['text' => 'Pablo Picasso', 'correct' => false],
                    ['text' => 'Michelangelo', 'correct' => false],
                ],
            ],
            [
                'text' => 'Danau air tawar terluas di dunia berdasarkan luas permukaan adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Danau Superior di perbatasan AS dan Kanada adalah danau air tawar terluas di dunia.',
                'options' => [
                    ['text' => 'Danau Superior', 'correct' => true],
                    ['text' => 'Danau Toba', 'correct' => false],
                    ['text' => 'Danau Baikal', 'correct' => false],
                    ['text' => 'Danau Victoria', 'correct' => false],
                ],
            ],
            [
                'text' => 'Gunung tertinggi di dunia yang puncaknya mencapai 8.848 meter di atas permukaan laut adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Gunung Everest terletak di pegunungan Himalaya di perbatasan Nepal dan Tibet.',
                'options' => [
                    ['text' => 'Gunung Everest', 'correct' => true],
                    ['text' => 'Gunung K2', 'correct' => false],
                    ['text' => 'Gunung Kilimanjaro', 'correct' => false],
                    ['text' => 'Gunung Denali', 'correct' => false],
                ],
            ],
            [
                'text' => 'Alat musik tradisional Indonesia berbahan bambu yang dimainkan dengan cara digoyangkan adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Angklung adalah alat musik tradisional Sunda (Jawa Barat) yang telah diakui UNESCO sebagai warisan dunia.',
                'options' => [
                    ['text' => 'Angklung', 'correct' => true],
                    ['text' => 'Kolintang', 'correct' => false],
                    ['text' => 'Gamelan', 'correct' => false],
                    ['text' => 'Sasando', 'correct' => false],
                ],
            ],
            [
                'text' => 'Vitamin apakah yang diproduksi secara alami oleh kulit dengan bantuan paparan sinar matahari pagi?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 1000,
                'explanation' => 'Sinar UVB dari matahari mengubah senyawa 7-dehidrokolesterol di kulit menjadi Vitamin D3.',
                'options' => [
                    ['text' => 'Vitamin D', 'correct' => true],
                    ['text' => 'Vitamin C', 'correct' => false],
                    ['text' => 'Vitamin A', 'correct' => false],
                    ['text' => 'Vitamin B12', 'correct' => false],
                ],
            ],
            [
                'text' => 'Berapakah jumlah pemain inti sepak bola dari satu tim di lapangan saat pertandingan resmi?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Satu tim sepak bola terdiri dari 11 pemain di lapangan, termasuk 1 penjaga gawang.',
                'options' => [
                    ['text' => '11 Pemain', 'correct' => true],
                    ['text' => '10 Pemain', 'correct' => false],
                    ['text' => '12 Pemain', 'correct' => false],
                    ['text' => '9 Pemain', 'correct' => false],
                ],
            ],
            [
                'text' => 'Benua terkecil di dunia berdasarkan luas daratannya adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 1000,
                'explanation' => 'Benua Australia adalah benua terkecil di dunia dengan luas sekitar 7,7 juta km persegi.',
                'options' => [
                    ['text' => 'Benua Australia', 'correct' => true],
                    ['text' => 'Benua Eropa', 'correct' => false],
                    ['text' => 'Benua Antartika', 'correct' => false],
                    ['text' => 'Benua Amerika Selatan', 'correct' => false],
                ],
            ],
            [
                'text' => 'Hewan marsupial berkantung khas Australia yang melompat menggunakan kaki belakangnya adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Kanguru membawa anaknya di dalam kantung perut (marsupium) hingga cukup dewasa.',
                'options' => [
                    ['text' => 'Kanguru', 'correct' => true],
                    ['text' => 'Koala', 'correct' => false],
                    ['text' => 'Platipus', 'correct' => false],
                    ['text' => 'Trenggiling', 'correct' => false],
                ],
            ],
            [
                'text' => 'Rumus senyawa kimia untuk air murni yang esensial bagi kehidupan adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Air terdiri dari dua atom Hidrogen dan satu atom Oksigen (H2O).',
                'options' => [
                    ['text' => 'H2O', 'correct' => true],
                    ['text' => 'CO2', 'correct' => false],
                    ['text' => 'NaCl', 'correct' => false],
                    ['text' => 'CH4', 'correct' => false],
                ],
            ],
            [
                'text' => 'Teks Proklamasi Kemerdekaan Indonesia dibacakan oleh Ir. Soekarno pada tahun...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Indonesia memproklamasikan kemerdekaannya pada hari Jumat, 17 Agustus 1945.',
                'options' => [
                    ['text' => '1945', 'correct' => true],
                    ['text' => '1928', 'correct' => false],
                    ['text' => '1908', 'correct' => false],
                    ['text' => '1950', 'correct' => false],
                ],
            ],
            [
                'text' => 'Burung apakah yang dijadikan sebagai lambang negara kesatuan Republik Indonesia?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Garuda Pancasila adalah lambang negara Indonesia dengan semboyan Bhinneka Tunggal Ika.',
                'options' => [
                    ['text' => 'Garuda', 'correct' => true],
                    ['text' => 'Cendrawasih', 'correct' => false],
                    ['text' => 'Elang Jawa', 'correct' => false],
                    ['text' => 'Merak Emas', 'correct' => false],
                ],
            ],
            [
                'text' => 'Gas apakah yang diserap oleh tumbuhan hijau dari udara saat melakukan fotosintesis?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Tumbuhan menyerap Karbon Dioksida (CO2) dan melepaskan Oksigen (O2).',
                'options' => [
                    ['text' => 'Karbon Dioksida (CO2)', 'correct' => true],
                    ['text' => 'Oksigen (O2)', 'correct' => false],
                    ['text' => 'Nitrogen (N2)', 'correct' => false],
                    ['text' => 'Argon (Ar)', 'correct' => false],
                ],
            ],
            [
                'text' => 'Piramida Agung Giza dan patung Sphinx yang berusia ribuan tahun terletak di negara...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Piramida Giza berada di pinggiran kota Kairo, Mesir.',
                'options' => [
                    ['text' => 'Mesir', 'correct' => true],
                    ['text' => 'Yunani', 'correct' => false],
                    ['text' => 'Turki', 'correct' => false],
                    ['text' => 'Irak', 'correct' => false],
                ],
            ],
            [
                'text' => 'Zat pigmen hijau pada sel daun yang berfungsi menyerap energi sinar matahari disebut...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Klorofil berada di dalam kloroplas dan memberikan warna hijau khas pada tumbuhan.',
                'options' => [
                    ['text' => 'Klorofil', 'correct' => true],
                    ['text' => 'Melanin', 'correct' => false],
                    ['text' => 'Karotenoid', 'correct' => false],
                    ['text' => 'Hemoglobin', 'correct' => false],
                ],
            ],
            [
                'text' => 'Siapakah ilmuwan fisika genius yang merumuskan persamaan kesetaraan massa dan energi E=mc²?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Albert Einstein mempublikasikan teori relativitas khusus ini pada tahun 1905.',
                'options' => [
                    ['text' => 'Albert Einstein', 'correct' => true],
                    ['text' => 'Sir Isaac Newton', 'correct' => false],
                    ['text' => 'Stephen Hawking', 'correct' => false],
                    ['text' => 'Galileo Galilei', 'correct' => false],
                ],
            ],
            [
                'text' => 'Kereta peluru super cepat berkecepatan lebih dari 300 km/jam di Jepang dikenal dengan nama...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Shinkansen (新幹線) pertama kali beroperasi pada tahun 1964 menyambut Olimpiade Tokyo.',
                'options' => [
                    ['text' => 'Shinkansen', 'correct' => true],
                    ['text' => 'TGV', 'correct' => false],
                    ['text' => 'Eurostar', 'correct' => false],
                    ['text' => 'Transrapid', 'correct' => false],
                ],
            ],
            [
                'text' => 'Bagian otak manusia yang bertanggung jawab mengatur koordinasi motorik dan keseimbangan tubuh adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1200,
                'explanation' => 'Otak kecil (Cerebellum) terletak di bagian belakang kepala di bawah otak besar.',
                'options' => [
                    ['text' => 'Otak Kecil (Cerebellum)', 'correct' => true],
                    ['text' => 'Otak Besar (Cerebrum)', 'correct' => false],
                    ['text' => 'Batang Otak (Brainstem)', 'correct' => false],
                    ['text' => 'Hipotalamus', 'correct' => false],
                ],
            ],
            [
                'text' => 'Selat yang menghubungkan Laut Jawa dengan Samudra Hindia di antara pulau Jawa dan Sumatra adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 1000,
                'explanation' => 'Selat Sunda memisahkan Anyer (Banten, Jawa) dan Bakauheni (Lampung, Sumatra).',
                'options' => [
                    ['text' => 'Selat Sunda', 'correct' => true],
                    ['text' => 'Selat Malaka', 'correct' => false],
                    ['text' => 'Selat Bali', 'correct' => false],
                    ['text' => 'Selat Makassar', 'correct' => false],
                ],
            ],
            [
                'text' => 'Berapakah total jumlah tulang rusuk pada kerangka dada manusia dewasa normal?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1200,
                'explanation' => 'Manusia memiliki 24 tulang rusuk (12 pasang di sisi kanan dan kiri).',
                'options' => [
                    ['text' => '24 tulang (12 pasang)', 'correct' => true],
                    ['text' => '20 tulang (10 pasang)', 'correct' => false],
                    ['text' => '28 tulang (14 pasang)', 'correct' => false],
                    ['text' => '16 tulang (8 pasang)', 'correct' => false],
                ],
            ],
            [
                'text' => 'Cabang olahraga yang mempertandingkan lari, lompat, dan lempar dikenal sebagai "Induk dari Segala Olahraga", yaitu...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Atletik dijuluki "Mother of Sports" karena gerakannya merupakan dasar gerak fisik manusia.',
                'options' => [
                    ['text' => 'Atletik', 'correct' => true],
                    ['text' => 'Senam Artistik', 'correct' => false],
                    ['text' => 'Renang', 'correct' => false],
                    ['text' => 'Pencak Silat', 'correct' => false],
                ],
            ],
            [
                'text' => 'Logam berat berwujud cair pada suhu ruangan yang biasa digunakan pada termometer adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Air raksa atau merkuri (Hg) memiliki titik lebur sangat rendah (-38,8°C).',
                'options' => [
                    ['text' => 'Raksa (Merkuri)', 'correct' => true],
                    ['text' => 'Timbal', 'correct' => false],
                    ['text' => 'Tembaga', 'correct' => false],
                    ['text' => 'Seng', 'correct' => false],
                ],
            ],
            [
                'text' => 'Tokoh yang mempatenkan pesawat telepon pertama di dunia pada tahun 1876 adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Alexander Graham Bell mengucapkan kata legendaris pertama: "Mr. Watson, come here, I want to see you."',
                'options' => [
                    ['text' => 'Alexander Graham Bell', 'correct' => true],
                    ['text' => 'Samuel Morse', 'correct' => false],
                    ['text' => 'Guglielmo Marconi', 'correct' => false],
                    ['text' => 'Steve Jobs', 'correct' => false],
                ],
            ],
            [
                'text' => 'Kota di Indonesia yang terkenal memiliki julukan "Kota Pelajar" dan "Kota Gudeg" adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'D.I. Yogyakarta memiliki ratusan perguruan tinggi dan pusat kebudayaan Jawa yang kental.',
                'options' => [
                    ['text' => 'Yogyakarta', 'correct' => true],
                    ['text' => 'Bandung', 'correct' => false],
                    ['text' => 'Malang', 'correct' => false],
                    ['text' => 'Semarang', 'correct' => false],
                ],
            ],
            [
                'text' => 'Satu-satunya satelit alami yang mengitari planet Bumi kita adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Bulan (Moon) mengitari Bumi dalam waktu sekitar 27,3 hari dan mempengaruhi pasang surut air laut.',
                'options' => [
                    ['text' => 'Bulan', 'correct' => true],
                    ['text' => 'Titan', 'correct' => false],
                    ['text' => 'Phobos', 'correct' => false],
                    ['text' => 'Europa', 'correct' => false],
                ],
            ],
            [
                'text' => 'Lapisan gas di atmosfer stratosfer yang melindungi bumi dari radiasi sinar ultraviolet berbahaya adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Lapisan Ozon (O3) menyerap sebagian besar radiasi UVB dari matahari.',
                'options' => [
                    ['text' => 'Lapisan Ozon', 'correct' => true],
                    ['text' => 'Troposfer Bawah', 'correct' => false],
                    ['text' => 'Mesosfer', 'correct' => false],
                    ['text' => 'Ionosfer', 'correct' => false],
                ],
            ],
            [
                'text' => 'Negara dengan populasi penduduk terbanyak di kawasan Asia Tenggara adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Indonesia memiliki lebih dari 280 juta jiwa, menjadikannya negara terpadat ke-4 di dunia.',
                'options' => [
                    ['text' => 'Indonesia', 'correct' => true],
                    ['text' => 'Filipina', 'correct' => false],
                    ['text' => 'Vietnam', 'correct' => false],
                    ['text' => 'Thailand', 'correct' => false],
                ],
            ],
            [
                'text' => 'Bunga raksasa tanpa daun dan batang "Rafflesia Arnoldii" pertama kali ditemukan di hutan pulau...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Rafflesia Arnoldii ditemukan di Bengkulu, Sumatra pada tahun 1818 oleh Dr. Joseph Arnold dan Thomas Stamford Raffles.',
                'options' => [
                    ['text' => 'Sumatra', 'correct' => true],
                    ['text' => 'Jawa', 'correct' => false],
                    ['text' => 'Kalimantan', 'correct' => false],
                    ['text' => 'Sulawesi', 'correct' => false],
                ],
            ],
            [
                'text' => 'Satuan unit terkecil dalam penyimpanan data digital komputer yang bernilai 0 atau 1 adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Bit (Binary Digit) adalah unit dasar, di mana 8 bit membentuk 1 Byte.',
                'options' => [
                    ['text' => 'Bit', 'correct' => true],
                    ['text' => 'Byte', 'correct' => false],
                    ['text' => 'Kilobyte', 'correct' => false],
                    ['text' => 'Pixel', 'correct' => false],
                ],
            ],
            [
                'text' => 'Hewan golongan amfibi mampu beradaptasi dan hidup di dua habitat, yaitu perairan dan daratan.',
                'type' => 'true_false',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Benar, contoh amfibi adalah katak dan salamander yang bernapas dengan insang saat berudu dan paru-paru/kulit saat dewasa.',
                'options' => [
                    ['text' => 'Benar', 'correct' => true],
                    ['text' => 'Salah', 'correct' => false],
                ],
            ],
            [
                'text' => 'Rumus kimia dari Garam Dapur yang lazim digunakan sebagai bumbu masakan sehari-hari adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Natrium Klorida (NaCl) adalah senyawa garam mineral yang memberi rasa asin.',
                'options' => [
                    ['text' => 'NaCl (Natrium Klorida)', 'correct' => true],
                    ['text' => 'KCl (Kalium Klorida)', 'correct' => false],
                    ['text' => 'CaCO3 (Kalsium Karbonat)', 'correct' => false],
                    ['text' => 'NaOH (Natrium Hidroksida)', 'correct' => false],
                ],
            ],
            [
                'text' => 'Gurun pasir terluas dan terpanas di dunia yang membentang melintasi benua Afrika bagian utara adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Gurun Sahara memiliki luas sekitar 9,2 juta kilometer persegi, hampir seukuran benua Amerika Serikat!',
                'options' => [
                    ['text' => 'Gurun Sahara', 'correct' => true],
                    ['text' => 'Gurun Gobi', 'correct' => false],
                    ['text' => 'Gurun Kalahari', 'correct' => false],
                    ['text' => 'Gurun Atacama', 'correct' => false],
                ],
            ],
            [
                'text' => 'Siapakah presiden pertama Amerika Serikat yang wajahnya diabadikan di Gunung Rushmore dan uang kertas 1 Dolar?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'George Washington menjabat sebagai presiden pertama AS dari tahun 1789 hingga 1797.',
                'options' => [
                    ['text' => 'George Washington', 'correct' => true],
                    ['text' => 'Abraham Lincoln', 'correct' => false],
                    ['text' => 'Thomas Jefferson', 'correct' => false],
                    ['text' => 'Benjamin Franklin', 'correct' => false],
                ],
            ],
            [
                'text' => 'Tiga warna primer dasar dalam teori seni rupa pigmen tradisional yang tidak dapat dibuat dari campuran warna lain adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Merah, Kuning, dan Biru adalah warna primer pigmen RYB klasik.',
                'options' => [
                    ['text' => 'Merah, Kuning, Biru', 'correct' => true],
                    ['text' => 'Merah, Hijau, Biru', 'correct' => false],
                    ['text' => 'Cyan, Magenta, Kuning', 'correct' => false],
                    ['text' => 'Hitam, Putih, Abu-abu', 'correct' => false],
                ],
            ],
            [
                'text' => 'Hewan yang mengonsumsi daging sebagai sumber nutrisi utamanya digolongkan sebagai...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Karnivora adalah pemakan daging, herbivora pemakan tumbuhan, dan omnivora pemakan segala.',
                'options' => [
                    ['text' => 'Karnivora', 'correct' => true],
                    ['text' => 'Herbivora', 'correct' => false],
                    ['text' => 'Omnivora', 'correct' => false],
                    ['text' => 'Insektivora', 'correct' => false],
                ],
            ],
            [
                'text' => 'Menara Lonceng miring yang sangat tersohor di dunia terletak di kota Pisa, negara...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Menara Miring Pisa dibangun di kota Pisa, wilayah Toskana, Italia.',
                'options' => [
                    ['text' => 'Italia', 'correct' => true],
                    ['text' => 'Spanyol', 'correct' => false],
                    ['text' => 'Prancis', 'correct' => false],
                    ['text' => 'Yunani', 'correct' => false],
                ],
            ],
        ];

        $this->seedQuestionsForQuiz($quizUmum, $soalUmum50);

        // 2. QUIZ: SAINS GALAKSI & ANTARIKSA (20 Soal)
        $quizSains = Quiz::updateOrCreate(
            ['slug' => 'sains-galaksi-pengetahuan-seru'],
            [
                'user_id' => $teacher->id,
                'title' => '🚀 Sains Galaksi & Rahasia Alam Semesta',
                'description' => 'Eksplorasi 20 rahasia luar angkasa, tata surya, lubang hitam, dan fisika modern!',
                'cover_image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&auto=format&fit=crop&q=80',
                'theme' => 'space',
                'is_public' => true,
            ]
        );

        $soalSains20 = [
            [
                'text' => 'Planet apakah yang memiliki julukan "Planet Merah" di tata surya kita?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Mars tampak kemerahan karena permukaannya kaya akan besi(III) oksida atau karat besi.',
                'options' => [
                    ['text' => 'Mars', 'correct' => true],
                    ['text' => 'Venus', 'correct' => false],
                    ['text' => 'Jupiter', 'correct' => false],
                    ['text' => 'Merkurius', 'correct' => false],
                ],
            ],
            [
                'text' => 'Berapa perkiraan kecepatan cahaya di ruang hampa udara?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Kecepatan cahaya di ruang hampa adalah tepat 299.792.458 meter per detik (~300.000 km/detik).',
                'options' => [
                    ['text' => '300.000 km/detik', 'correct' => true],
                    ['text' => '150.000 km/detik', 'correct' => false],
                    ['text' => '1.000.000 km/detik', 'correct' => false],
                    ['text' => '30.000 km/detik', 'correct' => false],
                ],
            ],
            [
                'text' => 'Matahari adalah sebuah planet raksasa yang berada di pusat tata surya.',
                'type' => 'true_false',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Salah! Matahari adalah Bintang tipe deret utama (G-type) yang melakukan fusi nuklir hidrogen.',
                'options' => [
                    ['text' => 'Salah (Matahari adalah Bintang)', 'correct' => true],
                    ['text' => 'Benar', 'correct' => false],
                ],
            ],
            [
                'text' => 'Apakah nama gunung berapi tertinggi di tata surya yang berada di planet Mars?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1200,
                'explanation' => 'Olympus Mons di Mars memiliki ketinggian hampir 22 kilometer!',
                'options' => [
                    ['text' => 'Olympus Mons', 'correct' => true],
                    ['text' => 'Mauna Kea', 'correct' => false],
                    ['text' => 'Mount Everest', 'correct' => false],
                    ['text' => 'Krakatau Prime', 'correct' => false],
                ],
            ],
            [
                'text' => 'Gas apakah yang paling melimpah di atmosfer bumi yang kita hirup sehari-hari?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Atmosfer bumi terdiri dari 78% Nitrogen (N2) dan 21% Oksigen.',
                'options' => [
                    ['text' => 'Nitrogen (N2)', 'correct' => true],
                    ['text' => 'Oksigen (O2)', 'correct' => false],
                    ['text' => 'Karbon Dioksida', 'correct' => false],
                    ['text' => 'Hidrogen', 'correct' => false],
                ],
            ],
            [
                'text' => 'Berapa lama waktu yang dibutuhkan cahaya matahari untuk sampai ke permukaan Bumi?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Jarak 150 juta km ditempuh cahaya matahari dalam waktu sekitar 8 menit 20 detik.',
                'options' => [
                    ['text' => 'Sekitar 8 Menit', 'correct' => true],
                    ['text' => 'Instan (0 detik)', 'correct' => false],
                    ['text' => '1 Jam', 'correct' => false],
                    ['text' => '24 Jam', 'correct' => false],
                ],
            ],
            [
                'text' => 'Gaya tarik gravitasi apakah yang menyebabkan peristiwa pasang dan surut air laut di Bumi?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Gravitasi Bulan memiliki pengaruh paling kuat terhadap pasang surut laut di Bumi.',
                'options' => [
                    ['text' => 'Gravitasi Bulan', 'correct' => true],
                    ['text' => 'Gravitasi Jupiter', 'correct' => false],
                    ['text' => 'Gravitasi Mars', 'correct' => false],
                    ['text' => 'Angin Matahari', 'correct' => false],
                ],
            ],
            [
                'text' => 'Planet manakah yang terkenal dengan sistem cincin es dan batu paling spektakuler?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Saturnus memiliki cincin es paling megah yang membentang hingga ratusan ribu kilometer.',
                'options' => [
                    ['text' => 'Saturnus', 'correct' => true],
                    ['text' => 'Uranus', 'correct' => false],
                    ['text' => 'Neptunus', 'correct' => false],
                    ['text' => 'Jupiter', 'correct' => false],
                ],
            ],
            [
                'text' => 'Apakah nama galaksi tempat tata surya kita berada?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Tata surya kita berada di dalam Galaksi Bima Sakti (Milky Way).',
                'options' => [
                    ['text' => 'Bima Sakti (Milky Way)', 'correct' => true],
                    ['text' => 'Andromeda', 'correct' => false],
                    ['text' => 'Sombrero', 'correct' => false],
                    ['text' => 'Triangulum', 'correct' => false],
                ],
            ],
            [
                'text' => 'Objek antariksa dengan gravitasi sangat kuat sehingga cahaya pun tidak dapat lolos disebut...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Lubang Hitam (Black Hole) memiliki batas cakrawala peristiwa (event horizon).',
                'options' => [
                    ['text' => 'Lubang Hitam (Black Hole)', 'correct' => true],
                    ['text' => 'Bintang Neutron', 'correct' => false],
                    ['text' => 'Nebula Gas', 'correct' => false],
                    ['text' => 'Katai Putih', 'correct' => false],
                ],
            ],
            [
                'text' => 'Siapakah manusia pertama yang berhasil mendarat dan berjalan di permukaan Bulan pada 1969?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Neil Armstrong mengucapkan kalimat: "That\'s one small step for man, one giant leap for mankind."',
                'options' => [
                    ['text' => 'Neil Armstrong', 'correct' => true],
                    ['text' => 'Buzz Aldrin', 'correct' => false],
                    ['text' => 'Yuri Gagarin', 'correct' => false],
                    ['text' => 'Michael Collins', 'correct' => false],
                ],
            ],
            [
                'text' => 'Planet terpanas di tata surya kita karena efek rumah kaca ekstrem atmosfernya adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1200,
                'explanation' => 'Venus memiliki suhu permukaan mencapai 465°C karena atmosfer tebal CO2.',
                'options' => [
                    ['text' => 'Venus', 'correct' => true],
                    ['text' => 'Merkurius', 'correct' => false],
                    ['text' => 'Mars', 'correct' => false],
                    ['text' => 'Jupiter', 'correct' => false],
                ],
            ],
            [
                'text' => 'Benda langit berbatu yang mengorbit matahari dan sebagian besar berada di antara Mars dan Jupiter disebut...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Sabuk Asteroid (Asteroid Belt) memisahkan planet terestrial dan raksasa gas.',
                'options' => [
                    ['text' => 'Asteroid', 'correct' => true],
                    ['text' => 'Komet', 'correct' => false],
                    ['text' => 'Meteor', 'correct' => false],
                    ['text' => 'Supernova', 'correct' => false],
                ],
            ],
            [
                'text' => 'Bintang fajar atau bintang kejora yang sering terlihat terang saat senja atau fajar adalah planet...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Planet Venus memantulkan cahaya matahari dengan sangat terang karena awan asam sulfatnya.',
                'options' => [
                    ['text' => 'Venus', 'correct' => true],
                    ['text' => 'Mars', 'correct' => false],
                    ['text' => 'Merkurius', 'correct' => false],
                    ['text' => 'Saturnus', 'correct' => false],
                ],
            ],
            [
                'text' => 'Teleskop luar angkasa canggih penerus Hubble yang diluncurkan pada akhir tahun 2021 bernama...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'James Webb Space Telescope (JWST) mengamati alam semesta dalam spektrum inframerah.',
                'options' => [
                    ['text' => 'James Webb (JWST)', 'correct' => true],
                    ['text' => 'Kepler Observatory', 'correct' => false],
                    ['text' => 'Spitzer', 'correct' => false],
                    ['text' => 'Galileo Orbiter', 'correct' => false],
                ],
            ],
            [
                'text' => 'Ledakan dahsyat pada akhir siklus hidup sebuah bintang bermassa besar disebut...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1200,
                'explanation' => 'Supernova melepaskan energi yang sangat besar dan menyebarkan unsur-unsur berat ke alam semesta.',
                'options' => [
                    ['text' => 'Supernova', 'correct' => true],
                    ['text' => 'Protostar', 'correct' => false],
                    ['text' => 'Pulsar', 'correct' => false],
                    ['text' => 'Aurora', 'correct' => false],
                ],
            ],
            [
                'text' => 'Planet terbesar di tata surya kita yang memiliki massa lebih besar dari gabungan semua planet lain adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Jupiter adalah raksasa gas dengan Bintik Merah Raksasa (Great Red Spot).',
                'options' => [
                    ['text' => 'Jupiter', 'correct' => true],
                    ['text' => 'Saturnus', 'correct' => false],
                    ['text' => 'Uranus', 'correct' => false],
                    ['text' => 'Neptunus', 'correct' => false],
                ],
            ],
            [
                'text' => 'Cahaya aurora yang indah di langit kutub bumi terjadi akibat interaksi partikel angin matahari dengan medan magnet bumi.',
                'type' => 'true_false',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Benar, Aurora Borealis (utara) dan Aurora Australis (selatan) terjadi di atmosfer atas dekat kutub.',
                'options' => [
                    ['text' => 'Benar', 'correct' => true],
                    ['text' => 'Salah', 'correct' => false],
                ],
            ],
            [
                'text' => 'Bintang yang menjadi penunjuk arah utara sejati di belahan bumi utara adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Polaris (North Star) berada hampir sejajar dengan poros rotasi bumi bagian utara.',
                'options' => [
                    ['text' => 'Polaris', 'correct' => true],
                    ['text' => 'Sirius', 'correct' => false],
                    ['text' => 'Betelgeuse', 'correct' => false],
                    ['text' => 'Alpha Centauri', 'correct' => false],
                ],
            ],
            [
                'text' => 'Berapa perkiraan usia alam semesta kita sejak peristiwa Dentuman Besar (Big Bang)?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1200,
                'explanation' => 'Pengukuran radiasi latar kosmik menunjukkan usia alam semesta sekitar 13,8 miliar tahun.',
                'options' => [
                    ['text' => '13,8 Miliar Tahun', 'correct' => true],
                    ['text' => '4,5 Miliar Tahun', 'correct' => false],
                    ['text' => '100 Miliar Tahun', 'correct' => false],
                    ['text' => '1 Miliar Tahun', 'correct' => false],
                ],
            ],
        ];

        $this->seedQuestionsForQuiz($quizSains, $soalSains20);

        // 3. QUIZ: WAYGROUND QUEST: EKSPEDISI HUTAN KRISTAL (20 Soal)
        $quizWayground = Quiz::updateOrCreate(
            ['slug' => 'wayground-ekspedisi-hutan-kristal'],
            [
                'user_id' => $teacher->id,
                'title' => '🗺️ Wayground: Ekspedisi Hutan Kristal (20 Quest)',
                'description' => 'Taklukkan tiap pos tantangan petualangan ajaib dari gerbang rimba hingga puncak kuil kristal!',
                'cover_image' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=800&auto=format&fit=crop&q=80',
                'theme' => 'fantasy',
                'is_public' => true,
            ]
        );

        $soalWayground20 = [
            [
                'text' => 'Pos 1 [Pintu Rimba]: Mamalia manakah yang memiliki kemampuan terbang sejati dengan mengepakkan sayap?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Kelelawar adalah satu-satunya mamalia sejati yang mampu terbang.',
                'options' => [
                    ['text' => 'Kelelawar', 'correct' => true],
                    ['text' => 'Tupai Terbang', 'correct' => false],
                    ['text' => 'Lemur Melayang', 'correct' => false],
                    ['text' => 'Burung Hantu', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 2 [Jembatan Nil]: Sungai terpanjang di benua Afrika yang melintasi 11 negara adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Sungai Nil membentang sejauh 6.650 km menuju Laut Tengah.',
                'options' => [
                    ['text' => 'Sungai Nil', 'correct' => true],
                    ['text' => 'Sungai Amazon', 'correct' => false],
                    ['text' => 'Sungai Kongo', 'correct' => false],
                    ['text' => 'Sungai Zambezi', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 3 [Gua Kristal]: Lambang unsur kimia Emas murni pada tabel periodik adalah Au (Aurum).',
                'type' => 'true_false',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Benar, Au berasal dari bahasa Latin "Aurum" yang berarti kilau fajar.',
                'options' => [
                    ['text' => 'Benar (Au)', 'correct' => true],
                    ['text' => 'Salah (Ag)', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 4 [Lembah Benua]: Berapakah jumlah total benua utama yang diakui di peta dunia?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 1000,
                'explanation' => '7 Benua: Asia, Afrika, Amerika Utara, Amerika Selatan, Antartika, Eropa, dan Australia.',
                'options' => [
                    ['text' => '7 Benua', 'correct' => true],
                    ['text' => '5 Benua', 'correct' => false],
                    ['text' => '6 Benua', 'correct' => false],
                    ['text' => '8 Benua', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 5 [Mata Air Murni]: Proses penguapan air laut ke udara membentuk awan disebut...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Evaporasi adalah perubahan wujud air cair menjadi uap gas yang naik ke atmosfer.',
                'options' => [
                    ['text' => 'Evaporasi', 'correct' => true],
                    ['text' => 'Kondensasi', 'correct' => false],
                    ['text' => 'Presipitasi', 'correct' => false],
                    ['text' => 'Infiltrasi', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 6 [Pohon Kehidupan]: Organ tumbuhan yang berfungsi menyerap air dan garam mineral dari dalam tanah adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Akar menyerap air dan zat hara melalui bulu-bulu akar.',
                'options' => [
                    ['text' => 'Akar', 'correct' => true],
                    ['text' => 'Batang', 'correct' => false],
                    ['text' => 'Daun', 'correct' => false],
                    ['text' => 'Bunga', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 7 [Kuil Gravitasi]: Siapakah ilmuwan yang terinspirasi apel jatuh dan merumuskan Hukum Gravitasi Universal?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Sir Isaac Newton menerbitkan karyanya "Philosophiae Naturalis Principia Mathematica" pada 1687.',
                'options' => [
                    ['text' => 'Sir Isaac Newton', 'correct' => true],
                    ['text' => 'Galileo Galilei', 'correct' => false],
                    ['text' => 'Johannes Kepler', 'correct' => false],
                    ['text' => 'Robert Hooke', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 8 [Batu Magnet]: Kutub magnet yang senama jika didekatkan akan saling tarik-menarik.',
                'type' => 'true_false',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Salah! Kutub senama (U-U atau S-S) akan tolak-menolak, sedangkan kutub tidak senama akan tarik-menarik.',
                'options' => [
                    ['text' => 'Salah (Tolak-menolak)', 'correct' => true],
                    ['text' => 'Benar', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 9 [Danau Es]: Pada suhu berapakah air murni mulai membeku menjadi es pada tekanan standar 1 atm?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Air membeku pada 0°C (32°F) dan mendidih pada 100°C (212°F).',
                'options' => [
                    ['text' => '0° Celsius', 'correct' => true],
                    ['text' => '-5° Celsius', 'correct' => false],
                    ['text' => '4° Celsius', 'correct' => false],
                    ['text' => '-10° Celsius', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 10 [Gua Api]: Gas alam utama yang tidak berbau dan mudah terbakar pada kompor gas elpiji adalah...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'LPG (Liquefied Petroleum Gas) utamanya terdiri dari Propana dan Butana.',
                'options' => [
                    ['text' => 'Propana & Butana', 'correct' => true],
                    ['text' => 'Oksigen & Nitrogen', 'correct' => false],
                    ['text' => 'Karbon Monoksida', 'correct' => false],
                    ['text' => 'Klorin', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 11 [Menara Waktu]: Berapakah jumlah detik dalam waktu 1 jam penuh?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => '1 jam = 60 menit x 60 detik = 3.600 detik.',
                'options' => [
                    ['text' => '3.600 Detik', 'correct' => true],
                    ['text' => '1.800 Detik', 'correct' => false],
                    ['text' => '6.000 Detik', 'correct' => false],
                    ['text' => '2.400 Detik', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 12 [Kawah Kristal]: Batuan yang terbentuk dari pendinginan dan pemadatan magma pijar disebut batuan...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1200,
                'explanation' => 'Batuan beku (Igneous rock) seperti basal, granit, dan obsidian terbentuk dari magma/lava.',
                'options' => [
                    ['text' => 'Batuan Beku', 'correct' => true],
                    ['text' => 'Batuan Sedimen', 'correct' => false],
                    ['text' => 'Batuan Metamorf', 'correct' => false],
                    ['text' => 'Batuan Fosil', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 13 [Jalur Kilat]: Apakah partikel subatomik bermuatan listrik negatif yang mengelilingi inti atom?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Elektron bermuatan negatif, proton bermuatan positif, dan neutron netral.',
                'options' => [
                    ['text' => 'Elektron', 'correct' => true],
                    ['text' => 'Proton', 'correct' => false],
                    ['text' => 'Neutron', 'correct' => false],
                    ['text' => 'Foton', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 14 [Samudra Dalam]: Selat tersibuk di dunia yang melintasi Indonesia, Malaysia, dan Singapura adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 1000,
                'explanation' => 'Selat Malaka merupakan jalur pelayaran perdagangan internasional terpadat di dunia.',
                'options' => [
                    ['text' => 'Selat Malaka', 'correct' => true],
                    ['text' => 'Selat Gibraltar', 'correct' => false],
                    ['text' => 'Selat Hormuz', 'correct' => false],
                    ['text' => 'Selat Bosporus', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 15 [Candi Prisma]: Pembiasan cahaya putih melalui prisma kaca menghasilkan spektrum warna pelangi yang disebut dispersi cahaya.',
                'type' => 'true_false',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Benar, dispersi menguraikan cahaya polikromatik menjadi monokromatik (Me-Ji-Ku-Hi-Bi-Ni-U).',
                'options' => [
                    ['text' => 'Benar', 'correct' => true],
                    ['text' => 'Salah', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 16 [Kubah Udara]: Tekanan udara di puncak gunung yang tinggi lebih rendah dibandingkan di pantai.',
                'type' => 'true_false',
                'time' => 15,
                'points' => 1000,
                'explanation' => 'Benar, semakin tinggi suatu tempat, kerapatan dan tekanan udara semakin berkurang.',
                'options' => [
                    ['text' => 'Benar', 'correct' => true],
                    ['text' => 'Salah', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 17 [Kandang Fabel]: Hewan yang berkembang biak dengan cara bertelur sekaligus melahirkan anaknya disebut...?',
                'type' => 'multiple_choice',
                'time' => 20,
                'points' => 1000,
                'explanation' => 'Ovovivipar menyimpan telur di dalam tubuh induk hingga menetas lalu dilahirkan (contoh beberapa jenis hiu dan ular).',
                'options' => [
                    ['text' => 'Ovovivipar', 'correct' => true],
                    ['text' => 'Vivipar', 'correct' => false],
                    ['text' => 'Ovipar', 'correct' => false],
                    ['text' => 'Mamalia', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 18 [Cahaya Suci]: Apakah alat optik yang digunakan untuk mengamati benda-benda renik mikroskopis seperti bakteri?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 800,
                'explanation' => 'Mikroskop menggunakan kombinasi lensa objektif dan okuler untuk memperbesar objek ribuan kali.',
                'options' => [
                    ['text' => 'Mikroskop', 'correct' => true],
                    ['text' => 'Teleskop', 'correct' => false],
                    ['text' => 'Periskop', 'correct' => false],
                    ['text' => 'Lup (Kaca Pembesar)', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 19 [Benteng Intan]: Mineral alami paling keras di planet bumi yang sering digunakan sebagai mata bor dan perhiasan adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 1000,
                'explanation' => 'Intan (Berlian) memiliki skala kekerasan Mohs tertinggi yaitu 10.',
                'options' => [
                    ['text' => 'Intan (Berlian)', 'correct' => true],
                    ['text' => 'Batu Safir', 'correct' => false],
                    ['text' => 'Batu Kuarsa', 'correct' => false],
                    ['text' => 'Batu Giok', 'correct' => false],
                ],
            ],
            [
                'text' => 'Pos 20 [Singgasana Boss Quest]: Negara maritim kepulauan terbesar di dunia dengan lebih dari 17.000 pulau adalah...?',
                'type' => 'multiple_choice',
                'time' => 15,
                'points' => 1500,
                'explanation' => 'Indonesia adalah negara kepulauan terbesar di dunia dengan garis pantai terpanjang kedua di dunia!',
                'options' => [
                    ['text' => 'Indonesia', 'correct' => true],
                    ['text' => 'Filipina', 'correct' => false],
                    ['text' => 'Jepang', 'correct' => false],
                    ['text' => 'Inggris Raya', 'correct' => false],
                ],
            ],
        ];

        $this->seedQuestionsForQuiz($quizWayground, $soalWayground20);
    }

    /**
     * Helper to seed questions & options cleanly.
     *
     * @param  array<int, array<string, mixed>>  $questions
     */
    protected function seedQuestionsForQuiz(Quiz $quiz, array $questions): void
    {
        foreach ($questions as $index => $item) {
            $question = Question::updateOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'order_index' => $index + 1,
                ],
                [
                    'question_text' => $item['text'],
                    'question_type' => $item['type'],
                    'media_url' => $item['media'] ?? null,
                    'media_type' => ! empty($item['media']) ? 'image' : null,
                    'time_limit_seconds' => $item['time'] ?? 20,
                    'base_points' => $item['points'] ?? 1000,
                    'explanation' => $item['explanation'] ?? null,
                ]
            );

            $question->options()->delete();
            foreach ($item['options'] as $optIdx => $opt) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct' => (bool) $opt['correct'],
                    'order_index' => $optIdx + 1,
                ]);
            }
        }
    }
}
