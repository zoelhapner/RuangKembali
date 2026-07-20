<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountingJournal;
use App\Models\AccountingJournalDetail;
use App\Models\AccountingAccount;
use Illuminate\Support\Str;

class AccountingJournalDetailSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = AccountingAccount::take(2)->pluck('id'); // ambil 2 akun contoh
        $journals = AccountingJournal::all();

        foreach ($journals as $journal) {
            // Tambah 2 baris detail (1 debit, 1 kredit)
            AccountingJournalDetail::create([
                'id' => Str::uuid(),
                'journal_id' => $journal->id,
                'account_id' => $accounts[0] ?? Str::uuid(),
                'debit' => 50000,
                'credit' => 0,
                'description' => 'Debit Contoh',
            ]);

            AccountingJournalDetail::create([
                'id' => Str::uuid(),
                'journal_id' => $journal->id,
                'account_id' => $accounts[1] ?? Str::uuid(),
                'debit' => 0,
                'credit' => 50000,
                'description' => 'Kredit Contoh',
            ]);
        }
    }
}
