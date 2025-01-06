<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Comment;

use App\Repositories\Tenant\CommentRepository;
use App\Http\Requests\Tenant\CommentRequest;
use App\Http\Resources\Tenant\CommentCollection;

class CommentController extends Controller
{
    function __construct(private CommentRepository $repository){}

    public function index(CommentRequest $request)
    {
        $comments = Comment::with(['user', 'commentable']);

        if (request()->has('type')) {
            $commentableType = Comment::getCommentable($request->type);
            $comments = $comments->where('commentable_type', $commentableType);
        }

        if (request()->has('uuid')) {
            $commentableType = Comment::getCommentable($request->type);
            $commentableId = $commentableType::where('uuid', $request->uuid)->value('id');

            if ($commentableId) {
                $comments = $comments->where('commentable_id', $commentableId);
            }
        }

        if (request()->has('search')) {
            $search = $request->search;
            $comments = $comments->where('body', 'LIKE', "%$search%");
        }

        return response()->json([
            'message' => 'Comment Listing fetched from database.',
            'data' => CommentCollection::make($comments->paginate($request->input('rowsPerPage', 20))),
        ]);
    }

    public function store(CommentRequest $request)
    {
        return $this->repository->store($request->all());
    }

    function delete(CommentRequest $request)
	{
		return $this->repository->delete(request('uuid'));
	}
}
