<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsultationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'order_no',
        'description',
        'remark',
    ];

    public function consultation()
{
    return $this->belongsTo(Consultation::class);
}

}
