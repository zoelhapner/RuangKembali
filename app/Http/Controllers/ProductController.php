<?php
   
namespace App\Http\Controllers;

use App\Models\ProductColor;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductType;
use App\Models\Supplier;
use App\Services\SkuService;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

 
class ProductController extends Controller
{
    
   public function index(Request $request)
{
    $auth = auth()->user();

    $query = Product::with([
        'brand',
        'colors',
        'category',
        'type',
    ]);

    // Jika ada hak akses untuk membatasi data
    if ($auth->can('lihat data produk') && !$auth->can('lihat daftar produk')) {
        $query->where('user_id', $auth->id);
    }

    if ($request->ajax()) {
        $products = $query->get();

        $statusLabel = [
            1 => 'Tersedia',
            2 => 'Stok Terbatas',
            3 => 'Habis',
            4 => 'Pre-Order'
        ];

        return DataTables::of($products)

            ->addColumn('brand', fn($row) => $row->brand->name ?? '-')
            ->addColumn('colors', function ($row) {
                return $row->colors->count()
                    ? $row->colors->pluck('name')->implode(', ')
                    : '-';
            })


            ->addColumn('category', fn($row) => $row->category->name ?? '-')

            ->addColumn('type', fn($row) => $row->type->name ?? '-')
            ->addColumn('sku_code', fn($row) => $row->sku_code ?? '-')

            ->addColumn('status', function ($row) use ($statusLabel) {

                $label = $statusLabel[$row->status] ?? 'Tidak Diketahui';

                $color = match ($row->status) {
                    1 => 'success',
                    2 => 'warning',
                    3 => 'danger',
                    4 => 'info',
                    default => 'secondary'
                };

                return '<span class="badge bg-' . $color . '">' . $label . '</span>';
            })

            // Foto
            ->addColumn('photo', function ($row) {
                if (!$row->photo) {
                    return '<span class="text-muted">Tidak ada foto</span>';
                }
                $url = asset('storage/' . $row->photo);
                return '<img src="' . $url . '" width="80" 
                        style="border-radius:5px; border:1px solid #ccc;">';
            })

            // Tombol Aksi
            ->addColumn('action', function ($product) {
                $buttons = '';

                if (auth()->user()->can('ubah data produk')) {
                    $buttons .= '<a href="' . route('products.edit', $product->id) . '" 
                                class="btn btn-icon btn-sm btn-dark me-1">
                                <i class="ti ti-edit"></i></a>';
                }

                if (auth()->user()->can('hapus data produk')) {
                    $buttons .= '<button data-id="' . $product->id . '" 
                                class="btn btn-icon btn-sm btn-dark delete-products">
                                <i class="ti ti-trash"></i></button>';
                }

                return $buttons;
            })

            ->rawColumns(['photo', 'action', 'status'])
            ->make(true);
    }

    return view('products.index');
}


