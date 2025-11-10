<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contacts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * 🔹 Relasi ke user yang membuat contact
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 🔹 Relasi ke user yang menjadi owner contact
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /**
     * 🔹 Relasi ke sumber leads (source)
     */
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * 🔹 Relasi ke provinsi
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * 🔹 Relasi ke kota
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * 🔹 Relasi ke perusahaan (jika ada table companies)
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Many-to-Many: Contact ↔ Tag
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'contact_tags')
                    ->withTimestamps();
    }
}
