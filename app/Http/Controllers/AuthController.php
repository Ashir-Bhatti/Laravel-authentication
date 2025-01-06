<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Tenant\Role;
use App\Models\Tenant\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function login (Request $request)
    {
        $guard = Auth::guard(Arr::first(config('sanctum.guard')));
        if( ! $guard->attempt($request->all())) {
            return json_response(500, "Invalid credentials.");
        }

        /**
         * Since we successfully logged in, this can no longer be `null`.
         *
         * @var \App\Models\User $user
         */
        $user = $guard->user();

        if($user->role !== "super_admin"){
			$organization = Organization::where("tenant_id", $user->tenant_id)
				->whereHas("subDetails", function ($query)  {
					$query->where("status", "active");
				})
				->first();


            tenancy()->initialize($user->tenant_id);
            $role = Role::where("slug", $user->role)->with('permissions.parent')->first();
            $moduleNames = $role ? $role->permissions->pluck('parent_slug')->filter()->unique()->values()->toArray() : [];

            $myUser = User::whereId($user->tenant_user_id)->select("uuid", "fname", "lname", "email")->first();
            $user['user_info'] = $myUser;
            $user['modules'] = $moduleNames;

			if(!$organization){
				return json_response(405, "Your subscription is not active, Please contact support team");
			}
		}
        tenancy()->end();
        $user['access_token'] = $user->createToken('personal_token')->plainTextToken;
        return json_response(200, "User LoggedIn successfully.", $user);
    }

    function logout (Request $request)
    {
        $request->user()->tokens()->delete();

        return json_response(200, "User Logged Out successfully.");
    }
}
