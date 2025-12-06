<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        if ($users->count() == 0) {
            $this->command->warn('Tidak ada user ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $viralNews = [
            [
                "title" => "Fenomena Cuaca Ekstrem Melanda Sejumlah Wilayah di Indonesia",
                "content" => "
Gelombang cuaca ekstrem kembali melanda sejumlah wilayah di Indonesia sejak awal pekan ini. Badan Meteorologi, Klimatologi, dan Geofisika (BMKG) mencatat peningkatan curah hujan signifikan yang disertai angin kencang dan potensi banjir bandang di berbagai daerah. Kondisi ini dipicu oleh anomali suhu permukaan laut yang meningkat di wilayah Samudra Pasifik dan Samudra Hindia sehingga memperkuat pembentukan awan hujan.

Di Jakarta, hujan deras yang turun selama lebih dari tiga jam menyebabkan beberapa ruas jalan tergenang dengan ketinggian mencapai 30–70 cm. Sejumlah kendaraan dilaporkan mogok, sementara petugas gabungan dikerahkan untuk melakukan penyedotan air. BMKG mengimbau masyarakat untuk tetap waspada terhadap potensi hujan lebat yang diperkirakan berlangsung hingga akhir bulan.

Sementara itu, di Jawa Tengah dan Jawa Barat angin puting beliung dilaporkan memporak-porandakan puluhan rumah. Pemerintah daerah telah menyalurkan bantuan darurat dan menyiagakan posko penampungan sementara. Para ahli mengingatkan bahwa fenomena cuaca ekstrem seperti ini berpotensi menjadi lebih sering terjadi di masa depan akibat perubahan iklim global."
            ],

            [
                "title" => "Harga Beras Melonjak Tajam, Pemerintah Siapkan Langkah Stabilitas Pasar",
                "content" => "
Harga beras kembali mengalami kenaikan signifikan di berbagai pasar tradisional dan ritel modern di Indonesia. Kenaikan ini dipicu oleh penurunan produksi akibat musim kemarau panjang serta meningkatnya biaya distribusi. Data Badan Pangan Nasional menunjukkan bahwa harga beras medium naik rata-rata 12 persen dalam dua minggu terakhir.

Sejumlah pedagang mengaku kesulitan mendapatkan pasokan dari distributor, sementara konsumen mulai mengeluhkan harga yang semakin tidak terjangkau. Pemerintah melalui Badan Urusan Logistik (Bulog) menyiapkan skema operasi pasar untuk menekan kenaikan harga dan memastikan ketersediaan pasokan terjaga.

Para analis ekonomi menilai bahwa kenaikan harga pangan yang berkepanjangan dapat berdampak pada inflasi nasional. Oleh sebab itu, pemerintah diminta bergerak cepat untuk mengantisipasi dampaknya terhadap kelompok masyarakat berpenghasilan rendah."
            ],

            [
                "title" => "Timnas Indonesia Cetak Sejarah Usai Melaju ke Babak Perempat Final Piala Asia",
                "content" => "
Tim nasional Indonesia mencatat sejarah baru dengan lolos ke babak perempat final Piala Asia untuk pertama kalinya. Kemenangan 2-1 atas Jepang pada laga penentuan membuat para penggemar sepak bola tanah air merayakan pencapaian besar tersebut. Gol kemenangan dicetak melalui serangan balik cepat pada menit-menit akhir pertandingan.

Pelatih timnas mengapresiasi kerja keras seluruh pemain yang menunjukkan determinasi tinggi di lapangan. Menurutnya, keberhasilan ini merupakan buah dari pengembangan program pembinaan pemain muda serta peningkatan kualitas kompetisi domestik.

Suasana meriah terjadi di berbagai kota besar Indonesia, termasuk Jakarta dan Surabaya, di mana ribuan suporter turun ke jalan merayakan kemenangan. Pemerintah menyatakan dukungan penuh dan berharap prestasi ini menjadi momentum kebangkitan sepak bola nasional."
            ],

            [
                "title" => "Startup Teknologi Lokal Raup Pendanaan Rp120 Miliar dari Investor Internasional",
                "content" => "
Sebuah startup teknologi Indonesia yang bergerak di sektor kecerdasan buatan (AI) berhasil memperoleh pendanaan seri A senilai Rp120 miliar dari konsorsium investor internasional. Pendanaan ini bertujuan mempercepat pengembangan platform mereka yang fokus pada otomasi proses bisnis dan analitik data.

CEO perusahaan tersebut mengungkapkan bahwa dana segar ini akan digunakan untuk memperkuat tim engineering, memperluas jaringan layanan di Asia Tenggara, serta meningkatkan kapasitas komputasi. Investor menilai startup ini memiliki potensi besar untuk menjadi pemain utama di kawasan karena teknologinya yang kompetitif.

Ekosistem startup Indonesia disebut semakin menarik bagi investor asing, terutama pada sektor teknologi yang berorientasi masa depan seperti AI, fintech, dan energi terbarukan."
            ],

            [
                "title" => "Perkembangan AI Global Mengubah Pola Kerja dan Industri di Indonesia",
                "content" => "
Teknologi kecerdasan buatan (AI) terus berkembang pesat dan mulai memberikan dampak signifikan terhadap berbagai sektor industri di Indonesia. Perusahaan manufaktur, logistik, dan perbankan kini memanfaatkan AI untuk meningkatkan efisiensi dan menekan biaya operasional. Namun di sisi lain, ada kekhawatiran mengenai hilangnya lapangan kerja konvensional.

Peneliti teknologi dari beberapa universitas ternama menekankan bahwa transformasi digital tidak dapat dihindari. Oleh karena itu, tenaga kerja Indonesia harus beradaptasi dengan meningkatkan kompetensi digital dan kemampuan analisis data. Pemerintah melalui Kementerian Kominfo telah menyiapkan berbagai program pelatihan AI untuk menjawab tantangan ini.

Meski memunculkan sejumlah risiko, penerapan AI juga membuka peluang besar dalam penciptaan profesi baru dan bisnis inovatif. Para ahli sepakat bahwa Indonesia harus memanfaatkan momentum ini untuk memperkuat daya saing global."
            ],

            [
                "title" => "Festival Budaya Nusantara Mendapat Sorotan Dunia Internasional",
                "content" => "
Festival Budaya Nusantara yang digelar di Yogyakarta tahun ini berhasil menarik perhatian media internasional. Acara tersebut menampilkan ratusan pertunjukan seni dari berbagai daerah, mulai dari tari tradisional, musik etnik, hingga pameran kerajinan tangan.

Para wisatawan mancanegara memuji kekayaan budaya Indonesia yang dianggap unik dan memiliki nilai artistik tinggi. Sejumlah negara bahkan menyampaikan minat untuk menjalin kerja sama kebudayaan di masa mendatang. Pemerintah menilai festival ini sebagai sarana diplomasi budaya yang efektif untuk memperkuat citra Indonesia di mata dunia.

Tahun ini, jumlah pengunjung meningkat 40 persen dibandingkan tahun sebelumnya, menunjukkan antusiasme publik terhadap pelestarian budaya lokal."
            ],

            [
                "title" => "Kemacetan Parah di Ibu Kota Pecahkan Rekor Baru",
                "content" => "
Kemacetan lalu lintas di Jakarta kembali mencapai titik kritis pada akhir pekan lalu. Data dari aplikasi pemantau mobilitas menunjukkan tingkat kemacetan mencapai 78 persen, tertinggi sepanjang tahun. Penyebab utama kemacetan adalah peningkatan volume kendaraan, pekerjaan perawatan jalan, serta cuaca buruk.

Beberapa jalur utama seperti Sudirman, Thamrin, dan Gatot Subroto mengalami antrean kendaraan hingga berkilometer. Warga mengeluhkan waktu tempuh yang berlipat ganda, sementara transportasi umum juga mengalami keterlambatan.

Pemerintah berencana mempercepat implementasi rekayasa lalu lintas dan memperluas kebijakan ganjil genap. Di sisi lain, para pakar transportasi menilai bahwa solusi jangka panjang harus mencakup integrasi transportasi publik dan pengendalian pertumbuhan kendaraan pribadi."
            ],

            [
                "title" => "Kasus Keamanan Siber Nasional Mengalami Lonjakan Signifikan",
                "content" => "
Laporan terbaru dari Badan Siber dan Sandi Negara (BSSN) mencatat peningkatan drastis dalam jumlah serangan siber yang menargetkan institusi pemerintah dan perusahaan swasta. Jenis serangan yang paling banyak terjadi meliputi phishing, ransomware, dan pencurian data sensitif.

Pakar keamanan siber mengingatkan bahwa masih banyak institusi yang belum menerapkan standar keamanan digital yang memadai. Kurangnya edukasi dan lemahnya perlindungan infrastruktur menjadi pintu masuk yang mudah bagi para pelaku kejahatan siber.

Pemerintah sedang menyiapkan peraturan baru yang bertujuan memperkuat keamanan data nasional dan meningkatkan kewajiban perusahaan dalam melindungi informasi pengguna."
            ],

            [
                "title" => "Film Indonesia Berhasil Masuk Nominasi Penghargaan Internasional Bergengsi",
                "content" => "
Industri perfilman Indonesia kembali mencetak prestasi membanggakan setelah sebuah film karya sutradara muda tanah air berhasil masuk nominasi penghargaan internasional bergengsi. Film tersebut dinilai memiliki kekuatan visual dan naratif yang kuat serta berhasil menonjolkan isu sosial dengan cara yang elegan.

Para kritikus film memuji keberanian sutradara dalam mengeksplorasi tema-tema kompleks yang jarang diangkat dalam perfilman lokal. Pemerintah dan komunitas perfilman berharap prestasi ini dapat memicu semangat sineas muda untuk terus berkarya.

Penayangan film ini di berbagai festival dunia menunjukkan bahwa karya Indonesia semakin diakui dalam kancah global."
            ],

            [
                "title" => "Aplikasi Baru Viral di TikTok, Pengguna Bertambah Jutaan Hanya Dalam Seminggu",
                "content" => "
Sebuah aplikasi baru berbasis video pendek mendadak viral di TikTok dan menarik jutaan pengguna baru hanya dalam waktu satu minggu. Aplikasi ini menawarkan fitur pengeditan unik dan efek visual yang belum tersedia di platform lain, membuatnya cepat populer di kalangan kreator konten.

Fenomena ini mencuri perhatian investor teknologi global yang menilai aplikasi tersebut memiliki potensi besar menjadi tren jangka panjang. Di Indonesia, jumlah unduhan melonjak drastis terutama di kalangan remaja dan mahasiswa.

Namun sejumlah pakar mengingatkan pentingnya regulasi dan perlindungan data mengingat maraknya aplikasi digital yang mengumpulkan informasi pengguna dalam jumlah besar."
            ],
        ];

        foreach ($viralNews as $news) {
            News::create([
                'user_id' => $users->random()->id,
                'title' => $news["title"],
                'slug' => Str::slug($news["title"]) . '-' . Str::random(5),
                'content' => trim($news["content"]),
                'thumbnail' => null,
                'published_at' => now(),
            ]);
        }
    }
}
