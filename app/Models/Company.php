<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'mysql';
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];


    public function bukukas()
    {
        return $this->belongsTo(CompanyBukukas::class, 'company_bukukas_id');
    }

    public function picContact()
    {
        return $this->belongsTo(Contact::class, 'pic_contact_id');
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

}
