<?php

namespace Database\Seeders;

use Database\Seeders\Tenant\AttachPermissionToOwnerSeeder;
use Database\Seeders\Tenant\DepartmentSeeder;
use Database\Seeders\Tenant\PermissionSeeder;
use Database\Seeders\Tenant\PositionBoardSeeder;
use Database\Seeders\Tenant\CampaignTypeSeeder;
use Database\Seeders\Tenant\CategorySeeder;
use Database\Seeders\Tenant\OutcomeSeeder;
use Database\Seeders\Tenant\RoleSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(PositionBoardSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(CampaignTypeSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(OutcomeSeeder::class);
        $this->call(AttachPermissionToOwnerSeeder::class);
    }
}
