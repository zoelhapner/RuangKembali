<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProjectTask extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $casts = [
        'started_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
        protected $fillable = [
        'project_id',
        'offer_id',
        'offer_item_id',
        'category',
        'task_name',
        'employee_id',
        'status',
        'progress',
        'started_at',
        'completed_at',
        'parent_task_id',
        'revision_number',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'reject_note',
    ];

    public function files()
    {
        return $this->hasMany(ProjectTaskFile::class, 'project_task_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

        public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // ProjectTask.php
    public function parent()
    {
        return $this->belongsTo(ProjectTask::class, 'parent_task_id');
    }

    public function revisions()
    {
        return $this->hasMany(ProjectTask::class, 'parent_task_id');
    }

    public function approvedBy()
{
    return $this->belongsTo(User::class, 'approved_by');
}

public function rejectedBy()
{
    return $this->belongsTo(User::class, 'rejected_by');
}



}
