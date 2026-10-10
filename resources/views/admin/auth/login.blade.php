<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Login Admin · {{ setting('site_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-dvh bg-forest-950 font-sans">
    <div class="grid min-h-dvh lg:grid-cols-2">
        <div class="relative hidden overflow-hidden lg:block">
            <img src="{{ asset('images/rempah.webp') }}" alt="" class="absolute inset-0 size-full object-cover opacity-60">
            <div class="absolute inset-0 bg-gradient-to-t from-forest-950 via-forest-950/40"></div>
            <div class="absolute inset-x-0 bottom-0 p-12 text-white">
                <p class="font-display text-4xl font-semibold">{{ setting('site_tagline') }}</p>
                <p class="mt-3 max-w-md text-forest-100/80">Kelola produk, pesanan, konten, dan pelanggan {{ setting('site_name') }} dari satu tempat.</p>
            </div>
        </div>
        <div class="flex items-center justify-center bg-slate-50 p-6">
            <div class="w-full max-w-sm">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="size-12">
                    <div><p class="font-display text-2xl font-semibold text-forest-900">{{ setting('site_name') }}</p><p class="text-xs font-semibold tracking-[0.2em] text-gold-600 uppercase">Admin Panel</p></div>
                </div>
                <h1 class="mt-10 text-2xl font-bold text-slate-900">Selamat datang kembali 👋</h1>
                <p class="mt-1 text-sm text-slate-500">Masuk untuk melanjutkan ke dashboard.</p>

                @if (session('success') || session('error'))
                    <p class="mt-6 rounded-xl px-4 py-3 text-sm {{ session('error') ? 'bg-red-50 text-red-700' : 'bg-forest-50 text-forest-800' }}">{{ session('success') ?? session('error') }}</p>
                @endif

                <form method="POST" action="{{ route('admin.login.attempt') }}" class="mt-8 space-y-5" x-data="{ show: false, loading: false }" @submit="loading = true">
                    @csrf
                    <div>
                        <label for="email" class="a-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="a-input !py-3">
                        @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password" class="a-label">Password</label>
                        <div class="relative">
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" class="a-input !py-3 pr-12">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 grid w-12 place-items-center text-slate-400 hover:text-slate-700" aria-label="Tampilkan password"><x-icon name="eye" class="size-4" /></button>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" class="rounded border-slate-300 text-forest-600 focus:ring-forest-500"> Ingat saya</label>
                    <button class="a-btn-primary w-full !py-3" :disabled="loading"><x-icon name="lock" class="size-4" /> <span x-text="loading ? 'Memproses…' : 'Masuk'">Masuk</span></button>
                </form>
                <p class="mt-8 text-center text-xs text-slate-400">Setiap aktivitas login tercatat demi keamanan.</p>
            </div>
        </div>
    </div>
</body>
</html>
