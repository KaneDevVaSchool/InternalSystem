<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    protected ?string $guardName = 'web';

    public function roles(): BelongsToMany
    {
        return $this->morphToMany(
            Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id'
        );
    }

    public function hasRole(string|array $roles): bool
    {
        return $this->hasAnyRole(is_array($roles) ? $roles : [$roles]);
    }

    public function hasAnyRole(array $roles): bool
    {
        if (empty($roles)) {
            return false;
        }
        $roleNames = $this->getRoleNames()->toArray();
        foreach ($roles as $role) {
            if (in_array($role, $roleNames, true)) {
                return true;
            }
        }
        return false;
    }

    public function getRoleNames(): Collection
    {
        return $this->roles()->pluck('name');
    }

    public function getAllPermissions(): Collection
    {
        $perms = \Illuminate\Support\Facades\DB::table('role_has_permissions')
            ->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('model_has_roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('model_has_roles.model_type', static::class)
            ->where('model_has_roles.model_id', $this->getKey())
            ->select('permissions.name')
            ->distinct()
            ->pluck('name');

        return collect($perms)->map(fn (string $name) => (object) ['name' => $name]);
    }

    public function syncRoles(array $roles): self
    {
        $guard = config('permissions.guard', 'web');
        $roleIds = \Illuminate\Support\Facades\DB::table('roles')
            ->whereIn('name', $roles)
            ->where('guard_name', $guard)
            ->pluck('id');

        \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->where('model_type', static::class)
            ->where('model_id', $this->getKey())
            ->delete();

        foreach ($roleIds as $roleId) {
            \Illuminate\Support\Facades\DB::table('model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => static::class,
                'model_id' => $this->getKey(),
            ]);
        }

        return $this;
    }
}
