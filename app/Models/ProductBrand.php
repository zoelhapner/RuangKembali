<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class ProductBrand extends Model
{
    use HasFactory;

    protected $table = 'product_brands';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'factory_origin',
    ];
    
    public function products()
{
    return $this->hasMany(Product::class);
}

}
