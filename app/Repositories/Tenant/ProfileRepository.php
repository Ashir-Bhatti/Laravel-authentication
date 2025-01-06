<?php

namespace App\Repositories\Tenant;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\{DB, Hash};
use App\Models\Tenant\{PositionBoard, Role, User as TenantUser};

class ProfileRepository extends BaseRepository
{
    public function __construct(TenantUser $model)
    {
        $this->model = $model;
    }

	function update(array $attributes, $uuid = null)
	{
		$tenantId = request()->user()->tenant_id;

		try {
			$user = $this->show($uuid);
			if (!$user) {
				return json_response(404, 'User not found.');
			}

			tenancy()->end();

			tenancy()->initialize($tenantId);

			$user->fname = $attributes['fname'];
			$user->lname = $attributes['lname'];
			$user->phone = $attributes['phone'];
			$user->address = $attributes['address'];
			$user->city = isset($attributes['city']);
			$user->zip_code = isset($attributes['zip_code']);
			$user->state = isset($attributes['state']);

			$user->save();

			tenancy()->end();

			$authUser = User::where('tenant_user_id', $user->id)->first();
			if (!$authUser) {
				return json_response(404, 'Main user not found.');
			}

			$authUser->phone = $user->phone;

			$authUser->save();
			tenancy()->initialize($tenantId);

			return json_response(200, 'User profile updated successfully');
		} catch (\Exception $e) {
			return json_response(500, __('Organization.update_failed'));
		}
	}
}
