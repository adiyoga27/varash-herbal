@extends('layouts.app')

@push('preload')
    <link rel="preload" as="image" href="{{ asset('images/hero.webp') }}" fetchpriority="high">
@endpush

@push('schema')
@if ($faqs->isNotEmpty())
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqs->map(fn ($f) => ['@type' => 'Question', 'name' => $f->question, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->answer]])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
@endpush

@section('content')
{{-- ============ HERO ============ --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top_right,var(--color-forest-100),transparent_55%),radial-gradient(ellipse_at_bottom_left,var(--color-gold-100),transparent_50%)]"></div>
    <svg class="absolute top-10 -left-10 -z-10 hidden size-72 text-forest-200/50 lg:block" viewBox="0 0 200 200" fill="currentColor" aria-hidden="true"><path d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,79.6,-45.8C87.4,-32.6,90,-16.3,88.5,-0.9C87,14.6,81.4,29.2,73.1,42.1C64.8,55,53.9,66.2,40.6,73.8C27.3,81.4,11.7,85.4,-3.6,91.6C-18.8,97.8,-37.7,106.2,-50.5,99.5C-63.4,92.9,-70.3,71.1,-77.3,52.1C-84.4,33.2,-91.6,17.1,-91.8,0.9C-92,-15.3,-85.1,-30.6,-75.2,-43C-65.2,-55.5,-52.2,-65.1,-38.2,-72.4C-24.2,-79.7,-12.1,-84.7,1.6,-87.4C15.3,-90.2,30.6,-83.6,44.7,-76.4Z" transform="translate(100 100)"/></svg>

    <div class="container-x grid grid-cols-1 items-center gap-10 py-12 sm:py-16 lg:grid-cols-2 lg:gap-6 lg:py-24">
        <div class="max-w-xl">
            <div class="flex flex-wrap gap-2">
                <span class="eyebrow"><x-icon name="leaf" class="size-3.5" /> 108 Tanaman Herbal Bali</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-bold text-forest-800 shadow-sm ring-1 ring-forest-900/5"><x-icon name="shield" class="size-3.5 text-forest-600" /> BPOM & Halal MUI</span>
            </div>
            <h1 class="mt-5 font-display text-4xl leading-[1.08] font-semibold text-forest-900 sm:text-5xl lg:text-6xl">
                {{ setting('hero_title') }}
            </h1>
            <p class="mt-5 text-base leading-relaxed text-forest-900/70 sm:text-lg">{{ setting('hero_subtitle') }}</p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('products.index') }}" class="btn-primary !px-7 !py-4 text-base">Belanja Sekarang <x-icon name="arrow-right" class="size-4" /></a>
                <a href="{{ wa_link('Halo Admin, saya ingin konsultasi gratis tentang keluhan saya') }}" target="_blank" rel="noopener" class="btn-outline !px-7 !py-4 text-base"><x-icon name="whatsapp" class="size-5 text-[#25D366]" /> Konsultasi Gratis</a>
            </div>

            <dl class="mt-10 grid max-w-md grid-cols-3 divide-x divide-forest-900/10">
                @foreach ([['stat_sold', 'Botol terjual'], ['stat_customers', 'Pelanggan puas'], ['stat_herbs', 'Tanaman herbal']] as [$key, $label])
                    <div class="px-3 first:pl-0">
                        <dt class="sr-only">{{ $label }}</dt>
                        <dd class="font-display text-xl font-semibold whitespace-nowrap text-forest-800 sm:text-3xl">{{ setting($key) }}</dd>
                        <dd class="mt-0.5 text-xs text-forest-900/60 sm:text-sm">{{ $label }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="relative mx-auto w-full max-w-md lg:max-w-lg">
            <div class="absolute inset-6 rounded-full bg-gradient-to-br from-forest-300/60 to-gold-200/70 blur-2xl"></div>
            <div class="relative aspect-square rounded-full bg-gradient-to-br from-forest-700 to-forest-900 p-6 shadow-2xl shadow-forest-900/30 ring-8 ring-white/70">
                <div class="absolute inset-0 rounded-full bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,.18),transparent_45%)]"></div>
                <img src="{{ asset('images/hero.webp') }}" alt="Minyak herbal Varash dari 108 tanaman berkhasiat" width="520" height="520" fetchpriority="high"
                     class="relative size-full animate-float object-contain drop-shadow-2xl">
            </div>
            <div class="absolute top-8 -left-2 flex items-center gap-3 rounded-2xl bg-white/95 p-3 pr-4 shadow-xl backdrop-blur sm:-left-8">
                <span class="grid size-10 place-items-center rounded-xl bg-gold-100 text-gold-600"><x-icon name="star" class="size-5 fill-current" /></span>
                <div><p class="text-sm font-bold text-forest-900">4.9/5 Rating</p><p class="text-xs text-forest-900/60">dari ribuan ulasan</p></div>
            </div>
            <div class="absolute -right-2 bottom-10 flex items-center gap-3 rounded-2xl bg-white/95 p-3 pr-4 shadow-xl backdrop-blur sm:-right-6">
                <span class="grid size-10 place-items-center rounded-xl bg-forest-100 text-forest-700"><x-icon name="award" class="size-5" /></span>
                <div><p class="text-sm font-bold text-forest-900">100% Original</p><p class="text-xs text-forest-900/60">Langsung dari pabrik</p></div>
            </div>
        </div>
    </div>
</section>

{{-- ============ TRUST MARQUEE ============ --}}
<div class="overflow-hidden border-y border-forest-900/5 bg-forest-800 py-4 text-forest-50" aria-hidden="true">
    <div class="flex w-max animate-marquee gap-12 whitespace-nowrap">
        @foreach (range(1, 2) as $_)
            @foreach (['Terdaftar BPOM', 'Bersertifikat Halal MUI', 'Standar Produksi GMP', '108 Tanaman Berkhasiat', 'Diracik di Tabanan, Bali', 'Pengiriman Seluruh Indonesia', 'Konsultasi Gratis'] as $t)
                <span class="flex items-center gap-3 text-sm font-semibold tracking-wide"><x-icon name="leaf" class="size-4 text-gold-300" /> {{ $t }}</span>
            @endforeach
        @endforeach
    </div>
</div>

{{-- ============ KATEGORI ============ --}}
<section class="container-x py-16 sm:py-20" aria-labelledby="cat-title">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Kategori</p>
            <h2 id="cat-title" class="section-title mt-3">Temukan Solusi Herbal Anda</h2>
        </div>
        <a href="{{ route('products.index') }}" class="hidden items-center gap-1 text-sm font-semibold text-forest-700 hover:text-forest-900 sm:inline-flex">Lihat semua <x-icon name="arrow-right" class="size-4" /></a>
    </div>
    <div class="no-scrollbar -mx-4 mt-8 flex snap-x gap-3 overflow-x-auto px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-3 sm:px-0 lg:grid-cols-5">
        @foreach ($categories as $cat)
            <a href="{{ route('products.category', $cat) }}" class="group card reveal flex w-44 shrink-0 snap-start flex-col p-5 transition hover:-translate-y-1 hover:bg-forest-700 sm:w-auto">
                <span class="grid size-12 place-items-center rounded-2xl bg-forest-100 text-forest-700 transition group-hover:bg-white/15 group-hover:text-gold-300"><x-icon :name="$cat->icon ?: 'leaf'" class="size-6" /></span>
                <h3 class="mt-4 font-sans text-base font-bold text-forest-900 group-hover:text-white">{{ $cat->name }}</h3>
                <p class="mt-1 text-xs text-forest-900/50 group-hover:text-forest-100">{{ $cat->products_count }} produk</p>
            </a>
        @endforeach
    </div>
</section>

{{-- ============ APA ITU VARASH ============ --}}
<section class="bg-white py-16 sm:py-24" aria-labelledby="about-title">
    <div class="container-x grid grid-cols-1 items-center gap-12 lg:grid-cols-2">
        <div class="reveal relative">
            <img src="{{ asset('images/rempah.webp') }}" alt="Rempah asli bahan minyak Varash" width="800" height="696" loading="lazy" class="w-full rounded-[2rem] object-cover shadow-xl">
            <div class="absolute -right-3 -bottom-6 max-w-[16rem] rounded-3xl bg-forest-800 p-5 text-white shadow-xl sm:-right-6">
                <p class="font-display text-4xl font-semibold text-gold-300">20+ tahun</p>
                <p class="mt-1 text-sm text-forest-100">pengalaman peracik dalam pengobatan herbal tradisional Bali</p>
            </div>
        </div>
        <div class="reveal">
            <p class="eyebrow">Sekilas Tentang Varash</p>
            <h2 id="about-title" class="section-title mt-3">Minyak Herbal dengan Saripati Rempah Asli Pertama di Indonesia</h2>
            <p class="mt-5 leading-relaxed text-forest-900/70">Minyak Varash diformulasikan dari beragam tanaman obat warisan pengetahuan herbal Nusantara. Kami percaya tubuh memiliki kemampuan alami untuk memulihkan dirinya bila mendapat dukungan yang tepat — melalui pendekatan holistik yang aman, cukup dengan dibalurkan di kulit.</p>
            <ul class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ([
                    ['drop', 'Transdermal', 'Meresap melalui kulit, tidak membebani lambung & ginjal.'],
                    ['leaf', '6 Unsur Kehidupan', 'Akar, batang, biji, buah, daun, dan bunga.'],
                    ['users', 'Untuk Semua Usia', 'Aman dipakai anak-anak hingga lansia.'],
                    ['shield', 'Teruji & Bersertifikat', 'BPOM, Halal MUI, dan standar GMP.'],
                ] as [$icon, $title, $desc])
                    <li class="flex gap-3">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-gold-100 text-gold-600"><x-icon :name="$icon" /></span>
                        <div><h3 class="font-sans font-bold text-forest-900">{{ $title }}</h3><p class="mt-0.5 text-sm text-forest-900/60">{{ $desc }}</p></div>
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('about') }}" class="btn-outline mt-8">Kenali Kami Lebih Dekat <x-icon name="arrow-right" class="size-4" /></a>
        </div>
    </div>
