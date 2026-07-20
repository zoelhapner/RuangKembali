class AhspAnalysisSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('seeders/files/analisa_detail.csv');
        $handle = fopen($path, 'r');

        DB::beginTransaction();

        try {

            $currentAhsp = null;
            $currentAnalysis = null;
            $currentType = null;
            $rowIndex = 0;

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $rowIndex++;

                if ($rowIndex <= 1) continue;

                /**
                 * CSV ANALISA
                 * 0 => kode_urut
                 * 1 => nama
                 * 2 => satuan
                 * 3 => koefisien
                 * 4 => harga_satuan
                 * 5 => jumlah
                 */

                $kodeUrut = trim($row[0] ?? null);
                $nama     = trim($row[1] ?? null);

                // === HEADER AHSP ===
                if ($kodeUrut && $nama) {
                    $currentAhsp = Ahsp::where('kode_urut', $kodeUrut)->first();

                    if ($currentAhsp) {
                        $currentAnalysis = $currentAhsp->analysis
                            ?? $currentAhsp->analysis()->create();
                    }

                    continue;
                }

                // === TIPE ===
                if (in_array(strtoupper($nama), ['TENAGA', 'BAHAN', 'PERALATAN'])) {
                    $currentType = strtoupper($nama);
                    continue;
                }

                // === DETAIL ITEM ===
                if ($currentAnalysis && $currentType && $nama) {

                    AhspAnalysisItem::create([
                        'ahsp_analysis_id' => $currentAnalysis->id,
                        'tipe'             => $currentType,
                        'nama'             => $nama,
                        'satuan'           => trim($row[2] ?? null),
                        'koefisien'        => (float) str_replace(',', '.', $row[3] ?? 0),
                        'harga_satuan'     => (float) str_replace(['.', ','], ['', '.'], $row[4] ?? 0),
                        'jumlah'           => (float) str_replace(['.', ','], ['', '.'], $row[5] ?? 0),
                    ]);
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
