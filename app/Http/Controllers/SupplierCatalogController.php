<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\RabRecalculator;

class SupplierCatalogController extends Controller
{
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

            'default_selling_prices' => $product->default_selling_prices ?? 0,
            'default_discount'      => $product->default_discount ?? 0,
            'tax_percentage'        => $product->tax_percentage ?? 0,

            'photo_url' => $product->photo 
                ? asset('storage/' . $product->photo) 
                : asset('images/logo-putih.png'),
        ]);
    }

    public function storeSupplierProduct(Request $request)
    {
        $request->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'product_id'     => 'required|exists:products,id',
            'stock'          => 'required|numeric',
            'selling_prices'  => 'required|numeric',
            'tax_percentage' => 'nullable|numeric',
            'discount'       => 'nullable|numeric',
        ]);

        $supplier = Supplier::findOrFail($request->supplier_id);

        $supplier->products()->attach($request->product_id, [
            'stock'           => $request->stock,
            'selling_prices'   => $request->selling_prices,
            'tax_percentage'  => $request->tax_percentage ?? 0,
            'discount'        => $request->discount ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk supplier berhasil ditambahkan'
        ]);
    }

    public function destroy($pivotId)
{
    DB::table('product_supplier')
        ->where('id', $pivotId)
        ->delete();

    return response()->json([
        'success' => true,
        'message' => 'Produk supplier berhasil dihapus'
    ]);
}

public function restore($id)
{
    DB::table('product_supplier')
        ->where('id', $id)
        ->update(['deleted_at' => null]);

    return response()->json(['success' => true]);
}
public function updatePrice(Request $request)
{
    $request->validate([
        'pivot_id' => 'required',
        'price'    => 'required|numeric|min:0',
    ]);

    DB::transaction(function () use ($request) {

        $pivot = DB::table('product_supplier')
            ->where('id', $request->pivot_id)
            ->first();

        // safety check (optional tapi bagus)
        if (!$pivot) {
            abort(404, 'Pivot tidak ditemukan');
        }

        DB::table('product_supplier')
            ->where('id', $request->pivot_id)
            ->update([
                'selling_prices' => $request->price,
                'updated_at'     => now()
            ]);

        // RabRecalculator::recalcByProduct($pivot->product_id);
        RabRecalculator::recalcByPivot($request->pivot_id);

        Cache::put('job_category_last_updated', now()->timestamp);
    });

    return response()->json([
        'success' => true,
        'price'   => $request->price
    ]);
}
public function updateLabel(Request $request)
{
    $request->validate([
        'pivot_id' => 'required',
        'label'    => 'nullable|string|max:50',
    ]);

    DB::table('product_supplier')
        ->where('id', $request->pivot_id)
        ->update([
            'label' => $request->label,
            'updated_at' => now()
        ]);

    return response()->json([
        'success' => true,
        'label'   => $request->label
    ]);
}
}
