@extends('layouts.app')

@section('title', $product->seoTitle())
@section('description', $product->seoDescription())
@section('og_image', $product->imageUrl())
@section('og_type', 'product')
@section('canonical', route('products.show', $product))
@section('wa_float_class', 'max-lg:bottom-24')

@push('meta')
    <meta property="product:price:amount" content="{{ $product->finalPrice() }}">
    <meta property="product:price:currency" content="IDR">
    <meta property="product:availability" content="{{ $product->stock > 0 ? 'in stock' : 'out of stock' }}">
@endpush

@push('schema')
@php
    $reviews = $product->testimonials;
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'image' => [$product->imageUrl()],
        'description' => $product->seoDescription(),
        'sku' => $product->sku,
        'brand' => ['@type' => 'Brand', 'name' => 'Varash'],
        'category' => $product->category?->name,
        'offers' => [
            '@type' => 'Offer',
            'url' => route('products.show', $product),
            'priceCurrency' => 'IDR',
            'price' => $product->finalPrice(),
            'priceValidUntil' => now()->addYear()->toDateString(),
            'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
            'seller' => ['@id' => url('/').'#organization'],
        ],
    ];
    if ($reviews->isNotEmpty()) {
        $schema['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => round($reviews->avg('rating'), 1), 'reviewCount' => $reviews->count()];
        $schema['review'] = $reviews->take(5)->map(fn ($r) => [
            '@type' => 'Review', 'author' => ['@type' => 'Person', 'name' => $r->name],
            'reviewRating' => ['@type' => 'Rating', 'ratingValue' => $r->rating, 'bestRating' => 5], 'reviewBody' => $r->content,
        ])->values()->all();
    }
@endphp
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<div class="container-x pt-6 sm:pt-8">
    <x-breadcrumb :items="array_filter([
        ['label' => 'Produk', 'url' => route('products.index')],
        $product->category ? ['label' => $product->category->name, 'url' => route('products.category', $product->category)] : null,
        ['label' => $product->name],
    ])" />
</div>

