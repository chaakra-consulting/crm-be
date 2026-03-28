<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    function attachment()
    {
        return $this->hasMany(OfferAttachment::class);
    }
}
