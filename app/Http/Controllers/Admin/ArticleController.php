<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\SubscriberBroadcaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('author')->latest('published_at');
        if ($search = $request->query('q')) {
            $query->where('title', 'like', "%{$search}%");
        }

        return view('admin.articles.index', ['articles' => $query->paginate(15)->withQueryString()]);
    }

    public function create()
    {
        return view('admin.articles.form', ['article' => new Article(['is_published' => true, 'published_at' => now()])]);
    }

    public function store(Request $request)
    {
        $article = Article::create($this->validated($request) + ['user_id' => $request->user()->id]);

        if ($request->boolean('notify_subscribers') && $article->is_published) {
            SubscriberBroadcaster::newArticle($article);
        }

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diterbitkan.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.form', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $article->update($this->validated($request, $article));

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return back()->with('success', 'Artikel dihapus.');
    }

    private function validated(Request $request, ?Article $article = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('articles')->ignore($article)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ]);

        $data['slug'] = Str::slug(($data['slug'] ?? null) ?: $data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] ??= now();
        $data['body'] = clean_html($data['body']);

        if ($request->hasFile('image')) {
            if ($article?->image && ! str_starts_with($article->image, '/')) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
        } else {
            unset($data['image']);
        }

        return $data;
    }
}
