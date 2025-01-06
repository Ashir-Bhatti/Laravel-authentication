<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
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
            'categories' => $this->getData(),
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

    private function getData()
    {
        return $this->collection->map(function ($category) {
            return [
                'uuid' => $category->uuid,
                'title' => $category->title,
                'slug' => $category->slug,
                'created_by' => $category->createdBy ? $category->createdBy->full_name : '',
            ];
        });
    }
}
