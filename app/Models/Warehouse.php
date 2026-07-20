<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid;

class Warehouse extends Model
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

    public function postalCode() {
        return $this->belongsTo(PostalCode::class);
    }

     public function stocks()
    {
        return $this->hasMany(WarehouseStock::class, 'warehouse_id', 'id');
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(WarehouseTransfer::class, 'from_warehouse_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(WarehouseTransfer::class, 'to_warehouse_id');
    }

    public function responsibleEmployee()
{
    return $this->belongsTo(Employee::class, 'responsible_person', 'id');
}




    use HasFactory, HasUuid;

    protected $table = 'warehouses';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

        protected $fillable = [
        'name',
        'responsible_person',
        'phone',
        'email',
        'address',
        'province_id',
        'city_id',
        'district_id',
        'sub_district_id',
        'postal_code_id',
        'description',
    ];

}
