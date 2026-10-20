<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'role',
        'company',
        'content',
        'rating',
        'avatar_path',
        'project_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id', 'desc');
    }

    public function avatarUrl(): string
    {
        if (! $this->avatar_path) {
            return '';
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/u', trim($this->name));

        if (count($parts) >= 2) {
            return mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1);
        }

        return mb_substr($this->name, 0, 1);
    }
}