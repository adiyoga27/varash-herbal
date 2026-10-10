<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'title', 'slug', 'excerpt', 'body', 'image', 'is_published', 'published_at', 'meta_title', 'meta_description'])]
class Article extends Model
{
    use LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'published_at' => 'datetime'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function imageUrl(): string
    {
        if (! $this->image) {
            return asset('images/rempah.webp');
        }

        return Str::startsWith($this->image, ['http://', 'https://', '/images']) ? asset($this->image) : Storage::url($this->image);
    }

    public function readingTime(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 200));
    }

    public function seoDescription(): string
    {
        return $this->meta_description ?: Str::limit(strip_tags($this->excerpt ?: $this->body), 155);
    }
}
