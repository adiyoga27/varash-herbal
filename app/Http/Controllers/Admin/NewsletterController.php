<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use App\Models\Subscriber;
use App\Services\SubscriberBroadcaster;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index()
    {
        return view('admin.newsletters.index', [
            'newsletters' => Newsletter::with('author')->latest()->paginate(15),
            'activeSubscribers' => Subscriber::active()->count(),
        ]);
    }

    public function create()
    {
        return view('admin.newsletters.form', ['activeSubscribers' => Subscriber::active()->count()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:20000'],
        ]);

        $newsletter = Newsletter::create($data + ['user_id' => $request->user()->id, 'status' => 'draft']);

        if ($request->input('action') === 'send') {
            $count = SubscriberBroadcaster::send($newsletter);

            return redirect()->route('admin.newsletters.index')->with('success', "Newsletter dikirim ke {$count} subscriber.");
        }

        return redirect()->route('admin.newsletters.index')->with('success', 'Draft newsletter disimpan.');
    }

    public function show(Newsletter $newsletter)
    {
        return view('admin.newsletters.show', ['newsletter' => $newsletter, 'activeSubscribers' => Subscriber::active()->count()]);
    }

    public function send(Newsletter $newsletter)
    {
        $count = SubscriberBroadcaster::send($newsletter);

        return back()->with('success', "Newsletter dikirim ke {$count} subscriber.");
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return redirect()->route('admin.newsletters.index')->with('success', 'Newsletter dihapus.');
    }
}
