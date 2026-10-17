<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'service',
        'message',
        'source_page',
        'status',
        'priority',
        'next_follow_up_at',
        'last_contact_at',
        'is_archived',
        'metadata',
    ];

    protected $casts = [
        'next_follow_up_at' => 'datetime',
        'last_contact_at' => 'datetime',
        'is_archived' => 'boolean',
        'metadata' => 'array',
    ];

    public static function statusOptions(): array
    {
        return [
            'new' => 'جدید',
            'contacted' => 'پیگیری شد',
            'negotiation' => 'در مذاکره',
            'proposal' => 'پیشنهاد داده شد',
            'won' => 'بسته موفق',
            'lost' => 'رد شد',
        ];
    }

    public static function priorityOptions(): array
    {
        return [
            'low' => 'کم',
            'normal' => 'عادی',
            'high' => 'زیاد',
            'urgent' => 'فوری',
        ];
    }

    public static function serviceOptions(): array
    {
        return [
            'web-design' => 'طراحی وبسایت',
            'seo' => 'سئو',
            'digital-marketing' => 'دیجیتال مارکتینگ',
            'social-media' => 'شبکه‌های اجتماعی',
            'ecommerce' => 'فروشگاه اینترنتی',
            'support' => 'پشتیبانی سایت',
            'other' => 'سایر / نیاز به راهنمایی',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeNeedsFollowUp($query)
    {
        return $query
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now())
            ->where('is_archived', false);
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function priorityLabel(): string
    {
        return self::priorityOptions()[$this->priority] ?? $this->priority;
    }

    public function serviceLabel(): string
    {
        return self::serviceOptions()[$this->service] ?? $this->service;
    }
}