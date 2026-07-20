<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class OfferBuild extends Model
{
    use HasUuid;

    protected $table = 'offer_builds';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'approved_at' => 'datetime',
        'contract_date' => 'date', // sekalian rapihin
    ];

    protected $fillable = [
        'project_id',
        'rab_process_id',
        'offer_number',
        'offer_date',
        'contact_name',
        'volume',
        'price_meter',
        'total_price',
        'discount',
        'tax_rate',
        'total_tax',
        'shipping',
        'grand_total',
        'notes',
        'created_by',
        'contract_number',
        'contract_date',
        'approved_at',
        'approved_by'
    ];

    public function items()
    {
        return $this->hasMany(OfferItemBuild::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function rab()
{
    return $this->belongsTo(RabProcess::class, 'rab_process_id');
}

    public function groupedItems()
{
    return $this->items
        ->groupBy('category')
        ->map(function ($items) {
            return $items;
        });
}

}
