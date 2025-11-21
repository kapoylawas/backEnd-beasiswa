<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

//route lupa password
Route::post('/lupaPassword', [App\Http\Controllers\Api\Auth\LupaPassword::class, 'index']);

//ganti password
Route::put('/changePassword/{user}', [App\Http\Controllers\Api\Auth\LupaPassword::class, 'update']);

Route::apiResource('/users', App\Http\Controllers\Api\Admin\UserController::class);

Route::post('/adminOpd', [App\Http\Controllers\Api\Admin\UserController::class, 'storeAdmin']);

//permissions all
Route::get('/permissions/all', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'all']);

//kecamatan all
Route::get('/kecamatan/all', [\App\Http\Controllers\Api\Admin\UserController::class, 'getKecamatan']);

//kelurahan all
Route::get('/kelurahan/byid', [\App\Http\Controllers\Api\Admin\UserController::class, 'getKelurahan']);

//get tanggal batas
Route::get('/tanggalBatas', [\App\Http\Controllers\Api\Admin\UserController::class, 'tanggalBatas']);

//reset password
Route::post('/send-welcome-email', [LoginController::class, 'sendWelcomeEmail']);


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

        Route::apiResource('/yatim', App\Http\Controllers\Api\Admin\YatimPiatuController::class)
            ->parameters(['yatim' => 'id'])
            ->middleware('permission:yatim.index|yatim.store|yatim.update|yatim.delete');

        Route::get('/yatim-byidsekolah/{user_id}', [App\Http\Controllers\Api\Admin\YatimPiatuController::class, 'getYatimPiatuByUserId']);

        // user by id
        Route::get('/users/byid', [\App\Http\Controllers\Api\Admin\UserController::class, 'userbyid']);

        // get akademik
        Route::get('/beasiswa/akademiks', [\App\Http\Controllers\Api\Admin\AkademikController::class, 'getData'])
            ->middleware('permission:akademiks.index|akademiks.store|akademiks.update|akademiks.delete');

        // update biodata
        Route::put('/users/biodata/{user}', [\App\Http\Controllers\Api\Admin\UserController::class, 'updateBiodata']);

        // update biodata
        Route::put('/users/verif/{user}', [\App\Http\Controllers\Api\Admin\UserController::class, 'updateVerif']);

        // update akademiks
        Route::put('/users/akademiks/{akademik}', [\App\Http\Controllers\Api\Admin\AkademikController::class, 'updateAkademik']);

        // update non akademiks
        Route::put('/users/nonAkademiks/{nonAkademik}', [\App\Http\Controllers\Api\Admin\NonAkademikController::class, 'updateNonAkademik']);

        // update dinsos
        Route::put('/users/dinsoses/{dinsos}', [\App\Http\Controllers\Api\Admin\DinsosController::class, 'updateDinsos']);

        // update luar negeri
        Route::put('/users/luarNegeris/{luarNegeri}', [\App\Http\Controllers\Api\Admin\LuarNegeriController::class, 'updateLuarNegeri']);

        // update kesra
        Route::put('/users/kesras/{kesra}', [\App\Http\Controllers\Api\Admin\KesraController::class, 'updateKesra']);

        // get data NonAkademiks
        Route::get('/beasiswa/nonAkademiks', [\App\Http\Controllers\Api\Admin\NonAkademikController::class, 'getDataNonAkademik'])
            ->middleware('permission:nonakademiks.index|nonakademiks.store|nonakademiks.update|nonakademiks.delete');

        // get data dinsos tipe dtks
        Route::get('/beasiswa/dinsosDtks', [\App\Http\Controllers\Api\Admin\DinsosController::class, 'getDataDinsosDtks'])
            ->middleware('permission:dinsos.index|dinsos.store|dinsos.update|dinsos.delete');

        // get data dinsos tipe tidak mempunyai dtks
        Route::get('/beasiswa/dinsosNoDtks', [\App\Http\Controllers\Api\Admin\DinsosController::class, 'getDataDinsosNoDtks'])
            ->middleware('permission:dinsos.index|dinsos.store|dinsos.update|dinsos.delete');

        /* update admin verif akademik */
        Route::put('/verif/akademik/{user}', [\App\Http\Controllers\Api\Admin\AkademikController::class, 'updateVerif']);

        /* update admin verif non akademik */
        Route::put('/verif/nonAkademik/{user}', [\App\Http\Controllers\Api\Admin\NonAkademikController::class, 'updateVerif']);

        /* update admin verif luar negeri */
        Route::put('/verif/luarNegeri/{user}', [\App\Http\Controllers\Api\Admin\LuarNegeriController::class, 'updateVerif']);

        /* update admin verif dinsos */
        Route::put('/verif/dinsos/{user}', [\App\Http\Controllers\Api\Admin\DinsosController::class, 'updateVerif']);

        /* update admin verif kesra */
        Route::put('/verif/kesra/{user}', [\App\Http\Controllers\Api\Admin\DinsosController::class, 'updateVerif']);

        /* update admin verif NIK */
        Route::put('/verif/nik/{user}', [\App\Http\Controllers\Api\Admin\UserController::class, 'updateVerifNik']);

        // get data Luar Negeri
        Route::get('/beasiswa/luarNegeri', [\App\Http\Controllers\Api\Admin\LuarNegeriController::class, 'getDataLuarNegeri'])
            ->middleware('permission:luarnegeri.index|luarnegeri.store|luarnegeri.update|luarnegeri.delete');

        // get data kesra tipe 1
        Route::get('/beasiswa/kesra', [\App\Http\Controllers\Api\Admin\KesraController::class, 'getDataKesra1'])
            ->middleware('permission:adminkesra.index|adminkesra.store|adminkesra.update|adminkesra.delete');

        // get data kesra tipe 2
        Route::get('/beasiswa/kesra2', [\App\Http\Controllers\Api\Admin\KesraController::class, 'getDataKesra2'])
            ->middleware('permission:adminkesra.index|adminkesra.store|adminkesra.update|adminkesra.delete');

        // get data kesra tipe 3
        Route::get('/beasiswa/kesra3', [\App\Http\Controllers\Api\Admin\KesraController::class, 'getDataKesra3'])
            ->middleware('permission:adminkesra.index|adminkesra.store|adminkesra.update|adminkesra.delete');

        // get data kesra tipe 3
        Route::get('/beasiswa/kesra4', [\App\Http\Controllers\Api\Admin\KesraController::class, 'getDataKesra4'])
            ->middleware('permission:adminkesra.index|adminkesra.store|adminkesra.update|adminkesra.delete');

        // get data users
        Route::get('/beasiswa/users', [\App\Http\Controllers\Api\Admin\UserController::class, 'getDataUser'])
            ->middleware('permission:users.index|users.store|users.update|users.delete');

        // get data users akademik
        Route::get('/beasiswa/usersAkademik', [\App\Http\Controllers\Api\Admin\UserController::class, 'getDataUserAkademik'])
            ->middleware('permission:users.index|users.store|users.update|users.delete');

        // get data users luar negeri
        Route::get('/beasiswa/usersLuarNegeri', [\App\Http\Controllers\Api\Admin\UserController::class, 'getDataUserLuarNegeri'])
            ->middleware('permission:users.index|users.store|users.update|users.delete');

        // get data users non akademik
        Route::get('/beasiswa/usersNonAkademik', [\App\Http\Controllers\Api\Admin\UserController::class, 'getDataUserNonkademik'])
            ->middleware('permission:users.index|users.store|users.update|users.delete');

        // get data users kesra
        Route::get('/beasiswa/userskesra', [\App\Http\Controllers\Api\Admin\UserController::class, 'getDataUserKesra'])
            ->middleware('permission:users.index|users.store|users.update|users.delete');

        // get data users dinsos
        Route::get('/beasiswa/usersDinsos', [\App\Http\Controllers\Api\Admin\UserController::class, 'getDataUserDinsos'])
            ->middleware('permission:users.index|users.store|users.update|users.delete');

        // get data users by id
        Route::get('/beasiswa/users/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'showUser']);

        // get data akademiks by uuiid
        Route::get('/beasiswa/akademiks/{uuid}', [\App\Http\Controllers\Api\Admin\AkademikController::class, 'showUuid']);

        // get data non akademiks by uuiid
        Route::get('/beasiswa/nonAkademiks/{uuid}', [\App\Http\Controllers\Api\Admin\NonAkademikController::class, 'showUuid']);

        // get data luar negeri by uuiid
        Route::get('/beasiswa/luarNegeris/{uuid}', [\App\Http\Controllers\Api\Admin\LuarNegeriController::class, 'showUuid']);

        // get data dinsos by uuiid
        Route::get('/beasiswa/dinsoses/{uuid}', [\App\Http\Controllers\Api\Admin\DinsosController::class, 'showUuid']);

        // get data kesra by uuiid
        Route::get('/beasiswa/kesras/{uuid}', [\App\Http\Controllers\Api\Admin\KesraController::class, 'showUuid']);
    });
});
