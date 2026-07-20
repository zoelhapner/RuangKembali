<?php

namespace App\Observers;

use App\Services\JobCategoryItemService;
use App\Models\LaborCost;
use App\Models\JobCategoryItem;

class LaborCostObserver
{
    /**
     * Handle the LaborCost "created" event.
     */
    public function created(LaborCost $laborCost): void
    {
        //
    }

    /**
     * Handle the LaborCost "updated" event.
     */
    public function updated(LaborCost $labor): void
    {
        $items = JobCategoryItem::where('labor_cost_id', $labor->id)->get();

        foreach ($items as $item) {
            app(JobCategoryItemService::class)->syncPrice($item);
        }
    }

    /**
     * Handle the LaborCost "deleted" event.
     */
    public function deleted(LaborCost $laborCost): void
    {
        //
    }

    /**
     * Handle the LaborCost "restored" event.
     */
    public function restored(LaborCost $laborCost): void
    {
        //
    }

    /**
     * Handle the LaborCost "force deleted" event.
     */
    public function forceDeleted(LaborCost $laborCost): void
    {
        //
    }
}
