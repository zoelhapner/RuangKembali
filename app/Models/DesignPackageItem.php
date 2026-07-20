<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class DesignPackageItem extends Model
{
    use HasUUid;

    protected $table = 'design_packages_items';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'design_package_id',
        'category',
        'item_name',
        'is_optional',
    ];
    
    public function package()
    {
        return $this->belongsTo(DesignPackage::class);
    }
}
