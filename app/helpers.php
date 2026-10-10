<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            return Setting::get($key, $default) ?? $default;
        } catch (Throwable) {
            return $default;
        }
    }
}

if (! function_exists('rupiah')) {
    function rupiah(int|float|null $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('phone_to_wa')) {
    /** Ubah 0812-xxx / +62 812 xxx menjadi 62812xxx untuk wa.me */
    function phone_to_wa(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        return match (true) {
            str_starts_with($digits, '62') => $digits,
            str_starts_with($digits, '0') => '62'.substr($digits, 1),
            str_starts_with($digits, '8') => '62'.$digits,
            default => $digits,
        };
    }
}

if (! function_exists('wa_link')) {
    function wa_link(?string $message = null, ?string $phone = null): string
    {
        $number = phone_to_wa($phone ?? setting('whatsapp_number', '6282210810856'));

        return 'https://wa.me/'.$number.($message ? '?text='.rawurlencode($message) : '');
    }
}

if (! function_exists('lines_to_array')) {
    /** Textarea "satu item per baris" → array bersih. */
    function lines_to_array(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $text))
            ->map(fn ($line) => trim(ltrim(trim($line), '-•·*')))
            ->filter()
            ->values()
            ->all();
    }
}

if (! function_exists('clean_html')) {
    /** Sanitasi HTML artikel: hanya tag aman, buang atribut event & javascript: URL. */
    function clean_html(?string $html): string
    {
        $allowed = '<p><br><strong><b><em><i><u><h2><h3><h4><ul><ol><li><a><blockquote><img><figure><figcaption><table><thead><tbody><tr><th><td><hr><span>';
        $html = strip_tags((string) $html, $allowed);
        $html = preg_replace('/\s+on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/(href|src)\s*=\s*("|\')\s*(javascript|data|vbscript):[^"\']*\2/i', '$1="#"', $html);
        $html = preg_replace('/\sstyle\s*=\s*("[^"]*"|\'[^\']*\')/i', '', $html);

        return $html;
    }
}
