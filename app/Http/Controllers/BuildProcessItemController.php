<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\BuildProcessItem;
use App\Models\JobCategory;
use DB;

class BuildProcessItemController extends Controller
{
public function updateBobot(Request $request)
{
    $project = Project::findOrFail($request->input('project_id'));

    if ($project->bobot_locked) {
        return response()->json([
            'ok' => false,
            'message' => 'Bobot sudah dikunci'
        ], 423);
    }

    $items = $request->input('items', []);
    $total = collect($items)->sum('bobot');

    DB::transaction(function () use ($items, $project, $total) {

        foreach ($items as $row) {

            BuildProcessItem::where('id', $row['id'])
                ->update([
                    'bobot_percent' => $row['bobot']
                ]);
        }

        // lock otomatis saat total 100%
        if (round($total, 2) == 100) {

            $project->update([
                'bobot_locked' => true
            ]);
        }
    });

    return response()->json([
        'ok' => true,
        'locked' => round($total, 2) == 100
    ]);
}
public function storeTambahan(Request $request)
{
    $request->validate([
        'project_id' => 'required|exists:projects,id',
        'parent_item_id' => 'required|exists:build_process_items,id',
        'job_category_id' => 'required|exists:job_categories,id',
    ]);

    return DB::transaction(function () use ($request) {

        $job = JobCategory::findOrFail($request->job_category_id);
        $parent = BuildProcessItem::findOrFail($request->parent_item_id);

        $exists = BuildProcessItem::where('parent_id', $parent->id)
            ->where('job_category_id', $job->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Pekerjaan tambahan sudah ada'
            ], 422);
        }

        $tambahan = BuildProcessItem::create([
            'project_id' => $request->project_id,
            'job_category_id' => $job->id,
            'uraian' => $job->nama_pekerjaan,
            'satuan' => $job->satuan ?? '-',
            'volume' => 0,
            'price' => $job->grand_total ?? 0,
            'bobot_percent' => 0,
            'is_tambahan' => true,
            'parent_id' => $parent->id,
            'sumber' => 'manual'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pekerjaan tambahan berhasil ditambahkan',
            'data' => [
                'id' => $tambahan->id,
                'uraian' => $tambahan->uraian,
                'satuan' => $tambahan->satuan,
                'volume' => $tambahan->volume,
                'price' => $tambahan->price,
                'parent_id' => $tambahan->parent_id,
            ]
        ]);
    });
}
}