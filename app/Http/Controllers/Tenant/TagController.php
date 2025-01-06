<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Tag;
use App\Models\Tenant\Taggable;

use App\Repositories\Tenant\TagRepository;
use App\Http\Requests\Tenant\TagRequest;
use App\Http\Resources\Tenant\TagCollection;

class TagController extends Controller
{
    function __construct(private TagRepository $repository){}

    function index(TagRequest $request)
    {
        $tags = Tag::with(['taggables.tag'])->latest();

        return response()->json([
            'message' => 'Tags fetched successfully', TagCollection::make($tags->paginate(request('rowsPerPage', 20))),
        ]);
    }

    public function store(TagRequest $request)
    {
        $attributes = [];
        $attributes['uuid'] = $request['uuid'] ?? null;
        $attributes['name'] = $request['name'];
        $attributes['color'] = $request['color'];

        return $this->repository->store($attributes);
    }

    public function syncTags(TagRequest $request)
    {
        try {
            $taggableType = Tag::getTaggable($request->type);
            $tasks = $taggableType::whereIn('uuid', $request->task_uuid)->get();

            foreach ($tasks as $task) {
                $task->tags()->detach();

                if (!empty($request->tag_uuid)) {
                    $tags = Tag::getByUUID($request->tag_uuid)->pluck('id');
                    if ($tags->isNotEmpty()) {
                        $task->tags()->attach($tags);
                    }
                }
            }

            return json_response(200, "Tags updated successfully.");
        } catch (\Exception $e) {
            return json_response(500, "An error occurred while updating tags. Please try again.");
        }
    }

    function delete(TagRequest $request)
	{
		return $this->repository->delete(request('uuid'));
	}
}
