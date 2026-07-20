<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubDistrict extends Model
{
    public function district() {
        return $this->belongsTo(District::class);
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

    protected $table = 'sub_districts';


}
