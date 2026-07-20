<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\JobCategory;
use App\Models\JobCategoryItem;
use Illuminate\Support\Facades\DB;

class UpahJobCategorySeeder extends Seeder
{
    public function run(): void
    {
        $filePath = storage_path('app/upah.xlsx');

        if (!file_exists($filePath)) {
            throw new \Exception("File Excel tidak ditemukan: {$filePath}");
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('ANALISA');
        $rows  = $sheet->toArray();

        $currentJob = null;
        $currentCategory = null;

        DB::transaction(function () use ($rows, &$currentJob, &$currentCategory) {

            foreach ($rows as $row) {

                $col0 = trim($row[0] ?? '');
                $col1 = trim($row[1] ?? '');

                // =============================
                // HEADER JOB CATEGORY
                // =============================
                if ($col0 && preg_match('/^\(K\d+\)/', $col0)) {
                    $currentJob = JobCategory::where('name', $col0)->first();
                    $currentCategory = null;
                    continue;
                }

                if (!$currentJob) continue;

                // =============================
                // GROUP A / B / C
                // =============================
                if (str_contains($col0, 'TENAGA')) {
                    $currentCategory = 'labor';
                    continue;
                }

                if (str_contains($col0, 'BAHAN')) {
                    $currentCategory = 'product';
                    continue;
                }

                if (str_contains($col0, 'PERALATAN')) {
                    $currentCategory = 'equipment';
                    continue;
                }

                // =============================
                // INSERT ITEM
                // =============================
                if (
                    $currentCategory &&
                    is_numeric($row[4] ?? null) &&
                    is_numeric($row[6] ?? null) &&
                    $col1
                ) {
                    JobCategoryItem::create([
                        'job_category_id' => $currentJob->id,
                        'category'        => $currentCategory,
                        'name'            => $col1,
                        'code'            => trim($row[2] ?? ''),
                        'unit'            => trim($row[3] ?? ''),
                        'coefisien'       => (float) $row[4],
                        'base_price'      => (float) $row[5],
                        'total_price'     => (float) $row[6],
                    ]);
                }

                // =============================
                // SUBTOTAL
                // =============================
                if (str_contains($col1, 'JUMLAH TENAGA')) {
                    $currentJob->update(['subtotal_labor' => (float)$row[6]]);
                }

                if (str_contains($col1, 'JUMLAH BAHAN')) {
                    $currentJob->update(['subtotal_material' => (float)$row[6]]);
                }

                if (str_contains($col1, 'JUMLAH ALAT')) {
                    $currentJob->update(['subtotal_equipment' => (float)$row[6]]);
                }

                // =============================
                // OVERHEAD & PROFIT
                // =============================
                if (str_contains($col1, 'Overhead')) {
                    $currentJob->update([
                        'overhead_percent' => ((float)$row[4]) * 100
                    ]);
                }

                // =============================
                // GRAND TOTAL
                // =============================
                if (str_contains($col1, 'Harga Satuan Pekerjaan')) {
                    $currentJob->update([
                        'grand_total' => (float)$row[6]
                    ]);
                }
            }
        });

        $this->command->info('✔ Upah Job Category berhasil diimport dari Excel');
    }
}
