<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UserRequest;
use App\Http\Resources\Tenant\UserCollection;
use App\Models\Tenant\User as TenantUser;
use App\Repositories\Tenant\UserRepository;
class UserController extends Controller
{
	function __construct(private UserRepository $repository){}

    function index(UserRequest $request)
    {
		$users = TenantUser::with('roles', 'positionBoard')->where('id', '<>', request()->user()->tenant_user_id);

		$users = request('uuid') ? $users->whereUUID(request('uuid')) : $users;

		$users = request('role_id') ? $users->roleString(request('role_id')) : $users;
		$users = request('position_board_id') ? $users->positionBoardString(request('position_board_id')) : $users;

		$search = request('search');
		$users = $users->whereAny(['fname', 'lname', 'email'], 'LIKE', "%$search%");

		return json_response(200, __('Organization.get_data'), UserCollection::make($users->paginate(request('rowsPerPage', 20))));
    }

    function store(UserRequest $request)
    {
		return $this->repository->store($request->all());
    }

	function update(UserRequest $request)
	{
		$uuid = request()->query('uuid');
		return $this->repository->update($request->all(), $uuid);
	}

	function delete(UserRequest $request)
	{
		return $this->repository->delete(request('uuid'));
	}
}
