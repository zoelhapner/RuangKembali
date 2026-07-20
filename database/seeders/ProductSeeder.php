<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $sheet = IOFactory::load(
            storage_path('app/upah.xlsx')
        )->getSheetByName('MATERIAL');

        $highestRow = $sheet->getHighestRow();
        $currentCategoryId = null;

        for ($row = 2; $row <= $highestRow; $row++) {

            $kodeCell = $sheet->getCell("C{$row}");
            $namaCell = $sheet->getCell("D{$row}");
            $satuanCell = $sheet->getCell("E{$row}");

            $kode   = trim((string) $kodeCell->getValue());
            $nama   = trim((string) $namaCell->getValue());
            $satuan = trim((string) $satuanCell->getValue());

            if ($nama === '') continue;

            // ======================
            // DETEKSI WARNA MERAH
            // ======================
            $fontColor = strtoupper(
                (string) $namaCell->getStyle()->getFont()->getColor()->getARGB()
            );

            $fillColor = strtoupper(
                (string) $namaCell->getStyle()->getFill()->getStartColor()->getARGB()
            );

            $isRed =
                ($fontColor && $fontColor !== 'FF000000') ||
                ($fillColor && $fillColor !== 'FFFFFFFF');

            // ======================
            // HEADER KATEGORI
            // ======================
            $isHeader =
                $isRed &&
                empty($kode) &&
                strlen($nama) > 5;

            if ($isHeader) {

                $category = ProductCategory::firstOrCreate(
                    ['name' => $nama],
                    ['is_active' => true]
                );

                $currentCategoryId = $category->id;

                $this->command->warn("🧱 KATEGORI: {$nama}");
                continue;
            }

            // ======================
            // ITEM PRODUK
            // ======================
            if (strlen($kode) === 1) continue;

            Product::updateOrCreate(
                ['sku_code' => $kode],
                [
                    'id'           => Str::uuid(),
                    'name'         => $nama,
                    'category_id'  => $currentCategoryId,
                    'unit_1_name'  => strtoupper($satuan ?: 'PCS'),
                    'unit_1_value' => 1,
                    'status'       => 1,
                ]
            );
        }
    }
}
