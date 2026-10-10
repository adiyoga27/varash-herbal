@extends('layouts.admin')

@section('title', 'Subscriber Newsletter')

@section('content')
<div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
    <div class="a-card p-4"><p class="text-sm text-slate-500">Subscriber Aktif</p><p class="mt-1 text-2xl font-extrabold text-forest-700">{{ number_format($activeCount) }}</p></div>
    <div class="a-card p-4"><p class="text-sm text-slate-500">Total Terdaftar</p><p class="mt-1 text-2xl font-extrabold text-slate-900">{{ number_format($totalCount) }}</p></div>
    <a href="{{ route('admin.newsletters.create') }}" class="a-card col-span-2 flex items-center gap-3 p-4 transition hover:border-forest-300 sm:col-span-1">
        <span class="grid size-10 place-items-center rounded-xl bg-forest-100 text-forest-700"><x-icon name="send" /></span>
        <span><span class="block font-bold text-slate-900">Kirim Newsletter</span><span class="text-xs text-slate-500">ke {{ $activeCount }} subscriber aktif</span></span>
    </a>
</div>

<div class="mt-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between" x-data="{ add: false }">
    <form method="GET" class="flex flex-1 gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari email…" class="a-input sm:max-w-xs">
        <select name="status" class="a-input !w-auto" onchange="this.form.submit()">
            <option value="">Semua</option><option value="active" @selected(request('status') === 'active')>Aktif</option><option value="unsubscribed" @selected(request('status') === 'unsubscribed')>Berhenti</option>
        </select>
    </form>
    <div class="flex gap-2">
        <a href="{{ route('admin.subscribers.export') }}" class="a-btn-light"><x-icon name="download" class="size-4" /> Export CSV</a>
        <button @click="add = !add" class="a-btn-primary"><x-icon name="plus" class="size-4" /> Tambah</button>
    </div>
    <form x-cloak x-show="add" x-collapse method="POST" action="{{ route('admin.subscribers.store') }}" class="a-card flex w-full flex-col gap-2 p-4 sm:flex-row lg:order-last">
        @csrf
        <input type="email" name="email" required placeholder="email@contoh.com" class="a-input">
        <input name="name" placeholder="Nama (opsional)" class="a-input">
        <button class="a-btn-primary shrink-0">Simpan</button>
    </form>
</div>

<div class="a-card mt-4 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Email</th><th class="hidden md:table-cell">Sumber</th><th class="hidden sm:table-cell">Terdaftar</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
                @forelse ($subscribers as $s)
                    <tr>
                        <td><p class="font-semibold text-slate-900">{{ $s->email }}</p>@if ($s->name)<p class="text-xs text-slate-500">{{ $s->name }}</p>@endif</td>
                        <td class="hidden md:table-cell text-slate-500">{{ $s->source }}</td>
                        <td class="hidden sm:table-cell text-xs whitespace-nowrap text-slate-500">{{ $s->created_at->translatedFormat('d M Y H:i') }}</td>
                        <td><span class="a-badge {{ $s->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">{{ $s->status === 'active' ? 'Aktif' : 'Berhenti' }}</span></td>
                        <td><div class="flex justify-end gap-1">
                            <form method="POST" action="{{ route('admin.subscribers.toggle', $s) }}">@csrf @method('PATCH')<button class="a-icon-btn" title="{{ $s->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}"><x-icon name="refresh" class="size-4" /></button></form>
                            <form method="POST" action="{{ route('admin.subscribers.destroy', $s) }}" data-confirm="Hapus {{ $s->email }}?">@csrf @method('DELETE')<button class="a-icon-btn hover:!text-red-600"><x-icon name="trash" class="size-4" /></button></form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-admin.empty icon="users" message="Belum ada subscriber." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $subscribers->links() }}</div>
@endsection
