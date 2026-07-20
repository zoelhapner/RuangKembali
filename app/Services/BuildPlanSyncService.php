<?php

namespace App\Services;

use App\Models\BuildPlans;
use App\Models\BuildProcessItem;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class BuildPlanSyncService
{
    public function syncFull(Project $project): void
    {
        DB::transaction(function () use ($project) {

            $project->load(
                'offer.rab.categories.uraians.items'
            );

            $subtotalPekerjaan = $project->offer->rab
                ->categories
                ->flatMap(fn($c) => $c->uraians)
                ->flatMap(fn($u) => $u->items)
                ->sum('total');

            /*
            |--------------------------------------------------------------------------
            | Ambil data RAB terbaru
            |--------------------------------------------------------------------------
            */

            $rabItems = collect();

            foreach ($project->offer->rab->categories as $cIndex => $category) {

                foreach ($category->uraians as $uIndex => $uraian) {

                    foreach ($uraian->items as $iIndex => $item) {

                        $bobot = $subtotalPekerjaan > 0
                            ? ($item->total / $subtotalPekerjaan) * 100
                            : 0;

                        $rabItems->push([

                            'rab_item_id' => $item->id,

                            'category_name' => $category->name,
                            'uraian_name' => $uraian->name,

                            'job_category_id' => $item->job_category_id,
                            'item_name' => $item->job_name,

                            'price' => $item->price,
                            'volume' => $item->volume,
                            'total' => $item->total,
                            'satuan' => $item->satuan,

                            'bobot_percent' => $bobot,

                            'category_order' => $cIndex,
                            'uraian_order' => $uIndex,
                            'item_order' => $iIndex,
                        ]);
                    }
                }
            }

            $currentRabIds = $rabItems
                ->pluck('rab_item_id')
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | Existing Build Items
            |--------------------------------------------------------------------------
            */

            $existingBuildPlan = BuildPlans::where(
                'project_id',
                $project->id
            )
            ->get();

            /*
            |--------------------------------------------------------------------------
            | DELETE ITEM YANG SUDAH HILANG DI RAB
            |--------------------------------------------------------------------------
            */

            foreach ($existingBuildPlan as $buildPlan) {

                $hasProgress = $buildPlan->weeks()
                    ->where('plan_percent', '>', 0)
                    ->exists();

                $stillExists = in_array(
                    $buildPlan->rab_item_id,
                    $currentRabIds
                );

                if (!$stillExists && !$hasProgress) {
                    $buildPlan->delete();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE / UPDATE
            |--------------------------------------------------------------------------
            */

            foreach ($rabItems as $row) {

                $buildPlan = BuildPlans::firstOrNew([

                    'project_id' => $project->id,
                    'rab_item_id' => $row['rab_item_id'],
                ]);

                $hasProgress =
                    $buildPlan->exists
                    &&
                    $buildPlan->weeks()
                        ->where('plan_percent', '>', 0)
                        ->exists();

                /*
                |--------------------------------------------------------------------------
                | ITEM SUDAH ADA PROGRESS
                |--------------------------------------------------------------------------
                */

                if ($hasProgress) {

                    /*
                    |--------------------------------------------------------------------------
                    | Metadata aman boleh update
                    |--------------------------------------------------------------------------
                    */

                    $buildPlan->update([

                        'category_name' => $row['category_name'],
                        'uraian_name' => $row['uraian_name'],

                        'item_name' => $row['item_name'],

                        'satuan' => $row['satuan'],

                        'category_order' => $row['category_order'],
                        'uraian_order' => $row['uraian_order'],
                        'item_order' => $row['item_order'],

                        /*
                        |--------------------------------------------------------------------------
                        | Bobot tetap update mengikuti RAB terbaru
                        |--------------------------------------------------------------------------
                        */

                        'bobot_percent' => $row['bobot_percent'],
                    ]);

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | BELUM ADA PROGRESS
                |--------------------------------------------------------------------------
                */

                $buildPlan->fill([

                    'project_id' => $project->id,

                    'category_name' => $row['category_name'],
                    'uraian_name' => $row['uraian_name'],

                    'job_category_id' => $row['job_category_id'],
                    'item_name' => $row['item_name'],

                    'price' => $row['price'],
                    'volume' => $row['volume'],
                    'total' => $row['total'],

                    'satuan' => $row['satuan'],

                    'bobot_percent' => $row['bobot_percent'],

                    'category_order' => $row['category_order'],
                    'uraian_order' => $row['uraian_order'],
                    'item_order' => $row['item_order'],
                ]);

                $buildPlan->save();
            }
            $plans = BuildPlans::where('project_id', $project->id)
                ->get();

            $totalBobot = $plans->sum('bobot_percent');

            $selisih = round(100 - $totalBobot, 10);

            if (abs($selisih) > 0.0000001 && $plans->isNotEmpty()) {

                $lastPlan = BuildPlans::where('project_id', $project->id)
                    ->orderByDesc('category_order')
                    ->orderByDesc('uraian_order')
                    ->orderByDesc('item_order')
                    ->first();

                if ($lastPlan) {

                    $lastPlan->update([
                        'bobot_percent' => $lastPlan->bobot_percent + $selisih
                    ]);
                }
                $plans = BuildPlans::where('project_id', $project->id)
                    ->get(['build_process_item_id', 'bobot_percent']);

                foreach ($plans as $plan) {
                    if ($plan->build_process_item_id) {
                        BuildProcessItem::where('id', $plan->build_process_item_id)
                            ->update([
                                'bobot_percent' => $plan->bobot_percent,
                            ]);
                    }
                }
            }
        });
    }
}