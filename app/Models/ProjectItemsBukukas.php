<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectItemsBukukas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'bukukas';
    protected $table = 'sales_invoices_items';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    public function salesInvoice()
    {
        $instance = $this->belongsTo(ProjectBukukas::class, 'fid_invoices');
        return $instance;
    }
}
