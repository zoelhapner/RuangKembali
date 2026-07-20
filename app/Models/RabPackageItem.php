<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class RabPackageItem extends Model
{
    use HasUUid;

    protected $table = 'rab_packages_items';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'rab_package_id',
        'category',
        'item_name',
        'is_optional',
    ];
    
    public function package()
    {
        return $this->belongsTo(RabPackage::class);
    }
}
