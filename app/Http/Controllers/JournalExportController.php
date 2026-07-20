<?php

namespace App\Http\Controllers;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response; 
use Illuminate\Http\Request;
use App\Models\License;
use App\Models\AccountingAccount;   
use App\Models\AccountingJournal;
use App\Models\AccountingJournalDetail;
    

class JournalExportController extends Controller
{  
    public function export(AccountingJournal $journal)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Jurnal Transaksi');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Info transaksi
        $sheet->setCellValue('A3', 'No. Transaksi');
        $sheet->setCellValue('B3', $journal->journal_code);

        $sheet->setCellValue('A4', 'Tanggal Transaksi');
        $sheet->setCellValue('B4', \Carbon\Carbon::parse($journal->transaction_date)->format('d/m/Y'));

        // $sheet->setCellValue('A5', 'Lisensi');
        // $sheet->setCellValue('B5', $journal->license->name ?? '-');

        // Header tabel
        $sheet->setCellValue('A7', 'No. Akun');
        $sheet->setCellValue('B7', 'Nama Akun');
        $sheet->setCellValue('C7', 'Deskripsi');
        $sheet->setCellValue('D7', 'User');
        $sheet->setCellValue('E7', 'Debit');
        $sheet->setCellValue('F7', 'Kredit');

        $row = 8;
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($journal->details as $detail) {
            $sheet->setCellValue("A{$row}", $detail->account->account_code ?? '-');
            $sheet->setCellValue("B{$row}", $detail->account->account_name ?? '-');
            $sheet->setCellValue("C{$row}", $detail->description ?? '-');
            $sheet->setCellValue("D{$row}", $detail->person_name ?? '-');
            $sheet->setCellValue("E{$row}", $detail->debit);
            $sheet->setCellValue("F{$row}", $detail->credit);

            $totalDebit += $detail->debit;
            $totalCredit += $detail->credit;
            $row++;
        }

        // Total
        $sheet->setCellValue("D{$row}", 'TOTAL');
        $sheet->setCellValue("E{$row}", $totalDebit);
        $sheet->setCellValue("F{$row}", $totalCredit);
        $sheet->getStyle("D{$row}:F{$row}")->getFont()->setBold(true);

