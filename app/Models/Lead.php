<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $guarded = [];

    function source()
    {
        return $this->belongsTo(Source::class, 'ad_source');
    }
}
