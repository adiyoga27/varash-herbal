@extends('layouts.app')

@section('title', 'Artikel Kesehatan & Herbal')
@section('description', 'Kumpulan artikel tips hidup sehat alami, edukasi tanaman herbal Nusantara, dan panduan pemakaian produk Varash.')
@section('canonical', route('articles.index'))
@if ($search || $articles->currentPage() > 1)
    @section('robots', 'noindex, follow')
@endif

@section('content')
<x-page-hero title="Artikel Kesehatan" :eyebrow="'Blog '.setting('site_name')" subtitle="Tips hidup sehat alami, edukasi herbal Nusantara, dan panduan pemakaian produk." :crumbs="[['label' => 'Artikel']]" />

<section class="container-x py-10 sm:py-14">
    <form method="GET" class="mb-8 flex max-w-md gap-2">
        <label for="aq" class="sr-only">Cari artikel</label>
        <input id="aq" type="search" name="q" value="{{ $search }}" placeholder="Cari artikel…" class="input !rounded-full">
        <button class="btn-primary shrink-0">Cari</button>
    </form>

    @if ($articles->isEmpty())
        <p class="card p-10 text-center text-forest-900/60">Belum ada artikel{{ $search ? ' untuk pencarian ini' : '' }}.</p>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($articles as $article)
                @include('articles._card')
            @endforeach
        </div>
        <div class="mt-12">{{ $articles->links() }}</div>
    @endif
</section>
@endsection
