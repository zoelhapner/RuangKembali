<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildDailyWork extends Model
{
    /** @use HasFactory<\Database\Factories\App\Models\BuildDailyWorkFactory> */
    use HasFactory;

    protected $fillable = [
        'build_daily_report_id',
        'rab_process_item_id',
        'volume',
        'satuan',
        'keterangan',
        'uraian_manual'
    ];

    public function report()
    {
        return $this->belongsTo(BuildDailyReport::class, 'build_daily_report_id');
    }
    public function rabProcessItem()
{
    return $this->belongsTo(RabProcessItem::class,'rab_process_item_id');
}
}
