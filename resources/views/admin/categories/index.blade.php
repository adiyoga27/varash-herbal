@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
<div x-data="crudModal({ name: '', slug: '', description: '', sort_order: 0, is_active: true })">
    <div class="flex justify-end"><button @click="create(@js(route('admin.categories.store')))" class="a-btn-primary"><x-icon name="plus" class="size-4" /> Tambah Kategori</button></div>

    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($categories as $c)
            <div class="a-card flex flex-col p-5">
                <div class="flex items-start justify-between gap-3">
                    <span class="grid size-11 place-items-center rounded-xl bg-forest-100 text-forest-700"><x-icon :name="$c->icon ?: 'leaf'" /></span>
                    <span class="a-badge {{ $c->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $c->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <h3 class="mt-3 font-bold text-slate-900">{{ $c->name }}</h3>
                <p class="text-xs text-slate-400">/kategori/{{ $c->slug }} · {{ $c->products_count }} produk</p>
                <p class="mt-2 line-clamp-2 flex-1 text-sm text-slate-500">{{ $c->description }}</p>
                <div class="mt-4 flex gap-2 border-t border-slate-100 pt-4">
                    <button @click="edit(@js(route('admin.categories.update', $c)), @js($c->only(['name', 'slug', 'description', 'sort_order', 'is_active'])))" class="a-btn-light flex-1 !py-2"><x-icon name="edit" class="size-4" /> Edit</button>
                    <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" data-confirm="Hapus kategori {{ $c->name }}? Produk di dalamnya tidak ikut terhapus.">@csrf @method('DELETE')<button class="a-btn-light !py-2 text-red-600"><x-icon name="trash" class="size-4" /></button></form>
                </div>
            </div>
        @empty
            <div class="a-card sm:col-span-2 xl:col-span-3"><x-admin.empty icon="tag" message="Belum ada kategori." /></div>
        @endforelse
    </div>

    <x-admin.modal title="Kategori">
        <form method="POST" :action="action" class="space-y-4 p-5">
            @csrf
            <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
            <div><label class="a-label">Nama *</label><input name="name" x-model="form.name" required class="a-input"></div>
            <div><label class="a-label">Slug</label><input name="slug" x-model="form.slug" placeholder="otomatis" class="a-input"></div>
            <div><label class="a-label">Deskripsi (juga dipakai sebagai meta description)</label><textarea name="description" x-model="form.description" rows="3" class="a-input"></textarea></div>
            <div><label class="a-label">Urutan</label><input type="number" name="sort_order" x-model="form.sort_order" class="a-input"></div>
            <x-admin.toggle name="is_active" model="form.is_active" label="Aktif" />
            <div class="flex gap-2 pt-2"><button type="button" @click="open = false" class="a-btn-light flex-1">Batal</button><button class="a-btn-primary flex-1">Simpan</button></div>
        </form>
    </x-admin.modal>
</div>
@endsection
