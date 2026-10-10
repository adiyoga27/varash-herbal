@extends('layouts.app')

@section('title', 'FAQ — Pertanyaan Seputar Produk Varash')
@section('description', 'Jawaban atas pertanyaan yang sering diajukan seputar produk herbal Varash: cara pakai, BPOM & Halal, pengiriman, pembayaran, dan pendaftaran agen.')

@push('schema')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqs->map(fn ($f) => ['@type' => 'Question', 'name' => $f->question, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->answer]])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<x-page-hero title="Pertanyaan yang Sering Diajukan" eyebrow="FAQ" :crumbs="[['label' => 'FAQ']]" subtitle="Temukan jawaban cepat seputar produk, pemesanan, dan pengiriman." />

<section class="container-x max-w-3xl py-12" x-data="{ open: null }">
    <div class="space-y-3">
        @foreach ($faqs as $i => $faq)
            <div class="card overflow-hidden">
                <h2>
                    <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" :aria-expanded="open === {{ $i }}" class="flex w-full items-center justify-between gap-4 p-5 text-left font-sans text-base font-bold text-forest-900 sm:text-lg">
                        {{ $faq->question }}
                        <span class="grid size-8 shrink-0 place-items-center rounded-full bg-forest-50 transition" :class="open === {{ $i }} && 'rotate-45 bg-forest-700 text-white'"><x-icon name="plus" class="size-4" /></span>
                    </button>
                </h2>
                <div x-cloak x-show="open === {{ $i }}" x-collapse>
                    <p class="px-5 pb-5 leading-relaxed text-forest-900/70">{{ $faq->answer }}</p>
                </div>
            </div>
        @endforeach
    </div>
    <div class="card mt-10 flex flex-col items-center gap-4 p-8 text-center sm:flex-row sm:text-left">
        <span class="grid size-14 shrink-0 place-items-center rounded-2xl bg-[#25D366]/10 text-[#1a9e4b]"><x-icon name="whatsapp" class="size-7" /></span>
        <div class="flex-1"><h2 class="font-sans text-lg font-bold text-forest-900">Masih ada pertanyaan?</h2><p class="text-sm text-forest-900/60">Admin kami siap membantu {{ setting('business_hours') }}.</p></div>
        <a href="{{ wa_link('Halo Admin, saya punya pertanyaan') }}" target="_blank" rel="noopener" class="btn-wa">Chat Sekarang</a>
    </div>
</section>
@endsection
