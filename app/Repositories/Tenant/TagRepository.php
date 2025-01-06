<?php

namespace App\Repositories\Tenant;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\{DB, Hash};
use App\Models\Tenant\{Tag, User as TenantUser};

class TagRepository extends BaseRepository
{
    public function __construct(Tag $model)
    {
        $this->model = $model;
    }

    public function store(array $attributes)
    {
        try {
            $this->model->updateOrCreate(['uuid' => $attributes['uuid'] ?? null], [
                'name' => $attributes['name'],
                'color' => $attributes['color'],
            ]);

            return json_response(200, "Tag successfully saved.");

        } catch (\Exception $e) {
            return json_response(500, "An error occurred while saving the tag. Please try again.");
        }
    }

    function delete($attributes)
    {
		try {
			foreach ($attributes as $uuid) {
				$this->destroy($uuid);
			}
			return json_response(200, "Tags removed successfully");
		} catch (\Exception $e) {
			return json_response(500, "An error occurred while removing tag. Please try again later.");
		}
    }
}
