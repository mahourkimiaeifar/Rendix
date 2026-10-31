<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateReport extends Model
{
    protected $fillable = ['template_id', 'reporter_email', 'description', 'status'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}