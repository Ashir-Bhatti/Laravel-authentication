<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CampaignTypeCollection extends ResourceCollection
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
            'campaign_types' => $this->getData(),
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

    public function getData()
    {
        $data = [];
        foreach ($this->collection as $item) {
            $data[] = [
                'uuid' => $item->uuid,
                'title' => $item->title,
                'slug' => $item->slug,
                'status' => $item->status,
				'created_by' => $item->createdBy ? $item->createdBy->full_name : '',
            ];
        }
        return $data;
    }
}
