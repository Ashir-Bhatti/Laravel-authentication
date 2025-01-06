<?php

use App\Http\Controllers\{ AuthController, ForgotPasswordController, OrganizationController };
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['middleware' => ['auth:sanctum']], function () {    
    Route::group(['middleware' => ['super_admin'], 'prefix' => 'organization'], function () {
        Route::get('list/{uuid?}', [OrganizationController::class, 'index']);
        Route::post('store', [OrganizationController::class, 'store']);
    });
    Route::post('organization/update-status', [OrganizationController::class, 'updateStatus']);
    Route::get('subscriptions', [OrganizationController::class, 'subscription']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword']);
