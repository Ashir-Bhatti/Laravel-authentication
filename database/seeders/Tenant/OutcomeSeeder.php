<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Outcome;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OutcomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $outcomes = array(
            [
                'title' => 'Outcome 1',
            ], [
                'title' => 'Outcome 2',
            ], [
                'title' => 'Outcome 3',
            ], [
                'title' => 'Outcome 4',
            ]
        );

        foreach ($outcomes as $outcome) {
            Outcome::create($outcome);
        }
    }
}
