<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ActivityLogCollection extends ResourceCollection
{
    public function toArray($request)
	{
		return [
            'logs' => $this->collection->map(function ($log) {
                return [
                    'uuid' => $log->event,
                    'description' => $log->description,
                    'subject_type' => $log->subject_type,
                    'causer_type' => $log->causer_type,
                    'causer_id' => $log->causer_id,
                    'properties' => $log->properties,
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
