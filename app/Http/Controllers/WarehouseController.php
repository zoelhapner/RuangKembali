<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\PostalCode;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\WarehouseStock;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\ProductColor;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
       public function index(Request $request)
    {
        $auth = auth()->user();

        $query = Warehouse::with(['province', 'city', 'district', 'subDistrict', 'postalCode']);

        if ($auth->can('lihat data gudang') && !$auth->can('lihat daftar gudang')) {
            $query->where('user_id', $auth->id);
        }

        if ($request->ajax()) {
            $warehouses = $query->get();

        return DataTables::of($warehouses)
                ->addIndexColumn()
                ->addColumn('province_name', fn($row) => $row->province->name ?? '-')
                ->addColumn('city_name', fn($row) => $row->city->name ?? '-')
                ->addColumn('district_name', fn($row) => $row->district->name ?? '-')
                ->addColumn('sub_district_name', fn($row) => $row->subDistrict->name ?? '-')
                ->addColumn('postal_code', fn($row) => $row->postalCode->postal_code ?? '-')
                ->editColumn('name', function ($row) {
                    $url = route('warehouses.show', $row->id);
                    $name = Str::title($row->name ?? '-');
                    return '<a href="'.$url.'">'.e($name).'</a>';
                })
                ->addColumn('action', function ($warehouse) {
                    $buttons = '';
                    if (auth()->user()->can('ubah data gudang')) {
                        $buttons .= '<a href="' . route('warehouses.edit', $warehouse->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Ubah">
                                        <i class="ti ti-edit"></i>
                                    </a>';
                    }
                    if (auth()->user()->can('lihat data gudang')) {
                        $buttons .= '<a href="' . route('warehouses.show', $warehouse->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Lihat">
                                        <i class="ti ti-eye"></i>
                                    </a>';

                    }
                    if (auth()->user()->can('hapus data gudang')) {
                        $buttons .= '<button data-id="' . $warehouse->id . '" class="btn btn-icon btn-sm btn-dark delete-warehouse" title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>';
                    }
                    return $buttons;
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }

        return view('warehouses.index');
    }

    /**
     * Show the form for creating a new resource.
     */
     public function create()
    {
        $user = auth()->user();
        $provinces = Province::all();
        return view('warehouses.create', compact('user', 'provinces'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'responsible_person' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:warehouses,email',
            'address' => 'required|string|max:255',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'sub_district_id' => 'required|exists:sub_districts,id',
            'postal_code_id' => 'required|exists:postal_codes,id',
        ]);

        Warehouse::create([
            'name' => $request->name,
            'responsible_person' => $request->responsible_person,
            'description' => $request->description,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'district_id' => $request->district_id,
            'sub_district_id' => $request->sub_district_id,
            'postal_code_id' => $request->postal_code_id,
        ]);

        return redirect()->route('warehouses.index')
            ->with('success', 'Data Gudang berhasil ditambahkan.');
    }

    public function show($id)
{
    $warehouse = Warehouse::with([
        'province', 'city', 'district', 'subDistrict', 'postalCode'
    ])->findOrFail($id);

    // ambil stok gudang ini
    $stocks = WarehouseStock::with(['product'])
        ->where('warehouse_id', $id)
        ->orderBy('id', 'DESC')
        ->get();

    return view('warehouses.show', compact('warehouse', 'stocks'));
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        $provinces = Province::all();
        $cities = City::where('province_id', $warehouse->province_id)->get();
        $districts = District::where('city_id', $warehouse->city_id)->get();
        $subDistricts = SubDistrict::where('district_id', $warehouse->district_id)->get();
        $postalCodes = PostalCode::where('sub_district_id', $warehouse->sub_district_id)->get();
        return view('warehouses.edit', compact('warehouse', 'provinces', 'cities', 'districts', 'subDistricts', 'postalCodes'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'responsible_person' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:warehouses,email,' . $warehouse->id,
            'address' => 'required|string|max:255',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'sub_district_id' => 'required|exists:sub_districts,id',
            'postal_code_id' => 'required|exists:postal_codes,id',
        ]);

        $warehouse->update([
            'name' => $request->name,
            'responsible_person' => $request->responsible_person,
            'description' => $request->description,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
            'district_id' => $request->district_id,
            'sub_district_id' => $request->sub_district_id,
            'postal_code_id' => $request->postal_code_id,
        ]);

        return redirect()->route('warehouses.index')
            ->with('success', 'Data Gudang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('warehouses.index')
            ->with('success', 'warehouse berhasil dihapus.');
    }
}
