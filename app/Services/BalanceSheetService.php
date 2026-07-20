<?php

namespace App\Services;

use Illuminate\Support\Collection;

class BalanceSheetService
{
    public static function calculate(Collection|array $groupedAccounts): array
    {
        if ($groupedAccounts instanceof Collection) {
            $groupedAccounts = $groupedAccounts->toArray();
        }

        $aktiva = $groupedAccounts['AKTIVA'] ?? [];

        $asetLancar = $aktiva['Aset Lancar - Kas & Bank']['subtotalBalance'] ?? 0;

        $persediaan = $aktiva['Aset Lancar - Persediaan Barang']['subtotalBalance'] ?? 0;

        $piutang = $aktiva['Aset Lancar - Piutang']['subtotalBalance'] ?? 0;

        $danaBelumDisetor = $aktiva['Aset Lancar - Dana Belum Disetor']['subtotalBalance'] ?? 0;

        $pajakDimuka = $aktiva['Aset Lancar - Pajak Bayar Dimuka']['subtotalBalance'] ?? 0;

        $asetTetap = $aktiva['Aset Tetap']['subtotalBalance'] ?? 0;

        $akumulasiPenyusutan = $aktiva['Penyusutan']['subtotalBalance'] ?? 0;

        $totalAktiva =
            $asetLancar
            + $persediaan
            + $piutang
            + $danaBelumDisetor
            + $pajakDimuka
            + $asetTetap
            + $akumulasiPenyusutan;

        $kewajiban = collect($groupedAccounts['KEWAJIBAN'] ?? [])
            ->sum('subtotalBalance');

        $ekuitas = collect($groupedAccounts['EKUITAS'] ?? [])
            ->sum('subtotalBalance');

        $pendapatan = collect($groupedAccounts['PENDAPATAN'] ?? [])
            ->sum('subtotalBalance');

        $beban = collect($groupedAccounts['BEBAN'] ?? [])
            ->sum('subtotalBalance');

        $labaBerjalan = $pendapatan - $beban;

        $totalPassiva =
            $kewajiban
            + $ekuitas
            + $labaBerjalan;

        return [

            // Aktiva
            'asetLancar' => $asetLancar,
            'persediaan' => $persediaan,
            'piutang' => $piutang,
            'dana' => $danaBelumDisetor,
            'pajak' => $pajakDimuka,
            'asetTetap' => $asetTetap,
            'penyusutan' => $akumulasiPenyusutan,

            // Passiva
            'kewajiban' => $kewajiban,
            'ekuitas' => $ekuitas,

            // Laba Rugi
            'pendapatan' => $pendapatan,
            'beban' => $beban,
            'labaBerjalan' => $labaBerjalan,

            // Total
            'totalAktiva' => $totalAktiva,
            'totalPassiva' => $totalPassiva,
        ];
    }
}