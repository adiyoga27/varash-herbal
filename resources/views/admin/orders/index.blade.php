@extends('layouts.admin')

@section('title', 'Pesanan')

@section('content')
<div class="no-scrollbar -mx-4 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0">
    <a href="{{ route('admin.orders.index') }}" @class(['a-badge !px-3.5 !py-2 !text-sm', 'bg-forest-700 text-white' => ! request('status'), 'bg-white text-slate-600 ring-1 ring-slate-200' => request('status')])>Semua <span class="opacity-70">{{ $counts->sum() }}</span></a>
    @foreach (\App\Models\Order::STATUSES as $key => $label)
        <a href="{{ route('admin.orders.index', ['status' => $key]) }}" @class(['a-badge !px-3.5 !py-2 !text-sm', 'bg-forest-700 text-white' => request('status') === $key, 'bg-white text-slate-600 ring-1 ring-slate-200' => request('status') !== $key])>{{ $label }} <span class="opacity-70">{{ $counts[$key] ?? 0 }}</span></a>
    @endforeach
</div>

<form method="GET" class="a-card mt-4 grid grid-cols-2 gap-3 p-4 sm:grid-cols-4 lg:grid-cols-6">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Kode / nama / no. HP" class="a-input col-span-2">
    <input type="date" name="from" value="{{ request('from') }}" class="a-input" aria-label="Dari tanggal">
    <input type="date" name="to" value="{{ request('to') }}" class="a-input" aria-label="Sampai tanggal">
    <button class="a-btn-primary"><x-icon name="filter" class="size-4" /> Filter</button>
    <a href="{{ route('admin.orders.export', request()->query()) }}" class="a-btn-light"><x-icon name="download" class="size-4" /> CSV</a>
</form>

{{-- Mobile: daftar kartu --}}
<div class="mt-4 space-y-3 sm:hidden">
    @forelse ($orders as $o)
        <a href="{{ route('admin.orders.show', $o) }}" class="a-card block p-4 active:bg-slate-50">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="font-mono text-xs font-bold text-forest-700">{{ $o->code }}</p>
                    <p class="mt-0.5 truncate font-semibold text-slate-900">{{ $o->customer_name }}</p>
                    <p class="text-xs text-slate-500">{{ $o->customer_phone }} · {{ $o->city }}</p>
                </div>
                <span class="a-badge shrink-0 {{ $o->statusColor() }}">{{ $o->statusLabel() }}</span>
            </div>
            <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3 text-sm">
                <span class="text-xs text-slate-500">{{ $o->created_at->translatedFormat('d M Y H:i') }} · {{ $o->items_count }} produk</span>
                <span class="font-bold text-slate-900">{{ rupiah($o->total) }}</span>
            </div>
        </a>
    @empty
        <div class="a-card"><x-admin.empty icon="package" message="Belum ada pesanan." /></div>
    @endforelse
</div>

<div class="a-card mt-4 hidden overflow-hidden sm:block">
    <div class="overflow-x-auto">
        <table class="a-table">
            <thead><tr><th>Pesanan</th><th>Pelanggan</th><th class="hidden lg:table-cell">Item</th><th>Total</th><th class="hidden sm:table-cell">Status</th><th class="hidden md:table-cell">WA</th><th></th></tr></thead>
            <tbody>
                @forelse ($orders as $o)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $o) }}" class="font-mono text-xs font-bold text-forest-700 hover:underline">{{ $o->code }}</a><p class="text-xs whitespace-nowrap text-slate-500">{{ $o->created_at->translatedFormat('d M Y H:i') }}</p></td>
                        <td><p class="font-semibold text-slate-900">{{ $o->customer_name }}</p><p class="text-xs text-slate-500">{{ $o->customer_phone }} · {{ $o->city }}</p></td>
                        <td class="hidden lg:table-cell text-slate-500">{{ $o->items_count }} produk</td>
                        <td class="font-semibold whitespace-nowrap">{{ rupiah($o->total) }}</td>
                        <td class="hidden sm:table-cell"><span class="a-badge {{ $o->statusColor() }}">{{ $o->statusLabel() }}</span></td>
                        <td class="hidden md:table-cell">@if ($o->whatsapp_sent_at)<span title="Pelanggan membuka WhatsApp {{ $o->whatsapp_sent_at->diffForHumans() }}" class="text-[#25D366]"><x-icon name="whatsapp" class="size-5" /></span>@else<span class="text-slate-300" title="Belum membuka WhatsApp"><x-icon name="whatsapp" class="size-5" /></span>@endif</td>
                        <td class="text-right"><a href="{{ route('admin.orders.show', $o) }}" class="a-icon-btn"><x-icon name="chevron-right" class="size-4" /></a></td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-admin.empty icon="package" message="Belum ada pesanan." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $orders->links() }}</div>
@endsection
