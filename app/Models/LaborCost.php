<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaborCost extends Model
{
    use HasFactory;

    protected $table = 'labor_costs';

    protected $fillable = [
        'code',
        'description',
        'unit',
        'base_unit_price',
        'notes',
    ];

    protected $casts = [
        'base_unit_price' => 'decimal:2',
    ];

        public function jobCategoryItems()
    {
        return $this->hasMany(JobCategoryItem::class);
    }
}
