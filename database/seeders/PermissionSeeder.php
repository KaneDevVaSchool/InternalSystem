<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('permissions.guard', 'web');
        $definitions = config('permissions.definitions', []);
        $rolePermissions = config('permissions.roles', []);

        if (class_exists(\Spatie\Permission\Models\Permission::class)) {
            $this->seedWithSpatie($guard, $definitions, $rolePermissions);
        } else {
            $this->seedWithModels($guard, $definitions, $rolePermissions);
        }
    }

    private function seedWithSpatie(string $guard, array $definitions, array $rolePermissions): void
    {
        $permissionModel = new \Spatie\Permission\Models\Permission;
        $roleModel = new \Spatie\Permission\Models\Role;

        foreach ($definitions as $name => $description) {
            $permissionModel->firstOrCreate(
                ['name' => $name, 'guard_name' => $guard],
                ['name' => $name, 'guard_name' => $guard]
            );
        }

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = $roleModel->firstOrCreate(
                ['name' => $roleName, 'guard_name' => $guard],
                ['name' => $roleName, 'guard_name' => $guard]
            );
            $role->syncPermissions($permissions);
        }
    }

    private function seedWithModels(string $guard, array $definitions, array $rolePermissions): void
    {
        foreach ($definitions as $name => $description) {
            \App\Models\Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard],
                ['name' => $name, 'guard_name' => $guard]
            );
        }

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = \App\Models\Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => $guard],
                ['name' => $roleName, 'guard_name' => $guard]
            );
            $permIds = \App\Models\Permission::whereIn('name', $permissions)
                ->where('guard_name', $guard)
                ->pluck('id');
            $role->permissions()->syncWithoutDetaching($permIds);
        }
    }

}
