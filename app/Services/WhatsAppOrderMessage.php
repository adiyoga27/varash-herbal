<?php

namespace App\Services;

use App\Models\Order;

/** Menyusun pesan pesanan yang dikirim pelanggan ke WhatsApp admin. */
class WhatsAppOrderMessage
{
    public static function build(Order $order): string
    {
        $order->loadMissing('items');
        $lines = [];
        $lines[] = '*PESANAN BARU - '.setting('site_name', config('app.name')).'*';
        $lines[] = '━━━━━━━━━━━━━━━━━━';
        $lines[] = 'No. Pesanan : *'.$order->code.'*';
        $lines[] = 'Tanggal     : '.$order->created_at->translatedFormat('d M Y, H:i');
        $lines[] = '';
        $lines[] = '*DATA PEMESAN*';
        $lines[] = 'Nama   : '.$order->customer_name;
        $lines[] = 'No. HP : '.$order->customer_phone;
        if ($order->customer_email) {
            $lines[] = 'Email  : '.$order->customer_email;
        }
        $lines[] = 'Alamat : '.$order->address;
        $region = collect([$order->city, $order->province, $order->postal_code])->filter()->implode(', ');
        if ($region) {
            $lines[] = '         '.$region;
        }
        $lines[] = '';
        $lines[] = '*DETAIL PESANAN*';
        foreach ($order->items as $i => $item) {
            $lines[] = ($i + 1).'. '.$item->product_name;
            $lines[] = '    '.$item->quantity.' x '.rupiah($item->price).' = *'.rupiah($item->subtotal).'*';
        }
        $lines[] = '';
        $lines[] = 'Subtotal : '.rupiah($order->subtotal);
        $lines[] = 'Ongkir   : '.($order->shipping_cost > 0 ? rupiah($order->shipping_cost) : 'Menunggu konfirmasi admin');
        $lines[] = '*TOTAL    : '.rupiah($order->total).'*';
        $lines[] = '';
        $lines[] = 'Pembayaran : '.$order->paymentLabel();
        if ($order->notes) {
            $lines[] = 'Catatan    : '.$order->notes;
        }
        $lines[] = '━━━━━━━━━━━━━━━━━━';
        $lines[] = 'Mohon konfirmasi ketersediaan & total ongkir. Terima kasih 🙏';
        $lines[] = 'Cek pesanan: '.route('order.show', $order->code);

        return implode("\n", $lines);
    }

    public static function url(Order $order): string
    {
        return wa_link(self::build($order));
    }
}
