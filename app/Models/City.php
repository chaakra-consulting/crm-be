<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    public function scopeFilterProvince($query, $provinceId)
    {
        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }
        return $query;
    }

    /**
     * Relasi ke provinsi
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
