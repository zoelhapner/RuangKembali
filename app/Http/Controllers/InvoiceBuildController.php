<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\InvoiceBuild;
use App\Models\ProjectLevel;
use App\Models\BuildProcessItem;
use App\Models\BuildPlans;
use App\Models\BuildWeeklyProgress;
use App\Services\ProjectNotifier;
use App\Services\InvoiceBuildNumberGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DB;

class InvoiceBuildController extends Controller
{

    public function invoiceBuild(Project $project, int $termin)
    {
        abort_if($project->project_type != 3, 403);
        abort_if(!$project->offer, 404);

        Carbon::setLocale('id');

        $terminMap = [
            1 => ['start' => 0,  'end' => 30,  'percent' => 30],
            2 => ['start' => 30, 'end' => 60,  'percent' => 30],
            3 => ['start' => 60, 'end' => 90,  'percent' => 30],
            4 => ['start' => 90, 'end' => 100, 'percent' => 10],
        ];

        abort_if(!isset($terminMap[$termin]), 404);

        $conf = $terminMap[$termin];
        $offer = $project->offer;
        $rab = $offer->rab;

        $result = DB::transaction(function () use ($project, $termin, $conf, $rab, $offer) {

            $subtotal = $rab->categories
                ->flatMap(fn($c) => $c->uraians)
                ->flatMap(fn($u) => $u->items)
                ->sum(fn($i) => $i->volume * $i->price);

            $discount = $offer->discount ?? 0;

            $subtotalAfterDiscount = $subtotal - $discount;

            $taxRate = $offer->tax_rate ?? 0;

            $totalTax = $subtotalAfterDiscount * ($taxRate / 100);

            $shipping = $offer->shipping ?? 0;

            $grandTotal = $subtotalAfterDiscount + $totalTax + $shipping;

            $newAmount = $grandTotal * ($conf['percent'] / 100);

            $invoice = InvoiceBuild::where('project_id', $project->id)
                ->where('termin', $termin)
                ->lockForUpdate()
                ->first();

            if (!$invoice) {

                $invoice = InvoiceBuild::create([
                    'project_id'          => $project->id,
                    'invoice_type'        => InvoiceBuild::TYPE_BUILD,
                    'invoice_number'      => InvoiceBuildNumberGenerator::generate($termin),
                    'invoice_date'        => now(),
                    'termin'              => $termin,
                    'progress_start'      => $conf['start'],
                    'progress_end'        => $conf['end'],
                    'payment_percentage'  => $conf['percent'],
                    'amount'              => $newAmount,
                    'status'              => 'waiting',
                ]);

            } else {

                if ($invoice->amount != $newAmount) {

                    $invoice->update([
                        'amount' => $newAmount,
                    ]);
                }
            }

            if (!$invoice->downloaded_at) {

                $invoice->update([
                    'downloaded_at' => now(),
                ]);
            }

            return [
                'invoice' => $invoice->fresh(),
                'grandTotal' => $grandTotal,
            ];
        });

        return Pdf::loadView('invoice.build', [
            'invoice' => $result['invoice'],
            'project' => $project,
            'offer'   => $offer,
            'grandTotal' => $result['grandTotal']
        ])
        ->setPaper('A4', 'portrait')
        ->stream(
            "Invoice-Build-Termin-{$termin}-{$project->project_name}.pdf"
        );
    }

