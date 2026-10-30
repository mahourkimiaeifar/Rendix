<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    protected $fillable = [
        'email',
        'token',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Subscriber $subscriber): void {
            if (blank($subscriber->token)) {
                $subscriber->token = Str::random(48);
            }
        });
    }
}