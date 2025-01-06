<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\CampaignType;
use Illuminate\Database\Seeder;

class CampaignTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campaignTypes = array(
            [
                'title' => 'Presidential Campaigns',
            ], [
                'title' => 'Congressional Campaigns',
            ], [
                'title' => 'Gubernatorial Campaigns',
            ], [
                'title' => 'State Legislative Campaigns',
            ], [
                'title' => 'Local Campaigns',
            ], [
                'title' => 'Referendum Campaigns',
            ], [
                'title' => 'Judicial Campaigns',
            ]
        );

        foreach ($campaignTypes as $campaignType) {
            CampaignType::create($campaignType);
        }
    }
}
