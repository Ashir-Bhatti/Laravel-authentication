<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentCollection extends ResourceCollection
{
    public function toArray($request)
	{
		return [
            'comments' => $this->collection->map(function ($comment) {
                return [
                    'uuid' => $comment->uuid,
                    'body' => $comment->body,
                    'deleted_at' => $comment->deleted_at,
                    'commentable' => [
                        'type' => class_basename($comment->commentable_type),
                        'id' => $comment->commentable_id,
                        'data' => $comment->commentable ? [
                            'uuid' => $comment->commentable->uuid ?? null,
                            'title' => $comment->commentable->title ?? null,
                            'name' => $comment->commentable->name ?? null,
                        ] : null,
                    ],
                    'author' => [
                        'uuid' => optional($comment->user)->uuid,
                        'username' => optional($comment->user)->username,
                    ],
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
