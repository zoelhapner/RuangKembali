<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyDocument extends Model
{
    protected $fillable = ['survey_id', 'file_path'];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
