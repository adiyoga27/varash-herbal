<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.logs.index', [
            'logs' => $this->filtered($request)->with('user')->paginate(25)->withQueryString(),
            'users' => User::orderBy('name')->get(['id', 'name']),
            'logNames' => ActivityLog::distinct()->orderBy('log_name')->pluck('log_name'),
            'events' => ActivityLog::distinct()->orderBy('event')->pluck('event'),
            'summary' => [
                'today' => ActivityLog::whereDate('created_at', today())->count(),
                'warnings' => ActivityLog::whereIn('level', ['warning', 'danger', 'error'])->where('created_at', '>=', now()->subDays(7))->count(),
                'failed_logins' => ActivityLog::where('event', 'login_failed')->where('created_at', '>=', now()->subDays(7))->count(),
                'total' => ActivityLog::count(),
            ],
        ]);
    }

    public function show(ActivityLog $log)
    {
        return view('admin.logs.show', ['log' => $log->load('user')]);
    }

    public function export(Request $request)
    {
        $logs = $this->filtered($request)->with('user')->limit(10000)->get();
        ActivityLogger::log('exported', 'Export '.$logs->count().' activity log ke CSV', null, $request->query(), 'system');

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Waktu', 'Pengguna', 'Modul', 'Event', 'Level', 'Deskripsi', 'IP', 'Perangkat', 'Method', 'URL']);
            foreach ($logs as $l) {
                fputcsv($out, [$l->created_at, $l->user?->name ?? 'Guest/Sistem', $l->log_name, $l->event, $l->level, $l->description, $l->ip_address, $l->device(), $l->method, $l->url]);
            }
            fclose($out);
        }, 'activity-log-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function purge(Request $request)
    {
        $days = (int) $request->validate(['days' => ['required', 'integer', 'min:7', 'max:3650']])['days'];
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();
        ActivityLogger::log('purged', "Menghapus {$deleted} log yang lebih lama dari {$days} hari", null, ['days' => $days], 'system', 'warning');

        return back()->with('success', "{$deleted} log lama dihapus.");
    }

    private function filtered(Request $request)
    {
        $query = ActivityLog::latest('id');

        if ($search = $request->query('q')) {
            $query->where(fn ($q) => $q->where('description', 'like', "%{$search}%")->orWhere('ip_address', 'like', "%{$search}%"));
        }
        foreach (['user_id', 'log_name', 'event', 'level'] as $field) {
            if ($value = $request->query($field)) {
                $query->where($field, $value);
            }
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }
}
