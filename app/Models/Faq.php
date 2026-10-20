<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'category',
        'location',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id', 'desc');
    }

    public function scopeForLocation(Builder $query, string $location): Builder
    {
        return $query->where(function (Builder $q) use ($location) {
            $q->where('location', 'all')
              ->orWhere('location', $location);
        });
    }

    public static function locationOptions(): array
    {
        return [
            'all' => 'همه صفحات',
            'home' => 'صفحه اصلی',
            'services' => 'خدمات',
            'projects' => 'نمونه‌کارها',
            'contact' => 'تماس با ما',
            'about' => 'درباره ما',
        ];
    }

    public static function categoryOptions(): array
    {
        return [
            'general' => 'عمومی',
            'web-design' => 'طراحی وبسایت',
            'seo' => 'سئو',
            'marketing' => 'دیجیتال مارکتینگ',
            'support' => 'پشتیبانی',
            'price' => 'قیمت و قرارداد',
        ];
    }

    public function locationLabel(): string
    {
        return self::locationOptions()[$this->location] ?? $this->location;
    }

    public function categoryLabel(): string
    {
        return self::categoryOptions()[$this->category] ?? $this->category;
    }
}