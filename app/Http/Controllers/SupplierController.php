<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Religion;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\PostalCode;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\ProductColor;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = Supplier::with(['user.roles']);

        if ($auth->can('lihat data supplier') && !$auth->can('lihat daftar supplier')) {
            $query->where('user_id', $auth->id);
        }

        if ($request->ajax()) {
            $suppliers = $query->get();

        return DataTables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('fullname', function ($row) {
                    return $row->user->fullname ?? '-';
                })
                ->addColumn('email', function ($row) {
                    return $row->user->email ?? '-';
                })

                ->addColumn('name', function ($row) {
                    return $row->name ?? '-';
                })

                ->addColumn('address', function ($row) {
                    return $row->address ?? '-';
                })

                ->addColumn('phone', function ($row) {
                    return $row->phone ?? '-';
                })
                
                ->editColumn('fullname', function ($row) {
                    $url = route('suppliers.show', $row->id);
                    $name = Str::title($row->user->fullname ?? '-');
                    return '<a href="'.$url.'">'.e($name).'</a>';
                })
                
                ->addColumn('action', function ($supplier) {
                    $buttons = '';
                    if (auth()->user()->can('ubah data supplier')) {
                        $buttons .= '<a href="' . route('suppliers.edit', $supplier->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Ubah">
                                        <i class="ti ti-edit"></i>
                                    </a>';
                    }
                    if (auth()->user()->can('lihat data supplier')) {
                        $buttons .= '<a href="' . route('suppliers.show', $supplier->id) . '" class="btn btn-icon btn-sm btn-dark me-1" title="Lihat">
                                        <i class="ti ti-eye"></i>
                                    </a>';

                    }
                    if (auth()->user()->can('hapus data supplier')) {
                        $buttons .= '<button data-id="' . $supplier->id . '" class="btn btn-icon btn-sm btn-dark delete-suppliers" title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>';
                    }
                    return $buttons;
                })
                ->rawColumns(['fullname', 'action', 'membership'])
                ->make(true);
        }

        return view('suppliers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $religions = Religion::all();
        $provinces = Province::all();
        $roles = Role::all();
        return view('suppliers.create', compact('user', 'roles', 'religions', 'provinces'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'nullable|exists:users,id',
        'fullname' => 'required|string|max:255',
        'nickname' => 'nullable|string|max:100',
        'gender' => 'required|in:1,2',
        'email' => 'required|email|unique:users,email',
        'birth_place' => 'nullable|string|max:100',
        'birth_date' => 'required|date_format:Y-m-d',
        'identity_number' => 'required|regex:/^[0-9]{16}$/|unique:users,identity_number',
        'religion_id' => 'required|exists:religions,id',
        'npwp' => 'nullable|string|max:30',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:255',
        'user_province_id' => 'required|exists:provinces,id',
        'user_city_id' => 'required|exists:cities,id',
        'user_district_id' => 'required|exists:districts,id',
        'user_sub_district_id' => 'required|exists:sub_districts,id',
        'user_postal_code_id' => 'required|exists:postal_codes,id',
        'bank_id' => 'nullable|uuid|exists:banks,id',
        'account_number' => 'nullable|string|max:50',
        'account_holder' => 'nullable|string|max:50',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        // --- data supplier ---
        'supplier_id' => 'required|unique:suppliers,supplier_id',
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:255',
        'province_id' => 'required|exists:provinces,id',
        'city_id' => 'required|exists:cities,id',
        'district_id' => 'required|exists:districts,id',
        'sub_district_id' => 'required|exists:sub_districts,id',
        'postal_code_id' => 'required|exists:postal_codes,id',
        'role' => 'required|array',
        'role.*' => 'string|exists:roles,name',
    ]);

    // Jika alamat pengiriman sama dengan domisili user
        if ($request->has('same_address')) {
            $validated['province_id'] = $validated['user_province_id'];
            $validated['city_id'] = $validated['user_city_id'];
            $validated['district_id'] = $validated['user_district_id'];
            $validated['sub_district_id'] = $validated['user_sub_district_id'];
            $validated['postal_code_id'] = $validated['user_postal_code_id'];
            $validated['shipping_address'] = $validated['address'];
            $validated['shipping_name'] = $validated['fullname'];
            $validated['shipping_phone'] = $validated['phone'];
        }

    DB::transaction(function () use ($validated, $request) {
        if ($request->hasFile('photo')) {
            $filename = Str::uuid().'.'.$request->file('photo')->getClientOriginalExtension();

            $path = $request->file('photo')->storeAs(
                'photos',
                $filename,
                'public'
            );

            $validated['photo'] = $path;   // → photos/uuid.jpg
        }

        // 🔹 Cek user atau buat baru
        if (!empty($validated['user_id'])) {
            $user = User::find($validated['user_id']);
        } else {
            $password = '12345678';
            $user = User::create([
                'id' => Str::uuid(),
                'fullname' => $validated['fullname'],
                'nickname' => $validated['nickname'],
                'birth_place' => $validated['birth_place'],
                'birth_date' => $validated['birth_date'],
                'identity_number' => $validated['identity_number'],
                'npwp' => $validated['npwp'],
                'address' => $validated['address'],
                'religion_id' => $validated['religion_id'],
                'province_id' => $validated['user_province_id'],
                'city_id' => $validated['user_city_id'],
                'district_id' => $validated['user_district_id'],
                'sub_district_id' => $validated['user_sub_district_id'],
                'postal_code_id' => $validated['user_postal_code_id'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'photo' => $validated['photo'] ?? null,
                'bank_id' => $validated['bank_id'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'account_holder' => $validated['account_holder'] ?? null,
            ]);

            session()->flash('new_user_password', $password);
        }

        // 🔹 Assign role tanpa hapus role lama
        if (!empty($validated['role'])) {
            foreach ($validated['role'] as $r) {
                if (!$user->hasRole($r)) {
                    $user->assignRole($r);
                }
            }
        }

        

        // 🔹 Simpan supplier
        Supplier::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'supplier_id' => $validated['supplier_id'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'province_id' => $validated['province_id'],
            'city_id' => $validated['city_id'],
            'district_id' => $validated['district_id'],
            'sub_district_id' => $validated['sub_district_id'],
            'postal_code_id' => $validated['postal_code_id'],
        ]);
    });

    return redirect()
        ->route('suppliers.index')
        ->with('success', 'Data supplier berhasil ditambahkan.' .
            (session('new_user_password') ? ' Akun user baru dibuat. Password: ' . session('new_user_password') : '')
        );
}


public function generateSupplierIdAjax()
{
    $lastNumber = Supplier::selectRaw("MAX(CAST(SUBSTRING(supplier_id, 3) AS INTEGER)) as max_supplier_id")->value('max_supplier_id');
    $newNumber = ($lastNumber ?? 0) + 1;

    $newSupplierId = 'S-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

    return response()->json([
        'supplier_id' => $newSupplierId
    ]);
}

     public function show(Supplier $supplier)
    {
        $supplier->load('user');
        // $productColors = $product->colors->pluck('id')->toArray();
        return view('suppliers.show', [
            'user' => $supplier->user,
            'supplier' => $supplier,
            'products' => $supplier->products()
                ->withPivot(['buying_prices', 'selling_prices', 'special_prices', 'stock'])
                ->orderByRaw('products.sku_code')
                ->get(),
            'product' => new Product(),
            'colors' => ProductColor::all(),
            'brands' => ProductBrand::all(),
            'categories' => ProductCategory::all(),
            'types' => ProductType::all(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $supplier = Supplier::with(['user.roles', 'user.bank'])->findOrFail($id);
        $user = $supplier->user;
        $religions = Religion::all();
        $provinces = Province::all();
        $roles = Role::all();
        $selectedRoles = $user->roles->pluck('name')->toArray();
        $cities = City::where('province_id', $user->province_id)->get();
        $districts = District::where('city_id', $user->city_id)->get();
        $subDistricts = SubDistrict::where('district_id', $user->district_id)->get();
        $postalCodes = PostalCode::where('sub_district_id', $user->sub_district_id)->get();
        
        return view('suppliers.edit', compact('user', 'roles', 'selectedRoles', 'supplier',
        'religions', 'provinces', 'cities', 'districts', 'subDistricts', 'postalCodes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
{
    $validated = $request->validate([
        // --- data user ---
        'fullname' => 'required|string|max:255',
        'nickname' => 'nullable|string|max:100',
        'gender' => 'required|in:1,2',
        'email' => 'required|email|unique:users,email,' . $supplier->user_id,
        'birth_place' => 'nullable|string|max:100',
        'birth_date' => 'nullable|date_format:Y-m-d',
        'identity_number' => [
            'required',
            'regex:/^[0-9]{16}$/',
            Rule::unique('users', 'identity_number')->ignore($supplier->user_id),
        ],
        'religion_id' => 'nullable|exists:religions,id',
        'npwp' => 'nullable|string|max:30',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'user_province_id' => 'required|exists:provinces,id',
        'user_city_id' => 'required|exists:cities,id',
        'user_district_id' => 'required|exists:districts,id',
        'user_sub_district_id' => 'required|exists:sub_districts,id',
        'user_postal_code_id' => 'required|exists:postal_codes,id',
        'bank_id' => 'nullable|uuid|exists:banks,id',
        'account_number' => 'nullable|string|max:50',
        'account_holder' => 'nullable|string|max:50',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        // --- data supplier ---
        'supplier_id' => 'required|string|max:50|unique:suppliers,supplier_id,' . $supplier->id,
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:255',
        'province_id' => 'required|exists:provinces,id',
        'city_id' => 'required|exists:cities,id',
        'district_id' => 'required|exists:districts,id',
        'sub_district_id' => 'required|exists:sub_districts,id',
        'postal_code_id' => 'required|exists:postal_codes,id',
        'role' => 'nullable|array',
        'role.*' => 'string|exists:roles,name',
    ]);

    if ($request->has('same_address')) {
            $validated['province_id'] = $validated['user_province_id'];
            $validated['city_id'] = $validated['user_city_id'];
            $validated['district_id'] = $validated['user_district_id'];
            $validated['sub_district_id'] = $validated['user_sub_district_id'];
            $validated['postal_code_id'] = $validated['user_postal_code_id'];
            $validated['shipping_address'] = $validated['address'];
            $validated['shipping_name'] = $validated['fullname'];
            $validated['shipping_phone'] = $validated['phone'];
        }

    $newPhotoPath = null;

    DB::transaction(function () use ($validated, $supplier, $request) {
        $user = $supplier->user;

        if ($request->hasFile('photo')) {
            $newPhotoPath = $request->file('photo')->storeAs(
                'photos',
                Str::uuid().'.'.$request->file('photo')->getClientOriginalExtension(),
                'public'
            );

            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $validated['photo'] = $newPhotoPath;
        }

        // 🔹 Update user data
        $user->update([
            'fullname' => $validated['fullname'],
            'nickname' => $validated['nickname'],
            'birth_place' => $validated['birth_place'],
            'birth_date' => $validated['birth_date'],
            'identity_number' => $validated['identity_number'],
            'religion_id' => $validated['religion_id'],
            'npwp' => $validated['npwp'],
            'address' => $validated['address'],
            'province_id' => $validated['user_province_id'],
            'city_id' => $validated['user_city_id'],
            'district_id' => $validated['user_district_id'],
            'sub_district_id' => $validated['user_sub_district_id'],
            'postal_code_id' => $validated['user_postal_code_id'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'bank_id' => $validated['bank_id'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'account_holder' => $validated['account_holder'] ?? null,
            'photo' => $validated['photo'] ?? $user->photo,
        ]);

        // 🔹 Role management: tetap multi-role aman
        if (!empty($validated['role'])) {
            foreach ($validated['role'] as $r) {
                if (!$user->hasRole($r)) {
                    $user->assignRole($r);
                }
            }
        } else {
            // Pastikan minimal masih punya role supplier
            if (!$user->hasRole('Mitra Supplier')) {
                $user->assignRole('Mitra Supplier');
            }
        }
        // 🔹 Update data supplier
        $supplier->update([
            'supplier_id' => $validated['supplier_id'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'province_id' => $validated['province_id'],
            'city_id' => $validated['city_id'],
            'district_id' => $validated['district_id'],
            'sub_district_id' => $validated['sub_district_id'],
            'postal_code_id' => $validated['postal_code_id'],
            'photo' => $validated['photo'] ?? $supplier->photo,
        ]);
    });

    return redirect()
        ->route('suppliers.show', $supplier->id)
        ->with('success', 'Data supplier berhasil diperbarui.');
}

    public function destroy(Supplier $supplier): JsonResponse
{
    DB::transaction(function () use ($supplier) {
        $user = $supplier->user;

        // 🔹 Hapus foto supplier dari storage kalau ada
        if ($supplier->photo && Storage::disk('public')->exists('photos/' . $supplier->photo)) {
            Storage::disk('public')->delete('photos/' . $supplier->photo);
        }

        // 🔹 Hapus record supplier
        $supplier->delete();

        // 🔹 Cek user yang terhubung
        if ($user) {
            $roles = $user->roles->pluck('name')->toArray();

            // Kalau hanya punya role "supplier"
            if (count($roles) === 1 && in_array('Mitra Supplier', $roles)) {

                // Hapus juga foto user kalau ada
                if ($user->photo && Storage::disk('public')->exists('photos/' . $user->photo)) {
                    Storage::disk('public')->delete('photos/' . $user->photo);
                }

                // Hapus akun user
                $user->delete();

            } else {
                // Kalau user masih punya role lain, hapus hanya role supplier-nya
                $user->removeRole('Mitra Supplier');
            }
        }
    });

    return response()->json([
        'status' => 'success',
        'message' => 'Data supplier berhasil dihapus.'
    ]);
}

public function getProducts($id)
{
    $products = DB::table('supplier_products')
        ->join('products', 'products.id', '=', 'supplier_products.product_id')
        ->where('supplier_products.supplier_id', $id)
        ->select(
            'products.id',
            'products.name',
            'supplier_products.purchase_price'
        )
        ->get();

    return response()->json($products);
}

public function datatableProducts(Request $request, Supplier $supplier)
{
    $query = $supplier->products()
        ->select([
            'products.id',
            'products.name',
            'products.sku_code',
            'product_supplier.id as pivot_id',
            'product_supplier.selling_prices',
            'product_supplier.stock',
            'product_supplier.label',
        ]);

    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('name_with_label', function ($row) use ($supplier) {

            $badge = '';
            if ($row->label === 'copy') {
                $badge = ' <span class="badge bg-warning">Copy</span>';
            }

            return '
            <div class="label-wrapper"
                data-pivot="'.$row->pivot_id.'"
                data-url="'.route('supplier-product.update-label').'">

                <span class="label-text">
                    '.$row->name.' '.$badge.' 
                    <small class="text-muted">'.($row->label ?? '').'</small>

                    <button class="btn btn-sm btn-dark ms-1 btn-edit-label">
                        <i class="ti ti-pencil"></i>
                    </button>
                </span>

                <span class="label-edit d-none">
                    <input type="text" class="form-control form-control-sm label-input"
                        value="'.($row->label ?? '').'" 
                        style="width:120px;display:inline-block">

                    <button class="btn btn-sm btn-success btn-save-label">
                        <i class="ti ti-check"></i>
                    </button>

                    <button class="btn btn-sm btn-danger btn-cancel-label">
                        <i class="ti ti-x"></i>
                    </button>
                </span>
            </div>
            ';
        })
        ->addColumn('aksi', function ($row) use ($supplier) {
            return '
                <button 
                    class="btn btn-icon btn-sm btn-dark btn-duplicate"
                    data-url="'.route('suppliers.duplicateProduct', [$supplier->id, $row->id]).'"
                >
                    <i class="ti ti-copy"></i>
                </button>
                <button 
                        data-pivot="'.$row->pivot_id.'"
                        data-url="' . route('supplier-product.destroy', $row->pivot_id) . '" 
                        class="btn btn-icon btn-sm btn-dark btn-delete">
                        <i class="ti ti-trash"></i>
                </button>
            ';
        })
        ->editColumn('selling_prices', function ($row) use ($supplier) {

            return '
            <div class="price-wrapper"
                data-product="'.$row->id.'"
                data-supplier="'.$supplier->id.'"
                data-pivot="'.$row->pivot_id.'"
                data-url="'.route('supplier-product.update-price').'">

                <span class="price-text">
                    <span class="price-label"
                        data-price="'.$row->selling_prices.'">
                        Rp '.number_format($row->selling_prices, 4, ',', '.').'
                    </span>
                    <button class="btn btn-sm btn-dark ms-1 btn-edit-price">
                        <i class="ti ti-pencil"></i>
                    </button>
                </span>

                <span class="price-edit d-none">
                    <input type="text" class="form-control form-control-sm price-input"
                        value="'.number_format($row->selling_prices, 4, ',', '.').'" style="width:120px;display:inline-block">

                    <button class="btn btn-sm btn-success btn-save-price">
                        <i class="ti ti-check"></i>
                    </button>

                    <button class="btn btn-sm btn-danger btn-cancel-price">
                        <i class="ti ti-x"></i>
                    </button>
                </span>
            </div>
            ';
        })
        ->rawColumns(['aksi', 'selling_prices', 'name_with_label'])
        ->make(true);
}

    public function duplicateProduct($supplierId, $productId)
{
    DB::transaction(function () use ($supplierId, $productId) {

        $supplier = Supplier::findOrFail($supplierId);

        $product = $supplier->products()
            ->where('product_id', $productId)
            ->firstOrFail();
        $existingLabels = DB::table('product_supplier')
            ->where('product_id', $productId)
            ->where('supplier_id', $supplierId)
            ->pluck('label')
            ->filter()
            ->toArray();

        // 🔥 cari angka terakhir
        $max = 0;

        foreach ($existingLabels as $label) {
            if (preg_match('/copy-(\d+)/', $label, $match)) {
                $num = (int) $match[1];
                if ($num > $max) {
                    $max = $num;
                }
            }
        }

        $nextNumber = $max + 1;
        $newLabel = 'copy-' . $nextNumber;
        // ambil semua pivot field
        $pivotData = [
            'buying_prices'  => $product->pivot->buying_prices,
            'selling_prices' => $product->pivot->selling_prices,
            'special_prices' => $product->pivot->special_prices,
            'stock'          => $product->pivot->stock,
            'label'          => $newLabel,
        ];

        // insert ulang ke pivot
        $supplier->products()->attach($productId, $pivotData);
    });

    return response()->json([
        'success' => true,
        'message' => 'Produk berhasil diduplikat',
        'highlight' => true
    ]);
}
// public function duplicateProduct($supplierId, $productId)
// {
//     DB::transaction(function () use ($supplierId, $productId) {

//         $supplier = Supplier::findOrFail($supplierId);

//         $product = Product::findOrFail($productId);

//         // 🔥 1. Clone product
//         $newProduct = $product->replicate();
//         $newProduct->name = $product->name . ' - copy';
//         $newProduct->sku_code = $product->sku_code . '-copy';
//         $newProduct->save();

//         // 🔥 2. ambil pivot lama
//         $pivot = $supplier->products()
//             ->where('product_id', $productId)
//             ->firstOrFail()
//             ->pivot;

//         // 🔥 3. attach ke supplier
//         $supplier->products()->attach($newProduct->id, [
//             'buying_prices'  => $pivot->buying_prices,
//             'selling_prices' => $pivot->selling_prices,
//             'special_prices' => $pivot->special_prices,
//             'stock'          => $pivot->stock,
//         ]);
//     });

//     return response()->json([
//         'success' => true,
//         'message' => 'Produk berhasil diduplikat',
//         'highlight' => true // 🔥 tanda untuk frontend
//     ]);
// }
}
