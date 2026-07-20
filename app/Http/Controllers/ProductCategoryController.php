<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product_category = ProductCategory::all();
        return view('categories.index', compact('product_category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('categories.create');
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

        ProductCategory::create([
            'name' => $request->name,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('product_categories.index')
            ->with('success', 'Piece berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Piece $piece)
    {
        return view('pieces.show', compact('piece'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $product_category)
    {
        return view('categories.edit', compact('product_category'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $product_category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $product_category->update([
            'name' => $request->name,
            'is_active' => $request->is_active,

        ]);

        return redirect()->route('product_categories.index')
            ->with('success', 'Piece berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $product_category)
    {
        $product_category->delete();

        return redirect()->route('product_categories.index')
            ->with('success', 'Piece berhasil dihapus.');

    }
}
