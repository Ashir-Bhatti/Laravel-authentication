<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ResetPasswordEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB, Hash, Mail};

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
	{
        $request->validate([
            'email' => 'required|exists:users,email',
            'url' => 'required'
        ]);
        DB::table('password_reset_tokens')->whereEmail($request->email)->delete();

        $token = Str::random(64);
		DB::table('password_reset_tokens')->insert([
			'email' => $request->email,
			'token' => $token,
			'created_at' => Carbon::now()
		]);

        $url = $request->url . '?email=' . $request->email . '&token=' . $token;
		
        Mail::to($request->email)->send(new ResetPasswordEmail($url));
        $data = [
            'url' => $url,
            'token' => $token
        ];

        return json_response(200, 'Email has been sent Successfully.', $data);
	}

    public function resetPassword(Request $request)
	{
        $request->validate([
            'email' => 'required|email|exists:users',
			'token' => 'required',
			'password' => 'required|string|min:6|confirmed'
        ]);

		$updatePassword = DB::table('password_reset_tokens')->where([
			'email' => $request->email
		])->first();

        if (!$updatePassword) {
            return json_response(403, "Invalid Token");
        }

        User::where('email', $request->email)
        ->update(['password' => Hash::make($request->password)]);
        
        DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();
        return json_response(200, "Password updated successfully");
	}
}
