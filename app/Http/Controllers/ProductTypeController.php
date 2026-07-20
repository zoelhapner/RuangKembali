<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product_types = ProductType::all();
        return view('types.index', compact('product_types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        ProductType::create([
            'name' => $request->name,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('product_types.index')
            ->with('success', 'product_type berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(product_type $product_type)
    {
        return view('product_types.show', compact('product_type'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductType $product_type)
    {
        return view('types.edit', compact('product_type'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductType $product_type)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $product_type->update([
            'name' => $request->name,
            'is_active' => $request->is_active,

        ]);

        return redirect()->route('product_types.index')
            ->with('success', 'product_type berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductType $product_type)
    {
        $product_type->delete();

        return redirect()->route('product_types.index')
            ->with('success', 'product_type berhasil dihapus.');

    }
}
