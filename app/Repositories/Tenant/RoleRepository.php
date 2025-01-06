<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    function store(array $attributes)
    {
        try {
            $role = Role::updateOrCreate([
                "uuid" =>  $attributes['uuid'] ?? null
            ],[
                "name" => $attributes['name']
            ]);
            $role->permissions()->detach();
            if (isset($attributes['permission_ids']) && count($attributes['permission_ids']) > 0) {
                $permissionIds = Permission::getByUUID($attributes['permission_ids'])->pluck('id');
                $role->permissions()->attach($permissionIds);
            }
            return json_response(200, "Role added successfully.");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while creating the role. Please try again.");
        }
    }
}
