<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRequestNote extends Model
{
    protected $fillable = [
        'project_request_id',
        'user_id',
        'body',
    ];

    public function request()
    {
        return $this->belongsTo(ProjectRequest::class, 'project_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}