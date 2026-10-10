<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return view('admin.faqs.index', ['faqs' => Faq::orderBy('sort_order')->get()]);
    }

    public function store(Request $request)
    {
        Faq::create($this->validated($request));

        return back()->with('success', 'FAQ ditambahkan.');
    }

    public function update(Request $request, Faq $faq)
    {
        $faq->update($this->validated($request));

        return back()->with('success', 'FAQ diperbarui.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('success', 'FAQ dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:3000'],
            'sort_order' => ['nullable', 'integer'],
        ]);
        $data['sort_order'] ??= 0;

        return $data + ['is_active' => $request->boolean('is_active')];
    }
}
