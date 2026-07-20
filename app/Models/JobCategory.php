<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    protected $table = 'job_categories';
    protected $fillable = [
        'bidang',
        'kode_group',
        'nama_group',
        'kode',
        'kode_urut',
        'nama_pekerjaan',
        'satuan',
        'overhead_percent',
        'profit_percent',
        'overhead_value',
        'profit_value',
        'subtotal',
        'grand_total',
        'subtotal_labor',
        'subtotal_material',
        'subtotal_equipment',
        'effective_labor',
        'effective_product',
        'effective_equipment'
    ];

    public function items()
{
    return $this->hasMany(JobCategoryItem::class, 'job_category_id');
}


    //     public function laborCost()
    // {
    //     return $this->belongsTo(LaborCost::class);
    // }

}
