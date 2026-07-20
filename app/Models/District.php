<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public function City() {
        return $this->belongsTo(City::class);
    }

    public function licenses() {
        return $this->hasMany(License::class);
    }

     public function license_holder() {
        return $this->hasMany(LicenseHolder::class);
    }

     public function employees() {
        return $this->hasMany(Employee::class);
    }

    public function students() {
        return $this->hasMany(Student::class);
    }
}
