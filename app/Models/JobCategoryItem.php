<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCategoryItem extends Model
{
    protected $table = 'job_category_items';
    protected $casts = [
        'product_supplier_id' => 'string',
    ];
    protected $fillable = [
        'job_category_id',
        'category',

        'product_id',
        'labor_cost_id',
        'equipment_cost_id',
        'product_supplier_id',
        'name',     
        'code',
        'unit',
        'supplier_id',
        'coefisien',
        'base_unit_price',
        'total_price',

        'overhead',
        'profit',
        'subtotal',
        'grand_total',
    ];


    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function labor()
    {
        return $this->belongsTo(LaborCost::class, 'labor_cost_id');
    }

    public function equipment()
    {
        return $this->belongsTo(EquipmentCost::class, 'equipment_cost_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

        public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function productSupplier()
{
    return $this->belongsTo(ProductSupplier::class, 'product_supplier_id');
}

    public function getSupplierNameAttribute()
{
    if (!$this->product_id) return null;

    $ps = \App\Models\ProductSupplier::where('product_id', $this->product_id)
        ->orderBy('selling_prices')
        ->with('supplier')
        ->first();

    return $ps?->supplier?->name;
}

}

