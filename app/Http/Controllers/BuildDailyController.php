<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BuildDailyReport;
use App\Models\BuildDailyWorker;
use App\Models\BuildDailyWork;
use App\Models\BuildDailyWorkTime;
use App\Models\BuildDailyMaterial;
use App\Models\BuildProcessItem;
use App\Models\DailyDocumentation;
use App\Models\Project;
use App\Models\RabProcessCategory;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;

class BuildDailyController extends Controller
{
    public function store(Request $request)
{
    $isLibur = $request->has('is_libur');
    
    $request->validate([
        'project_id' => 'required|exists:projects,id',
        'jam_mulai' => 'nullable|array',
        'jam_mulai.*' => 'nullable|date_format:H:i',
        'tanggal' => 'required|date',
        'jam_selesai' => 'nullable|array',
        'jam_selesai.*' => 'nullable|date_format:H:i',

        'total_jam' => 'nullable|array',
        'total_jam.*' => 'nullable|numeric|min:0',

        'cuaca' => 'nullable|array',
        'cuaca.*' => 'nullable|string|max:50',

        'cuaca_keterangan' => 'nullable|array',
        'cuaca_keterangan.*' => 'nullable|string|max:255',
        'catatan' => 'nullable|string',
        'mk_id' => 'nullable|string|max:100',
        'kontraktor_ttd_id' => 'nullable|string|max:100',
        'worker_id.*' => 'nullable',
        'keahlian.*' => 'nullable|string|max:100',
        'jumlah.*' => 'nullable|numeric|min:0',
        'alat.*' => 'nullable|string|max:150',
        'rab_process_item_id.*' => 'nullable',
        'uraian_manual.*' => 'nullable|string|max:255',
        'daily.volume.*' => 'nullable|numeric|min:0',
        'daily.satuan.*' => 'nullable|string|max:50',
        'ket.*' => 'nullable|string|max:255',
        'bahan.*' => 'nullable|string|max:150',
        'diterima.*' => 'nullable|numeric|min:0',
        'ditolak.*' => 'nullable|numeric|min:0',
        'documentation_tenaga' => 'nullable|array',
        'documentation_tenaga.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',

        'documentation_pekerjaan' => 'nullable|array',
        'documentation_pekerjaan.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',

        'documentation_material' => 'nullable|array',
        'documentation_material.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
    ]);
    foreach($request->worker_id ?? [] as $i => $worker){

        if($worker === 'manual'){
            if(empty($request->keahlian[$i])){
                return back()
                    ->withErrors(['keahlian.'.$i => 'Keahlian manual wajib diisi.'])
                    ->withInput();
            }
        }
    }
    foreach($request->rab_process_item_id ?? [] as $i => $rab) {

        if($rab === 'manual'){
            if(empty($request->uraian_manual[$i])){
                return back()
                    ->withErrors(['uraian_manual.'.$i => 'Uraian manual wajib diisi.'])
                    ->withInput();
            }
        }
    }
    DB::beginTransaction();

    try {

        $project = Project::findOrFail($request->project_id);

        $tanggal = Carbon::parse($request->tanggal);
        if (
            $tanggal->lt($project->start_date) ||
            $tanggal->gt($project->end_date)
        ) {
            return back()
                ->with('error', 'Tanggal di luar periode proyek.')
                ->withInput();
        }

        $exists = BuildDailyReport::where('project_id', $project->id)
                    ->whereDate('tanggal', $tanggal)
                    ->exists();

        if ($exists) {
            return back()
                ->with('error', 'Laporan untuk tanggal ini sudah ada.')
                ->withInput();
        }

        $report = BuildDailyReport::create([
            'project_id' => $request->project_id,
            'pekerjaan' => $project->project_name,
            'tanggal' => $tanggal,
            'is_libur' => $isLibur,
            'catatan' => $isLibur ? 'Tidak ada kegiatan (Hari Libur)' : $request->catatan,
            'mk_id' => $isLibur ? null : $request->mk_id,
            'kontraktor_ttd_id' => $isLibur ? null : $request->kontraktor_ttd_id,
            'created_by' => auth()->id(),
        ]);
        $jamMulai = $isLibur
            ? []
            : ($request->jam_mulai ?? []);

        $jamSelesai = $isLibur
            ? []
            : ($request->jam_selesai ?? []);

        $totalJam = $isLibur
            ? []
            : ($request->total_jam ?? []);

        $cuaca = $isLibur
            ? []
            : ($request->cuaca ?? []);

        $keteranganCuaca = $isLibur
            ? []
            : ($request->cuaca_keterangan ?? []);
        if (!$isLibur) {
            foreach ($jamMulai as $i => $mulai) {

                if (
                    empty($mulai) &&
                    empty($jamSelesai[$i]) &&
                    empty($cuaca[$i])
                ) {
                    continue;
                }

                BuildDailyWorkTime::create([
                    'build_daily_report_id' => $report->id,

                    'jam_mulai' => $mulai,
                    'jam_selesai' => $jamSelesai[$i] ?? null,

                    'total_jam' => $totalJam[$i] ?? 0,

                    'cuaca' => $cuaca[$i] ?? null,

                    'keterangan' => $keteranganCuaca[$i] ?? null,
                ]);
            }
        }
        if ($isLibur) {
            DB::commit();

            return redirect()
                ->route('projects.create', ['project_id' => $report->project_id])
                ->with('success','Laporan hari libur berhasil disimpan');
        }

        if($request->hasFile('documentation_tenaga')){
            foreach($request->file('documentation_tenaga') as $file){

                $path = $file->store('daily/tenaga','public');

                $report->documentations()->create([
                    'category' => 'tenaga',
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        if($request->hasFile('documentation_pekerjaan')){
            foreach($request->file('documentation_pekerjaan') as $file){

                $path = $file->store('daily/pekerjaan','public');

                $report->documentations()->create([
                    'category' => 'pekerjaan',
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        if($request->hasFile('documentation_material')){
            foreach($request->file('documentation_material') as $file){

                $path = $file->store('daily/material','public');

                $report->documentations()->create([
                    'category' => 'material',
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        foreach($request->worker_id ?? [] as $i => $worker){

            if(empty($worker)) continue;

            if($worker == 'manual'){

                BuildDailyWorker::create([
                    'daily_report_id'=>$report->id,
                    'worker_id'=>null,
                    'keahlian'=>$request->keahlian[$i] ?? null,
                    'jumlah'=>$request->jumlah[$i] ?? 0,
                    'alat'=>$request->alat[$i] ?? null
                ]);

            } else {

                $workerModel = Worker::find($worker);
                if(!$workerModel) continue;

                BuildDailyWorker::create([
                    'daily_report_id'=>$report->id,
                    'worker_id'=>$workerModel->id,
                    'keahlian'=>null,
                    'jumlah'=>$request->jumlah[$i] ?? 0,
                    'alat'=>$request->alat[$i] ?? null
                ]);
            }
        }

        $volumes = $request->daily['volume'] ?? [];
        $satuans = $request->daily['satuan'] ?? [];

        foreach($request->rab_process_item_id ?? [] as $i => $rab){

            if(
                empty($rab) &&
                empty($request->uraian_manual[$i]) &&
                empty($volumes[$i]) &&
                empty($satuans[$i])
            ){
                continue;
            }

            BuildDailyWork::create([
                'build_daily_report_id' => $report->id,
                'rab_process_item_id'   => $rab != 'manual' ? $rab : null,
                'uraian_manual'         => $rab == 'manual'
                                            ? ($request->uraian_manual[$i] ?? null)
                                            : null,
                'volume'                => $volumes[$i] ?? 0,
                'satuan'                => $satuans[$i] ?? null,
                'keterangan'            => $request->ket[$i] ?? null,
            ]);
        }

        foreach($request->bahan ?? [] as $i => $bahan){

            if(empty($bahan)) continue;

            BuildDailyMaterial::create([
                'daily_report_id'=>$report->id,
                'nama_bahan'=>$bahan,
                'diterima'=>$request->diterima[$i] ?? 0,
                'ditolak'=>$request->ditolak[$i] ?? 0
            ]);
        }

        DB::commit();

        return redirect()
            ->route('projects.create', ['project_id' => $report->project_id])
            ->with('success','Laporan berhasil disimpan');

    } catch(\Exception $e) {

        DB::rollBack();
        \Log::error($e);

        return back()
            ->with('error', 'Terjadi kesalahan saat menyimpan laporan.')
            ->withInput();
    }

}

public function detail($id)
{
    $daily = BuildDailyReport::with([
        'project',
        'workTimes',
        'works.rabProcessItem',
        'workers.worker.user',
        'materials',
        'documentations',
        'mkEmployee.user',
        'kontraktorEmployee.user'
    ])->findOrFail($id);
            
    $workerOptions = Worker::with('user')
        ->orderBy('id')
        ->get();

    $project = $daily->project;

    $categories = RabProcessCategory::with([
        'uraians.items.rab'
    ])
    ->whereHas('rabProcess.project', function ($q) use ($project) {
        $q->where('customer_id', $project->customer_id);
    })
    ->orderBy('order_no')
    ->get();
    $usedDates = BuildDailyReport::where('project_id', $daily->project_id)
        ->where('id', '!=', $daily->id) // jangan disable tanggal laporan yang sedang diedit
        ->pluck('tanggal')
        ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
        ->values();
    return response()->json([
        'daily' => $daily,
        'worker_options' => $workerOptions,
        'categories'     => $categories,
        'used_dates'     => $usedDates,
        'start_date' => Carbon::parse($project->start_date)->format('Y-m-d'),
        'end_date'   => Carbon::parse($project->end_date)->format('Y-m-d'),
    ]);
}
public function deleteWork($id)
{
    BuildDailyWork::findOrFail($id)->delete();
    return response()->json(['success' => true]);
}

public function deleteWorker($id)
{
    BuildDailyWorker::findOrFail($id)->delete();
    return response()->json(['success' => true]);
}

public function deleteMaterial($id)
{
    BuildDailyMaterial::findOrFail($id)->delete();
    return response()->json(['success' => true]);
}

public function updateAll(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $daily = BuildDailyReport::with([
            'works',
            'workers',
            'materials',
            'workTimes',
        ])->findOrFail($id);

        $daily->update([
            'tanggal'      => $request->tanggal,
            'catatan'      => $request->catatan,
        ]);

        if ($request->hasFile('new_files_pekerjaan')) {

            foreach ($request->file('new_files_pekerjaan') as $file) {

                $path = $file->store('daily_docs', 'public');

                DailyDocumentation::create([
                    'build_daily_report_id' => $daily->id,
                    'category' => 'pekerjaan',
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }
        if ($request->hasFile('new_files_tenaga')) {

            foreach ($request->file('new_files_tenaga') as $file) {

                $path = $file->store('daily_docs', 'public');

                DailyDocumentation::create([
                    'build_daily_report_id' => $daily->id,
                    'category' => 'tenaga',
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }
        if ($request->hasFile('new_files_material')) {

            foreach ($request->file('new_files_material') as $file) {

                $path = $file->store('daily_docs', 'public');

                DailyDocumentation::create([
                    'build_daily_report_id' => $daily->id,
                    'category' => 'material',
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }
        $existingWorkIds = $daily->works->pluck('id')->toArray();
        $requestWorkIds = [];

        foreach ($request->works ?? [] as $work) {

            if (!empty($work['id'])) {

                $requestWorkIds[] = $work['id'];

                BuildDailyWork::where('id', $work['id'])->update([
                    'rab_process_item_id' => $work['rab_process_item_id'] != 'manual'
                        ? ($work['rab_process_item_id'] ?: null)
                        : null,

                    'uraian_manual' => $work['rab_process_item_id'] == 'manual'
                        ? ($work['uraian_manual'] ?? null)
                        : null,

                    'volume'     => $work['volume'],
                    'satuan'     => $work['satuan'],
                    'keterangan' => $work['keterangan'],
                ]);

            } else {

                $daily->works()->create([
                    'build_daily_report_id' => $daily->id,

                    'rab_process_item_id' => $work['rab_process_item_id'] != 'manual'
                        ? ($work['rab_process_item_id'] ?: null)
                        : null,

                    'uraian_manual' => $work['rab_process_item_id'] == 'manual'
                        ? ($work['uraian_manual'] ?? null)
                        : null,

                    'volume'     => $work['volume'],
                    'satuan'     => $work['satuan'],
                    'keterangan' => $work['keterangan'],
                ]);
            }
        }

        $deleteWorkIds = array_diff($existingWorkIds, $requestWorkIds);
        BuildDailyWork::whereIn('id', $deleteWorkIds)->delete();

        $existingWorkerIds = $daily->workers->pluck('id')->toArray();
        $requestWorkerIds = [];

        foreach ($request->workers ?? [] as $worker) {

            if (!empty($worker['id'])) {

                $requestWorkerIds[] = $worker['id'];

                BuildDailyWorker::where('id', $worker['id'])->update([
                    'worker_id' => ($worker['worker_id'] ?? null) === 'manual'
                        ? null
                        : ($worker['worker_id'] ?? null),

                    'keahlian' => ($worker['worker_id'] ?? null) === 'manual'
                        ? ($worker['keahlian'] ?? null)
                        : null,

                    'jumlah' => $worker['jumlah'],
                    'alat'   => $worker['alat'],
                ]);

            } else {

                $daily->workers()->create([
                    'daily_report_id' => $daily->id,
                    'worker_id' => ($worker['worker_id'] ?? null) === 'manual'
                        ? null
                        : ($worker['worker_id'] ?? null),

                    'keahlian' => ($worker['worker_id'] ?? null) === 'manual'
                        ? ($worker['keahlian'] ?? null)
                        : null,
                    'jumlah'          => $worker['jumlah'],
                    'alat'            => $worker['alat'],
                ]);
            }
        }

        $deleteWorkerIds = array_diff($existingWorkerIds, $requestWorkerIds);
        BuildDailyWorker::whereIn('id', $deleteWorkerIds)->delete();

        $existingMaterialIds = $daily->materials->pluck('id')->toArray();
        $requestMaterialIds = [];

        foreach ($request->materials ?? [] as $material) {

            if (!empty($material['id'])) {

                $requestMaterialIds[] = $material['id'];

                BuildDailyMaterial::where('id', $material['id'])->update([
                    'diterima' => $material['diterima'],
                    'ditolak'  => $material['ditolak'],
                ]);

            } else {

                $daily->materials()->create([
                    'daily_report_id' => $daily->id,
                    'nama_bahan'      => $material['nama_bahan'],
                    'diterima'        => $material['diterima'],
                    'ditolak'         => $material['ditolak'],
                ]);
            }
        }

        $deleteMaterialIds = array_diff($existingMaterialIds, $requestMaterialIds);
        BuildDailyMaterial::whereIn('id', $deleteMaterialIds)->delete();

        $existingWorkTimeIds = $daily->workTimes->pluck('id')->toArray();
        $requestWorkTimeIds = [];

        foreach ($request->jam_kerja ?? [] as $jam) {

            $jamMulai = $jam['jam_mulai'] ?? null;
            $jamSelesai = $jam['jam_selesai'] ?? null;

            $totalJam = null;

            if ($jamMulai && $jamSelesai) {
                $start = Carbon::parse($jamMulai);
                $end = Carbon::parse($jamSelesai);

                $totalJam = $start->diffInMinutes($end) / 60;
            }

            if (!empty($jam['id'])) {

                $requestWorkTimeIds[] = $jam['id'];

                BuildDailyWorkTime::where('id', $jam['id'])->update([
                    'jam_mulai'  => $jamMulai,
                    'jam_selesai'=> $jamSelesai,
                    'total_jam'  => $totalJam,
                    'cuaca'      => $jam['cuaca'] ?? null,
                    'keterangan' => $jam['keterangan'] ?? null,
                ]);

            } else {

                $daily->workTimes()->create([
                    'build_daily_report_id' => $daily->id,
                    'jam_mulai'             => $jamMulai,
                    'jam_selesai'           => $jamSelesai,
                    'total_jam'             => $totalJam,
                    'cuaca'                 => $jam['cuaca'] ?? null,
                    'keterangan'            => $jam['keterangan'] ?? null,
                ]);

            }
        }

        $deleteWorkTimeIds = array_diff($existingWorkTimeIds, $requestWorkTimeIds);

        BuildDailyWorkTime::whereIn('id', $deleteWorkTimeIds)->delete();

        DB::commit();

        return response()->json([
            'success' => true
        ]);

    } catch (\Exception $e) {

        DB::rollBack();
         \Log::error($e);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
public function deleteDocumentation($id)
{
    $doc = DailyDocumentation::findOrFail($id);

    Storage::disk('public')->delete($doc->file_path);

    $doc->delete();

    return response()->json(['success' => true]);
}

public function destroy($id)
{
    $report = BuildDailyReport::findOrFail($id);
    $report->delete();

    return response()->json([
        'success' => true,
        'message' => 'Data berhasil dihapus'
    ]);
}
public function nextDate($projectId)
{
    if(!$projectId || $projectId === 'undefined'){
        return response()->json([
            'date' => null
        ]);
    }
    $project = Project::findOrFail($projectId);

    $lastReport = BuildDailyReport::where('project_id',$projectId)
                    ->orderByDesc('tanggal')
                    ->first();

    $nextDate = $lastReport
        ? Carbon::parse($lastReport->tanggal)->addDay()
        : Carbon::parse($project->start_date);

    return response()->json([
        'date' => $nextDate->translatedFormat('d F Y')
    ]);
}
}
