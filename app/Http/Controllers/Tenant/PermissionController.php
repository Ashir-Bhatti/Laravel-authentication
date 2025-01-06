<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\PermissionCollection;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;

class PermissionController extends Controller
{
    function index()
    {
        return json_response(
            200,
            "Permissions fetched successfully.",
            Permission::where('parent_id', 0)->with('children')->orderBy('id')->get(),
        );
    }

    function getAuthUserPermission()
    {
        $loggedInUserRole = Role::where('slug', request()->user()->role)->firstOrFail();
        return response()->json([
            'status' => 200,
            'message' => 'Permissions fetched successfully.',
            'data' => new PermissionCollection($loggedInUserRole->permissions),
        ], 200);
    }
}
