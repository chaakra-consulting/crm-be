<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SDMUserProject extends Model
{
    protected $connection = "sdm";
    protected $table = "tb_users_projects";
}
