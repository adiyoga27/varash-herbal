<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        return view('cart.index', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::active()->findOrFail($data['product_id']);
        $this->cart->add($product, $data['qty'] ?? 1);

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return $this->respond($request, "{$product->name} ditambahkan ke keranjang.");
    }

    public function update(Request $request, int $productId)
    {
        $qty = (int) $request->validate(['qty' => ['required', 'integer', 'min:0', 'max:99']])['qty'];
        $this->cart->update($productId, $qty);

        return $this->respond($request, 'Keranjang diperbarui.');
    }

    public function remove(Request $request, int $productId)
    {
        $this->cart->remove($productId);

        return $this->respond($request, 'Produk dihapus dari keranjang.');
    }

    private function respond(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            $items = $this->cart->items();

            return response()->json([
                'message' => $message,
                'count' => $items->sum('qty'),
                'subtotal' => $items->sum('subtotal'),
                'subtotal_formatted' => rupiah($items->sum('subtotal')),
                'items' => $items->map(fn ($i) => [
                    'id' => $i->product->id,
                    'qty' => $i->qty,
                    'subtotal_formatted' => rupiah($i->subtotal),
                ]),
            ]);
        }

        return back()->with('success', $message);
    }
}
