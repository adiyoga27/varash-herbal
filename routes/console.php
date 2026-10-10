<?php

use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bersihkan activity log lebih dari 180 hari setiap bulan (jalankan `php artisan schedule:work` atau cron schedule:run)
Schedule::call(function () {
    $deleted = ActivityLog::where('created_at', '<', now()->subDays(180))->delete();
    ActivityLogger::log('purged', "Pembersihan otomatis: {$deleted} log > 180 hari dihapus", logName: 'system');
})->monthly()->name('prune-activity-logs');
