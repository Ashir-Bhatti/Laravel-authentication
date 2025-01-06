<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CampaignCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'campaigns' => $this->collection->map(function ($campaign) {
                return [
                    'uuid' => $campaign->uuid,
                    'title' => $campaign->title,
                    'slug' => $campaign->slug,
                    'start_date' => $campaign->start_date,
                    'end_date' => $campaign->end_date,
                    'status' => $campaign->status,
                    'owner' => [
                        'uuid' => $campaign->owner->uuid,
                        'full_name' => $campaign->owner->full_name,
                    ],
                    'campaign_type' => [
                        'uuid' => $campaign->campaignType->uuid,
                        'title' => $campaign->campaignType->title,
                    ],
                    'department' => [
                        'uuid' => $campaign->department->uuid ?? null,
                        'title' => $campaign->department->title ?? null,
                    ],
                    'created_by' => [
                        'uuid' => $campaign->createdBy->uuid,
                        'full_name' => $campaign->createdBy->full_name,
                    ],
                    'campaign_file_url' => $campaign->campaign_file_url,
                ];
            }),
            'meta' => [
                'total_record' => $this->total(),
                'per_page_counts' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
        ];
    }
}
