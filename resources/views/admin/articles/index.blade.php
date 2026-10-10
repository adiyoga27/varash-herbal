@extends('layouts.admin')

@section('title', 'Artikel')

@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <form method="GET" class="flex-1"><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul artikel…" class="a-input sm:max-w-xs"></form>
    <a href="{{ route('admin.articles.create') }}" class="a-btn-primary"><x-icon name="plus" class="size-4" /> Tulis Artikel</a>
</div>
<div class="a-card mt-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Artikel</th><th class="hidden md:table-cell">Terbit</th><th class="hidden sm:table-cell">Dilihat</th><th class="hidden sm:table-cell">Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
                @forelse ($articles as $a)
                    <tr>
                        <td><div class="flex items-center gap-3">
                            <img src="{{ $a->imageUrl() }}" alt="" class="h-12 w-16 shrink-0 rounded-lg object-cover">
                            <div class="min-w-0"><p class="line-clamp-2 font-semibold text-slate-900">{{ $a->title }}</p><p class="text-xs text-slate-500">{{ $a->author?->name }}</p></div>
                        </div></td>
                        <td class="hidden md:table-cell text-xs whitespace-nowrap text-slate-500">{{ $a->published_at?->translatedFormat('d M Y H:i') }}</td>
                        <td class="hidden sm:table-cell text-slate-500">{{ number_format($a->views) }}</td>
                        <td class="hidden sm:table-cell">
                            @if (! $a->is_published)<span class="a-badge bg-slate-100 text-slate-600">Draft</span>
                            @elseif ($a->published_at?->isFuture())<span class="a-badge bg-sky-100 text-sky-800">Terjadwal</span>
                            @else<span class="a-badge bg-emerald-100 text-emerald-800">Terbit</span>@endif
                        </td>
                        <td><div class="flex justify-end gap-1">
                            <a href="{{ route('articles.show', $a) }}" target="_blank" class="a-icon-btn"><x-icon name="eye" class="size-4" /></a>
                            <a href="{{ route('admin.articles.edit', $a) }}" class="a-icon-btn"><x-icon name="edit" class="size-4" /></a>
                            <form method="POST" action="{{ route('admin.articles.destroy', $a) }}" data-confirm="Hapus artikel ini?">@csrf @method('DELETE')<button class="a-icon-btn hover:!text-red-600"><x-icon name="trash" class="size-4" /></button></form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-admin.empty icon="file" message="Belum ada artikel." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $articles->links() }}</div>
@endsection
