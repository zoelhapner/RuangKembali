<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

    class DailyDocumentation extends Model
{
    protected $fillable = [
        'build_daily_report_id',
        'category',
        'file_path',
        'file_name',
        'file_type'
    ];

    public function dailyReport()
    {
        return $this->belongsTo(BuildDailyReport::class);
    }
}