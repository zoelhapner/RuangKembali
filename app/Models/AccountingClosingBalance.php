<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class AccountingClosingBalance extends Model
{
     use HasUuid;

    protected $table = 'accounting_closing_balances';

    protected $fillable = [
        'license_id',
        'account_id',
        'period_name',
        'closing_balance',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function license()
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    public function account()
    {
        return $this->belongsTo(AccountingAccount::class, 'account_id');
    }

    public function period()
    {
        return $this->belongsTo(AccountingPeriod::class, 'period_id');
    }
}
