<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DinsosResource;
use App\Http\Resources\UserResource;
use App\Models\Dinsos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DinsosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $dinsos = Dinsos::with('user')
            ->where('user_id', auth()->user()->id)->first();

        //return with Api Resource
        return new DinsosResource(true, 'List Data Dinsos', $dinsos);
    }

    public function showUuid($uuid)
    {
        //get akademiks
        $dinsos = Dinsos::with('user')->where('uuid', $uuid)->first();

        if ($dinsos) {
            //return success with Api Resource
            return new DinsosResource(true, 'Detail Data Dinsos!', $dinsos);
        }

        //return failed with Api Resource
        return new DinsosResource(false, 'Detail Data Dinsos!', null);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe_daftar'       => 'required',
            // 'imagesktm'         => 'mimes:pdf|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $imagesktm = $request->file('imagesktm');
        if ($imagesktm != null) {
            $imagesktm->storeAs('public/sertifikat/dinsos', $imagesktm->hashName());
        }

        $dinsos = Dinsos::create([
            'user_id'     => auth()->guard('api')->user()->id,
            'uuid'     => $request->uuid,
            'name'       => "dinsos",
            'tipe_daftar'       => $request->tipe_daftar,
            'penghasilan_orangtua'       => $request->penghasilan_orangtua,
            'pekerjaan_orangtua'       => $request->pekerjaan_orangtua,
            'status_rumah'       => $request->status_rumah,
            'status_kendaraan'       => $request->status_kendaraan,
            'jumlah_kendaraan'       => $request->jumlah_kendaraan,
            'imagesktm'       => ($imagesktm != null) ? $imagesktm->hashName() : null,
        ]);

        if ($dinsos) {
            User::where('id', auth()->guard('api')->user()->id)->update([
                'status_pendaftar' => 1,
                'step'     => 3,
                'tipe_beasiswa'     => 4,
            ]);
            //return success with Api Resource
            return new DinsosResource(true, 'Data Dinsos Berhasil Disimpan!', $dinsos);
        }

        //return failed with Api Resource
        return new DinsosResource(false, 'Data Dinsos Gagal Disimpan!', null);
    }

    public function getDataDinsosDtks()
    {
        $searchString = request()->search;

        $kesras = Dinsos::where('tipe_daftar', '1')->whereHas('user', function ($query) use ($searchString) {
            $query->where('nik', 'like', '%' . $searchString . '%');
        })
            ->with(['user' => function ($query) use ($searchString) {
                $query->where('nik', 'like', '%' . $searchString . '%');
            }])->latest()->paginate(10);

        $kesras->appends(['search' => request()->search]);

        //return with Api Resource
        return new DinsosResource(true, 'List Data Luar Negeri', $kesras);
    }

    public function getDataDinsosNoDtks()
    {
        $searchString = request()->search;

        $kesras = Dinsos::where('tipe_daftar', '2')->whereHas('user', function ($query) use ($searchString) {
            $query->where('nik', 'like', '%' . $searchString . '%');
        })
            ->with(['user' => function ($query) use ($searchString) {
                $query->where('nik', 'like', '%' . $searchString . '%');
            }])->latest()->paginate(10);

        $kesras->appends(['search' => request()->search]);

        //return with Api Resource
        return new DinsosResource(true, 'List Data Luar Negeri', $kesras);
    }

    public function show($id)
    {
        //get dinsos
        $dinsos = Dinsos::with('user')->whereId($id)->first();

        if ($dinsos) {
            //return success with Api Resource
            return new DinsosResource(true, 'Detail Data Dinsos!', $dinsos);
        }

        //return failed with Api Resource
        return new DinsosResource(false, 'Detail Data Dinsos Tidak Ditemukan!', null);
    }

    public function updateDinsos(Request $request, Dinsos $dinsos)
    {

        if ($request->file('imagesktm')) {
            //remove old image
            Storage::disk('local')->delete('public/sertifikat/dinsos/' . basename($dinsos->imagesktm));

            //upload new surat sktm
            $imagesktm = $request->file('imagesktm');
            $imagesktm->storeAs('public/sertifikat/dinsos', $imagesktm->hashName());

            $dinsos->update([
                'tipe_daftar'       => $request->tipe_daftar,
                'imagesktm'       => $imagesktm->hashName(),
            ]);
        }

        $dinsos->update([
            'tipe_daftar'       => $request->tipe_daftar,
            'penghasilan_orangtua'       => $request->penghasilan_orangtua,
            'pekerjaan_orangtua'       => $request->pekerjaan_orangtua,
            'status_rumah'       => $request->status_rumah,
            'status_kendaraan'       => $request->status_kendaraan,
            'jumlah_kendaraan'       => $request->jumlah_kendaraan,
        ]);

        if ($dinsos) {
            //return success with Api Resource
            return new DinsosResource(true, 'Data User Berhasil Disimpan!', $dinsos);
        }

        //return failed with Api Resource
        return new DinsosResource(false, 'Data User Gagal Disimpan!', null);
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
            'verifikator_berkas'       => $request->verifikator_berkas,
        ]);

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Verifikasi Data Berhasil Disimpan!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Verifikasi Data Gagal Disimpan!', null);
    }
}
