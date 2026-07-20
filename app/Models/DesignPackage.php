<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class DesignPackage extends Model
{
    use HasUuid;

    protected $table = 'design_packages';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
    'name',
    'price_meter',
    ];

        public function items()
    {
        return $this->hasMany(DesignPackageItem::class);
    }
}
