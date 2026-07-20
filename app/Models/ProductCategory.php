<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';
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
