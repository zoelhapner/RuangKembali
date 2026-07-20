<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class FinalBuildProjectController extends Controller
{

public function store(Request $request, Project $project)
{
    $request->validate([
        'document' => 'required|file|mimes:zip,pdf,rar|max:51200', // max 50MB
    ]);

    if ($project->finalBuild) {
        Storage::delete($project->finalBuild->document_path);
        $project->finalBuild()->delete();
    }

    $path = $request->file('document')
        ->store('final-projects', 'public');

    $project->finalBuild()->updateOrCreate(
        ['project_id' => $project->id],
        [
            'document_path' => $path,
            'uploaded_at'   => now(),
        ]
    );

    // Complete level "Hasil Proyek"
    $finalLevel = $project->levels()
        ->where('level_name', 'Serah Terima')
        ->first();

    if ($finalLevel && !$finalLevel->is_completed) {
        $finalLevel->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    return back()->with('success', 'Hasil proyek berhasil diupload.');
}

public function destroy(Project $project)
{
    if ($project->finalBuild) {
        Storage::delete($project->finalBuild->document_path);
        $project->finalBuild()->delete();
    }

    return back()->with('success', 'Hasil proyek berhasil dihapus');
}


}
