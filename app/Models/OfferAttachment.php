<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferAttachment extends Model
{

    function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
