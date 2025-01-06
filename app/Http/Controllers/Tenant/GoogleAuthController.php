<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Repositories\Tenant\GoogleAuthRepository;

use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    function __construct(private GoogleAuthRepository $repository){}

    public function generateQrCode()
    {
        return $this->repository->googleQrCode();
    }

    public function verifyQrCode(Request $request)
    {
        $request->validate([
            'secret_key' => 'required',
        ]);

        return $this->repository->googleVerifyCode($request);
    }

    public function activateAuth(Request $request)
    {
        $request->validate([
            'verify_code' => 'required',
        ]);

        return $this->repository->googleAuthActivator($request);
    }

    public function resetAuth(Request $request)
    {
        $request->validate([
            'backup_key' => 'required',
        ]);

        return $this->repository->googleAuthReset($request);
    }
}
