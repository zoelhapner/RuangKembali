<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildProgressSnapshot extends Model
{
    protected $fillable = [
        'project_id',
        'week_no',
        'progress_weighted',
    ];

    protected $casts = [
        'progress_weighted' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
