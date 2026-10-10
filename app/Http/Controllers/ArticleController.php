<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Product;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::published()->latest('published_at');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('excerpt', 'like', "%{$search}%"));
        }

        return view('articles.index', [
            'articles' => $query->paginate(9)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published && $article->published_at?->isPast(), 404);

        Article::whereKey($article->id)->increment('views');

        return view('articles.show', [
            'article' => $article->load('author'),
            'latest' => Article::published()->whereKeyNot($article->id)->latest('published_at')->take(4)->get(),
            'products' => Product::active()->where('is_featured', true)->take(2)->get(),
        ]);
    }
}
