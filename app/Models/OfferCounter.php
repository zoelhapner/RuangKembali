<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferCounter extends Model
{
    protected $fillable = [
        'type',
        'year',
        'last_number',
    ];
}
