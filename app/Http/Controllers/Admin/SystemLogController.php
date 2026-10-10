<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

/** Penampil file log Laravel (storage/logs/*.log) untuk debugging error aplikasi. */
class SystemLogController extends Controller
{
    private const MAX_BYTES = 512 * 1024;

    public function index(Request $request)
    {
        $files = collect(File::glob(storage_path('logs/*.log')))
            ->sortByDesc(fn ($f) => filemtime($f))
            ->map(fn ($f) => ['name' => basename($f), 'size' => filesize($f), 'modified' => filemtime($f)])
            ->values();

        $current = $request->query('file', $files->first()['name'] ?? null);
        $entries = collect();

        if ($current && $files->contains('name', $current)) {
            $entries = $this->parse(storage_path('logs/'.basename($current)));
            if ($level = $request->query('level')) {
                $entries = $entries->where('level', strtoupper($level))->values();
            }
        }

        return view('admin.logs.system', compact('files', 'current', 'entries'));
    }

    public function clear(Request $request)
    {
        $file = basename((string) $request->input('file'));
        $path = storage_path('logs/'.$file);
        abort_unless(str_ends_with($file, '.log') && File::exists($path), 404);

        File::put($path, '');
        ActivityLogger::log('cleared', "Mengosongkan file log {$file}", null, [], 'system', 'warning');

        return back()->with('success', "File {$file} dikosongkan.");
    }

    private function parse(string $path)
    {
        $size = filesize($path);
        $handle = fopen($path, 'r');
        if ($size > self::MAX_BYTES) {
            fseek($handle, -self::MAX_BYTES, SEEK_END);
        }
        $content = stream_get_contents($handle);
        fclose($handle);

        preg_match_all('/^\[(\d{4}-\d{2}-\d{2}[ T][\d:.+\-]+)\] (\w+)\.(\w+): (.*?)(?=^\[\d{4}-\d{2}-\d{2}[ T]|\z)/ms', $content, $matches, PREG_SET_ORDER);

        return collect($matches)->map(function ($m) {
            [$message, $trace] = array_pad(explode("\n", trim($m[4]), 2), 2, '');

            return ['date' => $m[1], 'env' => $m[2], 'level' => strtoupper($m[3]), 'message' => $message, 'trace' => $trace];
        })->reverse()->take(300)->values();
    }
}
