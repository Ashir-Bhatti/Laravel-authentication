<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\Category;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function store(array $attributes)
    {
        try {
            Category::updateOrCreate([
                "uuid" => $attributes['uuid'] ?? null,
            ], [
                "title" => $attributes['title'],
                "status" => $attributes['status'] ?? 1,
                "user_id" => request()->user()->tenant_user_id,
            ]);

            return json_response(200, "Category saved successfully.");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while creating the category. Please try again.");
        }
    }

}
