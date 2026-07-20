<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class ProductColor extends Model
{
    use HasFactory;

    protected $table = 'colors';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];
    
    public function products()
{
    return $this->belongsToMany(Product::class, 'product_color', 'color_id', 'product_id');
}

}
