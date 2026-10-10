@props(['title', 'subtitle' => null, 'eyebrow' => null, 'crumbs' => []])
<section class="relative overflow-hidden border-b border-forest-900/5 bg-gradient-to-b from-forest-50 to-cream">
    <div class="pointer-events-none absolute -top-24 -right-24 size-72 rounded-full bg-gold-200/40 blur-3xl"></div>
    <div class="container-x relative py-10 sm:py-14">
        <x-breadcrumb :items="$crumbs" />
        @if ($eyebrow)<p class="eyebrow mt-6">{{ $eyebrow }}</p>@endif
        <h1 class="mt-3 max-w-3xl font-display text-3xl font-semibold text-forest-900 sm:text-5xl">{{ $title }}</h1>
        @if ($subtitle)<p class="mt-3 max-w-2xl text-forest-900/70 sm:text-lg">{{ $subtitle }}</p>@endif
    </div>
</section>
