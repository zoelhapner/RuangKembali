<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountingAccount;
use App\Models\License;
use Illuminate\Support\Str;

class AccountingAccountSeeder extends Seeder
{
    public function run(): void
    {
        $license = License::first();
        if (!$license) {
            $license = License::create([
                'id' => Str::uuid(),
                'license_name' => 'Lisensi Contoh',
            ]);
        }

       // Akun Induk: Aset
        $aset = AccountingAccount::create([
            'id' => Str::uuid(),
            'license_id' => $license->id,
            'account_code' => '1',
            'account_name' => 'Aset',
            'account_type' => 'Asset',
            'is_parent' => true,
            'balance_type' => 'Debit',
            'initial_balance' => 0,
            'is_active' => true,
        ]);

        // Akun Anak: Kas
        AccountingAccount::create([
            'id' => Str::uuid(),
            'license_id' => $license->id,
            'account_code' => '1.1',
            'account_name' => 'Kas',
            'account_type' => 'Asset',
            'is_parent' => false,
            'parent_id' => $aset->id,
            'balance_type' => 'Debit',
            'initial_balance' => 0,
            'is_active' => true,
        ]);

        // Akun Anak: Bank
        AccountingAccount::create([
            'id' => Str::uuid(),
            'license_id' => $license->id,
            'account_code' => '1.2',
            'account_name' => 'Bank',
            'account_type' => 'Asset',
            'is_parent' => false,
            'parent_id' => $aset->id,
            'balance_type' => 'Debit',
            'initial_balance' => 0,
            'is_active' => true,
        ]);

        // Akun Induk: Liabilitas
        $liabilitas = AccountingAccount::create([
            'id' => Str::uuid(),
            'license_id' => $license->id,
            'account_code' => '2',
            'account_name' => 'Liabilitas',
            'account_type' => 'Liability',
            'is_parent' => true,
            'balance_type' => 'Credit',
            'initial_balance' => 0,
            'is_active' => true,
        ]);

        // Akun Anak: Hutang Usaha
        AccountingAccount::create([
            'id' => Str::uuid(),
            'license_id' => $license->id,
            'account_code' => '2.1',
            'account_name' => 'Hutang Usaha',
            'account_type' => 'Liability',
            'is_parent' => false,
            'parent_id' => $liabilitas->id,
            'balance_type' => 'Credit',
            'initial_balance' => 0,
            'is_active' => true,
        ]);

    }
}
