<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Keranjang belanja berbasis session (tanpa perlu login).
 * Disimpan sebagai [product_id => qty]; harga selalu dibaca ulang dari database.
 */
class CartService
{
    private const KEY = 'cart';

    public const MAX_QTY = 99;

    public function raw(): array
    {
        return session(self::KEY, []);
    }

    public function add(Product $product, int $qty = 1): void
    {
        $cart = $this->raw();
        $cart[$product->id] = min(self::MAX_QTY, ($cart[$product->id] ?? 0) + max(1, $qty));
        session([self::KEY => $cart]);
    }

    public function update(int $productId, int $qty): void
    {
        $cart = $this->raw();
        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = min(self::MAX_QTY, $qty);
        }
        session([self::KEY => $cart]);
    }

    public function remove(int $productId): void
    {
        $this->update($productId, 0);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /** @return Collection<int, object{product: Product, qty: int, subtotal: int}> */
    public function items(): Collection
    {
        $raw = $this->raw();
        if ($raw === []) {
            return collect();
        }

        $products = Product::active()->whereIn('id', array_keys($raw))->get()->keyBy('id');

        // Bersihkan produk yang sudah tidak aktif / terhapus
        if ($products->count() !== count($raw)) {
            session([self::KEY => array_intersect_key($raw, $products->all())]);
        }

        return $products->map(fn (Product $p) => (object) [
            'product' => $p,
            'qty' => $raw[$p->id],
            'subtotal' => $p->finalPrice() * $raw[$p->id],
        ])->values();
    }

    public function subtotal(): int
    {
        return (int) $this->items()->sum('subtotal');
    }
}
