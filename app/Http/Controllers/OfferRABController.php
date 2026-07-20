<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferRABRequest;
use App\Models\Offer;
use App\Models\OfferItem;
use App\Models\Project;
use App\Models\ProjectLevel;
use App\Models\OfferCounter;
use App\Services\ProjectNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OfferRABController extends Controller
{
public function store(OfferRABRequest $request)
{
    abort_if(auth()->user()->cannot('lihat daftar proyek'), 403);

    $data = $request->validated();

    $project = Project::findOrFail($data['project_id']);

    DB::beginTransaction();

    try {

        $inputVolume   = (float) $data['volume'];
        $billingVolume = $inputVolume > 0 && $inputVolume < 100 ? 100 : $inputVolume;

        $priceMeter = (float) $data['price_meter'];

        $subtotal = $billingVolume * $priceMeter;

        $discount = (float) ($data['discount'] ?? 0);
        $subtotalAfterDiscount = $subtotal - $discount;

        $taxRate = (float) ($data['tax_rate'] ?? 0);
        $totalTax = $subtotalAfterDiscount * ($taxRate / 100);

        $shipping = (float) ($data['shipping'] ?? 0);
        $grandTotal = $subtotalAfterDiscount + $totalTax + $shipping;

        $offer = Offer::create([
            'project_id'        => $data['project_id'],
            'rab_package_id'    => $data['rab_package_id'],
            'offer_number'      => $this->generateOfferNumber('RAB'),
            'offer_date'        => $data['offer_date'],
            'contact_name'      => $data['contact_name'],
            'volume'            => $inputVolume,
            'satuan'            => $data['satuan'],
            'price_meter'       => $priceMeter,
            'total_price'       => $subtotal,
            'discount'          => $discount,
            'tax_rate'          => $taxRate,
            'total_tax'         => $totalTax,
            'shipping'          => $shipping,
            'grand_total'       => $grandTotal,
            'notes'             => $data['notes'] ?? null,
            'created_by'        => auth()->id(),
        ]);

        if ($request->has('items')) {
            foreach ($request->items as $item) {
                if (empty($item['item_name'])) continue;

                OfferItem::create([
                    'offer_id'  => $offer->id,
                    'item_name' => $item['item_name'],
                    'category'  => $item['category'],
                ]);
            }
        }

        ProjectLevel::where([
            'project_id'  => $data['project_id'],
            'level_order' => 4,
        ])->update(['is_completed' => true]);

        ProjectLevel::where([
            'project_id'  => $data['project_id'],
            'level_order' => 5,
        ])->update(['is_started' => true]);

        DB::commit();

        $creatorUser = auth()->user();

        $event = 'offerrab_created';
        $cfg   = config("project_events.offerrab_created");


        if (!$cfg) {
            throw new \Exception("Config project_events.$event not found");
        }

        $targets = [
            'created_self' => $creatorUser,
        ];

        if ($project->customer?->user) {
            $targets['customer'] = $project->customer->user;
        }

        foreach ($targets as $key => $user) {
            if (!$user) continue;

            if ($user->id === $creatorUser->id) {
                $role = 'created_self';
            } elseif ($project->customer?->user && $user->id === $project->customer->user->id) {
                $role = 'customer';
            }

            if (!isset($cfg['message'][$role])) {
                continue;
            }

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
            ->route('projects.create', ['project_id' => $offer->project_id])
            ->with('success', 'Penawaran berhasil disimpan!');

    } catch (\Exception $e) {

        DB::rollBack();
        return back()->withErrors($e->getMessage());
    }
}

protected function generateOfferNumber(string $type): string
{
    return DB::transaction(function () use ($type) {

        $now = now();
        $yearFull  = $now->format('Y'); // 2026
        $yearShort = $now->format('y'); // 26
        $bulanRomawi = \App\Helpers\GeneralHelper::bulanRomawi($now->month);

        $counter = OfferCounter::where('type', $type)
            ->where('year', $yearFull)
            ->lockForUpdate()
            ->first();

        if (!$counter) {
            $counter = OfferCounter::create([
                'type'        => $type,
                'year'        => $yearFull,
                'last_number' => 0,
            ]);
        }

        $next = $counter->last_number + 1;

        $counter->update([
            'last_number' => $next,
        ]);

        $nomorUrut = str_pad($next, 3, '0', STR_PAD_LEFT);

        return "PH/$type/$yearShort/$bulanRomawi/$nomorUrut";
    });
}


public function update(Request $request, $id)
{
    abort_if(auth()->user()->cannot('ubah data proyek'), 403);

    $offer = Offer::findOrFail($id);

    $request->validate([
        'project_id'        => 'required|uuid|exists:projects,id',
        'offer_number'      => 'required|string',
        'offer_date'        => 'required|date',
        'contact_name'      => 'nullable|string',

        'rab_package_id' => 'required|uuid|exists:rab_packages,id',
        'volume'            => 'nullable|numeric',
        'satuan'            => 'nullable|string',
        'price_meter'       => 'nullable|numeric',
        'total_price'       => 'nullable|numeric',

        'discount'          => 'nullable|numeric',
        'tax_rate'          => 'nullable|numeric',
        'shipping'          => 'nullable|numeric',

        'notes'             => 'nullable|string',
        'items'             => 'array',
    ]);

    $offer->update([
        'project_id'               => $request->project_id,
        'offer_number'             => $request->offer_number,
        'offer_date'               => $request->offer_date,
        'contact_name'             => $request->contact_name,

        'rab_package_id'        => $request->rab_package_id,
        'volume'                   => $request->volume,
        'satuan'                   => $request->satuan,
        'price_meter'              => $request->price_meter,
        'total_price'              => $request->total_price,

        'subtotal'                 => $request->total_price,
        'discount'                 => $request->discount,
        'subtotal_after_discount'  => $request->total_price - $request->discount,

        'tax_rate'                 => $request->tax_rate,
        'tax_total'                => ($request->total_price - $request->discount) * ($request->tax_rate / 100),

        'shipping'                 => $request->shipping,
        'grand_total'              =>
            ($request->total_price - $request->discount) +
            (($request->total_price - $request->discount) * ($request->tax_rate / 100)) +
            $request->shipping,

        'notes'                    => $request->notes,
    ]);

    $offer->items()->delete();

    if ($request->items && count($request->items) > 0) {
        foreach ($request->items as $item) {
            $offer->items()->create([
                'category'   => $item['category'],
                'item_name'  => $item['item_name'],
                'volume'     => $request->volume ?? 0,
                'satuan'     => $request->satuan ?? '-',
                'price'      => $request->price_meter ?? 0,
                'total'      => $request->price_meter * $request->volume,
            ]);
        }
    }

    return back()->with('success', 'Data Penawaran berhasil diperbarui.');
}

public function printPdf(Offer $offer)
{
    // EAGER LOAD semua relasi yang dibutuhkan PDF
    $offer->load([
        'rabpackage',
        'items',
        'project',
        'project.customer',
        'project.employee'
    ]);

        // Amanin nama file PDF
    $safeName = str_replace(['/', '\\'], '-', $offer->offer_number);

    $filename = 'Penawaran-'.$safeName.'.pdf';

    $pdf = Pdf::loadView('offer.rabpdf', compact('offer'))
              ->setPaper('A4', 'portrait');

    return $pdf->stream($filename);
}
// private function generateOfferNumber()
// {
//     $tahunFull = date('Y');        // 2026
//     $tahun = date('y');            // 26
//     $bulan = date('n');            // 1-12
//     $romawiBulan = \App\Helpers\GeneralHelper::bulanRomawi($bulan);

//     // Ambil nomor terakhir di tahun ini saja
//     $lastOffer = \App\Models\Offer::whereYear('offer_date', $tahunFull)
//         ->orderBy('id', 'DESC')
//         ->first();

//     if ($lastOffer) {
//         // PH/DSN/26/I/001 → ambil 001
//         $explode = explode('/', $lastOffer->offer_number);
//         $lastNumber = intval(end($explode)) + 1;
//     } else {
//         // Kalau belum ada di tahun ini → mulai dari 1
//         $lastNumber = 1;
//     }

//     // Format ke 3 digit: 1 → 001
//     $nomorUrut = str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

//     return "PH/RAB/$tahun/$romawiBulan/$nomorUrut";
// }

}
