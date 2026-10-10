@extends('layouts.app')

@section('title', $article->meta_title ?: $article->title)
@section('description', $article->seoDescription())
@section('og_image', $article->imageUrl())
@section('og_type', 'article')
@section('canonical', route('articles.show', $article))

@push('meta')
    <meta property="article:published_time" content="{{ $article->published_at->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $article->updated_at->toIso8601String() }}">
@endpush

@push('schema')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $article->title,
    'description' => $article->seoDescription(),
    'image' => [$article->imageUrl()],
    'datePublished' => $article->published_at->toIso8601String(),
    'dateModified' => $article->updated_at->toIso8601String(),
    'author' => ['@type' => 'Organization', 'name' => setting('site_name')],
    'publisher' => ['@id' => url('/').'#organization'],
    'mainEntityOfPage' => route('articles.show', $article),
    'inLanguage' => 'id-ID',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<article>
    <header class="container-x max-w-4xl pt-8 sm:pt-12">
        <x-breadcrumb :items="[['label' => 'Artikel', 'url' => route('articles.index')], ['label' => $article->title]]" />
        <h1 class="mt-6 font-display text-3xl leading-tight font-semibold text-forest-900 sm:text-5xl">{{ $article->title }}</h1>
        <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-forest-900/60">
            <span class="flex items-center gap-2"><img src="{{ asset('images/logo.png') }}" alt="" class="size-7 rounded-full bg-white">Tim {{ setting('site_name') }}</span>
            <span class="flex items-center gap-1.5"><x-icon name="calendar" class="size-4" /><time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('d F Y') }}</time></span>
            <span class="flex items-center gap-1.5"><x-icon name="clock" class="size-4" />{{ $article->readingTime() }} menit baca</span>
        </div>
    </header>

    <div class="container-x mt-8 max-w-5xl">
        <img src="{{ $article->imageUrl() }}" alt="{{ $article->title }}" width="1200" height="675" fetchpriority="high" class="aspect-[16/9] w-full rounded-[2rem] object-cover shadow-lg">
    </div>

    <div class="container-x mt-10 grid grid-cols-1 max-w-6xl gap-12 lg:grid-cols-[1fr_300px]">
        <div class="mx-auto w-full max-w-3xl">
            @if ($article->excerpt)
                <p class="border-l-4 border-gold-400 pl-5 text-lg font-medium text-forest-900/80">{{ $article->excerpt }}</p>
            @endif
            <div class="prose-herbal mt-6">{!! $article->body !!}</div>

            <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-forest-900/10 pt-6">
                <span class="text-sm font-semibold text-forest-900">Bagikan:</span>
                <a href="https://wa.me/?text={{ rawurlencode($article->title.' '.route('articles.show', $article)) }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full bg-[#25D366] text-white" aria-label="Bagikan ke WhatsApp"><x-icon name="whatsapp" class="size-4" /></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('articles.show', $article)) }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full bg-[#1877F2] text-white" aria-label="Bagikan ke Facebook"><x-icon name="facebook" class="size-4" /></a>
                <button type="button" x-data @click="navigator.clipboard.writeText(location.href); $store.toast.show('Tautan disalin')" class="rounded-full bg-forest-100 px-4 py-2.5 text-sm font-semibold text-forest-800">Salin tautan</button>
            </div>
        </div>

        <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
            @foreach ($products as $p)
                <a href="{{ route('products.show', $p) }}" class="card group flex gap-4 p-4 transition hover:shadow-lg">
                    <img src="{{ $p->imageUrl() }}" alt="{{ $p->name }}" width="80" height="80" loading="lazy" class="size-20 rounded-2xl bg-forest-50 object-contain">
                    <div>
                        <p class="text-xs font-bold text-forest-500 uppercase">Produk Rekomendasi</p>
                        <p class="mt-1 text-sm font-bold text-forest-900 group-hover:text-forest-700">{{ $p->name }}</p>
                        <p class="mt-1 text-sm font-extrabold text-forest-800">{{ rupiah($p->finalPrice()) }}</p>
                    </div>
                </a>
            @endforeach
            <div class="rounded-3xl bg-forest-800 p-6 text-white" x-data="newsletter('article')">
                <h2 class="text-xl font-semibold">Dapatkan artikel terbaru</h2>
                <p class="mt-1 text-sm text-forest-100/80">Langsung di email Anda, gratis.</p>
                <form @submit.prevent="submit" class="mt-4 space-y-2">
                    <label for="side-email" class="sr-only">Email</label>
                    <input id="side-email" x-model="email" type="email" required placeholder="nama@email.com" class="w-full rounded-full border-0 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-forest-100/50 focus:ring-2 focus:ring-gold-300">
                    <button class="btn-gold w-full" :disabled="loading">Berlangganan</button>
                </form>
            </div>
        </aside>
    </div>
</article>

@if ($latest->isNotEmpty())
<section class="container-x mt-20">
    <h2 class="section-title !text-2xl sm:!text-3xl">Artikel Lainnya</h2>
    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($latest as $article)
            @include('articles._card')
        @endforeach
    </div>
</section>
@endif
@endsection