    public function approve(Project $project, InvoiceBuild $invoice)
    {
        abort_if($project->project_type != 3, 403);
        abort_if($invoice->project_id !== $project->id, 404);

        abort_if(
            $project->customer->user_id !== auth()->id()
            && auth()->user()->cannot('lihat daftar proyek'),
            403
        );

        if (!$invoice->downloaded_at) {
            return back()->with('error','Invoice belum didownload.');
        }

        if ($invoice->approved_at) {
            return back()->with('info','Invoice sudah disetujui.');
        }

        DB::transaction(function () use ($invoice, $project) {

            $invoice->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approve_by_name' => auth()->user()->fullname ?? 'Customer',
                'approved_ip' => request()->ip(),
            ]);

            $project->load('offer.rab.categories.uraians.items');

            $subtotalPekerjaan = $project->offer->rab
                ->categories
                ->flatMap(fn($c) => $c->uraians)
                ->flatMap(fn($u) => $u->items)
                ->sum('total');

            $currentRabItemIds = $project->offer->rab
                ->categories
                ->flatMap(fn($c) => $c->uraians)
                ->flatMap(fn($u) => $u->items)
                ->pluck('id')
                ->toArray();
            BuildProcessItem::where('project_id', $project->id)
                ->whereNotIn('rab_item_id', $currentRabItemIds)
                ->whereDoesntHave('weeklyProgresses')
                ->delete();

            foreach ($project->offer->rab->categories as $cIndex => $category) {

                foreach ($category->uraians as $uIndex => $uraian) {

                    foreach ($uraian->items as $iIndex => $item) {

                        $existing = BuildProcessItem::where([
                            'project_id' => $project->id,
                            'rab_item_id' => $item->id,
                        ])->first();

                        $hasProgress =
                            $existing &&
                            $existing->weeklyProgresses()->exists();

                        $bobot = $subtotalPekerjaan > 0
                            ? ($item->total / $subtotalPekerjaan) * 100
                            : 0;

                        $buildProcessItem = BuildProcessItem::updateOrCreate(

                            [
                                'project_id' => $project->id,
                                'rab_item_id' => $item->id,
                            ],

                            [
                                'category_name' => $category->name,
                                'uraian_name' => $uraian->name,

                                'job_category_id' => $item->job_category_id,
                                'uraian' => $item->job_name,

                                'price' => $hasProgress
                                    ? $existing->price
                                    : $item->price,

                                'volume' => $hasProgress
                                    ? $existing->volume
                                    : $item->volume,

                                'total' => $hasProgress
                                    ? $existing->total
                                    : ($item->volume * $item->price),

                                'satuan' => $item->satuan,

                                'bobot_percent' => $bobot,

                                'category_order' => $cIndex,
                                'uraian_order' => $uIndex,
                                'item_order' => $iIndex,
                            ]
                        );
                        BuildPlans::updateOrCreate(

                            [
                                'project_id' => $project->id,
                                'rab_item_id' => $item->id,
                            ],

                            [
                                'build_process_item_id' => $buildProcessItem->id,

                                'category_name' => $buildProcessItem->category_name,
                                'uraian_name' => $buildProcessItem->uraian_name,

                                'job_category_id' => $buildProcessItem->job_category_id,
                                'item_name' => $buildProcessItem->uraian,

                                'price' => $buildProcessItem->price,
                                'volume' => $buildProcessItem->volume,
                                'total' => $buildProcessItem->total,

                                'satuan' => $buildProcessItem->satuan,

                                'bobot_percent' => $buildProcessItem->bobot_percent,

                                'category_order' => $buildProcessItem->category_order,
                                'uraian_order' => $buildProcessItem->uraian_order,
                                'item_order' => $buildProcessItem->item_order,
                            ]
                        );
                        $plans = BuildPlans::where('project_id', $project->id)
                            ->orderBy('id')
                            ->get();

                        $selisih = round(
                            100 - $plans->sum('bobot_percent'),
                            10
                        );

                        if (abs($selisih) > 0.0000001) {

                            $lastPlan = $plans->last();

                            $lastPlan->update([
                                'bobot_percent' => $lastPlan->bobot_percent + $selisih
                            ]);
                        }
                    }
                }
            }

            $lastTermin = 4;

            if ($invoice->termin == 1) {

                ProjectLevel::where([
                    'project_id' => $project->id,
                    'level_order' => 6,
                ])->update(['is_completed' => true]);

                ProjectLevel::where([
                    'project_id' => $project->id,
                    'level_order' => 7,
                ])->update(['is_started' => true]);

                $project->update([
                    'active_step' => 7
                ]);
            }

            elseif ($invoice->termin == $lastTermin) {

                ProjectLevel::where([
                    'project_id' => $project->id,
                    'level_order' => 7,
                ])->update(['is_completed' => true]);

                ProjectLevel::where([
                    'project_id' => $project->id,
                    'level_order' => 8,
                ])->update(['is_started' => true]);

                $project->update([
                    'active_step' => 8
                ]);
            }
        });

        $event = 'invoice_build_created';
        $cfg   = config("project_events.$event");

        $payloadExtra = [
            'termin'          => $invoice->termin,
            'amount'          => number_format($invoice->amount, 0, ',', '.'),
            'progress_start'  => $invoice->progress_start,
            'progress_end'    => $invoice->progress_end,
        ];

        if (!$cfg) {
            throw new \Exception("Config project_events.$event not found");
        }

        ProjectNotifier::notifyUsers(
            [$project->createdBy ?? auth()->user()],
            ProjectNotifier::makePayload($project, [
                'type'    => $event,
                'role'    => 'Super-Admin',
                'title'   => ProjectNotifier::parseMessage($cfg['title'], $payloadExtra),
                'message' => ProjectNotifier::parseMessage(
                    $cfg['message']['Super-Admin'],
                    $payloadExtra
                ),
                'url'     => route('projects.create', ['project_id' => $project->id]),
            ])
        );

        if ($project->customer?->user) {
            ProjectNotifier::notifyUsers(
                [$project->customer->user],
                ProjectNotifier::makePayload($project, [
                    'type'    => $event,
                    'role'    => 'Customer',
                    'title'   => ProjectNotifier::parseMessage($cfg['title'], $payloadExtra),
                    'message' => ProjectNotifier::parseMessage(
                        $cfg['message']['customer'],
                        $payloadExtra
                    ),
                    'url'     => route('projects.create', ['project_id' => $project->id]),
                ])
            );
        }


        return redirect()
            ->route('projects.create', ['project_id' => $project->id])
            ->with(
                'success',
                "Invoice Termin {$invoice->termin} berhasil disetujui."
            );
    }

