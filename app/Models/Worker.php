<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Worker extends Model
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

public function scopeLoyalty($query, $level)
    {
        return $query->where('loyalty_level', $level);
    }

    /**
     * 🔹 Accessor: Format membership jadi huruf besar pertama (Silver, Gold, Platinum)
     */
    public function getLoyaltyLevelFormattedAttribute()
    {
        return ucfirst($this->loyalty_level);
    }

    /**
     * 🔹 Accessor: Status aktif/nonaktif readable
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    public static function generateWorkerId()
    {
        $lastNumber = self::selectRaw("MAX(CAST(SUBSTRING(worker_id, 3) AS INTEGER)) as max_worker_id")->value('max_worker_id');
        $newNumber = ($lastNumber ?? 0) + 1;

        return 'T-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function getDefaultAttributes($user)
    {
        return [
            'worker_id' => self::generateWorkerId(),
            // 'province_id' => 15,
            // 'city_id' => 234,
            // 'district_id' => 3372,
            // 'sub_district_id' => 42178,
            // 'postal_code_id' => 42178,
            // 'loyalty_level' => 1,

        ];
    }

    public function getReadableLoyaltyLevelAttribute()
    {
    return [
        1 => 'Silver',
        2 => 'Gold',
        3 => 'Platinum',
    ][$this->loyalty_level] ?? 'Tidak diketahui';
    }

    public function getReadableCategoryAttribute()
    {
    return [
        1 => 'Individu',
        2 => 'Perusahaan',
        3 => 'Instansi',
        3 => 'Lainnya',
    ][$this->loyalty_level] ?? 'Tidak diketahui';
    }

    use HasFactory, HasUuids;

    protected $table = 'workers';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'worker_id',
        'notes',
        'salary',
    ];

}
