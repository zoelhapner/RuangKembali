<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    | Here you can change the default title of your admin panel.
    |
    */

    'title' => 'Ruang Kembali',
    'title_prefix' => '',
    'title_postfix' => '',
    'bottom_title' => 'Ruang Kembali',
    'current_version' => 'v11.11',


    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    */

    'logo' => '<b>Tab</b>LAR',
    'logo_img_alt' => 'Admin Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can set up an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'assets/ruka.png',
            'alt' => 'Ruang Kembali',
            'class' => 'navbar-brand-image',
            'width' =>  50,
            'height' => 50,
        ],
    ],

    /*
     *
     * Default path is 'resources/views/vendor/tablar' as null. Set your custom path here If you need.
     */

    'views_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look at the layout section here:
    |
    */

    'layout' => 'vertical',
    //boxed, combo, condensed, fluid, fluid-vertical, horizontal, navbar-overlap, navbar-sticky, rtl, vertical, vertical-right, vertical-transparent

    'layout_light_sidebar' => true,
    'layout_light_topbar' => true,
    'layout_enable_top_header' => true,

    /*
    |--------------------------------------------------------------------------
    | Sticky Navbar for Top Nav
    |--------------------------------------------------------------------------
    |
    | Here you can enable/disable the sticky functionality of Top Navigation Bar.
    |
    | For detailed instructions, you can look at the Top Navigation Bar classes here:
    |
    */

    'sticky_top_nav_bar' => false,

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions, you can look at the admin panel classes here:
    |
    */

    'classes_body' => '',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions, you can look at the urls section here:
    |
    */

    'use_route_url' => true,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password.request',
    'password_email_url' => 'password.email',
    'profile_url' => 'profile.edit',

    /*
    |--------------------------------------------------------------------------
    | Display Alert
    |--------------------------------------------------------------------------
    |
    | Display Alert Visibility.
    |
    */
    'display_alert' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    |
    */

    'menu' => [

        [
            'text' => 'Beranda',
            'icon' => 'ti ti-home',
            'url'  => 'dashboard',
        ],

        [
            'text' => 'Akun',
            'url' => '#',
            'icon' => 'ti ti-user-circle',
            'active' => ['support1'],
            'submenu' => [
                [
                    'text' => 'Manajemen User',
                    'url'  => '/users',
                    'icon' => 'ti ti-user-circle',
                    ''  => 'lihat daftar user',
                ],
                [
                    'text' => 'Manajemen Role',
                    'url'  => '/roles',
                    'icon' => 'ti ti-user-check',
                    ''  => 'lihat daftar role',
                ],
                [
                    'text' => 'Manajemen Akun',
                    'url'  => '/accounts',
                    'icon' => 'ti ti-user',
                    ''  => 'lihat daftar akun',
                ],
            ],
        ],

        [
            'text' => 'SDM',
            'url' => '#',
            'icon' => 'ti ti-briefcase',
            'active' => ['support2'],
            'submenu' => [
                [
                    'text' => 'Data Karyawan',
                    'url'  => '/employees',
                    'icon' => 'ti ti-id-badge',
                    ''  => 'lihat daftar karyawan',
                ],
                [
                    'text' => 'Absensi',
                    'url'  => '/licenses',
                    'icon' => 'ti ti-article',
                    ''  => 'lihat daftar absensi',
                ],
                [
                    'text' => 'Pelatihan',
                    'url'  => '/license_holders',
                    'icon' => 'ti ti-article',
                    ''  => 'lihat daftar pelatihan',
                ],
                [
                    'text' => 'Penilaian Kinerja',
                    'url'  => '/accounting',
                    'icon' => 'ti ti-article',
                    ''  => 'lihat daftar penilaian kinerja',
                ],
            ],
        ],

        [
            'text' => 'Recruitment',
            'url' => '#',
            'icon' => 'ti ti-users',
            'active' => ['support3'],
            'submenu' => [
                [
                    'text' => 'Ticket',
                    'url'  => 'support3',
                    'icon' => 'ti ti-article',
                    ''  => 'lihat daftar recruitment',
                ],
            ],
        ],

        [
            'text' => 'Data Gudang',
            'url' => '#',
            'icon' => 'ti ti-hammer',
            'submenu' => [
                [
                    'text' => 'Daftar Gudang',
                    'url'  => '/warehouses',
                    'icon' => 'ti ti-building-warehouse',
                    ''  => 'lihat daftar gudang',
                ],
                [
                    'text' => 'Riwayat Gudang',
                    'url'  => '/products',
                    'icon' => 'ti ti-package',
                    ''  => 'lihat daftar produk',
                ],
            ],
        ],

        [
            'text' => 'Data Produk',
            'url' => '#',
            'icon' => 'ti ti-hammer',
            'submenu' => [
                [
                    'text' => 'Daftar Produk',
                    'url'  => '/warehouses',
                    'icon' => 'ti ti-building-warehouse',
                    ''  => 'lihat daftar gudang',
                ],
                [
                    'text' => 'Riwayat Transaksi Produk',
                    'url'  => '/products',
                    'icon' => 'ti ti-package',
                    ''  => 'lihat daftar produk',
                ],
            ],
        ],

        [
            'text' => 'Finance',
            'url' => '#',
            'icon' => 'ti ti-building-bank',
            'submenu' => [
                [
                    'text' => 'Daftar Akun Akuntansi',
                    'url'  => '/accounting/accounts',
                    'icon' => 'ti ti-book',
                    ''  => 'lihat akun-akuntansi',
                ],
                [
                    'text' => 'Jurnal',
                    'url'  => '/accounting/journals',
                    'icon' => 'ti ti-notebook',
                    ''  => 'lihat jurnal',
                ],
            ],
        ],

        [
            'text' => 'Customer',
            'url' => '#',
            'icon' => 'ti ti-building-bank',
            'submenu' => [
                [
                    'text' => 'Daftar Customer',
                    'url'  => '/accounting/accounts',
                    'icon' => 'ti ti-book',
                    ''  => 'lihat daftar customer',
                ],
                [
                    'text' => 'Edit Profile',
                    'url'  => '/accounting/journals',
                    'icon' => 'ti ti-notebook',
                    ''  => 'ubah data customer',
                ],
                 [
                    'text' => 'Riwayat Transaksi Customer',
                    'url'  => '/accounting/journals',
                    'icon' => 'ti ti-notebook',
                    ''  => 'riwayat transaksi customer',
                ],
            ],
        ],

        // [
        //     'text' => 'Marketing',
        //     'url' => '#',
        //     'icon' => 'ti ti-shopping-cart',
        //     'submenu' => [
        //         [
        //             'text' => 'Daftar Customer',
        //             'url'  => '/customers',
        //             'icon' => 'ti ti-user-star',
        //             ''  => 'lihat daftar customer',
        //         ],
        //         [
        //             'text' => 'Daftar Affiliator',
        //             'url'  => '/affiliators',
        //             'icon' => 'ti ti-affiliate',
        //             ''  => 'lihat daftar affiliator',
        //         ],
        //         [
        //             'text' => 'Daftar Supplier',
        //             'url'  => '/suppliers',
        //             'icon' => 'ti ti-truck-delivery',
        //             ''  => 'lihat daftar supplier',
        //         ],
        //         [
        //             'text' => 'Daftar Investor',
        //             'url'  => '/investors',
        //             'icon' => 'ti ti-coin',
        //             ''  => 'lihat daftar investor',
        //         ],
        //     ],
        // ],

        [
            'text' => 'Proyek',
            'url' => '#',
            'icon' => 'ti ti-building-arch',
            'submenu' => [
                [
                    'text' => 'Daftar Proyek',
                    'url'  => '/projects',
                    'icon' => 'ti ti-building-community',
                    'can'  => 'lihat daftar proyek',
                ],
                [
                    'text' => 'RAB Proyek',
                    'url'  => '/projects/rab',
                    'icon' => 'ti ti-calculator',
                    ''  => 'lihat data rab',
                ],
            ],
        ],

        [
            'text' => 'Dokumen',
            'url' => '#',
            'icon' => 'ti ti-files',
            'submenu' => [
                [
                    'text' => 'Daftar Dokumen',
                    'url'  => '/documents',
                    'icon' => 'ti ti-file-text',
                ],
            ],
        ],

        // [
        //     'text' => 'Partner',
        //     'url' => '#',
        //     'icon' => 'ti ti-users-group',
        //     'submenu' => [
        //         [
        //             'text' => 'Daftar Kontraktor',
        //             'url'  => '/contractors',
        //             'icon' => 'ti ti-user-cog',
        //         ],
        //         [
        //             'text' => 'Daftar Tukang',
        //             'url'  => '/workers',
        //             'icon' => 'ti ti-user-wrench',
        //         ],
        //     ],
        // ],

    ],

    

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    |
    */

    'filters' => [
        TakiElias\Tablar\Menu\Filters\GateFilter::class,
        TakiElias\Tablar\Menu\Filters\HrefFilter::class,
        TakiElias\Tablar\Menu\Filters\SearchFilter::class,
        TakiElias\Tablar\Menu\Filters\ActiveFilter::class,
        TakiElias\Tablar\Menu\Filters\ClassesFilter::class,
        TakiElias\Tablar\Menu\Filters\LangFilter::class,
        TakiElias\Tablar\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Vite
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Vite support.
    |
    | For detailed instructions you can look the Vite here:
    | https://laravel-vite.dev
    |
    */

    'vite' => false,

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://livewire.laravel.com
    |
    */

    'livewire' => false,
];
