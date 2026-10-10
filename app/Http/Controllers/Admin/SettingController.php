<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public const FIELDS = [
        'Umum' => [
            'site_name' => ['Nama Website', 'text'],
            'site_tagline' => ['Tagline', 'text'],
            'company_name' => ['Nama Perusahaan', 'text'],
            'about_short' => ['Deskripsi Singkat (footer)', 'textarea'],
        ],
        'Kontak & Checkout' => [
            'whatsapp_number' => ['Nomor WhatsApp Penerima Pesanan', 'text', 'Format: 6282210810856 atau 0822-1081-0856'],
            'whatsapp_admin_name' => ['Nama Admin WhatsApp', 'text'],
            'phone' => ['Telepon', 'text'],
            'email' => ['Email', 'email'],
            'address' => ['Alamat', 'textarea'],
            'business_hours' => ['Jam Operasional', 'text'],
            'maps_embed' => ['URL Embed Google Maps', 'text'],
            'checkout_note' => ['Catatan di Halaman Checkout', 'textarea'],
        ],
        'Hero Beranda' => [
            'hero_title' => ['Judul Hero', 'text'],
            'hero_subtitle' => ['Sub-judul Hero', 'textarea'],
            'stat_sold' => ['Statistik: Botol Terjual', 'text'],
            'stat_customers' => ['Statistik: Pelanggan', 'text'],
            'stat_herbs' => ['Statistik: Jumlah Tanaman', 'text'],
        ],
        'Media Sosial' => [
            'instagram' => ['Instagram URL', 'url'],
            'facebook' => ['Facebook URL', 'url'],
            'tiktok' => ['TikTok URL', 'url'],
            'youtube' => ['YouTube URL', 'url'],
        ],
        'SEO' => [
            'meta_title' => ['Meta Title Default', 'text'],
            'meta_description' => ['Meta Description Default', 'textarea'],
            'meta_keywords' => ['Meta Keywords', 'text'],
            'google_site_verification' => ['Google Site Verification', 'text'],
            'google_analytics_id' => ['Google Analytics ID (G-XXXX)', 'text'],
        ],
    ];

    public function edit()
    {
        return view('admin.settings', ['groups' => self::FIELDS, 'values' => Setting::allCached()]);
    }

    public function update(Request $request)
    {
        $keys = collect(self::FIELDS)->flatMap(fn ($fields) => array_keys($fields));
        $rules = $keys->mapWithKeys(fn ($k) => [$k => ['nullable', 'string', 'max:2000']])->all();
        $data = $request->validate($rules);

        // Setiap perubahan dicatat oleh trait LogsActivity pada model Setting
        foreach ($keys as $key) {
            $value = $data[$key] ?? null;
            if ((string) Setting::get($key) !== (string) $value) {
                Setting::put($key, $value);
            }
        }

        return back()->with('success', 'Pengaturan disimpan.');
    }
}
