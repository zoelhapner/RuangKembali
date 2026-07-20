<?php

namespace App\Http\Controllers;

use App\Models\LaborCost;
use App\Models\JobCategoryItem;
use App\Services\RabRecalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class LaborCostController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = LaborCost::query();

            return DataTables::of($query)
                ->addIndexColumn()

                ->editColumn('base_unit_price', function ($row) {
                    return $row->base_unit_price;
                })

                ->addColumn('action', function ($row) {
                    $buttons = '';
                        if (auth()->user()->can('ubah data tenaga')) {
                            $buttons .= '<a href="' . route('labor_costs.edit', $row->id) . '" class="btn btn-icon btn-sm btn-dark" title="Ubah">
                                            <i class="ti ti-edit"></i>
                                        </a>';
                        }
                        if (auth()->user()->can('lihat data tenaga')) {
                            $buttons .= '<button data-id="' . $row->id . '" class="btn btn-icon btn-sm btn-dark btn-duplicate" title="Duplikat">
                                            <i class="ti ti-copy"></i>
                                        </button>';

                        }
                        if (auth()->user()->can('hapus data tenaga')) {
                            $buttons .= '<button 
                                            data-url="' . route('labor_costs.destroy', $row->id) . '" 
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

        return view('labor_costs.index');
    }

    public function create()
    {
        return view('labor_costs.create');
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

        LaborCost::create($request->all());

        return redirect()->route('labor_costs.index')
            ->with('success', 'Data upah tenaga berhasil ditambahkan.');
    }

    public function edit(LaborCost $labor_cost)
    {
        return view('labor_costs.edit', compact('labor_cost'));
    }

    public function update(Request $request, LaborCost $laborCost)
    {
        $request->validate([
            'code'             => 'nullable|string|max:50',
            'description'      => 'required|string|max:255',
            'unit'             => 'required|string|max:50',
            'base_unit_price'  => 'required|string',
            'notes'            => 'nullable|string',
        ]);
        DB::transaction(function () use ($request, $laborCost) {

            $laborCost->update($request->all());
                    JobCategoryItem::where('labor_cost_id', $laborCost->id)
            ->update([
                'base_unit_price' => $request->base_unit_price,
                'total_price' => DB::raw('coefisien * ' . (float) $request->base_unit_price),
            ]);
        });

        RabRecalculator::recalcByLabor($laborCost->id);
        
        Cache::put('job_category_last_updated', now()->timestamp);

        return redirect()->route('labor_costs.index')
            ->with('success', 'Data upah tenaga berhasil diubah.');
    }

    public function destroy(LaborCost $laborCost)
    {
        $laborCost->delete();

        return redirect()->route('labor_costs.index')
            ->with('success', 'Labor cost deleted successfully.');
    }

    public function duplicate($id)
{
    $data = LaborCost::findOrFail($id);

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