</section>

{{-- ============ PRODUK UNGGULAN ============ --}}
<section class="container-x py-16 sm:py-24" aria-labelledby="featured-title">
    <div class="mx-auto max-w-2xl text-center">
        <p class="eyebrow">Produk Varash</p>
        <h2 id="featured-title" class="section-title mt-3">Produk Unggulan Pilihan Pelanggan</h2>
        <p class="mt-3 text-forest-900/60">100% original, langsung dari PT. Varash Saddan Nusantara.</p>
    </div>
    <div class="mt-10 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
        @foreach ($featured as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
    <div class="mt-10 text-center">
        <a href="{{ route('products.index') }}" class="btn-primary">Lihat Semua Produk <x-icon name="arrow-right" class="size-4" /></a>
    </div>
</section>

{{-- ============ CARA PAKAI ============ --}}
<section class="relative overflow-hidden bg-forest-900 py-16 text-white sm:py-24" aria-labelledby="howto-title">
    <div class="pointer-events-none absolute -top-40 left-1/2 size-[40rem] -translate-x-1/2 rounded-full bg-forest-600/30 blur-3xl"></div>
    <div class="container-x relative">
        <div class="mx-auto max-w-2xl text-center">
            <p class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold tracking-[0.14em] text-gold-300 uppercase">Cara Pakai</p>
            <h2 id="howto-title" class="mt-3 text-3xl font-semibold sm:text-4xl">4 Langkah Mudah Membalur Varash</h2>
            <p class="mt-3 text-forest-100/70">Gunakan rutin pagi setelah mandi dan malam sebelum tidur untuk hasil terbaik.</p>
        </div>
        <ol class="mt-12 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Telapak Kaki', 'Balurkan pada telapak kaki dan sela-sela jari, lalu pijat lembut.'],
                ['Tulang Belakang', 'Balurkan dari pangkal kepala hingga tulang ekor.'],
                ['Area Keluhan', 'Oleskan pada bagian tubuh yang terasa tidak nyaman.'],
                ['Air Hangat', 'Minum 1–2 gelas air putih hangat untuk bantu detoksifikasi.'],
            ] as $i => [$title, $desc])
                <li class="reveal group overflow-hidden rounded-3xl bg-white/5 ring-1 ring-white/10 transition hover:bg-white/10" style="transition-delay: {{ $i * 80 }}ms">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ asset('images/cara-pakai-'.($i + 1).'.webp') }}" alt="Langkah {{ $i + 1 }}: {{ $title }}" width="400" height="300" loading="lazy" class="size-full object-cover transition duration-500 group-hover:scale-105">
                    </div>
                    <div class="p-5">
                        <span class="font-display text-sm font-semibold text-gold-300">Langkah 0{{ $i + 1 }}</span>
                        <h3 class="mt-1 font-sans text-lg font-bold">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-forest-100/70">{{ $desc }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- ============ NILAI VARASH ============ --}}
