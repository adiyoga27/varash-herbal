<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['category_id', 'name', 'slug', 'sku', 'size', 'short_description', 'description', 'ingredients', 'benefits', 'advantages', 'usage', 'price', 'sale_price', 'stock', 'weight', 'image', 'is_featured', 'is_active', 'sort_order', 'meta_title', 'meta_description'])]
class Product extends Model
{
    use LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'advantages' => 'array',
            'usage' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'price' => 'integer',
            'sale_price' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function finalPrice(): int
    {
        return $this->sale_price && $this->sale_price < $this->price ? $this->sale_price : $this->price;
    }

    public function hasDiscount(): bool
    {
        return $this->finalPrice() < $this->price;
    }

    public function discountPercent(): int
    {
        return $this->hasDiscount() ? (int) round(100 - ($this->finalPrice() / $this->price * 100)) : 0;
    }

    public function imageUrl(): string
    {
        if (! $this->image) {
            return asset('images/logo.png');
        }

        return Str::startsWith($this->image, ['http://', 'https://']) ? $this->image : Storage::url($this->image);
    }

    public function seoTitle(): string
    {
        return $this->meta_title ?: Str::title(Str::lower($this->name)).' Original';
    }

    public function seoDescription(): string
    {
        return $this->meta_description ?: Str::limit(strip_tags($this->short_description ?: $this->description), 155);
    }
}
