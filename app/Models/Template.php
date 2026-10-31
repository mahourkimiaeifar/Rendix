<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Template extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'features',
        'price',
        'demo_path',
        'has_support',
        'has_trial',
        'has_guarantee',
        'is_fullstack',
        'is_active',
        'views_count',
        'downloads_count',
    ];

    protected $casts = [
        'features' => 'array',
        'has_support' => 'boolean',
        'has_trial' => 'boolean',
        'has_guarantee' => 'boolean',
        'is_fullstack' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Template $template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }

    public function languages(): HasMany
    {
        return $this->hasMany(TemplateLanguage::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(TemplateFile::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(TemplateReport::class);
    }

    public function demoFile()
    {
        return $this->files()->where('is_demo', true)->first();
    }

    public function formattedPrice(): string
    {
        return number_format($this->price, 0, '.', ',') . ' تومان';
    }

    public function incrementViewsCount(): void
    {
        $this->increment('views_count');
    }

    public function incrementDownloadsCount(): void
    {
        $this->increment('downloads_count');
    }
}