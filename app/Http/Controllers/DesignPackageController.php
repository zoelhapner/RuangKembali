<?php

namespace App\Http\Controllers;

use App\Models\DesignPackage;
use App\Models\DesignPackageItem;
use Illuminate\Http\Request;

class DesignPackageController extends Controller
{

public function index()
{
    $packages = DesignPackage::withCount('items')
        ->orderByRaw("SUBSTRING(name FROM 'Paket ([A-Z])') ASC")
        ->get();

    return view('design-packages.index', compact('packages'));
}


    /**
     * Form create
     */
    public function create()
    {
        return view('design-packages.create');
    }

    /**
     * Store paket baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price_meter' => 'required|integer|min:0',
        ]);

        $package = DesignPackage::create($data);

        return redirect()
            ->route('design-packages.edit', $package->id)
            ->with('success', 'Paket berhasil dibuat. Silahkan tambahkan item rincian.');
    }

    /**
     * Edit paket + item-itemnya
     */
    public function edit(DesignPackage $designPackage)
    {
        $designPackage->load('items');
        return view('design-packages.edit', compact('designPackage'));
    }

    /**
     * Update paket
     */
    public function update(Request $request, DesignPackage $designPackage)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price_meter' => 'required|integer|min:0',
        ]);

        $designPackage->update($data);

        return back()->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Hapus paket beserta item-nya
     */
    public function destroy(DesignPackage $designPackage)
    {
        $designPackage->delete();

        return redirect()
            ->route('design-packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    /**
     * Tambah item pekerjaan
     */
    public function addItem(Request $request, DesignPackage $designPackage)
    {
        $data = $request->validate([
            'category' => 'nullable|string|max:255',
            'item_name' => 'required|string|max:255',
            'is_optional' => 'nullable'
        ]);

        $data['is_optional'] = $request->has('is_optional');
        $data['design_package_id'] = $designPackage->id;

        DesignPackageItem::create($data);

        return back()->with('success', 'Item berhasil ditambahkan.');
    }

    /**
     * Update item pekerjaan
     */
    public function updateItem(Request $request, DesignPackageItem $item)
    {
        $data = $request->validate([
            'category' => 'nullable|string|max:255',
            'item_name' => 'required|string|max:255',
            'is_optional' => 'nullable'
        ]);

        $data['is_optional'] = $request->has('is_optional');

        $item->update($data);

        return back()->with('success', 'Item berhasil diperbarui.');
    }

    /**
     * Hapus item pekerjaan
     */
    public function deleteItem(DesignPackageItem $item)
    {
        $item->delete();

        return back()->with('success', 'Item berhasil dihapus.');
    }

    /**
     * API: Ambil paket berikut item-itemnya → untuk autofill form penawaran
     */
    public function getPackage($id)
    {
        $package = DesignPackage::with('items')->findOrFail($id);
        return response()->json($package);
    }
}
