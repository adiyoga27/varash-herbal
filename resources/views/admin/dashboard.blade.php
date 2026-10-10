@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php $maxTotal = max(1, $chart->max('total')); @endphp

<div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-sm text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
        <h2 class="text-2xl font-bold text-slate-900">Halo, {{ auth()->user()->name }} 👋</h2>
    </div>
    <a href="{{ route('admin.products.create') }}" class="a-btn-primary mt-3 self-start sm:mt-0"><x-icon name="plus" class="size-4" /> Tambah Produk</a>
</div>

{{-- Stat cards --}}
<div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
    @foreach ([
        ['Pendapatan Bulan Ini', rupiah($stats['revenue_month']), 'wallet', 'bg-forest-100 text-forest-700', null],
        ['Pesanan Hari Ini', $stats['orders_today'], 'package', 'bg-sky-100 text-sky-700', null],
        ['Menunggu Konfirmasi', $stats['orders_pending'], 'clock', 'bg-amber-100 text-amber-700', route('admin.orders.index', ['status' => 'pending'])],
        ['Subscriber Aktif', $stats['subscribers'], 'users', 'bg-violet-100 text-violet-700', route('admin.subscribers.index')],
    ] as [$label, $value, $icon, $color, $link])
        <div class="a-card relative p-4 sm:p-5">
            <div class="flex items-start justify-between gap-2">
                <p class="text-xs font-medium text-slate-500 sm:text-sm">{{ $label }}</p>
                <span class="grid size-9 shrink-0 place-items-center rounded-xl {{ $color }}"><x-icon :name="$icon" class="size-[1.1rem]" /></span>
            </div>
            <p class="mt-2 truncate text-xl font-extrabold text-slate-900 sm:text-2xl">{{ $value }}</p>
            @if ($label === 'Subscriber Aktif')<p class="mt-1 text-xs text-forest-600">+{{ $stats['subscribers_week'] }} minggu ini</p>@endif
            @if ($link)<a href="{{ $link }}" class="absolute inset-0" aria-label="{{ $label }}"></a>@endif
        </div>
    @endforeach
</div>

<div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
    {{-- Chart --}}
    <div class="a-card p-5 xl:col-span-2">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900">Penjualan 14 Hari Terakhir</h3>
                <p class="text-xs text-slate-500">Total {{ rupiah($chart->sum('total')) }} dari {{ $chart->sum('count') }} pesanan</p>
            </div>
        </div>
        <div class="mt-6 flex h-56 items-end gap-1.5 sm:gap-2" role="img" aria-label="Grafik penjualan harian">
            @foreach ($chart as $day)
                <div class="group relative flex h-full flex-1 flex-col justify-end">
                    <div class="w-full rounded-t-md bg-gradient-to-t from-forest-600 to-forest-400 transition group-hover:from-gold-500 group-hover:to-gold-300"
                         style="height: {{ max(2, $day['total'] / $maxTotal * 100) }}%"></div>
                    <div class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-2 hidden -translate-x-1/2 rounded-lg bg-slate-900 px-2.5 py-1.5 text-center text-[0.7rem] whitespace-nowrap text-white group-hover:block">
                        {{ $day['label'] }}<br><strong>{{ rupiah($day['total']) }}</strong> · {{ $day['count'] }} pesanan
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-2 flex gap-1.5 text-[0.6rem] text-slate-400 sm:gap-2 sm:text-[0.65rem]">
            @foreach ($chart as $i => $day)
                <span class="flex-1 text-center whitespace-nowrap"><span class="sm:hidden">{{ $i % 4 === 0 ? $day['label'] : '' }}</span><span class="hidden sm:inline">{{ $i % 2 === 0 ? $day['label'] : '' }}</span></span>
            @endforeach
        </div>
    </div>

    {{-- Top products --}}
    <div class="a-card p-5">
        <h3 class="font-bold text-slate-900">Produk Terlaris</h3>
        <ul class="mt-4 space-y-4">
            @forelse ($topProducts as $i => $p)
                <li class="flex items-center gap-3">
                    <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600">{{ $i + 1 }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $p->product_name }}</p>
                        <p class="text-xs text-slate-500">{{ $p->qty }} terjual</p>
                    </div>
                    <p class="text-sm font-bold text-forest-700">{{ rupiah($p->revenue) }}</p>
                </li>
            @empty
                <li class="py-8 text-center text-sm text-slate-500">Belum ada penjualan.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
    {{-- Recent orders --}}
    <div class="a-card overflow-hidden xl:col-span-2">
        <div class="flex items-center justify-between p-5">
            <h3 class="font-bold text-slate-900">Pesanan Terbaru</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-forest-700 hover:underline">Lihat semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="a-table">
                <thead><tr><th>Pesanan</th><th class="hidden sm:table-cell">Pelanggan</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr class="cursor-pointer" onclick="location='{{ route('admin.orders.show', $order) }}'">
                            <td><p class="font-mono text-xs font-bold text-slate-900">{{ $order->code }}</p><p class="text-xs text-slate-500">{{ $order->created_at->diffForHumans() }}</p></td>
                            <td class="hidden sm:table-cell">{{ $order->customer_name }}</td>
                            <td class="font-semibold whitespace-nowrap">{{ rupiah($order->total) }}</td>
                            <td><span class="a-badge {{ $order->statusColor() }}">{{ $order->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><x-admin.empty icon="package" message="Belum ada pesanan." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Low stock --}}
        <div class="a-card p-5">
            <h3 class="flex items-center gap-2 font-bold text-slate-900"><x-icon name="alert" class="size-4 text-amber-500" /> Stok Menipis</h3>
            <ul class="mt-3 divide-y divide-slate-100">
                @forelse ($lowStock as $p)
                    <li class="flex items-center justify-between py-2.5 text-sm">
                        <a href="{{ route('admin.products.edit', $p) }}" class="truncate hover:text-forest-700">{{ $p->name }}</a>
                        <span class="a-badge {{ $p->stock == 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">{{ $p->stock }}</span>
                    </li>
                @empty
                    <li class="py-4 text-center text-sm text-slate-500">Semua stok aman ✅</li>
                @endforelse
            </ul>
        </div>

        {{-- Activity --}}
        <div class="a-card p-5">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.logs.index') }}" class="text-xs font-semibold text-forest-700 hover:underline">Semua log</a>
            </div>
            <ol class="mt-4 space-y-4 border-l-2 border-slate-100 pl-4">
                @foreach ($activities as $log)
                    <li class="relative">
                        <span class="absolute top-1.5 -left-[1.3rem] size-2.5 rounded-full ring-4 ring-white {{ $log->levelDot() }}"></span>
                        <a href="{{ route('admin.logs.show', $log) }}" class="block text-sm text-slate-700 hover:text-forest-700">{{ $log->description }}</a>
                        <p class="text-xs text-slate-400">{{ $log->user?->name ?? 'Sistem/Tamu' }} · {{ $log->created_at->diffForHumans() }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</div>
@endsection
