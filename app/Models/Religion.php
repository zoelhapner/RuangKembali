<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    public function licenseHolders()
{
    return $this->hasMany(LicenseHolder::class, 'religion_id');
}

public function employees()
{
    return $this->hasMany(Employee::class, 'religion_id');
}

public function users()
{
    return $this->hasMany(User::class, 'student_id');
}

}
