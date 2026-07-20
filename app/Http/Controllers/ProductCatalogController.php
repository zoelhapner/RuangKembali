<?php

namespace App\Http\Controllers;

use App\ViewModels\CatalogItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductSupplier;
use App\Models\ProductCategory;
use App\Models\ProductBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductCatalogController extends Controller
{
    /**
     * AJAX Search produk
     */
    public function searchProduct(Request $request)
    {
        $keyword = $request->keyword;

        $products = Product::where('name', 'ILIKE', "%{$keyword}%") 
        ->limit(10)
        ->get();

        return response()->json([
            'found' => $products->count() > 0,
            'html'  => view('suppliers.catalog.search-results', compact('products'))->render()
        ]);
    }


    /**
     * AJAX Detail produk
     */
    public function productDetail($id)
    {
        $product = Product::with(['brand', 'category', 'type', 'colors'])->findOrFail($id);

        return response()->json([
            'id'                 => $product->id,
            'name'               => $product->name,
            'sku_code'             => $product->sku_code,
            'description'        => $product->description,
            'size'               => $product->size,
            'volume'             => $product->volume,
            'status'             => $product->status,
            'brand_id'           => $product->brand_id,
            'category_id'        => $product->category_id,
            'type_id'            => $product->type_id,
            'colors'           => $product->colors,
            'brand_name'         => $product->brand->name ?? '',
            'category_name'      => $product->category->name ?? '',
            'type_name'          => $product->type->name ?? '',
            'unit_1_name'        => $product->unit_1_name,
            'unit_1_value'       => $product->unit_1_value,
            'unit_2_name'        => $product->unit_2_name,
            'unit_2_value'       => $product->unit_2_value,
            'unit_3_name'        => $product->unit_3_name,
            'unit_3_value'       => $product->unit_3_value,
            'unit_4_name'        => $product->unit_4_name,
            'unit_4_value'       => $product->unit_4_value,

            'default_buying_prices' => $product->default_buying_prices ?? 0,
            'default_discount'      => $product->default_discount ?? 0,
            'tax_percentage'        => $product->tax_percentage ?? 0,

            'photo_url' => $product->photo 
                ? asset('storage/' . $product->photo) 
                : asset('images/logo-putih.png'),
        ]);
    }


    /**
     * Simpan produk ke supplier (pivot)
     */
    public function storeSupplierProduct(Request $request)
    {
        $request->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'product_id'     => 'required|exists:products,id',
            'stock'          => 'required|numeric',
            'buying_prices'  => 'required|numeric',
            'tax_percentage' => 'nullable|numeric',
            'discount'       => 'nullable|numeric',
        ]);

        $supplier = Supplier::findOrFail($request->supplier_id);

        $supplier->products()->attach($request->product_id, [
            'stock'           => $request->stock,
            'buying_prices'   => $request->buying_prices,
            'tax_percentage'  => $request->tax_percentage ?? 0,
            'discount'        => $request->discount ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk supplier berhasil ditambahkan'
        ]);
    }

public function updatePrice(Request $request)
{
    $request->validate([
        'supplier_id' => 'required',
        'product_id'  => 'required',
        'price'       => 'required|numeric|min:0',
    ]);

    DB::table('product_supplier')
        ->where('supplier_id', $request->supplier_id)
        ->where('product_id', $request->product_id)
        ->update([
            'buying_prices' => $request->price,
            'updated_at'    => now()
        ]);

    return response()->json([
        'success' => true,
        'price'   => number_format($request->price)
    ]);
}

public function supplierCatalog(Request $request)
    {
        $query = ProductSupplier::with([
            'product.category',
            'product.brand',
            'product.type',
            'supplier',
            'product',
        ]);

        // FILTERS
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->category_id) {
            $query->whereHas('product', fn($q) =>
                $q->where('category_id', $request->category_id)
            );
        }

        if ($request->brand_id) {
            $query->whereHas('product', fn($q) =>
                $q->where('brand_id', $request->brand_id)
            );
        }

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('product', fn($q) =>
                $q->where('name', 'ilike', "%$search%")
                  ->orWhere('sku_code', 'ilike', "%$search%")
            );
        }

        $items = $query->paginate(20);

        // Data filter
        $suppliers = Supplier::all();
        $categories = ProductCategory::all();
        $brands = ProductBrand::all();

        return view('products.catalog.supplier-index', compact(
            'items', 'suppliers', 'categories', 'brands'
        ));
    }

    public function customerCatalog()
{
    // Produk gudang (priority)
    $warehouseProducts = Product::with([
            'category','brand','type','warehouseStocks','price'
        ])
        ->whereHas('warehouseStocks', fn($q) => $q->where('stock', '>', 0))
        ->get()
        ->map(function ($p) {
            $warehouseStock = optional($p->warehouseStocks->first());

            return new CatalogItem([
                'source'         => 'warehouse',
                'id'             => $p->id,
                'name'           => $p->name,
                'sku'            => $p->sku_code,
                'photo'          => $p->photo,
                'category'       => $p->category->name ?? '-',
                'brand'          => $p->brand->name ?? '-',
                'price'          => $p->price->buying_prices ?? $p->price->price,
                'original_price' => $p->price->price ?? null,
                'stock'          => $warehouseStock->stock ?? 0,
                'supplier'       => null,
                // optionally keep model for further need:
                'product_model'  => $p,
            ]);
        });

    // Produk fallback dari supplier
    $supplierProducts = ProductSupplier::with([
            'product.category','product.brand','product.type','supplier'
        ])
        ->whereDoesntHave('product.warehouseStocks')
        ->get()
        ->map(function ($sp) {
            $prod = $sp->product;
            return new CatalogItem([
                'source'         => 'supplier',
                'id'             => $prod->id,
                'name'           => $prod->name,
                'sku'            => $prod->sku_code,
                'photo'          => $prod->photo,
                'category'       => $prod->category->name ?? '-',
                'brand'          => $prod->brand->name ?? '-',
                'price'          => $sp->buying_prices,
                'original_price' => null,
                'stock'          => $sp->stock ?? 0,
                // 'supplier'       => $sp->supplier->name ?? '-',
                'product_model'  => $prod,
            ]);
        });

    // Merge dan kembalikan
    $items = $warehouseProducts->merge($supplierProducts)->sortBy('name')->values();

    return view('products.catalog.customer-index', compact('items'));
}


}
