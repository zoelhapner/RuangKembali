<?php

namespace App\Http\Controllers;

use App\Models\RabProcess;
use App\Models\RabProcessItem;
use App\Models\JobCategory;
use App\Models\Project;
use App\Models\RabImage;
use App\Models\RabUraianImage;
use App\Models\RabProcessUraian;
use App\Models\RabProcessCategory;
use App\Services\ProjectNotifier;
use App\Services\BuildProcessSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class RabProcessController extends Controller
{

public function store(Request $request)
{
    abort_if(auth()->user()->cannot('lihat daftar proyek'), 403);

    $request->validate([
        'project_id' => 'required|exists:projects,id',
        'contact_name' => 'required|string',
        'job_location' => 'required|string',
        'job_duration' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.job_category_id' => 'required|exists:job_categories,id',
        'items.*.job_name' => 'required|string',
        'items.*.satuan' => 'required|string',
        'items.*.volume' => 'required|numeric|min:0',
        'items.*.base_price' => 'required|numeric|min:0',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.total' => 'required|numeric|min:0',

        'profit' => 'required|numeric|max:100',
        'overhead' => 'required|numeric|max:100',
        'discount' => 'nullable|numeric|min:0',
        'tax_rate' => 'nullable|numeric|min:0|max:100',
        'shipping' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string',
    ]);

    $project = null;
    $rab = null;

    DB::transaction(function () use ($request, &$project, &$rab) {

        $project = Project::findOrFail($request->project_id);

        // 🔹 Ambil langsung dari form (hasil JS)
        $subtotal = collect($request->items)->sum(fn($i) => (float) $i['total']);

        $discount = (float) ($request->discount ?? 0);
        $taxRate  = (float) ($request->tax_rate ?? 0);
        $shipping = (float) ($request->shipping ?? 0);

        $subtotalAfterDiscount = max($subtotal - $discount, 0);
        $taxTotal = $subtotalAfterDiscount * ($taxRate / 100);
        $grandTotal = $subtotalAfterDiscount + $taxTotal + $shipping;

        $rab = RabProcess::create([
            'project_id' => $project->id,
            'contact_name' => $request->contact_name,
            'job_location' => $request->job_location,
            'job_duration' => $request->job_duration,

            'base_subtotal' => $subtotal,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'subtotal_after_discount' => $subtotalAfterDiscount,

            'tax_rate' => $taxRate,
            'tax_total' => $taxTotal,
            'profit' => $request->profit,      
            'overhead' => $request->overhead,    
            'shipping' => $shipping,
            'grand_total' => $grandTotal,

            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'analisa_version' => Cache::get('job_category_last_updated', 0),
        ]);

        $categoryMap = [];

        foreach ($request->categories as $cat) {

            $category = RabProcessCategory::create([
                'rab_process_id' => $rab->id,
                'name' => $cat['name'],
            ]);

            $categoryMap[$cat['key']] = $category->id;
        }

        $uraianMap = [];

        foreach ($request->items as $item) {
        $key = $item['uraian_key'];

        if(!isset($uraianMap[$key])) {

            $uraian = RabProcessUraian::create([
                'rab_process_id' => $rab->id,
                'job_category_id' => $item['job_category_id'],
                'category_id' => $categoryMap[$item['category_key']],
                'uraian_key' => $key,
                'name' => $item['uraian_name'],
            ]);

            $uraianMap[$key] = $uraian->id;
        }
            RabProcessItem::create([
                'rab_process_id' => $rab->id,
                'uraian_id' => $uraianMap[$key],
                'job_category_id' => $item['job_category_id'],
                'job_name' => $item['job_name'],
                'base_price' => $item['base_price'],
                'satuan' => $item['satuan'],
                'volume' => $item['volume'],
                'price' => $item['price'],   
                'total' => $item['total'],  
            ]);
        }

        foreach($request->uraian_images ?? [] as $uraianKey => $images){

            foreach($images as $imgId){

                RabUraianImage::create([
                    'rab_id' => $rab->id,
                    'uraian_key' => $uraianKey,
                    'image_id' => $imgId
                ]);

            }

        }

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

    $this->notifyProjectEvent($project, 'rab_created');

    return back()->with('success', 'RAB berhasil disimpan dan proyek dinyatakan selesai');
}
public function exportPdf(Project $project)
{
    $rab = $project->rab()->with([
        'categories.uraians.items',
        'categories.uraians.images.image'
    ])->first();

    if (!$rab) abort(404);

    $pdf = Pdf::loadView('rab.pdf', compact('rab', 'project'))
        ->setPaper('A4', 'portrait');

    return $pdf->stream('RENCANA ANGGARAN BIAYA-'.$project->project_name.'.pdf');
}

public function refreshFromMaster(RabProcess $rab)
{
    DB::transaction(function () use ($rab) {

        $subtotal = 0;

        foreach ($rab->items as $item) {

            // Ambil harga terbaru dari job_category
            $job = JobCategory::find($item->job_category_id);

            if (!$job) continue;

            $newPrice = $job->grand_total;

            $base = $item->volume * $newPrice;

            $profitValue = $base * ($item->profit / 100);
            $overheadValue = $base * ($item->overhead / 100);

            $total = $base + $profitValue + $overheadValue;

            $item->update([
                'price' => $newPrice,
                'total' => $total,
            ]);

            $subtotal += $total;
        }

        $discount = $rab->discount;
        $taxRate  = $rab->tax_rate;
        $shipping = $rab->shipping;

        $afterDiscount = max($subtotal - $discount, 0);
        $taxTotal = $afterDiscount * ($taxRate / 100);
        $grandTotal = $afterDiscount + $taxTotal + $shipping;

        $rab->update([
            'subtotal' => $subtotal,
            'subtotal_after_discount' => $afterDiscount,
            'tax_total' => $taxTotal,
            'grand_total' => $grandTotal,
            'analisa_version' => Cache::get('job_category_last_updated', 0),
            'updated_by' => auth()->id(),
        ]);
    });

    return response()->json(['success' => true]);
}

protected function notifyProjectEvent(Project $project, string $event)
{
    $cfg = config("project_events.$event");
    if (!$cfg) return;

    $admin    = auth()->user();
    $customer = $project->customer?->user;

    $targets = [];

    if ($admin) {
        $targets['admin'] = $admin;
    }

    if ($customer) {
        $targets['customer'] = $customer;
    }

    foreach ($targets as $role => $user) {
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
}

public function getPackage($id)
{
    $package = RabProcess::with('items')->findOrFail($id);
    return response()->json($package);
}

public function items($id)
{
    $rab = RabProcess::with([
        'categories.uraians.items',
        'categories.uraians.images.image'
    ])->findOrFail($id);

    $subtotal = 0;

    foreach ($rab->categories as $category) {

        foreach ($category->uraians as $uraian) {

            foreach ($uraian->items as $item) {

                $subtotal += ($item->volume * $item->price);
            }
        }
    }

    $discount = $rab->discount ?? 0;
    $shipping = $rab->shipping ?? 0;
    $taxRate = $rab->tax_rate ?? 0;

    $subtotalAfterDiscount = $subtotal - $discount;

    $taxTotal = $subtotalAfterDiscount * ($taxRate / 100);

    $grandTotal = $subtotalAfterDiscount + $taxTotal + $shipping;

    return response()->json([
        'categories' => $rab->categories,

        'header' => [
            'tax_rate' => $taxRate,
            'discount' => $discount,
            'shipping' => $shipping,

            'subtotal' => $subtotal,
            'subtotal_after_discount' => $subtotalAfterDiscount,
            'tax_total' => $taxTotal,
            'grand_total' => $grandTotal,

            'notes' => $rab->notes,
        ],
    ]);
}

public function upload(Request $request)
{
    $request->validate([
        'image' => 'required|image|max:4096',
        'uraian_id' => 'required|exists:rab_process_uraians,id',
        'rab_id' => 'required|exists:rab_process,id'
    ]);

    $path = $request->file('image')->store('rab/uraian', 'public');

    $img = RabImage::create([
        'path' => $path
    ]);

    RabUraianImage::create([
        'rab_id' => $request->rab_id,
        'uraian_id' => $request->uraian_id,
        'image_id' => $img->id
    ]);

    return response()->json([
        'id' => $img->id,
        'path' => $path,
        'url' => asset('storage/' . $path)
    ]);
}

public function destroy($id)
{
    $img = RabImage::findOrFail($id);

    Storage::disk('public')->delete($img->path);

    $img->delete();

    return response()->json(['success'=>true]);
}
public function uraianImages($uraianId)
{
    $images = RabUraianImage::with('image')
        ->where('uraian_id', $uraianId)
        ->get();

    return response()->json(
        $images->map(fn($i) => [
            'id' => $i->image?->id,
            'url' => $i->image
                ? asset('storage/' . $i->image->path)
                : null
        ])
    );
}
public function structure($id)
{
    $rab = RabProcess::with([
        'categories.uraians.items.category',
        'categories.uraians.images.image'
    ])->findOrFail($id);

    return response()->json([
        'meta' => [
            'profit' => $rab->profit,
            'overhead' => $rab->overhead,
            'discount' => $rab->discount,
            'tax_rate' => $rab->tax_rate,
            'shipping' => $rab->shipping,
        ],
        'categories' => $rab->categories
    ]);
}
public function update(Request $request, Project $project, RabProcess $rab)
{
    abort_if(auth()->user()->cannot('ubah data proyek'), 403);

    $categoryMap = [];

    $uraianMap = [];
    
    $itemMap = [];
    DB::transaction(function () use ($request, $rab, &$categoryMap, &$uraianMap, &$itemMap) {
        $existingCategories = RabProcessCategory::where('rab_process_id', $rab->id)
            ->get()
            ->keyBy('id');

        $existingUraians = RabProcessUraian::where('rab_process_id', $rab->id)
            ->get()
            ->keyBy('id');

        $existingItems = RabProcessItem::where('rab_process_id', $rab->id)
            ->get()
            ->keyBy('id');

        $usedCategoryIds = [];
        $usedUraianIds = [];
        $usedItemIds = [];

        foreach ($request->categories ?? [] as $i => $cat) {

            if (empty($cat['name'])) continue;

            $category = null; 

            if (!empty($cat['id'])) {

                $category = $existingCategories[$cat['id']] ?? null;

                if ($category) {
                    $category->update([
                        'name' => $cat['name'],
                        'order_no' => $cat['order'],
                    ]);

                    $usedCategoryIds[] = $category->id;
                } else {
                    $category = RabProcessCategory::create([
                        'rab_process_id' => $rab->id,
                        'name' => $cat['name'],
                        'order_no' => $cat['order'],
                    ]);

                    $usedCategoryIds[] = $category->id;
                }

            } else {

                $category = RabProcessCategory::create([
                    'rab_process_id' => $rab->id,
                    'name' => $cat['name'],
                    'order_no' => $cat['order'],
                ]);

                $usedCategoryIds[] = $category->id;
            }

            if ($category) {
                $categoryMap[$cat['temp_id']] = $category->id;
            }
            if (!$category) {
                \Log::error('CATEGORY NULL', [
                    'cat' => $cat
                ]);
            }
        }

        RabProcessCategory::where('rab_process_id', $rab->id)
            ->when(
                count($usedCategoryIds),
                fn($q) => $q->whereNotIn('id', $usedCategoryIds)
            )
            ->when(
                empty($usedCategoryIds),
                fn($q) => $q
            )
            ->delete();

        foreach ($request->categories ?? [] as $cat) {

            foreach ($cat['uraians'] ?? [] as $j => $uraian) {

                if (empty($uraian['name'])) continue;

                $categoryId = $categoryMap[$cat['temp_id']] ?? null;
                if (!$categoryId) continue;

                $u = null; 

                if (!empty($uraian['id'])) {

                    $u = $existingUraians[$uraian['id']] ?? null;

                    if ($u) {
                        $u->update([
                            'name' => $uraian['name'],
                            'category_id' => $categoryId,
                            'order_no' => $uraian['order'] ?? $j
                        ]);

                        $usedUraianIds[] = $u->id;
                    } else {
                        $u = RabProcessUraian::create([
                            'rab_process_id' => $rab->id,
                            'category_id' => $categoryId,
                            'name' => $uraian['name'],
                            'uraian_key' => $uraian['temp_id'],
                            'order_no' => $uraian['order'] ?? $j,
                        ]);

                        $usedUraianIds[] = $u->id;
                    }

                } else {

                    $u = RabProcessUraian::create([
                        'rab_process_id' => $rab->id,
                        'category_id' => $categoryId,
                        'name' => $uraian['name'],
                        'uraian_key' => $uraian['temp_id'],
                        'order_no' => $uraian['order'] ?? $j,
                    ]);

                    $usedUraianIds[] = $u->id;
                }

                if ($u) {
                    $uraianMap[$uraian['temp_id']] = $u->id;
                }
                if (!$u) {
                    \Log::error('URAIAN NULL', [
                        'uraian_data' => $u
                    ]);
                }
            }
        }

        RabProcessUraian::where('rab_process_id', $rab->id)
        ->when(
            count($usedUraianIds),
            fn($q) => $q->whereNotIn('id', $usedUraianIds)
        )
        ->when(
            empty($usedUraianIds),
            fn($q) => $q
        )
        ->delete();

        $jobIds = collect($request->items)
            ->pluck('job_category_id')
            ->filter()
            ->unique();

        $jobs = JobCategory::with('items')
            ->whereIn('id', $jobIds)
            ->get()
            ->keyBy('id');

        $profit = str_replace(',', '.', $request->profit ?? 0);
        $overhead = str_replace(',', '.', $request->overhead ?? 0);

        foreach ($request->items ?? [] as $index => $item) {

            if (empty($item['job_category_id'])) continue;

            $uraianId = $uraianMap[$item['uraian_key']] ?? null;
            if (!$uraianId) continue;

            $job = $jobs[$item['job_category_id']] ?? null;
            if (!$job) continue;

            $basePrice = $job->grand_total;

            $price = $basePrice +
                ($basePrice * $profit / 100) +
                ($basePrice * $overhead / 100);

            $volume = $item['volume'];

            $total = $volume * $price;

            if (!empty($item['id'])) {

                $existing = $existingItems[$item['id']] ?? null;

                if ($existing) {
                    $existing->update([
                        'uraian_id' => $uraianId,
                        'job_category_id' => $job->id,
                        'job_name' => $job->nama_pekerjaan,
                        'base_price' => $basePrice,
                        'satuan' => $job->satuan ?? '',
                        'volume' => $volume,
                        'price' => $price,
                        'total' => $total,
                        'order_no' => $item['order']
                    ]);

                    $usedItemIds[] = $existing->id;
                    if (!empty($item['id'])) {
                        $itemMap[$item['id']] = $existing->id;
                    }
                }

            } else {

                $new = RabProcessItem::create([
                    'rab_process_id' => $rab->id,
                    'uraian_id' => $uraianId,
                    'job_category_id' => $job->id,
                    'job_name' => $job->nama_pekerjaan,      
                    'base_price' => $basePrice,     
                    'satuan' => $job->satuan ?? '', 
                    'volume' => $volume,
                    'price' => $price,
                    'total' => $total,
                    'order_no' => $item['order'],
                ]);

                $usedItemIds[] = $new->id;
                if (!empty($item['id'])) {
                    $itemMap[$item['id']] = $new->id;
                }
            }
        }

        RabProcessItem::where('rab_process_id', $rab->id)
            ->when(
                count($usedItemIds),
                fn($q) => $q->whereNotIn('id', $usedItemIds)
            )
            ->when(
                empty($usedItemIds),
                fn($q) => $q
            )
            ->delete();

        $subtotal = RabProcessItem::where('rab_process_id', $rab->id)
            ->sum('total');

        $discount = $request->discount;
        $taxRate  = $request->tax_rate;
        $shipping = $request->shipping;

        $afterDiscount = max(0, $subtotal - $discount);
        $tax = round($afterDiscount * $taxRate / 100);
        $grandTotal = $afterDiscount + $tax + $shipping;

        $rab->update([
            'contact_name' => $request->contact_name,
            'job_location' => $request->job_location,
            'job_duration' => $request->job_duration,
            'base_subtotal' => $subtotal,
            'discount' => $discount,
            'subtotal_after_discount' => $afterDiscount,
            'profit' => $profit,
            'overhead' => $overhead,
            'tax_rate' => $taxRate,
            'tax_total' => $tax,
            'shipping' => $shipping,
            'subtotal' => $subtotal,
            'grand_total' => $grandTotal,
            'updated_by' => auth()->id(),
        ]);
    });

    return response()->json([
        'status' => 'saved',
    ]);
}
public function loadDraft(RabProcess $rab)
{
    $hasDraft = RabProcessCategory::where('rab_process_id', $rab->id)
        ->where('is_draft', true)
        ->exists();

    $isDraft = $hasDraft;

    $categories = RabProcessCategory::where('rab_process_id', $rab->id)
        ->where('is_draft', $isDraft)
        ->orderBy('order_no')
        ->get();

    $uraians = RabProcessUraian::with([
        'images.image'
    ])
    ->where('rab_process_id', $rab->id)
    ->where('is_draft', $isDraft)
    ->orderBy('order_no')
    ->get()
    ->groupBy('category_id');

    $items = RabProcessItem::where('rab_process_id', $rab->id)
        ->where('is_draft', $isDraft)
        ->orderBy('order_no')
        ->get()
        ->groupBy('uraian_id');

    $result = [
        'meta' => [
            'profit' => $rab->profit,
            'overhead' => $rab->overhead,
        ],
        'categories' => []
    ];

    foreach ($categories as $cat) {

        $catData = [
            'id' => $cat->id,
            'name' => $cat->name,
            'uraians' => []
        ];

        foreach ($uraians[$cat->id] ?? [] as $u) {

            $uData = [
                'id' => $u->id,
                'uraian_key' => $u->uraian_key,
                'name' => $u->name,

                'images' => $u->images->map(function ($pivot) {

                    $image = $pivot->image;

                    if (!$image) {
                        return null;
                    }

                    return [
                        'id' => $image->id,
                        'url' => $image->url,
                    ];

                })->filter()->values()->toArray(),

                'items' => []
            ];

            foreach ($items[$u->id] ?? [] as $it) {

                $uData['items'][] = [
                    'id' => $it->id,
                    'job_category_id' => $it->job_category_id,
                    'satuan' => $it->satuan,
                    'volume' => $it->volume,
                    'base_price' => $it->base_price,
                    'price' => $it->price,
                    'total' => $it->total,
                ];
            }

            $catData['uraians'][] = $uData;
        }

        $result['categories'][] = $catData;
    }

    return response()->json($result);
}

public function reorder(Request $request, RabProcess $rab)
{
    DB::transaction(function () use ($request, $rab) {

        foreach ($request->structure ?? [] as $cat) {

            RabProcessCategory::where('id', $cat['id'])
                ->where('rab_process_id', $rab->id)
                ->update([
                    'order_no' => $cat['order']
                ]);

            foreach ($cat['uraians'] ?? [] as $uraian) {

                RabProcessUraian::where('id', $uraian['id'])
                    ->where('rab_process_id', $rab->id)
                    ->update([
                        'order_no' => $uraian['order']
                    ]);

                foreach ($uraian['items'] ?? [] as $item) {

                    RabProcessItem::where('id', $item['id'])
                        ->where('rab_process_id', $rab->id)
                        ->update([
                            'order_no' => $item['order']
                        ]);
                }
            }
        }
    });

    return response()->json(['status' => 'ok']);
}
}