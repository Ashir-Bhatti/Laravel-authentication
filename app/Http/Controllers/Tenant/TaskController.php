<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\{Task, Comment, User as TenantUser};
use App\Http\Requests\Tenant\TaskRequest;
use App\Http\Resources\Tenant\TaskCollection;

use App\Repositories\Tenant\TaskRepository;

class TaskController extends Controller
{
    function __construct(private TaskRepository $repository){}

    function index(TaskRequest $request)
    {
        $tasks = Task::with(['comments.user', 'assignee', 'reporter']);

		if (request('user_uuid')) {
            $tasks = $tasks->where(function($query) {
                $query->whereHas('assignee', function($q) {
                    $q->where('uuid', request('user_uuid'));
                })
                ->orWhereHas('reporter', function($q) {
                    $q->where('uuid', request('user_uuid'));
                });
            });
        }

        $search = request('search');
        $tasks = $tasks->whereAny(['title', 'description'], 'LIKE', "%$search%");

        $tasks = $tasks->when($request->has('is_important') && $request->is_important == '1', function ($query) {
            return $query->whereNotNull('important_at');
        })->when($request->has('is_read') && $request->is_read == '1', function ($query) {
            return $query->orWhereNotNull('read_at');
        })->when($request->has('is_complete') && $request->is_complete == '1', function ($query) {
            return $query->orWhereNotNull('completed_at');
        })->when($request->has('is_favorite') && $request->is_favorite == '1', function ($query) {
            return $query->orWhereNotNull('favorite_at');
        });

		return json_response(200, "Task Listing fetched from database.", TaskCollection::make($tasks->paginate(request('rowsPerPage', 20))));
    }

    function store(TaskRequest $request)
    {
		return $this->repository->store($request->all());
    }

    function delete(TaskRequest $request)
	{
		return $this->repository->delete(request('uuid'));
	}

    function updateTaskStatus(TaskRequest $request)
    {
		$uuid = request()->query('uuid');
		return $this->repository->updateTaskStatus($request->all(), $uuid);
    }
}
