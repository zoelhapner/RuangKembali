<?php

namespace App\Http\Controllers;

use App\Models\RabProcess;
use App\Models\RabProcessItem;
use App\Models\JobCategory;
use App\Models\Project;
use App\Services\RabRecalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class RabController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'project_id' => 'required|exists:projects,id',
        'contact_name' => 'required|string',
        'job_location' => 'required|string',
        'job_duration' => 'nullable|string',

        'items' => 'required|array|min:1',
        'items.*.job_category_id' => 'required|exists:job_categories,id',
        'items.*.job_name' => 'required|string',
        'items.*.satuan' => 'required|string',
        'items.*.volume' => 'required|numeric|min:0.01',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.total' => 'required|numeric|min:0',

        // SUMMARY INPUT
        'profit'   => 'nullable|numeric|min:0|max:100',
        'overhead' => 'nullable|numeric|min:0|max:100',
        'discount' => 'nullable|numeric|min:0',
        'tax_rate' => 'nullable|numeric|min:0|max:100',
        'shipping' => 'nullable|numeric|min:0',

        'notes' => 'nullable|string',
    ]);

    DB::transaction(function () use ($request) {

        $project = Project::findOrFail($request->project_id);

        // ================================
        // 🔹 HITUNG ULANG DARI ITEMS
        // ================================
        $subtotal = collect($request->items)->sum(function ($item) {
            return (float) $item['total'];
        });

        $profitPercent   = (float) ($request->profit ?? 0);
        $overheadPercent = (float) ($request->overhead ?? 0);
        $discount        = (float) ($request->discount ?? 0);
        $taxRate         = (float) ($request->tax_rate ?? 0);
        $shipping        = (float) ($request->shipping ?? 0);

        $profitValue   = $subtotal * ($profitPercent / 100);
        $overheadValue = $subtotal * ($overheadPercent / 100);

        $base = $subtotal + $profitValue + $overheadValue;

        $subtotalAfterDiscount = max($base - $discount, 0);

        $taxTotal = $subtotalAfterDiscount * ($taxRate / 100);

        $grandTotal = $subtotalAfterDiscount + $taxTotal + $shipping;

        // ================================
        // 🔹 SIMPAN RAB
        // ================================
        $rab = RabProcess::create([
            'project_id' => $project->id,
            'contact_name' => $request->contact_name,
            'job_location' => $request->job_location,
            'job_duration' => $request->job_duration,

            'subtotal' => $subtotal,
            'profit' => $profitPercent,
            'overhead' => $overheadPercent,
            'discount' => $discount,
            'subtotal_after_discount' => $subtotalAfterDiscount,

            'tax_rate' => $taxRate,
            'tax_total' => $taxTotal,

            'shipping' => $shipping,
            'grand_total' => $grandTotal,

            'notes' => $request->notes,
        ]);

        // ================================
        // 🔹 SIMPAN ITEMS
        // ================================
        foreach ($request->items as $item) {
            RabProcessItem::create([
                'rab_process_id' => $rab->id,
                'job_category_id' => $item['job_category_id'],
                'job_name' => $item['job_name'],
                'satuan' => $item['satuan'],
                'volume' => $item['volume'],
                'price' => $item['price'],
                'total' => $item['total'],
            ]);
        }

        // ================================
        // 🔹 UPDATE STATUS PROJECT
        // ================================
        $finalLevel = $project->levels()
            ->where('level_name', 'Proses Pengerjaan RAB')
            ->first();

        if ($finalLevel && !$finalLevel->is_completed) {
            $finalLevel->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }
    });

    return back()->with('success', 'RAB berhasil disimpan dan proyek dinyatakan selesai');
}


public function exportPdf(Project $project)
{
    $rab = $project->rab;
    if (!$rab) abort(404);

    $grouped = [];

    foreach ($rab->items as $item) {

        $kode = $item->category->kode_group ?? '-';
        $nama = $item->category->nama_group ?? 'PEKERJAAN LAIN-LAIN';

        if (!isset($grouped[$kode])) {
            $grouped[$kode] = [
                'kode' => $kode,
                'nama' => $nama,
                'items' => [],
                'subtotal' => 0
            ];
        }

        $grouped[$kode]['items'][] = $item;
        $grouped[$kode]['subtotal'] += $item->total;
    }

    $pdf = Pdf::loadView('rab.pdf', compact('rab', 'project', 'grouped'))
        ->setPaper('A4', 'portrait');

    return $pdf->stream('RAB-'.$project->name.'.pdf');
}

public function recalculateAll()
{
    RabRecalculator::recalcAll();

    return back()->with('success', 'Semua RAB berhasil direfresh dari harga master');
}
}