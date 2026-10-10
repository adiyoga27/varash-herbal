@extends('layouts.admin')

@section('title', 'Notifikasi')

@section('content')
<div class="flex justify-end">
    <form method="POST" action="{{ route('admin.notifications.readAll') }}">@csrf<button class="a-btn-light"><x-icon name="check" class="size-4" /> Tandai semua dibaca</button></form>
</div>
<div class="a-card mt-4 divide-y divide-slate-100 overflow-hidden">
    @forelse ($notifications as $n)
        <a href="{{ route('admin.notifications.read', $n->id) }}" class="flex gap-3 p-4 hover:bg-slate-50 sm:p-5 {{ $n->read_at ? '' : 'bg-forest-50/50' }}">
            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-slate-100 text-lg">{{ ['order' => '🛒', 'subscriber' => '✉️', 'message' => '💬'][$n->data['type'] ?? ''] ?? '🔔' }}</span>
            <div class="min-w-0 flex-1">
                <p class="{{ $n->read_at ? 'text-slate-700' : 'font-bold text-slate-900' }}">{{ $n->data['title'] ?? '' }}</p>
                <p class="truncate text-sm text-slate-500">{{ $n->data['message'] ?? '' }}</p>
            </div>
            <span class="shrink-0 text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</span>
        </a>
    @empty
        <x-admin.empty icon="bell" message="Belum ada notifikasi." />
    @endforelse
</div>
<div class="mt-5">{{ $notifications->links() }}</div>
@endsection
