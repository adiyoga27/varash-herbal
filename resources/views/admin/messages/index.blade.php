@extends('layouts.admin')

@section('title', 'Pesan Masuk')

@section('content')
<div class="a-card divide-y divide-slate-100 overflow-hidden">
    @forelse ($messages as $m)
        <a href="{{ route('admin.messages.show', $m) }}" class="flex gap-3 p-4 transition hover:bg-slate-50 sm:p-5 {{ $m->is_read ? '' : 'bg-forest-50/50' }}">
            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-slate-100 font-bold text-slate-600">{{ mb_substr($m->name, 0, 1) }}</span>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2">
                    <p class="truncate {{ $m->is_read ? 'font-medium text-slate-700' : 'font-bold text-slate-900' }}">{{ $m->name }} @if ($m->subject)<span class="font-normal text-slate-500">— {{ $m->subject }}</span>@endif</p>
                    <span class="shrink-0 text-xs text-slate-400">{{ $m->created_at->diffForHumans() }}</span>
                </div>
                <p class="truncate text-sm text-slate-500">{{ $m->message }}</p>
            </div>
        </a>
    @empty
        <x-admin.empty icon="mail" message="Belum ada pesan masuk." />
    @endforelse
</div>
<div class="mt-5">{{ $messages->links() }}</div>
@endsection
