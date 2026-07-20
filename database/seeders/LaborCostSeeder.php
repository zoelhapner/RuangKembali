<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\LaborCost;
use Illuminate\Support\Facades\DB;

class LaborCostSeeder extends Seeder
{
public function run()
{
    $file = storage_path('app/upah.xlsx');
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getSheetByName('UPAH');

    $rows = $sheet->toArray();

    foreach ($rows as $i => $row) {

        if (empty($row[1])) continue;

        LaborCost::create([
            'code'              => trim($row[2]), // KODE
            'description'       => trim($row[3]), // URAIAN
            'unit'              => trim($row[4]), // SATUAN (index 4!!)
            'base_unit_price'   => $this->cleanPrice($row[5]), // HARGA
            'notes'             => trim($row[6]), // KETERANGAN
        ]);
    }
}

private function cleanPrice($value)
{
    // Convert numeric raw 93.0 to string "93.0"
    $value = (string)$value;

    // remove non visible characters
    $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);

    // remove all except digits
    $value = preg_replace('/[^0-9]/', '', $value);

    return intval($value);
}



}
