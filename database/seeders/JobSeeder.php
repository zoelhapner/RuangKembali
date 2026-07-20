<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JobCategory;

class JobSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('seeders/files/rekap_analisa.csv');
        $handle = fopen($path, 'r');

        DB::beginTransaction();

        try {

            $currentGroup = null;
            $rowIndex = 0;

            while (($row = fgetcsv($handle, 0, ';')) !== false) {

                $rowIndex++;
                if ($rowIndex <= 1) continue;

                // BARIS GROUP
                if (
                    !empty($row[0]) &&
                    !empty($row[1]) &&
                    str_contains(strtoupper($row[2] ?? ''), 'HARGA SATUAN PEKERJAAN')
                ) {
                    $currentGroup = [
                        'bidang' => trim($row[0]),
                        'kode_group' => trim($row[1]),
                        'nama_group' => trim($row[2]),
                    ];
                    continue;
                }

                // BARIS ITEM
                if ($currentGroup && !empty($row[2]) && !empty($row[3]) && !empty($row[4])) {

                    JobCategory::updateOrCreate(
                        [
                            'kode_urut' => trim($row[2]),
                        ],
                        [
                            'bidang'         => $currentGroup['bidang'],
                            'kode_group'     => $currentGroup['kode_group'],
                            'nama_group'     => $currentGroup['nama_group'],
                            'kode'           => trim($row[1]),
                            'nama_pekerjaan' => trim($row[3]),
                            'satuan'         => trim($row[4]),
                        ]
                    );
                }
            }

            fclose($handle);
            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

