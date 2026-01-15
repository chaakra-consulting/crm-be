<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SDMTask extends Model
{
    protected $connection = "sdm";
    protected $table = "tb_tasks";
    protected $guarded = [];
}
