<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'icon',
        'short_description',
        'description',
        'metric_value',
        'metric_label',
        'sort_order',
        'is_active',
        'is_featured',
        'image_path',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public static function activeOptions(): array
    {
        try {
            return self::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('title', 'slug')
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function imageUrl(): string
    {
        if (! $this->image_path) {
            return '';
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function deleteImage(): void
    {
        if (! $this->image_path) {
            return;
        }

        Storage::disk('public')->delete($this->image_path);

        $this->image_path = null;
        $this->save();
    }
}