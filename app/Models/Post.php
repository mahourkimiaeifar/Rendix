<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category_id',
        'author_id',
        'image_path',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'views',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
        'is_featured' => 'boolean',
        'category_id' => 'integer',
        'author_id' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public static function statusOptions(): array
    {
        return [
            'draft' => 'پیش‌نویس',
            'published' => 'منتشرشده',
            'archived' => 'بایگانی',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function readingTime(): int
    {
        $text = strip_tags((string) $this->content);
        $chars = mb_strlen($text);

        return max(1, (int) ceil($chars / 900));
    }

    public function displayExcerpt(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        $text = trim(strip_tags((string) $this->content));

        if (mb_strlen($text) <= 180) {
            return $text;
        }

        return mb_substr($text, 0, 180) . '…';
    }

    public function imageUrl(): string
    {
        if (! $this->image_path) {
            return '';
        }

        return Storage::disk('public')->url($this->image_path);
    }

    public function relatedPosts(int $limit = 3): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return self::query()
                ->published()
                ->where('id', '!=', $this->id)
                ->when($this->category_id, fn ($q) => $q->where('category_id', $this->category_id))
                ->ordered()
                ->limit($limit)
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}