<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function Provinces() {
        return $this->hasMany(Province::class);
    }
}
