<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Http\Requests\Tenant\ProfileRequest;
use App\Http\Requests\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{DB, Hash, Mail};

use App\Models\User;
use App\Models\Tenant\User as TenantUser;
use App\Mail\ResetEmail;
use App\Repositories\Tenant\ProfileRepository;

class ProfileController extends Controller
{
    function __construct(private ProfileRepository $repository){}

    public function changePassword(ProfileRequest $request)
    {
        $loggedIn = request()->user();

        tenancy()->end();
        $tenantUser = User::find($loggedIn->id);

        if (!$tenantUser) {
            return json_response(403, "User not found");
        }

        $tenantUser->update(['password' => Hash::make($request->password)]);

        return json_response(200, "Password updated successfully");
    }

    function update(ProfileRequest $request)
	{
		$authUser = request()->user();
        $tenantUser = TenantUser::where('id', $authUser->tenant_user_id)->first();
        $uuid = $tenantUser->uuid;
		return $this->repository->update($request->all(), $uuid);
	}

    public function changeEmail(ProfileRequest $request)
	{
        $token = Str::random(64);
        $loginUser = request()->user();
        $loginUser->verification_token = $token;
        $loginUser->save();

        $url = $request->url . '?email=' . $request->email . '&token=' . $token;
		
        try {
            Mail::to($request->email)->send(new ResetEmail($url));
        } catch (\Exception $e) {
            return json_response(500, 'Failed to send email. Please try again later.');
        }

        $data = [
            'url' => $url,
            'token' => $token
        ];

        return json_response(200, 'Verification email has been successfully sent to you.', $data);
	}

    public function verifyChangedEmail(ProfileRequest $request)
    {
        $loggedIn = request()->user();

        if ($loggedIn->verification_token != $request->token) {
            return json_response(403, 'Invalid Token!');
        }
        tenancy()->end();

        $loggedIn->email_verified_at = Carbon::now();
        $loggedIn->email = $request->email;
        $loggedIn->verification_token = null;
        $loggedIn->save();

        tenancy()->initialize($loggedIn->tenant_id);

        $user = TenantUser::where('id', $loggedIn->tenant_user_id)->first();
        $user->email = $request->email;
        $user->save();

        return json_response(200, 'Email changed successfully.');
    }
}
