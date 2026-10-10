<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->testimonials();
        $this->faqs();
        $this->articles();
    }

    private function testimonials(): void
    {
        $classic = Product::where('slug', 'varash-classic-natural-oil')->value('id');
        $natural = Product::where('slug', 'varash-natural-oil')->value('id');

        $items = [
            ['Komang Budi Wiastini', 'Denpasar', $natural, 'Punggung saya sering pegal dan perut kiri bawah terasa tidak nyaman. Setelah rutin membalur Minyak Varash pagi dan malam, badan terasa jauh lebih ringan dan nyaman.'],
            ['Wyn Yudhiantara', 'Gianyar', $natural, 'Waktu batuk pilek dan napas terasa berat, saya balurkan Varash di dada, punggung, dan telapak kaki. Rasanya hangat dan lega, tidur pun jadi lebih nyenyak.'],
            ['Jero Puspa', 'Tabanan', $classic, 'Saya sering tegang di kepala, kram, dan kesemutan. Sejak pakai Varash Classic rutin, keluhan itu sudah jarang muncul. Aromanya juga menenangkan.'],
            ['Jeanne Louise Prasetya', 'Surabaya', $natural, 'Alergi dingin saya dulu sering kambuh. Sekarang jauh lebih jarang, dan kalau sedang flu pemulihannya terasa lebih cepat. Varash selalu ada di tas saya.'],
            ['I Gede Sudarsana', 'Badung', $classic, 'Asam lambung saya sering naik. Setelah rutin membalur Varash Classic dan menjaga pola makan, perut terasa lebih nyaman. Terima kasih Varash!'],
            ['Aisyah', 'Mataram', $classic, 'Lutut yang sering nyeri sekarang lebih enak dipakai jalan. Saya pakai Varash sekeluarga, anak-anak juga cocok.'],
        ];

        foreach ($items as [$name, $location, $productId, $content]) {
            Testimonial::updateOrCreate(['name' => $name], [
                'location' => $location,
                'product_id' => $productId,
                'content' => $content,
                'rating' => 5,
                'is_active' => true,
            ]);
        }
    }

    private function faqs(): void
    {
        $faqs = [
            ['Apakah produk Varash sudah terdaftar BPOM dan Halal?', 'Ya. Produk Varash diproduksi dengan standar CPOTB/GMP, terdaftar di BPOM, dan telah bersertifikat Halal MUI. Nomor registrasi tercantum pada kemasan setiap produk.'],
            ['Bagaimana cara memesan?', 'Pilih produk, masukkan ke keranjang, lalu isi data pengiriman di halaman checkout. Setelah menekan "Pesan via WhatsApp", detail pesanan otomatis terkirim ke admin kami untuk dikonfirmasi ongkir dan pembayarannya.'],
            ['Apa bedanya Varash Classic dan Varash Natural Oil?', 'Keduanya berasal dari 108 tanaman herbal. Varash Classic memiliki saripati yang lebih pekat (sekitar 5× lebih kuat) dan umumnya dipilih untuk keluhan yang sudah lama dirasakan, sedangkan Varash Natural Oil cocok untuk perawatan harian dan relaksasi.'],
            ['Apakah minyak Varash boleh diminum?', 'Tidak. Minyak Varash adalah minyak balur untuk pemakaian luar. Cukup dibalurkan pada kulit — terutama telapak kaki dan sepanjang tulang belakang — agar kandungan herbalnya meresap melalui kulit.'],
            ['Apakah aman untuk anak-anak dan ibu hamil?', 'Varash Classic dapat digunakan anak-anak hingga dewasa, dan tersedia Avara Natural Oil yang diformulasikan khusus untuk si kecil. Untuk ibu hamil atau kondisi medis tertentu, kami sarankan berkonsultasi dengan dokter terlebih dahulu.'],
            ['Metode pembayaran apa saja yang tersedia?', 'Kami menerima transfer bank, e-wallet/QRIS, dan COD untuk area tertentu. Detail pembayaran akan diinformasikan admin melalui WhatsApp.'],
            ['Berapa lama pengiriman?', 'Pesanan diproses di hari yang sama bila dikonfirmasi sebelum pukul 15.00 WITA. Estimasi tiba 1–3 hari untuk Bali & Jawa, 3–7 hari untuk luar pulau, tergantung ekspedisi.'],
            ['Bagaimana cara menjadi agen atau reseller?', 'Pilih Paket Agen & Reseller di halaman produk atau hubungi admin via WhatsApp. Anda akan mendapatkan harga spesial member, buku panduan, dan dukungan materi promosi.'],
        ];

        foreach ($faqs as $i => [$q, $a]) {
            Faq::updateOrCreate(['question' => $q], ['answer' => $a, 'sort_order' => $i + 1, 'is_active' => true]);
        }
    }

    private function articles(): void
    {
        $author = User::first()?->id;

        $articles = [
            [
                'title' => 'Cara Membedakan Minyak Varash Classic Asli dan Palsu',
                'image' => '/images/bpom.webp',
                'excerpt' => 'Maraknya produk tiruan membuat Anda perlu jeli. Kenali ciri-ciri Varash Classic original agar manfaatnya optimal dan uang Anda tidak sia-sia.',
                'body' => '<p>Popularitas minyak Varash membuat banyak pihak tidak bertanggung jawab menjual produk tiruan. Selain tidak memberikan manfaat yang sama, produk palsu berisiko menimbulkan iritasi karena bahan dan proses pembuatannya tidak terjamin.</p><h2>1. Periksa nomor BPOM dan logo Halal</h2><p>Produk Varash original mencantumkan nomor registrasi BPOM dan logo Halal MUI pada kemasan. Anda dapat mengecek keasliannya di situs <a href="https://cekbpom.pom.go.id" target="_blank" rel="noopener">cekbpom.pom.go.id</a>.</p><h2>2. Perhatikan aroma dan warna</h2><p>Varash Classic memiliki aroma rempah yang kuat namun menenangkan, dengan warna minyak yang pekat karena saripati 108 tanaman. Produk palsu biasanya beraroma tajam seperti minyak sintetis.</p><h2>3. Beli dari toko dan agen resmi</h2><p>Cara paling aman adalah membeli langsung melalui website resmi atau agen terverifikasi. Harga yang jauh di bawah harga resmi patut dicurigai.</p><h2>4. Cek kualitas kemasan</h2><p>Kemasan original dicetak rapi, segel utuh, dan tutup terpasang dengan baik. Hindari produk dengan label buram atau segel rusak.</p><p>Jika ragu, hubungi admin kami melalui WhatsApp untuk verifikasi keaslian produk.</p>',
            ],
            [
                'title' => 'Cara Pakai Minyak Balur Varash yang Benar agar Hasil Maksimal',
                'image' => '/images/cara-pakai-1.webp',
                'excerpt' => 'Minyak Varash bekerja melalui kulit (transdermal). Ikuti langkah pembaluran yang tepat agar kandungan herbal terserap optimal.',
                'body' => '<p>Minyak Varash dirancang untuk diserap melalui kulit, sehingga tidak melewati sistem pencernaan. Agar hasilnya maksimal, cara pembaluran sangat menentukan.</p><h2>Titik wajib: telapak kaki dan tulang belakang</h2><p>Telapak kaki memiliki banyak titik refleksi, sedangkan sepanjang tulang belakang — dari pangkal kepala hingga tulang ekor — adalah jalur saraf utama tubuh. Balurkan secukupnya lalu pijat lembut hingga meresap.</p><h2>Area keluhan</h2><p>Setelah titik wajib, balurkan minyak pada area yang terasa tidak nyaman. Untuk keluhan ringan, ulangi setiap 2–3 jam. Untuk keluhan yang sudah lama, pembaluran dapat dilakukan lebih sering.</p><h2>Jangan lupa air putih hangat</h2><p>Minum 1–2 gelas air putih hangat sebelum atau sesudah pembaluran untuk membantu proses detoksifikasi alami tubuh.</p><h2>Waktu terbaik</h2><ul><li>Pagi hari setelah mandi</li><li>Malam hari sebelum tidur</li></ul><p>Lakukan secara rutin dan konsisten, dibarengi pola makan sehat serta istirahat yang cukup.</p>',
            ],
            [
                'title' => 'Mengenal 108 Tanaman Berkhasiat dalam Minyak Varash',
                'image' => '/images/rempah.webp',
                'excerpt' => 'Akar, batang, biji, buah, daun, dan bunga — inilah filosofi 6 unsur pohon kehidupan di balik racikan minyak Varash dari Bali.',
                'body' => '<p>Minyak Varash diracik di Tabanan, Bali, dengan memadukan 108 tanaman berkhasiat. Angka 108 dipilih bukan tanpa alasan: dalam tradisi pengobatan Nusantara, keseimbangan tubuh dicapai dengan menghadirkan seluruh unsur kehidupan.</p><h2>Filosofi 6 unsur pohon kehidupan</h2><ul><li><strong>Akar</strong> — jahe merah, kunyit, kencur, lengkuas, akar wangi</li><li><strong>Batang</strong> — kayu manis, serai, pule, bidara laut</li><li><strong>Biji</strong> — jinten hitam, adas, ketumbar, merica</li><li><strong>Buah</strong> — pala, kemiri, mengkudu, cabai jawa</li><li><strong>Daun</strong> — sirsak, binahong, kumis kucing, kemangi</li><li><strong>Bunga</strong> — cengkeh, sandat Bali, rosella, melati, lavender</li></ul><h2>Kekayaan rempah Nusantara</h2><p>Indonesia memiliki ribuan jenis tanaman obat yang telah digunakan turun-temurun. Varash berupaya menghadirkan kearifan ini dalam bentuk modern yang praktis, tetap mengikuti standar produksi BPOM dan Halal.</p>',
            ],
            [
                'title' => '5 Kebiasaan Sederhana untuk Tidur Lebih Nyenyak Secara Alami',
                'image' => '/images/cara-pakai-3.webp',
                'excerpt' => 'Sulit tidur bisa menurunkan daya tahan tubuh. Coba lima kebiasaan alami ini untuk kualitas istirahat yang lebih baik.',
                'body' => '<p>Tidur berkualitas adalah fondasi kesehatan. Berikut lima kebiasaan sederhana yang bisa Anda mulai malam ini.</p><h2>1. Jadwal tidur yang konsisten</h2><p>Tidur dan bangun di jam yang sama setiap hari membantu menstabilkan jam biologis tubuh.</p><h2>2. Batasi layar 1 jam sebelum tidur</h2><p>Cahaya biru dari gawai dapat menghambat hormon melatonin. Ganti dengan membaca buku atau peregangan ringan.</p><h2>3. Pijat relaksasi dengan minyak herbal</h2><p>Membalur telapak kaki dan punggung dengan minyak herbal beraroma rempah seperti Varash Natural Oil membantu tubuh lebih rileks sebelum tidur.</p><h2>4. Hindari kafein di sore hari</h2><p>Kopi dan teh pekat sebaiknya dikonsumsi sebelum pukul 14.00.</p><h2>5. Ciptakan kamar yang nyaman</h2><p>Suhu sejuk, pencahayaan redup, dan suasana tenang membuat tubuh lebih mudah terlelap.</p>',
            ],
        ];

        foreach ($articles as $i => $a) {
            Article::updateOrCreate(['slug' => Str::slug($a['title'])], $a + [
                'user_id' => $author,
                'is_published' => true,
                'published_at' => now()->subDays(($i + 1) * 5),
            ]);
        }
    }
}
