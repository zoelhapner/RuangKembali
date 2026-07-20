<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProjectTaskFile extends Model
{
    use HasUuids;

    protected $fillable = [
        'project_task_id',
        'file_path',
        'file_name',
        'uploaded_by',
    ];

    public function task()
    {
        return $this->belongsTo(ProjectTask::class, 'project_task_id');
    }

        public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUploaderNameAttribute()
{
    if (!$this->uploader) {
        return 'Sistem';
    }

    return optional($this->uploader->employee?->user)->fullname
        ?? $this->uploader->fullname
        ?? 'Sistem';
}

public function getUploaderShortNameAttribute()
{
    $fullname = $this->uploader_name;

    if (!$fullname || $fullname === 'Sistem') {
        return $fullname;
    }

    $words = preg_split('/\s+/', trim($fullname));

    return implode(' ', array_slice($words, 0, 3));
}

}

