<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//route login
Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);

//users
Route::apiResource('/users', App\Http\Controllers\Api\Admin\UserController::class);

//permissions all
Route::get('/permissions/all', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'all']);



//dashboard

//group route with middleware "auth"
Route::group(['middleware' => 'auth:api'], function () {

    //logout
    Route::post('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);
});

//group route with prefix "admin"
Route::prefix('admin')->group(function () {
    //group route with middleware "auth:api"
    Route::group(['middleware' => 'auth:api'], function () {

        Route::get('/dashboard', App\Http\Controllers\Api\Admin\DashboardController::class);

        //permissions
        Route::get('/permissions', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'index'])
            ->middleware('permission:permissions.index');


        //roles all
        Route::get('/roles/all', [\App\Http\Controllers\Api\Admin\RoleController::class, 'all'])
            ->middleware('permission:roles.index');

        //roles
        Route::apiResource('/roles', App\Http\Controllers\Api\Admin\RoleController::class)
            ->middleware('permission:roles.index|roles.store|roles.update|roles.delete');
        
        //Akademik
        Route::apiResource('/akademiks', App\Http\Controllers\Api\Admin\AkademikController::class)
        ->middleware('permission:akademiks.index|akademiks.store|akademiks.update|akademiks.delete');

        //Non Akademik
        Route::apiResource('/nonakademiks', App\Http\Controllers\Api\Admin\NonAkademikController::class)
        ->middleware('permission:nonakademiks.index|nonakademiks.store|nonakademiks.update|nonakademiks.delete');

        // user by id
        Route::get('/users/byid', [\App\Http\Controllers\Api\Admin\UserController::class, 'userbyid']);
    });
});
