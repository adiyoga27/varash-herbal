@extends('layouts.admin')

@section('title', 'Pesan dari '.$message->name)

@section('content')
<a href="{{ route('admin.messages.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-900"><x-icon name="arrow-left" class="size-4" /> Kembali</a>
<div class="a-card mt-4 max-w-3xl p-5 sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-900">{{ $message->subject ?: 'Tanpa subjek' }}</h2>
            <p class="text-sm text-slate-500">{{ $message->name }} · {{ $message->email }} · {{ $message->phone }}</p>
        </div>
        <p class="text-xs text-slate-400">{{ $message->created_at->translatedFormat('d M Y H:i') }}<br>IP {{ $message->ip_address }}</p>
    </div>
    <p class="mt-6 whitespace-pre-line text-slate-700">{{ $message->message }}</p>
    <div class="mt-8 flex flex-wrap gap-2 border-t border-slate-100 pt-5">
        @if ($message->phone)<a href="{{ wa_link('Halo '.$message->name.', terima kasih telah menghubungi '.setting('site_name').'. ', $message->phone) }}" target="_blank" rel="noopener" class="a-btn bg-[#25D366] text-white"><x-icon name="whatsapp" class="size-4" /> Balas via WhatsApp</a>@endif
        @if ($message->email)<a href="mailto:{{ $message->email }}?subject=Re: {{ rawurlencode($message->subject ?? '') }}" class="a-btn-light"><x-icon name="mail" class="size-4" /> Balas via Email</a>@endif
        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Hapus pesan ini?" class="ml-auto">@csrf @method('DELETE')<button class="a-btn-light text-red-600"><x-icon name="trash" class="size-4" /> Hapus</button></form>
    </div>
</div>
@endsection
