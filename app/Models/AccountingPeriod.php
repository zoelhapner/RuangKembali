<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model
{
    use HasUuid;

    protected $table = 'accounting_periods';

    protected $fillable = [
        'license_id',
        'period_name',
        'start_date',
        'end_date',
        'is_closed',
        'closed_by',
        'closed_at',
    ];

     protected $casts = [
        'is_closed' => 'boolean',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

     public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
