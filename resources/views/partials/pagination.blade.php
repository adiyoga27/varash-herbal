@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex items-center justify-center gap-1.5">
        @if ($paginator->onFirstPage())
            <span class="grid size-10 place-items-center rounded-full text-forest-900/30"><x-icon name="chevron-left" class="size-4" /></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="grid size-10 place-items-center rounded-full bg-white text-forest-800 shadow-sm ring-1 ring-forest-900/5 hover:bg-forest-50" aria-label="Sebelumnya"><x-icon name="chevron-left" class="size-4" /></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-1 text-forest-900/40">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="grid size-10 place-items-center rounded-full bg-forest-700 text-sm font-bold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="hidden size-10 place-items-center rounded-full bg-white text-sm font-semibold text-forest-800 shadow-sm ring-1 ring-forest-900/5 hover:bg-forest-50 sm:grid">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="grid size-10 place-items-center rounded-full bg-white text-forest-800 shadow-sm ring-1 ring-forest-900/5 hover:bg-forest-50" aria-label="Berikutnya"><x-icon name="chevron-right" class="size-4" /></a>
        @else
            <span class="grid size-10 place-items-center rounded-full text-forest-900/30"><x-icon name="chevron-right" class="size-4" /></span>
        @endif
    </nav>
@endif
