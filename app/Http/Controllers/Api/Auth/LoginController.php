<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Terdaftar;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /**
     * index
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        //set validasi
        $validator = Validator::make($request->all(), [
            'nik'    => 'required|max:16|min:16',
            'password' => 'required',
        ], [
            'nik.required' => 'nik tidak boleh kosong',
            'nik.max' => 'nik harus 16 digit',
            'nik.min' => 'nik harus 16 digit',
            'password.required' => 'password tidak boleh kosong',
        ]);

        //response error validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cek apakah NIK sudah terdaftar
        $terdaftar = Terdaftar::where('nik', $request->nik)->first();

        //get "nik" dan "password" dari input
        $credentials = $request->only('nik', 'password');

        //check jika "nik" dan "password" tidak sesuai
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            //response login "failed"
            return response()->json([
                'success' => false,
                'message' => 'NIK atau Password anda salah'
            ], 400);
        }

        //response login "success" dengan generate "Token"
        $response = [
            'success'       => true,
            'user'          => auth()->guard('api')->user()->only(['name', 'nik']),
            'permissions'   => auth()->guard('api')->user()->getPermissionArray(),
            'token'         => $token
        ];

        // Jika NIK sudah terdaftar, tambahkan pesan dan tahun
        if ($terdaftar) {
            $response['metta'] = 'Anda sudah menerima beasiswa di tahun ' . $terdaftar->tahun;
        } else {
            $response['metta'] = 'Anda belum menerima beasiswa sama sekali';
        }

        return response()->json($response, 200);
    }
    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        //remove "token" JWT
        JWTAuth::invalidate(JWTAuth::getToken());

        //response "success" logout
        return response()->json([
            'success' => true,
        ], 200);
    }
}
