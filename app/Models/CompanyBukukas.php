<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyBukukas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'bukukas';
    protected $table = 'master_customers';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    public function local()
    {
        $instance = $this->hasOne(Company::class, 'company_bukukas_id');
        $instance->getRelated()->setConnection(config('database.default'));
        return $instance;
    }
}
