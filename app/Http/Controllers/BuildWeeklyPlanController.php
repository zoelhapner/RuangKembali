<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuildPlanWeek;
use App\Models\BuildProcessItem;
use DB;

class BuildWeeklyPlanController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'build_plan_id' => 'required|exists:build_plans,id',
            'week_no'       => 'required|integer',
            'plan_percent'  => 'nullable|numeric|min:0|max:100',
        ]);

        $plan = BuildPlanWeek::updateOrCreate(

            [
                'build_plan_id' => $request->build_plan_id,
                'week_no'       => $request->week_no,

            ],
            [
                'plan_percent'  => $request->plan_percent ?? 0,
            ]
        );
        return response()->json([
            'success' => true,
            'data'    => $plan
        ]);
    }
}