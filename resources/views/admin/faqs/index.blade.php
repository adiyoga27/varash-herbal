@extends('layouts.admin')

@section('title', 'FAQ')

@section('content')
<div x-data="crudModal({ question: '', answer: '', sort_order: 0, is_active: true })">
    <div class="flex justify-end"><button @click="create(@js(route('admin.faqs.store')))" class="a-btn-primary"><x-icon name="plus" class="size-4" /> Tambah FAQ</button></div>
    <div class="a-card mt-5 divide-y divide-slate-100">
        @forelse ($faqs as $f)
            <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-start">
                <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600">{{ $f->sort_order }}</span>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-slate-900">{{ $f->question }} @unless ($f->is_active)<span class="a-badge ml-1 bg-slate-100 text-slate-500">Nonaktif</span>@endunless</p>
                    <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ $f->answer }}</p>
                </div>
                <div class="flex gap-1">
                    <button @click="edit(@js(route('admin.faqs.update', $f)), @js($f->only(['question', 'answer', 'sort_order', 'is_active'])))" class="a-icon-btn"><x-icon name="edit" class="size-4" /></button>
                    <form method="POST" action="{{ route('admin.faqs.destroy', $f) }}" data-confirm="Hapus FAQ ini?">@csrf @method('DELETE')<button class="a-icon-btn hover:!text-red-600"><x-icon name="trash" class="size-4" /></button></form>
                </div>
            </div>
        @empty
            <x-admin.empty icon="question" message="Belum ada FAQ." />
        @endforelse
    </div>

    <x-admin.modal title="FAQ">
        <form method="POST" :action="action" class="space-y-4 p-5">
            @csrf
            <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
            <div><label class="a-label">Pertanyaan *</label><input name="question" x-model="form.question" required class="a-input"></div>
            <div><label class="a-label">Jawaban *</label><textarea name="answer" x-model="form.answer" rows="5" required class="a-input"></textarea></div>
            <div><label class="a-label">Urutan</label><input type="number" name="sort_order" x-model="form.sort_order" class="a-input"></div>
            <x-admin.toggle name="is_active" model="form.is_active" label="Aktif" />
            <div class="flex gap-2 pt-2"><button type="button" @click="open = false" class="a-btn-light flex-1">Batal</button><button class="a-btn-primary flex-1">Simpan</button></div>
        </form>
    </x-admin.modal>
</div>
@endsection
