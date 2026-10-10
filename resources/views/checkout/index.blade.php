@extends('layouts.app')

@section('title', 'Checkout')
@section('robots', 'noindex, nofollow')

@section('content')
<x-page-hero title="Checkout" :crumbs="[['label' => 'Keranjang', 'url' => route('cart.index')], ['label' => 'Checkout']]" />

<section class="container-x py-10">
    {{-- Stepper --}}
    <ol class="mx-auto mb-10 flex max-w-xl items-center justify-between text-xs font-semibold sm:text-sm">
        @foreach (['Keranjang', 'Data Pengiriman', 'Kirim ke WhatsApp'] as $i => $step)
            <li class="flex flex-1 items-center gap-2 {{ $i < 2 ? 'text-forest-800' : 'text-forest-900/40' }}">
                <span class="grid size-8 shrink-0 place-items-center rounded-full {{ $i < 1 ? 'bg-forest-700 text-white' : ($i === 1 ? 'bg-gold-400 text-forest-950' : 'bg-white ring-1 ring-forest-900/10') }}">
                    @if ($i < 1)<x-icon name="check" class="size-4" />@else{{ $i + 1 }}@endif
                </span>
                <span class="hidden sm:inline">{{ $step }}</span>
                @if (! $loop->last)<span class="mx-2 h-px flex-1 bg-forest-900/10"></span>@endif
            </li>
        @endforeach
    </ol>

    <form method="POST" action="{{ route('checkout.store') }}" class="grid grid-cols-1 gap-8 lg:grid-cols-5" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        <div class="space-y-6 lg:col-span-3">
            <div class="card p-6 sm:p-8">
                <h2 class="flex items-center gap-2 font-sans text-lg font-bold text-forest-900"><x-icon name="user" class="size-5 text-forest-600" /> Data Pemesan</h2>
                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="customer_name" class="label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required autocomplete="name" class="input" placeholder="Nama penerima">
                        @error('customer_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="customer_phone" class="label">No. WhatsApp <span class="text-red-500">*</span></label>
                        <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required inputmode="tel" autocomplete="tel" class="input" placeholder="08xxxxxxxxxx">
                        @error('customer_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="customer_email" class="label">Email <span class="font-normal text-forest-900/40">(opsional)</span></label>
                        <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" autocomplete="email" class="input" placeholder="nama@email.com">
                        @error('customer_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="card p-6 sm:p-8">
                <h2 class="flex items-center gap-2 font-sans text-lg font-bold text-forest-900"><x-icon name="map" class="size-5 text-forest-600" /> Alamat Pengiriman</h2>
                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="address" class="label">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea id="address" name="address" rows="3" required autocomplete="street-address" class="input" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan">{{ old('address') }}</textarea>
                        @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="city" class="label">Kota / Kabupaten <span class="text-red-500">*</span></label>
                        <input id="city" name="city" value="{{ old('city') }}" required autocomplete="address-level2" class="input">
                        @error('city')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="province" class="label">Provinsi <span class="text-red-500">*</span></label>
                        <input id="province" name="province" value="{{ old('province') }}" required autocomplete="address-level1" class="input" list="provinces">
                        <datalist id="provinces">
                            @foreach (['Aceh','Sumatera Utara','Sumatera Barat','Riau','Kepulauan Riau','Jambi','Sumatera Selatan','Bangka Belitung','Bengkulu','Lampung','DKI Jakarta','Jawa Barat','Banten','Jawa Tengah','DI Yogyakarta','Jawa Timur','Bali','Nusa Tenggara Barat','Nusa Tenggara Timur','Kalimantan Barat','Kalimantan Tengah','Kalimantan Selatan','Kalimantan Timur','Kalimantan Utara','Sulawesi Utara','Gorontalo','Sulawesi Tengah','Sulawesi Barat','Sulawesi Selatan','Sulawesi Tenggara','Maluku','Maluku Utara','Papua','Papua Barat','Papua Selatan','Papua Tengah','Papua Pegunungan','Papua Barat Daya'] as $prov)
                                <option value="{{ $prov }}">
                            @endforeach
                        </datalist>
                        @error('province')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="postal_code" class="label">Kode Pos</label>
                        <input id="postal_code" name="postal_code" value="{{ old('postal_code') }}" inputmode="numeric" autocomplete="postal-code" class="input">
                    </div>
                </div>
            </div>

            <div class="card p-6 sm:p-8">
                <h2 class="flex items-center gap-2 font-sans text-lg font-bold text-forest-900"><x-icon name="wallet" class="size-5 text-forest-600" /> Metode Pembayaran</h2>
                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach (\App\Models\Order::PAYMENT_METHODS as $value => $label)
                        <label class="relative flex cursor-pointer flex-col gap-1 rounded-2xl border border-forest-900/10 bg-white p-4 transition has-[:checked]:border-forest-600 has-[:checked]:bg-forest-50 has-[:checked]:ring-4 has-[:checked]:ring-forest-500/10">
                            <input type="radio" name="payment_method" value="{{ $value }}" class="peer sr-only" @checked(old('payment_method', 'transfer') === $value)>
                            <span class="text-sm font-bold text-forest-900">{{ $label }}</span>
                            <x-icon name="check-circle" class="absolute top-3 right-3 size-5 text-forest-600 opacity-0 peer-checked:opacity-100" />
                        </label>
                    @endforeach
                </div>
                <div class="mt-5">
                    <label for="notes" class="label">Catatan <span class="font-normal text-forest-900/40">(opsional)</span></label>
                    <textarea id="notes" name="notes" rows="2" class="input" placeholder="Contoh: kirim sore hari, titip ke satpam">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <aside class="lg:col-span-2 lg:sticky lg:top-28 lg:self-start">
            <div class="card p-6">
                <h2 class="font-sans text-lg font-bold text-forest-900">Pesanan Anda</h2>
                <ul class="mt-4 divide-y divide-forest-900/5">
                    @foreach ($items as $item)
                        <li class="flex items-center gap-3 py-3">
                            <span class="relative shrink-0">
                                <img src="{{ $item->product->imageUrl() }}" alt="" width="56" height="56" class="size-14 rounded-xl bg-forest-50 object-contain">
                                <span class="absolute -top-1.5 -right-1.5 grid size-5 place-items-center rounded-full bg-forest-700 text-[0.65rem] font-bold text-white">{{ $item->qty }}</span>
                            </span>
                            <p class="flex-1 text-sm font-semibold text-forest-900">{{ $item->product->name }}</p>
                            <p class="text-sm font-bold">{{ rupiah($item->subtotal) }}</p>
                        </li>
                    @endforeach
                </ul>
                <dl class="mt-2 space-y-2 border-t border-forest-900/10 pt-4 text-sm">
                    <div class="flex justify-between"><dt class="text-forest-900/60">Subtotal</dt><dd class="font-semibold">{{ rupiah($subtotal) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-forest-900/60">Ongkos kirim</dt><dd class="text-forest-900/60">Dikonfirmasi admin</dd></div>
                    <div class="flex justify-between pt-2 text-lg"><dt class="font-bold">Total</dt><dd class="font-extrabold text-forest-800">{{ rupiah($subtotal) }}</dd></div>
                </dl>
                <button type="submit" class="btn-wa mt-6 w-full !py-4 text-base" :disabled="submitting">
                    <x-icon name="whatsapp" class="size-5" />
                    <span x-show="!submitting">Pesan via WhatsApp</span>
                    <span x-cloak x-show="submitting">Memproses pesanan…</span>
                </button>
                @if (setting('checkout_note'))
                    <p class="mt-4 rounded-2xl bg-forest-50 p-4 text-xs leading-relaxed text-forest-800">{{ setting('checkout_note') }}</p>
                @endif
            </div>
        </aside>
    </form>
</section>
@endsection