<section class="container-x grid grid-cols-1 gap-8 py-6 sm:py-10 lg:grid-cols-2 lg:gap-14" x-data="addToCart({{ $product->id }})">
    {{-- Gambar --}}
    <div class="lg:sticky lg:top-28 lg:self-start">
        <div class="relative aspect-square overflow-hidden rounded-[2rem] bg-gradient-to-br from-forest-50 via-white to-gold-50 shadow-sm ring-1 ring-forest-900/5">
            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" width="800" height="800" fetchpriority="high" class="size-full object-contain p-6 sm:p-10">
            @if ($product->hasDiscount())
                <span class="absolute top-5 left-5 rounded-full bg-red-500 px-3 py-1.5 text-sm font-bold text-white">Hemat {{ $product->discountPercent() }}%</span>
            @endif
        </div>
        <div class="mt-4 grid grid-cols-3 gap-3 text-center">
            @foreach ([['shield', 'BPOM & Halal'], ['award', '100% Original'], ['truck', 'Kirim Cepat']] as [$icon, $label])
                <div class="rounded-2xl bg-white p-3 ring-1 ring-forest-900/5">
                    <x-icon :name="$icon" class="mx-auto size-5 text-forest-600" />
                    <p class="mt-1 text-xs font-semibold text-forest-900">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Info --}}
    <div>
        @if ($product->category)
            <a href="{{ route('products.category', $product->category) }}" class="eyebrow">{{ $product->category->name }}</a>
        @endif
        <h1 class="mt-3 font-display text-3xl leading-tight font-semibold text-forest-900 sm:text-4xl">{{ $product->name }}</h1>

        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
            <span class="flex items-center gap-1 text-gold-500">
                @for ($i = 0; $i < 5; $i++)<x-icon name="star" class="size-4 fill-current" />@endfor
                <span class="ml-1 font-semibold text-forest-900">{{ $product->testimonials->isNotEmpty() ? number_format($product->testimonials->avg('rating'), 1) : '5.0' }}</span>
            </span>
            @if ($product->size)<span class="text-forest-900/60">Isi: {{ $product->size }}</span>@endif
            <span @class(['font-semibold', 'text-forest-600' => $product->stock > 0, 'text-red-600' => $product->stock <= 0])>
                {{ $product->stock > 0 ? '● Stok tersedia' : '● Stok habis' }}
            </span>
        </div>

        <div class="mt-6 flex items-end gap-3">
            <p class="text-3xl font-extrabold text-forest-800 sm:text-4xl">{{ rupiah($product->finalPrice()) }}</p>
            @if ($product->hasDiscount())
                <p class="pb-1 text-lg text-forest-900/40 line-through">{{ rupiah($product->price) }}</p>
            @endif
        </div>

        @if ($product->short_description)
            <p class="mt-5 leading-relaxed text-forest-900/70">{{ $product->short_description }}</p>
        @endif

        {{-- Qty + CTA (desktop) --}}
        <div class="mt-8 hidden flex-wrap items-center gap-3 lg:flex">
            <div class="flex items-center rounded-full bg-white p-1 ring-1 ring-forest-900/10">
                <button type="button" @click="dec" class="grid size-10 place-items-center rounded-full hover:bg-forest-50" aria-label="Kurangi"><x-icon name="minus" class="size-4" /></button>
                <input type="number" x-model.number="qty" min="1" max="99" class="w-12 border-0 bg-transparent text-center font-bold focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none" aria-label="Jumlah">
                <button type="button" @click="inc" class="grid size-10 place-items-center rounded-full hover:bg-forest-50" aria-label="Tambah"><x-icon name="plus" class="size-4" /></button>
            </div>
            <button type="button" @click="add" :disabled="loading || {{ $product->stock > 0 ? 'false' : 'true' }}" class="btn-outline flex-1 !py-3.5"><x-icon name="cart" class="size-5" /> Tambah ke Keranjang</button>
            <form method="POST" action="{{ route('cart.add') }}" class="flex-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="qty" :value="qty">
                <input type="hidden" name="buy_now" value="1">
                <button class="btn-primary w-full !py-3.5" @disabled($product->stock <= 0)>Beli Sekarang</button>
            </form>
        </div>
        <a href="{{ wa_link("Halo Admin, saya ingin bertanya tentang produk {$product->name}") }}" target="_blank" rel="noopener" class="mt-3 hidden items-center gap-2 text-sm font-semibold text-forest-700 hover:text-forest-900 lg:inline-flex">
            <x-icon name="whatsapp" class="size-4 text-[#25D366]" /> Tanya produk ini via WhatsApp
        </a>

        {{-- Tabs detail --}}
        <div class="mt-10" x-data="{ tab: 'desc' }">
            <div class="no-scrollbar flex gap-1 overflow-x-auto rounded-full bg-white p-1 ring-1 ring-forest-900/5" role="tablist">
                @foreach (array_filter(['desc' => 'Deskripsi', 'benefit' => $product->benefits ? 'Manfaat' : null, 'usage' => $product->usage ? 'Cara Pakai' : null, 'ingredients' => $product->ingredients ? 'Komposisi' : null]) as $key => $label)
                    <button type="button" role="tab" @click="tab = '{{ $key }}'" :aria-selected="tab === '{{ $key }}'" :class="tab === '{{ $key }}' ? 'bg-forest-700 text-white shadow' : 'text-forest-900/70 hover:text-forest-900'" class="flex-1 shrink-0 rounded-full px-4 py-2.5 text-sm font-semibold whitespace-nowrap transition">{{ $label }}</button>
                @endforeach
            </div>

            <div class="mt-6">
                <div x-show="tab === 'desc'" role="tabpanel">
                    <div class="prose-herbal">{!! $product->description !!}</div>
                    @if ($product->advantages)
                        <h2 class="mt-8 font-sans text-lg font-bold text-forest-900">Keunggulan</h2>
                        <ul class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                            @foreach ($product->advantages as $adv)
                                <li class="flex gap-2.5 rounded-2xl bg-white p-3 text-sm ring-1 ring-forest-900/5"><x-icon name="check-circle" class="size-5 shrink-0 text-forest-600" />{{ $adv }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @if ($product->benefits)
                    <div x-cloak x-show="tab === 'benefit'" role="tabpanel">
                        <ul class="space-y-2.5">
                            @foreach ($product->benefits as $benefit)
                                <li class="flex gap-3 rounded-2xl bg-white p-4 ring-1 ring-forest-900/5"><span class="grid size-6 shrink-0 place-items-center rounded-full bg-forest-100 text-forest-700"><x-icon name="check" class="size-3.5" /></span><span class="text-forest-900/80">{{ $benefit }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if ($product->usage)
                    <div x-cloak x-show="tab === 'usage'" role="tabpanel">
                        <ol class="space-y-3">
                            @foreach ($product->usage as $i => $step)
                                <li class="flex gap-4"><span class="grid size-8 shrink-0 place-items-center rounded-full bg-gold-100 font-display font-semibold text-gold-700">{{ $i + 1 }}</span><span class="pt-1 text-forest-900/80">{{ $step }}</span></li>
                            @endforeach
                        </ol>
                    </div>
                @endif
                @if ($product->ingredients)
                    <div x-cloak x-show="tab === 'ingredients'" role="tabpanel" class="space-y-2">
                        @foreach (lines_to_array($product->ingredients) as $line)
                            <p class="rounded-2xl bg-white p-4 text-sm leading-relaxed text-forest-900/80 ring-1 ring-forest-900/5">{{ $line }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
            <p class="mt-6 flex gap-2 rounded-2xl bg-gold-50 p-4 text-xs leading-relaxed text-gold-700"><x-icon name="info" class="size-4 shrink-0" /> Produk herbal ini membantu menjaga kesehatan dan bukan pengganti diagnosis atau pengobatan dokter. Bila keluhan berlanjut, hubungi tenaga medis.</p>
        </div>
    </div>

    {{-- Sticky CTA mobile --}}
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-forest-900/10 bg-white/95 p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] backdrop-blur lg:hidden">
        <div class="flex items-center gap-2">
            <a href="{{ wa_link("Halo Admin, saya ingin bertanya tentang produk {$product->name}") }}" target="_blank" rel="noopener" class="grid size-12 shrink-0 place-items-center rounded-full bg-[#25D366]/10 text-[#1a9e4b]" aria-label="Tanya via WhatsApp"><x-icon name="whatsapp" class="size-6" /></a>
            <button type="button" @click="add" :disabled="loading" class="btn-outline flex-1 !px-3" @disabled($product->stock <= 0)><x-icon name="cart" class="size-5" /> Keranjang</button>
            <form method="POST" action="{{ route('cart.add') }}" class="flex-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="buy_now" value="1">
                <button class="btn-primary w-full !px-3" @disabled($product->stock <= 0)>Beli Sekarang</button>
            </form>
        </div>
    </div>
</section>

@if ($product->testimonials->isNotEmpty())
<section class="container-x py-10">
    <h2 class="section-title !text-2xl sm:!text-3xl">Ulasan Pelanggan</h2>
    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($product->testimonials->take(6) as $t)
            <figure class="card p-6">
                <div class="flex gap-0.5 text-gold-400">@for ($s = 0; $s < $t->rating; $s++)<x-icon name="star" class="size-4 fill-current" />@endfor</div>
                <blockquote class="mt-3 text-sm leading-relaxed text-forest-900/80">“{{ $t->content }}”</blockquote>
                <figcaption class="mt-4 text-sm font-bold text-forest-900">{{ $t->name }} <span class="font-normal text-forest-900/50">· {{ $t->location }}</span></figcaption>
            </figure>
        @endforeach
    </div>
</section>
@endif

@if ($related->isNotEmpty())
<section class="container-x py-10 pb-28 lg:pb-10">
    <div class="flex items-end justify-between">
        <h2 class="section-title !text-2xl sm:!text-3xl">Mungkin Anda Suka</h2>
        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-forest-700 hover:text-forest-900">Semua produk →</a>
    </div>
    <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
        @foreach ($related as $item)
            <x-product-card :product="$item" />
        @endforeach
    </div>
</section>
@endif
@endsection
