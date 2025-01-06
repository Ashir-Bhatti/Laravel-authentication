<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventCollection extends ResourceCollection
{
    public function toArray($request)
	{
		return [
            'events' => $this->collection->map(function ($event) {
                return [
                    'uuid' => $event->uuid,
                    'title' => $event->title,
                    'genre' => $event->genre,
                    'type' => $event->type,
                    'privacy' => $event->privacy,
                    'event_cover_img' => $event->event_cover_img_url,
                    'eventable' => [
                        'type' => class_basename($event->eventable_type),
                        'id' => $event->eventable_id,
                        'data' => $event->eventable ? [
                            'uuid' => $event->eventable->uuid ?? null,
                            'title' => $event->eventable->title ?? null,
                        ] : null,
                    ],
                    'creator' => [
                        'uuid' => optional($event->users)->uuid,
                        'username' => optional($event->users)->username,
                    ],
                    'attendees' => $event->attendees->map(function ($attendee) {
                        return [
                            'uuid' => $attendee->uuid,
                            'username' => $attendee->username,
                        ];
                    }),
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
