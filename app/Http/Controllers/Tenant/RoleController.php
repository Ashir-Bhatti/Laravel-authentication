<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RoleRequest;
use App\Http\Resources\Tenant\RoleCollection;
use App\Models\Tenant\Role;
use App\Models\Tenant\User;
use App\Repositories\Tenant\RoleRepository;

class RoleController extends Controller
{
    function __construct(private RoleRepository $repository){}

    function index()
    {
        return json_response(
            200,
            "Role Listing fetched from database.",
            RoleCollection::make(Role::search(request('search'))->paginate(request('rowsPerPage')))
        );
    }

    function store(RoleRequest $request)
    {
        return $this->repository->store($request->all());
    }

    function delete($uuid)
    {
        $role = $this->repository->show($uuid);
        $user = User::whereHas('roles', function ($query) use ($role) {
            $query->where('id', $role->id);
        })->first();
        if ($user) {
            return json_response(403, "Role has assigned to user.");
        }
        return json_response(200, "Role deleted Successfully.", $this->repository->destroy($uuid));
    }
}
