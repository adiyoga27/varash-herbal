@extends('layouts.app')

@section('title', 'Berhenti Berlangganan')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="container-x flex min-h-[60vh] items-center justify-center py-16">
    <div class="card w-full max-w-md p-8 text-center">
        <span class="mx-auto grid size-16 place-items-center rounded-full bg-forest-100 text-forest-700"><x-icon name="mail" class="size-8" /></span>
        @if (! empty($done))
            <h1 class="mt-5 text-2xl font-semibold text-forest-900">Anda telah berhenti berlangganan</h1>
            <p class="mt-2 text-forest-900/60">{{ $subscriber->email }} tidak akan menerima newsletter lagi. Kami akan merindukan Anda 🌿</p>
            <a href="{{ route('home') }}" class="btn-primary mt-6">Kembali ke Beranda</a>
        @else
            <h1 class="mt-5 text-2xl font-semibold text-forest-900">Berhenti berlangganan?</h1>
            <p class="mt-2 text-forest-900/60">Email <strong>{{ $subscriber->email }}</strong> tidak akan lagi menerima tips sehat dan promo dari kami.</p>
            <form method="POST" action="{{ route('newsletter.unsubscribe.confirm', $subscriber->token) }}" class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-center">
                @csrf
                <a href="{{ route('home') }}" class="btn-outline">Batal</a>
                <button class="btn bg-red-600 text-white hover:bg-red-700">Ya, berhenti berlangganan</button>
            </form>
        @endif
    </div>
</section>
@endsection
