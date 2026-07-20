<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\EquipmentCost;

class EquipmentCostSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('seeders/files/alat.xlsx');
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('PERALATAN'); // sesuaikan nama sheet

        $rows = $sheet->toArray();

        foreach ($rows as $i => $row) {

            // skip header
            if ($i === 0) continue;

            if (!preg_match('/^[A-Z]\.\d+$/', trim($row[2] ?? ''))) {
                continue;
            }

            EquipmentCost::create([
                'code'            => trim($row[2]), // KODE
                'description'     => trim($row[3]), // URAIAN
                'unit'            => trim($row[4]), // SATUAN
                'base_unit_price' => $this->cleanPrice($row[5]), // HARGA
                'notes' => trim($row[6] ?? '-'),
            ]);
        }
    }

    private function cleanPrice($value)
    {
        if (is_numeric($value)) {
            return intval($value);
        }

        $value = (string) $value;

        // hapus karakter aneh & spasi
        $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);

        // Rp103.200 → 103200
        $value = preg_replace('/[^0-9]/', '', $value);

        return (int) $value;
    }
}
