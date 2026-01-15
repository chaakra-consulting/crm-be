<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SDMUserTask extends Model
{
    protected $connection = "sdm";
    protected $table = "tb_users_tasks";
    protected $guarded = [];
}
