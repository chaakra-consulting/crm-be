<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactTag extends Model
{
        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

}
