@extends('layouts.admin')

@section('title', 'Detail Log #'.$log->id)

@section('content')
<a href="{{ url()->previous() === url()->current() ? route('admin.logs.index') : url()->previous() }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-900"><x-icon name="arrow-left" class="size-4" /> Kembali</a>
<div class="mt-4 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="a-card p-5 sm:p-6 xl:col-span-2">
        <span class="a-badge {{ $log->levelBadge() }}">{{ strtoupper($log->level) }}</span>
        <h2 class="mt-3 text-lg font-bold text-slate-900">{{ $log->description }}</h2>

        @php $props = $log->properties ?? []; @endphp
        @if (isset($props['old']) || (isset($props['attributes']) && $log->event === 'updated'))
            <h3 class="mt-6 text-sm font-bold text-slate-700">Perubahan Data</h3>
            <div class="mt-2 overflow-x-auto rounded-xl border border-slate-200">
                <table class="a-table">
                    <thead><tr><th>Field</th><th>Sebelum</th><th>Sesudah</th></tr></thead>
                    <tbody>
                        @foreach ($props['attributes'] ?? [] as $field => $new)
                            <tr>
                                <td class="font-mono text-xs font-semibold">{{ $field }}</td>
                                <td class="max-w-xs text-xs break-words text-red-700"><del>{{ is_scalar($props['old'][$field] ?? null) ? $props['old'][$field] : json_encode($props['old'][$field] ?? null, JSON_UNESCAPED_UNICODE) }}</del></td>
                                <td class="max-w-xs text-xs break-words text-emerald-700">{{ is_scalar($new) ? $new : json_encode($new, JSON_UNESCAPED_UNICODE) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif (! empty($props))
            <h3 class="mt-6 text-sm font-bold text-slate-700">Data</h3>
            <pre class="mt-2 max-h-[28rem] overflow-auto rounded-xl bg-slate-900 p-4 text-xs leading-relaxed text-emerald-300">{{ json_encode($props, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
        @endif
    </div>
    <dl class="a-card space-y-3 p-5 text-sm">
        @foreach ([
            'Waktu' => $log->created_at->translatedFormat('l, d F Y H:i:s'),
            'Pengguna' => $log->user ? $log->user->name.' ('.$log->user->email.')' : 'Tamu / Sistem',
            'Modul' => $log->log_name,
            'Event' => $log->event,
            'Subjek' => $log->subject_type ? class_basename($log->subject_type).' #'.$log->subject_id : '-',
            'IP Address' => $log->ip_address ?? '-',
            'Perangkat' => $log->device(),
            'Request' => trim(($log->method ?? '').' '.($log->url ?? '')) ?: '-',
            'User Agent' => $log->user_agent ?? '-',
        ] as $label => $value)
            <div><dt class="text-xs font-semibold text-slate-500 uppercase">{{ $label }}</dt><dd class="mt-0.5 break-words text-slate-800">{{ $value }}</dd></div>
        @endforeach
    </dl>
</div>
@endsection
