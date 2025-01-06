<?php

namespace App\Repositories\Tenant;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\OnBoardingEmail;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\{DB, Hash, Mail};
use App\Models\Tenant\{PositionBoard, Role, User as TenantUser};

class UserRepository extends BaseRepository
{
    public function __construct(TenantUser $model)
    {
        $this->model = $model;
    }

	function store(array $attributes)
	{
		// $password = Str::random(12);
		$password = 'password';
		$tenantId = request()->user()->tenant_id;
		
		try {
			tenancy()->end();
			$authUser = User::where('email', $attributes['email'])
							->orWhere('username', $attributes['username'])
							->first();
		
			if ($authUser) {
				if ($authUser->email == $attributes['email']) {
					return json_response(403, __('Organization.email_error'));
				} else {
					return json_response(403, __('Organization.email_error'));
				}
			}

			tenancy()->initialize($tenantId);

			$role = Role::findByUUID($attributes['role_id']);
			$positionBoard = PositionBoard::findByUUID($attributes['position_board_id']);

			DB::beginTransaction();
		
			$user = TenantUser::create([
				'fname' => $attributes['fname'],
				'lname' => $attributes['lname'],
				'username' => $attributes['username'],
				'email' => $attributes['email'],
				'phone' => $attributes['phone'],
				'address' => $attributes['address'],
				'position_board_id' => $positionBoard->id,
				'term_start_date' => Carbon::parse($attributes['term_start_date'])->format('Y-m-d H:i:s'),
				'term_end_date' => Carbon::parse($attributes['term_end_date'])->format('Y-m-d H:i:s'),
			]);

			if (!$user) {
				return json_response(500, __('Organization.creation_error'));
			}

			if (isset($attributes['avatar_image'])) {
				$user
					->addMediaFromBase64($attributes['avatar_image'])
					->usingFileName(Str::uuid() . '.' . ext_base64($attributes['avatar_image']))
					->toMediaCollection('avatar');
			}

			DB::commit();
			tenancy()->end();

			User::create([
				'username' => $user->username,
				'email' => $user->email,
				'password' => Hash::make($password),
				'phone' => $user->phone,
				'tenant_user_id' => $user->id,
				'tenant_id' => $tenantId,
				'role' => $role->slug,
			]);

			tenancy()->initialize($tenantId);

			$user->roles()->attach($role->id);

			Mail::to($user->email)->send(new OnBoardingEmail($user->email, $password));

			return json_response(200, "User Added Successfully.");
		} catch (\Exception $e) {
			DB::rollBack();
			return json_response(500, 'An error occurred. Please try again later.');
		}
	}

	function update(array $attributes, $uuid = null)
	{
		try {
			$tenantId = request()->user()->tenant_id;

			$user = TenantUser::whereUUID($uuid)->with('roles')->first();
			if (!$user) {
				return json_response(404, 'User not found.');
			}

			tenancy()->end();
			$authUser = User::where('tenant_user_id', '!=', $user->id)
							->where(function ($query) use ($attributes) {
								$query->where('email', $attributes['email'])
									->orWhere('username', $attributes['username']);
							})->first();
		
			if ($authUser) {
				if ($authUser->email == $attributes['email']) {
					return json_response(403, __('Organization.email_error'));
				} else {
					return json_response(403, __('Organization.username_error'));
				}
			}

			tenancy()->initialize($tenantId);

			$role = null;
			if (isset($attributes['role_id'])) {
				$role = Role::findByUUID($attributes['role_id']);
			}
		
			$x = $y = 0;

			$user->fname = $attributes['fname'];
			$user->lname = $attributes['lname'];
			$user->phone = $attributes['phone'];

			if ($user->email != $attributes['email']) {
				$user->email = $attributes['email'];
				$x = 1;
			}

			if ($user->username != $attributes['username']) {
				$user->username = $attributes['username'];
				$y = 1;
			}

			$positionBoard = PositionBoard::findByUUID($attributes['position_board_id']);
			$user->position_board_id = $positionBoard->id;
			$user->term_start_date = Carbon::parse($attributes['term_start_date'])->format('Y-m-d H:i:s');
			$user->term_end_date = Carbon::parse($attributes['term_end_date'])->format('Y-m-d H:i:s');
			$user->address = $attributes['address'];
			$user->city = isset($attributes['city']);
			$user->zip_code = isset($attributes['zip_code']);
			$user->state = isset($attributes['state']);
			$user->status = $attributes['status'];

			$user->save();

			if (isset($attributes['role_id'])) {
				$user->roles()->detach();
				$user->roles()->attach($role->id);
			}

			tenancy()->end();
			$mainUser = User::where('tenant_user_id', $user->id)->where('tenant_id', $tenantId)->first();
			if (!$mainUser) {
				return json_response(404, 'Main user not found.');
			}

			$mainUser->phone = $user->phone;
			$mainUser->role = $role->slug;
			if ($x) {
				$mainUser->email = $user->email;
			}
			if ($y) {
				$mainUser->username = $user->username;
			}

			$mainUser->save();
			tenancy()->initialize($tenantId);

			return json_response(200, __('Organization.updated'));
		} catch (\Exception $e) {
			return json_response(500, __('Organization.update_failed'));
		}		
	}

    function delete($attributes)
    {
        $tenantId = request()->user()->tenant_id;
		try {
			foreach ($attributes as $uuid) {
				$user = $this->show($uuid);
				$this->destroy($uuid);
				tenancy()->end();

				User::where('email', $user->email)->delete();

				tenancy()->initialize($tenantId);
			}
			return json_response(200, __('Organization.delete'));
		} catch (\Exception $e) {
			return json_response(500, __('Organization.delete_failed'));
		}
    }
}
