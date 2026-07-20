<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Carbon;

class Survey extends Model
{
    use HasUuids;

    protected $table = 'surveys';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'project_id',
        'created_by',
        'contact_name',
        'survey_date',
        'site_area',
        'building_area',
        'survey_time',
        'notes',
        'consultant_signed',
        'client_signed',
        'signed_at',
        'document',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(SurveyItem::class);
    }

        public function images()
    {
        return $this->hasMany(SurveyImage::class);
    }

        public function documentations()
    {
        return $this->hasMany(SurveyDocumentation::class);
    }

            public function documents()
    {
        return $this->hasMany(SurveyDocument::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

        public function employees()
{
    return $this->belongsToMany(Employee::class, 'survey_employees');
}

     public function getSurveyDateFormattedAttribute()
    {
        return $this->survey_date ? Carbon::parse($this->survey_date)->format('d/m/Y') : '-';
    }
}
