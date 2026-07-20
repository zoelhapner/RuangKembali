<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractCounter extends Model
{
    protected $fillable = [
        'year',
        'last_number',
    ];
}
