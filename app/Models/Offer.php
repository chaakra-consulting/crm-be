<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $guarded = [];
    function attachment()
    {
        return $this->hasMany(OfferAttachment::class);
    }
    function lead() {
        return $this->belongsTo(Lead::class, 'leads_id');
    }
}
