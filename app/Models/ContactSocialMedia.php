<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSocialMedia extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_social_medias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];
}
