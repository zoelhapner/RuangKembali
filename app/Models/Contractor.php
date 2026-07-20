<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Contractor extends Model
{

    public function user()
{
    return $this->belongsTo(User::class);
}


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

    /**
     * 🔹 Accessor: Status aktif/nonaktif readable
     */
   

    public static function generateContractorId()
    {
        $lastNumber = self::selectRaw("MAX(CAST(SUBSTRING(contractor_id, 3) AS INTEGER)) as max_contractor_id")->value('max_contractor_id');
        $newNumber = ($lastNumber ?? 0) + 1;

        return 'K-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function getDefaultAttributes($user)
    {
        return [
            'contractor_id' => self::generateContractorId(),
            // 'province_id' => 15,
            // 'city_id' => 234,
            // 'district_id' => 3372,
            // 'sub_district_id' => 42178,
            // 'postal_code_id' => 42178,

        ];
    }


    use HasFactory, HasUuids;

    protected $table = 'contractors';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'contractor_id',
        'contractor_name',
        'contractor_phone',
        'contractor_address',
        'province_id',
        'city_id',
        'district_id',
        'sub_district_id',
        'postal_code_id',
        'notes',
        'is_active',
    ];

}
