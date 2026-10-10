@extends('layouts.app')

@section('title', 'Pesanan Dibuat')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="container-x flex justify-center py-12 sm:py-20"
         x-data="{ seconds: 5, timer: null }"
         x-init="timer = setInterval(() => { if (--seconds <= 0) { clearInterval(timer); window.location.href = @js(route('checkout.whatsapp', $order->code)); } }, 1000)">
    <div class="card w-full max-w-2xl overflow-hidden">
        <div class="bg-gradient-to-br from-forest-700 to-forest-900 p-8 text-center text-white">
            <span class="mx-auto grid size-16 place-items-center rounded-full bg-white/15"><x-icon name="check-circle" class="size-9 text-gold-300" /></span>
            <h1 class="mt-4 text-3xl font-semibold">Pesanan Berhasil Dibuat!</h1>
            <p class="mt-2 text-forest-100">Nomor pesanan Anda: <strong class="font-mono text-gold-300">{{ $order->code }}</strong></p>
        </div>

        <div class="p-6 sm:p-8">
            <div class="rounded-2xl bg-[#25D366]/10 p-5 text-center">
                <p class="font-semibold text-forest-900">Langkah terakhir: kirim detail pesanan ke WhatsApp admin</p>
                <p class="mt-1 text-sm text-forest-900/60" x-show="seconds > 0">Anda akan diarahkan otomatis dalam <strong x-text="seconds"></strong> detik…</p>
                <a href="{{ route('checkout.whatsapp', $order->code) }}" @click="clearInterval(timer); seconds = 0" class="btn-wa mt-4 w-full !py-4 text-base sm:w-auto">
                    <x-icon name="whatsapp" class="size-5" /> Kirim Pesanan ke WhatsApp
                </a>
            </div>

            <h2 class="mt-8 font-sans text-sm font-bold tracking-wider text-forest-900/50 uppercase">Ringkasan Pesanan</h2>
            <ul class="mt-3 divide-y divide-forest-900/5">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4 py-3 text-sm"><span>{{ $item->product_name }} <span class="text-forest-900/50">× {{ $item->quantity }}</span></span><span class="font-semibold">{{ rupiah($item->subtotal) }}</span></li>
                @endforeach
                <li class="flex justify-between py-3 font-bold"><span>Total</span><span class="text-forest-800">{{ rupiah($order->total) }}</span></li>
            </ul>
            <div class="mt-4 grid grid-cols-1 gap-4 rounded-2xl bg-cream p-5 text-sm sm:grid-cols-2">
                <div><p class="text-forest-900/50">Penerima</p><p class="font-semibold">{{ $order->customer_name }} · {{ $order->customer_phone }}</p></div>
                <div><p class="text-forest-900/50">Pembayaran</p><p class="font-semibold">{{ $order->paymentLabel() }}</p></div>
                <div class="sm:col-span-2"><p class="text-forest-900/50">Alamat</p><p class="font-semibold">{{ $order->address }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p></div>
            </div>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('order.track', ['code' => $order->code]) }}" class="btn-outline flex-1">Lacak Pesanan</a>
                <a href="{{ route('products.index') }}" class="btn-outline flex-1">Belanja Lagi</a>
            </div>
        </div>
    </div>
</section>
@endsection
