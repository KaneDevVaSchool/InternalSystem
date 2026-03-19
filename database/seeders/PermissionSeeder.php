<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('permissions.guard', 'web');
        $definitions = config('permissions.definitions', []);
        $rolePermissions = config('permissions.roles', []);

        foreach ($definitions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard],
                ['name' => $name, 'guard_name' => $guard]
            );
        }

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => $guard],
                ['name' => $roleName, 'guard_name' => $guard]
            );
            $role->syncPermissions($permissions);
        }
    }
}
