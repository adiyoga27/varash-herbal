# Herbal Asli Indonesia — Toko Online Produk Herbal (Laravel 13 + Blade)

Website toko **Herbal Asli Indonesia** untuk produk herbal Varash yang SEO-friendly, responsif di semua perangkat, dengan checkout langsung ke WhatsApp, newsletter subscriber, notifikasi admin, dan activity log lengkap.

## Fitur

**Website (publik)**
- Beranda, katalog produk (filter kategori, pencarian, urutan), detail produk dengan tab manfaat/cara pakai/komposisi
- Artikel/blog, Tentang Kami, FAQ, Kontak, Kebijakan Privasi, Lacak Pesanan
- Keranjang berbasis session (tanpa login), tambah ke keranjang via AJAX
- **Checkout ke WhatsApp**: pesanan disimpan di database, lalu pelanggan diarahkan ke `wa.me` dengan pesan berisi nomor pesanan, data pemesan, alamat, rincian produk, dan total
- Newsletter subscribe (email sambutan + tautan berhenti berlangganan)
- SEO: meta title/description per halaman, canonical, Open Graph & Twitter Card, JSON-LD (Organization, WebSite+SearchAction, Product+Offer+Review, BreadcrumbList, BlogPosting, FAQPage, ItemList), `sitemap.xml` dinamis dengan gambar, `robots.txt`, `noindex` untuk halaman filter, keranjang, dan checkout, gambar WebP dengan lazy loading
- Desain modern dan mobile-first: sticky CTA di halaman produk (mobile), drawer menu, tombol WhatsApp mengambang

**Panel Admin (`/admin`)**, responsif dengan sidebar off-canvas di mobile
- Dashboard: pendapatan, grafik 14 hari, produk terlaris, stok menipis, aktivitas terbaru
- CRUD Produk (upload gambar, harga promo, SEO, soft delete/restore), Kategori, Artikel (sanitasi HTML), Testimoni, FAQ
- Pesanan: filter status/tanggal, update status dan ongkir, chat pelanggan via WA, riwayat perubahan, export CSV
- Subscriber (export CSV) dan Newsletter (kirim ke semua subscriber aktif via queue). Opsi "kirim notifikasi ke subscriber" saat menambah produk atau artikel baru
- Notifikasi admin (lonceng + polling 30 detik): pesanan baru, subscriber baru, pesan kontak
- Pengguna & role (Super Admin / Admin / Editor), profil dan ganti password
- Pengaturan website: nomor WhatsApp penerima pesanan, kontak, sosial media, teks hero, SEO, Google Analytics

**Log System**
- **Activity Log**: setiap create/update/delete model tercatat otomatis beserta nilai *sebelum → sesudah*. Login, logout, login gagal, lockout, checkout, pembukaan WhatsApp, subscribe/unsubscribe, pengiriman newsletter, export, dan error server (500) juga dicatat. Setiap entri menyimpan IP, perangkat/browser, method, dan URL
- Filter per modul, event, level, pengguna, dan tanggal; halaman detail diff; export CSV; pembersihan log lama (manual + otomatis bulanan)
- **System Log**: penampil file `storage/logs/*.log` (level, stack trace) khusus Super Admin

## Instalasi

```bash
composer install
npm install
cp .env.example .env        # lalu sesuaikan APP_URL, DB_*, MAIL_*
php artisan key:generate
php artisan storage:link
php artisan migrate --seed  # data produk, artikel, FAQ, testimoni & admin awal
npm run build
php artisan serve
```

Mode pengembangan (server + queue + vite sekaligus): `composer run dev`

**Login admin awal:** `admin@varashherbal.com` / `password`. Segera ganti password di menu **Profil**.

## Konfigurasi penting

| Kebutuhan | Cara |
|---|---|
| Nomor WhatsApp penerima pesanan | Admin → Pengaturan → Kontak & Checkout |
| Email newsletter terkirim sungguhan | Atur `MAIL_MAILER=smtp` + kredensial di `.env` (default `log` hanya menulis ke `storage/logs`) |
| Queue untuk email/newsletter | Jalankan `php artisan queue:work` (atau supervisor di server) |
| Scheduler (pembersihan log bulanan) | Cron: `* * * * * php /path/artisan schedule:run` |
| Database MySQL | Ubah `DB_CONNECTION=mysql` dan `DB_*` di `.env`, lalu `php artisan migrate --seed` |
| Zona waktu | `APP_TIMEZONE=Asia/Makassar` (WITA) di `.env` |

## Testing

```bash
php artisan test
```

21 feature test mencakup: render semua halaman publik dan admin, markup SEO, sitemap/robots, alur checkout ke WhatsApp (isi pesan & pencatatan log), validasi checkout, privasi halaman sukses, subscribe/unsubscribe, notifikasi admin, logging login dan diff perubahan produk, hak akses role, pengiriman newsletter, sanitasi XSS artikel, dan export CSV.

## Catatan konten

Konten produk diambil dari varashsaddannusantara.com, warisanherbalnusantara.com, dan varashofficial.com. Klaim kesehatan sengaja ditulis dengan bahasa "membantu…", dan disclaimer medis ditampilkan di footer serta halaman produk. Klaim penyakit berat (kanker, stroke, dan sejenisnya) dari situs sumber tidak disalin karena berisiko terhadap regulasi iklan obat tradisional BPOM. Sebelum go-live, mohon tim memverifikasi harga, nomor BPOM, dan izin penggunaan nama di testimoni.
