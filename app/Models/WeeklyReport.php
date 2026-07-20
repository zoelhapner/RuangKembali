<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyReport extends Model
{
    protected $fillable = [
        'project_id',
        'minggu',
        'capaian',
        'kendala',
        'rencana',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}