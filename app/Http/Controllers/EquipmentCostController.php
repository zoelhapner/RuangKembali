<?php

namespace App\Http\Controllers;

use App\Models\EquipmentCost;
use App\Models\JobCategoryItem;
use App\Services\RabRecalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EquipmentCostController extends Controller
{

public function index(Request $request)
{
    if ($request->ajax()) {

        $query = EquipmentCost::query();

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('base_unit_price', function ($row) {
                return $row->base_unit_price;
            })

            ->addColumn('action', function ($row) {
                $buttons = '';
                    if (auth()->user()->can('ubah data alat')) {
                        $buttons .= '<a href="' . route('equipment_costs.edit', $row->id) . '" class="btn btn-icon btn-sm btn-dark" title="Ubah">
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
                                            data-url="' . route('equipment_costs.destroy', $row->id) . '" 
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

            ->rawColumns(['action'])
            ->make(true);
    }

    return view('tools.index');
}

    public function create()
    {
        return view('tools.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'             => 'nullable|string|max:50',
            'description'      => 'required|string|max:255',
            'unit'             => 'required|string|max:50',
            'base_unit_price'  => 'required|numeric|min:0',
            'notes'            => 'nullable|string',
        ]);

        EquipmentCost::create($request->all());

        return redirect()->route('equipment_costs.index')
            ->with('success', 'Data Peralatan berhasil dibuat.');
    }

    public function edit(EquipmentCost $equipment_cost)
    {
        return view('tools.edit', compact('equipment_cost'));
    }

    public function update(Request $request, EquipmentCost $equipment_cost)
    {
        $request->validate([
            'code'             => 'nullable|string|max:50',
            'description'      => 'required|string|max:255',
            'unit'             => 'required|string|max:50',
            'base_unit_price'  => 'required|string',
            'notes'            => 'nullable|string',
        ]);
        DB::transaction(function () use ($request, $equipment_cost) {
        $equipment_cost->update($request->all());
                    JobCategoryItem::where('equipment_cost_id', $equipment_cost->id)->update([
                'base_unit_price' => $equipment_cost->base_unit_price,
                'total_price' => DB::raw('coefisien * ' . $equipment_cost->base_unit_price),
            ]);
        });

        RabRecalculator::recalcByEquipment($equipment_cost->id);

        Cache::put('job_category_last_updated', now()->timestamp);

        return redirect()->route('equipment_costs.index')
            ->with('success', 'Data Peralatan berhasil diperbarui.');
    }

    public function destroy(EquipmentCost $equipment_cost)
    {
        $equipment_cost->delete();

        return redirect()->route('equipment_costs.index')
            ->with('success', 'Data Peralatan berhasil dihapus.');
    }

    public function duplicate($id)
    {
        $data = EquipmentCost::findOrFail($id);

        $new = $data->replicate();

        $new->code = $data->code . '-copy';
        $new->description = $data->description . '-copy';

        $new->save();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diduplikat'
        ]);
    }
}
