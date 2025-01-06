<?php

namespace App\Repositories\Tenant;

use Illuminate\Support\Str;
use App\Repositories\BaseRepository;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;

class GoogleAuthRepository extends BaseRepository
{
    private $google2fa;

    public function __construct() 
    {
        $this->google2fa = new Google2FA();
    }

    public function googleQrCode() 
    {
        $g_secret = $this->google2fa->generateSecretKey(32);

        $this->google2fa->setEnforceGoogleAuthenticatorCompatibility(false);

        $user = Auth::user();

        $user->google2fa_secret = $g_secret;
        $user->save();

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
			$user->username,
			$user->email,
			$g_secret
		);

        $data = [
            'secret_key' => $g_secret,
            'qr_img' => $qrCodeUrl
        ];

        return json_response(200, 'QR Code Generated Successfully.', $data);
    }

    public function googleVerifyCode($request) 
    {
        $user = Auth::user();

        if ($user->google2fa_secret === $request->secret_key) {
            $google_key = strtoupper(Str::random(32));

            $user->google2fa_key = $google_key;
            $user->save();

            $data = [
                'google_key' => $google_key,
            ];

            return json_response(200, 'Google Key saved successfully.', $data);
        }

        return json_response(400, 'Scanned Error');
    }

    public function googleAuthActivator($request) 
    {
        $user = Auth::user();

        if ($user->google2fa_secret) {
            $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->verify_code);

            if ($valid) {
                if ($request->has('disable') && $request->disable === true) {
                    $user->enable_google = 0;
                    $user->save();

                    return json_response(200, 'Google Auth disabled successfully.');
                }

                $user->enable_google = 1;
                $user->save();
                
                return json_response(200, 'Google Auth enabled successfully.');
            }

            return json_response(400, 'Invalid Token');
        }

        return json_response(400, 'Google 2FA key not found.');
    }

    public function googleAuthReset($request) 
    {
        $user = Auth::user();

        if ($user->google2fa_key === $request->backup_key) {
            $user->google2fa_key = null;
            $user->google2fa_secret = null;
            $user->enable_google = 0;
            $user->save();

            return json_response(200, 'Google Auth has been reset successfully.');
        }

        return json_response(400, 'Invalid Key');
    }
}
