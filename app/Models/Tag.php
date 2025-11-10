<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * Many-to-Many: Contact ↔ Tag
     */
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_tags')
                    ->withTimestamps();
    }
}
