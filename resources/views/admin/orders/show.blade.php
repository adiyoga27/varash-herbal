@extends('layouts.admin')

@section('title', 'Pesanan '.$order->code)

@section('content')
@php
    $followUp = "Halo {$order->customer_name}, terima kasih telah memesan di ".setting('site_name')." 🙏\n\nPesanan *{$order->code}* sudah kami terima dengan total ".rupiah($order->total).".\nStatus: *{$order->statusLabel()}*";
@endphp
<a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-900"><x-icon name="arrow-left" class="size-4" /> Kembali</a>

<div class="mt-4 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <div class="a-card p-5 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="font-mono text-lg font-bold text-slate-900">{{ $order->code }}</p>
                    <p class="text-sm text-slate-500">{{ $order->created_at->translatedFormat('l, d F Y H:i') }} · IP {{ $order->ip_address }}</p>
                </div>
                <span class="a-badge !px-3 !py-1 !text-sm {{ $order->statusColor() }}">{{ $order->statusLabel() }}</span>
            </div>
            <ul class="mt-5 divide-y divide-slate-100 border-y border-slate-100">
                @foreach ($order->items as $item)
                    <li class="flex items-center gap-3 py-3">
                        <img src="{{ $item->product?->imageUrl() ?? asset('images/logo.png') }}" alt="" class="size-12 shrink-0 rounded-lg bg-slate-100 object-contain">
                        <div class="min-w-0 flex-1"><p class="font-semibold text-slate-900">{{ $item->product_name }}</p><p class="text-xs text-slate-500">{{ $item->quantity }} × {{ rupiah($item->price) }}</p></div>
                        <p class="font-semibold">{{ rupiah($item->subtotal) }}</p>
                    </li>
                @endforeach
            </ul>
            <dl class="mt-4 space-y-1.5 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>{{ rupiah($order->subtotal) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Ongkos kirim</dt><dd>{{ rupiah($order->shipping_cost) }}</dd></div>
                <div class="flex justify-between pt-1 text-base font-bold"><dt>Total</dt><dd class="text-forest-700">{{ rupiah($order->total) }}</dd></div>
            </dl>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="a-card p-5">
                <h3 class="font-bold text-slate-900">Pelanggan</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div><dt class="text-slate-500">Nama</dt><dd class="font-semibold">{{ $order->customer_name }}</dd></div>
                    <div><dt class="text-slate-500">WhatsApp</dt><dd class="font-semibold">{{ $order->customer_phone }}</dd></div>
                    @if ($order->customer_email)<div><dt class="text-slate-500">Email</dt><dd class="font-semibold">{{ $order->customer_email }}</dd></div>@endif
                    <div><dt class="text-slate-500">Pembayaran</dt><dd class="font-semibold">{{ $order->paymentLabel() }}</dd></div>
                </dl>
                <a href="{{ wa_link($followUp, $order->customer_phone) }}" target="_blank" rel="noopener" class="a-btn mt-4 w-full bg-[#25D366] text-white hover:bg-[#1ebe5b]"><x-icon name="whatsapp" class="size-4" /> Chat Pelanggan</a>
            </div>
            <div class="a-card p-5">
                <h3 class="font-bold text-slate-900">Pengiriman</h3>
                <p class="mt-3 text-sm leading-relaxed">{{ $order->address }}<br>{{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
                @if ($order->notes)<p class="mt-3 rounded-lg bg-amber-50 p-3 text-sm text-amber-800"><strong>Catatan:</strong> {{ $order->notes }}</p>@endif
                <p class="mt-3 text-xs text-slate-500">WhatsApp dibuka pelanggan: {{ $order->whatsapp_sent_at?->translatedFormat('d M Y H:i') ?? 'belum' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="a-card space-y-4 p-5">
            @csrf @method('PUT')
            <h3 class="font-bold text-slate-900">Update Pesanan</h3>
            <div><label class="a-label">Status</label>
                <select name="status" class="a-input">@foreach (\App\Models\Order::STATUSES as $k => $v)<option value="{{ $k }}" @selected($order->status === $k)>{{ $v }}</option>@endforeach</select>
            </div>
            <div><label class="a-label">Ongkos Kirim (Rp)</label><input type="number" min="0" name="shipping_cost" value="{{ $order->shipping_cost }}" class="a-input"></div>
            <div><label class="a-label">Catatan Internal</label><textarea name="admin_notes" rows="3" class="a-input">{{ $order->admin_notes }}</textarea></div>
            <button class="a-btn-primary w-full">Simpan Perubahan</button>
        </form>

        <div class="a-card p-5">
            <h3 class="font-bold text-slate-900">Riwayat Pesanan</h3>
            <ol class="mt-4 space-y-4 border-l-2 border-slate-100 pl-4">
                @foreach ($logs as $log)
                    <li class="relative">
                        <span class="absolute top-1.5 -left-[1.3rem] size-2.5 rounded-full ring-4 ring-white {{ $log->levelDot() }}"></span>
                        <p class="text-sm text-slate-700">{{ $log->description }}</p>
                        @if ($log->event === 'updated' && isset($log->properties['attributes']['status']))
                            <p class="text-xs text-slate-500">Status: {{ \App\Models\Order::STATUSES[$log->properties['old']['status'] ?? ''] ?? '-' }} → <strong>{{ \App\Models\Order::STATUSES[$log->properties['attributes']['status']] ?? '' }}</strong></p>
                        @endif
                        <p class="text-xs text-slate-400">{{ $log->user?->name ?? 'Pelanggan' }} · {{ $log->created_at->translatedFormat('d M Y H:i') }}</p>
                    </li>
                @endforeach
            </ol>
        </div>

        <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" data-confirm="Hapus pesanan {{ $order->code }} secara permanen?">
            @csrf @method('DELETE')
            <button class="a-btn-light w-full text-red-600"><x-icon name="trash" class="size-4" /> Hapus Pesanan</button>
        </form>
    </div>
</div>
@endsection
