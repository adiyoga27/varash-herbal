<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['email', 'name', 'token', 'status', 'source', 'ip_address', 'unsubscribed_at'])]
class Subscriber extends Model
{
    use LogsActivity, Notifiable;

    protected function casts(): array
    {
        return ['unsubscribed_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (Subscriber $subscriber) {
            $subscriber->token ??= Str::random(48);
        });
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}