<section class="container-x py-16 sm:py-24" aria-labelledby="values-title">
    <div class="grid grid-cols-1 gap-12 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <p class="eyebrow">Nilai-Nilai Varash</p>
            <h2 id="values-title" class="section-title mt-3">Dari Cinta, Untuk Melayani</h2>
            <p class="mt-4 text-forest-900/70">Setiap botol Varash dibuat dengan komitmen menghadirkan kesehatan alami yang dapat dipercaya oleh seluruh keluarga Indonesia.</p>
            <img src="{{ asset('images/bpom.webp') }}" alt="Sertifikat registrasi BPOM produk Varash" width="800" height="511" loading="lazy" class="mt-8 w-full rounded-3xl shadow-lg ring-1 ring-forest-900/5">
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:col-span-3">
            @foreach ([
                ['shield', 'Kepercayaan & Keaslian', 'Produk asli dengan bahan herbal pilihan dan proses produksi terjamin.'],
                ['heart', 'Kasih & Dukungan', 'Kami mendampingi perjalanan sehat Anda melalui konsultasi gratis.'],
                ['award', 'Kualitas & Ketelitian', 'Diproses dengan standar GMP dan quality control di setiap batch.'],
                ['users', 'Tanggung Jawab Konsumen', 'Edukasi pemakaian yang benar dan layanan purna jual yang responsif.'],
                ['check-circle', 'Agen Terverifikasi', 'Jaringan agen resmi di seluruh Indonesia untuk memastikan keaslian.'],
                ['chat', 'Konsultasi Resmi', 'Tim kami siap membantu memilih produk sesuai kebutuhan Anda.'],
            ] as $i => [$icon, $title, $desc])
                <div class="card reveal p-6" style="transition-delay: {{ $i * 60 }}ms">
                    <span class="grid size-12 place-items-center rounded-2xl bg-forest-100 text-forest-700"><x-icon :name="$icon" class="size-6" /></span>
                    <h3 class="mt-4 font-sans text-lg font-bold text-forest-900">{{ $title }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-forest-900/60">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ TESTIMONI ============ --}}
@if ($testimonials->isNotEmpty())
<section class="bg-sand/60 py-16 sm:py-24" aria-labelledby="testi-title" x-data="{ scroll(dir) { $refs.track.scrollBy({ left: dir * $refs.track.clientWidth * 0.8, behavior: 'smooth' }) } }">
    <div class="container-x">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Testimoni</p>
                <h2 id="testi-title" class="section-title mt-3">Cerita dari Para Pengguna Kami</h2>
            </div>
            <div class="hidden gap-2 sm:flex">
                <button @click="scroll(-1)" class="grid size-11 place-items-center rounded-full bg-white shadow ring-1 ring-forest-900/5 hover:bg-forest-50" aria-label="Sebelumnya"><x-icon name="chevron-left" /></button>
                <button @click="scroll(1)" class="grid size-11 place-items-center rounded-full bg-forest-700 text-white shadow hover:bg-forest-800" aria-label="Berikutnya"><x-icon name="chevron-right" /></button>
            </div>
        </div>
        <div x-ref="track" class="no-scrollbar -mx-4 mt-10 flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth px-4 pb-4 sm:mx-0 sm:px-0">
            @foreach ($testimonials as $t)
                <figure class="card flex w-[85%] shrink-0 snap-start flex-col p-6 sm:w-[calc(50%-0.5rem)] lg:w-[calc(33.333%-0.7rem)]">
                    <div class="flex items-center justify-between">
                        <div class="flex gap-0.5 text-gold-400">@for ($s = 0; $s < $t->rating; $s++)<x-icon name="star" class="size-4 fill-current" />@endfor</div>
                        <x-icon name="quote" class="size-8 text-forest-100" />
                    </div>
                    <blockquote class="mt-4 flex-1 leading-relaxed text-forest-900/80">“{{ $t->content }}”</blockquote>
                    <figcaption class="mt-6 flex items-center gap-3 border-t border-forest-900/5 pt-4">
                        <span class="grid size-11 place-items-center rounded-full bg-gradient-to-br from-forest-500 to-forest-700 font-bold text-white">{{ mb_substr($t->name, 0, 1) }}</span>
                        <div>
                            <p class="font-bold text-forest-900">{{ $t->name }}</p>
                            <p class="text-xs text-forest-900/50">{{ $t->location }}@if ($t->product) · <a href="{{ route('products.show', $t->product) }}" class="text-forest-600 hover:underline">{{ $t->product->name }}</a>@endif</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
        <p class="mt-4 text-center text-xs text-forest-900/50">*Hasil yang dirasakan setiap orang dapat berbeda-beda.</p>
    </div>
</section>
@endif

{{-- ============ CTA AGEN ============ --}}
<section class="container-x py-16 sm:py-24">
    <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-forest-700 via-forest-800 to-forest-950 p-8 text-white sm:p-12 lg:p-16">
        <div class="pointer-events-none absolute -top-20 -right-20 size-80 rounded-full bg-gold-400/20 blur-3xl"></div>
        <div class="relative grid grid-cols-1 items-center gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <p class="text-sm font-bold tracking-[0.18em] text-gold-300 uppercase">Peluang Usaha</p>
                <h2 class="mt-2 text-3xl font-semibold sm:text-4xl">Jadi Agen & Reseller Resmi Varash</h2>
                <p class="mt-3 max-w-xl text-forest-100/80">Dapatkan harga spesial member, buku panduan, materi promosi, dan pendampingan. Bantu lebih banyak keluarga hidup sehat sambil menambah penghasilan.</p>
                <ul class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-sm">
                    @foreach (['Margin menarik', 'Subsidi ongkir', 'Support 24/7', 'Tanpa target'] as $perk)
                        <li class="flex items-center gap-2"><x-icon name="check-circle" class="size-4 text-gold-300" />{{ $perk }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="flex flex-col gap-3">
                <a href="{{ route('products.category', 'paket-agen-reseller') }}" class="btn-gold">Lihat Paket Reseller</a>
                <a href="{{ wa_link('Halo Admin, saya tertarik menjadi agen/reseller Varash. Mohon info lengkapnya.') }}" target="_blank" rel="noopener" class="btn border border-white/20 text-white hover:bg-white/10"><x-icon name="whatsapp" class="size-5" /> Daftar via WhatsApp</a>
            </div>
        </div>
    </div>
</section>

{{-- ============ FAQ ============ --}}
@if ($faqs->isNotEmpty())
<section class="container-x pb-16 sm:pb-24" aria-labelledby="faq-title">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <p class="eyebrow">FAQ</p>
            <h2 id="faq-title" class="section-title mt-3">Pertanyaan yang Sering Diajukan</h2>
            <p class="mt-4 text-forest-900/70">Tidak menemukan jawaban yang Anda cari? Tim kami siap membantu.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('faq') }}" class="btn-outline">Semua FAQ</a>
                <a href="{{ wa_link('Halo Admin, saya punya pertanyaan') }}" target="_blank" rel="noopener" class="btn-wa"><x-icon name="whatsapp" class="size-4" /> Tanya Admin</a>
            </div>
        </div>
        <div class="space-y-3 lg:col-span-3" x-data="{ open: 0 }">
            @foreach ($faqs as $i => $faq)
                <div class="card overflow-hidden">
                    <h3>
                        <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" :aria-expanded="open === {{ $i }}" class="flex w-full items-center justify-between gap-4 p-5 text-left font-sans font-bold text-forest-900">
                            {{ $faq->question }}
                            <span class="grid size-8 shrink-0 place-items-center rounded-full bg-forest-50 transition" :class="open === {{ $i }} && 'rotate-45 bg-forest-700 text-white'"><x-icon name="plus" class="size-4" /></span>
                        </button>
                    </h3>
                    <div x-show="open === {{ $i }}" x-collapse @if ($i !== 0) x-cloak @endif>
                        <p class="px-5 pb-5 leading-relaxed text-forest-900/70">{{ $faq->answer }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ ARTIKEL ============ --}}
@if ($articles->isNotEmpty())
<section class="bg-white py-16 sm:py-24" aria-labelledby="blog-title">
    <div class="container-x">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="eyebrow">Artikel</p>
                <h2 id="blog-title" class="section-title mt-3">Tips & Edukasi Sehat Alami</h2>
            </div>
            <a href="{{ route('articles.index') }}" class="hidden items-center gap-1 text-sm font-semibold text-forest-700 hover:text-forest-900 sm:inline-flex">Semua artikel <x-icon name="arrow-right" class="size-4" /></a>
        </div>
        <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ($articles as $article)
                @include('articles._card', ['article' => $article])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
