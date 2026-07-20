<?php

namespace App\Http\Controllers;

use App\Models\ProductColor;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product_colors = ProductColor::all();
        return view('colors.index', compact('product_colors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('colors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'factory_origin' => 'nullable|string|max:255',
        ]);

        ProductColor::create([
            'name' => $request->name,
            'factory_origin' => $request->factory_origin,
        ]);

        return redirect()->route('product_colors.index')
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
    public function edit(ProductColor $product_color)
    {
        return view('colors.edit', compact('product_color'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductColor $product_color)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'factory_origin' => 'nullable|string|max:255',
        ]);

        $product_color->update([
            'name' => $request->name,
            'factory_origin' => $request->factory_origin,

        ]);

        return redirect()->route('product_colors.index')
            ->with('success', 'Piece berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductColor $product_color)
    {
        $product_color->delete();

        return redirect()->route('product_colors.index')
            ->with('success', 'Piece berhasil dihapus.');

    }
}
