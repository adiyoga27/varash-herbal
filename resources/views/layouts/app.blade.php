@php
    $siteName = setting('site_name', config('app.name'));
    $metaTitle = trim($__env->yieldContent('title'));
    $metaTitle = $metaTitle ? $metaTitle.' | '.$siteName : setting('meta_title', $siteName);
    $metaDescription = trim($__env->yieldContent('description')) ?: setting('meta_description');
    $metaImage = trim($__env->yieldContent('og_image')) ?: asset('images/hero.webp');
    $canonical = trim($__env->yieldContent('canonical')) ?: url()->current();
    $nav = [
        ['label' => 'Beranda', 'route' => 'home', 'active' => 'home'],
        ['label' => 'Produk', 'route' => 'products.index', 'active' => 'products.*'],
        ['label' => 'Artikel', 'route' => 'articles.index', 'active' => 'articles.*'],
        ['label' => 'Tentang Kami', 'route' => 'about', 'active' => 'about'],
        ['label' => 'FAQ', 'route' => 'faq', 'active' => 'faq'],
        ['label' => 'Kontak', 'route' => 'contact', 'active' => 'contact'],
    ];
    $socials = collect(['instagram', 'facebook', 'tiktok', 'youtube'])->filter(fn ($s) => setting($s));
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="cart-count" content="{{ $cartCount ?? 0 }}">
    <meta name="theme-color" content="#1e5438">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    @if (setting('meta_keywords'))<meta name="keywords" content="{{ setting('meta_keywords') }}">@endif
    <meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large')">
    <link rel="canonical" href="{{ $canonical }}">
    @if (setting('google_site_verification'))<meta name="google-site-verification" content="{{ setting('google_site_verification') }}">@endif

    {{-- Open Graph & Twitter --}}
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    @stack('meta')

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    @stack('preload')

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Structured data: Organization + WebSite (sitelinks search) --}}
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => url('/').'#organization',
                'name' => setting('company_name', $siteName),
                'alternateName' => $siteName,
                'url' => url('/'),
                'logo' => asset('images/logo.png'),
                'contactPoint' => ['@type' => 'ContactPoint', 'telephone' => '+'.phone_to_wa(setting('whatsapp_number')), 'contactType' => 'customer service', 'availableLanguage' => 'Indonesian'],
                'address' => ['@type' => 'PostalAddress', 'streetAddress' => setting('address'), 'addressCountry' => 'ID'],
                'sameAs' => $socials->map(fn ($s) => setting($s))->values(),
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('/').'#website',
                'url' => url('/'),
                'name' => $siteName,
                'inLanguage' => 'id-ID',
                'publisher' => ['@id' => url('/').'#organization'],
                'potentialAction' => ['@type' => 'SearchAction', 'target' => route('products.index').'?q={search_term_string}', 'query-input' => 'required name=search_term_string'],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @stack('schema')

    @if ($ga = setting('google_analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ $ga }}');</script>
    @endif

    <script>
        window.routes = {
            cartAdd: @json(route('cart.add')),
            cartBase: @json(url('/keranjang')),
            subscribe: @json(route('newsletter.subscribe')),
        };
    </script>
</head>
<body class="min-h-dvh font-sans" x-data="{ mobileNav: false, search: false }" :class="{ 'overflow-hidden': mobileNav }">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow">Lewati ke konten utama</a>

    {{-- Topbar --}}
    <div class="hidden bg-forest-900 text-forest-100 md:block">
        <div class="container-x flex h-9 items-center justify-between text-xs">
            <p class="flex items-center gap-2"><x-icon name="truck" class="size-4 text-gold-300" /> Gratis konsultasi & pengiriman ke seluruh Indonesia</p>
            <div class="flex items-center gap-5">
                <a href="{{ route('order.track') }}" class="hover:text-white">Lacak Pesanan</a>
                <a href="{{ wa_link('Halo, saya ingin bergabung menjadi agen Varash') }}" target="_blank" rel="noopener" class="hover:text-white">Jadi Agen</a>
                <div class="flex items-center gap-3">
                    @foreach ($socials as $s)
                        <a href="{{ setting($s) }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($s) }}" class="hover:text-gold-300"><x-icon :name="$s" class="size-3.5" /></a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-forest-900/5 bg-cream/85 backdrop-blur-xl supports-[backdrop-filter]:bg-cream/70">
        <div class="container-x flex h-16 items-center gap-4 lg:h-20">
            <button type="button" @click="mobileNav = true" class="-ml-2 rounded-xl p-2 text-forest-900 hover:bg-forest-100 lg:hidden" aria-label="Buka menu">
                <x-icon name="menu" class="size-6" />
            </button>

            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ $siteName }} - Beranda">
                <img src="{{ asset('images/logo.png') }}" alt="Logo {{ $siteName }}" width="44" height="43" class="size-10 lg:size-11">
                <span class="leading-none">
                    <span class="block font-display text-xl font-semibold text-forest-900 lg:text-2xl">{{ $siteName }}</span>
                    <span class="hidden text-[0.65rem] font-semibold tracking-[0.2em] text-gold-600 uppercase sm:block">{{ setting('site_tagline') }}</span>
                </span>
            </a>

            <nav class="ml-6 hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}" @class([
                        'rounded-full px-4 py-2 text-sm font-semibold transition',
                        'bg-forest-100 text-forest-800' => request()->routeIs($item['active']),
                        'text-forest-900/70 hover:text-forest-900 hover:bg-forest-50' => ! request()->routeIs($item['active']),
                    ]) @if (request()->routeIs($item['active'])) aria-current="page" @endif>{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <div class="ml-auto flex items-center gap-1 sm:gap-2">
                <button type="button" @click="search = !search; $nextTick(() => $refs.searchInput?.focus())" class="rounded-full p-2.5 text-forest-900 hover:bg-forest-100" aria-label="Cari produk">
                    <x-icon name="search" />
                </button>
                <a href="{{ route('cart.index') }}" class="relative rounded-full p-2.5 text-forest-900 hover:bg-forest-100" aria-label="Keranjang belanja">
                    <x-icon name="cart" />
                    <span x-cloak x-show="$store.cart.count > 0" x-text="$store.cart.count" :class="$store.cart.bump && 'scale-125'"
                          class="absolute -top-0.5 -right-0.5 grid min-w-5 place-items-center rounded-full bg-gold-400 px-1 text-[0.65rem] font-bold text-forest-950 ring-2 ring-cream transition-transform"></span>
                </a>
                <a href="{{ wa_link('Halo Admin, saya ingin konsultasi produk Varash') }}" target="_blank" rel="noopener" class="btn-wa ml-1 hidden !py-2.5 sm:inline-flex">
                    <x-icon name="whatsapp" class="size-4" /> Konsultasi
                </a>
            </div>
        </div>

        {{-- Search bar --}}
        <div x-cloak x-show="search" x-collapse class="border-t border-forest-900/5 bg-cream">
            <form action="{{ route('products.index') }}" method="GET" role="search" class="container-x flex gap-2 py-3">
                <label for="site-search" class="sr-only">Cari produk</label>
                <input id="site-search" x-ref="searchInput" type="search" name="q" value="{{ request('q') }}" placeholder="Cari minyak herbal, suplemen, perawatan…" class="input !rounded-full">
                <button class="btn-primary shrink-0">Cari</button>
            </form>
        </div>
    </header>

    {{-- Mobile drawer --}}
    <div x-cloak x-show="mobileNav" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Menu">
        <div x-show="mobileNav" x-transition.opacity @click="mobileNav = false" class="absolute inset-0 bg-forest-950/50 backdrop-blur-sm"></div>
        <aside x-show="mobileNav" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="-translate-x-full" x-transition:leave="transition duration-200 ease-in" x-transition:leave-end="-translate-x-full"
               @keydown.escape.window="mobileNav = false" class="absolute inset-y-0 left-0 flex w-[85%] max-w-sm flex-col bg-cream shadow-2xl">
            <div class="flex items-center justify-between border-b border-forest-900/5 p-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="size-9">
                    <span class="font-display text-xl font-semibold text-forest-900">{{ $siteName }}</span>
                </a>
                <button @click="mobileNav = false" class="rounded-xl p-2 hover:bg-forest-100" aria-label="Tutup menu"><x-icon name="x" /></button>
            </div>
            <nav class="flex-1 space-y-1 overflow-y-auto p-4" aria-label="Navigasi mobile">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}" @class([
                        'flex items-center justify-between rounded-2xl px-4 py-3.5 text-base font-semibold',
                        'bg-forest-700 text-white' => request()->routeIs($item['active']),
                        'text-forest-900 hover:bg-forest-100' => ! request()->routeIs($item['active']),
                    ])>{{ $item['label'] }} <x-icon name="chevron-right" class="size-4 opacity-50" /></a>
                @endforeach
                <a href="{{ route('order.track') }}" class="flex items-center justify-between rounded-2xl px-4 py-3.5 text-base font-semibold text-forest-900 hover:bg-forest-100">Lacak Pesanan <x-icon name="chevron-right" class="size-4 opacity-50" /></a>
            </nav>
            <div class="space-y-3 border-t border-forest-900/5 p-4">
                <a href="{{ wa_link('Halo Admin, saya ingin konsultasi produk Varash') }}" target="_blank" rel="noopener" class="btn-wa w-full"><x-icon name="whatsapp" class="size-5" /> Konsultasi Gratis</a>
                <div class="flex justify-center gap-4 text-forest-700">
                    @foreach ($socials as $s)
                        <a href="{{ setting($s) }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($s) }}" class="rounded-full bg-forest-100 p-2.5"><x-icon :name="$s" class="size-4" /></a>
                    @endforeach
                </div>
            </div>
        </aside>
    </div>

    <main id="main">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="relative mt-24 overflow-hidden bg-forest-950 text-forest-100">
        <div class="pointer-events-none absolute -top-32 -right-32 size-96 rounded-full bg-forest-700/30 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-20 size-96 rounded-full bg-gold-500/10 blur-3xl"></div>

        {{-- Newsletter --}}
        <div class="container-x relative">
            <div class="relative -mb-px grid grid-cols-1 gap-6 border-b border-white/10 py-12 lg:grid-cols-2 lg:items-center lg:py-16">
                <div>
                    <p class="text-sm font-bold tracking-[0.18em] text-gold-300 uppercase">Newsletter</p>
                    <h2 class="mt-2 text-3xl font-semibold text-white sm:text-4xl">Tips sehat alami & promo eksklusif</h2>
                    <p class="mt-3 max-w-lg text-forest-200/80">Langganan gratis dan dapatkan info produk baru, artikel kesehatan, dan penawaran khusus subscriber langsung di email Anda.</p>
                </div>
                <form x-data="newsletter('footer')" @submit.prevent="submit" class="w-full lg:justify-self-end lg:max-w-md">
                    <label for="footer-email" class="sr-only">Alamat email</label>
                    <div class="flex flex-col gap-2 rounded-3xl bg-white/5 p-2 ring-1 ring-white/10 sm:flex-row sm:rounded-full">
                        <input id="footer-email" x-model="email" type="email" required placeholder="nama@email.com" autocomplete="email"
                               class="min-w-0 flex-1 rounded-full bg-transparent px-5 py-3 text-white placeholder:text-forest-200/50 focus:outline-none">
                        <button type="submit" class="btn-gold shrink-0" :disabled="loading">
                            <span x-show="!loading">Berlangganan</span><span x-cloak x-show="loading">Memproses…</span>
                            <x-icon name="send" class="size-4" />
                        </button>
                    </div>
                    <p class="mt-3 flex items-center gap-1.5 px-2 text-xs text-forest-200/60"><x-icon name="lock" class="size-3.5" /> Kami menghargai privasi Anda. Berhenti berlangganan kapan saja.</p>
                </form>
            </div>
        </div>

        <div class="container-x relative grid grid-cols-1 gap-10 py-14 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="size-12 rounded-full bg-white p-1" loading="lazy">
                    <span class="font-display text-2xl font-semibold text-white">{{ $siteName }}</span>
                </a>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-forest-200/80">{{ setting('about_short') }}</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">✓ BPOM</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">✓ Halal MUI</span>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">✓ GMP</span>
                </div>
            </div>
            <div class="lg:col-span-2">
                <h3 class="font-sans text-sm font-bold tracking-wider text-white uppercase">Belanja</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('products.index') }}" class="hover:text-gold-300">Semua Produk</a></li>
                    @foreach (\App\Models\Category::active()->take(5)->get() as $cat)
                        <li><a href="{{ route('products.category', $cat) }}" class="hover:text-gold-300">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="lg:col-span-2">
                <h3 class="font-sans text-sm font-bold tracking-wider text-white uppercase">Informasi</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-gold-300">Tentang Kami</a></li>
                    <li><a href="{{ route('articles.index') }}" class="hover:text-gold-300">Artikel Kesehatan</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-gold-300">FAQ</a></li>
                    <li><a href="{{ route('order.track') }}" class="hover:text-gold-300">Lacak Pesanan</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-gold-300">Kebijakan Privasi</a></li>
                </ul>
            </div>
            <div class="lg:col-span-4">
                <h3 class="font-sans text-sm font-bold tracking-wider text-white uppercase">Hubungi Kami</h3>
                <ul class="mt-4 space-y-3 text-sm">
                    <li class="flex gap-3"><x-icon name="map" class="mt-0.5 size-4 shrink-0 text-gold-300" /><span>{{ setting('address') }}</span></li>
                    <li><a href="{{ wa_link() }}" target="_blank" rel="noopener" class="flex gap-3 hover:text-gold-300"><x-icon name="whatsapp" class="size-4 shrink-0 text-gold-300" />{{ setting('phone') }} ({{ setting('whatsapp_admin_name') }})</a></li>
                    @if (setting('email'))<li><a href="mailto:{{ setting('email') }}" class="flex gap-3 hover:text-gold-300"><x-icon name="mail" class="size-4 shrink-0 text-gold-300" />{{ setting('email') }}</a></li>@endif
                    <li class="flex gap-3"><x-icon name="clock" class="size-4 shrink-0 text-gold-300" />{{ setting('business_hours') }}</li>
                </ul>
                <div class="mt-5 flex gap-2">
                    @foreach ($socials as $s)
                        <a href="{{ setting($s) }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($s) }}" class="grid size-10 place-items-center rounded-full bg-white/10 transition hover:bg-gold-400 hover:text-forest-950"><x-icon :name="$s" class="size-4" /></a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="relative border-t border-white/10">
            <div class="container-x flex flex-col gap-2 py-6 text-xs text-forest-200/60 sm:flex-row sm:items-center sm:justify-between">
                <p>© {{ date('Y') }} {{ setting('company_name', $siteName) }}. Semua hak dilindungi.</p>
                <p>Produk herbal ini bukan pengganti diagnosis atau pengobatan dokter.</p>
            </div>
        </div>
    </footer>

    {{-- Floating WhatsApp --}}
    <a href="{{ wa_link('Halo Admin, saya ingin bertanya tentang produk Varash') }}" target="_blank" rel="noopener" aria-label="Chat WhatsApp"
       class="group fixed right-4 bottom-4 z-30 flex items-center gap-2 rounded-full bg-[#25D366] p-3.5 text-white shadow-xl shadow-green-900/25 transition hover:scale-105 sm:right-6 sm:bottom-6 @yield('wa_float_class')">
        <span class="absolute inset-0 -z-10 animate-ping rounded-full bg-[#25D366]/40"></span>
        <x-icon name="whatsapp" class="size-7" />
        <span class="hidden max-w-0 overflow-hidden pr-0 text-sm font-semibold whitespace-nowrap transition-all duration-300 group-hover:max-w-40 group-hover:pr-2 sm:block">Chat Admin</span>
    </a>

    {{-- Toast --}}
    <div class="pointer-events-none fixed inset-x-0 top-20 z-[60] flex flex-col items-center gap-2 px-4 sm:top-auto sm:right-6 sm:bottom-24 sm:left-auto sm:items-end" aria-live="polite">
        <template x-for="t in $store.toast.items" :key="t.id">
            <div x-transition:enter="transition duration-300" x-transition:enter-start="translate-y-3 opacity-0" x-transition:leave="transition duration-200" x-transition:leave-end="opacity-0"
                 class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-2xl bg-forest-950 px-4 py-3.5 text-sm text-white shadow-2xl ring-1 ring-white/10">
                <span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full" :class="t.type === 'error' ? 'bg-red-500' : 'bg-forest-400'">
                    <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path :d="t.type === 'error' ? 'M18 6 6 18M6 6l12 12' : 'M20 6 9 17l-5-5'"/></svg>
                </span>
                <p class="flex-1" x-text="t.message"></p>
                <button @click="$store.toast.dismiss(t.id)" class="opacity-60 hover:opacity-100" aria-label="Tutup">✕</button>
            </div>
        </template>
    </div>

    @if (session('success') || session('error'))
        <script>
            document.addEventListener('alpine:initialized', () => Alpine.store('toast').show(@json(session('success') ?? session('error')), @json(session('error') ? 'error' : 'success')));
        </script>
    @endif
    @stack('scripts')
</body>
</html>
