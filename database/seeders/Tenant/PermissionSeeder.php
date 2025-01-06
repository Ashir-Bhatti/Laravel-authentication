<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Permission;
Use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
			[
				'name' => 'Campaign',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				],
			],
			[
				'name' => 'Task',
				'children' => [
                    ['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'Fundraising',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'Analytics',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'Media',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'User',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'Chat Room',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'Direct Message',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'Shared Data',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'CampHq School',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'Calender',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'Collaboration',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
			[
				'name' => 'Role And Permission',
				'children' => [
					['name' => 'View'],['name' => 'Create'],['name' => 'Edit'],['name' => 'Delete']
				]
			],
		];

		foreach ($permissions as $perm) {
			$perm1 = [
				'name' => $perm['name'],
				'parent_id' => 0
			];
			$permission = Permission::create($perm1);

			if ($permission) {
				foreach ($perm['children'] as $child) {
					$child['parent_id'] = $permission->id;
					$child['slug'] = Str::slug($child['name'] . ' ' . $permission->name);
					Permission::create($child);
				}
			}
		}
	}
}
