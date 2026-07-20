<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Project;
use App\Models\BuildDailyReport;
use App\Models\WeeklyReport;
use App\Models\BuildWeeklyProgress;
use App\Models\BuildProcessItem;
use App\Models\BuildPlans;
use App\Models\BuildPlanWeek;
use Mpdf\Mpdf;
use Carbon\Carbon;
use DB;

class BuildWeeklyController extends Controller
{
    public function store(Request $request, Project $project)
{
    $request->validate([
        'minggu'   => ['required', 'integer'],
        'capaian'  => ['nullable'],
        'kendala'  => ['nullable'],
        'rencana'  => ['nullable'],
    ]);

    WeeklyReport::updateOrCreate(
        [
            'project_id' => $project->id,
            'minggu'     => $request->minggu,
        ],
        [
            'capaian' => $request->capaian,
            'kendala' => $request->kendala,
            'rencana' => $request->rencana,
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'Laporan mingguan berhasil disimpan.'
    ]);
}
public function update(Request $r)
{

    $item = BuildProcessItem::findOrFail($r->item_id);

    $progress = BuildWeeklyProgress::firstOrCreate([
        'build_process_item_id' => $item->id,
        'week_no' => $r->week_no
    ], [
        'volume' => 0,
        'progress_percent' => 0,
        'bobot_percent' => 0,
        'just_kurang' => 0,
        'just_tambah' => 0,
        'just_baru' => 0,
    ]);

    if ($r->has('volume')) {

        $vol = floatval($r->volume);

        if (!$item->is_tambahan) {
            $totalExisting = BuildWeeklyProgress::where(
                    'build_process_item_id', $item->id
                )
                ->where('week_no', '!=', $r->week_no)
                ->sum('volume');

            $sisa = $item->volume - $totalExisting;

            if ($sisa <= 0) {
                return response()->json([
                    'error' => 'Volume kontrak sudah tercapai'
                ], 422);
            }

            if ($vol > $sisa) {
                return response()->json([
                    'error' => 'Melebihi volume kontrak'
                ], 422);
            }
        }

        $progressPercent = $item->volume > 0
            ? ($vol / $item->volume) * 100
            : 0;

        $bobot = $progressPercent * $item->bobot_percent / 100;

        $progress->volume = $vol;
        $progress->progress_percent = $progressPercent;
        $progress->bobot_percent = $bobot;
    }

    if ($r->has('just_kurang')) {
        $progress->just_kurang = floatval($r->just_kurang ?? 0);
    }

    if ($r->has('just_tambah')) {
        $progress->just_tambah = floatval($r->just_tambah ?? 0);
    }

    if ($r->has('just_baru')) {
        $progress->just_baru = floatval($r->just_baru ?? 0);
    }

    $progress->updated_at = now();
    $progress->save();
    $project = $item->project;

    $totalProgress = BuildWeeklyProgress::whereHas('item', function($q) use ($project){
            $q->where('project_id',$project->id);
        })
        ->sum('bobot_percent');

    InvoiceBuildController::autoGenerate(
        $project,
        $totalProgress
    );
    app(\App\Services\BuildInvoiceService::class)
    ->generateJustek($project);
    return response()->json([
        'ok' => true,
        'item_id' => $item->id,
        'week_no' => $r->week_no
    ]);
}

public function exportPdf(Request $request, Project $project) {
    $week = $request->week;
    $date = $request->date;
    
    $buildItems = BuildProcessItem::with([
        'weeklyProgresses',
        'tambahan',
    ])
    ->where('project_id', $project->id)
    ->orderBy('category_order')
    ->orderBy('uraian_order')
    ->orderBy('item_order')
    ->get();

    $buildItems->each(function ($item) { $item->progress_map = $item->weeklyProgresses ->keyBy('week_no'); });

    $groupedItems = $this->groupItems($buildItems);
    $allWeeks = collect($project->week_labels);
    $filteredWeeks = collect($project->week_labels);

    if ($date) {

        $filteredWeeks = $filteredWeeks->filter(function ($w) use ($date) {

            $start = Carbon::createFromFormat('d/m/Y', $w['start']);
            $end   = Carbon::createFromFormat('d/m/Y', $w['end']);

            return Carbon::parse($date)->between($start, $end);

        });

    } elseif ($week) {

        $filteredWeeks = $filteredWeeks->where('week_no', $week);

    }

    $totalColsSchedule = 5 + ($allWeeks->count() * 9);
    $totalCols = 5 + ($filteredWeeks->count() * 9);
    $weekNo = $filteredWeeks->first()['week_no'] ?? null;

    $weeklyReport = $project->weeklyReports()
        ->where('minggu', $weekNo)
        ->first();
    $rekap = collect($groupedItems)->map(function ($cat) use ($filteredWeeks, $project, $week) {

        $items = collect($cat['uraians'])->flatMap(fn($u) => $u['items']);
        $bobot = $items->sum('bobot_percent');
        $categoryId = $items->first()->category_order;
        $weekNow = $filteredWeeks->max('week_no');
        $weekPrev = max($weekNow - 1, 0);
        $rencana = BuildPlans::with('weeks')
            ->where('project_id', $project->id)
            ->where('category_order', $categoryId)
            ->get()
            ->sum(function ($plan) use ($weekNow) {

                $persenKumulatif = $plan->weeks
                    ->where('week_no', '<=', $weekNow)
                    ->sum('plan_percent');

                return (
                    $persenKumulatif / 100
                ) * $plan->bobot_percent;

            });

            $prestasiLalu = $items->avg(function ($item) use ($weekPrev) {
                    $vol = 0;
                    for($w = 1; $w <= $weekPrev; $w++){

                        $prog =
                            $item->progress_map[$w]
                            ?? null;

                        $vol +=
                            $prog->volume ?? 0;

                    }
                    return $item->volume > 0

                        ? ($vol / $item->volume) * 100

                        : 0;
            });
        // $status = 'Belum Dimulai';

        // if ($realisasiKumulatif >= $bobot) {
        //     $status = 'Selesai';
        // } elseif ($realisasiKumulatif > 0) {
        //     $status = 'Sedang Berlangsung';
        // }
        $bobotLalu = $items->sum(function ($item) use ($weekPrev) {

            $sum = 0;

            for($w = 1; $w <= $weekPrev; $w++){

                $prog =
                    $item->progress_map[$w]
                    ?? null;

                $vol =
                    $prog->volume ?? 0;

                $sum += $item->volume > 0

                    ? ($vol / $item->volume)
                        * $item->bobot_percent

                    : 0;

            }

            return $sum;
        });
        
        // $prestasiMingguIni = $items->avg(function ($item) use ($weekNow) {

        //     $prog =
        //         $item->progress_map[$weekNow]
        //         ?? null;

        //     $vol =
        //         $prog->volume ?? 0;

        //     return $item->volume > 0

        //         ? ($vol / $item->volume) * 100

        //         : 0;

        // });

        $bobotMingguIni = $items->sum(function ($item) use ($weekNow) {

            $prog = $item->progress_map[$weekNow] ?? null;

            $vol = $prog->volume ?? 0;

            return $item->volume > 0

                ? ($vol / $item->volume)
                    * $item->bobot_percent

                : 0;

        });
        $prestasiMingguIni =
            $bobot > 0
        ? ($bobotMingguIni / $bobot) * 100
        : 0;
        // $prestasiKumulatif = $prestasiLalu + $prestasiMingguIni;

        $realisasiKumulatif = $bobotLalu + $bobotMingguIni;
        $prestasiKumulatif =
            $bobot > 0
        ? ($realisasiKumulatif / $bobot) * 100
        : 0;
        $status = match (true) {
            $prestasiKumulatif >= 100 => 'Selesai',
            $prestasiKumulatif > 0    => 'Sedang Berlangsung',
            default                   => 'Belum Dimulai',
        };
        return [
            'category' => $cat['category_name'],
            'bobot' => $bobot,
            'rencana' => $rencana,
            'prestasi_lalu' => $prestasiLalu,
            'bobot_lalu' => $bobotLalu,
            'prestasi_minggu_ini' => $prestasiMingguIni,
            'bobot_minggu_ini' => $bobotMingguIni,
            'prestasi_sd_minggu_ini' => $prestasiKumulatif,
            'realisasi_sd_minggu_ini' => $realisasiKumulatif,
            'status' => $status
        ];
    });
    $kurvaS = $project->getKurvaSData() ?? [];
    $labels = collect($kurvaS)
        ->pluck('week')
        ->map(fn($w) => 'M'.$w)
        ->values()
        ->toArray();

    $realisasi = collect($kurvaS)
        ->pluck('progress')
        ->values()
        ->toArray();
    $weekNow = $filteredWeeks->max('week_no');
    $realisasi = collect($realisasi)
            ->map(function ($value, $index) use ($weekNow) {
                // index dimulai dari 0, minggu dimulai dari 1
                return ($index + 1) <= $weekNow ? $value : null;
            })
            ->values()
            ->toArray();
    $plan = [];
    $jalan = 0;

    foreach ($project->week_labels as $w) {

        $mingguan = BuildPlanWeek::query()
            ->with('buildPlan')
            ->whereHas('buildPlan', function ($q) use ($project) {

                $q->where('project_id', $project->id);

            })
            ->where('week_no', $w['week_no'])
            ->get()
            ->sum(function ($week) {

                return (
                    ($week->plan_percent ?? 0) / 100
                ) * ($week->buildPlan->bobot_percent ?? 0);

            });

        $jalan += $mingguan;

        $plan[] = round($jalan, 3);
    }

    $planMap = BuildPlanWeek::query()
        ->with('buildPlan')
        ->whereHas('buildPlan', function ($q) use ($project) {
            $q->where('project_id', $project->id);
        })
        ->get()
        ->groupBy(function ($item) {
            return $item->buildPlan->category_order;
        });
        $startWeek = Carbon::parse($project->start_date)
            ->addWeeks($weekNow - 1)
            ->startOfDay();

        $endWeek = (clone $startWeek)
            ->addDays(6)
            ->endOfDay();

        $dailyReports = BuildDailyReport::with([
            'documentations' => function ($q) {
                $q->where('file_type', 'like', 'image/%');
                // ->whereIn('category', ['pekerjaan', 'bahan'])
            }
        ])
        ->where('project_id', $project->id)
        ->whereBetween('tanggal', [$startWeek, $endWeek])
        ->orderBy('tanggal')
        ->get();
    $chartConfig = [
        'type' => 'line',
        'data' => [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Rencana',
                    'data' => $plan,
                    'borderColor' => 'green',
                    'fill' => false,
                ],
                [
                    'label' => 'Realisasi',
                    'data' => $realisasi,
                    'borderColor' => 'blue',
                    'fill' => false,
                    'spanGaps' => false,
                ]
            ],
        ],
    ];

    $chartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig));
    $chartPath = storage_path(
        'app/public/kurva-'.$project->id.'.png'
    );

    $image = Http::withoutVerifying()->get($chartUrl);

    if ($image->successful()) {

        file_put_contents(
            $chartPath,
            $image->body()
        );

    }
    $weekNow = $filteredWeeks->max('week_no');
    $rencanaKumulatif = $plan[$weekNow - 1] ?? 0;
    $realisasiKumulatif = $realisasi[$weekNow - 1] ?? 0;
    $deviasi = round(
        $realisasiKumulatif - $rencanaKumulatif,
        2
    );
    if ($deviasi < 0) {
        $status = 'Pekerjaan Lebih Lambat';
    } elseif ($deviasi > 0) {
        $status = 'Pekerjaan Lebih Cepat';
    } else {
        $status = 'Sesuai Rencana';
    }

    $svgWidth  = 900;
    $svgHeight = 300;

    $paddingLeft   = 35;
    $paddingRight  = 15;
    $paddingTop    = 50;
    $paddingBottom = 40;

    $chartWidth  = $svgWidth - $paddingLeft - $paddingRight;
    $chartHeight = $svgHeight - $paddingTop - $paddingBottom;

    $safeMax = max(
        collect($plan)->max() ?: 1,
        collect($realisasi)->filter()->max() ?: 1
    );

    $stepX = $chartWidth / max(count($allWeeks) - 1, 1);

    $planPoints = [];

    foreach ($plan as $i => $value) {

        $x = $paddingLeft + ($i * $stepX);

        $y = $paddingTop
            + $chartHeight
            - (($value / $safeMax) * $chartHeight);

        $planPoints[] = round($x,2).','.round($y,2);
    }
    $realPoints = [];

    foreach ($realisasi as $i => $value) {

        if ($value === null) {
            break;
        }

        $x = $paddingLeft + ($i * $stepX);

        $y = $paddingTop
            + $chartHeight
            - (($value / $safeMax) * $chartHeight);

        $realPoints[] = round($x,2).','.round($y,2);
    }
    $columnWidth = $svgWidth / count($allWeeks);

    $svgPlan = implode(' ', $planPoints);
    $svgReal = implode(' ', $realPoints);
    $totalRows = count($groupedItems);

    $rowHeight = 8; // mm
    $curveLeft = $request->curve_left_mm
        ?? $project->curve_left_mm
        ?? 110;
    $curveTop = $request->curve_top_mm
        ?? $project->curve_top_mm
        ?? 80;
    $maxValue = max(
        collect($plan)->max() ?: 1,
        collect($realisasi)->max() ?: 1
    );
    $curveWidth = $request->curve_width_mm
        ?? $project->curve_width_mm
        ?? 220;

    $curveHeight = $request->curve_height_mm
        ?? $project->curve_height_mm
        ?? 50;
    $headerCover = 'file://' . public_path('images/header-penawaran.jpg');
    $headerKurva = 'file://' . public_path('images/header-penawaran.jpg');
    $headerRekap = 'file://' . public_path('images/header-penawaran.jpg');
    $headerDetail = 'file://' . public_path('images/header-penawaran.jpg');
    // $footerKurva = 'file://' . public_path('images/footer-penawaran.jpg');
    // $footerRekap = 'file://' . public_path('images/footer-penawaran.jpg');
    // $footerDetail = 'file://' . public_path('images/footer-penawaran.jpg');
    $coverHtml = view(
    'build.pdf-cover',
        [
            'project' => $project,
            'rekap' => $rekap,
            'periode' => $filteredWeeks->first()['start'].' - '.$filteredWeeks->last()['end'],
            'summary' => $project->description ?? '',
            'capaian' => $weeklyReport?->capaian ?? '',
            'kendala' => $weeklyReport?->kendala ?? '',
            'rencana' => $weeklyReport?->rencana ?? '',
            'status_progress' => $status,
            'deviasi' => $deviasi,
        ]
    )->render();
    $kurvaHtml = view(
        'build.pdf.schedule',
        [
            'project'      => $project,
            'weeks'        => $allWeeks,
            'groupedItems' => $groupedItems,
            'chartUrl'     => $chartUrl,
            'totalCols'    => $totalColsSchedule,
            'plan'         => $plan,
            'realisasi'    => $realisasi,
            'planMap'         => $planMap,
            'svgWidth'     => $svgWidth,
            'svgHeight'    => $svgHeight,
            'svgPlan'      => $svgPlan,
            'svgReal'      => $svgReal,
            'paddingLeft'   => $paddingLeft,
            'paddingRight'  => $paddingRight,
            'paddingTop'    => $paddingTop,
            'paddingBottom' => $paddingBottom,
            'chartWidth'    => $chartWidth,
            'chartHeight'   => $chartHeight,
            'maxValue' => $maxValue,
            'weekNow' => $weekNow,
            'stepX'         => $stepX,
            'safeMax'       => $safeMax,
        ]
    )->render();

    $rekapHtml = view(
        'build.pdf.rekap',
        [
            'project'             => $project,
            'rekap'               => $rekap,
            'weekNow'             => $weekNow,
            'rencanaKumulatif'    => $rencanaKumulatif,
            'realisasiKumulatif'  => $realisasiKumulatif,
            'deviasi'             => $deviasi,
        ]
    )->render();

    $detailHtml = view(
        'build.pdf.detail',
        [
            'project'      => $project,
            'groupedItems' => $groupedItems,
            'weeks'        => $filteredWeeks,
            'totalCols'    => $totalCols,
        ]
    )->render();
    $svg = view(
        'build.kurva-svg',
        [
            'weeks'       => $allWeeks,
            'plan'        => $plan,
            'realisasi'   => $realisasi,
            'svgWidth'    => $svgWidth,
            'svgHeight'   => $svgHeight,
            'svgPlan'     => $svgPlan,
            'svgReal'     => $svgReal,
            'weekNow'     => $weekNow,

            'paddingLeft'   => $paddingLeft,
            'paddingRight'  => $paddingRight,
            'paddingTop'    => $paddingTop,
            'paddingBottom' => $paddingBottom,
            'chartWidth'    => $chartWidth,
            'chartHeight'   => $chartHeight,
            'stepX'         => $stepX,
            'safeMax'       => $safeMax,
        ]
    )->render();

    $svgPath = storage_path('app/public/kurva-'.$project->id.'.svg');

    file_put_contents($svgPath, $svg);
    $dokumentasiHtml = view(
        'build.pdf.documentation',
        [
            'project' => $project,
            'dailyReports' => $dailyReports,
        ]
    )->render();
    ini_set('pcre.backtrack_limit', '5000000');
    ini_set('pcre.recursion_limit', '5000000');
    $mpdf = new Mpdf([
        'format' => 'A4',
        'margin_top' => 50,
        'margin_bottom' => 20,
        'margin_left' => 10,
        'margin_right' => 10,
    ]);

    $mpdf->curlAllowUnsafeSslRequests = true;

    $mpdf->SetHTMLHeader('
    <div>
        <img src="'.$headerCover.'" width="100%">
    </div>
    ');

    $mpdf->WriteHTML($coverHtml);

    $mpdf->AddPage('L');
    $mpdf->SetHTMLHeader('
    <div>
        <img src="'.$headerKurva.'" width="100%">
    </div>
    ');
    $mpdf->WriteHTML($kurvaHtml);

    $mpdf->AddPage('P');
    $mpdf->SetHTMLHeader('
    <div>
        <img src="'.$headerRekap.'" width="100%">
    </div>
    ');

    // $mpdf->SetHTMLFooter('
    // <div>
    //     <img src="'.$footerRekap.'" width="100%">
    // </div>
    // ');
    $mpdf->WriteHTML($rekapHtml);
    $mpdf->AddPage('P');
    $mpdf->SetHTMLHeader('
    <div>
        <img src="'.$headerDetail.'" width="100%">
    </div>
    ');

    // $mpdf->SetHTMLFooter('
    // <div>
    //     <img src="'.$footerDetail.'" width="100%">
    // </div>
    // ');
    $mpdf->WriteHTML($detailHtml);
    $mpdf->AddPage('P');

    $mpdf->SetHTMLHeader('
    <div>
        <img src="'.$headerDetail.'" width="100%">
    </div>
    ');

    $mpdf->WriteHTML($dokumentasiHtml);

    return response(
        $mpdf->Output(
            'LAPORAN-'.$project->project_name.'.pdf',
            'S'
        ),
        200,
        [
            'Content-Type' => 'application/pdf',

            'Content-Disposition' =>
                'inline; filename="LAPORAN-'.$project->project_name.'.pdf"; filename*=UTF-8\'\'LAPORAN-'.$project->project_name.'.pdf',
        ]
    );
}

private function groupItems($buildItems)
{
    return $buildItems
        ->groupBy('category_order')
        ->map(function ($categoryItems) {

            $firstCategory = $categoryItems->first();

            return [

                'category_name' => $firstCategory->category_name,
                'category_order' => $firstCategory->category_order,

                'uraians' => $categoryItems
                    ->groupBy('uraian_order')
                    ->map(function ($uraianItems) {

                        $firstUraian = $uraianItems->first();

                        return [

                            'uraian_name' => $firstUraian->uraian_name,
                            'uraian_order' => $firstUraian->uraian_order,

                            'items' => $uraianItems
                                ->sortBy('item_order')
                                ->values(),

                        ];

                    })->values()

            ];

        })->values();
}
}