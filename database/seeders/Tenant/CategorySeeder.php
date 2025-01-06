<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = array(
            [
                'title' => 'Category 1',
            ], [
                'title' => 'Category 2',
            ], [
                'title' => 'Category 3',
            ], [
                'title' => 'Category 4',
            ]
        );

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
