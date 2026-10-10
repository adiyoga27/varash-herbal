@props(['product', 'eager' => false])
<article class="group card relative flex flex-col overflow-hidden p-2.5 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-forest-900/10 sm:p-3">
    <a href="{{ route('products.show', $product) }}" class="relative block aspect-square overflow-hidden rounded-[1.25rem] bg-gradient-to-br from-forest-50 via-cream to-gold-50" aria-label="{{ $product->name }}">
        <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" width="400" height="400"
             loading="{{ $eager ? 'eager' : 'lazy' }}" decoding="async"
             class="size-full object-contain p-3 transition duration-500 group-hover:scale-105">
        <div class="absolute top-2.5 left-2.5 flex flex-col gap-1.5">
            @if ($product->hasDiscount())
                <span class="rounded-full bg-red-500 px-2.5 py-1 text-[0.65rem] font-bold text-white">-{{ $product->discountPercent() }}%</span>
            @endif
            @if ($product->is_featured)
                <span class="rounded-full bg-gold-400 px-2.5 py-1 text-[0.65rem] font-bold text-forest-950">Best Seller</span>
            @endif
        </div>
    </a>
    <div class="flex flex-1 flex-col px-1.5 pt-3 pb-1 sm:px-2">
        @if ($product->category)
            <p class="text-[0.7rem] font-bold tracking-wider text-forest-500 uppercase">{{ $product->category->name }}</p>
        @endif
        <h3 class="mt-1 font-sans text-sm leading-snug font-bold text-forest-950 sm:text-base">
            <a href="{{ route('products.show', $product) }}" class="line-clamp-2 hover:text-forest-700">{{ $product->name }}</a>
        </h3>
        @if ($product->size)<p class="mt-0.5 text-xs text-forest-900/50">{{ $product->size }}</p>@endif
        <div class="mt-auto flex items-end justify-between gap-2 pt-3">
            <div>
                @if ($product->hasDiscount())
                    <p class="text-xs text-forest-900/40 line-through">{{ rupiah($product->price) }}</p>
                @endif
                <p class="text-[0.95rem] font-extrabold whitespace-nowrap text-forest-800 sm:text-lg">{{ rupiah($product->finalPrice()) }}</p>
            </div>
            <button type="button" x-data="addToCart({{ $product->id }})" @click="add" :disabled="loading"
                    class="grid size-9 shrink-0 place-items-center rounded-full bg-forest-700 text-white shadow-md transition hover:bg-forest-800 active:scale-95 disabled:opacity-60 sm:size-11"
                    aria-label="Tambah {{ $product->name }} ke keranjang">
                <x-icon name="plus" class="size-5" x-show="!loading" />
                <svg x-cloak x-show="loading" class="size-5 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3"/></svg>
            </button>
        </div>
    </div>
</article>
