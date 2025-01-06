<?php 

namespace App\Traits;

use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermission
{
    // Get permissions
    public function getAllPermissions(array $permission): Collection
    {
        return Permission::whereIn('slug', $permission)->get();
    }

    // Check has permission
    public function hasPermission(Permission $permission): bool
    {
        return (bool) $this->permissions->where('slug', $permission->slug)->count();
    }

    // Check has role 
    public function hasRole(string ...$roles): bool
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermissionTo(Permission $permission): bool
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }
    
    public function hasPermissionThroughRole(Permission $permissions): bool
    {
        foreach ($permissions->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    // Give permission 
    public function givePermissionTo(string ...$permissions): self
    {
        $permissions = $this->getAllPermissions($permissions);
        if ($permissions->isEmpty()) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    // Relation with permission
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }
}
