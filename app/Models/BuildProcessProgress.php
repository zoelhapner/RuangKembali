<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildProcessProgress extends Model
{
    protected $fillable = [
        'build_process_item_id',
        'weekly_report_id',
        'week_no',
        'progress_percent',
        'note',
    ];

    public function item()
    {
        return $this->belongsTo(BuildProcessItem::class,'build_process_item_id');
    }

    public function weeklyReport()
    {
        return $this->belongsTo(BuildWeeklyReport::class);
    }
}
