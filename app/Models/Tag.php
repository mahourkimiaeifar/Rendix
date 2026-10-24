<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag');
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