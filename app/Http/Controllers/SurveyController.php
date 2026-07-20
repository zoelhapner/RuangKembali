<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyRequest;
use App\Models\Survey;
use App\Models\SurveyItem;
use App\Models\SurveyImage;
use App\Models\SurveyDocumentation;
use App\Models\SurveyDocument;
use App\Models\Project;
use App\Models\ProjectLevel;
use App\Services\ProjectNotifier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class SurveyController extends Controller
{

    public function store(SurveyRequest $request)
{
    abort_if(auth()->user()->cannot('lihat daftar proyek'), 403);
    $survey = null;
    $project = null;

    DB::transaction(function () use ($request, &$project, &$survey) {

        $data = $request->validated();

        $project = Project::with(['employee', 'customer'])
            ->findOrFail($data['project_id']);

        $consultantSigned = $request->boolean('consultant_signed');
        $clientSigned     = $request->boolean('client_signed');

        $signedAt = ($consultantSigned || $clientSigned) ? now() : null;

        $survey = Survey::create([
            'project_id'        => $data['project_id'],
            'created_by'        => auth()->id(),
            'survey_date'       => $data['survey_date'] ?? null,
            'survey_time'       => $data['survey_time'] ?? null,
            'contact_name'      => $data['contact_name'] ?? null,
            'contact_phone'     => $data['contact_phone'] ?? null,
            'site_area'         => $data['site_area'] ?? null,
            'building_area'     => $data['building_area'] ?? null,
            'notes'             => $data['notes'] ?? null,
            'consultant_signed' => $consultantSigned,
            'client_signed'     => $clientSigned,
            'signed_at'         => $signedAt,
        ]);

        if ($request->hasFile('documentation')) {
            foreach ($request->file('documentation') as $file) {
                $survey->documentations()->create([
                    'file_path' => $file->store('surveys/documentations', 'public')
                ]);
            }
        }

        if ($request->hasFile('result_images')) {
            foreach ($request->file('result_images') as $file) {
                $survey->images()->create([
                    'file_path' => $file->store('surveys/result-images', 'public')
                ]);
            }
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $survey->documents()->create([
                    'file_path' => $file->store('surveys/documents', 'public')
                ]);
            }
        }

        foreach ($data['items'] as $i => $item) {
            $survey->items()->create([
                'order_no'    => $i + 1,
                'description' => $item['description'],
                'remark'      => $item['remark'] ?? null,
            ]);
        }

        if ($clientSigned) {
            $level = ProjectLevel::where([
                'project_id'  => $project->id,
                'level_order' => 3,
            ])->first();

            if ($level) {
                $level->update(['is_completed' => true]);
                $level->employees()->sync($data['employee_id']);
            }

            ProjectLevel::where([
                'project_id'  => $project->id,
                'level_order' => 4,
            ])->update(['is_started' => true]);
        }
    });

    $event = 'survey_created';
    $cfg   = config("project_events.$event");

    $creator = auth()->user();

    $users = collect([$creator]);

    $level3 = $project->levels->where('level_order', 3)->first();
    if ($level3) {
        $users = $users->merge($level3->employees->pluck('user'));
    }

    if ($project->customer?->user) {
        $users->push($project->customer->user);
    }

    $users = $users->filter()->unique('id');

    foreach ($users as $user) {

        if ($user->id === $creator->id) {
            $role = 'created_self';
        } elseif ($project->customer?->user && $user->id === $project->customer->user->id) {
            $role = 'customer';
        } else {
            $role = 'assigned';
        }

        if (!isset($cfg['message'][$role])) continue;

        ProjectNotifier::notifyUsers(
            [$user],
            ProjectNotifier::makePayload($project, [
                'type'    => $event,
                'role'    => $role,
                'title'   => $cfg['title'],
                'message' => $cfg['message'][$role],
                'url'     => route('projects.create', ['project_id' => $project->id]),
            ])
        );
    }

    return redirect()
        ->route('projects.create', ['project_id' => $project->id])
        ->with('success', 'Form survey berhasil disimpan.');
}

    public function update(Request $request, Survey $survey)
{
    abort_if(auth()->user()->cannot('ubah data proyek'), 403);
    
    $validated = $request->validate([
        'survey_date' => 'required|date',
        'survey_time' => 'required',
        'employee_id'   => 'required|array',
        'employee_id.*'  => 'uuid',
        'project_id' => 'required|uuid|exists:projects,id',
        'contact_name' => 'nullable|string|max:255',
        'site_area' => 'nullable|string|max:255',
        'building_area' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ]);

    $survey->update([
        'survey_date'   => $request->survey_date,
        'survey_time'   => $request->survey_time,
        'notes'         => $request->notes,
        'project_id'    => $request->project_id,
        'contact_name'  => $request->contact_name,
        'site_area'     => $request->site_area,
        'building_area' => $request->building_area,
        'consultant_signed' => $request->has('consultant_signed') ? 1 : 0,
        'client_signed'     => $request->has('client_signed') ? 1 : 0,
    ]);

    $survey->items()->delete();

    if ($request->has('items')) {
        foreach ($request->items as $i => $item) {

            if (
                (!isset($item['description']) || trim($item['description']) === '') &&
                (!isset($item['remark']) || trim($item['remark']) === '')
            ) {
                continue;
            }

            $survey->items()->create([
                'order_no'    => $i + 1,
                'description' => $item['description'] ?? '',
                'remark'      => $item['remark'] ?? '',
            ]);
        }
    }

    if ($request->hasFile('documents')) {
        foreach ($request->file('documents') as $file) {
            $path = $file->store('surveys/documents', 'public');
            $survey->documents()->create(['file_path' => $path]);
        }
    }

    if ($request->hasFile('result_images')) {
        foreach ($request->file('result_images') as $file) {
            $path = $file->store('surveys/result-images', 'public');
            $survey->images()->create(['file_path' => $path]);
        }
    }

    if ($request->hasFile('documentation')) {
        foreach ($request->file('documentation') as $file) {
            $path = $file->store('surveys/documentations', 'public');
            $survey->documentations()->create(['file_path' => $path]);
        }
    }

    $project = $survey->project;
    $surveyLevel = $project->levels->firstWhere('level_order', 3);

    if ($surveyLevel) {
        $surveyLevel->employees()->sync($request->employee_id);
    }

    return back()->with('success', 'Survey berhasil diperbarui.');
}

public function deleteDocument(SurveyDocument $docs)
{
    Storage::disk('public')->delete($docs->file_path);
    $doc->delete();
}

public function deleteImage(SurveyImage $img)
{
    Storage::disk('public')->delete($img->file_path);
    $img->delete();
}

public function deleteDocumentation(SurveyDocumentation $doc)
{
    Storage::disk('public')->delete($doc->file_path);
    $doc->delete();
}

}

