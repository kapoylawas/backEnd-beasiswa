<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Terdaftar;
use App\Models\User;
use App\Models\Akademik;
use App\Models\NonAkademik;
use App\Models\Kesra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return response()->json('ngapain broo');
    }

    public function userbyid()
    {

        $userbyid = User::with('akademik', 'nonakademik', 'kesra', 'dinsos', 'luarNegeri', 'kecamatan', 'kelurahan')->where('id', auth()->user()->id)->first();

        //return with Api Resource
        return new UserResource(true, 'List Data User', $userbyid);
    }

    public function getKecamatan()
    {
        $kecamatans = Kecamatan::latest()->get();

        //return with Api Resource
        return new UserResource(true, 'List kecamatan', $kecamatans);
    }

    public function getKelurahan(Request $request)
    {

        $kelurahans = Kelurahan::where('kecamatan_id', $request->kecamatan_id)->get();


        //return with Api Resource
        return new UserResource(true, 'List kelurahan', $kelurahans);
    }

    public function getDataUser(Request $request)
    {
        try {
            //get users
            $query = User::where('status', '2')
                ->with('roles');

            // Filter berdasarkan search (NIK)
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nik', 'like', '%' . $search . '%');
                });
            }

            // Filter berdasarkan status verifikasi NIK
            if ($request->has('status') && $request->status != '') {
                $status = $request->status;

                if ($status === 'null') {
                    $query->whereNull('jenis_verif_nik');
                } else {
                    $query->where('jenis_verif_nik', $status);
                }
            }

            // Hitung total counts untuk semua status (SEBELUM pagination)
            // Clone query untuk menghitung masing-masing status
            $totalCounts = [
                'total' => (clone $query)->count(),
                'lolos' => (clone $query)->where('jenis_verif_nik', 'lolos')->count(),
                'tidak' => (clone $query)->where('jenis_verif_nik', 'tidak')->count(),
                'belum' => (clone $query)->whereNull('jenis_verif_nik')->count(),
            ];

            // Sorting
            $query->orderBy('jenis_verif_nik', 'asc');

            // Pagination
            $perPage = $request->get('per_page', 10);
            $users = $query->paginate($perPage);

            //append query string to pagination links
            $users->appends([
                'search' => $request->search,
                'status' => $request->status,
                'per_page' => $perPage
            ]);

            // Convert pagination data to array
            $usersData = $users->toArray();

            // Tambahkan counts ke dalam data
            $usersData['counts'] = $totalCounts;

            //return with success response
            return response()->json([
                'success' => true,
                'data' => $usersData,
                'message' => 'List Data Users berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDataUserAkademik(Request $request)
    {
        try {
            //get users
            $query = User::where('status', '2')
                ->where('tipe_beasiswa', '1')
                ->with('roles');

            // Filter berdasarkan search (NIK)
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nik', 'like', '%' . $search . '%');
                });
            }

            // Filter berdasarkan status verifikasi NIK
            if ($request->has('status') && $request->status != '') {
                $status = $request->status;

                if ($status === 'null') {
                    $query->whereNull('jenis_verif_nik');
                } else {
                    $query->where('jenis_verif_nik', $status);
                }
            }

            // Hitung total counts untuk semua status (SEBELUM pagination)
            // Clone query untuk menghitung masing-masing status
            $totalCounts = [
                'total' => (clone $query)->count(),
                'lolos' => (clone $query)->where('jenis_verif_nik', 'lolos')->count(),
                'tidak' => (clone $query)->where('jenis_verif_nik', 'tidak')->count(),
                'belum' => (clone $query)->whereNull('jenis_verif_nik')->count(),
            ];

            // Sorting
            $query->orderBy('jenis_verif_nik', 'asc');

            // Pagination
            $perPage = $request->get('per_page', 10);
            $users = $query->paginate($perPage);

            //append query string to pagination links
            $users->appends([
                'search' => $request->search,
                'status' => $request->status,
                'per_page' => $perPage
            ]);

            // Convert pagination data to array
            $usersData = $users->toArray();

            // Tambahkan counts ke dalam data
            $usersData['counts'] = $totalCounts;

            //return with success response
            return response()->json([
                'success' => true,
                'data' => $usersData,
                'message' => 'List Data Users Akademik berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data users akademik: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDataUserNonkademik(Request $request)
    {
        try {
            //get users
            $query = User::where('status', '2')
                ->where('tipe_beasiswa', '2')
                ->with('roles');

            // Filter berdasarkan search (NIK)
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nik', 'like', '%' . $search . '%');
                });
            }

            // Filter berdasarkan status verifikasi NIK
            if ($request->has('status') && $request->status != '') {
                $status = $request->status;

                if ($status === 'null') {
                    $query->whereNull('jenis_verif_nik');
                } else {
                    $query->where('jenis_verif_nik', $status);
                }
            }

            // Hitung total counts untuk semua status (SEBELUM pagination)
            // Clone query untuk menghitung masing-masing status
            $totalCounts = [
                'total' => (clone $query)->count(),
                'lolos' => (clone $query)->where('jenis_verif_nik', 'lolos')->count(),
                'tidak' => (clone $query)->where('jenis_verif_nik', 'tidak')->count(),
                'belum' => (clone $query)->whereNull('jenis_verif_nik')->count(),
            ];

            // Sorting
            $query->orderBy('jenis_verif_nik', 'asc');

            // Pagination
            $perPage = $request->get('per_page', 10);
            $users = $query->paginate($perPage);

            //append query string to pagination links
            $users->appends([
                'search' => $request->search,
                'status' => $request->status,
                'per_page' => $perPage
            ]);

            // Convert pagination data to array
            $usersData = $users->toArray();

            // Tambahkan counts ke dalam data
            $usersData['counts'] = $totalCounts;

            //return with success response
            return response()->json([
                'success' => true,
                'data' => $usersData,
                'message' => 'List Data Users Non Akademik berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data users non akademik: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDataUserLuarNegeri()
    {
        //get users
        $users = User::where('status', '2')->where('tipe_beasiswa', '5')->when(request()->search, function ($users) {
            $users = $users->where('nik', 'like', '%' . request()->search . '%');
        })->with('roles')->orderBy('jenis_verif_nik', 'asc')->paginate(10);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    public function getDataUserKesra(Request $request)
    {
        try {
            //get users
            $query = User::where('status', '2')
                ->where('tipe_beasiswa', '3')
                ->with('roles');

            // Filter berdasarkan search (NIK)
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nik', 'like', '%' . $search . '%');
                });
            }

            // Filter berdasarkan status verifikasi NIK
            if ($request->has('status') && $request->status != '') {
                $status = $request->status;

                if ($status === 'null') {
                    $query->whereNull('jenis_verif_nik');
                } else {
                    $query->where('jenis_verif_nik', $status);
                }
            }

            // Hitung total counts untuk semua status (SEBELUM pagination)
            // Clone query untuk menghitung masing-masing status
            $totalCounts = [
                'total' => (clone $query)->count(),
                'lolos' => (clone $query)->where('jenis_verif_nik', 'lolos')->count(),
                'tidak' => (clone $query)->where('jenis_verif_nik', 'tidak')->count(),
                'belum' => (clone $query)->whereNull('jenis_verif_nik')->count(),
            ];

            // Sorting
            $query->orderBy('jenis_verif_nik', 'asc');

            // Pagination
            $perPage = $request->get('per_page', 10);
            $users = $query->paginate($perPage);

            //append query string to pagination links
            $users->appends([
                'search' => $request->search,
                'status' => $request->status,
                'per_page' => $perPage
            ]);

            // Convert pagination data to array
            $usersData = $users->toArray();

            // Tambahkan counts ke dalam data
            $usersData['counts'] = $totalCounts;

            //return with success response
            return response()->json([
                'success' => true,
                'data' => $usersData,
                'message' => 'List Data Users Kesra berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data users kesra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDataUserDinsos(Request $request)
    {
        try {
            //get users
            $query = User::where('status', '2')
                ->where('tipe_beasiswa', '4')
                ->with('roles');

            // Filter berdasarkan search (NIK)
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nik', 'like', '%' . $search . '%');
                });
            }

            // Filter berdasarkan status verifikasi NIK
            if ($request->has('status') && $request->status != '') {
                $status = $request->status;

                if ($status === 'null') {
                    $query->whereNull('jenis_verif_nik');
                } else {
                    $query->where('jenis_verif_nik', $status);
                }
            }

            // Hitung total counts untuk semua status (SEBELUM pagination)
            // Clone query untuk menghitung masing-masing status
            $totalCounts = [
                'total' => (clone $query)->count(),
                'lolos' => (clone $query)->where('jenis_verif_nik', 'lolos')->count(),
                'tidak' => (clone $query)->where('jenis_verif_nik', 'tidak')->count(),
                'belum' => (clone $query)->whereNull('jenis_verif_nik')->count(),
            ];

            // Sorting
            $query->orderBy('jenis_verif_nik', 'asc');

            // Pagination
            $perPage = $request->get('per_page', 10);
            $users = $query->paginate($perPage);

            //append query string to pagination links
            $users->appends([
                'search' => $request->search,
                'status' => $request->status,
                'per_page' => $perPage
            ]);

            // Convert pagination data to array
            $usersData = $users->toArray();

            // Tambahkan counts ke dalam data
            $usersData['counts'] = $totalCounts;

            //return with success response
            return response()->json([
                'success' => true,
                'data' => $usersData,
                'message' => 'List Data Users Dinsos berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data users dinsos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showUser($id)
    {
        //get dinsos
        $users = User::whereId($id)->first();

        if ($users) {
            //return success with Api Resource
            return new UserResource(true, 'Detail Data User!', $users);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Detail Data User Tidak Ditemukan!', null);
    }

    public function store(Request $request)
    {
        // 1. Cek jika NIK sudah ada di database (Terdaftar 2026 atau sebelumnya)
        $existingUser = User::where('nik', $request->nik)->first();
        if ($existingUser) {
            // Cek apakah NIK ini SUDAH PERNAH MENDAFTAR di PERIODE TAHUN 2027
            $sudahDaftar2027 = Akademik::where('user_id', $existingUser->id)->whereYear('created_at', 2027)->exists() ||
                               NonAkademik::where('user_id', $existingUser->id)->where('tahun', '2027')->exists() ||
                               Kesra::where('user_id', $existingUser->id)->where('tahun', '2027')->exists();
            
            if ($sudahDaftar2027) {
                // KUNCI PERIODE TAHUN 2027: Ditolak jika mendaftar 2x di tahun 2027 yang sama
                return response()->json([
                    'nik' => ['NIK ' . $request->nik . ' sudah terdaftar pada Periode Beasiswa Tahun 2027. Anda tidak diperbolehkan mendaftar 2 kali pada tahun yang sama!']
                ], 422);
            }
        }

        // 2. Validasi Form Data Registrasi
        $validator = Validator::make(
            $request->all(),
            [
                'nik'          => 'required|max:16|min:16',
                'nokk'         => 'required|max:16|min:16',
                'name'         => 'required',
                'nohp'         => 'required',
                'email'        => 'required',
                'gender'       => 'required',
                'id_kecamatan' => 'required',
                'id_kelurahan' => 'required',
                'rt'           => 'required',
                'rw'           => 'required',
                'alamat'       => 'required',
                'imagektp'     => 'required|mimes:pdf|max:2048',
                'imagekk'      => 'required|mimes:pdf|max:2000',
                'password'     => 'required|confirmed'
            ],
            [
                'nik.required' => 'NIK tidak boleh kosong',
                'nik.max' => 'NIK harus 16 digit',
                'nik.min' => 'NIK harus 16 digit',
                'nokk.required' => 'No kartu keluarga tidak boleh kosong',
                'nokk.max' => 'No kartu keluarga harus 16 digit',
                'nokk.min' => 'No kartu keluarga harus 16 digit',
                'name.required' => 'Nama tidak boleh kosong',
                'nohp.required' => 'No handphone/whatsapp tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'gender.required' => 'Pilih jenis kelamin terlebih dahulu',
                'id_kecamatan.required' => 'Pilih kecamatan terlebih dahulu',
                'id_kelurahan.required' => 'Pilih kelurahan/desa terlebih dahulu',
                'rt.required' => 'RT tidak boleh kosong',
                'rw.required' => 'RW tidak boleh kosong',
                'alamat.required' => 'Alamat tidak boleh kosong',
                'imagektp.required' => 'File KTP tidak boleh kosong',
                'imagektp.mimes' => 'File KTP harus PDF',
                'imagektp.max' => 'File KTP melebihi 2 MB',
                'imagekk.required' => 'File kartu keluarga tidak boleh kosong',
                'imagekk.mimes' => 'File kartu keluarga harus PDF',
                'imagekk.max' => 'File kartu keluarga melebihi 2 MB',
                'password.required' => 'Password tidak boleh kosong',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload KTP & KK
        $imagektp = $request->file('imagektp');
        $imagektp->storeAs('public/ktp', $imagektp->hashName());

        $imagekk = $request->file('imagekk');
        $imagekk->storeAs('public/kk', $imagekk->hashName());

        $userData = [
            'nik'          => $request->nik,
            'nokk'         => $request->nokk,
            'name'         => $request->name,
            'nohp'         => $request->nohp,
            'email'        => $request->email,
            'gender'       => $request->gender,
            'id_kecamatan' => $request->id_kecamatan,
            'id_kelurahan' => $request->id_kelurahan,
            'codepos'      => $request->codepos,
            'rt'           => $request->rt,
            'rw'           => $request->rw,
            'alamat'       => $request->alamat,
            'status'       => 2,
            'status_terkirim' => 'false',
            'status_wa'    => 0,
            'status_email' => 0,
            'status_finish' => 0,
            'jenis_verif'  => "belum",
            'step'         => 1,
            'imagektp'     => $imagektp->hashName(),
            'imagekk'      => $imagekk->hashName(),
            'password'     => bcrypt($request->password)
        ];

        // Jika user dari 2026 mendaftar lagi di 2027 (karena tidak lolos / lupa password)
        if ($existingUser) {
            $existingUser->update($userData);
            $user = $existingUser;
        } else {
            $user = User::create($userData);
            $user->assignRole(['user']);
        }

        return new UserResource(true, 'Registrasi Berhasil! Data Akun Anda diperbarui untuk Periode 2027. Silakan Login.', $user);
    }

    public function storeAdmin(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nik'    => 'required|unique:users|max:16|min:16',
                'name'     => 'required',
                'nohp'     => 'required',
                'email'    => 'required|unique:users',
                'gender'     => 'required',
                'rt'     => 'required',
                'rw'     => 'required',
                'alamat'     => 'required',
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
                'rt.required' => 'rt tidak boleh kosong',
                'rw.required' => 'rw tidak boleh kosong',
                'alamat.required' => 'alamat tidak boleh kosong',
                'password.required' => 'password tidak boleh kosong',
                'password.confirmed' => 'password tidak tidak sama',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

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
            'status'     => 1,
            'status_terkirim'     => 'false',
            'status_wa'     => 0,
            'status_email'     => 0,
            'status_finish'     => 0,
            'step'     => 1,
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

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'nim'     => 'required',
            'ktm'    => 'required|mimes:pdf|max:2048',
            'universitas' => 'required',
            'alamat_univ' => 'required',
            'jurusan' => 'required',
            'imageaktifkampus' => 'required|mimes:pdf|max:2048',
            'imagesuratpernyataan' => 'required|mimes:pdf|max:2048',
            'imageakrekampus' => 'required|mimes:pdf|max:2048',
            'pilih_universitas' => 'required',
        ], [
            'nim.required' => 'nim tidak boleh kosong',
            'ktm.unique' => 'ktm tidak boleh kosong',
            'universitas.required' => 'nama universitas tidak boleh kosong',
            'jurusan.required' => 'jurusan tidak boleh kosong',
            'imageaktifkampus.required' => 'file aktif kuliah tidak boleh kosong',
            'imagesuratpernyataan.required' => 'file surat pernyataan tidak boleh kosong',
            'imageakrekampus.required' => 'file  akredetasi dari universitas tidak boleh kosong',
            'pilih_universitas.required' => 'pilih universitas terlebih dahulu',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload new ktm
        $ktm = $request->file('ktm');
        $ktm->storeAs('public/ktm', $ktm->hashName());

        //upload new imageaktifkampus
        $imageaktifkampus = $request->file('imageaktifkampus');
        $imageaktifkampus->storeAs('public/imageaktifkampus', $imageaktifkampus->hashName());

        //upload new imagesuratpernyataan
        $imagesuratpernyataan = $request->file('imagesuratpernyataan');
        $imagesuratpernyataan->storeAs('public/imagesuratpernyataan', $imagesuratpernyataan->hashName());

        //upload new imageakrekampus
        $imageakrekampus = $request->file('imageakrekampus');
        $imageakrekampus->storeAs('public/imageakrekampus', $imageakrekampus->hashName());

        //upload new imagesuratbeasiswa
        $imagesuratbeasiswa = $request->file('imagesuratbeasiswa');
        if ($imagesuratbeasiswa != null) {
            $imagesuratbeasiswa->storeAs('public/imagesuratbeasiswa', $imagesuratbeasiswa->hashName());
        }

        $user->update([
            'nim'       => $request->nim,
            'ktm'       => $ktm->hashName(),
            'universitas'       => $request->universitas,
            'alamat_univ'       => $request->alamat_univ,
            'jurusan'       => $request->jurusan,
            'imageaktifkampus'       => $imageaktifkampus->hashName(),
            'imagesuratpernyataan'       => $imagesuratpernyataan->hashName(),
            'imageakrekampus'       => $imageakrekampus->hashName(),
            'imagesuratbeasiswa'       => ($imagesuratbeasiswa != null) ? $imagesuratbeasiswa->hashName() : null,
            'pilih_universitas'       => $request->pilih_universitas,
            'jenis_universitas'       => $request->jenis_universitas,
            'kota'       => $request->kota,
            'step'     => 2,
        ]);

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Disimpan!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Disimpan!', null);
    }

    public function updateBiodata(Request $request, User $user)
    {

        if ($request->file('imagektp')) {
            //remove old image
            Storage::disk('local')->delete('public/ktp/' . basename($user->imagektp));

            //upload new ktp
            $imagektp = $request->file('imagektp');
            $imagektp->storeAs('public/ktp', $imagektp->hashName());

            $user->update([
                'name'       => $request->name,
                'email'       => $request->email,
                'nik'       => $request->nik,
                'nim'       => $request->nim,
                'nokk'       => $request->nokk,
                'nohp'       => $request->nohp,
                'alamat'       => $request->alamat,
                'imagektp'       => $imagektp->hashName(),
            ]);
        }

        if ($request->file('imagekk')) {
            //remove old image
            Storage::disk('local')->delete('public/kk/' . basename($user->imagekk));

            //upload new kk
            $imagekk = $request->file('imagekk');
            $imagekk->storeAs('public/kk', $imagekk->hashName());

            $user->update([
                'name'       => $request->name,
                'email'       => $request->email,
                'nik'       => $request->nik,
                'nim'       => $request->nim,
                'nokk'       => $request->nokk,
                'nohp'       => $request->nohp,
                'alamat'       => $request->alamat,
                'imagekk'       => $imagekk->hashName(),
            ]);
        }

        $user->update([
            'name'       => $request->name,
            'email'       => $request->email,
            'nik'       => $request->nik,
            'nim'       => $request->nim,
            'nokk'       => $request->nokk,
            'nohp'       => $request->nohp,
            'alamat'       => $request->alamat,
        ]);

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Disimpan!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Disimpan!', null);
    }

    public function updateVerif(Request $request, User $user)
    {
        $user->update([
            'status_finish'       => $request->status_finish,
            'jenis_verif_nik'       => $request->jenis_verif_nik,
        ]);

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil di Update!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Update!', null);
    }

    public function updateVerifNik(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'alasan_nik'     => 'required',
            'jenis_verif_nik'    => 'required',
        ], [
            'alasan_nik.required' => 'alasan verifikasi tidak boleh kosong',
            'jenis_verif_nik.required' => 'pilih jenis verifikasi terlebih dahulu',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->update([
            'alasan_nik'       => $request->alasan_nik,
            'jenis_verif_nik'       => $request->jenis_verif_nik,
            'verifikator_nik'       => $request->verifikator_nik,
        ]);

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Verifikasi Data Berhasil Disimpan!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Verifikasi Data Gagal Disimpan!', null);
    }

    function tanggalBatas(Request $request)
    {
        $dates = DB::table('tgl_batas')
            ->get();

        return response()->json($dates[0], 200);
    }

    /**
     * Upload/Update imagespjmt (surat perjanjian) untuk user
     */
    public function uploadImageSpjmt(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Validasi file
            $validator = Validator::make($request->all(), [
                'imagespjmt' => 'required|file|mimes:pdf|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Hapus file lama jika ada
            if ($user->imagespjmt) {
                $oldFilename = $user->getRawOriginal('imagespjmt');
                if ($oldFilename) {
                    Storage::disk('public')->delete('imagespjmt/' . $oldFilename);
                }
            }

            // Upload file baru
            $file = $request->file('imagespjmt');
            $filename = time() . '_imagespjmt_' . uniqid() . '.pdf';
            $file->storeAs('public/imagespjmt', $filename);

            // Update database
            $user->update([
                'imagespjmt' => $filename
            ]);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'File SPJMT berhasil diupload'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage()
            ], 500);
        }
    }
}
