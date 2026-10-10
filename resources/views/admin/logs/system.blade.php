@extends('layouts.admin')

@section('title', 'System Log')

@section('content')
<div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <form method="GET" class="flex flex-wrap gap-2" x-data>
        <select name="file" class="a-input !w-auto" @change="$el.form.submit()">
            @forelse ($files as $f)
                <option value="{{ $f['name'] }}" @selected($current === $f['name'])>{{ $f['name'] }} ({{ number_format($f['size'] / 1024, 1) }} KB)</option>
            @empty
                <option>Tidak ada file log</option>
            @endforelse
        </select>
        <select name="level" class="a-input !w-auto" @change="$el.form.submit()">
            <option value="">Semua level</option>
            @foreach (['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'] as $l)<option @selected(request('level') === $l)>{{ $l }}</option>@endforeach
        </select>
    </form>
    @if ($current)
        <form method="POST" action="{{ route('admin.system-logs.clear') }}" data-confirm="Kosongkan file {{ $current }}?">
            @csrf <input type="hidden" name="file" value="{{ $current }}">
            <button class="a-btn-light text-red-600"><x-icon name="trash" class="size-4" /> Kosongkan File</button>
        </form>
    @endif
</div>
<p class="mt-3 text-xs text-slate-500">Menampilkan maks. 300 entri terbaru dari file log Laravel (storage/logs). Error aplikasi juga tercatat di Activity Log dengan modul <code>error</code>.</p>

<div class="mt-4 space-y-2">
    @forelse ($entries as $i => $e)
        @php $color = match (true) { in_array($e['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY']) => 'bg-red-100 text-red-700', $e['level'] === 'WARNING' => 'bg-amber-100 text-amber-800', default => 'bg-sky-100 text-sky-800' }; @endphp
        <div class="a-card overflow-hidden" x-data="{ open: false }">
            <button type="button" @click="open = !open" class="flex w-full items-start gap-3 p-4 text-left">
                <span class="a-badge shrink-0 {{ $color }}">{{ $e['level'] }}</span>
                <span class="min-w-0 flex-1"><span class="block font-mono text-xs break-words text-slate-800">{{ \Illuminate\Support\Str::limit($e['message'], 300) }}</span><span class="text-xs text-slate-400">{{ $e['date'] }} · {{ $e['env'] }}</span></span>
                @if ($e['trace'])<x-icon name="chevron-down" class="size-4 shrink-0 text-slate-400" />@endif
            </button>
            @if ($e['trace'])
                <pre x-cloak x-show="open" x-collapse class="max-h-96 overflow-auto bg-slate-900 p-4 text-[0.7rem] leading-relaxed text-slate-300">{{ $e['trace'] }}</pre>
            @endif
        </div>
    @empty
        <div class="a-card"><x-admin.empty icon="terminal" message="Log kosong — tidak ada error. 🎉" /></div>
    @endforelse
</div>
@endsection
