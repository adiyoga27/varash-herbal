<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Minyak Herbal', 'icon' => 'drop', 'description' => 'Minyak balur herbal dari saripati rempah asli Nusantara untuk dioles di kulit.'],
            ['name' => 'Suplemen Herbal', 'icon' => 'capsule', 'description' => 'Kapsul herbal untuk membantu memelihara daya tahan dan kesehatan tubuh.'],
            ['name' => 'Minuman Herbal', 'icon' => 'cup', 'description' => 'Minuman herbal siap saji dari ekstrak rempah pilihan.'],
            ['name' => 'Perawatan Tubuh', 'icon' => 'leaf', 'description' => 'Perawatan wajah, kulit, dan rambut dengan bahan-bahan alami.'],
            ['name' => 'Paket Agen & Reseller', 'icon' => 'box', 'description' => 'Paket hemat untuk member, agen, dan reseller resmi Varash.'],
        ])->mapWithKeys(function ($c, $i) {
            $category = Category::updateOrCreate(['slug' => Str::slug($c['name'])], $c + ['sort_order' => $i + 1, 'is_active' => true]);

            return [$c['name'] => $category->id];
        });

        foreach ($this->products() as $i => $p) {
            $slug = Str::slug($p['name']);
            $image = $p['image'] ?? $slug;

            $source = database_path("seeders/assets/{$image}.webp");
            $path = "products/{$image}.webp";
            if (is_file($source) && ! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, file_get_contents($source));
            }

            Product::updateOrCreate(['slug' => $slug], [
                'category_id' => $categories[$p['category']],
                'name' => $p['name'],
                'sku' => $p['sku'],
                'size' => $p['size'] ?? null,
                'short_description' => $p['short'],
                'description' => $p['description'],
                'ingredients' => $p['ingredients'] ?? null,
                'benefits' => $p['benefits'],
                'advantages' => $p['advantages'],
                'usage' => $p['usage'],
                'price' => $p['price'],
                'sale_price' => $p['sale_price'] ?? null,
                'stock' => $p['stock'] ?? 100,
                'weight' => $p['weight'] ?? 200,
                'image' => $path,
                'is_featured' => $p['featured'] ?? false,
                'is_active' => true,
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function products(): array
    {
        $balur = [
            'Wajib balurkan pada telapak kaki dan sela-sela jari kaki',
            'Wajib balurkan di sepanjang tulang belakang (pangkal kepala sampai tulang ekor)',
            'Balurkan pada bagian tubuh yang terasa sakit atau tidak nyaman',
            'Minum 1–2 gelas air putih hangat setelah pembaluran untuk membantu proses detoksifikasi',
            'Gunakan rutin pagi (setelah mandi) dan malam (sebelum tidur)',
        ];

        $careOil = [
            'category' => 'Minyak Herbal',
            'ingredients' => 'Minyak Pala, Minyak Cengkeh, Minyak Zaitun, Minyak Kayu Putih, Minyak Eucalyptus',
            'benefits' => [
                'Menghangatkan tubuh',
                'Membantu meredakan nyeri sendi dan kram otot',
                'Membantu meredakan nyeri haid dan sakit perut',
                'Membantu meredakan pegal di punggung dan lutut',
                'Membantu meredakan mabuk perjalanan',
                'Membantu meredakan pusing, masuk angin, mual dan keluhan ringan mendadak lainnya',
            ],
            'advantages' => ['100% herbal', 'Aroma rempah yang khas', 'Praktis dan mudah dibawa ke mana saja', 'Harga terjangkau'],
            'usage' => [
                'Oleskan pada area kening, pelipis, dan leher',
                'Oleskan pada area tubuh yang terasa tidak nyaman',
                'Hirup aromanya untuk efek relaksasi',
            ],
            'weight' => 100,
        ];

        return [
            [
                'name' => 'Varash Classic Natural Oil',
                'sku' => 'VRS-CLS-100',
                'category' => 'Minyak Herbal',
                'size' => '100 ml',
                'price' => 395000,
                'featured' => true,
                'short' => 'Minyak balur premium dari 108 tanaman berkhasiat dengan 6 unsur pohon kehidupan. Saripati lebih pekat untuk keluhan yang sudah lama dirasakan.',
                'description' => '<p><strong>Varash Classic Natural Oil</strong> adalah varian premium minyak Varash yang diracik secara tradisional di Tabanan, Bali, dari <strong>108 tanaman berkhasiat obat</strong>. Ramuannya memadukan 6 unsur pohon kehidupan — akar, batang, biji, buah, daun, dan bunga — sesuai kearifan pengobatan tradisional Nusantara.</p><p>Dibandingkan Varash Natural Oil, Varash Classic memiliki saripati yang lebih pekat sehingga lebih cepat merangsang titik-titik meridian tubuh. Cocok digunakan secara rutin oleh anak-anak hingga orang dewasa sebagai pendamping gaya hidup sehat.</p><p>Bekerja secara <em>transdermal</em>: cukup dibalurkan pada kulit, kandungan herbal meresap tanpa melalui sistem pencernaan sehingga nyaman bagi lambung.</p>',
                'ingredients' => "Bunga: Cengkeh, Lawang, Sandat Bali, Tapak Dara, Rosella, Kamboja, Melati, Teratai, Sedap Malam, Lavender\nDaun: Asem, Pepaya, Lidah Buaya, Jambu Biji, Binahong, Kumis Kucing, Kemangi, Tapak Liman, Pecut Kuda, Waru, Sirsak, Sibukan\nBuah: Pinang, Vanili, Jarak, Jeruk Nipis, Pala, Mahoni, Kemiri, Manjakani, Mengkudu, Cabai Jawa, Cabai Keriting\nBiji: Jinten Putih, Jinten Hitam, Adas, Ketumbar, Merica Hitam, Merica Putih, Pulasari, Kemukus, Halba, Rosemary\nBatang: Bidara Laut, Cengkeh, Serai, Kayu Manis, Pule, Manggis, Mesui, Kepundung, Jeruk, Jeruk Bali, Sekucang, Kerangean\nAkar: Akar Wangi, Kencur, Jangu, Pinang, Pepaya, Pohon Aren, Lengkuas, Jahe Bali, Jahe Merah, Jahe Gajah, Kunyit, Kunyit Putih",
                'benefits' => [
                    'Membantu meredakan pegal linu, nyeri sendi, dan otot kaku',
                    'Membantu meredakan keluhan yang sudah lama dirasakan',
                    'Membantu meredakan keluhan psikosomatis akibat stres dan kelelahan',
                    'Membantu menjaga kesehatan dan daya tahan tubuh secara menyeluruh',
                    'Memberikan rasa hangat dan rileks dengan aromaterapi rempah',
                ],
                'advantages' => [
                    'Saripati 5× lebih pekat dibanding Varash Natural Oil',
                    'Dapat digunakan anak-anak hingga dewasa',
                    'Aroma rempah memberikan efek relaksasi & aromaterapi',
                    'Terbuat dari 108 tanaman rempah berkhasiat',
                    'Terdaftar BPOM & bersertifikat Halal MUI',
                    '100% asli buatan Indonesia',
                ],
                'usage' => $balur,
            ],
            [
                'name' => 'Varash Natural Oil',
                'sku' => 'VRS-NAT-60',
                'category' => 'Minyak Herbal',
                'size' => '60 ml',
                'price' => 149000,
                'featured' => true,
                'short' => 'Minyak balur herbal praktis dengan botol pump yang membantu tubuh lebih rileks, nyaman, dan tidur lebih nyenyak.',
                'description' => '<p><strong>Varash Natural Oil</strong> hadir sebagai minyak balur herbal praktis yang membantu tubuh lebih rileks, nyaman, dan tenang setelah beraktivitas seharian. Cocok digunakan saat badan terasa berat, pikiran sulit tenang, hingga mengalami gangguan tidur.</p><p>Perpaduan aroma herbal alami dan sensasi hangat yang nyaman membantu tubuh masuk ke fase istirahat dengan lebih rileks, sehingga tidur terasa lebih nyenyak dan kualitas istirahat lebih terjaga.</p>',
                'ingredients' => 'Ekstrak 108 tanaman herbal Bali dalam basis minyak nabati.',
                'benefits' => [
                    'Membantu memberikan rasa nyaman pada tubuh yang pegal',
                    'Membantu tubuh lebih rileks dan tenang',
                    'Membantu menjaga kualitas tidur agar lebih nyenyak',
                    'Membantu menjaga stamina dan daya tahan tubuh',
                    'Cocok untuk aktivitas harian dan relaksasi tubuh',
                ],
                'advantages' => ['Praktis dengan botol pump', 'Mudah dibawa bepergian', 'Aroma herbal yang nyaman', 'Cepat menyerap di kulit', 'Terdaftar BPOM & Halal'],
                'usage' => $balur,
                'weight' => 120,
            ],
            [
                'name' => 'Avara Natural Oil',
                'sku' => 'VRS-AVR',
                'category' => 'Minyak Herbal',
                'price' => 275000,
                'featured' => true,
                'short' => 'Minyak herbal khusus si kecil untuk meredakan kembung & masuk angin, memberi kehangatan, dan membantu tidur lebih nyenyak.',
                'description' => '<p><strong>Avara Natural Oil</strong> adalah minyak herbal yang diformulasikan khusus untuk si kecil. Mengandung minyak kayu putih, adas, cengkeh, citronella, lavender, dan minyak kelapa yang lembut di kulit.</p><p>Aroma rempahnya memberikan efek relaksasi sekaligus membantu melindungi si kecil dari gigitan nyamuk — teman setia para ibu di rumah maupun saat bepergian.</p>',
                'ingredients' => 'Minyak Kayu Putih, Minyak Adas, Minyak Cengkeh, Minyak Citronella, Minyak Lavender, Minyak Kelapa',
                'benefits' => [
                    'Membantu meredakan perut kembung dan masuk angin',
                    'Memberikan kehangatan dan kenyamanan untuk si kecil',
                    'Membantu melindungi si kecil dari gigitan nyamuk',
                    'Membantu meningkatkan kualitas tidur si kecil',
                ],
                'advantages' => ['Minyak khusus si kecil', 'Aroma rempah untuk relaksasi & aromaterapi', 'Bahan herbal tanaman berkhasiat', 'BPOM & Halal', '100% produk asli Indonesia'],
                'usage' => ['Balurkan ke seluruh tubuh si kecil (punggung, perut, kaki, dan tangan)', 'Gunakan setelah mandi dan sebelum tidur'],
            ],
            array_merge($careOil, [
                'name' => 'Varash Care Hot Natural Oil',
                'sku' => 'VRS-CARE-HOT',
                'price' => 55000,
                'featured' => true,
                'short' => 'Minyak gosok herbal sensasi hangat ekstra untuk pertolongan pertama keluhan sehari-hari. Praktis dibawa ke mana saja.',
                'description' => '<p><strong>Varash Care Hot Natural Oil</strong> adalah minyak gosok herbal dengan sensasi hangat ekstra. Perpaduan minyak pala, cengkeh, zaitun, kayu putih, dan eucalyptus membuatnya ideal sebagai pertolongan pertama untuk keluhan ringan sehari-hari.</p><p>Kemasannya ringkas sehingga mudah diselipkan di tas, laci kantor, atau dashboard kendaraan.</p>',
            ]),
            array_merge($careOil, [
                'name' => 'Varash Care Natural Oil',
                'sku' => 'VRS-CARE',
                'price' => 50000,
                'short' => 'Minyak gosok herbal serbaguna untuk menghangatkan tubuh dan meredakan pegal, pusing, serta masuk angin.',
                'description' => '<p><strong>Varash Care Natural Oil</strong> adalah minyak gosok herbal serbaguna dengan aroma khas rempah. Mengandung minyak pala, cengkeh, zaitun, kayu putih, dan eucalyptus yang membantu menghangatkan tubuh dan memberi rasa nyaman.</p>',
            ]),
            [
                'name' => 'Xamplus Multivitamin',
                'sku' => 'VRS-XMP',
                'category' => 'Suplemen Herbal',
                'price' => 150000,
                'featured' => true,
                'short' => 'Suplemen herbal pilihan untuk membantu memelihara dan meningkatkan daya tahan tubuh.',
                'description' => '<p><strong>Xamplus</strong> merupakan suplemen yang menjadi pilihan tepat untuk membantu memelihara dan meningkatkan daya tahan tubuh. Diformulasikan dari ekstrak herbal pilihan dalam kemasan kapsul yang praktis.</p>',
                'benefits' => ['Membantu memelihara daya tahan tubuh', 'Mendukung kebugaran saat aktivitas padat'],
                'advantages' => ['Kemasan kapsul praktis', 'Ekstrak herbal pilihan', 'Terdaftar BPOM'],
                'usage' => ['Minum 3 kali sehari 1 kapsul setelah makan'],
                'weight' => 100,
            ],
            [
                'name' => 'Karistia Kapsul Haid',
                'sku' => 'VRS-KRS',
                'featured' => true,
                'category' => 'Suplemen Herbal',
                'price' => 150000,
                'short' => 'Kapsul herbal untuk membantu melancarkan dan meredakan nyeri saat datang bulan.',
                'description' => '<p><strong>Karistia</strong> merupakan kapsul herbal untuk membantu meredakan nyeri saat datang bulan. Diracik dari kunyit, asam jawa, kayu manis, kencur, klabet, dan nanas — ramuan jamu warisan perempuan Nusantara.</p>',
                'ingredients' => 'Kunyit, Asam Jawa, Kayu Manis, Kencur, Klabet, Nanas',
                'benefits' => ['Membantu melancarkan datang bulan', 'Membantu meredakan nyeri haid', 'Membantu mengurangi bau badan', 'Menyegarkan badan'],
                'advantages' => ['Kemasan praktis', 'Ekstrak herbal pilihan', 'BPOM & Halal'],
                'usage' => ['Minum 2 kapsul, 2–3 kali sehari saat datang bulan'],
                'weight' => 100,
            ],
            [
                'name' => 'Matanaya Vitamin Mata',
                'sku' => 'VRS-MTN',
                'category' => 'Suplemen Herbal',
                'price' => 150000,
                'short' => 'Suplemen kesehatan mata dari 5 ekstrak terbaik: bilberry, wortel, bit, tomat, dan pegagan. Kaya beta karoten.',
                'description' => '<p><strong>MataNaya</strong> merupakan suplemen kesehatan mata yang terbuat dari 5 ekstrak terbaik untuk mata, yaitu tomat, bilberry, bit, wortel, dan pegagan. Cocok untuk Anda yang banyak menatap layar gawai setiap hari.</p>',
                'ingredients' => "Vaccinium Myrtillus Fructus Extract (Bilberry)\nDaucus Carota Tuber Extract (Wortel)\nBeta Vulgaris Tuber Extract (Bit)\nSolanum Lycopersicum Fructus Extract (Tomat)\nCentella Asiatica Herba Extract (Pegagan)",
                'benefits' => [
                    'Membantu memelihara kesehatan mata',
                    'Membantu melindungi sel-sel mata dari kerusakan',
                    'Membantu melancarkan sirkulasi darah ke mata',
                    'Membantu meningkatkan daya tahan mata',
                    'Membantu mencegah mata kering',
                ],
                'advantages' => ['Mengandung beta karoten', '5 ekstrak terbaik untuk mata', 'BPOM & Halal'],
                'usage' => ['Minum 2 kapsul, 2–3 kali sehari'],
                'weight' => 100,
            ],
            [
                'name' => 'Amozaa',
                'sku' => 'VRS-AMZ',
                'category' => 'Minuman Herbal',
                'price' => 155000,
                'featured' => true,
                'short' => 'Minuman herbal dari jahe, kunyit, kencur, bit, mint, sambiloto, habbatussauda & madu untuk membantu meredakan gangguan lambung.',
                'description' => '<p><strong>Amozaa</strong> memadukan bahan-bahan herbal seperti jahe, kunyit, kencur, buah bit, daun mint, sambiloto, habbatussauda, dan madu menjadi minuman herbal yang membantu meredakan masalah lambung.</p>',
                'ingredients' => 'Jahe, Kunyit, Kencur, Buah Bit, Daun Mint, Sambiloto, Habbatussauda, Madu',
                'benefits' => [
                    'Membantu meredakan gangguan lambung',
                    'Membantu mengurangi sakit perut, kembung, dan mual',
                    'Membantu proses detoksifikasi tubuh',
                    'Membantu meningkatkan daya tahan tubuh',
                ],
                'advantages' => ['Kemasan botol praktis', 'Ekstrak herbal pilihan', 'Terdaftar BPOM'],
                'usage' => ['Diminum 3 kali sehari, 1 botol', 'Dapat diminum langsung atau dicampur 100 ml air hangat'],
                'weight' => 500,
            ],
            [
                'name' => 'Kenzen',
                'sku' => 'VRS-KNZ',
                'category' => 'Minuman Herbal',
                'price' => 125000,
                'short' => 'Minuman energi herbal dari purwoceng, pasak bumi, ginseng, kopi & epimedium untuk membantu memelihara stamina pria.',
                'description' => '<p><strong>Kenzen</strong> merupakan minuman energi herbal yang membantu memelihara stamina pria dewasa. Mengandung ekstrak purwoceng, pasak bumi, ginseng, kopi, dan epimedium dalam sachet 20 gram yang praktis diseduh.</p>',
                'ingredients' => 'Ekstrak Purwoceng, Ekstrak Pasak Bumi, Ekstrak Ginseng, Ekstrak Kopi, Ekstrak Epimedium',
                'benefits' => ['Membantu memelihara stamina pria', 'Membantu menjaga kebugaran saat beraktivitas'],
                'advantages' => ['5 ekstrak terbaik untuk stamina', 'Sachet praktis 20 gram', 'BPOM & Halal'],
                'usage' => ['Masukkan 1 sachet (20 gram) ke dalam gelas', 'Tambahkan 150 ml air panas lalu aduk rata', 'Diminum 2 kali sehari setelah makan atau bila perlu'],
                'weight' => 250,
            ],
            [
                'name' => 'Black Mayva Natural Soap',
                'sku' => 'VRS-BMV',
                'category' => 'Perawatan Tubuh',
                'price' => 145000,
                'short' => 'Sabun wajah alami dengan charcoal, jojoba, argan, zaitun, lavender, almond, serai wangi & vitamin E.',
                'description' => '<p><strong>Black Mayva Natural Soap</strong> merupakan perpaduan bahan alami — charcoal, jojoba, minyak argan, minyak zaitun, lavender, almond, serai wangi, dan vitamin E — yang membantu merawat kulit wajah agar tetap bersih dan lembap.</p>',
                'ingredients' => 'Charcoal, Jojoba, Minyak Argan, Minyak Zaitun, Minyak Lavender, Minyak Almond, Minyak Serai Wangi, Vitamin E',
                'benefits' => ['Membersihkan wajah secara alami', 'Membantu menjaga kelembapan kulit', 'Membantu mengangkat kotoran dan minyak berlebih'],
                'advantages' => ['Terbuat dari bahan alami', 'Diperkaya vitamin E', 'Aroma lembut menenangkan'],
                'usage' => ['Basahi wajah dengan air bersih', 'Gosokkan sabun di kedua tangan hingga berbusa', 'Usapkan busa merata pada wajah dan leher', 'Bilas dengan air bersih'],
                'weight' => 150,
            ],
            [
                'name' => 'Svatish Hair Oil',
                'sku' => 'VRS-SVT',
                'featured' => true,
                'category' => 'Perawatan Tubuh',
                'price' => 195000,
                'short' => 'Minyak rambut alami untuk menjaga kekuatan rambut, mencegah kusut & bercabang, serta membuat rambut sehat berkilau.',
                'description' => '<p><strong>Svatish Hair Oil</strong> diramu dari bahan-bahan alami yang memberikan perawatan maksimal bagi rambut untuk membantu mengatasi berbagai masalah rambut.</p>',
                'benefits' => ['Menjaga kekuatan rambut', 'Mencegah rambut kusut dan bercabang', 'Merawat kondisi rambut', 'Membuat rambut tampak sehat berkilau', 'Memberikan keharuman pada rambut'],
                'advantages' => ['Terbuat dari bahan alami', 'Kemasan spray praktis'],
                'usage' => ['Semprotkan pada rambut dengan jarak 10–15 cm sebelum keramas', 'Ratakan dengan jari dari batang hingga ujung rambut', 'Diamkan 1–2 jam, lalu bilas hingga bersih'],
                'weight' => 150,
            ],
            [
                'name' => 'Paket Reseller Varash Natural Oil',
                'sku' => 'VRS-PKT-NAT',
                'category' => 'Paket Agen & Reseller',
                'size' => '15 × 100 ml',
                'price' => 3750000,
                'sale_price' => 2950000,
                'image' => 'varash-natural-oil',
                'short' => 'Paket member isi 15 botol Varash Natural Oil 100 ml + buku panduan. Harga jual kembali Rp 250.000/botol.',
                'description' => '<p>Mulai usaha herbal Anda bersama Varash! <strong>Paket Reseller Varash Natural Oil</strong> berisi 15 botol Varash Natural Oil 100 ml beserta buku panduan pemakaian.</p><p>Dengan harga jual kembali yang disarankan Rp 250.000 per botol, Anda berkesempatan memperoleh keuntungan sekaligus membantu lebih banyak keluarga hidup sehat alami. Daftar sebagai member melalui WhatsApp admin kami.</p>',
                'benefits' => ['Harga spesial member/agen', 'Margin keuntungan menarik', 'Dukungan materi promosi & edukasi produk'],
                'advantages' => ['Isi 15 botol @100 ml', 'Bonus buku panduan', 'Produk 100% original langsung dari pabrik'],
                'usage' => ['Hubungi admin untuk pendaftaran member', 'Pelajari buku panduan sebelum memasarkan'],
                'weight' => 2500,
            ],
            [
                'name' => 'Paket Reseller Varash Classic',
                'sku' => 'VRS-PKT-CLS',
                'category' => 'Paket Agen & Reseller',
                'size' => '10 × 100 ml',
                'price' => 3550000,
                'sale_price' => 2850000,
                'image' => 'varash-classic-natural-oil',
                'short' => 'Paket member isi 10 botol Varash Classic 100 ml + buku panduan. Harga jual kembali Rp 355.000/botol.',
                'description' => '<p><strong>Paket Reseller Varash Classic</strong> berisi 10 botol Varash Classic Natural Oil 100 ml beserta buku panduan. Varian premium dengan saripati lebih pekat — produk favorit pelanggan untuk keluhan yang sudah lama dirasakan.</p>',
                'benefits' => ['Harga spesial member/agen', 'Margin keuntungan menarik', 'Dukungan materi promosi & edukasi produk'],
                'advantages' => ['Isi 10 botol @100 ml', 'Bonus buku panduan', 'Produk 100% original langsung dari pabrik'],
                'usage' => ['Hubungi admin untuk pendaftaran member', 'Pelajari buku panduan sebelum memasarkan'],
                'weight' => 1800,
            ],
        ];
    }
}
