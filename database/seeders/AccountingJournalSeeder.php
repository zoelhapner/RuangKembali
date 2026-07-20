<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountingJournal;
use App\Models\User;
use App\Models\License;
use Illuminate\Support\Str;

class AccountingJournalSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil license pertama (contoh)
        $licenseId = License::first()->id ?? Str::uuid();
        $userId = User::first()->id ?? Str::uuid();

        // Buat 3 jurnal contoh
        for ($i = 1; $i <= 3; $i++) {
            AccountingJournal::create([
                'id' => Str::uuid(),
                'license_id' => $licenseId,
                'journal_code' => 'JRN-' . strtoupper(Str::random(5)),
                'transaction_date' => now()->subDays($i),
                'description' => 'Contoh Jurnal #' . $i,
                'created_by' => $userId,
            ]);
        }
    }
}
