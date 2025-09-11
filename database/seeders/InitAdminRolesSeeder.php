<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Admin;

class InitAdminRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'admin',
        ]);

        $perm = Permission::firstOrCreate([
            'name' => 'workout-cards.manage',
            'guard_name' => 'admin',
        ]);

        if (! $role->hasPermissionTo($perm)) {
            $role->givePermissionTo($perm);
        }

        // Assign role to first admin if exists
        $admin = Admin::query()->first();
        if ($admin && ! $admin->hasRole($role)) {
            $admin->assignRole($role);
        }
    }
}
