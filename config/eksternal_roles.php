<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Role yang Termasuk Eksternal Perusahaan
    |--------------------------------------------------------------------------
    | Daftar role yang bila dimiliki oleh user, maka user tersebut otomatis
    | dianggap sebagai eksternal dan harus ada di tabel masing-masing.
    */

    'roles' => [
        'Investor',
        'Tukang',
        'Mitra Kontraktor',
        'Mitra Supplier',
        'Mitra Arsitek',
        'Customer',
        'Affiliator',
    ],

     'models' => [
        'Investor' => App\Models\Investor::class,
        'Tukang' => App\Models\Worker::class,
        'Mitra Kontraktor' => App\Models\Contractor::class,
        'Mitra Supplier' => App\Models\Supplier::class,
        'Mitra Arsitek' => App\Models\Architect::class,
        'Customer' => App\Models\Customer::class,
        'Affiliator' => App\Models\Affiliator::class,
    ],
];
