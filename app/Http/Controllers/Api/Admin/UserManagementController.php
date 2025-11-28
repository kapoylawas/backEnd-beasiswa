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
    public function store(Request $request): JsonResponse
    {
        // Validasi langsung di controller
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|unique:users,nik',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|in:male,female',
            'alamat' => 'required|string',
            'nokk' => 'nullable|string|max:16',
            'nohp' => 'nullable|string|max:15',
            'id_kecamatan' => 'nullable|integer',
            'id_kelurahan' => 'nullable|integer',
            'codepos' => 'nullable|string|max:5',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
        ], [
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'name.required' => 'Nama wajib diisi',
            'gender.required' => 'Jenis kelamin wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Debug: Log request data
            Log::info('User Creation Request:', $request->all());

            $userData = [
                'nik' => $request->nik,
                'nokk' => $request->nokk ?? '1234567890123456',
                'name' => $request->name,
                'nohp' => $request->nohp ?? '081234567890',
                'email' => $request->email,
                'gender' => $request->gender,
                'id_kecamatan' => $request->id_kecamatan ?? 1,
                'id_kelurahan' => $request->id_kelurahan ?? 1,
                'codepos' => $request->codepos ?? '61256',
                'rt' => $request->rt ?? '001',
                'rw' => $request->rw ?? '001',
                'alamat' => $request->alamat,
                'status' => 1,
                'status_terkirim' => 'false',
                'status_wa' => 0,
                'status_email' => 0,
                'status_finish' => 0,
                'jenis_verif' => 'belum',
                'step' => 1,
                'password' => Hash::make('!pendidikan@2025')
            ];

            Log::info('User Data to Create:', $userData);

            $user = User::create($userData);

            // Assign role 'yatim' ke user
            $user->assignRole('admindinsos');

            DB::commit();

            Log::info('User created successfully:', [
                'user_id' => $user->id,
                'role_assigned' => 'yatim'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat dengan role yatim',
                'data' => $user->load('roles') // Load roles untuk response
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log error details
            Log::error('User Creation Failed:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat user',
                'error' => $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
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
