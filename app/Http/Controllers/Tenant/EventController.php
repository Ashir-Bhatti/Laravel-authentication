<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\{Campaign, Event, User as TenantUser};
use App\Http\Requests\Tenant\EventRequest;
use App\Http\Resources\Tenant\EventCollection;

use App\Repositories\Tenant\EventRepository;

class EventController extends Controller
{
    function __construct(private EventRepository $repository){}

    function index(EventRequest $request)
    {
        $events = Event::with(['users', 'attendees']);

		if (request('user_uuid')) {
            $events = $events->where(function($query) {
                $query->whereHas('users', function($q) {
                    $q->where('uuid', request('user_uuid'));
                })
                ->orWhereHas('attendees', function($q) {
                    $q->where('uuid', request('user_uuid'));
                });
            });
        }

        $search = request('search');
        $events = $events->whereAny(['title', 'description'], 'LIKE', "%$search%");

		return json_response(200, "Event Listing fetched from database.", EventCollection::make($events->paginate(request('rowsPerPage', 20))));
    }

    function store(EventRequest $request)
    {
        return $this->repository->store($request->all());
    }

    function delete(EventRequest $request)
	{
		return $this->repository->delete(request('uuid'));
	}

    public function attachUsersToEvent(EventRequest $request)
    {
        try {
            $users = TenantUser::whereIn('uuid', $request->user_uuid)->get();

            foreach ($users as $user) {
                $user->events()->detach();

                if ($request->has('event_uuid')) {
                    $eventIds = Event::whereIn('uuid', $request->event_uuid)->pluck('id');

                    $user->events()->attach($eventIds);
                }
            }

            return json_response(200, "Event updated successfully.");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while updating events. Please try again.");
        }
    }
}
