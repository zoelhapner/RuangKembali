<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class InvoiceBuild extends Model
{
    use HasUuid;

    const TYPE_BUILD = 'build';
    const TYPE_JUSTEK = 'justek';

    protected $casts = [
        'invoice_date' => 'date',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
    ];

    protected $fillable = [
        'project_id',
        'invoice_number',
        'invoice_date',
        'invoice_type',
        'amount',
        'status',
        'approved_at',
        'approved_by',
        'approval_token',
        'rejected_at',
        'rejected_by',
        'reject_note',
        'downloaded_at',
        'approve_by_name',
        'approved_ip',
        'termin',
        'progress_start',
        'progress_end',
        'payment_percentage',
        'paid_at',
        'note',
        'nominal'
    ];

        public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
