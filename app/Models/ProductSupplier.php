<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductSupplier extends Pivot
{
    protected $table = 'product_supplier';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'supplier_id',
        'product_id',
        'stock',
        'buying_prices',
        'selling_prices',
        'tax_percentage',
        'discount',
        'label'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // RELASI KE SUPPLIER
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
