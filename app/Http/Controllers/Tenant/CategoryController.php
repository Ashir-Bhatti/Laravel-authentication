<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CategoryRequest;
use App\Http\Resources\Tenant\CategoryCollection;
use App\Models\Tenant\Category;
use App\Repositories\Tenant\CategoryRepository;

class CategoryController extends Controller
{
    function __construct(private CategoryRepository $repository) {}

    public function index()
    {
        return json_response(
            200,
            'Categories fetched successfully!',
            CategoryCollection::make(Category::search(request('search'))->paginate(request('rowsPerPage', 20)))
        );
    }

    public function store(CategoryRequest $request)
    {
        return $this->repository->store($request->all());
    }

    public function delete(CategoryRequest $request)
    {
        return $this->repository->destroyMultiple($request->uuid);
    }
}
