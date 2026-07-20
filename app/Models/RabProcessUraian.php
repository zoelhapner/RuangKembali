<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabProcessUraian extends Model
{
    protected $table = 'rab_process_uraians';

    protected $fillable = [
        'rab_process_id',
        'category_id',
        'name',
        'order_no',
        'uraian_key'
    ];
public function items()
{
    return $this->hasMany(RabProcessItem::class,'uraian_id')
        ->orderBy('order_no');
}
public function images()
{
    return $this->hasMany(RabUraianImage::class, 'uraian_id');
}
public function categori()
{
    return $this->belongsTo(RabProcessCategory::class, 'category_id');
}
    public function category()
{
    return $this->belongsTo(JobCategory::class,'job_category_id');
}
}
