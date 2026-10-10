@extends('layouts.admin')

@section('title', 'Activity Log')

@section('content')
<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    @foreach ([
        ['Aktivitas Hari Ini', $summary['today'], 'activity', 'bg-sky-100 text-sky-700'],
        ['Peringatan (7 hari)', $summary['warnings'], 'alert', 'bg-amber-100 text-amber-700'],
        ['Login Gagal (7 hari)', $summary['failed_logins'], 'lock', 'bg-red-100 text-red-700'],
        ['Total Log', number_format($summary['total']), 'file', 'bg-slate-100 text-slate-700'],
    ] as [$label, $value, $icon, $color])
        <div class="a-card flex items-center gap-3 p-4">
            <span class="grid size-10 shrink-0 place-items-center rounded-xl {{ $color }}"><x-icon :name="$icon" class="size-5" /></span>
            <div class="min-w-0"><p class="text-xs leading-tight text-slate-500">{{ $label }}</p><p class="text-xl font-extrabold text-slate-900">{{ $value }}</p></div>
        </div>
    @endforeach
</div>

<form method="GET" class="a-card mt-4 grid grid-cols-2 gap-3 p-4 md:grid-cols-4 xl:grid-cols-8" x-data>
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari deskripsi / IP" class="a-input col-span-2">
    <select name="log_name" class="a-input" @change="$el.form.submit()"><option value="">Semua modul</option>@foreach ($logNames as $n)<option @selected(request('log_name') === $n)>{{ $n }}</option>@endforeach</select>
    <select name="event" class="a-input" @change="$el.form.submit()"><option value="">Semua event</option>@foreach ($events as $e)<option @selected(request('event') === $e)>{{ $e }}</option>@endforeach</select>
    <select name="level" class="a-input" @change="$el.form.submit()"><option value="">Semua level</option>@foreach (['info', 'success', 'warning', 'danger', 'error'] as $l)<option @selected(request('level') === $l)>{{ $l }}</option>@endforeach</select>
    <select name="user_id" class="a-input" @change="$el.form.submit()"><option value="">Semua pengguna</option>@foreach ($users as $u)<option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>@endforeach</select>
    <input type="date" name="from" value="{{ request('from') }}" class="a-input" aria-label="Dari">
    <input type="date" name="to" value="{{ request('to') }}" class="a-input" aria-label="Sampai">
    <div class="col-span-2 flex gap-2 md:col-span-4 xl:col-span-8">
        <button class="a-btn-primary"><x-icon name="filter" class="size-4" /> Terapkan</button>
        <a href="{{ route('admin.logs.index') }}" class="a-btn-light">Reset</a>
        <a href="{{ route('admin.logs.export', request()->query()) }}" class="a-btn-light ml-auto"><x-icon name="download" class="size-4" /> <span class="hidden sm:inline">Export</span> CSV</a>
    </div>
</form>

<div class="a-card mt-4 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Waktu</th><th>Aktivitas</th><th class="hidden md:table-cell">Pengguna</th><th class="hidden lg:table-cell">IP / Perangkat</th><th class="hidden sm:table-cell">Level</th><th></th></tr></thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="text-xs whitespace-nowrap text-slate-500">{{ $log->created_at->format('d/m/y') }}<br><span class="font-mono">{{ $log->created_at->format('H:i:s') }}</span></td>
                        <td>
                            <p class="line-clamp-2 text-slate-800">{{ $log->description }}</p>
                            <p class="mt-0.5 flex flex-wrap gap-1"><span class="a-badge bg-slate-100 text-slate-600">{{ $log->log_name }}</span><span class="a-badge bg-slate-100 text-slate-600">{{ $log->event }}</span><span class="a-badge sm:hidden {{ $log->levelBadge() }}">{{ $log->level }}</span></p>
                        </td>
                        <td class="hidden md:table-cell whitespace-nowrap">{{ $log->user?->name ?? 'Tamu / Sistem' }}</td>
                        <td class="hidden lg:table-cell text-xs text-slate-500"><span class="font-mono">{{ $log->ip_address ?? '-' }}</span><br>{{ $log->device() }}</td>
                        <td class="hidden sm:table-cell"><span class="a-badge {{ $log->levelBadge() }}">{{ $log->level }}</span></td>
                        <td class="text-right"><a href="{{ route('admin.logs.show', $log) }}" class="a-icon-btn"><x-icon name="chevron-right" class="size-4" /></a></td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-admin.empty icon="activity" message="Tidak ada log yang cocok." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $logs->links() }}</div>

@if (auth()->user()->isSuperAdmin())
    <form method="POST" action="{{ route('admin.logs.purge') }}" data-confirm="Hapus permanen log lama?" class="a-card mt-6 flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
        @csrf @method('DELETE')
        <p class="flex-1 text-sm text-slate-600"><strong>Pembersihan log:</strong> hapus log yang lebih lama dari</p>
        <select name="days" class="a-input !w-auto">@foreach ([30, 90, 180, 365] as $d)<option value="{{ $d }}" @selected($d === 90)>{{ $d }} hari</option>@endforeach</select>
        <button class="a-btn-danger"><x-icon name="trash" class="size-4" /> Bersihkan</button>
    </form>
@endif
@endsection
