<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TaskCollection extends ResourceCollection
{
    public function toArray($request)
	{
		return [
            'tasks' => $this->collection->map(function ($task) {
                return [
                    'uuid' => $task->uuid,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date,
                    'start_date' => $task->start_date,
                    'completed_at' => $task->completed_at,
                    'favorite_at' => $task->favorite_at,
                    'read_at' => $task->read_at,
                    'important_at' => $task->important_at,
                    'comments' => $task->comments->map(function ($comment) {
                        return [
                            'uuid' => $comment->uuid,
                            'body' => $comment->body,
                            'author' => [
                                'uuid' => optional($comment->user)->uuid,
                                'username' => optional($comment->user)->username,
                            ],
                            'created_at' => $comment->created_at,
                            'updated_at' => $comment->updated_at,
                        ];
                    }),
                    'assigned_to' => [
                        'uuid' => optional($task->assignee)->uuid,
                        'username' => optional($task->assignee)->username,
                    ],
                    'reported_by' => [
                        'uuid' => optional($task->reporter)->uuid,
                        'username' => optional($task->reporter)->username,
                    ]
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
