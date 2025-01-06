<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttachPermissionToOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'Owner')->first();
        $permissions = Permission::where('parent_id', 0)->get();
        foreach ($permissions as $permission) {
            foreach ($permission->children as $child) {
                $role->permissions()->attach($child->id);
            }
        }
    }
}
