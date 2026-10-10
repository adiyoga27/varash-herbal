@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan')
@section('robots', 'noindex, follow')

@section('content')
<section class="container-x flex min-h-[60vh] flex-col items-center justify-center py-20 text-center">
    <p class="font-display text-8xl font-semibold text-forest-200">404</p>
    <h1 class="mt-4 text-3xl font-semibold text-forest-900">Halaman tidak ditemukan</h1>
    <p class="mt-2 max-w-md text-forest-900/60">Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.</p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a href="{{ route('home') }}" class="btn-primary">Ke Beranda</a>
        <a href="{{ route('products.index') }}" class="btn-outline">Lihat Produk</a>
    </div>
</section>
@endsection
