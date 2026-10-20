<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'short_description',
        'challenge',
        'solution',
        'result',
        'technologies',
        'demo_url',
        'client_name',
        'delivered_at',
        'sort_order',
        'is_active',
        'is_featured',
        'image_path',
        'gallery',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'delivered_at' => 'date',
        'gallery' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id', 'desc');
    }

    public static function categoryOptions(): array
    {
        return [
            'web-design' => 'طراحی وبسایت',
            'ecommerce' => 'فروشگاه اینترنتی',
            'seo' => 'سئو و رشد ارگانیک',
            'branding' => 'برندینگ',
            'campaign' => 'کمپین تبلیغاتی',
            'social-media' => 'شبکه اجتماعی',
            'other' => 'سایر',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categoryOptions()[$this->category] ?? $this->category;
    }

    public function imageUrl(): string
    {
        if (! $this->image_path) {
            return '';
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function galleryUrls(): array
    {
        return collect($this->gallery ?? [])
            ->filter()
            ->map(fn (string $path): string => Storage::disk('public')->url($path))
            ->values()
            ->all();
    }

    public function technologyList(): array
    {
        return collect(explode(',', (string) $this->technologies))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->values()
            ->all();
    }
}