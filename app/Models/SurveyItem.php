<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'order_no',
        'description',
        'remark',
    ];

    public function survey()
{
    return $this->belongsTo(Survey::class);
}

}
