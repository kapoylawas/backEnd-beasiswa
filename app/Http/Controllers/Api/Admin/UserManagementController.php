<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {
            // Query dasar dengan status 1
            $users = User::where('status', 1)
                ->with(['roles']) // Load relasi roles jika diperlukan
                ->latest() // Urutkan dari yang terbaru
                ->get();

            // Jika perlu pagination (opsional)
            // $users = User::where('status', 1)
            //     ->with(['roles'])
            //     ->latest()
            //     ->paginate(10); // 10 data per halaman

            // Format response
            return response()->json([
                'success' => true,
                'message' => 'Data user dengan status 1 berhasil diambil',
                'data' => $users,
                'count' => $users->count()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get Users with Status 1 Failed:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user by ID dengan status 1
     */
    public function show($id): JsonResponse
    {
        try {
            $user = User::where('status', 1)
                ->where('id', $id)
                ->with(['roles'])
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan atau status tidak aktif'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get User by ID Failed:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nik'    => 'required|unique:users|max:16|min:16',
                'nokk'    => 'required|max:16|min:16',
                'name'     => 'required',
                'nohp'     => 'required',
                'email'    => 'required|unique:users',
                'gender'     => 'required',
                'id_kecamatan'     => 'required',
                'id_kelurahan'     => 'required',
                'rt'     => 'required',
                'rw'     => 'required',
                'alamat'     => 'required',
                'imagektp'         => 'required|mimes:pdf|max:2048',
                'imagekk'         => 'required|mimes:pdf|max:2000',
                'password' => 'required|confirmed'
            ],
            [
                'nik.required' => 'nik no induk tidak boleh kosong',
                'nik.unique' => 'nik sudah terdaftar',
                'nokk.required' => ' no kartu kelearga tidak boleh kosong',
                'nokk.max' => ' no kartu kelearga harus 16 digit',
                'nokk.min' => ' no kartu kelearga harus 16 digit',
                'name.required' => 'nama tidak boleh kosong',
                'nohp.required' => 'no handphone/whatsapp tidak boleh kosong',
                'email.required' => 'email tidak boleh kosong',
                'email.unique' => 'email sudah di daftarkan',
                'gender.required' => 'pilih jenis kelamin terlebih dahulu',
                'id_kecamatan.required' => 'pilih kecamatan kelamin terlebih dahulu',
                'id_kelurahan.required' => 'pilih kelurahan/desa kelamin terlebih dahulu',
                'rt.required' => 'rt tidak boleh kosong',
                'rw.required' => 'rw tidak boleh kosong',
                'alamat.required' => 'alamat tidak boleh kosong',
                'imagektp.required' => 'file KTP tidak boleh kosong',
                'imagektp.mimes' => 'file KTP harus pdf',
                'imagektp.max' => 'file KTP melebihi dari 2 mb',
                'imagekk.required' => 'file kartu keluarga tidak boleh kosong',
                'imagekk.mimes' => 'file kartu keluarga harus pdf',
                'imagekk.max' => 'file kartu keluarga melebihi dari 2mb',
                'password.required' => 'password tidak boleh kosong',
                'password.confirmed' => 'password tidak tidak sama',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload imagektp
        $imagektp = $request->file('imagektp');
        $imagektp->storeAs('public/ktp', $imagektp->hashName());

        //upload imagekk
        $imagekk = $request->file('imagekk');
        $imagekk->storeAs('public/kk', $imagekk->hashName());

        //create user
        $user = User::create([
            'nik'     => $request->nik,
            'nokk'     => $request->nokk,
            'name'      => $request->name,
            'nohp'      => $request->nohp,
            'email'     => $request->email,
            'gender'     => $request->gender,
            'id_kecamatan'     => $request->id_kecamatan,
            'id_kelurahan'     => $request->id_kelurahan,
            'codepos'     => $request->codepos,
            'rt'     => $request->rt,
            'rw'     => $request->rw,
            'alamat'     => $request->alamat,
            'status'     => 2,
            'status_terkirim'     => 'false',
            'status_wa'     => 0,
            'status_email'     => 0,
            'status_finish'     => 0,
            'step'     => 1,
            'imagektp'       => $imagektp->hashName(),
            'imagekk'       => $imagekk->hashName(),
            'password'  => bcrypt($request->password)
        ]);

        //assign roles to user
        $user->assignRole(['user']);

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Disimpan!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Disimpan!', null);
    }

    // Method untuk test koneksi
    public function testConnection(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Controller berfungsi dengan baik',
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Controller error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method untuk test validation
    public function testValidation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|unique:users,nik',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Validasi berhasil',
            'data' => $request->all()
        ]);
    }
}
