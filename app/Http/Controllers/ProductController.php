<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, ?Category $category = null)
    {
        abort_if($category && ! $category->is_active, 404);

        $query = Product::active()->with('category');

        if ($category) {
            $query->where('category_id', $category->id);
        }

        if ($search = trim((string) $request->query('q'))) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%")
                ->orWhere('ingredients', 'like', "%{$search}%"));
        }

        match ($request->query('sort')) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'popular' => $query->orderByDesc('views'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('is_featured')->orderBy('sort_order'),
        };

        return view('products.index', [
            'products' => $query->paginate(12)->withQueryString(),
            'categories' => Category::active()->withCount(['products' => fn ($q) => $q->active()])->get(),
            'currentCategory' => $category,
            'search' => $search,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        // Hitung view tanpa memicu activity log / updated_at
        Product::whereKey($product->id)->increment('views');

        $product->load(['category', 'testimonials' => fn ($q) => $q->where('is_active', true)->latest()]);

        return view('products.show', [
            'product' => $product,
            'related' => Product::active()
                ->whereKeyNot($product->id)
                ->orderByRaw('category_id = ? desc', [$product->category_id])
                ->orderByDesc('is_featured')
                ->take(4)->get(),
        ]);
    }
}
