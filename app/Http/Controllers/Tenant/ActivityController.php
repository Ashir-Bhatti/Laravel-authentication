<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Activity;
use App\Http\Requests\Tenant\ActivityLogRequest;
use App\Http\Resources\Tenant\ActivityLogCollection;

class ActivityController extends Controller
{
    function index(ActivityLogRequest $request)
    {
        $subjectType = $request->query('type');
        $uuid = $request->query('uuid');

        $activities = Activity::when($subjectType, function ($query) use ($subjectType) {
            return $query->where('subject_type', 'LIKE', "%$subjectType%");
        })->when($uuid, function ($query) use ($uuid) {
            return $query->whereHas('subject', function ($subQuery) use ($uuid) {
                $subQuery->where('uuid', $uuid);
            });
        });

        return json_response(200, "Activity Logs fetched from database.", ActivityLogCollection::make($activities->paginate(request('rowsPerPage', 20))));
    }
}