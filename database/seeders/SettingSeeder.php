<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'Varash Herbal',
            'site_tagline' => 'Sehat Tanpa Minum Obat',
            'company_name' => 'PT. Varash Saddan Nusantara',
            'about_short' => 'Minyak herbal dengan saripati rempah asli pertama di Indonesia. Diracik dari 108 tanaman berkhasiat warisan leluhur Bali, terdaftar BPOM & bersertifikat Halal MUI.',
            'whatsapp_number' => '6282210810856',
            'whatsapp_admin_name' => 'Admin Varash',
            'phone' => '0822-1081-0856',
            'email' => 'info@varashherbal.com',
            'address' => 'Jl. Ahmad Yani Utara, Sriti Residence No 88, Denpasar Utara, Bali',
            'business_hours' => 'Senin – Sabtu, 08.00 – 20.00 WITA',
            'maps_embed' => 'https://maps.google.com/maps?q=Sriti%20Residence%20Denpasar%20Utara%20Bali&t=&z=15&ie=UTF8&iwloc=&output=embed',
            'checkout_note' => 'Setelah menekan tombol "Pesan via WhatsApp", detail pesanan akan otomatis terkirim ke admin kami. Ongkos kirim & nomor rekening akan dikonfirmasi oleh admin melalui WhatsApp.',
            'hero_title' => 'Sehat Alami, Tanpa Ketergantungan Obat Kimia',
            'hero_subtitle' => 'Minyak balur herbal dari 108 tanaman berkhasiat warisan Nusantara. Cukup dioles, langsung meresap melalui kulit — praktis, aman untuk seluruh keluarga.',
            'stat_sold' => '1 Juta+',
            'stat_customers' => '250 Ribu+',
            'stat_herbs' => '108',
            'instagram' => 'https://www.instagram.com/varashsaddannusantara',
            'facebook' => 'https://www.facebook.com/profile.php?id=61578141444082',
            'tiktok' => 'https://www.tiktok.com/@varashsaddannusantara',
            'youtube' => 'https://youtube.com/c/VarashSaddanNusantara',
            'meta_title' => 'Varash Herbal — Minyak Herbal Alami 108 Tanaman Berkhasiat | BPOM & Halal',
            'meta_description' => 'Toko resmi produk herbal Varash: Varash Classic, Varash Natural Oil, Avara, suplemen & perawatan alami. 100% original, BPOM & Halal MUI. Pesan mudah via WhatsApp.',
            'meta_keywords' => 'minyak varash, varash classic, minyak herbal, obat herbal alami, minyak balur, herbal bali, produk herbal bpom',
            'google_site_verification' => '',
            'google_analytics_id' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
