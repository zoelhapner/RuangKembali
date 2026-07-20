<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabProcessItem extends Model
{
    protected $table = 'rab_process_items';
    
    protected $fillable = [
    'rab_process_id',
    'job_category_id',
    'job_name',
    'satuan',
    'volume',
    'base_price',
    'price',
    'total',
    'uraian_id',
    'order_no'
];
public function category()
{
    return $this->belongsTo(JobCategory::class, 'job_category_id');
}

public function rab()
{
    return $this->belongsTo(RabProcess::class, 'rab_process_id');
}

public function getNamaPekerjaanAttribute()
{
    return $this->job_name;
}

}
