<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Planning extends Model
{
    use HasUuids;

    protected $table = 'plannings';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'planning_date',
        'planning_time',
        'survey_address',
        'same_address',
        'province_id',
        'survey_fee',
        'city_id',
        'district_id',
        'sub_district_id',
        'postal_code_id',
        'aproved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'planning_notes',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

public function level()
{
    return $this->hasOne(ProjectLevel::class, 'project_id', 'project_id')
                ->where('level_order', 2);
}

public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function subDistrict()
    {
        return $this->belongsTo(SubDistrict::class);
    }

    public function postalCode()
    {
        return $this->belongsTo(PostalCode::class, 'postal_code_id');
    }
}

