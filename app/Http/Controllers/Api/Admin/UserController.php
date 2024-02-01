<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        //get users
        $users = User::when(request()->search, function ($users) {
            $users = $users->where('name', 'like', '%' . request()->search . '%');
        })->with('roles')->latest()->paginate(5);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);

        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    public function userbyid()
    {
        try {
            $userbyid = User::with('akademik', 'nonakademik', 'kesra', 'dinsos', 'luarNegeri')->where('id', auth()->user()->id)->first();
        } catch (\Throwable $th) {
            dd($th);
        }

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik'    => 'required|unique:users|max:16|min:16',
            'nokk'    => 'required|unique:users|max:16|min:16',
            'name'     => 'required',
            'nohp'     => 'required',
            'email'    => 'required|unique:users',
            'gender'     => 'required',
            'id_kecamatan'     => 'required',
            'id_kelurahan'     => 'required',
            'codepos'     => 'required',
            'rt'     => 'required',
            'rw'     => 'required',
            'alamat'     => 'required',
            'imagektp'         => 'required|mimes:pdf|max:2000',
            'imagekk'         => 'required|mimes:pdf|max:2000',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // //upload imagektp
        $imagektp = $request->file('imagektp');
        $imagektp->storeAs('public/ktp', $imagektp->hashName());

        // //upload imagekk
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
        $user->assignRole($request->roles);

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
            'imageakrekampus' => 'required|mimes:pdf|max:2000',
            'pilih_universitas' => 'required',
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

        $user->update([
            'nim'       => $request->nim,
            'ktm'       => $ktm->hashName(),
            'universitas'       => $request->universitas,
            'alamat_univ'       => $request->alamat_univ,
            'jurusan'       => $request->jurusan,
            'imageaktifkampus'       => $imageaktifkampus->hashName(),
            'imagesuratpernyataan'       => $imagesuratpernyataan->hashName(),
            'imageakrekampus'       => $imageakrekampus->hashName(),
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


        $validator = Validator::make($request->all(), [
            'nim'     => 'required',
            'universitas' => 'required',
            'jurusan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // //upload new ktm
        // $ktm = $request->file('ktm');
        // $ktm->storeAs('public/ktm', $ktm->hashName());

        // //upload new imageaktifkampus
        // $imageaktifkampus = $request->file('imageaktifkampus');
        // $imageaktifkampus->storeAs('public/imageaktifkampus', $imageaktifkampus->hashName());

        // //upload new imagesuratpernyataan
        // $imagesuratpernyataan = $request->file('imagesuratpernyataan');
        // $imagesuratpernyataan->storeAs('public/imagesuratpernyataan', $imagesuratpernyataan->hashName());

        // //upload new imageakrekampus
        // $imageakrekampus = $request->file('imageakrekampus');
        // $imageakrekampus->storeAs('public/imageakrekampus', $imageakrekampus->hashName());

        $user->update([
            'nim'       => $request->nim,
            'universitas'       => $request->universitas,
            'alamat_univ'       => $request->alamat_univ,
            'jurusan'       => $request->jurusan,
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
}
