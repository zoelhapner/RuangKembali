<?php

namespace App\Http\Controllers;

use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eventcategory = EventCategory::all();
        return view('categories.index', compact('eventcategory'));
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

        EventCategory::create([
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
    public function edit(EventCategory $event_category)
    {
        return view('categories.edit', compact('event_category'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventCategory $product_category)
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
    public function destroy(EventCategory $product_category)
    {
        $product_category->delete();

        return redirect()->route('product_categories.index')
            ->with('success', 'Piece berhasil dihapus.');

    }
}
