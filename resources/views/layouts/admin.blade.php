@php
    $user = auth()->user();
    $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
    $unreadMessages = \App\Models\ContactMessage::where('is_read', false)->count();
    $menu = [
        'Utama' => [
            ['Dashboard', 'admin.dashboard', 'grid', 'admin.dashboard', null],
            ['Pesanan', 'admin.orders.index', 'package', 'admin.orders.*', $pendingOrders],
        ],
        'Katalog' => [
            ['Produk', 'admin.products.index', 'box', 'admin.products.*', null],
            ['Kategori', 'admin.categories.index', 'tag', 'admin.categories.*', null],
        ],
        'Konten' => [
            ['Artikel', 'admin.articles.index', 'file', 'admin.articles.*', null],
            ['Testimoni', 'admin.testimonials.index', 'star', 'admin.testimonials.*', null],
            ['FAQ', 'admin.faqs.index', 'question', 'admin.faqs.*', null],
        ],
        'Marketing' => [
            ['Subscriber', 'admin.subscribers.index', 'users', 'admin.subscribers.*', null],
            ['Newsletter', 'admin.newsletters.index', 'send', 'admin.newsletters.*', null],
            ['Pesan Masuk', 'admin.messages.index', 'mail', 'admin.messages.*', $unreadMessages],
        ],
        'Sistem' => array_filter([
            ['Activity Log', 'admin.logs.index', 'activity', 'admin.logs.*', null],
            $user->isSuperAdmin() ? ['System Log', 'admin.system-logs', 'terminal', 'admin.system-logs*', null] : null,
            $user->isSuperAdmin() ? ['Pengguna', 'admin.users.index', 'user', 'admin.users.*', null] : null,
            $user->isSuperAdmin() ? ['Pengaturan', 'admin.settings', 'settings', 'admin.settings*', null] : null,
        ]),
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') · Admin {{ setting('site_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-dvh bg-slate-50 font-sans text-slate-800" x-data="{ sidebar: false }" :class="{ 'overflow-hidden lg:overflow-auto': sidebar }">
    {{-- Overlay mobile --}}
    <div x-cloak x-show="sidebar" x-transition.opacity @click="sidebar = false" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-forest-950 text-forest-100 transition-transform duration-300 lg:translate-x-0"
           :class="sidebar && '!translate-x-0'" @keydown.escape.window="sidebar = false">
        <div class="flex h-16 shrink-0 items-center justify-between px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.png') }}" alt="" class="size-9 rounded-full bg-white p-0.5">
                <div class="leading-tight">
                    <p class="font-display text-lg font-semibold text-white">{{ setting('site_name') }}</p>
                    <p class="text-[0.65rem] font-semibold tracking-[0.2em] text-gold-300 uppercase">Admin Panel</p>
                </div>
            </a>
            <button @click="sidebar = false" class="rounded-lg p-1.5 hover:bg-white/10 lg:hidden" aria-label="Tutup menu"><x-icon name="x" /></button>
        </div>
        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4" aria-label="Menu admin">
            @foreach ($menu as $group => $items)
                <div>
                    <p class="px-3 text-[0.65rem] font-bold tracking-[0.18em] text-forest-300/50 uppercase">{{ $group }}</p>
                    <ul class="mt-2 space-y-0.5">
                        @foreach ($items as [$label, $route, $icon, $pattern, $badge])
                            @php $active = request()->routeIs($pattern); @endphp
                            <li>
                                <a href="{{ route($route) }}" @class([
                                    'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                                    'bg-forest-700 text-white shadow-lg shadow-black/20' => $active,
                                    'text-forest-100/70 hover:bg-white/5 hover:text-white' => ! $active,
                                ])>
                                    <x-icon :name="$icon" class="size-[1.15rem]" />
                                    <span class="flex-1">{{ $label }}</span>
                                    @if ($badge)<span class="rounded-full bg-gold-400 px-2 py-0.5 text-[0.65rem] font-bold text-forest-950">{{ $badge }}</span>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>
        <div class="border-t border-white/10 p-3">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-forest-100/70 hover:bg-white/5 hover:text-white"><x-icon name="external" class="size-[1.15rem]" /> Lihat Website</a>
        </div>
    </aside>

    <div class="lg:pl-72">
        {{-- Topbar --}}
        <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200/70 bg-white/85 px-4 backdrop-blur-xl sm:px-6">
            <button @click="sidebar = true" class="a-icon-btn -ml-1 lg:hidden" aria-label="Buka menu"><x-icon name="menu" /></button>
            <div class="min-w-0 flex-1">
                <h1 class="truncate text-base font-bold text-slate-900 sm:text-lg">@yield('title', 'Dashboard')</h1>
            </div>

            {{-- Notifikasi --}}
            <div class="relative" x-data="notifications(@js(route('admin.notifications.feed')))" @click.outside="open = false">
                <button @click="open = !open" class="a-icon-btn relative" aria-label="Notifikasi">
                    <x-icon name="bell" />
                    <span x-cloak x-show="unread > 0" x-text="unread > 9 ? '9+' : unread" class="absolute top-0.5 right-0.5 grid min-w-4 place-items-center rounded-full bg-red-500 px-1 text-[0.6rem] font-bold text-white ring-2 ring-white"></span>
                </button>
                <div x-cloak x-show="open" x-transition.origin.top.right class="fixed inset-x-3 top-16 mt-1 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:absolute sm:inset-x-auto sm:top-auto sm:right-0 sm:w-96">
                    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                        <p class="font-bold text-slate-900">Notifikasi</p>
                        <form method="POST" action="{{ route('admin.notifications.readAll') }}">@csrf<button class="text-xs font-semibold text-forest-700 hover:underline">Tandai semua dibaca</button></form>
                    </div>
                    <ul class="max-h-96 divide-y divide-slate-100 overflow-y-auto">
                        <template x-if="items.length === 0"><li class="p-6 text-center text-sm text-slate-500">Belum ada notifikasi</li></template>
                        <template x-for="n in items" :key="n.id">
                            <li>
                                <a :href="n.url" class="flex gap-3 px-4 py-3 hover:bg-slate-50" :class="!n.read && 'bg-forest-50/60'">
                                    <span class="mt-0.5 grid size-9 shrink-0 place-items-center rounded-full"
                                          :class="{ 'bg-amber-100 text-amber-700': n.type === 'order', 'bg-sky-100 text-sky-700': n.type === 'subscriber', 'bg-violet-100 text-violet-700': n.type === 'message' }">
                                        <span x-text="n.type === 'order' ? '🛒' : (n.type === 'subscriber' ? '✉️' : '💬')"></span>
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-sm font-semibold text-slate-900" x-text="n.title"></span>
                                        <span class="block truncate text-xs text-slate-500" x-text="n.message"></span>
                                        <span class="mt-0.5 block text-[0.7rem] text-slate-400" x-text="n.time"></span>
                                    </span>
                                    <span x-show="!n.read" class="mt-2 size-2 shrink-0 rounded-full bg-forest-500"></span>
                                </a>
                            </li>
                        </template>
                    </ul>
                    <a href="{{ route('admin.notifications.index') }}" class="block border-t border-slate-100 py-3 text-center text-sm font-semibold text-forest-700 hover:bg-slate-50">Lihat semua</a>
                </div>
            </div>

            {{-- User menu --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="flex items-center gap-2 rounded-xl p-1 pr-2 hover:bg-slate-100" aria-label="Menu pengguna">
                    <span class="grid size-8 place-items-center rounded-lg bg-gradient-to-br from-forest-500 to-forest-700 text-xs font-bold text-white">{{ $user->initials() }}</span>
                    <span class="hidden text-left leading-tight sm:block">
                        <span class="block text-sm font-semibold text-slate-900">{{ $user->name }}</span>
                        <span class="block text-[0.7rem] text-slate-500">{{ \App\Models\User::ROLES[$user->role] ?? $user->role }}</span>
                    </span>
                    <x-icon name="chevron-down" class="hidden size-4 text-slate-400 sm:block" />
                </button>
                <div x-cloak x-show="open" x-transition.origin.top.right class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-xl">
                    <a href="{{ route('admin.profile') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50"><x-icon name="user" class="size-4" /> Profil Saya</a>
                    <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-slate-50"><x-icon name="bell" class="size-4" /> Notifikasi</a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="border-t border-slate-100">
                        @csrf
                        <button class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50"><x-icon name="logout" class="size-4" /> Keluar</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8">
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-semibold">Periksa kembali isian Anda:</p>
                    <ul class="mt-1 list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    {{-- Toast --}}
    <div class="pointer-events-none fixed inset-x-0 bottom-4 z-[60] flex flex-col items-center gap-2 px-4 sm:right-6 sm:left-auto sm:items-end" aria-live="polite">
        <template x-for="t in $store.toast.items" :key="t.id">
            <div x-transition class="pointer-events-auto flex w-full max-w-sm items-center gap-3 rounded-xl px-4 py-3 text-sm text-white shadow-2xl"
                 :class="{ 'bg-forest-800': t.type === 'success', 'bg-red-600': t.type === 'error', 'bg-slate-900': t.type === 'info' }">
                <span x-text="t.message"></span>
            </div>
        </template>
    </div>
    @if (session('success') || session('error'))
        <script>document.addEventListener('alpine:initialized', () => Alpine.store('toast').show(@json(session('success') ?? session('error')), @json(session('error') ? 'error' : 'success')));</script>
    @endif
    @stack('scripts')
</body>
</html>
