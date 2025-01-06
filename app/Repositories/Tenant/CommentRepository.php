<?php

namespace App\Repositories\Tenant;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\{DB, Hash};
use App\Models\Tenant\{Comment, User as TenantUser};

class CommentRepository extends BaseRepository
{
    public function __construct(Comment $model)
    {
        $this->model = $model;
    }

    function store(array $attributes)
    {
        $commentableType = Comment::getCommentable($attributes['type']);
        $commentableId = $commentableType::findByUUIDOrFail($attributes['commentable_id'])->id;
        try {
            $this->model->updateOrCreate(['uuid' => $attributes['uuid'] ?? null], [
                'commentable_type' => $commentableType,
                'commentable_id' => $commentableId,
                'body' => $attributes['body'],
                'user_id' => request()->user()->tenant_user_id,
            ]);
            return json_response(200, "Comment successfully saved.");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while saving the comment. Please try again.", $e->getMessage());
        }
    }

    public function delete($attributes)
    {
        try {
            foreach ($attributes as $uuid) {
                $this->deleteWithChildren($uuid);
            }
            return json_response(200, "Comments removed successfully");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while removing comment. Please try again later.");
        }
    }

    private function deleteWithChildren($uuid)
    {
        $comment = Comment::where('uuid', $uuid)->first();

        if ($comment) {
            $childComments = Comment::where('commentable_type', get_class($comment))
                                    ->where('commentable_id', $comment->id)
                                    ->get();

            foreach ($childComments as $child) {
                $this->deleteWithChildren($child->uuid);
            }

            $comment->delete();
        }
    }
}
