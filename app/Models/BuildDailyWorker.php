<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildDailyWorker extends Model
{
    protected $fillable = [
        'daily_report_id',
        'worker_id',
        'keahlian',
        'jumlah',
        'alat'
    ];

    public function report()
    {
        return $this->belongsTo(BuildDailyReport::class, 'daily_report_id');
    }
        public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
