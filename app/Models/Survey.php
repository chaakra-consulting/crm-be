<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $guarded = [];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function pic_project()
    {
        return $this->belongsTo(User::class, 'project_pic');
    }
}
