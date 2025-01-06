<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = array(
            [
                'title' => 'Campaign Leadership',
            ],[
                'title' => 'Finance',    
            ],[
                'title' => 'Fundraising',
            ],[
                'title' => 'Communications',
            ], [
                'title' => 'Digital and Social Media',
            ], [
                'title' => 'Policy and Research',
            ], [
                'title' => 'Field Operations',
            ], [
                'title' => 'Voter Outreach and Engagement',
            ], [
                'title' => 'Legal and Compliance',
            ]
        );

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
