@extends('layouts.admin')

@section('title', 'Pengaturan Website')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" x-data="{ tab: @js(array_key_first($groups)) }">
    @csrf @method('PUT')
    <div class="no-scrollbar -mx-4 flex gap-2 overflow-x-auto px-4 sm:mx-0 sm:px-0">
        @foreach ($groups as $group => $fields)
            <button type="button" @click="tab = @js($group)" :class="tab === @js($group) ? 'bg-forest-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200'" class="shrink-0 rounded-xl px-4 py-2 text-sm font-semibold">{{ $group }}</button>
        @endforeach
    </div>
    @foreach ($groups as $group => $fields)
        <section x-show="tab === @js($group)" @if (! $loop->first) x-cloak @endif class="a-card mt-4 grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
            @foreach ($fields as $key => $field)
                @php [$label, $type] = $field; $hint = $field[2] ?? null; @endphp
                <div @class(['md:col-span-2' => $type === 'textarea'])>
                    <label for="{{ $key }}" class="a-label">{{ $label }}</label>
                    @if ($type === 'textarea')
                        <textarea id="{{ $key }}" name="{{ $key }}" rows="3" class="a-input">{{ old($key, $values[$key] ?? '') }}</textarea>
                    @else
                        <input id="{{ $key }}" type="{{ $type === 'url' ? 'text' : $type }}" name="{{ $key }}" value="{{ old($key, $values[$key] ?? '') }}" class="a-input">
                    @endif
                    @if ($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
                </div>
            @endforeach
        </section>
    @endforeach
    <div class="sticky bottom-0 -mx-4 mt-6 flex justify-end border-t border-slate-200 bg-slate-50/90 px-4 py-3 backdrop-blur sm:mx-0 sm:rounded-xl sm:border sm:px-4">
        <button class="a-btn-primary">Simpan Pengaturan</button>
    </div>
</form>
@endsection
