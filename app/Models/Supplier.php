<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Supplier extends Model
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

public function products()
{
    return $this->belongsToMany(Product::class, 'product_supplier')
                ->using(ProductSupplier::class)
                ->withPivot(['id', 'buying_prices', 'selling_prices', 'special_prices', 'stock', 'label'])
                ->withTimestamps();
}


    /**
     * 🔹 Accessor: Status aktif/nonaktif readable
     */
   

    public static function generateNis()
    {
        $lastNumber = self::selectRaw("MAX(CAST(SUBSTRING(supplier_id, 3) AS INTEGER)) as max_supplier_id")->value('max_supplier_id');
        $newNumber = ($lastNumber ?? 0) + 1;

        return 'S-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function getDefaultAttributes($user)
    {
        return [
            'supplier_id' => self::generateNis(),
            // 'province_id' => 15,
            // 'city_id' => 234,
            // 'district_id' => 3372,
            // 'sub_district_id' => 42178,
            // 'postal_code_id' => 42178,

        ];
    }


    use HasFactory, HasUuids;

    protected $table = 'suppliers';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'supplier_id',
        'name',
        'phone',
        'address',
        'province_id',
        'city_id',
        'district_id',
        'sub_district_id',
        'postal_code_id',
        'notes',
        'is_active',
    ];

}
