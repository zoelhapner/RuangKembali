<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Config;
use App\Traits\HasUuid;
use App\Models\AccountingAccount;
use Illuminate\Support\Str;

class License extends Model
{
    public function province() {

    return $this->belongsTo(Province::class);
}

    public function city() {

    return $this->belongsTo(City::class);
}

    public function district() {

    return $this->belongsTo(District::class);
}

    public function subDistrict() {

    return $this->belongsTo(SubDistrict::class);
}

    public function postalCode()
{
    return $this->belongsTo(PostalCode::class, 'postal_code_id');
}

public function owners()
{
    return $this->belongsToMany(User::class, 'license_user', 'license_id', 'user_id');
}

public function employees()
{
    return $this->belongsToMany(Employee::class, 'employee_license', 'license_id', 'employee_id');
}

public function students()
{
    return $this->hasMany(Student::class);
}

  public function accounts()
    {
        return $this->hasMany(AccountingAccount::class);
    }

 public function journals()
    {
        return $this->hasMany(AccountingJournal::class);
    }

    public function notifications()
{
    return $this->hasMany(LicenseNotification::class);
}

public function pusatUser()
{
    // Misal ambil Super-Admin pertama
    return \App\Models\License::where('name', 'AHA Right Brain')->first();
    
    // Kalau mau spesifik ke email tertentu, pakai ini:
    // return \App\Models\User::where('email', 'admin@pusat.com')->first();
    
    // Kalau mau spesifik ke ID tertentu, pakai ini:
    // return \App\Models\User::find(1);
}


    public function getDisplayNameAttribute()
{
    return is_array($this->name)
        ? $this->name['id'] ?? reset($this->name)
        : $this->name;
}

    use HasFactory, HasUuid;

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'license_id',
        'license_type',
        'name',
        'email',
        'address',
        'province_id',
        'city_id',
        'district_id',
        'sub_district_id',
        'postal_code_id',
        'phone',
        'join_date',
        'expired_date',
        'contract_agreement_number',
        'contract_document',
        'document_form',
        'status',
        'building_type',
        'building_status',
        'building_rent_expired_date',
        'building_area',
        'building_condition',
        'building_has_ac',
        'instagram',
        'facebook_page',
        'tiktok',
        'youtube',
        'google_maps',
        'landing_page_student_registration',
    ];

    protected static function booted()
{
    static::created(function ($license) {
        $defaultAccounts = Config::get('accounting_defaults.accounts');

        foreach ($defaultAccounts as $acc) {
            AccountingAccount::create([
                'id' => Str::uuid(),
                'license_id' => $license->id,
                'category' => $acc['category'] ?? null,
                'account_code' => $acc['account_code'] ?? null,
                'account_name' => $acc['account_name'] ?? null,
                'person_type' => $acc['person_type'] ?? null,           
                'is_active' => true,
                'is_parent' => $acc['is_parent'] ?? false,
                'initial_balance' => 0,
                'sub_category' => $acc['sub_category'] ?? null,
            ]);
        }
    });
}
}
