@extends('layouts.app')

@section('title', 'Kebijakan Privasi')
@section('description', 'Kebijakan privasi '.setting('site_name').': bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.')

@section('content')
<x-page-hero title="Kebijakan Privasi" :crumbs="[['label' => 'Kebijakan Privasi']]" />
<section class="container-x max-w-3xl py-12">
    <div class="prose-herbal">
        <p>{{ setting('company_name') }} menghargai privasi Anda. Halaman ini menjelaskan data apa yang kami kumpulkan dan bagaimana kami menggunakannya.</p>
        <h2>Data yang kami kumpulkan</h2>
        <ul>
            <li>Nama, nomor WhatsApp, email, dan alamat pengiriman saat Anda melakukan pemesanan.</li>
            <li>Alamat email saat Anda berlangganan newsletter.</li>
            <li>Data teknis seperti alamat IP dan jenis peramban untuk keamanan dan pencegahan penyalahgunaan.</li>
        </ul>
        <h2>Penggunaan data</h2>
        <ul>
            <li>Memproses dan mengirimkan pesanan Anda.</li>
            <li>Menghubungi Anda terkait konfirmasi pesanan melalui WhatsApp.</li>
            <li>Mengirimkan newsletter, info produk, dan promo (hanya jika Anda berlangganan).</li>
        </ul>
        <h2>Berhenti berlangganan</h2>
        <p>Setiap email newsletter menyertakan tautan untuk berhenti berlangganan kapan saja.</p>
        <h2>Kontak</h2>
        <p>Pertanyaan terkait privasi dapat dikirim ke <a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a>.</p>
    </div>
</section>
@endsection
