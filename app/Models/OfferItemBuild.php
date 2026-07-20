<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OfferItemBuild extends Model
{
        use HasUuids;

    protected $table = 'offer_item_builds';
    protected $keyType = 'string';
    public $incrementing = false;
        protected $fillable = [
        'offer_build_id',
        'item_name',
        'category_name',
        'uraian_name',
        'volume',
        'satuan',
        'price',
        'total',
        'sort_order',
    ];



        public function offer()
    {
        return $this->belongsTo(OfferBuild::class);
    }
}
