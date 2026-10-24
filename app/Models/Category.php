<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public static function activeOptions(): array
    {
        try {
            return self::query()
                ->active()
                ->ordered()
                ->pluck('name', 'id')
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function postCount(): int
    {
        try {
            return $this->posts()->where('status', 'published')->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }
}