<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RabImage extends Model
{
    protected $table = 'rab_images';

    protected $appends = ['url'];

    protected $fillable = [
        'path',
    ];

        public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }
}
