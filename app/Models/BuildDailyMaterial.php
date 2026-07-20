<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildDailyMaterial extends Model
{
    protected $fillable = [
        'daily_report_id',
        'nama_bahan',
        'qty',
        'diterima',
        'ditolak'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
    ];

    public function report()
    {
        return $this->belongsTo(BuildDailyReport::class, 'daily_report_id');
    }
}
