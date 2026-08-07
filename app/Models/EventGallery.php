<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class EventGallery extends Model
{
    use HasUuid;

    protected $fillable = [
        'event_id',
        'image',
        'caption',
        'sort_order',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
