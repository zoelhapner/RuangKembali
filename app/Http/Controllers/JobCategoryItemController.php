<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ProductSupplier;
use App\Models\LaborCost;
use App\Models\EquipmentCost;
use App\Models\JobCategoryItem;


class JobCategoryItemController extends Controller
{
    // public function changeSupplier(Request $request, JobCategoryItem $item)
    // {
    //     $request->validate([
    //         'supplier_id' => 'required|exists:suppliers,id'
    //     ]);

    //     $pivot = ProductSupplier::where('product_id', $item->product_id)
    //         ->where('supplier_id', $request->supplier_id)
    //         ->firstOrFail();

    //     $item->update([
    //         'supplier_id' => $request->supplier_id,
    //         'base_unit_price' => $pivot->selling_prices,
    //         'total_price' => $item->coefisien * $pivot->selling_prices,
    //     ]);

    //     return response()->json([
    //         'base_unit_price' => $item->base_unit_price,
    //         'total_price' => $item->total_price,
    //     ]);
    // }

    public function changeSupplier(Request $request, JobCategoryItem $item)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id'
        ]);

        $pivot = ProductSupplier::where('product_id', $item->product_id)
            ->where('supplier_id', $request->supplier_id)
            ->firstOrFail();

        $item->update([
            'supplier_id' => $request->supplier_id,
            'base_unit_price' => $pivot->selling_prices,
            'total_price' => $item->coefisien * $pivot->selling_prices,
        ]);

        // Hitung ulang subtotal category ini
        $items = JobCategoryItem::where('job_category_id', $item->job_category_id)->get();

        $totalLabor = $items->where('category','labor')->sum('total_price');
        $totalProduct = $items->where('category','product')->sum('total_price');
        $totalEquipment = $items->where('category','equipment')->sum('total_price');

        $subTotal = $totalLabor + $totalProduct + $totalEquipment;

        $jobCategory = $item->jobCategory;

        $overheadValue = $subTotal * ($jobCategory->overhead_percent / 100);
        $profitValue   = $subTotal * ($jobCategory->profit_percent / 100);
        $grandTotal    = $subTotal + $overheadValue + $profitValue;
        $jobCategory->update([
            'subtotal' => $subTotal,
            'overhead_value' => $overheadValue,
            'profit_value' => $profitValue,
            'grand_total' => $grandTotal,
        ]);

        return response()->json([
            'item' => [
                'id' => $item->id,
                'base_unit_price' => $item->base_unit_price,
                'total_price' => $item->total_price,
            ],
            'summary' => [
                'subtotal' => $subTotal,
                'overhead_value' => $overheadValue,
                'profit_value' => $profitValue,
                'grand_total' => $grandTotal,
            ]
        ]);
    }

    public function changeUraian(Request $request, JobCategoryItem $item)
{
    $value = $request->value;

    [$type, $id] = explode('_', $value);

    DB::transaction(function () use ($item, $type, $id, $request) {

        if ($type === 'product') {

            $pivot = ProductSupplier::with('product')->findOrFail($id);

            $item->update([
                'product_id'           => $pivot->product_id,
                'product_supplier_id'  => $pivot->id,
                'labor_cost_id'        => null,
                'equipment_cost_id'    => null,

                'name' => $pivot->product->name,
                'code' => $pivot->product->sku_code,
                'unit' => $pivot->product->unit_1_name
                    ?: $item->unit
                    ?: 'pcs',
            ]);

        } elseif ($type === 'labor') {

            $labor = LaborCost::findOrFail($id);

            $item->update([
                'labor_cost_id'        => $labor->id,
                'product_id'           => null,
                'product_supplier_id'  => null,
                'equipment_cost_id'    => null,

                'name' => $labor->description,
                'code' => $labor->code ?? '-',
                'unit' => $labor->unit,
            ]);

        } elseif ($type === 'equipment') {

            $eq = EquipmentCost::findOrFail($id);

            $item->update([
                'equipment_cost_id'    => $eq->id,
                'product_id'           => null,
                'product_supplier_id'  => null,
                'labor_cost_id'        => null,

                'name' => $eq->description,
                'code' => $eq->code ?? '-',
                'unit' => $eq->unit,
            ]);
        }

        $item->jobCategory->update([
            'effective_labor'     => $request->effective_labor !== null ? (float) $request->effective_labor : null,
            'effective_product'   => $request->effective_product !== null ? (float) $request->effective_product : null,
            'effective_equipment' => $request->effective_equipment !== null ? (float) $request->effective_equipment : null,
        ]);

        $item->jobCategory->refresh();

        \App\Services\RabRecalculator::recalcItemAndParent($item);
    });

    $item->refresh();

    return response()->json([
        'success' => true,
        'item' => [
            'base_unit_price' => $item->base_unit_price,
            'total_price'     => $item->total_price,
            'code'            => $item->code,
            'unit'            => $item->unit,
        ],
        'summary' => [
            'subtotal'       => $item->jobCategory->subtotal,
            'overhead_value' => $item->jobCategory->overhead_value,
            'profit_value'   => $item->jobCategory->profit_value,
            'grand_total'    => $item->jobCategory->grand_total,
        ]
    ]);
}

}