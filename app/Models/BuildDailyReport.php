<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildDailyReport extends Model
{
    protected $fillable = [
        'project_id',
        'tanggal',
        'cuaca',
        'jam_mulai',
        'jam_selesai',
        'total_jam',
        'pekerjaan',
        'catatan',
        'mk_id',
        'kontraktor_ttd_id',
        'created_by',
        'is_libur'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];
    protected $appends = [
        'minggu',
        'tanggal_formatted',
        'jam_mulai_formatted',
        'jam_selesai_formatted',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function workers()
    {
        return $this->hasMany(BuildDailyWorker::class, 'daily_report_id');
    }

    public function materials()
    {
        return $this->hasMany(BuildDailyMaterial::class, 'daily_report_id');
    }

        public function works()
    {
        return $this->hasMany(BuildDailyWork::class, 'build_daily_report_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function documentations()
{
    return $this->hasMany(DailyDocumentation::class);
}

public function workTimes()
{
    return $this->hasMany(BuildDailyWorkTime::class);
}
public function mkEmployee()
{
    return $this->belongsTo(Employee::class, 'mk_id');
}

public function kontraktorEmployee()
{
    return $this->belongsTo(Employee::class, 'kontraktor_ttd_id');
}
public function getMingguAttribute()
{
    $start = \Carbon\Carbon::parse($this->project->start_date);
    $current = \Carbon\Carbon::parse($this->tanggal);

    return floor($start->diffInDays($current) / 7) + 1;
}
public function getTanggalFormattedAttribute()
{
    return $this->tanggal
        ? \Carbon\Carbon::parse($this->tanggal)
            ->locale('id')
            ->translatedFormat('l, d F Y')
        : null;
}

public function getJamMulaiFormattedAttribute()
{
    return $this->jam_mulai 
        ? \Carbon\Carbon::parse($this->jam_mulai)->format('H:i')
        : null;
}

public function getJamSelesaiFormattedAttribute()
{
    return $this->jam_selesai 
        ? \Carbon\Carbon::parse($this->jam_selesai)->format('H:i')
        : null;
}
}
