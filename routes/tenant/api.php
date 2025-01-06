<?php

use App\Http\Controllers\Tenant\{
	MediaController,
	PermissionController,
	PositionBoardController,
	RoleController, 
	UserController,
	ProfileController,
	GoogleAuthController,
	TaskController,
	CommentController,
	TagController,
	ActivityController,
	CampaignController, 
	CampaignTypeController, 
	DepartmentController,
	CategoryController,
	EventController
};
use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'role'], function () {
	Route::get('list/{uuid?}', [RoleController::class, 'index']);
	Route::post('store', [RoleController::class, 'store']);
	Route::delete('delete/{uuid}', [RoleController::class, 'delete']);
});

Route::group(['prefix' => 'user'], function () {
	Route::get('list/{uuid?}', [UserController::class, 'index']);
	Route::post('store', [UserController::class, 'store']);
	Route::put('update', [UserController::class, 'update']);
	Route::delete('delete', [UserController::class, 'delete']);
});

Route::group(['prefix' => 'user'], function () {
	Route::put('/media/update', [MediaController::class, 'update']);
});

Route::group(['prefix' => '2fa'], function () {
	Route::post('generate-qr', [GoogleAuthController::class, 'generateQrCode']);
    Route::post('verify-qr', [GoogleAuthController::class, 'verifyQrCode']);
    Route::post('activate', [GoogleAuthController::class, 'activateAuth']);
    Route::post('reset', [GoogleAuthController::class, 'resetAuth']);
});

Route::group(['prefix' => 'profile'], function () {
	Route::put('update', [ProfileController::class, 'update']);
	Route::post('email-verify', [ProfileController::class, 'changeEmail']);
	Route::post('reset-email', [ProfileController::class, 'verifyChangedEmail']);
	Route::post('change-password', [ProfileController::class, 'changePassword']);
});

Route::group(['prefix' => 'task'], function () {
	Route::get('list', [TaskController::class, 'index']);
	Route::post('store', [TaskController::class, 'store']);
	Route::delete('delete', [TaskController::class, 'delete']);
	Route::put('status', [TaskController::class, 'updateTaskStatus']);
});

Route::group(['prefix' => 'activity'], function () {
	Route::get('logs', [ActivityController::class, 'index']);
});

Route::prefix('comment')->group(function () {
    Route::get('list', [CommentController::class, 'index']);
    Route::post('store', [CommentController::class, 'store']);
    Route::delete('delete', [CommentController::class, 'delete']);
});

Route::prefix('tag')->group(function () {
    Route::get('list', [TagController::class, 'index']);
    Route::post('store', [TagController::class, 'store']);
    Route::delete('delete', [TagController::class, 'delete']);
    Route::post('sync-tags', [TagController::class, 'syncTags']);
});

Route::group(['prefix' => 'event'], function () {
	Route::get('list', [EventController::class, 'index']);
	Route::post('store', [EventController::class, 'store']);
	Route::delete('delete', [EventController::class, 'delete']);
	Route::post('sync-users', [EventController::class, 'attachUsersToEvent']);
});

Route::group(['prefix' => 'permission'], function () {
	Route::get('list/{uuid?}', [PermissionController::class, 'index']);
	Route::get('permission', [PermissionController::class, 'getAuthUserPermission']);
});

Route::group(['prefix' => 'position-board'], function () {
	Route::get('list', [PositionBoardController::class, 'index']);
});

Route::group(['prefix' => 'organization'], function () {
	Route::put('update', [OrganizationController::class, 'updateOrganization']);
});

// department
Route::group(['prefix' => 'department'], function () {
	Route::get('list/{uuid?}', [DepartmentController::class, 'index']);
	Route::post('store', [DepartmentController::class, 'store']);
	Route::delete('delete/{uuid}', [DepartmentController::class, 'delete']);
});

// campaignType
Route::group(['prefix' => 'campaign-type'], function () {
	Route::get('list/{uuid?}', [CampaignTypeController::class, 'index']);
	Route::post('store', [CampaignTypeController::class, 'store']);
	Route::delete('delete/{uuid?}', [CampaignTypeController::class, 'delete']);
});

// campaign
Route::group(['prefix' => 'campaign'], function () {
	Route::get('list/{uuid?}', [CampaignController::class, 'index']);
	Route::post('store', [CampaignController::class, 'store']);
	Route::delete('delete/{uuid?}', [CampaignController::class, 'delete']);
});

// category
Route::group(['prefix' => 'category'], function () {
	Route::get('list/{uuid?}', [CategoryController::class, 'index']);
	Route::post('store', [CategoryController::class, 'store']);
	Route::delete('delete/{uuid?}', [CategoryController::class, 'delete']);
});