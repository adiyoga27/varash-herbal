@extends('layouts.app')

@section('title', 'Lacak Pesanan')
@section('robots', 'noindex, follow')

@php
    $mask = fn ($s, $keep = 3) => mb_substr($s, 0, $keep).str_repeat('•', max(0, mb_strlen($s) - $keep * 2)).mb_substr($s, -$keep);
    $flow = ['pending', 'confirmed', 'paid', 'shipped', 'completed'];
@endphp

@section('content')
<x-page-hero title="Lacak Pesanan" :crumbs="[['label' => 'Lacak Pesanan']]" subtitle="Masukkan nomor pesanan Anda (contoh: VRS-261010-AB12)." />

<section class="container-x max-w-3xl py-10">
    <form method="GET" class="flex gap-2">
        <label for="code" class="sr-only">Nomor pesanan</label>
        <input id="code" name="code" value="{{ $code }}" required placeholder="VRS-XXXXXX-XXXX" class="input !rounded-full font-mono uppercase">
        <button class="btn-primary shrink-0"><x-icon name="search" class="size-4" /> Lacak</button>
    </form>

    @if ($code && ! $order)
        <div class="card mt-8 p-8 text-center">
            <p class="font-semibold text-forest-900">Pesanan <span class="font-mono">{{ $code }}</span> tidak ditemukan.</p>
            <p class="mt-1 text-sm text-forest-900/60">Periksa kembali nomor pesanan atau hubungi admin kami.</p>
        </div>
    @elseif ($order)
        <div class="card mt-8 p-6 sm:p-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm text-forest-900/50">Nomor Pesanan</p>
                    <p class="font-mono text-xl font-bold text-forest-900">{{ $order->code }}</p>
                </div>
                <span class="rounded-full px-3 py-1.5 text-sm font-bold {{ $order->statusColor() }}">{{ $order->statusLabel() }}</span>
            </div>

            @if ($order->status !== 'cancelled')
                @php $current = array_search($order->status, $flow); @endphp
                <ol class="mt-8 grid grid-cols-5 gap-1">
                    @foreach ($flow as $i => $status)
                        <li class="text-center">
                            <div class="h-1.5 rounded-full {{ $i <= $current ? 'bg-forest-600' : 'bg-forest-100' }}"></div>
                            <p class="mt-2 text-[0.65rem] font-semibold sm:text-xs {{ $i <= $current ? 'text-forest-800' : 'text-forest-900/40' }}">{{ \App\Models\Order::STATUSES[$status] }}</p>
                        </li>
                    @endforeach
                </ol>
            @endif

            <dl class="mt-8 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                <div><dt class="text-forest-900/50">Tanggal</dt><dd class="font-semibold">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</dd></div>
                <div><dt class="text-forest-900/50">Penerima</dt><dd class="font-semibold">{{ $mask($order->customer_name, 2) }} · {{ $mask($order->customer_phone) }}</dd></div>
                <div><dt class="text-forest-900/50">Tujuan</dt><dd class="font-semibold">{{ $order->city }}, {{ $order->province }}</dd></div>
                <div><dt class="text-forest-900/50">Total</dt><dd class="font-semibold">{{ rupiah($order->total) }}</dd></div>
            </dl>
            <ul class="mt-6 divide-y divide-forest-900/5 border-t border-forest-900/10">
                @foreach ($order->items as $item)
                    <li class="flex justify-between py-3 text-sm"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><span class="font-semibold">{{ rupiah($item->subtotal) }}</span></li>
                @endforeach
            </ul>
            <a href="{{ wa_link("Halo Admin, saya ingin menanyakan status pesanan {$order->code}") }}" target="_blank" rel="noopener" class="btn-wa mt-6 w-full sm:w-auto"><x-icon name="whatsapp" class="size-4" /> Tanya Status ke Admin</a>
        </div>
    @endif
</section>
@endsection
