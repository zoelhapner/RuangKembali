<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class SetupRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        DB::statement('SET session_replication_role = replica');

        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();

        DB::statement('SET session_replication_role = DEFAULT');

        $permissions = $this->permissions();

        foreach ($permissions as $name => $module) {
            Permission::create([
                'id' => Str::uuid(),
                'name' => $name,
                'guard_name' => 'web',
                'modules' => $module,
            ]);
        }

        $roles = $this->roles();

        foreach ($roles as $group => $items) {
            foreach ($items as $role) {
                Role::create([
                    'id' => Str::uuid(),
                    'name' => $role,
                    'guard_name' => 'web',
                    'role_group' => $group,
                ]);
            }
        }

        Role::where('name', 'Super-Admin')
            ->first()
            ->syncPermissions(Permission::all());
    }

private function permissions(): array
{

}

    private function roles(): array
    {
        return [
            'Internal' => [
                'Super-Admin',
                'Direktur',
                'Manager Administrasi',
                'Manager Teknik',
                'Supervisor Marketing',
                'Supervisor Finance',
                'Supervisor HRD',
                'Supervisor Principal Arsitek',
                'Spv Sipil',
                'Staff Marketing',
                'Staff Finance',
                'Staff HRD',
                'Drafter',
                'QC',
                'Estimator',
            ],
            'Eksternal' => [
                'Investor',
                'Tukang',
                'Mitra Kontraktor',
                'Mitra Supplier',
                'Mitra Arsitek',
                'Customer',
                'Affiliator',
            ],
        ];
    }
}
