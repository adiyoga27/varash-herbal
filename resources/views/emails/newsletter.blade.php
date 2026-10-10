<x-mail::message>
# {{ $newsletter->subject }}

{!! $newsletter->body !!}

<x-mail::button :url="route('home')" color="success">
Kunjungi {{ setting('site_name', config('app.name')) }}
</x-mail::button>

Salam sehat,<br>
{{ setting('site_name', config('app.name')) }}

<x-slot:subcopy>
Anda menerima email ini karena berlangganan newsletter kami. [Berhenti berlangganan]({{ $unsubscribeUrl }})
</x-slot:subcopy>
</x-mail::message>
