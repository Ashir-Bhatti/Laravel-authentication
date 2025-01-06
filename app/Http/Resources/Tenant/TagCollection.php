<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TagCollection extends ResourceCollection
{
    public function toArray($request)
	{
		return [
            'tags' => $this->collection->map(function ($tag) {
                return [
                    'uuid' => $tag->uuid,
                    'name' => $tag->name,
                    'color' => $tag->color,
                    'deleted_at' => $tag->deleted_at,
                    'taggables' => $tag->taggables->map(function ($taggable) {
                        return [
                            'type' => class_basename($taggable->taggable_type),
                            'id' => $taggable->taggable_id,
                            'data' => $taggable->taggable ? [
                                'uuid' => $taggable->taggable->uuid ?? null,
                                'title' => $taggable->taggable->title ?? null,
                                'name' => $taggable->taggable->name ?? null,
                            ] : null,
                        ];
                    }),
                ];
            }),
            'meta' => [
                'total_records' => $this->total(),
                'per_page_count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
        ];
	}
}
