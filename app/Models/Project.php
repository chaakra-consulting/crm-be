<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'mysql';
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];


    public function bukukas()
    {
        return $this->belongsTo(ProjectBukukas::class, 'project_bukukas_id');
    }

    public function picProject()
    {
        return $this->belongsTo(User::class, 'pic_project_user_id');
    }

    public function picCompany()
    {
        return $this->belongsTo(User::class, 'pic_company_user_id');
    }
}