public static function autoGenerate(Project $project, $progress)
{

    $buffer = 10; // 2%

    $terminMap = [
        1 => 0,
        2 => 30,
        3 => 60,
        4 => 90,
    ];

    foreach ($terminMap as $termin => $targetProgress) {

        $triggerProgress = max(0, $targetProgress - $buffer);

        if($progress >= $triggerProgress){

            InvoiceBuild::firstOrCreate([
                'project_id'=>$project->id,
                'termin'=>$termin
            ],[

                'invoice_type'=>InvoiceBuild::TYPE_BUILD,

                'invoice_number'=>InvoiceBuildNumberGenerator::generate($termin),

                'invoice_date'=>now(),

                'progress_start'=>$targetProgress,

                'progress_end'=>match($termin){
                    1=>30,
                    2=>60,
                    3=>90,
                    4=>100
                },

                'payment_percentage'=>match($termin){
                    1=>30,
                    2=>30,
                    3=>30,
                    4=>10
                },

                'amount'=>$project->offer->grand_total * (
                    match($termin){
                        1=>0.30,
                        2=>0.30,
                        3=>0.30,
                        4=>0.10
                    }
                ),

                'status'=>'waiting'
            ]);

        }

    }

}

public function invoiceJustek(Project $project)
{
    $invoice = InvoiceBuild::where([
        'project_id'=>$project->id,
        'invoice_type'=>'justek'
    ])->firstOrFail();

    $justekRows = BuildWeeklyProgress::whereHas('item', function ($q) use ($project) {
            $q->where('project_id', $project->id);
        })
        ->where(function ($q) {
            $q->where('just_tambah', '>', 0)
              ->orWhere('just_kurang', '>', 0)
              ->orWhere('just_baru', '>', 0);
        })
        ->with('item')
        ->get();
    // $justekRows = BuildWeeklyProgress::whereHas('item', function ($q) use ($project) {
    //     $q->where('project_id', $project->id);
    // })
    // ->where(function ($q) {
    //     $q->where('just_tambah', '>', 0)
    //       ->orWhere('just_kurang', '>', 0)
    //       ->orWhere('just_baru', '>', 0);
    // })
    // ->with('item')
    // ->get()
    // ->groupBy('build_process_item_id');

    $grandTotal = $justekRows->sum(function ($row) {
        $price = optional($row->item)->price ?? 0;

        return 
            ($row->just_tambah * $price)
        + ($row->just_baru * $price)
        - ($row->just_kurang * $price);
    });

    return Pdf::loadView('invoice.build-justek', [
        'invoice'=>$invoice,
        'project'=>$project,
        'offer'=>$project->offer,
        'justekRows'=>$justekRows,
        'grandTotal'=>$grandTotal
    ])->stream("Invoice-Justek-{$project->project_name}.pdf");
}
public function autoJustek(Project $project)
{
    $nilaiJustek = $project->buildItems()
        ->with('weeklyProgresses')
        ->get()
        ->sum(function($item){

            return $item->weeklyProgresses->sum(function($w){

                return ($w->just_tambah ?? 0)
                     - ($w->just_kurang ?? 0)
                     + ($w->just_baru ?? 0);

            });

        });

    if($nilaiJustek <= 0){
        return response()->json(['ok'=>false]);
    }

    $exist = InvoiceBuild::where('project_id',$project->id)
        ->where('invoice_type','justek')
        ->first();

    if(!$exist){

        InvoiceBuild::create([
            'project_id'=>$project->id,
            'invoice_type'=>'justek',
            'termin'=>0,
            'nominal'=>$nilaiJustek,
            'status'=>'draft'
        ]);

    }

    return response()->json(['ok'=>true]);
}
}
            // if (
            //     $invoice->termin == 1 &&
            //     BuildProcessItem::where('project_id', $project->id)->doesntExist()
            // ) {

            //     $project->load('offer.rab.categories.uraians.items');

            //     $rows = [];

            //     foreach ($project->offer->rab->categories as $cIndex => $category) {
                      
            //         foreach ($category->uraians as $uIndex => $uraian) {

            //             foreach ($uraian->items as $iIndex => $item) {

            //                 $rows[] = [

            //                     'project_id' => $project->id,
            //                     'rab_item_id' => $item->id,

            //                     'category_name' => $category->name,
            //                     'uraian_name' => $uraian->name,

            //                     'job_category_id' => $item->job_category_id,
            //                     'uraian' => $item->job_name,

            //                     'price' => $item->price,
            //                     'volume' => $item->volume,
            //                     'total' => $item->total,
            //                     'satuan' => $item->satuan,

            //                     'bobot_percent' => 0,

            //                     'category_order' => $cIndex,
            //                     'uraian_order' => $uIndex,
            //                     'item_order' => $iIndex,

            //                     'created_at' => now(),
            //                     'updated_at' => now(),
            //                 ];
            //             }
            //         }
            //     }

            //     BuildProcessItem::insert($rows);
            // }