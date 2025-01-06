<?php

namespace App\Repositories\Tenant;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Repositories\BaseRepository;
use App\Models\Tenant\{Event, User as TenantUser};

class EventRepository extends BaseRepository
{
    public function __construct(Event $model)
    {
        $this->model = $model;
    }

    function store(array $attributes)
    {
        $eventable_type = Event::getEventable($attributes['eventable_type']);
        $eventable_id = $eventable_type::findByUUIDOrFail($attributes['eventable_id'])->id;
        try {
            $event = Event::updateOrCreate(['uuid' => $attributes['uuid'] ?? null], [
                'title' => $attributes['title'],
                'user_id' => isset($attributes['user_id']) ? userId($attributes['user_id'])->id : request()->user()->tenant_user_id,
                'genre' => $attributes['genre'],
                'privacy' => $attributes['privacy'],
                'type' => $attributes['type'],
                'eventable_type' => $eventable_type,
                'address' => $attributes['address'],
                'open_registration' => Carbon::parse($attributes['open_registration'])->format('Y-m-d H:i:s'),
                'close_registration' => Carbon::parse($attributes['close_registration'])->format('Y-m-d H:i:s'),
                'start_date' => Carbon::parse($attributes['start_date'])->format('Y-m-d H:i:s'),
                'end_date' => Carbon::parse($attributes['end_date'])->format('Y-m-d H:i:s'),
                'eventable_id' => $eventable_id,
                'description' => $attributes['description'],
            ]);

            if (isset($attributes['event_cover_img'])) {
				$event
					->addMediaFromBase64($attributes['event_cover_img'])
					->usingFileName(Str::uuid() . '.' . ext_base64(base64: $attributes['event_cover_img']))
					->toMediaCollection('event_cover_img');
			}

            return json_response(200, "Event saved successfully.");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while saving the event. Please try again.", $e->getMessage());
        }
    }

    function delete($attributes)
    {
		try {
			foreach ($attributes as $uuid) {
				$this->destroy($uuid);
			}
			return json_response(200, "Events removed successfully");
		} catch (\Exception $e) {
			return json_response(500, "An error occurred while removing Events. Please try again later.");
		}
    }
}
