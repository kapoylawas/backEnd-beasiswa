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

//kecamatan all
Route::get('/kecamatan/all', [\App\Http\Controllers\Api\Admin\UserController::class, 'getKecamatan']);

//kelurahan all
Route::get('/kelurahan/byid', [\App\Http\Controllers\Api\Admin\UserController::class, 'getKelurahan']);



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

        //Kesra
        Route::apiResource('/kesra', App\Http\Controllers\Api\Admin\KesraController::class)
            ->middleware('permission:kesra.index|kesra.store|kesra.update|kesra.delete');

        //Dinsos
        Route::apiResource('/dinsos', App\Http\Controllers\Api\Admin\DinsosController::class)
            ->middleware('permission:dinsos.index|dinsos.store|dinsos.update|dinsos.delete');

        //luar negeri
        Route::apiResource('/luarnegeri', App\Http\Controllers\Api\Admin\LuarNegeriController::class)
            ->middleware('permission:luarnegeri.index|luarnegeri.store|luarnegeri.update|luarnegeri.delete');

        // user by id
        Route::get('/users/byid', [\App\Http\Controllers\Api\Admin\UserController::class, 'userbyid']);

        // get akademik
        Route::get('/beasiswa/akademiks', [\App\Http\Controllers\Api\Admin\AkademikController::class, 'getData']);

        // update biodata
        Route::put('/users/biodata/{user}', [\App\Http\Controllers\Api\Admin\UserController::class, 'updateBiodata']);

        // update biodata
        Route::put('/users/verif/{user}', [\App\Http\Controllers\Api\Admin\UserController::class, 'updateVerif']);

        // update akademiks
        Route::put('/users/akademiks/{akademik}', [\App\Http\Controllers\Api\Admin\AkademikController::class, 'updateAkademik']);

        // update non akademiks
        Route::put('/users/nonAkademiks/{nonAkademik}', [\App\Http\Controllers\Api\Admin\NonAkademikController::class, 'updateNonAkademik']);
    });
});
