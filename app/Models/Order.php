<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['code', 'customer_name', 'customer_phone', 'customer_email', 'address', 'city', 'province', 'postal_code', 'payment_method', 'notes', 'subtotal', 'shipping_cost', 'total', 'status', 'admin_notes', 'ip_address', 'whatsapp_sent_at'])]
class Order extends Model
{
    use LogsActivity;

    public const STATUSES = [
        'pending' => 'Menunggu Konfirmasi',
        'confirmed' => 'Dikonfirmasi',
        'paid' => 'Sudah Dibayar',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    public const PAYMENT_METHODS = [
        'transfer' => 'Transfer Bank',
        'ewallet' => 'E-Wallet / QRIS',
        'cod' => 'COD (Bayar di Tempat)',
    ];

    protected function casts(): array
    {
        return ['whatsapp_sent_at' => 'datetime'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateCode(): string
    {
        do {
            $code = 'VRS-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800',
            'confirmed' => 'bg-sky-100 text-sky-800',
            'paid' => 'bg-indigo-100 text-indigo-800',
            'shipped' => 'bg-violet-100 text-violet-800',
            'completed' => 'bg-emerald-100 text-emerald-800',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function paymentLabel(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    /** Nomor pelanggan dalam format internasional (62xxx) untuk tautan WhatsApp. */
    public function customerWhatsapp(): string
    {
        return phone_to_wa($this->customer_phone);
    }
}
