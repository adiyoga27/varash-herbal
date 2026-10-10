@extends('layouts.admin')

@section('title', 'Produk')

@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <form method="GET" class="flex flex-1 flex-wrap gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / SKU…" class="a-input min-w-0 flex-1 sm:max-w-xs">
        <select name="category" class="a-input !w-auto" onchange="this.form.submit()">
            <option value="">Semua kategori</option>
            @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach
        </select>
        <select name="status" class="a-input !w-auto" onchange="this.form.submit()">
            @foreach (['' => 'Semua status', 'active' => 'Aktif', 'inactive' => 'Nonaktif', 'trashed' => 'Sampah'] as $v => $l)
                <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('admin.products.create') }}" class="a-btn-primary"><x-icon name="plus" class="size-4" /> Tambah Produk</a>
</div>

<div class="a-card mt-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Produk</th><th class="hidden md:table-cell">Kategori</th><th>Harga</th><th class="hidden sm:table-cell">Stok</th><th class="hidden lg:table-cell">Dilihat</th><th class="hidden sm:table-cell">Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $p->imageUrl() }}" alt="" class="size-12 shrink-0 rounded-xl bg-slate-100 object-contain">
                                <div class="min-w-0">
                                    <p class="line-clamp-1 font-semibold text-slate-900">{{ $p->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $p->sku }} @if ($p->is_featured)<span class="a-badge ml-1 bg-gold-100 text-gold-700">★ Unggulan</span>@endif</p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell">{{ $p->category?->name ?? '—' }}</td>
                        <td class="whitespace-nowrap">
                            <p class="font-semibold">{{ rupiah($p->finalPrice()) }}</p>
                            @if ($p->hasDiscount())<p class="text-xs text-slate-400 line-through">{{ rupiah($p->price) }}</p>@endif
                        </td>
                        <td class="hidden sm:table-cell"><span class="a-badge {{ $p->stock <= 10 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">{{ $p->stock }}</span></td>
                        <td class="hidden lg:table-cell text-slate-500">{{ number_format($p->views) }}</td>
                        <td class="hidden sm:table-cell">
                            @if ($p->trashed())<span class="a-badge bg-red-100 text-red-700">Dihapus</span>
                            @else<span class="a-badge {{ $p->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span>@endif
                        </td>
                        <td>
                            <div class="flex justify-end gap-1">
                                @if ($p->trashed())
                                    <form method="POST" action="{{ route('admin.products.restore', $p->id) }}">@csrf<button class="a-icon-btn" title="Pulihkan"><x-icon name="refresh" class="size-4" /></button></form>
                                @else
                                    <a href="{{ route('products.show', $p) }}" target="_blank" class="a-icon-btn" title="Lihat"><x-icon name="eye" class="size-4" /></a>
                                    <a href="{{ route('admin.products.edit', $p) }}" class="a-icon-btn" title="Edit"><x-icon name="edit" class="size-4" /></a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $p) }}" data-confirm="Hapus produk {{ $p->name }}?">@csrf @method('DELETE')<button class="a-icon-btn hover:!bg-red-50 hover:!text-red-600" title="Hapus"><x-icon name="trash" class="size-4" /></button></form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-admin.empty message="Belum ada produk." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $products->links() }}</div>
@endsection
