<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildDailyWorkTime extends Model
{
    protected $fillable = [
        'build_daily_report_id',
        'jam_mulai',
        'jam_selesai',
        'total_jam',
        'cuaca',
        'keterangan',
    ];

    public function report()
    {
        return $this->belongsTo(BuildDailyReport::class, 'build_daily_report_id');
    }
}
