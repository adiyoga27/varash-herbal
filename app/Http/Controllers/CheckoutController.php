<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\ActivityLogger;
use App\Services\CartService;
use App\Services\WhatsAppOrderMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        return view('checkout.index', ['items' => $items, 'subtotal' => $items->sum('subtotal')]);
    }

    public function store(Request $request)
    {
        $items = $this->cart->items();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'min:9', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'payment_method' => ['required', Rule::in(array_keys(Order::PAYMENT_METHODS))],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [], [
            'customer_name' => 'nama lengkap',
            'customer_phone' => 'nomor WhatsApp',
            'address' => 'alamat',
            'city' => 'kota/kabupaten',
            'province' => 'provinsi',
        ]);

        $order = DB::transaction(function () use ($data, $items, $request) {
            $subtotal = (int) $items->sum('subtotal');

            $order = Order::create($data + [
                'code' => Order::generateCode(),
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'total' => $subtotal,
                'status' => 'pending',
                'ip_address' => $request->ip(),
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name.($item->product->size ? " ({$item->product->size})" : ''),
                    'price' => $item->product->finalPrice(),
                    'quantity' => $item->qty,
                    'subtotal' => $item->subtotal,
                ]);
            }

            return $order;
        });

        ActivityLogger::log('checkout', "Checkout pesanan {$order->code} oleh {$order->customer_name} (".rupiah($order->total).')', $order, [
            'items' => $order->items->map->only(['product_name', 'quantity', 'subtotal'])->all(),
        ], 'order', 'success');

        Notification::send(User::where('is_active', true)->get(), new NewOrderNotification($order));

        $this->cart->clear();
        session()->put('last_order', $order->code);

        return redirect()->route('checkout.success', $order->code);
    }

    public function success(string $code)
    {
        // Hanya pembeli pada sesi yang sama yang bisa melihat halaman ini
        abort_unless(session('last_order') === $code, 404);

        $order = Order::with('items')->where('code', $code)->firstOrFail();

        return view('checkout.success', [
            'order' => $order,
            'waUrl' => WhatsAppOrderMessage::url($order),
        ]);
    }

    /** Dipanggil saat tombol WhatsApp diklik, untuk mencatat bahwa pesan sudah dikirim. */
    public function whatsapp(string $code)
    {
        abort_unless(session('last_order') === $code, 404);

        $order = Order::with('items')->where('code', $code)->firstOrFail();

        if (! $order->whatsapp_sent_at) {
            $order->forceFill(['whatsapp_sent_at' => now()])->saveQuietly();
            ActivityLogger::log('whatsapp_redirect', "Pelanggan membuka WhatsApp untuk pesanan {$order->code}", $order, [], 'order');
        }

        return redirect()->away(WhatsAppOrderMessage::url($order));
    }
}
