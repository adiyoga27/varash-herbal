<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\SubscriberBroadcaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($search = $request->query('q')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
        }
        if ($category = $request->query('category')) {
            $query->where('category_id', $category);
        }
        if ($request->query('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->query('status') === 'inactive') {
            $query->where('is_active', false);
        } elseif ($request->query('status') === 'trashed') {
            $query->onlyTrashed();
        }

        return view('admin.products.index', [
            'products' => $query->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product(['is_active' => true, 'stock' => 100, 'weight' => 200]), 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $product = Product::create($this->validated($request));

        if ($request->boolean('notify_subscribers') && $product->is_active) {
            SubscriberBroadcaster::newProduct($product);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', ['product' => $product, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Produk dipindahkan ke sampah.');
    }

    public function restore(int $id)
    {
        Product::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Produk dipulihkan.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:170', Rule::unique('products')->ignore($product)],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products')->ignore($product)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'size' => ['nullable', 'string', 'max:50'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'ingredients' => ['nullable', 'string', 'max:5000'],
            'benefits' => ['nullable', 'string'],
            'advantages' => ['nullable', 'string'],
            'usage' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'sale_price' => ['nullable', 'integer', 'min:0', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ]);

        $data['slug'] = Str::slug(($data['slug'] ?? null) ?: $data['name']);
        foreach (['benefits', 'advantages', 'usage'] as $field) {
            $data[$field] = lines_to_array($data[$field] ?? null);
        }
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] ??= 0;
        $data['weight'] ??= 0;

        if ($request->hasFile('image')) {
            if ($product?->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($data['image']);
        }

        return $data;
    }
}
