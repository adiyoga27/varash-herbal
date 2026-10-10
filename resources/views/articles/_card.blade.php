<article class="group card reveal flex flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-xl">
    <a href="{{ route('articles.show', $article) }}" class="block aspect-[16/10] overflow-hidden bg-forest-50">
        <img src="{{ $article->imageUrl() }}" alt="{{ $article->title }}" width="600" height="375" loading="lazy" decoding="async" class="size-full object-cover transition duration-500 group-hover:scale-105">
    </a>
    <div class="flex flex-1 flex-col p-6">
        <p class="flex items-center gap-3 text-xs text-forest-900/50">
            <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->translatedFormat('d M Y') }}</time>
            <span>·</span><span>{{ $article->readingTime() }} menit baca</span>
        </p>
        <h3 class="mt-2 font-sans text-lg leading-snug font-bold text-forest-900">
            <a href="{{ route('articles.show', $article) }}" class="line-clamp-2 hover:text-forest-700">{{ $article->title }}</a>
        </h3>
        <p class="mt-2 line-clamp-3 text-sm text-forest-900/60">{{ $article->excerpt }}</p>
        <a href="{{ route('articles.show', $article) }}" class="mt-auto inline-flex items-center gap-1 pt-4 text-sm font-semibold text-forest-700 group-hover:gap-2">Baca selengkapnya <x-icon name="arrow-right" class="size-4 transition-all" /></a>
    </div>
</article>
