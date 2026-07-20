<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasUuid;

    protected $guard_name = 'web';
    

    public $incrementing = false;
    protected $keyType = 'string';

    public function licenses()
{
    return $this->belongsToMany(License::class, 'license_user', 'user_id', 'license_id');
}


    public function licenseholder()
{
    return $this->hasOne(LicenseHolder::class);
}

public function employee()
{
    return $this->hasOne(Employee::class);
}

public function student()
{
    return $this->hasOne(Student::class);
}

public function getActiveLicenseName()
{
    return $this->licenses->first()?->name ?? 'AHA Right Brain';
}

public function getNameAttribute()
{
    if ($this->employee && is_string($this->employee->fullname ?? null)) {
        return $this->employee->fullname;
    }

    return $this->attributes['name'] ?? 'User';
}

public function getPhotoUrlAttribute()
{
    if ($this->employee && $this->employee->photo) {
        return asset('storage/photos/' . $this->employee->photo);
    }

    if ($this->licenseholder && $this->licenseholder->photo) {
        return asset('storage/photos/' . $this->licenseholder->photo);
    }

    // fallback default
    return asset('ahasquare.png');
}

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
