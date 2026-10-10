@extends('layouts.app')

@section('title', 'Tentang Kami')
@section('description', 'Mengenal '.setting('company_name').' — produsen minyak herbal Varash dari 108 tanaman berkhasiat, diracik di Tabanan, Bali. Terdaftar BPOM, Halal MUI, dan standar GMP.')

@section('content')
<x-page-hero title="Menghidupkan Kembali Pengobatan Tradisional Nusantara" eyebrow="Tentang Kami" :crumbs="[['label' => 'Tentang Kami']]"
    subtitle="Dari cinta untuk melayani — menghadirkan kesehatan alami yang aman dan teruji bagi keluarga Indonesia." />

<section class="container-x grid grid-cols-1 items-center gap-12 py-16 lg:grid-cols-2">
    <div class="reveal">
        <h2 class="section-title">Apa itu Minyak Varash?</h2>
        <div class="prose-herbal mt-4">
            <p>Minyak Varash adalah minyak terapi herbal yang terbuat dari ekstrak <strong>108 tanaman pilihan</strong> dan diproduksi secara tradisional di Tabanan, Bali. Kandungan herbalnya bekerja secara <em>transdermal</em> — meresap melalui kulit tanpa melewati sistem pencernaan, sehingga nyaman bagi lambung dan ginjal.</p>
            <p>Diproduksi oleh <strong>{{ setting('company_name') }}</strong> dengan standar <strong>GMP</strong>, terdaftar di <strong>BPOM</strong>, dan telah bersertifikat <strong>Halal MUI</strong>. Hingga hari ini, lebih dari {{ setting('stat_sold') }} botol telah dipercaya keluarga di seluruh Indonesia.</p>
        </div>
    </div>
    <img src="{{ asset('images/rempah.webp') }}" alt="Rempah-rempah bahan minyak Varash" width="800" height="696" loading="lazy" class="reveal w-full rounded-[2rem] shadow-xl">
</section>

<section class="bg-white py-16">
    <div class="container-x grid grid-cols-1 items-center gap-12 lg:grid-cols-5">
        <img src="{{ asset('images/peracik.webp') }}" alt="Peracik minyak Varash" width="640" height="800" loading="lazy" class="reveal mx-auto w-full max-w-sm rounded-[2rem] shadow-xl lg:col-span-2">
        <div class="reveal lg:col-span-3">
            <p class="eyebrow">Peracik Minyak Varash</p>
            <h2 class="section-title mt-3">Bapak Putu Eka Suryawan</h2>
            <p class="mt-2 font-semibold text-gold-600">20+ tahun pengalaman dalam pengobatan herbal</p>
            <div class="prose-herbal mt-4">
                <p>Berbekal pengalaman lebih dari dua dekade mempelajari dan mempraktikkan pengobatan herbal tradisional Bali, beliau meracik Varash dengan satu visi: <strong>menghidupkan kembali pengobatan tradisional Indonesia</strong> untuk penyembuhan yang alami, aman, dan teruji.</p>
                <blockquote>“Varash bukan sekadar ramuan. Ia adalah warisan pengetahuan leluhur yang kami jaga dan hadirkan untuk generasi kini.”</blockquote>
            </div>
        </div>
    </div>
</section>

<section class="container-x py-16">
    <div class="mx-auto max-w-2xl text-center">
        <p class="eyebrow">Visi & Misi</p>
        <h2 class="section-title mt-3">Membantu Keluarga Indonesia Hidup Lebih Sehat Secara Alami</h2>
    </div>
    <div class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-3">
        @foreach ([
            ['leaf', 'Alami', 'Memanfaatkan kekayaan alam Indonesia yang terbukti secara turun-temurun.'],
            ['shield', 'Aman & Teruji', 'Produksi berstandar GMP, terdaftar BPOM, dan bersertifikat Halal.'],
            ['heart', 'Melayani', 'Pendampingan & konsultasi gratis bagi setiap pelanggan dan agen.'],
        ] as [$icon, $title, $desc])
            <div class="card reveal p-8 text-center">
                <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-forest-100 text-forest-700"><x-icon :name="$icon" class="size-7" /></span>
                <h3 class="mt-5 font-sans text-xl font-bold text-forest-900">{{ $title }}</h3>
                <p class="mt-2 text-forest-900/60">{{ $desc }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="container-x">
    <div class="grid grid-cols-1 gap-6 rounded-[2rem] bg-forest-800 p-8 text-center text-white sm:grid-cols-3 sm:p-12">
        @foreach ([['stat_sold', 'Botol terjual'], ['stat_customers', 'Pelanggan'], ['stat_herbs', 'Tanaman herbal']] as [$k, $l])
            <div><p class="font-display text-4xl font-semibold text-gold-300 sm:text-5xl">{{ setting($k) }}</p><p class="mt-1 text-forest-100">{{ $l }}</p></div>
        @endforeach
    </div>
</section>
@endsection
