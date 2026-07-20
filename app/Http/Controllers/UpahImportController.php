<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobCategory;
use App\Models\JobCategoryItem;

class UpahImportController extends Controller
{
    public function importUpah(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = fopen($request->file('file')->getPathname(), 'r');
        if (!$file) abort(500, 'File gagal dibuka');

        // detect delimiter
        $firstLine = fgets($file);
        rewind($file);

        $delimiter = match (true) {
            substr_count($firstLine, "\t") > 3 => "\t",
            substr_count($firstLine, ';') > 3 => ';',
            substr_count($firstLine, ',') > 3 => ',',
            default => ';'
        };

        $now = now();
        $currentJobId = null;
        $currentCategory = null;
        $itemsBuffer = [];

        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {

            // trim + clean BOM
            $row = array_map(fn($v) =>
                is_string($v)
                    ? trim(preg_replace('/^\xEF\xBB\xBF|\x{FEFF}/u', '', $v))
                    : $v
            , $row);

            /**
             * ===============================
             * 1. DETEKSI JUDUL PEKERJAAN (K1)
             * ===============================
             */
            if (
                !empty($row[1]) &&
                empty($row[5]) &&
                empty($row[6]) &&
                strlen($row[1]) > 5
            ) {
                // simpan job sebelumnya
                if ($currentJobId && $itemsBuffer) {
                    JobCategoryItem::insert($itemsBuffer);

                    $job = JobCategory::find($currentJobId);
                    if ($job) {
                        $this->recalcJobCategory($job); // 🔥 INI PENTING
                    }
                }


                $jobName = trim($row[1]);

                $currentJobId = JobCategory::where(
                    'nama_pekerjaan',
                    'ILIKE',
                    "%{$jobName}%"
                )->value('id');

                if (!$currentJobId) {
                    logger()->warning('JOB NOT FOUND', [$jobName]);
                    $currentJobId = null;
                    continue;
                }

                JobCategoryItem::where('job_category_id', $currentJobId)->delete();

                $itemsBuffer = [];
                $currentCategory = null;
                continue;
            }



            if (!$currentJobId) continue;

            /**
             * ===============================
             * 2. DETEKSI GROUP A / B / C
             * ===============================
             * (DARI KOLOM KODE, BUKAN URAIAN)
             */
            $groupCell = strtoupper(trim($row[1] ?? ''));

            if (preg_match('/^A(\.|$)/', $groupCell)) {
                $currentCategory = 'labor';
                continue;
            }

            if (preg_match('/^B(\.|$)/', $groupCell)) {
                $currentCategory = 'product';
                continue;
            }

            if (preg_match('/^C(\.|$)/', $groupCell)) {
                $currentCategory = 'equipment';
                continue;
            }

            if (!$currentCategory) continue;

            /**
             * ===============================
             * 3. VALIDASI ITEM DETAIL
             * ===============================
             */
            if (
                empty($row[2]) ||
                empty($row[3]) ||
                !isset($row[5], $row[6])
            ) continue;

            $coef = (float) str_replace(',', '.', $row[5]);
            $price = (float) str_replace(['Rp', '.', ','], ['', '', '.'], $row[6]);

            if ($coef <= 0 || $price <= 0) continue;

            $itemsBuffer[] = [
                'job_category_id' => $currentJobId,
                'category'        => $currentCategory,
                'name'            => trim($row[2]),
                'code'            => trim($row[3]),
                'unit'            => trim($row[4] ?? ''),
                'coefisien'       => $coef,
                'base_unit_price' => $price,
                'total_price'     => $coef * $price,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        fclose($file);

        // simpan job terakhir
        if ($currentJobId && $itemsBuffer) {
            JobCategoryItem::insert($itemsBuffer);

            $job = JobCategory::find($currentJobId);
            if ($job) {
                $this->recalcJobCategory($job); // 🔥 INI PENTING
            }
        }


        return back()->with('success', 'Import item A/B/C berhasil 🔥');
    }



private function recalcJobCategory(JobCategory $jobCategory)
{
    $subTotal = $jobCategory->items()->sum('total_price');

    $overheadPercent = $jobCategory->overhead_percent ?? 0;
    $profitPercent   = $jobCategory->profit_percent ?? 0;

    $overheadValue = $subTotal * ($overheadPercent / 100);
    $profitValue   = $subTotal * ($profitPercent / 100);

    $grandTotal = $subTotal + $overheadValue + $profitValue;

    $jobCategory->update([
        'subtotal'       => $subTotal,
        'overhead_value' => $overheadValue,
        'profit_value'   => $profitValue,
        'grand_total'    => $grandTotal,
    ]);
}

}
