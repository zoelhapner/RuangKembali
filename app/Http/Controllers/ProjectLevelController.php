<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectLevel;

class ProjectLevelController extends Controller
{
    public function complete(ProjectLevel $level)
{
    // 1. Selesaikan tahap ini
    $level->update([
        'is_completed' => true,
    ]);

    // 2. Ambil tahap berikutnya
    $next = ProjectLevel::where('project_id', $level->project_id)
            ->where('level_order', '>', $level->level_order)
            ->orderBy('level_order')
            ->first();

    // 3. Hidupkan tahap berikutnya
    if ($next && !$next->is_started) {
        $next->update(['is_started' => true]);
    }

    // 4. Jika tidak ada tahap tersisa → Project Selesai
    $remaining = ProjectLevel::where('project_id', $level->project_id)
                ->where('is_completed', false)
                ->count();

    if ($remaining === 0) {
        $level->project->update(['project_status' => 4]); // 4 = Selesai
    }

    return back()->with('success', 'Tahap selesai! Lanjut ke tahap berikutnya.');
}

public function reset(ProjectLevel $level)
{
    // 1. Reset tahap ini
    $level->update([
        'is_completed' => false,
        'is_started' => true, // tetap berjalan
    ]);

    // 2. Matikan semua tahap setelahnya
    ProjectLevel::where('project_id', $level->project_id)
        ->where('level_order', '>', $level->level_order)
        ->update([
            'is_started' => false,
            'is_completed' => false,
        ]);

    return back()->with('success', 'Tahap telah di-reset.');
}

    
}
