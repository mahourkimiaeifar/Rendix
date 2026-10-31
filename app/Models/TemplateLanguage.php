<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateLanguage extends Model
{
    protected $fillable = ['template_id', 'language', 'color'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}