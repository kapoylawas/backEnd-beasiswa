<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Terdaftar;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;



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
            'nik'    => 'required',
            'password' => 'required',
        ], [
            'nik.required' => 'nik tidak boleh kosong',
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
            'user'          => auth()->guard('api')->user()->only(['name', 'nik', 'id']),
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

    public function sendWelcomeEmail(Request $request)
    {
        // Validasi email tidak boleh kosong
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email'); // Ambil email dari request

        // Cek apakah email terdaftar di tabel users
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email belum terdaftar'], 404);
        }

        // Ambil NIK dari pengguna
        $nik = $user->nik; // Pastikan kolom 'nik' ada di tabel users
        $id = $user->id; // Pastikan kolom 'nik' ada di tabel users

        // Buat token reset password
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Generate random password
        $newPassword = Str::random(8); // Password acak dengan 8 karakter
        $user->password = Hash::make($newPassword); // Hash password
        $user->save(); // Simpan perubahan password

        // Buat URL reset password dengan NIK
        $resetLink = url('reset-password/' . $nik . '/' . $token . '/' . $id);

        // Kirim email dengan link reset password dan password baru
        Mail::to($email)->send(new WelcomeMail($resetLink, $newPassword));

        return response()->json(['message' => 'Email reset password berhasil terkirim!']);
    }
}
