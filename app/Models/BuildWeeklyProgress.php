<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildWeeklyProgress extends Model
{
    protected $fillable = [
        'week_no',
        'volume',
        'bobot_percent',
        'build_process_item_id',
        'progress_percent',
        'just_kurang',
        'just_tambah',
        'just_baru'
    ];

    protected $casts = [
        'progress_percent' => 'decimal:2',
    ];

    public function weeklyReport()
    {
        return $this->belongsTo(BuildWeeklyReport::class);
    }

    public function item()
    {
        return $this->belongsTo(BuildProcessItem::class, 'build_process_item_id');
    }

    public function getWeightedAttribute()
    {
        return $this->progress_percent * ($this->item->bobot_percent / 100);
    }
}
