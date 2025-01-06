<?php

namespace App\Repositories\Tenant;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\{DB, Hash};
use App\Models\Tenant\{Task, User as TenantUser};

class TaskRepository extends BaseRepository
{
    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    function store(array $attributes)
    {
        try {
            Task::updateOrCreate(['uuid' => $attributes['uuid'] ?? null], [
				'title' => $attributes['title'],
				'description' => $attributes['description'],
				'status' => $attributes['status'],
				'priority' => $attributes['priority'],
				'assignee_id' => userId($attributes['assignee_id'])->id ?? null,
				'reporter_id' => isset($attributes['reporter_id']) ? userId($attributes['reporter_id'])->id : request()->user()->tenant_user_id,
				'start_date' => Carbon::parse($attributes['start_date'])->format('Y-m-d H:i:s'),
				'due_date' => Carbon::parse($attributes['due_date'])->format('Y-m-d H:i:s'),
			]);

            return json_response(200, "Task saved successfully.");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while creating the task. Please try again.");
        }
    }

    function delete($attributes)
    {
		try {
			foreach ($attributes as $uuid) {
				$this->destroy($uuid);
			}
			return json_response(200, "Tasks removed successfully");
		} catch (\Exception $e) {
			return json_response(500, "An error occurred while removing tasks. Please try again later.");
		}
    }

    function updateTaskStatus(array $attributes, $uuid = null)
    {
        try {
            DB::beginTransaction();

            $task = $this->show($uuid);

            $task->update([
                'important_at' => $attributes['important_at'] ? Carbon::now()->format('Y-m-d H:i:s') : null,
                'read_at' => $attributes['read_at'] ? Carbon::now()->format('Y-m-d H:i:s') : null,
                'completed_at' => $attributes['completed_at'] ? Carbon::now()->format('Y-m-d H:i:s') : null,
                'favorite_at' => $attributes['favorite_at'] ? Carbon::now()->format('Y-m-d H:i:s') : null,
            ]);

            DB::commit();

            return json_response(200, "Task updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return json_response(500, "An error occurred while updating the task. Please try again.");
        }
    }
}
