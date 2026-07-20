<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Permission;
use App\Models\Role;

class EnsureRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

        // USER
        'tambah data user' => 'User',
        'lihat daftar user' => 'User',
        'ubah data user' => 'User',
        'hapus data user' => 'User',

        // ROLE
        'tambah data role' => 'Role',
        'lihat daftar role' => 'Role',
        'ubah data role' => 'Role',
        'hapus data role' => 'Role',

        // KARYAWAN
        'tambah data karyawan' => 'Karyawan',
        'lihat data karyawan' => 'Karyawan',
        'lihat daftar karyawan' => 'Karyawan',
        'ubah data karyawan' => 'Karyawan',
        'hapus data karyawan' => 'Karyawan',
        'riwayat penggajian karyawan' => 'Karyawan',

        // GUDANG
        'lihat daftar gudang' => 'Gudang',
        'tambah data gudang' => 'Gudang',
        'lihat data gudang' => 'Gudang',
        'ubah data gudang' => 'Gudang',
        'hapus data gudang' => 'Gudang',
        'riwayat transaksi gudang' => 'Gudang',

        // PRODUK
        'lihat daftar produk' => 'Produk',
        'tambah data produk' => 'Produk',
        'lihat data produk' => 'Produk',
        'ubah data produk' => 'Produk',
        'hapus data produk' => 'Produk',
        'riwayat pembelian produk' => 'Produk',
        'riwayat penjualan produk' => 'Produk',

        // CUSTOMER
        'lihat daftar customer' => 'Customer',
        'tambah data customer' => 'Customer',
        'lihat data customer' => 'Customer',
        'ubah data customer' => 'Customer',
        'hapus data customer' => 'Customer',
        'riwayat transaksi customer' => 'Customer',

        // AFFILIATOR
        'lihat daftar affiliator' => 'Affiliator',
        'tambah data affiliator' => 'Affiliator',
        'lihat data affiliator' => 'Affiliator',
        'ubah data affiliator' => 'Affiliator',
        'hapus data affiliator' => 'Affiliator',
        'riwayat performa affiliator' => 'Affiliator',

        // SUPPLIER
        'lihat daftar supplier' => 'Supplier',
        'tambah data supplier' => 'Supplier',
        'lihat data supplier' => 'Supplier',
        'ubah data supplier' => 'Supplier',
        'hapus data supplier' => 'Supplier',
        'riwayat pembelian supplier' => 'Supplier',

        // INVESTOR
        'lihat daftar investor' => 'Investor',
        'tambah data investor' => 'Investor',
        'lihat data investor' => 'Investor',
        'ubah data investor' => 'Investor',
        'hapus data investor' => 'Investor',
        'saham investor' => 'Investor',

        // TUKANG
        'lihat daftar tukang' => 'Tukang',
        'tambah data tukang' => 'Tukang',
        'lihat data tukang' => 'Tukang',
        'ubah data tukang' => 'Tukang',
        'hapus data tukang' => 'Tukang',
        'riwayat penggajian tukang' => 'Tukang',

        // KONTRAKTOR
        'lihat daftar kontraktor' => 'Kontraktor',
        'tambah data kontraktor' => 'Kontraktor',
        'lihat data kontraktor' => 'Kontraktor',
        'ubah data kontraktor' => 'Kontraktor',
        'hapus data kontraktor' => 'Kontraktor',
        'riwayat penggajian kontraktor' => 'Kontraktor',

        // DOKUMEN
        'lihat daftar dokumen' => 'Dokumen',
        'tambah dokumen' => 'Dokumen',
        'lihat dokumen' => 'Dokumen',
        'ubah dokumen' => 'Dokumen',
        'hapus dokumen' => 'Dokumen',

        // PEMBELIAN
        'lihat daftar pembelian produk' => 'Pembelian',
        'tambah data pembelian produk' => 'Pembelian',
        'lihat data pembelian produk' => 'Pembelian',
        'ubah data pembelian produk' => 'Pembelian',
        'hapus data pembelian produk' => 'Pembelian',
        'persetujuan pembelian produk' => 'Pembelian',

        // PENJUALAN
        'lihat daftar penjualan produk' => 'Penjualan',
        'tambah data penjualan produk' => 'Penjualan',
        'lihat data penjualan produk' => 'Penjualan',
        'ubah data penjualan produk' => 'Penjualan',
        'hapus data penjualan produk' => 'Penjualan',
        'persetujuan penjualan produk' => 'Penjualan',

        // PROYEK
        'lihat daftar proyek' => 'Proyek',
        'tambah data proyek' => 'Proyek',
        'lihat data proyek' => 'Proyek',
        'ubah data proyek' => 'Proyek',
        'hapus data proyek' => 'Proyek',

        // RAB
        'tambah data rab' => 'RAB',
        'lihat data rab' => 'RAB',
        'ubah data rab' => 'RAB',
        'hapus data rab' => 'RAB',

        // AKUNTANSI
        'tambah akunakuntansi' => 'Akuntansi',
        'lihat akunakuntansi' => 'Akuntansi',
        'ubah akunakuntansi' => 'Akuntansi',
        'hapus akunakuntansi' => 'Akuntansi',

        'tambah jurnal' => 'Akuntansi',
        'lihat jurnal' => 'Akuntansi',
        'ubah jurnal' => 'Akuntansi',
        'hapus jurnal' => 'Akuntansi',

        // ABSENSI
        'lihat daftar absensi' => 'Absensi',
        'tambah data absensi' => 'Absensi',
        'lihat data absensi' => 'Absensi',
        'ubah data absensi' => 'Absensi',
        'hapus data absensi' => 'Absensi',

        // PELATIHAN
        'lihat daftar pelatihan' => 'Pelatihan',
        'tambah data pelatihan' => 'Pelatihan',
        'lihat data pelatihan' => 'Pelatihan',
        'ubah data pelatihan' => 'Pelatihan',
        'hapus data pelatihan' => 'Pelatihan',

        // PENILAIAN
        'lihat daftar penilaian kinerja' => 'Penilaian',
        'tambah data penilaian kinerja' => 'Penilaian',
        'lihat data penilaian kinerja' => 'Penilaian',
        'ubah data penilaian kinerja' => 'Penilaian',
        'hapus data penilaian kinerja' => 'Penilaian',

        // AKUN
        'kelola akun' => 'Manajemen Akun',

        // MENU
        'tambah data menu' => 'Menu',
        'lihat daftar menu' => 'Menu',
        'ubah data menu' => 'Menu',
        'hapus data menu' => 'Menu',

        // ARSITEK
        'tambah data arsitek' => 'Arsitek',
        'lihat daftar arsitek' => 'Arsitek',
        'lihat data arsitek' => 'Arsitek',
        'ubah data arsitek' => 'Arsitek',
        'hapus data arsitek' => 'Arsitek',
        'riwayat penggajian arsitek' => 'Arsitek',

        // MASTER
        'tambah data kategori' => 'Kategori',
        'lihat daftar kategori' => 'Kategori',
        'ubah data kategori' => 'Kategori',
        'hapus data kategori' => 'Kategori',

        'tambah data merk' => 'Merk',
        'lihat daftar merk' => 'Merk',
        'ubah data merk' => 'Merk',
        'hapus data merk' => 'Merk',

        'tambah data tipe' => 'Tipe',
        'lihat daftar tipe' => 'Tipe',
        'ubah data tipe' => 'Tipe',
        'hapus data tipe' => 'Tipe',
    ];

        foreach ($permissions as $name => $module) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['modules' => $module]
            );
        }

        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super-Admin', 'guard_name' => 'web'],
            ['role_group' => 'Internal']
        );

        if ($superAdmin->permissions()->count() === 0) {
            $superAdmin->syncPermissions(Permission::all());
        }
    }
}
