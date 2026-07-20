<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildPlanWeek extends Model
{
    protected $fillable = [
        'build_plan_id',
        'week_no',
        'plan_percent',
    ];

    protected $casts = [
        'week_no'      => 'integer',
        'plan_percent' => 'double',
    ];

    public function buildPlan()
    {
        return $this->belongsTo(BuildPlans::class);
    }

    public function getFormattedPercentAttribute()
    {
        return number_format(
            $this->plan_percent,
            2
        );
    }
}