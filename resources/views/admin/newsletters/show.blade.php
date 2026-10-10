@extends('layouts.admin')

@section('title', 'Newsletter')

@section('content')
<a href="{{ route('admin.newsletters.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-900"><x-icon name="arrow-left" class="size-4" /> Kembali</a>
<div class="mt-4 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <article class="a-card p-5 sm:p-8 xl:col-span-2">
        <h2 class="text-xl font-bold text-slate-900">{{ $newsletter->subject }}</h2>
        <div class="prose-herbal mt-4 !text-sm">{!! \Illuminate\Support\Str::markdown($newsletter->body, ['html_input' => 'strip']) !!}</div>
    </article>
    <div class="space-y-4">
        <div class="a-card space-y-2 p-5 text-sm">
            <p><span class="text-slate-500">Status:</span> <strong>{{ $newsletter->status === 'sent' ? 'Terkirim' : 'Draft' }}</strong></p>
            <p><span class="text-slate-500">Dibuat:</span> {{ $newsletter->created_at->translatedFormat('d M Y H:i') }} oleh {{ $newsletter->author?->name ?? '-' }}</p>
            @if ($newsletter->sent_at)<p><span class="text-slate-500">Dikirim:</span> {{ $newsletter->sent_at->translatedFormat('d M Y H:i') }} ke {{ $newsletter->recipients_count }} subscriber</p>@endif
        </div>
        <form method="POST" action="{{ route('admin.newsletters.send', $newsletter) }}" data-confirm="Kirim {{ $newsletter->status === 'sent' ? 'ulang ' : '' }}ke {{ $activeSubscribers }} subscriber aktif?">
            @csrf<button class="a-btn-primary w-full"><x-icon name="send" class="size-4" /> {{ $newsletter->status === 'sent' ? 'Kirim Ulang' : 'Kirim Sekarang' }}</button>
        </form>
        <form method="POST" action="{{ route('admin.newsletters.destroy', $newsletter) }}" data-confirm="Hapus newsletter ini?">@csrf @method('DELETE')<button class="a-btn-light w-full text-red-600">Hapus</button></form>
    </div>
</div>
@endsection
