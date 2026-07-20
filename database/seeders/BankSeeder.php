<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $banks = [
            ['code' => '002', 'name' => 'Bank BRI'],
            ['code' => '008', 'name' => 'Bank Mandiri'],
            ['code' => '009', 'name' => 'Bank BNI'],
            ['code' => '014', 'name' => 'Bank BCA'],
            ['code' => '022', 'name' => 'CIMB Niaga'],
            ['code' => '013', 'name' => 'Bank Permata'],
            ['code' => '011', 'name' => 'Bank Danamon'],
            ['code' => '016', 'name' => 'Bank Maybank Indonesia'],
            ['code' => '028', 'name' => 'Bank OCBC NISP'],
            ['code' => '213', 'name' => 'Bank BTPN'],
            ['code' => '451', 'name' => 'Bank Syariah Indonesia (BSI)'],
            ['code' => '145', 'name' => 'Bank Nobu'],
            ['code' => '422', 'name' => 'Bank Mega'],
            ['code' => '426', 'name' => 'Bank Mega Syariah'],
            ['code' => '536', 'name' => 'Bank Ina Perdana'],
        ];

        foreach ($banks as $bank) {
            DB::table('banks')->insert([
                'id' => Str::uuid(),
                'code' => $bank['code'],
                'name' => $bank['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
