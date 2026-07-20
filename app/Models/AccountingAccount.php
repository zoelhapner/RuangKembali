<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class AccountingAccount extends Model
{
    use HasUuid;

    protected $table = 'accounting_accounts';
    protected $casts = [
        'category' => 'string',
        'sub_category' => 'string',
        'is_active' => 'boolean',
        'is_parent' => 'boolean',
        'initial_balance' => 'decimal:2',
        'person_type' => 'string',
    ];
    
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    
    protected $fillable = [
        'account_code',
        'account_name',
        'category',
        'is_parent',
        'parent_id',
        'license_id',
        'sub_category',
        'initial_balance',
        'is_active',
        'person_type',
    ];

      public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

        public function details()
    {
        return $this->hasMany(AccountingJournalDetail::class, 'account_id');
    }
}
