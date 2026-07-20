<?php

namespace App\Services;

use App\Models\JobCategory;
use App\Models\JobCategoryItem;
use App\Models\LaborCost;
use App\Models\EquipmentCost;
use App\Models\ProductSupplier;
use Illuminate\Support\Facades\DB;

class RabRecalculator
{
    public static function recalcCategory(JobCategory $category)
    {
        $totals = JobCategoryItem::where('job_category_id', $category->id)
            ->selectRaw("
                SUM(CASE WHEN category = 'labor' THEN total_price ELSE 0 END) as total_labor,
                SUM(CASE WHEN category = 'product' THEN total_price ELSE 0 END) as total_product,
                SUM(CASE WHEN category = 'equipment' THEN total_price ELSE 0 END) as total_equipment
            ")
            ->first();

        // $subTotal = ($totals->total_labor ?? 0)
        //           + ($totals->total_product ?? 0)
        //           + ($totals->total_equipment ?? 0);
        $totalLabor     = $totals->total_labor ?? 0;
        $totalProduct   = $totals->total_product ?? 0;
        $totalEquipment = $totals->total_equipment ?? 0;

        $effectiveLabor     = $category->effective_labor ?? $totalLabor;
        $effectiveProduct   = $category->effective_product ?? $totalProduct;
        $effectiveEquipment = $category->effective_equipment ?? $totalEquipment;

        $subTotal = $effectiveLabor + $effectiveProduct + $effectiveEquipment;

        $overheadValue = $subTotal * ($category->overhead_percent / 100);
        $profitValue   = $subTotal * ($category->profit_percent / 100);

        $category->update([
            'subtotal'        => $subTotal,
            'overhead_value'  => $overheadValue,
            'profit_value'    => $profitValue,
            'grand_total'     => $subTotal + $overheadValue + $profitValue,
        ]);
    }

    public static function recalcItem(JobCategoryItem $item)
    {
        $price = null;

        if ($item->labor_cost_id) {
            $price = $item->labor?->base_unit_price;
        }
        elseif ($item->equipment_cost_id) {
            $price = $item->equipment?->base_unit_price;
        }
        elseif ($item->product_supplier_id) {
            // 🔥 PAKAI RELASI (NO QUERY ULANG)
            $price = $item->productSupplier?->selling_prices;
        }

        if ($price === null) {
            $price = $item->base_unit_price;
        }

        $item->update([
            'base_unit_price' => $price,
            'total_price'     => $item->coefisien * $price,
        ]);
    }

    public static function recalcItems($items)
    {
        foreach ($items as $item) {
            self::recalcItem($item);
        }
    }

    public static function recalcByLabor($laborId)
    {
        $items = JobCategoryItem::where('labor_cost_id', $laborId)
            ->with(['jobCategory', 'labor'])
            ->get()
            ->groupBy('job_category_id');

        foreach ($items as $group) {
            self::recalcItems($group);
            self::recalcCategory($group->first()->jobCategory);
        }
    }

    public static function recalcByEquipment($equipmentId)
    {
        $items = JobCategoryItem::where('equipment_cost_id', $equipmentId)
            ->with(['jobCategory', 'equipment'])
            ->get()
            ->groupBy('job_category_id');

        foreach ($items as $group) {
            self::recalcItems($group);
            self::recalcCategory($group->first()->jobCategory);
        }
    }

    public static function recalcByPivot($pivotId)
    {
        $items = JobCategoryItem::where('product_supplier_id', $pivotId)
            ->with(['jobCategory', 'productSupplier'])
            ->get()
            ->groupBy('job_category_id');

        foreach ($items as $group) {
            self::recalcItems($group);
            self::recalcCategory($group->first()->jobCategory);
        }
    }

    public static function recalcItemAndParent(JobCategoryItem $item)
    {
        $item->load(['jobCategory', 'labor', 'equipment', 'productSupplier']);

        self::recalcItem($item);
        self::recalcCategory($item->jobCategory);
    }

    public static function recalcAll()
    {
        JobCategoryItem::with(['labor', 'equipment', 'productSupplier'])
            ->chunk(500, function ($items) {

                $grouped = $items->groupBy('job_category_id');

                foreach ($grouped as $group) {
                    self::recalcItems($group);
                    self::recalcCategory($group->first()->jobCategory);
                }
            });
    }
}