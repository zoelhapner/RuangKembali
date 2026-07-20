<?php

namespace App\Services;

use App\Models\Project;
use App\Models\BuildProcessItem;
use App\Models\InvoiceBuild;
use App\Services\InvoiceBuildNumberGenerator;
use Illuminate\Support\Facades\DB;

class BuildInvoiceService
{

// public function generateJustek(Project $project)
// {

//     /*
//     Hitung total Justek dari progress
//     */

//     $justekTotal = DB::table('build_weekly_progress')
//         ->join('build_process_items','build_process_items.id','=','build_weekly_progress.build_process_item_id')
//         ->where('build_process_items.project_id',$project->id)
//         ->selectRaw('
//             SUM(
//                 (just_tambah - just_kurang + just_baru)
//                 * price
//             ) as total
//         ')
//         ->value('total') ?? 0;


//     if($justekTotal == 0) return;


//     /*
//     Cek sudah ada belum
//     */

//     $exists = InvoiceBuild::where([
//         'project_id'=>$project->id,
//         'invoice_type'=>InvoiceBuild::TYPE_JUSTEK
//     ])->exists();


//     if($exists) return;


//     InvoiceBuild::create([

//         'project_id'=>$project->id,

//         'invoice_type'=>InvoiceBuild::TYPE_JUSTEK,

//         'invoice_number'=>InvoiceBuildNumberGenerator::generateJustek(),

//         'invoice_date'=>now(),

//         'termin'=>0,

//         'progress_start'=>0,

//         'progress_end'=>0,

//         'payment_percentage'=>0,

//         'amount'=>$justekTotal,

//         'status'=>'waiting'

//     ]);

// }
public function generateJustek(Project $project)
{
    $justekTotal = DB::table('build_weekly_progress')
        ->join(
            'build_process_items',
            'build_process_items.id',
            '=',
            'build_weekly_progress.build_process_item_id'
        )
        ->where('build_process_items.project_id',$project->id)
        ->selectRaw('
            SUM(
                (just_tambah - just_kurang + just_baru)
                * price
            ) as total
        ')
        ->value('total') ?? 0;

    if($justekTotal == 0){

        InvoiceBuild::where([
            'project_id'=>$project->id,
            'invoice_type'=>InvoiceBuild::TYPE_JUSTEK
        ])->delete();

        return;
    }

    $invoice = InvoiceBuild::where([
        'project_id'=>$project->id,
        'invoice_type'=>InvoiceBuild::TYPE_JUSTEK
    ])->first();

    if(!$invoice){

        InvoiceBuild::create([

            'project_id'=>$project->id,
            'invoice_type'=>InvoiceBuild::TYPE_JUSTEK,
            'invoice_number'=>InvoiceBuildNumberGenerator::generateJustek(),
            'invoice_date'=>now(),
            'termin'=>0,
            'progress_start'=>0,
            'progress_end'=>0,
            'payment_percentage'=>0,
            'amount'=>$justekTotal,
            'status'=>'waiting'

        ]);

        return;
    }

    $invoice->update([
        'amount'=>$justekTotal
    ]);

}
}

