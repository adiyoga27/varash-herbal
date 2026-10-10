@extends('layouts.admin')

@section('title', $article->exists ? 'Edit Artikel' : 'Tulis Artikel')

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}" class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    @csrf
    @if ($article->exists) @method('PUT') @endif
    <div class="space-y-6 xl:col-span-2">
        <section class="a-card space-y-5 p-5 sm:p-6">
            <div><label for="title" class="a-label">Judul *</label><input id="title" name="title" value="{{ old('title', $article->title) }}" required class="a-input !text-base font-semibold"></div>
            <div><label for="slug" class="a-label">Slug URL</label><input id="slug" name="slug" value="{{ old('slug', $article->slug) }}" placeholder="otomatis dari judul" class="a-input"></div>
            <div><label for="excerpt" class="a-label">Ringkasan</label><textarea id="excerpt" name="excerpt" rows="2" class="a-input">{{ old('excerpt', $article->excerpt) }}</textarea></div>
            <div>
                <label for="body" class="a-label">Isi Artikel * <span class="font-normal text-slate-400">— HTML: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;, &lt;a&gt;, &lt;img&gt;</span></label>
                <textarea id="body" name="body" rows="18" required class="a-input font-mono text-xs leading-relaxed">{{ old('body', $article->body) }}</textarea>
            </div>
        </section>
        <section class="a-card space-y-5 p-5 sm:p-6">
            <h2 class="font-bold text-slate-900">SEO</h2>
            <div x-data="counter(@js(old('meta_title', $article->meta_title)), 70)">
                <label for="meta_title" class="a-label flex justify-between">Meta Title <span class="text-xs font-normal text-slate-400" x-text="left + ' tersisa'"></span></label>
                <input id="meta_title" name="meta_title" x-model="value" maxlength="70" class="a-input">
            </div>
            <div x-data="counter(@js(old('meta_description', $article->meta_description)), 160)">
                <label for="meta_description" class="a-label flex justify-between">Meta Description <span class="text-xs font-normal text-slate-400" x-text="left + ' tersisa'"></span></label>
                <textarea id="meta_description" name="meta_description" x-model="value" rows="2" class="a-input"></textarea>
            </div>
        </section>
    </div>
    <div class="space-y-6">
        <section class="a-card space-y-4 p-5 sm:p-6">
            <h2 class="font-bold text-slate-900">Publikasi</h2>
            <x-admin.toggle name="is_published" :checked="old('is_published', $article->is_published)" label="Terbitkan" />
            <div><label for="published_at" class="a-label">Tanggal Terbit</label><input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i')) }}" class="a-input"></div>
            @unless ($article->exists)<x-admin.toggle name="notify_subscribers" label="Kirim ke subscriber" />@endunless
            <div class="flex gap-2 border-t border-slate-100 pt-4">
                <a href="{{ route('admin.articles.index') }}" class="a-btn-light flex-1">Batal</a>
                <button class="a-btn-primary flex-1">Simpan</button>
            </div>
        </section>
        <section class="a-card space-y-3 p-5 sm:p-6" x-data="imagePreview(@js($article->exists ? $article->imageUrl() : null))">
            <h2 class="font-bold text-slate-900">Gambar Utama</h2>
            <label class="relative flex aspect-video cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 hover:border-forest-400">
                <img x-show="preview" :src="preview" alt="" class="absolute inset-0 size-full object-cover">
                <span x-show="!preview" class="text-center text-sm text-slate-400"><x-icon name="image" class="mx-auto size-8" /> Klik untuk unggah</span>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="pick">
            </label>
            <p class="text-xs text-slate-500">Rasio 16:9, maks 2MB.</p>
        </section>
    </div>
</form>
@endsection
