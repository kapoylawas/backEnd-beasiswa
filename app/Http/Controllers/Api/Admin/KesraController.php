<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\KesraResource;
use App\Http\Resources\UserResource;
use App\Models\Kesra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KesraController extends Controller
{
    public function index()
    {
        $kesras = Kesra::with('user')
            ->where('user_id', auth()->user()->id)->first();

        //return with Api Resource
        return new KesraResource(true, 'List Data Kesra', $kesras);
    }

    public function showUuid($uuid)
    {
        //get kesra
        $kesras = Kesra::with('user')->where('uuid', $uuid)->first();

        if ($kesras) {
            //return success with Api Resource
            return new KesraResource(true, 'Detail Data Kesra!', $kesras);
        }

        //return failed with Api Resource
        return new KesraResource(false, 'Detail Data Kesra!', null);
    }

    public function getDataKesra1()
    {
        $searchString = request()->search;

        $kesras = Kesra::where('tipe_kesra', '1')->whereHas('user', function ($query) use ($searchString) {
            $query->where('nik', 'like', '%' . $searchString . '%');
        })
            ->with(['user' => function ($query) use ($searchString) {
                $query->where('nik', 'like', '%' . $searchString . '%');
            }])->latest()->paginate(10);

        $kesras->appends(['search' => request()->search]);

        //return with Api Resource
        return new KesraResource(true, 'List Data kesra', $kesras);
    }

    public function getDataKesra2()
    {
        $searchString = request()->search;

        $kesras = Kesra::where('tipe_kesra', '2')->whereHas('user', function ($query) use ($searchString) {
            $query->where('nik', 'like', '%' . $searchString . '%');
        })
            ->with(['user' => function ($query) use ($searchString) {
                $query->where('nik', 'like', '%' . $searchString . '%');
            }])->latest()->paginate(10);

        $kesras->appends(['search' => request()->search]);

        //return with Api Resource
        return new KesraResource(true, 'List Data kesra', $kesras);
    }

    public function getDataKesra3()
    {
        $searchString = request()->search;

        $kesras = Kesra::where('tipe_kesra', '3')->whereHas('user', function ($query) use ($searchString) {
            $query->where('nik', 'like', '%' . $searchString . '%');
        })
            ->with(['user' => function ($query) use ($searchString) {
                $query->where('nik', 'like', '%' . $searchString . '%');
            }])->latest()->paginate(10);

        $kesras->appends(['search' => request()->search]);

        //return with Api Resource
        return new KesraResource(true, 'List Data kesra', $kesras);
    }

    public function getDataKesra4()
    {
        $searchString = request()->search;

        $kesras = Kesra::where('tipe_kesra', '4')->whereHas('user', function ($query) use ($searchString) {
            $query->where('nik', 'like', '%' . $searchString . '%');
        })
            ->with(['user' => function ($query) use ($searchString) {
                $query->where('nik', 'like', '%' . $searchString . '%');
            }])->latest()->paginate(10);

        $kesras->appends(['search' => request()->search]);

        //return with Api Resource
        return new KesraResource(true, 'List Data kesra', $kesras);
    }


    public function show($id)
    {
        //get dinsos
        $kesras = Kesra::with('user')->whereId($id)->first();

        if ($kesras) {
            //return success with Api Resource
            return new KesraResource(true, 'Detail Data Kesra!', $kesras);
        }

        //return failed with Api Resource
        return new KesraResource(false, 'Detail Data Kesra Tidak Ditemukan!', null);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe_sertifikat'         => 'required',
            'imagesertifikat'         => 'required|mimes:pdf|max:2000',
            'tahun'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image sertifikat
        $imagesertifikat = $request->file('imagesertifikat');
        $imagesertifikat->storeAs('public/sertifikat/kesra', $imagesertifikat->hashName());

        //upload image sertifikat non muslim
        $imagepiagamnonmuslim = $request->file('imagepiagamnonmuslim');
        if ($imagepiagamnonmuslim != null) {
            $imagepiagamnonmuslim->storeAs('public/sertifikat/kesra', $imagepiagamnonmuslim->hashName());
        }

        $kesra = Kesra::create([
            'user_id'     => auth()->guard('api')->user()->id,
            'uuid'     => $request->uuid,
            'name'       => "Kesra",
            'tipe_kesra'       => $request->tipe_kesra,
            'tipe_sertifikat'       => $request->tipe_sertifikat,
            'nama_ponpes'       => $request->nama_ponpes,
            'alamat_ponpes'       => $request->alamat_ponpes,
            'nama_organisasi'       => $request->nama_organisasi,
            'alamat_organisasi'       => $request->alamat_organisasi,
            'imagesertifikat'       => $imagesertifikat->hashName(),
            'imagepiagamnonmuslim'       => ($imagepiagamnonmuslim != null) ? $imagepiagamnonmuslim->hashName() : null,
            'tahun'       => $request->tahun,
        ]);

        if ($kesra) {
            User::where('id', auth()->guard('api')->user()->id)->update([
                'status_pendaftar' => 1,
                'step'     => 3,
                'tipe_beasiswa'     => 3,
            ]);
            //return success with Api Resource
            return new KesraResource(true, 'Data Kesra Berhasil Disimpan!', $kesra);
        }

        //return failed with Api Resource
        return new KesraResource(false, 'Data Kesra Gagal Disimpan!', null);
    }

    public function updateKesra(Request $request, Kesra $kesra)
    {

        if ($request->file('imagesertifikat')) {
            //remove old image
            Storage::disk('local')->delete('public/sertifikat/kesra/' . basename($kesra->imagesertifikat));

            //upload new imagesertifikat
            $imagesertifikat = $request->file('imagesertifikat');
            $imagesertifikat->storeAs('public/sertifikat/kesra', $imagesertifikat->hashName());

            $kesra->update([
                'nama_ponpes'       => $request->nama_ponpes,
                'alamat_ponpes'       => $request->alamat_ponpes,
                'nama_organisasi'       => $request->nama_organisasi,
                'alamat_organisasi'       => $request->alamat_organisasi,
                'imagesertifikat'       => $imagesertifikat->hashName(),
                'tahun'       => $request->tahun,
            ]);
        }

        if ($request->file('imagepiagamnonmuslim')) {
            //remove old image
            Storage::disk('local')->delete('public/sertifikat/kesra/' . basename($kesra->imagepiagamnonmuslim));

            //upload new imagepiagamnonmuslim
            $imagepiagamnonmuslim = $request->file('imagepiagamnonmuslim');
            $imagepiagamnonmuslim->storeAs('public/sertifikat/kesra', $imagepiagamnonmuslim->hashName());

            $kesra->update([
                'nama_ponpes'       => $request->nama_ponpes,
                'alamat_ponpes'       => $request->alamat_ponpes,
                'nama_organisasi'       => $request->nama_organisasi,
                'alamat_organisasi'       => $request->alamat_organisasi,
                'imagepiagamnonmuslim'       => $imagepiagamnonmuslim->hashName(),
                'tahun'       => $request->tahun,
            ]);
        }

        $kesra->update([
            'nama_ponpes'       => $request->nama_ponpes,
            'alamat_ponpes'       => $request->alamat_ponpes,
            'nama_organisasi'       => $request->nama_organisasi,
            'alamat_organisasi'       => $request->alamat_organisasi,
            'tahun'       => $request->tahun,
        ]);

        if ($kesra) {
            //return success with Api Resource
            return new KesraResource(true, 'Data User Berhasil Disimpan!', $kesra);
        }

        //return failed with Api Resource
        return new KesraResource(false, 'Data User Gagal Disimpan!', null);
    }

    public function updateVerif(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'alasan'     => 'required',
            'jenis_verif'    => 'required',
        ], [
            'alasan.required' => 'alasan verifikasi tidak boleh kosong',
            'jenis_verif.required' => 'pilih jenis verifikasi terlebih dahulu',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->update([
            'alasan'       => $request->alasan,
            'jenis_verif'       => $request->jenis_verif,
            'verifikator_berkas'       => $request->verifikator,
        ]);

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Verifikasi Data Berhasil Disimpan!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Verifikasi Data Gagal Disimpan!', null);
    }
}
