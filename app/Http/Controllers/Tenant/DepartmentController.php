<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\DepartmentRequest;
use App\Http\Resources\Tenant\DepartmentCollection;
use App\Models\Tenant\Department;
use App\Repositories\Tenant\DepartmentRepository;

class DepartmentController extends Controller
{
    function __construct(private DepartmentRepository $repository){}

    public function index()
    {
        return json_response(
            200,
            "Department Listing fetched from database.",
            DepartmentCollection::make(Department::search(request('search'))->paginate(request('rowsPerPage', 20)))
        );
    }

    public function store(DepartmentRequest $request)
    {
        return $this->repository->store($request->all());
    }

    public function delete(DepartmentRequest $request)
    {
        return json_response(200, "Department deleted Successfully.", $this->repository->destroyMultiple($request->uuid));
    }
}
