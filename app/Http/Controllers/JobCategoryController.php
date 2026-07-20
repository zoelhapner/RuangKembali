<?php

namespace App\Http\Controllers;

use App\Models\JobCategory;
use App\Models\JobCategoryItem;
use App\Models\LaborCost;
use App\Models\EquipmentCost;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Services\RabRecalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class JobCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jobs = JobCategory::select('*');

            return DataTables::of($jobs)
                ->addIndexColumn() // untuk kolom No
                ->editColumn('grand_total', function ($row) {
                    return 'Rp ' . number_format($row->grand_total ?? 0, 0, ',', '.');
                })
                ->editColumn('nama_group', function ($row) {
                    $url = route('job-categories.edit', $row->id);
                    $name = Str::title($row->nama_group ?? '-');
                    return '<a href="'.$url.'">'.e($name).'</a>';
                })
                // ->addColumn('aksi', function ($row) {
                //     return '
                //         <a href="'.route('job-categories.edit', $row->id).'" 
                //         class="btn btn-sm btn-dark">
                //             <i class="ti ti-edit"></i>
                //         </a>

                //         <form action="'.route('job-categories.destroy', $row->id).'"
                //             method="POST" class="d-inline"
                //             onsubmit="return confirm(\'Hapus data ini?\')">
                //             '.csrf_field().method_field('DELETE').'
                //             <button class="btn btn-sm btn-dark">
                //                 <i class="ti ti-trash"></i>
                //             </button>
                //         </form>
                //     ';
                // })
                ->addColumn('aksi', function ($row) {
                    $buttons = '';
                        if (auth()->user()->can('ubah data alat')) {
                            $buttons .= '<a href="'.route('job-categories.edit', $row->id).'" 
                        class="btn btn-sm btn-dark">
                            <i class="ti ti-edit"></i>
                        </a>';
                        }
                        if (auth()->user()->can('lihat data alat')) {
                            $buttons .= '<button data-id="' . $row->id . '" class="btn btn-icon btn-sm btn-dark btn-duplicate" title="Duplikat">
                                            <i class="ti ti-copy"></i>
                                        </button>';

                        }
                        if (auth()->user()->can('hapus data alat')) {
                            $buttons .= '<button 
                                                data-url="' . route('job-categories.destroy', $row->id) . '" 
                                                class="btn btn-icon btn-sm btn-dark btn-delete">
                                                <i class="ti ti-trash"></i>
                                        </button>';
                        }
                        return '
                                <div class="d-flex gap-1">
                                    ' . $buttons . '
                                </div>
                            ';
                })
                ->orderColumn('kode_urut', function ($query, $order) {
                    $query->orderByRaw("
                        lower(split_part(kode_urut, '.', 1)) $order,
                        (
                            SELECT array_agg(val::int)
                            FROM regexp_split_to_table(
                                trim(trailing '.' from kode_urut),
                                '\\.'
                            ) AS val
                            WHERE val ~ '^[0-9]+$'
                        )
                    ");
                })
                ->rawColumns(['aksi', 'nama_group'])
                ->make(true);
        }

        return view('job-categories.index');
    }
    
    public function create()
    {
        $groups = JobCategory::select('nama_group')
            ->distinct()
            ->orderBy('nama_group')
            ->pluck('nama_group');

        return view('job-categories.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bidang' => 'required|string|max:50',
            'kode_group' => 'required|string|max:50',
            'nama_group' => 'required|string|max:255',
            'kode' => 'required|string|max:50',
            'kode_urut' => 'required|string|max:100|unique:job_categories,kode_urut',
            'nama_pekerjaan' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
        ]);

        JobCategory::create($data);

        return redirect()
            ->route('job-categories.index')
            ->with('success', 'Data pekerjaan berhasil ditambahkan.');
    }

    public function edit(JobCategory $jobCategory)
    {
        $groups = JobCategory::select('bidang','nama_group')
            ->distinct()
            ->orderBy('bidang')
            ->get()
            ->groupBy('bidang');

        $laborCosts = LaborCost::all();
        $productSuppliers = ProductSupplier::with('product')->get();
        $equipments = EquipmentCost::all();

        return view(
            'job-categories.edit',
            compact('jobCategory', 'groups', 'laborCosts', 'productSuppliers', 'equipments')
        );
    }

    public function update(Request $request, JobCategory $jobCategory)
    {
        $data = $request->validate([
            'bidang' => 'required|string|max:50',
            'kode_group' => 'required|string|max:50',
            'nama_group' => 'required|string|max:255',
            'kode' => 'required|string|max:50',
            'kode_urut' => 'required|string|max:100|unique:job_categories,kode_urut,' . $jobCategory->id,
            'nama_pekerjaan' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
        ]);

        $jobCategory->update($data);

        return back()->with('success', 'Data pekerjaan berhasil diperbarui.');
    }

    public function destroy(JobCategory $jobCategory)
    {
        $jobCategory->delete();

        return redirect()
            ->route('job-categories.index')
            ->with('success', 'Data pekerjaan berhasil dihapus.');
    }

    public function addItem(Request $request, JobCategory $jobCategory)
    {
        $data = $request->validate([
            'category'          => 'required|in:product,labor,equipment',
            'coefisien'         => 'required|numeric|min:0',

            'product_supplier_id' => 'required_if:category,product|nullable|exists:product_supplier,id',
            'labor_cost_id' => 'required_if:category,labor|nullable|exists:labor_costs,id',
            'equipment_cost_id' => 'required_if:category,equipment|nullable|exists:equipment_costs,id',

            'code'              => 'nullable|string|max:50',
            'unit'              => 'nullable|string|max:50',
            'name'              => 'required|string|max:255',
        ]);

        switch ($data['category']) {

            case 'product':
                $pivot = ProductSupplier::with('product')
                    ->findOrFail($request->product_supplier_id);

                $data['product_id'] = $pivot->product_id;
                $data['product_supplier_id'] = $pivot->id;
                $data['name'] = $pivot->product->name;
                $data['code'] = $pivot->product->sku_code;
                $data['unit'] = $pivot->product->unit_1_name ?: 'pcs';
                $data['base_unit_price'] = $pivot->selling_prices;
                break;

            case 'labor':
                $lab = LaborCost::findOrFail($request->labor_cost_id);

                $data['name'] = $lab->description;
                $data['code'] = $lab->code ?? '-';
                $data['unit'] = $lab->unit;
                $data['base_unit_price'] = $lab->base_unit_price;
                break;

            case 'equipment':
                $eq = EquipmentCost::findOrFail($request->equipment_cost_id);

                $data['name'] = $eq->description;
                $data['code'] = $eq->code ?? '-';
                $data['unit'] = $eq->unit;
                $data['base_unit_price'] = $eq->base_unit_price;
                break;
        }

        $data['job_category_id'] = $jobCategory->id;
        $data['total_price']     = $data['coefisien'] * $data['base_unit_price'];

        JobCategoryItem::create($data);

        RabRecalculator::recalcCategory($jobCategory);

        return back()->with('success', 'Item pekerjaan berhasil ditambahkan.');
    }

    public function saveOverheadProfit(Request $request, JobCategory $jobCategory)
    {
        $data = $request->validate([
            'overhead_percent' => 'nullable|numeric|min:0',
            'profit_percent'   => 'nullable|numeric|min:0',
        ]);

        $jobCategory->update([
            'overhead_percent' => $data['overhead_percent'] ?? 0,
            'profit_percent'   => $data['profit_percent'] ?? 0,
        ]);

        RabRecalculator::recalcCategory($jobCategory);

        return response()->json([
            'success' => true
        ]);
    }

    public function updateItem(Request $request, JobCategoryItem $item)
    {
        $data = $request->validate([
            'category' => 'nullable|string|max:255',
            'item_name' => 'required|string|max:255',
            'is_optional' => 'nullable'
        ]);

        $data['is_optional'] = $request->has('is_optional');

        $item->update($data);

        return back()->with('success', 'Item berhasil diperbarui.');
    }

    public function deleteItem(JobCategoryItem $item)
    {
        $jobCategory = $item->jobCategory; // ambil parent

        $item->delete();

        RabRecalculator::recalcCategory($jobCategory);

        return back()->with('success', 'Item berhasil dihapus.');
    }


    private function recalcJobCategory(JobCategory $jobCategory)
    {
        $subTotal = $jobCategory->items()->sum('total_price');

        $overheadPercent = $jobCategory->overhead_percent ?? 0;
        $profitPercent   = $jobCategory->profit_percent ?? 0;

        $overheadValue = $subTotal * ($overheadPercent / 100);
        $profitValue   = $subTotal * ($profitPercent / 100);

        $grandTotal = $subTotal + $overheadValue + $profitValue;

        $jobCategory->update([
            'subtotal'       => $subTotal,
            'overhead_value' => $overheadValue,
            'profit_value'   => $profitValue,
            'grand_total'    => $grandTotal,
        ]);
        Cache::put('job_category_last_updated', now()->timestamp);
    }

    public function getItems($type)
    {
        return match ($type) {
            // 'product' => Product::select('id', 'name')->get(),
            'product' => ProductSupplier::with(['product','supplier'])
                ->orderByDesc('id')
                ->get()
                ->map(function ($ps) {
                    return [
                        'id'   => $ps->id, // 🔥 pivot_id
                        'name' => $ps->product->name
                            . ' - ' . ($ps->supplier->name ?? '-')
                            . ($ps->label ? " ({$ps->label})" : ''),
                    ];
                }),

            'labor' => LaborCost::selectRaw(
                'id, description as name'
            )->get(),

            'equipment' => EquipmentCost::selectRaw(
                'id, description as name'
            )->get(),
        };
    }

    public function getItemDetail($type, $id)
    {
        return match ($type) {

        // 'product' => $this->getProductWithSupplierPrice($id),

            'labor' => LaborCost::select(
                'id',
                'description as name',
                'code',
                'unit',
                'base_unit_price as price'
            )->findOrFail($id),

            'equipment' => EquipmentCost::select(
                'id',
                'description as name',
                'code',
                'unit',
                'base_unit_price as price'
            )->findOrFail($id),

            'supplier' => $this->getSuppliersByProduct($id),
        };
    }

    // protected function getProductWithSupplierPrice($productId)
    // {
    //     $product = Product::select(
    //         'id',
    //         'name',
    //         'sku_code as code',
    //         'unit_1_name as unit'
    //     )->findOrFail($productId);

    //     $supplier = ProductSupplier::where('product_id', $productId)
    //         ->orderBy('selling_prices') // bisa diganti: supplier utama
    //         ->first();

    //     return [
    //         'id'    => $product->id,
    //         'name'  => $product->name,
    //         'code'  => $product->code,
    //         'unit'  => $product->unit,
    //         'price' => $supplier?->selling_prices ?? 0,
    //     ];
    // }

    public function getSuppliersByProduct($productId)
{
    return ProductSupplier::with('supplier')
        ->where('product_id', $productId)
        ->orderByDesc('id') // biar copy muncul atas
        ->get()
        ->map(function ($ps) {
            return [
                'id'    => $ps->id, // 🔥 pivot id (bukan supplier_id)
                'name'  => ($ps->supplier->name ?? '-')
                            . ($ps->label ? " ({$ps->label})" : ''),
                'price' => $ps->selling_prices,
            ];
        });
}

