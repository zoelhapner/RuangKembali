<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'ruangkembaliproject@gmail.com'],
            [
                'id' => Str::uuid(),
                'fullname' => 'Super Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => '08123456789',
                'remember_token' => Str::random(10),
            ]
        );

        $role = Role::where('name', 'Super-Admin')->first();

        if ($role && !$user->hasRole($role->name)) {
            $user->assignRole($role);
        }
    }
}
