<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function cities() {
        return $this->hasMany(City::class);
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
