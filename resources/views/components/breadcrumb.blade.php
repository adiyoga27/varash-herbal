@props(['items' => []])
@php $all = array_merge([['label' => 'Beranda', 'url' => route('home')]], $items); @endphp
<nav aria-label="Breadcrumb" class="text-sm">
    <ol class="flex flex-wrap items-center gap-1.5 text-forest-900/60">
        @foreach ($all as $i => $crumb)
            <li class="flex items-center gap-1.5">
                @if ($i > 0)<x-icon name="chevron-right" class="size-3.5 opacity-50" />@endif
                @if (! empty($crumb['url']) && ! $loop->last)
                    <a href="{{ $crumb['url'] }}" class="hover:text-forest-800">{{ $crumb['label'] }}</a>
                @else
                    <span class="font-semibold text-forest-900" aria-current="page">{{ \Illuminate\Support\Str::limit($crumb['label'], 50) }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@push('schema')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => collect($all)->values()->map(fn ($c, $i) => array_filter([
        '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['label'], 'item' => $c['url'] ?? url()->current(),
    ]))->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
