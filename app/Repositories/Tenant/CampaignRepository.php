<?php

namespace App\Repositories\Tenant;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Tenant\Campaign;
use App\Repositories\BaseRepository;

class CampaignRepository extends BaseRepository
{
    public function __construct(Campaign $model)
    {
        $this->model = $model;
    }

    public function store(array $attributes)
    {
        try {
            $campaign = Campaign::updateOrCreate([
                "uuid" => $attributes['uuid'] ?? null,
            ], [
                "owner_id" => userId($attributes['owner_id'])->id,
                "type_id" => getIdByUUID($attributes['type_id'], 'campaignType'),
                "user_id" => request()->user()->tenant_user_id,
                "title" => $attributes['title'],
                "umt_code" => $attributes['umt_code'],
                "outcome_id" => isset($attributes['outcome_id']) ? getIdByUUID($attributes['outcome_id'], 'outcome') : null,
                "description" => $attributes['description'],
                "category_id" => getIdByUUID($attributes['category_id'], 'category'),
                "department_id" => getIdByUUID($attributes['department_id'], 'department'),
                "start_date" => Carbon::parse($attributes['start_date'])->format('Y-m-d H:i:s'),
                "end_date" => Carbon::parse($attributes['end_date'])->format('Y-m-d H:i:s'),
                "status" => $attributes['status'] ?? 1,
            ]);

			if (isset($attributes['file'])) {
				$campaign
					->addMediaFromBase64($attributes['file'])
					->usingFileName(Str::uuid() . '.' . ext_base64(base64: $attributes['file']))
					->toMediaCollection('campaign_file');
			}

            return json_response(200, "Campaign Type saved successfully.");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while creating the campaign type. Please try again.");
        }
    }

}
