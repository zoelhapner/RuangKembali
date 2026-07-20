<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationDocument extends Model
{
    protected $fillable = ['consultation_id', 'file_path'];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}