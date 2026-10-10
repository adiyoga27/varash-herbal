@extends('layouts.app')

@section('title', 'Hubungi Kami')
@section('description', 'Hubungi '.setting('company_name').' via WhatsApp '.setting('phone').' atau kunjungi kantor kami di '.setting('address').'.')

@section('content')
<x-page-hero title="Hubungi Kami" eyebrow="Kontak" :crumbs="[['label' => 'Kontak']]" subtitle="Konsultasi gratis, pertanyaan produk, atau pendaftaran agen — kami siap membantu." />

<section class="container-x grid grid-cols-1 gap-8 py-12 lg:grid-cols-5">
    <div class="space-y-4 lg:col-span-2">
        @foreach ([
            ['whatsapp', 'WhatsApp', setting('phone').' ('.setting('whatsapp_admin_name').')', wa_link('Halo Admin '.setting('site_name'))],
            ['mail', 'Email', setting('email'), 'mailto:'.setting('email')],
            ['map', 'Alamat', setting('address'), null],
            ['clock', 'Jam Operasional', setting('business_hours'), null],
        ] as [$icon, $label, $value, $href])
            @continue(! $value)
            <div class="card flex gap-4 p-5">
                <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-forest-100 text-forest-700"><x-icon :name="$icon" /></span>
                <div>
                    <p class="text-sm font-semibold text-forest-900/50">{{ $label }}</p>
                    @if ($href)<a href="{{ $href }}" target="_blank" rel="noopener" class="font-bold text-forest-900 hover:text-forest-700">{{ $value }}</a>@else<p class="font-bold text-forest-900">{{ $value }}</p>@endif
                </div>
            </div>
        @endforeach
        @if (setting('maps_embed'))
            <iframe src="{{ setting('maps_embed') }}" title="Lokasi kantor" class="aspect-video w-full rounded-3xl border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        @endif
    </div>

    <form method="POST" action="{{ route('contact.send') }}" class="card space-y-5 p-6 sm:p-8 lg:col-span-3">
        @csrf
        <h2 class="font-sans text-2xl font-bold text-forest-900">Kirim Pesan</h2>
        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div><label for="name" class="label">Nama <span class="text-red-500">*</span></label><input id="name" name="name" value="{{ old('name') }}" required class="input" autocomplete="name">@error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
            <div><label for="phone" class="label">No. WhatsApp</label><input id="phone" name="phone" value="{{ old('phone') }}" class="input" inputmode="tel" autocomplete="tel"></div>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div><label for="email" class="label">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" class="input" autocomplete="email">@error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
            <div><label for="subject" class="label">Subjek</label><input id="subject" name="subject" value="{{ old('subject') }}" class="input"></div>
        </div>
        <div><label for="message" class="label">Pesan <span class="text-red-500">*</span></label><textarea id="message" name="message" rows="6" required class="input">{{ old('message') }}</textarea>@error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
        <button class="btn-primary w-full sm:w-auto"><x-icon name="send" class="size-4" /> Kirim Pesan</button>
    </form>
</section>
@endsection
