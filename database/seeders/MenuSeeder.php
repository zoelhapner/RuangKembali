<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus semua data lama
        Menu::truncate();

        $menus = [
            [
                'text' => 'Dashboard',
                'icon' => 'ti ti-home',
                'url' => 'dashboard',
                'type' => 'route',
                'order' => 0,
                'is_active' => true,
            ],
            [
                'text' => 'Menu',
                'icon' => 'ti ti-home',
                'url' => '/menus',
                'type' => 'url',
                'order' => 1,
                'is_active' => true,
                'permission_name' => 'lihat daftar menu',
            ],
            [
                'text' => 'Akun',
                'icon' => 'ti ti-user-circle',
                'url' => '#',
                'type' => 'url',
                'order' => 2,
                'is_active' => true,
                'permission_name' => 'lihat daftar user',
                'children' => [
                    [
                        'text' => 'Manajemen User',
                        'icon' => 'ti ti-user-circle',
                        'url' => '/users',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar user',
                    ],
                    [
                        'text' => 'Manajemen Role',
                        'icon' => 'ti ti-user-check',
                        'url' => '/roles',
                        'type' => 'url',
                        'order' => 1,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar role',
                    ],
                    [
                        'text' => 'Manajemen Akun',
                        'icon' => 'ti ti-user',
                        'url' => '/accounts',
                        'type' => 'url',
                        'order' => 2,
                        'is_active' => true,
                        'permission_name' => 'kelola akun',
                    ],
                ],
            ],
            [
                'text' => 'Tim',
                'icon' => 'ti ti-briefcase',
                'url' => '#',
                'type' => 'url',
                'order' => 3,
                'is_active' => true,
                'permission_name' => 'lihat daftar karyawan|lihat data karyawan',
                'children' => [
                    [
                        'text' => 'Daftar Tim',
                        'icon' => 'ti ti-id-badge',
                        'url' => '/employees',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar karyawan|lihat data karyawan',
                    ],
                ],
            ],
            [
                'text' => 'Member',
                'icon' => 'ti ti-building-bank',
                'url' => '#',
                'type' => 'url',
                'order' => 5,
                'is_active' => true,
                'permission_name' => 'lihat daftar customer',
                'children' => [
                    [
                        'text' => 'Daftar Member',
                        'icon' => 'ti ti-book',
                        'url' => '/customer',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar customer',
                    ],
                    [
                        'text' => 'Edit Profile',
                        'icon' => 'ti ti-notebook',
                        'url' => '/customer/profile',
                        'type' => 'url',
                        'order' => 1,
                        'is_active' => true,
                        'permission_name' => 'ubah data customer',
                    ],
                    [
                        'text' => 'Riwayat Transaksi',
                        'icon' => 'ti ti-notebook',
                        'url' => '/customer/history',
                        'type' => 'url',
                        'order' => 2,
                        'is_active' => true,
                        'permission_name' => 'riwayat transaksi customer',
                    ],
                ],
            ],
            [
                'text' => 'Affiliator',
                'icon' => 'ti ti-building-bank',
                'url' => '#',
                'type' => 'url',
                'order' => 6,
                'is_active' => true,
                'permission_name' => 'lihat daftar affiliator',
                'children' => [
                    [
                        'text' => 'Daftar Affiliator',
                        'icon' => 'ti ti-book',
                        'url' => '/affiliator',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar affiliator',
                    ],
                    [
                        'text' => 'Riwayat Performa',
                        'icon' => 'ti ti-notebook',
                        'url' => '/affiliator/history',
                        'type' => 'url',
                        'order' => 1,
                        'is_active' => true,
                        'permission_name' => 'riwayat performa affiliator',
                    ],
                ],
            ],
            [
                'text' => 'Mitra',
                'icon' => 'ti ti-building-bank',
                'url' => '#',
                'type' => 'url',
                'order' => 7,
                'is_active' => true,
                'permission_name' => 'lihat daftar mitra',
                'children' => [
                    [
                        'text' => 'Daftar Mitra',
                        'icon' => 'ti ti-book',
                        'url' => '/mitra',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar mitra',
                    ],
                    [
                        'text' => 'Riwayat Pembelian',
                        'icon' => 'ti ti-notebook',
                        'url' => '/history/supplier',
                        'type' => 'url',
                        'order' => 1,
                        'is_active' => true,
                        'permission_name' => 'riwayat pembelian supplier',
                    ],
                ],
            ],

            [
                'text' => 'Finance',
                'icon' => 'ti ti-building-bank',
                'url' => '#',
                'type' => 'url',
                'order' => 16,
                'is_active' => true,
                'permission_name' => 'lihat akun-akuntansi',
                'children' => [
                    [
                        'text' => 'Daftar Akun Akuntansi',
                        'icon' => 'ti ti-book',
                        'url' => '/accounting/accounts',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat akun-akuntansi',
                    ],
                    [
                        'text' => 'Jurnal',
                        'icon' => 'ti ti-notebook',
                        'url' => '/accounting/journals',
                        'type' => 'url',
                        'order' => 1,
                        'is_active' => true,
                        'permission_name' => 'lihat jurnal',
                    ],
                ],
            ],
        ];

        foreach ($menus as $menuData) {
            $children = $menuData['children'] ?? [];
            unset($menuData['children']);

            $parent = Menu::create($menuData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                Menu::create($childData);
            }
        }
    }
}
