<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'featured' => Product::active()->with('category')->where('is_featured', true)->orderBy('sort_order')->take(8)->get(),
            'categories' => Category::active()->withCount(['products' => fn ($q) => $q->active()])->get(),
            'testimonials' => Testimonial::where('is_active', true)->with('product:id,name,slug')->latest()->take(9)->get(),
            'articles' => Article::published()->latest('published_at')->take(3)->get(),
            'faqs' => Faq::where('is_active', true)->orderBy('sort_order')->take(6)->get(),
        ]);
    }
}
