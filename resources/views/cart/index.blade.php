@extends('layouts.app')

@section('title', 'Keranjang Belanja')
@section('robots', 'noindex, nofollow')

@section('content')
<x-page-hero title="Keranjang Belanja" :crumbs="[['label' => 'Keranjang']]" />

<section class="container-x py-10" x-data="{ subtotal: @js(rupiah($subtotal)), count: {{ $items->sum('qty') }} }"
         @cart-updated.window="subtotal = $event.detail.subtotal_formatted; count = $event.detail.count">
    @if ($items->isEmpty())
        <div class="card mx-auto flex max-w-lg flex-col items-center p-12 text-center">
            <span class="grid size-20 place-items-center rounded-full bg-forest-50 text-forest-400"><x-icon name="cart" class="size-10" /></span>
            <h2 class="mt-5 text-2xl font-semibold text-forest-900">Keranjang Anda masih kosong</h2>
            <p class="mt-2 text-forest-900/60">Yuk, temukan produk herbal yang cocok untuk Anda dan keluarga.</p>
            <a href="{{ route('products.index') }}" class="btn-primary mt-6">Mulai Belanja</a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-3 lg:col-span-2">
                @foreach ($items as $item)
                    <div x-data="cartItem({{ $item->product->id }}, {{ $item->qty }}, @js(rupiah($item->subtotal)))" x-show="!removed" x-transition.opacity
                         class="card flex gap-4 p-3 sm:items-center sm:p-4">
                        <a href="{{ route('products.show', $item->product) }}" class="shrink-0">
                            <img src="{{ $item->product->imageUrl() }}" alt="{{ $item->product->name }}" width="96" height="96" class="size-20 rounded-2xl bg-forest-50 object-contain p-1 sm:size-24">
                        </a>
                        <div class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('products.show', $item->product) }}" class="line-clamp-2 font-bold text-forest-900 hover:text-forest-700">{{ $item->product->name }}</a>
                                <p class="mt-0.5 text-sm text-forest-900/60">{{ rupiah($item->product->finalPrice()) }} @if ($item->product->size)· {{ $item->product->size }}@endif</p>
                            </div>
                            <div class="flex items-center justify-between gap-4 sm:justify-end">
                                <div class="flex items-center rounded-full bg-cream p-1 ring-1 ring-forest-900/10">
                                    <button type="button" @click="update(qty - 1)" class="grid size-8 place-items-center rounded-full hover:bg-white" aria-label="Kurangi"><x-icon name="minus" class="size-3.5" /></button>
                                    <span class="w-8 text-center text-sm font-bold" x-text="qty"></span>
                                    <button type="button" @click="update(qty + 1)" class="grid size-8 place-items-center rounded-full hover:bg-white" aria-label="Tambah"><x-icon name="plus" class="size-3.5" /></button>
                                </div>
                                <p class="w-28 text-right font-extrabold text-forest-800" x-text="subtotal"></p>
                                <button type="button" @click="update(0)" class="rounded-full p-2 text-forest-900/40 hover:bg-red-50 hover:text-red-600" aria-label="Hapus"><x-icon name="trash" class="size-4" /></button>
                            </div>
                        </div>
                    </div>
                @endforeach
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 pt-2 text-sm font-semibold text-forest-700 hover:text-forest-900"><x-icon name="arrow-left" class="size-4" /> Lanjut belanja</a>
            </div>

            <aside class="lg:sticky lg:top-28 lg:self-start">
                <div class="card p-6">
                    <h2 class="font-sans text-lg font-bold text-forest-900">Ringkasan Belanja</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-forest-900/60">Total item</dt><dd class="font-semibold" x-text="count + ' item'"></dd></div>
                        <div class="flex justify-between"><dt class="text-forest-900/60">Ongkos kirim</dt><dd class="text-forest-900/60">Dikonfirmasi admin</dd></div>
                        <div class="flex justify-between border-t border-forest-900/10 pt-3 text-base"><dt class="font-bold">Subtotal</dt><dd class="font-extrabold text-forest-800" x-text="subtotal"></dd></div>
                    </dl>
                    <template x-if="count > 0">
                        <a href="{{ route('checkout.index') }}" class="btn-primary mt-6 w-full !py-3.5">Lanjut ke Checkout <x-icon name="arrow-right" class="size-4" /></a>
                    </template>
                    <template x-if="count === 0">
                        <a href="{{ route('products.index') }}" class="btn-outline mt-6 w-full">Keranjang kosong — Belanja lagi</a>
                    </template>
                    <p class="mt-4 flex items-center justify-center gap-1.5 text-xs text-forest-900/50"><x-icon name="lock" class="size-3.5" /> Checkout aman via WhatsApp</p>
                </div>
            </aside>
        </div>
    @endif
</section>
@endsection
