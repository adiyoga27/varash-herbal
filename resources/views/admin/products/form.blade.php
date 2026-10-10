@extends('layouts.admin')

@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    @csrf
    @if ($product->exists) @method('PUT') @endif

    <div class="space-y-6 xl:col-span-2">
        <section class="a-card space-y-5 p-5 sm:p-6">
            <h2 class="font-bold text-slate-900">Informasi Produk</h2>
            <div>
                <label for="name" class="a-label">Nama Produk *</label>
                <input id="name" name="name" value="{{ old('name', $product->name) }}" required class="a-input">
            </div>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div><label for="slug" class="a-label">Slug URL</label><input id="slug" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="otomatis dari nama" class="a-input"></div>
                <div><label for="sku" class="a-label">SKU</label><input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" class="a-input"></div>
                <div><label for="size" class="a-label">Ukuran / Isi</label><input id="size" name="size" value="{{ old('size', $product->size) }}" placeholder="100 ml" class="a-input"></div>
            </div>
            <div>
                <label for="short_description" class="a-label">Deskripsi Singkat</label>
                <textarea id="short_description" name="short_description" rows="2" class="a-input">{{ old('short_description', $product->short_description) }}</textarea>
            </div>
            <div>
                <label for="description" class="a-label">Deskripsi Lengkap <span class="font-normal text-slate-400">(HTML diperbolehkan: &lt;p&gt;, &lt;strong&gt;, &lt;ul&gt;…)</span></label>
                <textarea id="description" name="description" rows="8" class="a-input font-mono text-xs">{{ old('description', $product->description) }}</textarea>
            </div>
        </section>

        <section class="a-card space-y-5 p-5 sm:p-6">
            <h2 class="font-bold text-slate-900">Detail Produk <span class="text-sm font-normal text-slate-500">— satu item per baris</span></h2>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div><label for="benefits" class="a-label">Manfaat</label><textarea id="benefits" name="benefits" rows="6" class="a-input">{{ old('benefits', implode("\n", $product->benefits ?? [])) }}</textarea></div>
                <div><label for="advantages" class="a-label">Keunggulan</label><textarea id="advantages" name="advantages" rows="6" class="a-input">{{ old('advantages', implode("\n", $product->advantages ?? [])) }}</textarea></div>
                <div><label for="usage" class="a-label">Cara Pemakaian</label><textarea id="usage" name="usage" rows="6" class="a-input">{{ old('usage', implode("\n", $product->usage ?? [])) }}</textarea></div>
                <div><label for="ingredients" class="a-label">Komposisi</label><textarea id="ingredients" name="ingredients" rows="6" class="a-input">{{ old('ingredients', $product->ingredients) }}</textarea></div>
            </div>
        </section>

        <section class="a-card space-y-5 p-5 sm:p-6">
            <h2 class="font-bold text-slate-900">SEO</h2>
            <div x-data="counter(@js(old('meta_title', $product->meta_title)), 70)">
                <label for="meta_title" class="a-label flex justify-between">Meta Title <span class="text-xs font-normal" :class="left < 0 ? 'text-red-600' : 'text-slate-400'" x-text="left + ' karakter tersisa'"></span></label>
                <input id="meta_title" name="meta_title" x-model="value" maxlength="70" placeholder="Kosongkan untuk otomatis" class="a-input">
            </div>
            <div x-data="counter(@js(old('meta_description', $product->meta_description)), 160)">
                <label for="meta_description" class="a-label flex justify-between">Meta Description <span class="text-xs font-normal" :class="left < 0 ? 'text-red-600' : 'text-slate-400'" x-text="left + ' karakter tersisa'"></span></label>
                <textarea id="meta_description" name="meta_description" x-model="value" rows="2" placeholder="Kosongkan untuk otomatis dari deskripsi singkat" class="a-input"></textarea>
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="a-card space-y-4 p-5 sm:p-6">
            <h2 class="font-bold text-slate-900">Publikasi</h2>
            <x-admin.toggle name="is_active" :checked="old('is_active', $product->is_active)" label="Tampilkan di website" />
            <x-admin.toggle name="is_featured" :checked="old('is_featured', $product->is_featured)" label="Produk unggulan (beranda)" />
            @unless ($product->exists)
                <x-admin.toggle name="notify_subscribers" label="Kirim notifikasi ke subscriber" />
            @endunless
            <div class="flex gap-2 border-t border-slate-100 pt-4">
                <a href="{{ route('admin.products.index') }}" class="a-btn-light flex-1">Batal</a>
                <button class="a-btn-primary flex-1">Simpan</button>
            </div>
        </section>

        <section class="a-card space-y-3 p-5 sm:p-6" x-data="imagePreview(@js($product->image ? $product->imageUrl() : null))">
            <h2 class="font-bold text-slate-900">Gambar Produk</h2>
            <label class="group relative flex aspect-square cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 hover:border-forest-400">
                <img x-show="preview" :src="preview" alt="" class="absolute inset-0 size-full object-contain p-2">
                <span x-show="!preview" class="text-center text-sm text-slate-400"><x-icon name="image" class="mx-auto size-8" /> Klik untuk unggah</span>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="pick">
            </label>
            <p class="text-xs text-slate-500">JPG/PNG/WEBP, maks 2MB. Rasio 1:1 disarankan.</p>
        </section>

        <section class="a-card space-y-4 p-5 sm:p-6">
            <h2 class="font-bold text-slate-900">Harga & Stok</h2>
            <div><label for="category_id" class="a-label">Kategori</label>
                <select id="category_id" name="category_id" class="a-input"><option value="">— Tanpa kategori —</option>
                    @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div><label for="price" class="a-label">Harga Normal (Rp) *</label><input id="price" type="number" min="0" name="price" value="{{ old('price', $product->price) }}" required class="a-input"></div>
            <div><label for="sale_price" class="a-label">Harga Promo (Rp)</label><input id="sale_price" type="number" min="0" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="a-input"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label for="stock" class="a-label">Stok *</label><input id="stock" type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}" required class="a-input"></div>
                <div><label for="weight" class="a-label">Berat (gr)</label><input id="weight" type="number" min="0" name="weight" value="{{ old('weight', $product->weight) }}" class="a-input"></div>
            </div>
            <div><label for="sort_order" class="a-label">Urutan</label><input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" class="a-input"></div>
        </section>
    </div>
</form>
@endsection