    public function create()
{
    return view('products.create', [
        'product' => new Product(),
        'colors' => ProductColor::all(),
        'brands' => ProductBrand::all(),
        'categories' => ProductCategory::all(),
        'types' => ProductType::all(),
        'suppliers' => Supplier::all(),
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'sku_code'        => 'nullable|string|max:255|unique:products,sku_code',
        'name'            => 'required|string|max:255',
        'photo'           => 'nullable|image|max:2048',
        'description'     => 'nullable|string|max:500',
        'colors'          => 'nullable|array',
        'colors.*'        => 'exists:colors,id',
        'brand_id'        => 'nullable|exists:product_brands,id',
        'category_id'     => 'nullable|exists:product_categories,id',
        'type_id'         => 'nullable|exists:product_types,id',
        'status'          => 'required|in:1,2,3,4',
        'unit_1_name'     => 'required|string|max:50',
        'unit_1_value'    => 'required|integer|min:1',
        'unit_2_name'     => 'nullable|string|max:50',
        'unit_2_value'    => 'nullable|integer|min:1',
        'unit_3_name'     => 'nullable|string|max:50',
        'unit_3_value'    => 'nullable|integer|min:1',
        'unit_4_name'     => 'nullable|string|max:50',
        'unit_4_value'    => 'nullable|integer|min:1',
        'volume'          => 'nullable|string|max:255',
        'size'            => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();

    try {
        // Upload photo jika ada
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('product_photos', 'public');
        }

        // Insert product
        $product = Product::create([
            'id'              => \Str::uuid(),

            'sku_code'        => $request->sku_code,
            'name'            => $request->name,
            'photo'           => $photoPath,
            'description'     => $request->description,
            'brand_id'        => $request->brand_id,
            'category_id'     => $request->category_id,
            'type_id'         => $request->type_id,

            // Unit berjenjang
            'unit_1_name'     => $request->unit_1_name,
            'unit_1_value'    => $request->unit_1_value,
            'unit_2_name'     => $request->unit_2_name,
            'unit_2_value'    => $request->unit_2_value,
            'unit_3_name'     => $request->unit_3_name,
            'unit_3_value'    => $request->unit_3_value,
            'unit_4_name'     => $request->unit_4_name,
            'unit_4_value'    => $request->unit_4_value,

            // Atribut produk
            'volume'          => $request->volume,
            'size'            => $request->size,

            'status'          => $request->status,

            // jika produk milik user
            'user_id'         => auth()->id(),
        ]);

        $product->colors()->sync($request->colors ?? []);

        DB::commit();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}

public function storeAjax(Request $request)
{
    $request->validate([
        'name'        => 'required',
        'sku_code'        => 'nullable|string|max:255|unique:products,sku_code',
        'photo'       => 'nullable|image|max:2048',
        'description'     => 'nullable|string|max:500',
        'status'        => 'required|in:1,2,3,4',
        'brand_id'        => 'nullable|exists:product_brands,id',
        'category_id'     => 'nullable|exists:product_categories,id',
        'type_id'         => 'nullable|exists:product_types,id',
        'colors'          => 'nullable|array',
        'colors.*'        => 'exists:colors,id',
    ]);

    $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('product_photos', 'public');
        }

    $product = Product::create([
        'name'              => $request->name,
        'photo'             => $photoPath,
        'description'       => $request->description,
        'sku_code'       => $request->sku_code,
        'brand_id'          => $request->brand_id,
        'category_id'       => $request->category_id,
        'type_id'           => $request->type_id,
        'status'          => $request->status,
        'size'      => $request->size,
        'volume'    => $request->volume,
        'unit_1_name'       => $request->unit_1_name,
        'unit_1_value'      => $request->unit_1_value,
        'unit_2_name'       => $request->unit_2_name,
        'unit_2_value'      => $request->unit_2_value,
        'unit_3_name'       => $request->unit_3_name,
        'unit_3_value'      => $request->unit_3_value,
        'unit_4_name'       => $request->unit_4_name,
        'unit_4_value'      => $request->unit_4_value,
    ]);

    $product->colors()->sync($request->colors ?? []);

    return response()->json([
        'success'    => true,
        'product_id' => $product->id
    ]);
}

public function generateSku(Request $request)
{
    // Ambil nama berdasarkan ID
    $categoryName = ProductCategory::find($request->category_id)->name ?? '';
    $brandName    = ProductBrand::find($request->brand_id)->name ?? '';
    $typeName     = ProductType::find($request->type_id)->name ?? '';

    // Kirim ke generator
    $sku = SkuService::generate(
        $categoryName,
        $brandName,
        $typeName,
        $request->ukuran,
        $request->volume,
        $request->colors ?? []
    );

    return response()->json(['sku' => $sku]);
}



public function show(Product $product)
{
    // Hitung profit
    $buy = $product->buying_prices ?? 0;
    $sell = $product->selling_prices ?? 0;

    $profit = $sell - $buy;

    $margin = $buy > 0 
                ? ($profit / $buy) * 100 
                : 0;

    return view('products.show', [
        'product' => $product,
        'profit' => $profit,
        'margin' => $margin,
    ]);
}


    public function edit($id)
{
    $product = Product::with([
        'brand',
        'colors',
        'category',
        'type',
        'suppliers'
    ])->findOrFail($id);
    
    $colors     = ProductColor::all();
    $brands     = ProductBrand::all();
    $categories = ProductCategory::all();
    $types      = ProductType::all();
    
    $productColors = $product->colors->pluck('id')->toArray();
    $hpp = $product->suppliers()
        ->orderBy('product_supplier.buying_prices', 'asc')
        ->first()?->pivot->buying_prices;

    return view('products.edit', compact(
        'product',
        'brands',
        'colors',
        'categories',
        'types',
        'productColors',
        'hpp'
    ));
}

    public function update(Request $request, Product $product)
{
    $request->validate([
        'sku_code'        => 'nullable|string|max:255|unique:products,sku_code,' . $product->id,
        'name'            => 'required|string|max:255',
        'photo'           => 'nullable|image|max:2048',
        'description'     => 'nullable|string|max:500',

        'colors'          => 'nullable|array',
        'colors.*'        => 'exists:colors,id',
        'brand_id'        => 'nullable|exists:product_brands,id',
        'category_id'     => 'nullable|exists:product_categories,id',
        'type_id'         => 'nullable|exists:product_types,id',

        'unit_1_name'     => 'required|string|max:50',
        'unit_1_value'    => 'required|integer|min:1',
        'unit_2_name'     => 'nullable|string|max:50',
        'unit_2_value'    => 'nullable|integer|min:1',
        'unit_3_name'     => 'nullable|string|max:50',
        'unit_3_value'    => 'nullable|integer|min:1',
        'unit_4_name'     => 'nullable|string|max:50',
        'unit_4_value'    => 'nullable|integer|min:1',

        
        'volume'          => 'nullable|string|max:255',
        'size'            => 'nullable|string|max:255',
        'status'          => 'required|in:1,2,3,4',
    ]);

    DB::beginTransaction();

    try {
        // Upload foto baru jika ada
        if ($request->hasFile('photo')) {
            
            // Hapus foto lama jika ada
            if ($product->photo && file_exists(storage_path('app/public/' . $product->photo))) {
                unlink(storage_path('app/public/' . $product->photo));
            }

            // Simpan foto baru
            $product->photo = $request->file('photo')->store('product_photos', 'public');
        }

        // Update product
        $product->update([
            'sku_code'        => $request->sku_code,
            'name'            => $request->name,
            'description'     => $request->description,
            'brand_id'        => $request->brand_id,
            'category_id'     => $request->category_id,
            'type_id'         => $request->type_id,

            // Units
            'unit_1_name'     => $request->unit_1_name,
            'unit_1_value'    => $request->unit_1_value,
            'unit_2_name'     => $request->unit_2_name,
            'unit_2_value'    => $request->unit_2_value,
            'unit_3_name'     => $request->unit_3_name,
            'unit_3_value'    => $request->unit_3_value,
            'unit_4_name'     => $request->unit_4_name,
            'unit_4_value'    => $request->unit_4_value,

            // Attributes
            'volume'          => $request->volume,
            'size'            => $request->size,

            // Prices
            // 'buying_prices'   => $request->buying_prices,
            // 'selling_prices'  => $request->selling_prices,
            // 'special_prices'  => $request->special_prices,
            // 'tax_percentage'  => $request->tax_percentage,

            'status'          => $request->status,
        ]);

        // Sync suppliers pivot
        $product->colors()->sync($request->colors ?? []);

        DB::commit();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');

    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}


    public function destroy(Product $product) 
    {
    
        if ($product) {
            $product->delete();
            return response()->json(['status' => 'success', 'message' => 'product deleted successfully']);
        }

        return response()->json(['status' => 'failed', 'message' => 'Unable to delete']);
    }


}