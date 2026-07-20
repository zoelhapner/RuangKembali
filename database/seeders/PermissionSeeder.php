<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // === TENAGA ===
            ['name' => 'lihat data tenaga', 'module' => 'tenaga'],
            ['name' => 'tambah data tenaga', 'module' => 'tenaga'],
            ['name' => 'ubah data tenaga', 'module' => 'tenaga'],
            ['name' => 'hapus data tenaga', 'module' => 'tenaga'],

            // === AKUN AKUNTANSI ===
            ['name' => 'lihat data alat', 'module' => 'peralatan'],
            ['name' => 'tambah data alat', 'module' => 'peralatan'],
            ['name' => 'ubah data alat', 'module' => 'peralatan'],
            ['name' => 'hapus data alat', 'module' => 'peralatan'],

            // tambahin sesuai kebutuhan kamu
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                [
                    'name' => $perm['name'],
                    'guard_name' => 'web',
                ],
                [
                    'id' => DB::raw('gen_random_uuid()'), // PostgreSQL UUID
                    'modules' => $perm['module'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}