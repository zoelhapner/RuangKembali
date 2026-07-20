<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuildPlans extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'build_process_item_id',
        'rab_item_id',
        'category_name',
        'category_order',
        'uraian_name',
        'uraian_order',
        'job_category_id',
        'item_name',
        'item_order',
        'volume',
        'price',
        'total',
        'satuan',
        'bobot_percent',
        'planned_progress',
        'status',
    ];

    protected $casts = [
        'volume' => 'decimal:2',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'bobot_percent' => 'decimal:6',
        'planned_progress' => 'decimal:6',
        'category_order' => 'integer',
        'uraian_order' => 'integer',
        'item_order' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function buildProcessItem()
    {
        return $this->belongsTo(BuildProcessItem::class);
    }

    public function rabItem()
    {
        return $this->belongsTo(RabItem::class, 'rab_item_id');
    }

    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function scopeOrdered($query)
    {
        return $query
            ->orderBy('category_order')
            ->orderBy('uraian_order')
            ->orderBy('item_order');
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 0, ',', '.');
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }

    public function getProgressPercentAttribute()
    {
        return round($this->planned_progress, 2) . '%';
    }
public function weeks()
{
    return $this->hasMany(
        BuildPlanWeek::class,
        'build_plan_id'
    );
}
}