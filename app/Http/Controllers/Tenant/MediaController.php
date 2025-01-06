<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\User;
use App\Rules\ValidateBase64;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    function update(Request $request)
    {
        $request->validate([
            "user_id" => "required|exists:users,uuid",
            "avatar_image" => [new ValidateBase64(), 'nullable']
        ]);

        $userID = request('user_id');
        $user = User::findByUUIDOrFail($userID);
        $user->clearMediaCollection('avatar');

        if ($request->avatar_image) {
            $user
                ->addMediaFromBase64($request->avatar_image)
                ->usingFileName(Str::uuid() . '.' . ext_base64($request->avatar_image))
                ->toMediaCollection('avatar');
        }

        return json_response(200, "Avatar Updated successfully.");
    }
}
