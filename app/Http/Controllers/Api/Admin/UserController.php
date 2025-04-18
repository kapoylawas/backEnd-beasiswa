<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Terdaftar;
use App\Models\User;
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

    public function getDataUser()
    {
        //get users
        $users = User::where('status', '2')->when(request()->search, function ($users) {
            $users = $users->where('nik', 'like', '%' . request()->search . '%');
        })->with('roles')->orderBy('jenis_verif_nik', 'asc')->paginate(10);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    public function getDataUserAkademik()
    {
        //get users
        $users = User::where('status', '2')->where('tipe_beasiswa', '1')->when(request()->search, function ($users) {
            $users = $users->where('nik', 'like', '%' . request()->search . '%');
        })->with('roles')->orderBy('jenis_verif_nik', 'asc')->paginate(10);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    public function getDataUserNonkademik()
    {
        //get users
        $users = User::where('status', '2')->where('tipe_beasiswa', '2')->when(request()->search, function ($users) {
            $users = $users->where('nik', 'like', '%' . request()->search . '%');
        })->with('roles')->orderBy('jenis_verif_nik', 'asc')->paginate(10);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
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

    public function getDataUserKesra()
    {
        //get users
        $users = User::where('status', '2')->where('tipe_beasiswa', '3')->when(request()->search, function ($users) {
            $users = $users->where('nik', 'like', '%' . request()->search . '%');
        })->with('roles')->orderBy('jenis_verif_nik', 'asc')->paginate(10);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    public function getDataUserDinsos()
    {
        //get users
        $users = User::where('status', '2')->where('tipe_beasiswa', '4')->when(request()->search, function ($users) {
            $users = $users->where('nik', 'like', '%' . request()->search . '%');
        })->with('roles')->orderBy('jenis_verif_nik', 'asc')->paginate(10);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
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
        // Cek apakah NIK sudah terdaftar
        $nikExists = Terdaftar::where('nik', $request->nik)->exists();

        $validator = Validator::make(
            $request->all(),
            [
                'nik'    => 'required|max:16|min:16',
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
            'jenis_verif'     => "belum",
            'step'     => 1,
            'imagektp'       => $imagektp->hashName(),
            'imagekk'       => $imagekk->hashName(),
            'password'  => bcrypt($request->password)
        ]);

        //assign roles to user
        $user->assignRole(['user']);

        // Kembalikan respons dengan peringatan jika NIK sudah terdaftar
        if ($nikExists) {
            return new UserResource(true, 'Data User Berhasil Disimpan! Namun, Anda sudah menerima beasiswa di tahun sebelumnya.', $user);
        }

        //return success with Api Resource
        return new UserResource(true, 'Anda belum pernah menerima beasiswa!', $user);
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
}
