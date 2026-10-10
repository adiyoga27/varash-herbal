<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['user_id', 'log_name', 'event', 'description', 'subject_type', 'subject_id', 'properties', 'level', 'ip_address', 'user_agent', 'method', 'url', 'created_at'])]
class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['properties' => 'array', 'created_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    public function levelDot(): string
    {
        return match ($this->level) {
            'warning' => 'bg-amber-500',
            'danger', 'error' => 'bg-red-500',
            'success' => 'bg-emerald-500',
            default => 'bg-sky-500',
        };
    }

    public function levelBadge(): string
    {
        return match ($this->level) {
            'warning' => 'bg-amber-100 text-amber-800',
            'danger', 'error' => 'bg-red-100 text-red-700',
            'success' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-sky-100 text-sky-800',
        };
    }

    public function device(): string
    {
        $ua = (string) $this->user_agent;

        $os = match (true) {
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Mac OS') => 'macOS',
            str_contains($ua, 'Linux') => 'Linux',
            $ua === 'console' => 'Console',
            default => 'Lainnya',
        };
        $browser = match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/') => 'Opera',
            str_contains($ua, 'Chrome/') => 'Chrome',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Safari/') => 'Safari',
            default => '',
        };

        return trim("{$browser} · {$os}", ' ·');
    }
}
