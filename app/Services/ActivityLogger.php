<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityLogger
{
    public static function log(
        string $event,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        string $logName = 'system',
        string $level = 'info',
        ?int $userId = null,
    ): ?ActivityLog {
        try {
            $request = app()->runningInConsole() ? null : request();

            return ActivityLog::create([
                'user_id' => $userId ?? Auth::id(),
                'log_name' => $logName,
                'event' => $event,
                'description' => mb_substr($description, 0, 250),
                'subject_type' => $subject?->getMorphClass(),
                'subject_id' => $subject?->getKey(),
                'properties' => $properties ?: null,
                'level' => $level,
                'ip_address' => $request?->ip(),
                'user_agent' => $request ? mb_substr((string) $request->userAgent(), 0, 500) : 'console',
                'method' => $request?->method(),
                'url' => $request?->fullUrl(),
                'created_at' => now(),
            ]);
        } catch (Throwable $e) {
            // Logging tidak boleh menggagalkan proses utama
            Log::warning('Gagal menulis activity log: '.$e->getMessage());

            return null;
        }
    }
}
