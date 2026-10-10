<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Product;

class SeoController extends Controller
{
    public function sitemap()
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['loc' => route('products.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['loc' => route('articles.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => route('about'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => route('faq'), 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => route('contact'), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ]);

        Category::active()->get()->each(fn ($c) => $urls->push([
            'loc' => route('products.category', $c), 'lastmod' => $c->updated_at, 'priority' => '0.8', 'changefreq' => 'weekly',
        ]));

        Product::active()->get()->each(fn ($p) => $urls->push([
            'loc' => route('products.show', $p), 'lastmod' => $p->updated_at, 'priority' => '0.9', 'changefreq' => 'weekly',
            'image' => $p->imageUrl(), 'title' => $p->name,
        ]));

        Article::published()->get()->each(fn ($a) => $urls->push([
            'loc' => route('articles.show', $a), 'lastmod' => $a->updated_at, 'priority' => '0.7', 'changefreq' => 'monthly',
            'image' => $a->imageUrl(), 'title' => $a->title,
        ]));

        return response()->view('seo.sitemap', ['urls' => $urls], 200)->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /keranjang',
            'Disallow: /checkout',
            'Disallow: /newsletter/',
            'Disallow: /*?sort=',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
    }
}
