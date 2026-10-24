<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectRequest extends Model
{
    protected $fillable = [
        'user_id',
        'tracking_code',
        'name',
        'email',
        'phone',
        'company',
        'role',
        'service',
        'current_website',
        'current_website_url',
        'business_description',
        'project_goals',
        'competitors',
        'references',
        'timeline',
        'budget',
        'message',
        'status',
        'metadata',
        'submitted_at',
        'responded_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'submitted_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProjectRequest $request): void {
            if (blank($request->tracking_code)) {
                $request->tracking_code = self::generateTrackingCode();
            }

            if (blank($request->status)) {
                $request->status = 'new';
            }

            if (blank($request->submitted_at)) {
                $request->submitted_at = now();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notes()
    {
        return $this->hasMany(ProjectRequestNote::class);
    }

    public function attachments()
    {
        return $this->hasMany(ProjectRequestAttachment::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    public static function generateTrackingCode(): string
    {
        do {
            $code = 'RX-PRJ-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (static::query()->where('tracking_code', $code)->exists());

        return $code;
    }

    public static function statusOptions(): array
    {
        return [
            'new' => 'دریافت شد',
            'reviewing' => 'در حال بررسی',
            'meeting_scheduled' => 'جلسه تعیین شد',
            'proposal_sent' => 'پیشنهاد ارسال شد',
            'waiting_client' => 'در انتظار تأیید شما',
            'in_progress' => 'در حال اجرا',
            'delivered' => 'تحویل شد',
            'cancelled' => 'لغو شد',
            'archived' => 'بایگانی شد',
        ];
    }

    public static function timelineOptions(): array
    {
        return [
            'urgent' => 'فوری — کمتر از ۲ هفته',
            'short' => 'کوتاه — ۲ تا ۴ هفته',
            'normal' => 'معمولی — ۱ تا ۲ ماه',
            'flexible' => 'انعطاف‌پذیر',
            'not_sure' => 'مطمئن نیستم',
        ];
    }

    public static function budgetOptions(): array
    {
        return [
            'under_10' => 'زیر ۱۰ میلیون تومان',
            '10_25' => '۱۰ تا ۲۵ میلیون تومان',
            '25_50' => '۲۵ تا ۰ میلیون تومان',
            '50_100' => '۵۰ تا ۱۰۰ میلیون تومان',
            'over_100' => 'بالای ۱۰۰ میلیون تومان',
            'not_sure' => 'مطمئن نیستم',
        ];
    }

    public static function websiteStatusOptions(): array
    {
        return [
            'yes' => 'سایت دارم',
            'no' => 'سایت ندارم',
            'not_sure' => 'مطمئن نیستم',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    public function timelineLabel(): string
    {
        return self::timelineOptions()[$this->timeline] ?? ($this->timeline ?: '—');
    }

    public function budgetLabel(): string
    {
        return self::budgetOptions()[$this->budget] ?? ($this->budget ?: '—');
    }

    public function websiteStatusLabel(): string
    {
        return self::websiteStatusOptions()[$this->current_website] ?? ($this->current_website ?: '—');
    }
}