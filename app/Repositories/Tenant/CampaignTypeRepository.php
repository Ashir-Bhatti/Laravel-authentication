<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant\CampaignType;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;

class CampaignTypeRepository extends BaseRepository
{
    public function __construct(CampaignType $model)
    {
        $this->model = $model;
    }

    public function store(array $attributes)
    {
        try {
            CampaignType::updateOrCreate([
                "uuid" => $attributes['uuid'] ?? null,
            ], [
                "title" => $attributes['title'],
                "user_id" => request()->user()->tenant_user_id,
                "status" => $attributes['status'] ?? 1,
            ]);

            return json_response(200, "Campaign Type saved successfully.");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while creating the campaign type. Please try again.");
        }
    }
}
