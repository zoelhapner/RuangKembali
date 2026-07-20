<?php

namespace App\Http\Controllers;

use App\Models\RabPackage;
use App\Models\RabPackageItem;
use Illuminate\Http\Request;

class RabPackageController extends Controller
{
    /**
     * List semua paket
     */
    public function index()
    {
        $packages = RabPackage::withCount('items')->get();
        return view('rab-packages.index', compact('packages'));
    }

    /**
     * Form create
     */
    public function create()
    {
        return view('rab-packages.create');
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

        $package = RabPackage::create($data);

        return redirect()
            ->route('rab-packages.edit', $package->id)
            ->with('success', 'Paket berhasil dibuat. Silahkan tambahkan item rincian.');
    }

    /**
     * Edit paket + item-itemnya
     */
    public function edit(RabPackage $rabPackage)
    {
        $rabPackage->load('items');
        return view('rab-packages.edit', compact('rabPackage'));
    }

    /**
     * Update paket
     */
    public function update(Request $request, RabPackage $rabPackage)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price_meter' => 'required|integer|min:0',
        ]);

        $rabPackage->update($data);

        return back()->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Hapus paket beserta item-nya
     */
    public function destroy(RabPackage $rabPackage)
    {
        $rabPackage->delete();

        return redirect()
            ->route('rab-packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    /**
     * Tambah item pekerjaan
     */
    public function addItem(Request $request, RabPackage $rabPackage)
    {
        $data = $request->validate([
            'category' => 'nullable|string|max:255',
            'item_name' => 'required|string|max:255',
            'is_optional' => 'nullable'
        ]);

        $data['is_optional'] = $request->has('is_optional');
        $data['rab_package_id'] = $rabPackage->id;

        RabPackageItem::create($data);

        return back()->with('success', 'Item berhasil ditambahkan.');
    }

    /**
     * Update item pekerjaan
     */
    public function updateItem(Request $request, RabPackageItem $item)
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
    public function deleteItem(RabPackageItem $item)
    {
        $item->delete();

        return back()->with('success', 'Item berhasil dihapus.');
    }

    /**
     * API: Ambil paket berikut item-itemnya → untuk autofill form penawaran
     */
    public function getPackage($id)
    {
        $package = RabPackage::with('items')->findOrFail($id);
        return response()->json($package);
    }
}
