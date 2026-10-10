@extends('layouts.admin')

@section('title', 'Buat Newsletter')

@section('content')
<form method="POST" action="{{ route('admin.newsletters.store') }}" class="grid grid-cols-1 gap-6 xl:grid-cols-3" x-data="{ subject: @js(old('subject', '')), body: @js(old('body', '')) }">
    @csrf
    <div class="a-card space-y-5 p-5 sm:p-6 xl:col-span-2">
        <div><label for="subject" class="a-label">Subjek Email *</label><input id="subject" name="subject" x-model="subject" required class="a-input" placeholder="Promo spesial akhir bulan 🌿"></div>
        <div>
            <label for="body" class="a-label">Isi Newsletter * <span class="font-normal text-slate-400">— mendukung Markdown: **tebal**, ## judul, [teks](url)</span></label>
            <textarea id="body" name="body" x-model="body" rows="16" required class="a-input font-mono text-sm"></textarea>
        </div>
    </div>
    <div class="space-y-6">
        <div class="a-card space-y-3 p-5">
            <h2 class="font-bold text-slate-900">Kirim</h2>
            <p class="text-sm text-slate-500">Newsletter akan dikirim melalui antrian (queue) ke <strong class="text-slate-900">{{ $activeSubscribers }}</strong> subscriber aktif. Setiap email menyertakan tautan berhenti berlangganan.</p>
            <button name="action" value="send" class="a-btn-primary w-full" onclick="return confirm('Kirim newsletter ke {{ $activeSubscribers }} subscriber sekarang?')"><x-icon name="send" class="size-4" /> Kirim Sekarang</button>
            <button name="action" value="draft" class="a-btn-light w-full">Simpan sebagai Draft</button>
        </div>
        <div class="a-card overflow-hidden">
            <p class="border-b border-slate-100 px-5 py-3 text-xs font-bold tracking-wider text-slate-500 uppercase">Pratinjau</p>
            <div class="bg-slate-50 p-5">
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <p class="font-bold text-slate-900" x-text="subject || 'Subjek email'"></p>
                    <p class="mt-3 text-sm whitespace-pre-line text-slate-600" x-text="body || 'Isi newsletter akan tampil di sini…'"></p>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
