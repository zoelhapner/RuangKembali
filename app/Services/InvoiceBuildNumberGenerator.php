<?php

namespace App\Services;

use App\Models\InvoiceBuild;
use Illuminate\Support\Facades\DB;

class InvoiceBuildNumberGenerator
{
    public static function generate(int $termin): string
    {
        return DB::transaction(function () use ($termin) {

            $now = now();
            $tahunFull = $now->format('Y');
            $tahun = $now->format('y');
            $bulanRomawi = \App\Helpers\GeneralHelper::bulanRomawi($now->month);

            $terminCode = match ($termin) {
                1 => 'A',
                2 => 'B',
                3 => 'C',
                4 => 'D',
                default => throw new \Exception('Termin tidak valid'),
            };

            $prefix = "INV/BLD/{$terminCode}/{$tahun}/{$bulanRomawi}";

            $counter = DB::table('invoice_counters')
                ->where('prefix', $prefix)
                ->where('year', $tahunFull)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                DB::table('invoice_counters')->insert([
                    'prefix' => $prefix,
                    'year' => $tahunFull,
                    'last_number' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $next = 1;
            } else {
                $next = $counter->last_number + 1;

                DB::table('invoice_counters')
                    ->where('prefix', $prefix)
                    ->where('year', $tahunFull)
                    ->update([
                        'last_number' => $next,
                        'updated_at' => now(),
                    ]);
            }

            return $prefix . '/' . str_pad($next, 3, '0', STR_PAD_LEFT);
        }, 5);
    }
    public static function generateJustek(): string
{
    return DB::transaction(function () {

        $now = now();
        $tahunFull = $now->format('Y');
        $tahun = $now->format('y');
        $bulanRomawi = \App\Helpers\GeneralHelper::bulanRomawi($now->month);

        $prefix = "INV/BLD/JSTK/{$tahun}/{$bulanRomawi}";

        $counter = DB::table('invoice_counters')
            ->where('prefix', $prefix)
            ->where('year', $tahunFull)
            ->lockForUpdate()
            ->first();

        if (!$counter) {

            DB::table('invoice_counters')->insert([
                'prefix' => $prefix,
                'year' => $tahunFull,
                'last_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $next = 1;

        } else {

            $next = $counter->last_number + 1;

            DB::table('invoice_counters')
                ->where('prefix', $prefix)
                ->where('year', $tahunFull)
                ->update([
                    'last_number' => $next,
                    'updated_at' => now(),
                ]);
        }

        return $prefix . '/' . str_pad($next, 3, '0', STR_PAD_LEFT);

    }, 5);
}
}

