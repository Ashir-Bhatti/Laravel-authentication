<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\Department;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;

class DepartmentRepository extends BaseRepository
{
    public function __construct(Department $model)
    {
        $this->model = $model;
    }

    public function store(array $attributes)
    {
        try {
            Department::updateOrCreate([
                "uuid" => $attributes['uuid'] ?? null,
            ], [
                "title" => $attributes['title'],
                "status" => $attributes['status'] ?? 1,
                "user_id" => request()->user()->tenant_user_id,
                "department_type" => $attributes['department_type'] ?? 'internal',
            ]);

            return json_response(200, "Department added successfully.");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while creating the department. Please try again.");
        }
    }

}
