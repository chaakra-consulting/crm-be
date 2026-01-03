<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class ProjectBukukas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'bukukas';
    protected $table = 'sales_invoices';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    public function scopeByPicCompany($query, $userId = null)
    {
        return $query->when($userId, function ($q) use ($userId) {

            $projectDb = config('database.connections.mysql.database');

            $q->whereExists(function ($sub) use ($userId, $projectDb) {
                $sub->select(DB::raw(1))
                    ->from($projectDb . '.projects')
                    ->whereColumn(
                        $projectDb . '.projects.project_bukukas_id',
                        'sales_invoices.id'
                    )
                    ->where(
                        $projectDb . '.projects.pic_company_user_id',
                        $userId
                    );
            });
        });
    }

    public function scopeByPicProject($query, $userId = null)
    {
        return $query->when($userId, function ($q) use ($userId) {

            $projectDb = config('database.connections.mysql.database');

            $q->whereExists(function ($sub) use ($userId, $projectDb) {
                $sub->select(DB::raw(1))
                    ->from($projectDb . '.projects')
                    ->whereColumn(
                        $projectDb . '.projects.project_bukukas_id',
                        'sales_invoices.id'
                    )
                    ->where(
                        $projectDb . '.projects.pic_project_user_id',
                        $userId
                    );
            });
        });
    }

    public function local()
    {
        $instance = $this->hasOne(Project::class, 'project_bukukas_id');
        $instance->getRelated()->setConnection(config('database.default'));
        return $instance;
    }

    public function item()
    {
        $instance = $this->hasOne(ProjectItemsBukukas::class, 'fid_invoices');
        return $instance;
    }

    public function payments() {
        return $this->hasMany(ProjectPaymentBukukas::class, 'fid_sales_invoice');
    }

    public function tax() {
        return $this->belongsTo(TaxBukukas::class, 'fid_tax');
    }

    public function companyBukukas1()
    {
        return $this->belongsTo(CompanyBukukas::class, 'fid_cust');
    }

    public function companyBukukas2()
    {
        return $this->belongsTo(CompanyBukukas::class, 'fid_custtt');
    }

    public function companyBukukas3()
    {
        return $this->belongsTo(CompanyBukukas::class, 'fid_custttt');
    }

    public function getCompanyBukukasAttribute()
    {
        if (!empty($this->fid_cust) && $this->fid_cust != 0) {
            return $this->companyBukukas1()->first();
        }

        if (!empty($this->fid_custtt) && $this->fid_custtt != 0) {
            return $this->companyBukukas2()->first();
        }

        if (!empty($this->fid_custttt) && $this->fid_custttt != 0) {
            return $this->companyBukukas3()->first();
        }

        return null;
    }
}