        // Styling (opsional)
        $sheet->getStyle("A7:F7")->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);

        // Download response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'journal-'.$journal->journal_code.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function exportGeneral(Request $request)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Judul
    $sheet->setCellValue('A1', 'Jurnal Umum');
    $sheet->mergeCells('A1:G1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

    // Info transaksi
    $sheet->setCellValue('A3', 'Dari');
    $sheet->setCellValue('B3', $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') : '-');
    $sheet->setCellValue('A4', 'Sampai');
    $sheet->setCellValue('B4', $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') : '-');
    // $sheet->setCellValue('A5', 'Lisensi');
    // $sheet->setCellValue('B5', $request->license_id ? License::find($request->license_id)->name : 'Semua Lisensi');

    // Header tabel
    $sheet->setCellValue('A7', 'Tanggal');
    $sheet->setCellValue('B7', 'No Jurnal');
    $sheet->setCellValue('C7', 'Deskripsi');
    $sheet->setCellValue('D7', 'No. Akun');
    $sheet->setCellValue('E7', 'Nama Akun');
    $sheet->setCellValue('F7', 'Debit');
    $sheet->setCellValue('G7', 'Kredit');
    $sheet->getStyle("A7:G7")->getFont()->setBold(true);

    // Ambil data jurnal
    $journals = AccountingJournal::with(['details.account', 'license'])
        ->when($request->start_date, fn($q) => $q->whereDate('transaction_date', '>=', $request->start_date))
        ->when($request->end_date, fn($q) => $q->whereDate('transaction_date', '<=', $request->end_date))
        // ->when($request->license_id, fn($q) => $q->where('license_id', $request->license_id))
        ->orderBy('transaction_date')
        ->get();

    $row = 8;
    $totalDebit = 0;
    $totalCredit = 0;

    foreach ($journals as $journal) {
        foreach ($journal->details as $detail) {
            $sheet->setCellValue("A{$row}", \Carbon\Carbon::parse($journal->transaction_date)->format('d/m/Y'));
            $sheet->setCellValue("B{$row}", $journal->journal_code ?? '-');
            $sheet->setCellValue("C{$row}", $detail->description ?? '-');
            $sheet->setCellValue("D{$row}", $detail->account->account_code ?? '-');
            $sheet->setCellValue("E{$row}", $detail->account->account_name ?? '-');
            $sheet->setCellValue("F{$row}", $detail->debit);
            $sheet->setCellValue("G{$row}", $detail->credit);

            $totalDebit += $detail->debit;
            $totalCredit += $detail->credit;
            $row++;
        }
    }

    // Total
    $sheet->setCellValue("D{$row}", 'TOTAL');
    $sheet->setCellValue("F{$row}", $totalDebit);
    $sheet->setCellValue("G{$row}", $totalCredit);
    $sheet->getStyle("D{$row}:G{$row}")->getFont()->setBold(true);

    // Auto width
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Download
    $writer = new Xlsx($spreadsheet);
    $fileName = 'journal-general-' . now()->format('YmdHis') . '.xlsx';
    $temp_file = tempnam(sys_get_temp_dir(), $fileName);
    $writer->save($temp_file);

    return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
}

public function exportLedger(Request $request)
{
    // Ambil data akun dan transaksi ledger
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    // Ambil jurnal detail dengan relasi akun & jurnal
    $ledger = \App\Models\AccountingJournalDetail::with(['account', 'journal'])
        ->when($startDate, fn($q) => $q->whereHas('journal', fn($q2) => $q2->whereDate('transaction_date', '>=', $startDate)))
        ->when($endDate, fn($q) => $q->whereHas('journal', fn($q2) => $q2->whereDate('transaction_date', '<=', $endDate)))
        ->join('accounting_accounts', 'accounting_accounts.id', '=', 'accounting_journal_details.account_id')
        ->orderBy('accounting_accounts.account_code', 'asc') // ✅ urut berdasarkan kode akun
        ->orderBy('journal_id', 'asc')
        ->select('accounting_journal_details.*') // penting biar model tetap bener
        ->get()
        ->groupBy('account_id');

    // Siapkan spreadsheet baru
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Judul laporan
    $sheet->setCellValue('A1', 'Laporan Buku Besar');
    $sheet->mergeCells('A1:F1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

    // Informasi filter
    $sheet->setCellValue('A3', 'Dari');
    $sheet->setCellValue('B3', $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '-');
    $sheet->setCellValue('A4', 'Sampai');
    $sheet->setCellValue('B4', $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '-');

    $row = 7;

    foreach ($ledger as $accountId => $rows) {
        $account = $rows->first()->account;

        // Header akun
        $sheet->setCellValue("A{$row}", $account->account_code . ' - ' . $account->account_name);
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;

        // Header tabel
        $sheet->setCellValue("A{$row}", 'Tanggal');
        $sheet->setCellValue("B{$row}", 'Transaksi');
        $sheet->setCellValue("C{$row}", 'Deskripsi');
        $sheet->setCellValue("D{$row}", 'Debit');
        $sheet->setCellValue("E{$row}", 'Kredit');
        $sheet->setCellValue("F{$row}", 'Saldo');
        $sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true);
        $row++;

        $saldo = 0;

        foreach ($rows as $detail) {
            $saldo += $detail->debit - $detail->credit;

            $sheet->setCellValue("A{$row}", \Carbon\Carbon::parse($detail->journal->transaction_date)->format('d/m/Y'));
            $sheet->setCellValue("B{$row}", $detail->journal->journal_code);
            $sheet->setCellValue("C{$row}", $detail->description);
            $sheet->setCellValue("D{$row}", $detail->debit);
            $sheet->setCellValue("E{$row}", $detail->credit);
            $sheet->setCellValue("F{$row}", $saldo);

            $row++;
        }

        // Spasi antar akun
        $row++;
    }

    // Styling lebar kolom otomatis
    foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Generate file
    $writer = new Xlsx($spreadsheet);
    $fileName = 'ledger-' . now()->format('YmdHis') . '.xlsx';
    $temp_file = tempnam(sys_get_temp_dir(), $fileName);
    $writer->save($temp_file);

    return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
}

public function exportTrialBalance(Request $request)
   {
    $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
    $endDate   = $request->end_date ?? now()->endOfMonth()->toDateString();
    $licenseId = $request->license_id;

    // 🔹 Ambil langsung detail jurnal
    $journalDetails = AccountingJournalDetail::with('account')
        ->whereHas('journal', function ($q) use ($startDate, $endDate, $licenseId) {
            $q->whereBetween('transaction_date', [$startDate, $endDate])
              ->when($licenseId, fn($q) => $q->where('license_id', $licenseId));
        })
        ->get();

    // 🔹 Kelompokkan per kategori & sub kategori
    $groupedAccounts = [];
    $totalDebit = 0;
    $totalCredit = 0;

    $accounts = $journalDetails->groupBy('account_id');

    foreach ($accounts as $accountId => $details) {
        $account = $details->first()->account;

        $debit  = $details->sum('debit');
        $credit = $details->sum('credit');

        $category    = $account->category ?? 'Lainnya';
        $subCategory = $account->sub_category ?? 'Tanpa Sub Kategori';

        if (!isset($groupedAccounts[$category][$subCategory])) {
            $groupedAccounts[$category][$subCategory] = [
                'accounts' => [],
                'subtotalDebit' => 0,
                'subtotalCredit' => 0,
            ];
        }

        $groupedAccounts[$category][$subCategory]['accounts'][] = [
            'account_code' => $account->account_code,
            'account_name' => $account->account_name,
            'debit'        => $debit,
            'credit'       => $credit,
        ];

        $groupedAccounts[$category][$subCategory]['subtotalDebit']  += $debit;
        $groupedAccounts[$category][$subCategory]['subtotalCredit'] += $credit;

        $totalDebit  += $debit;
        $totalCredit += $credit;
    }

    // 🔹 Buat spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Judul
    $sheet->setCellValue('A1', 'Neraca Saldo');
    $sheet->mergeCells('A1:D1');
    $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Header
    $sheet->setCellValue('A3', 'Kode Akun');
    $sheet->setCellValue('B3', 'Nama Akun');
    $sheet->setCellValue('C3', 'Debit');
    $sheet->setCellValue('D3', 'Kredit');

    $sheet->getStyle('A3:D3')->getFont()->setBold(true);
    $sheet->getStyle('A3:D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A3:D3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E2E3E5');
    $sheet->getStyle('A3:D3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Data
    $row = 4;
    foreach ($groupedAccounts as $category => $subs) {
        // kategori
        $sheet->setCellValue("A{$row}", strtoupper($category));
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->getStyle("A{$row}:D{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:D{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F3F4F6');
        $row++;

        foreach ($subs as $subCat => $data) {
            // sub kategori
            $sheet->setCellValue("A{$row}", $subCat);
            $sheet->mergeCells("A{$row}:D{$row}");
            $sheet->getStyle("A{$row}:D{$row}")->getFont()->setItalic(true);
            $sheet->getStyle("A{$row}:D{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F8F9FA');
            $row++;

            foreach ($data['accounts'] as $acc) {
                $sheet->setCellValue("A{$row}", $acc['account_code']);
                $sheet->setCellValue("B{$row}", $acc['account_name']);
                $sheet->setCellValue("C{$row}", $acc['debit']);
                $sheet->setCellValue("D{$row}", $acc['credit']);
                $row++;
            }

            // subtotal
            $sheet->setCellValue("A{$row}", "Subtotal {$subCat}");
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->setCellValue("C{$row}", $data['subtotalDebit']);
            $sheet->setCellValue("D{$row}", $data['subtotalCredit']);
            $sheet->getStyle("A{$row}:D{$row}")->getFont()->setBold(true);
            $row++;
        }
    }

    // Total akhir
    $sheet->setCellValue("A{$row}", 'TOTAL');
    $sheet->mergeCells("A{$row}:B{$row}");
    $sheet->setCellValue("C{$row}", $totalDebit);
    $sheet->setCellValue("D{$row}", $totalCredit);
    $sheet->getStyle("A{$row}:D{$row}")->getFont()->setBold(true);

    // Format angka
    $sheet->getStyle("C4:D{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

    // Auto width
    foreach (range('A', 'D') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Output
    $fileName = 'trial_balance_' . now()->format('Ymd_His') . '.xlsx';
    $writer = new Xlsx($spreadsheet);

    return new StreamedResponse(function () use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
    ]);
}


}