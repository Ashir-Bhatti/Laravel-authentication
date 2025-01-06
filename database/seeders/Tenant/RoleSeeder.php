<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleArr = array([
				'name' => 'Owner'
			],[
				'name' => 'Campaign Manager'
			],[
				'name' => 'Director'
			],[
				'name' => 'Treasurer'
			],[
				'name' => 'Staffer'
			],[
				'name' => 'Deputy Campaign Manager'
			],[
				'name' => 'Volunteers'
			]
			);
			
        foreach ($roleArr as $role) {
            Role::create($role);
        }
    }
}
