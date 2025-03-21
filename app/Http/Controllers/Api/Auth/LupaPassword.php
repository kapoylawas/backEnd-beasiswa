<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LupaPassword extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|max:16|min:16',
            'nokk' => 'required|max:16|min:16',
        ], [
            'nik.required' => 'nik tidak boleh kosong',
            'nik.max' => 'nik harus 16 digit',
            'nik.min' => 'nik harus 16 digit',
            'nokk.required' => 'nokk tidak boleh kosong',
            'nokk.max' => 'nokk harus 16 digit',
            'nokk.min' => 'nokk harus 16 digit',
        ]);

        //response error validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('nik', $request->nik)
            ->where('nokk', $request->nokk)
            ->first();

        if ($user) {
            return response()->json([
                'id' => $user->id,
                'nama' => $user->name,
            ], 200);
        } else {
            return response()->json([
                'message' => 'NIK dan NoKK Anda salah.',
            ], 404);
        }
    }

    public function update(Request $request, User $user)
    {
       /*  $validator = Validator::make($request->all(), [
            'password' => 'confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } */

        $user->update([
            'password'  => bcrypt($request->password)
        ]);

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Diupdate!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Diupdate!', null);
    }
}
