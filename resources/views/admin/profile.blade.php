@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
<form method="POST" action="{{ route('admin.profile.update') }}" class="grid max-w-4xl grid-cols-1 gap-6 lg:grid-cols-2">
    @csrf @method('PUT')
    <section class="a-card space-y-4 p-5 sm:p-6">
        <h2 class="font-bold text-slate-900">Informasi Akun</h2>
        <div><label for="name" class="a-label">Nama</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required class="a-input"></div>
        <div><label for="email" class="a-label">Email</label><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="a-input"></div>
        <p class="text-xs text-slate-500">Login terakhir: {{ $user->last_login_at?->translatedFormat('d M Y H:i') ?? '-' }} dari {{ $user->last_login_ip ?? '-' }}</p>
    </section>
    <section class="a-card space-y-4 p-5 sm:p-6">
        <h2 class="font-bold text-slate-900">Ganti Password</h2>
        <div><label for="current_password" class="a-label">Password Saat Ini</label><input id="current_password" type="password" name="current_password" autocomplete="current-password" class="a-input"></div>
        <div><label for="password" class="a-label">Password Baru</label><input id="password" type="password" name="password" autocomplete="new-password" class="a-input"></div>
        <div><label for="password_confirmation" class="a-label">Konfirmasi Password Baru</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="a-input"></div>
    </section>
    <div class="lg:col-span-2"><button class="a-btn-primary">Simpan Profil</button></div>
</form>
@endsection
