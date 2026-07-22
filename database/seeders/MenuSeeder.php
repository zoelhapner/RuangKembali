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
                'text' => 'Tambahan',
                'icon' => 'ti ti-home',
                'url' => '#',
                'type' => 'url',
                'order' => 1,
                'is_active' => true,
                'permission_name' => 'lihat daftar menu',
                'children' => [
                    [
                        'text' => 'Daftar Menu',
                        'icon' => 'ti ti-book',
                        'url' => '/menus',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar menu',
                    ],
                    [
                        'text' => 'Daftar Permission',
                        'icon' => 'ti ti-notebook',
                        'url' => '/permissions',
                        'type' => 'url',
                        'order' => 1,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar permission',
                    ]
                ],
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
                'permission_name' => 'lihat daftar tim|lihat data tim',
                'children' => [
                    [
                        'text' => 'Daftar Tim',
                        'icon' => 'ti ti-id-badge',
                        'url' => '/teams',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar tim|lihat data tim',
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
                'permission_name' => 'lihat daftar member',
                'children' => [
                    [
                        'text' => 'Daftar Member',
                        'icon' => 'ti ti-book',
                        'url' => '/members',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar member',
                    ],
                    [
                        'text' => 'Edit Profile',
                        'icon' => 'ti ti-notebook',
                        'url' => '/member/profile',
                        'type' => 'url',
                        'order' => 1,
                        'is_active' => true,
                        'permission_name' => 'ubah data member',
                    ],
                    [
                        'text' => 'Riwayat Transaksi',
                        'icon' => 'ti ti-notebook',
                        'url' => '/member/history',
                        'type' => 'url',
                        'order' => 2,
                        'is_active' => true,
                        'permission_name' => 'riwayat transaksi member',
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
                        'url' => '/affiliators',
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
                'text' => 'Vendor',
                'icon' => 'ti ti-building-bank',
                'url' => '#',
                'type' => 'url',
                'order' => 8,
                'is_active' => true,
                'permission_name' => 'lihat daftar vendor',
                'children' => [
                    [
                        'text' => 'Daftar Mitra',
                        'icon' => 'ti ti-book',
                        'url' => '/vendors',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat daftar vendor',
                    ]
                ],
            ],

            [
                'text' => 'Finance',
                'icon' => 'ti ti-building-bank',
                'url' => '#',
                'type' => 'url',
                'order' => 9,
                'is_active' => true,
                'permission_name' => 'lihat akun-akuntansi',
                'children' => [
                    [
                        'text' => 'Detail Akun',
                        'icon' => 'ti ti-book',
                        'url' => '/accounting',
                        'type' => 'url',
                        'order' => 0,
                        'is_active' => true,
                        'permission_name' => 'lihat akun-akuntansi',
                    ],
                    [
                        'text' => 'Input Jurnal',
                        'icon' => 'ti ti-notebook',
                        'url' => '/journals',
                        'type' => 'url',
                        'order' => 1,
                        'is_active' => true,
                        'permission_name' => 'lihat jurnal',
                    ],
                    [
                        'text' => 'Jurnal Umum',
                        'icon' => 'ti ti-notebook',
                        'url' => '/journals/general',
                        'type' => 'url',
                        'order' => 2,
                        'is_active' => true,
                        'permission_name' => 'lihat jurnal',
                    ],
                    [
                        'text' => 'Transaksi',
                        'icon' => 'ti ti-notebook',
                        'url' => '/journals/report',
                        'type' => 'url',
                        'order' => 3,
                        'is_active' => true,
                        'permission_name' => 'lihat jurnal',
                    ],
                    [
                        'text' => 'Buku Besar',
                        'icon' => 'ti ti-notebook',
                        'url' => '/journals/ledger',
                        'type' => 'url',
                        'order' => 4,
                        'is_active' => true,
                        'permission_name' => 'lihat jurnal',
                    ],
                    [
                        'text' => 'Neraca Saldo',
                        'icon' => 'ti ti-notebook',
                        'url' => '/reports/balance_sheet',
                        'type' => 'url',
                        'order' => 5,
                        'is_active' => true,
                        'permission_name' => 'lihat jurnal',
                    ],
                    [
                        'text' => 'Laba Rugi',
                        'icon' => 'ti ti-notebook',
                        'url' => '/reports/income-statemment',
                        'type' => 'url',
                        'order' => 6,
                        'is_active' => true,
                        'permission_name' => 'lihat jurnal',
                    ],
                    [
                        'text' => 'Tutup Buku',
                        'icon' => 'ti ti-notebook',
                        'url' => '/periods',
                        'type' => 'url',
                        'order' => 7,
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
