<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('users')->insert([
            [
                'id' => Str::uuid(),
                'fullname' => 'Super Admin',
                'nickname' => '-',
                'gender' => 0, // 1 = laki-laki, 2 = perempuan
                'email' => 'superadmin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'birth_place' => '-',
                'birth_date' => '-',
                'religion_id' => 0, // pastikan ID ini ada di tabel religions
                'address' => '-',
                'province_id' => 15, // contoh Jawa Timur
                'city_id' => 238, // contoh Kota Surabaya
                'district_id' => 3478,
                'sub_district_id' => 43167,
                'postal_code_id' => 43167,
                'phone' => '081234567890',
                'photo' => 'users/fadli.jpg',
                'identity_number' => '3509192207970002',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'fullname' => 'Siti Rahmawati',
                'nickname' => 'Siti',
                'gender' => 2,
                'email' => 'siti@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'birth_place' => 'Bandung',
                'birth_date' => '1998-09-23',
                'religion_id' => 1,
                'address' => 'Jl. Pahlawan No. 22, Bandung Wetan',
                'province_id' => 4, // Jawa Barat
                'city_id' => 86, // Kota Bandung
                'district_id' => 1034,
                'sub_district_id' => 15186,
                'postal_code_id' => 15186,
                'phone' => '082233445566',
                'photo' => 'users/siti.jpg',
                'identity_number' => '3509194811990005',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]); 
    }
}