public function getProductSupplierDetail($productId, $supplierId)
    {
        $pivot = ProductSupplier::with('product')
            ->where('product_id', $productId)
            ->where('supplier_id', $supplierId)
            ->first();

        if (!$pivot) {
            return response()->json([
                'error' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'id'    => $pivot->id,
            'name'  => $pivot->product->name,
            'code'  => $pivot->product->sku_code,
            'unit'  => $pivot->product->unit_1_name ?? '-',
            'price' => $pivot->selling_prices,
        ]);
    }
public function getProductSupplierById($id)
{
    $ps = ProductSupplier::with('product')->findOrFail($id);

    return response()->json([
        'id'    => $ps->id,
        'name'  => $ps->product->name,
        'code'  => $ps->product->sku_code,
        'unit'  => $ps->product->unit_1_name ?? '-',
        'price' => $ps->selling_prices,
    ]);
}

    public function simple($id)
    {
        $job = JobCategory::findOrFail($id);

        return response()->json([
            'id'    => $job->id,
            'kode_group'  => $job->kode_group,
            'nama_group'  => $job->nama_group,
            'name'  => $job->job_name,
            'satuan'=> $job->satuan,
            'harga' => $job->grand_total,
        ]);
    }
    public function duplicate($id)
    {
        DB::beginTransaction();

        try {
            $category = JobCategory::with('items')->findOrFail($id);

            // 1. Clone parent
            $newCategory = $category->replicate();
            $newCategory->kode_urut = $category->kode_urut . '-copy';
            $newCategory->nama_pekerjaan = $category->nama_pekerjaan . ' (Copy)';
            $newCategory->save(); // pakai save saja (lebih jelas)

            // 2. Clone children
            foreach ($category->items as $item) {
                $newItem = $item->replicate();
                $newItem->job_category_id = $newCategory->id;
                $newItem->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data + item berhasil diduplikat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal duplikat: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updateEffective(Request $request, $id)
{
    $job = JobCategory::findOrFail($id);

    $job->update([
            'effective_labor'     => $request->effective_labor !== null ? (float) $request->effective_labor : null,
            'effective_product'   => $request->effective_product !== null ? (float) $request->effective_product : null,
            'effective_equipment' => $request->effective_equipment !== null ? (float) $request->effective_equipment : null,
    ]);
    $job->refresh();
    RabRecalculator::recalcCategory($job);

    return response()->json(['success' => true]);
}
}