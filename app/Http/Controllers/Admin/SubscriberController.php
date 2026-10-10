<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscriber::latest();
        if ($search = $request->query('q')) {
            $query->where('email', 'like', "%{$search}%");
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('admin.subscribers.index', [
            'subscribers' => $query->paginate(20)->withQueryString(),
            'activeCount' => Subscriber::active()->count(),
            'totalCount' => Subscriber::count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'unique:subscribers,email'], 'name' => ['nullable', 'string', 'max:100']]);
        Subscriber::create($data + ['source' => 'admin']);

        return back()->with('success', 'Subscriber ditambahkan.');
    }

    public function toggle(Subscriber $subscriber)
    {
        $active = $subscriber->status !== 'active';
        $subscriber->update(['status' => $active ? 'active' : 'unsubscribed', 'unsubscribed_at' => $active ? null : now()]);

        return back()->with('success', 'Status subscriber diperbarui.');
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', 'Subscriber dihapus.');
    }

    public function export()
    {
        $subscribers = Subscriber::orderBy('created_at')->get();
        ActivityLogger::log('exported', 'Export '.$subscribers->count().' subscriber ke CSV', null, [], 'subscriber');

        return response()->streamDownload(function () use ($subscribers) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Email', 'Nama', 'Status', 'Sumber', 'Tanggal Daftar']);
            foreach ($subscribers as $s) {
                fputcsv($out, [$s->email, $s->name, $s->status, $s->source, $s->created_at->format('Y-m-d H:i')]);
            }
            fclose($out);
        }, 'subscribers-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }
}
