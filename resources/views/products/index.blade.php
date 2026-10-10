@extends('layouts.app')

@php
    $title = $currentCategory?->name ?? ($search ? "Hasil pencarian \"{$search}\"" : 'Semua Produk Herbal');
    $sorts = ['' => 'Rekomendasi', 'popular' => 'Terpopuler', 'newest' => 'Terbaru', 'price_asc' => 'Harga Terendah', 'price_desc' => 'Harga Tertinggi'];
@endphp

@section('title', $currentCategory ? $currentCategory->name.' Original' : 'Katalog Produk Herbal Original')
@section('description', $currentCategory?->description ?: 'Belanja produk herbal Varash original: minyak balur 108 tanaman, suplemen, minuman herbal, dan perawatan alami. BPOM & Halal, pesan mudah via WhatsApp.')
@if ($search || request('sort') || $products->currentPage() > 1)
    @section('robots', 'noindex, follow')
@endif
@section('canonical', $currentCategory ? route('products.category', $currentCategory) : route('products.index'))

@push('schema')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => $title,
    'itemListElement' => $products->values()->map(fn ($p, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'url' => route('products.show', $p), 'name' => $p->name])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<x-page-hero :title="$title" :eyebrow="setting('site_name')"
    :subtitle="$currentCategory?->description ?? 'Produk herbal original langsung dari pabrik — terdaftar BPOM & bersertifikat Halal MUI.'"
    :crumbs="array_filter([['label' => 'Produk', 'url' => route('products.index')], $currentCategory ? ['label' => $currentCategory->name] : null])" />

<section class="container-x py-8 sm:py-12">
    {{-- Filter bar --}}
    <div class="sticky top-16 z-20 -mx-4 bg-cream/90 px-4 py-3 backdrop-blur lg:top-20 lg:mx-0 lg:px-0">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="no-scrollbar -mx-4 flex gap-2 overflow-x-auto px-4 lg:mx-0 lg:px-0">
                <a href="{{ route('products.index', array_filter(['q' => $search])) }}" @class(['shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-forest-700 text-white' => ! $currentCategory, 'bg-white text-forest-800 ring-1 ring-forest-900/10 hover:bg-forest-50' => $currentCategory])>Semua</a>
                @foreach ($categories as $cat)
                    <a href="{{ route('products.category', $cat) }}" @class(['shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition', 'bg-forest-700 text-white' => $currentCategory?->is($cat), 'bg-white text-forest-800 ring-1 ring-forest-900/10 hover:bg-forest-50' => ! $currentCategory?->is($cat)])>
                        {{ $cat->name }} <span class="opacity-60">({{ $cat->products_count }})</span>
                    </a>
                @endforeach
            </div>
            <form method="GET" class="flex gap-2" x-data>
                <label for="q" class="sr-only">Cari</label>
                <div class="relative flex-1 lg:w-64">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-forest-900/40" />
                    <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Cari produk…" class="input !rounded-full !py-2.5 !pl-10">
                </div>
                <label for="sort" class="sr-only">Urutkan</label>
                <select id="sort" name="sort" @change="$el.form.submit()" class="input !w-auto !rounded-full !py-2.5">
                    @foreach ($sorts as $value => $label)
                        <option value="{{ $value }}" @selected(request('sort') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <p class="mt-4 text-sm text-forest-900/60">Menampilkan <strong class="text-forest-900">{{ $products->total() }}</strong> produk</p>

    @if ($products->isEmpty())
        <div class="card mt-6 flex flex-col items-center p-12 text-center">
            <span class="grid size-16 place-items-center rounded-full bg-forest-50 text-forest-400"><x-icon name="search" class="size-8" /></span>
            <h2 class="mt-4 text-xl font-semibold text-forest-900">Produk tidak ditemukan</h2>
            <p class="mt-1 text-forest-900/60">Coba kata kunci lain atau tanyakan langsung ke admin kami.</p>
            <a href="{{ route('products.index') }}" class="btn-primary mt-6">Lihat Semua Produk</a>
        </div>
    @else
        <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-5 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($products as $i => $product)
                <x-product-card :product="$product" :eager="$i < 4" />
            @endforeach
        </div>
        <div class="mt-12">{{ $products->links() }}</div>
    @endif
</section>
@endsection
