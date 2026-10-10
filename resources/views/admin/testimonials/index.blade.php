@extends('layouts.admin')

@section('title', 'Testimoni')

@section('content')
<div x-data="crudModal({ name: '', location: '', product_id: '', content: '', rating: 5, is_active: true })">
    <div class="flex justify-end"><button @click="create(@js(route('admin.testimonials.store')))" class="a-btn-primary"><x-icon name="plus" class="size-4" /> Tambah Testimoni</button></div>

    <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($testimonials as $t)
            <div class="a-card flex flex-col p-5">
                <div class="flex items-center justify-between">
                    <div class="flex gap-0.5 text-gold-400">@for ($i = 0; $i < $t->rating; $i++)<x-icon name="star" class="size-4 fill-current" />@endfor</div>
                    <span class="a-badge {{ $t->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $t->is_active ? 'Tampil' : 'Disembunyikan' }}</span>
                </div>
                <p class="mt-3 line-clamp-4 flex-1 text-sm text-slate-600">“{{ $t->content }}”</p>
                <p class="mt-3 text-sm font-bold text-slate-900">{{ $t->name }} <span class="font-normal text-slate-400">· {{ $t->location }}</span></p>
                @if ($t->product)<p class="text-xs text-forest-700">{{ $t->product->name }}</p>@endif
                <div class="mt-4 flex gap-2 border-t border-slate-100 pt-4">
                    <button @click="edit(@js(route('admin.testimonials.update', $t)), @js($t->only(['name', 'location', 'product_id', 'content', 'rating', 'is_active'])))" class="a-btn-light flex-1 !py-2"><x-icon name="edit" class="size-4" /> Edit</button>
                    <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" data-confirm="Hapus testimoni ini?">@csrf @method('DELETE')<button class="a-btn-light !py-2 text-red-600"><x-icon name="trash" class="size-4" /></button></form>
                </div>
            </div>
        @empty
            <div class="a-card md:col-span-2 xl:col-span-3"><x-admin.empty icon="star" message="Belum ada testimoni." /></div>
        @endforelse
    </div>
    <div class="mt-5">{{ $testimonials->links() }}</div>

    <x-admin.modal title="Testimoni">
        <form method="POST" :action="action" class="space-y-4 p-5">
            @csrf
            <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="a-label">Nama *</label><input name="name" x-model="form.name" required class="a-input"></div>
                <div><label class="a-label">Kota</label><input name="location" x-model="form.location" class="a-input"></div>
            </div>
            <div><label class="a-label">Produk</label>
                <select name="product_id" x-model="form.product_id" class="a-input"><option value="">— Umum —</option>
                    @foreach ($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="a-label">Isi Testimoni *</label><textarea name="content" x-model="form.content" rows="4" required class="a-input"></textarea></div>
            <div><label class="a-label">Rating</label>
                <select name="rating" x-model="form.rating" class="a-input">@for ($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ str_repeat('★', $i) }} ({{ $i }})</option>@endfor</select>
            </div>
            <x-admin.toggle name="is_active" model="form.is_active" label="Tampilkan di website" />
            <div class="flex gap-2 pt-2"><button type="button" @click="open = false" class="a-btn-light flex-1">Batal</button><button class="a-btn-primary flex-1">Simpan</button></div>
        </form>
    </x-admin.modal>
</div>
@endsection
