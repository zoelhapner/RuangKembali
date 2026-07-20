<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'project_id',
        'created_by',
        'contact_name',
        'contact_phone',
        'site_area',
        'building_area',
        'notes',
        'consultant_signed',
        'client_signed',
        'employee_id',
        'signed_at',
        'documentation',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(ConsultationItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

        public function employee()
{
    return $this->belongsTo(Employee::class, 'employee_id');
}

        public function documents()
    {
        return $this->hasMany(ConsultationDocument::class);
    }

        public function documentations()
    {
        return $this->hasMany(ConsultationDocumentation::class);
    }
}
