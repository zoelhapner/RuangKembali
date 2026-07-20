<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        $sheet = IOFactory::load(
            storage_path('app/upah.xlsx')
        )->getSheetByName('MATERIAL');

        $highestRow = $sheet->getHighestRow();

        // SUPPLIER DEFAULT
        $supplierId = Supplier::where('name', 'Kebon Jaya')->value('id');

        if (!$supplierId) {
            throw new \Exception('Supplier default belum ada');
        }

        for ($row = 2; $row <= $highestRow; $row++) {

            $kode  = trim((string) $sheet->getCell("C{$row}")->getValue());
            $harga = $sheet->getCell("F{$row}")->getValue();

            // skip kosong
            if ($kode === '' || $harga === null) {
                continue;
            }

            // skip baris kategori (A, B, dst)
            if (strlen($kode) === 1) {
                continue;
            }

            $product = Product::where('sku_code', $kode)->first();

            // kalau product belum ada → skip (AMAN)
            if (!$product) {
                continue;
            }

            $product->suppliers()->syncWithoutDetaching([
                $supplierId => [
                    'selling_prices' => (float) $harga,
                    'updated_at'     => now(),
                    'created_at'     => now(),
                ]
            ]);
        }
    }
}
