<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadNote extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'type',
        'body',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function typeOptions(): array
    {
        return [
            'note' => 'یادداشت داخلی',
            'call' => 'تماس تلفنی',
            'meeting' => 'جلسه',
            'email' => 'ایمیل',
            'whatsapp' => 'واتساپ',
            'telegram' => 'تلگرام',
        ];
    }

    public function typeLabel(): string
    {
        return self::typeOptions()[$this->type] ?? $this->type;
    }
}