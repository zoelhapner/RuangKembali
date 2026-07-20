<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AccountingJournal;
class KasController extends Controller
{
   public function exportExcel(Request $request)
{
    $accountId = $request->account_id;

    $journalsQuery = AccountingJournal::query()
        ->with('creator')
        ->with(['details' => function ($q) use ($accountId) {
            if ($accountId) {
                $q->where('account_id', $accountId)->with('account');
            } else {
                $q->with('account');
            }
        }])
        ->when($accountId, fn($q) =>
            $q->whereHas('details', fn($q2) => $q2->where('account_id', $accountId))
        )
        ->when($request->start_date && $request->end_date, fn($q) =>
            $q->whereBetween('transaction_date', [$request->start_date, $request->end_date])
        )
        ->orderBy('transaction_date', 'asc');

    $journals = $journalsQuery->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $headers = [
        'NO', 'TANGGAL', 'DESKRIPSI', 'KODE AKUN', 'AKUN',
        'USER', 'PIC', 'DEBIT (pemasukan)', 'KREDIT (pengeluaran)',
        'SALDO', 'KETERANGAN'
    ];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $col++;
    }

    // Isi data
    $no = 1;
    $row = 2;
    $saldo = 0;
    $totalDebit = 0;
    $totalKredit = 0;

    foreach ($journals as $journal) {
        foreach ($journal->details as $detail) {
            $debit = $detail->debit;
            $kredit = $detail->credit;
            $saldo += $debit - $kredit;
            $totalDebit += $debit;
            $totalKredit += $kredit;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, Carbon::parse($journal->transaction_date)->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $detail->description ?? '-');
            $sheet->setCellValue('D' . $row, $detail->account->account_code);
            $sheet->setCellValue('E' . $row, $detail->account->account_name);
            $sheet->setCellValue('F' . $row, $detail->person_name);
            $sheet->setCellValue('G' . $row, $journal->creator->fullname ?? '-');
            $sheet->setCellValue('H' . $row, $debit);
            $sheet->setCellValue('I' . $row, $kredit);
            $sheet->setCellValue('J' . $row, $saldo);
            $sheet->setCellValue('K' . $row, $journal->description);
            $row++;
        }
    }

    // Baris total
    $sheet->setCellValue('A' . $row, 'TOTAL');
    $sheet->mergeCells("A{$row}:G{$row}");
    $sheet->setCellValue('H' . $row, $totalDebit);
    $sheet->setCellValue('I' . $row, $totalKredit);
    $sheet->setCellValue('J' . $row, $saldo);

    // Output Excel
    $fileName = 'laporan_kas_' . date('Y-m-d_H-i-s') . '.xlsx';
    $writer = new Xlsx($spreadsheet);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    $writer->save('php://output');
    exit;
}
    
}
