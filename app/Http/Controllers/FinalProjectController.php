<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectFinal;
use Illuminate\Support\Facades\Storage;

class FinalProjectController extends Controller
{

public function store(Request $request, Project $project)
{
    $request->validate([
        'document' => 'required|file|mimes:zip,pdf,rar|max:51200', // max 50MB
    ]);

    // Pastikan pelunasan sudah approve
    $finalInvoice = $project->invoices()
        ->where('invoice_type', 'final')
        ->whereNotNull('approved_at')
        ->first();

    if (!$finalInvoice) {
        abort(403, 'Pelunasan belum disetujui.');
    }

    if ($project->finalDocument) {
        Storage::delete($project->finalDocument->document_path);
        $project->finalDocument()->delete();
    }

    $path = $request->file('document')
        ->store('final-projects', 'public');

    $project->finalDocument()->updateOrCreate(
        ['project_id' => $project->id],
        [
            'document_path' => $path,
            'uploaded_at'   => now(),
        ]
    );

    // Complete level "Hasil Proyek"
    $finalLevel = $project->levels()
        ->where('level_name', 'Cetak & Softcopy')
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
    if ($project->finalDocument) {
        Storage::delete($project->finalDocument->document_path);
        $project->finalDocument()->delete();
    }

    return back()->with('success', 'Hasil proyek berhasil dihapus');
}


}
