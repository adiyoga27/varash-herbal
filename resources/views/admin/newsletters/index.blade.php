@extends('layouts.admin')

@section('title', 'Newsletter')

@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm text-slate-500"><strong class="text-slate-900">{{ $activeSubscribers }}</strong> subscriber aktif akan menerima newsletter.</p>
    <a href="{{ route('admin.newsletters.create') }}" class="a-btn-primary"><x-icon name="plus" class="size-4" /> Buat Newsletter</a>
</div>
<div class="a-card mt-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Subjek</th><th class="hidden md:table-cell">Penulis</th><th>Status</th><th class="hidden sm:table-cell">Penerima</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
                @forelse ($newsletters as $n)
                    <tr>
                        <td><a href="{{ route('admin.newsletters.show', $n) }}" class="font-semibold text-slate-900 hover:text-forest-700">{{ $n->subject }}</a><p class="text-xs text-slate-500">{{ $n->created_at->translatedFormat('d M Y H:i') }}</p></td>
                        <td class="hidden md:table-cell text-slate-500">{{ $n->author?->name ?? '-' }}</td>
                        <td><span class="a-badge {{ $n->status === 'sent' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $n->status === 'sent' ? 'Terkirim' : 'Draft' }}</span></td>
                        <td class="hidden sm:table-cell text-slate-500">{{ $n->status === 'sent' ? number_format($n->recipients_count).' · '.$n->sent_at->diffForHumans() : '—' }}</td>
                        <td class="text-right"><a href="{{ route('admin.newsletters.show', $n) }}" class="a-icon-btn"><x-icon name="eye" class="size-4" /></a></td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-admin.empty icon="send" message="Belum ada newsletter." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $newsletters->links() }}</div>
@endsection
