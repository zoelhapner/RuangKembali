<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductType extends Model
{
    use HasFactory;

    protected $table = 'product_types';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function products()
{
    return $this->hasMany(Product::class);
}

}
