<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear Spatie permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

          // Bersihkan dulu pivot biar FK aman
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();

        // Daftar permission
        $permissions = [
            'tambah data user',
            'lihat daftar user',
            'ubah data user',
            'hapus data user',
            'tambah data role',
            'lihat daftar role',
            'ubah data role',
            'hapus data role',
            'tambah data tim',
            'lihat data tim',
            'lihat daftar tim',
            'ubah data tim',
            'hapus data tim',
            'riwayat penggajian tim',
            'lihat daftar member',
            'tambah data member',
            'lihat data member',
            'ubah data member',
            'hapus data member',
            'riwayat transaksi member',
            'lihat daftar affiliator',
            'tambah data affiliator',
            'lihat data affiliator',
            'ubah data affiliator',
            'hapus data affiliator',
            'riwayat performa affiliator',
            'tambah akun-akuntansi',
            'lihat akun-akuntansi',
            'ubah akun-akuntansi',
            'hapus akun-akuntansi',
            'tambah jurnal',
            'lihat jurnal',
            'ubah jurnal',
            'hapus jurnal',
            'kelola akun',
            'tambah data menu',
            'lihat daftar menu',
            'ubah data menu',
            'hapus data menu',

            // ARSITEK
            'tambah data mitra',
            'lihat daftar mitra',
            'lihat data mitra',
            'ubah data mitra',
            'hapus data mitra',
            'riwayat penggajian mitra',
        ];

        foreach ($permissions as $permissionName) {
            if (str_contains($permissionName, 'tim')) $module = 'Tim';
            elseif (str_contains($permissionName, 'gudang')) $module = 'Gudang';
            elseif (str_contains($permissionName, 'produk')) $module = 'Produk';
            elseif (str_contains($permissionName, 'member')) $module = 'Member';
            elseif (str_contains($permissionName, 'affiliator')) $module = 'Affiliator';
            elseif (str_contains($permissionName, 'supplier')) $module = 'Supplier';
            elseif (str_contains($permissionName, 'investor')) $module = 'Investor';
            elseif (str_contains($permissionName, 'tukang')) $module = 'Tukang';
            elseif (str_contains($permissionName, 'kontraktor')) $module = 'Kontraktor';
            elseif (str_contains($permissionName, 'dokumen')) $module = 'Dokumen';
            elseif (str_contains($permissionName, 'pembelian')) $module = 'Pembelian Produk';
            elseif (str_contains($permissionName, 'penjualan')) $module = 'Penjualan Produk';
            elseif (str_contains($permissionName, 'proyek')) $module = 'Proyek';
            elseif (str_contains($permissionName, 'rab')) $module = 'RAB';
            elseif (str_contains($permissionName, 'akun-akuntansi')) $module = 'Akun Akuntansi';
            elseif (str_contains($permissionName, 'jurnal')) $module = 'Jurnal';
            elseif (str_contains($permissionName, 'user')) $module = 'User';
            elseif (str_contains($permissionName, 'role')) $module = 'Role';
            elseif (str_contains($permissionName, 'absensi')) $module = 'Absensi';
            elseif (str_contains($permissionName, 'pelatihan')) $module = 'Pelatihan';
            elseif (str_contains($permissionName, 'kinerja')) $module = 'Kinerja';
            elseif (str_contains($permissionName, 'menu')) $module = 'Menu';
            elseif (str_contains($permissionName, 'kategori')) $module = 'Kategori';
            elseif (str_contains($permissionName, 'merk')) $module = 'Merk';
            elseif (str_contains($permissionName, 'tipe')) $module = 'Tipe';
            elseif (str_contains($permissionName, 'akun')) $module = 'Manajemen Akun';
            else $module = 'Lainnya';

        $permission = Permission::firstOrCreate(
            ['name' => $permissionName, 'guard_name' => 'web'],
            ['id' => (string) Str::uuid(),
              'modules' => $module]
        );

        // Paksa id kalau masih numeric atau salah format
        if (!$permission->id || strlen($permission->id) < 36) {
            $permission->id = Str::uuid();
            $permission->save();
        }
    }

        $roleGroups = [
            'Internal' => [
                'Super-Admin',
                'Tim',
                'Tim Finance',
                ],
            'Eksternal' => [
                'Mitra',
                'Member',
                'Affiliator',
            ],
        ];

        foreach ($roleGroups as $group => $roles) {
            foreach ($roles as $roleName) {
                Role::firstOrCreate(
                    ['name' => $roleName, 'guard_name' => 'web'],
                    ['id' => (string) Str::uuid(), 'role_group' => $group] // tambahan kolom group
                );
            }
        }

        $superAdmin = Role::where('name', 'Super-Admin')->first();
        $superAdmin->syncPermissions(Permission::all());
    }
}

        // Role::where('name', 'Komisaris')->first()->syncPermissions([]);
        // Role::where('name', 'Tukang')->first()->syncPermissions([]);
        // Role::where('name', 'Kontraktor')->first()->syncPermissions([]);

        // Buat contoh user + role
        // \App\Models\User::factory()->create([
        //     'fullname' => 'Komisaris',
        //     'email' => 'komisaris@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole('Komisaris');

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Manager Administrasi',
        //     'email' => 'manageradm@example.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole('Manager Administrasi');

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Manager Teknik',
        //     'email' => 'managerteknik@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole('Manager Teknik');

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Spv Marketing',
        //     'email' => 'spvmarketing@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Spv Marketing']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Spv Finance',
        //     'email' => 'spvfinance@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Spv Finance']);
        // \App\Models\User::factory()->create([
        //     'fullname' => 'Spv Arsitek',
        //     'email' => 'spvarsitek@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Spv Arsitek']);
        // \App\Models\User::factory()->create([
        //     'fullname' => 'Spv Sipil',
        //     'email' => 'spvsipil@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Spv Sipil']);
        // \App\Models\User::factory()->create([
        //     'fullname' => 'Staff Marketing',
        //     'email' => 'staffmarketing@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Staff Marketing']);
        // \App\Models\User::factory()->create([
        //     'fullname' => 'Staff Finance',
        //     'email' => 'stafffinance@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Staff Finance']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Staff HRD',
        //     'email' => 'staffhrd@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Staff HRD']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Quality Control',
        //     'email' => 'qc@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['QC']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Estimator',
        //     'email' => 'estimator@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Estimator']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Investor',
        //     'email' => 'investor@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Investor']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Tukang',
        //     'email' => 'worker@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Tukang']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Mitra Kontraktor',
        //     'email' => 'mitrak@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Mitra Kontraktor']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Mitra Supplier',
        //     'email' => 'mitras@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Mitra Supplier']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Mitra Arsitek',
        //     'email' => 'mitraa@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Mitra Arsitek']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Customer',
        //     'email' => 'customer@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Customer']);

        // \App\Models\User::factory()->create([
        //     'fullname' => 'Affiliator',
        //     'email' => 'affiliator@gmail.com',
        //     'password' => Hash::make('12345678'),
        // ])->assignRole(['Affiliator']);
