<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\NonAkademikResource;
use App\Http\Resources\UserResource;
use App\Models\NonAkademik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class NonAkademikController extends Controller
{
    public function index()
    {
        $akademiks = NonAkademik::with('user')
            ->where('user_id', auth()->user()->id)->first();

        //return with Api Resource
        return new NonAkademikResource(true, 'List Data Akademiks', $akademiks);
    }

    public function getDataNonAkademik()
    {
        $searchString = request()->search;

        $nonAkademiks = NonAkademik::whereHas('user', function ($query) use ($searchString) {
            $query->where('nik', 'like', '%' . $searchString . '%');
        })
            ->with(['user' => function ($query) use ($searchString) {
                $query->where('nik', 'like', '%' . $searchString . '%');
            }])->latest()->paginate(10);

        $nonAkademiks->appends(['search' => request()->search]);

        //return with Api Resource
        return new NonAkademikResource(true, 'List Data Akademiks', $nonAkademiks);
    }

    public function show($id)
    {
        //get akademiks
        $nonAkademiks = NonAkademik::with('user')->whereId($id)->first();;

        if ($nonAkademiks) {
            //return success with Api Resource
            return new NonAkademikResource(true, 'Detail Data Non Akademik!', $nonAkademiks);
        }

        //return failed with Api Resource
        return new NonAkademikResource(false, 'Detail Data Non Akademik Tidak Ditemukan!', null);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'semester'         => 'required',
            'jenis_sertifikat'       => 'required',
            'tingkat_sertifikat'       => 'required',
            'imagesertifikat'         => 'required|mimes:pdf|max:2000',
            'tahun'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image sertifikat
        $imagesertifikat = $request->file('imagesertifikat');
        $imagesertifikat->storeAs('public/sertifikat/dispora', $imagesertifikat->hashName());

        // $imageakredetasi = $request->file('imageakredetasi');
        // $imageakredetasi->storeAs('public/imageakrekampus', $imageakredetasi->hashName());

        $nonakademik = NonAkademik::create([
            'user_id'     => auth()->guard('api')->user()->id,
            'name'       => "non akademik",
            'semester'       => $request->semester,
            'jenis_sertifikat'       => $request->jenis_sertifikat,
            'tingkat_sertifikat'       => $request->tingkat_sertifikat,
            'imagesertifikat'       => $imagesertifikat->hashName(),
            'tahun'       => $request->tahun,
        ]);

        if ($nonakademik) {
            User::where('id', auth()->guard('api')->user()->id)->update([
                'status_pendaftar' => 1,
                'step'     => 3,
                'tipe_beasiswa'     => 2,
            ]);
            //return success with Api Resource
            return new NonAkademikResource(true, 'Data Post Berhasil Disimpan!', $nonakademik);
        }

        //return failed with Api Resource
        return new NonAkademikResource(false, 'Data Post Gagal Disimpan!', null);
    }

    public function updateNonAkademik(Request $request, NonAkademik $nonAkademik)
    {
        if ($request->file('imagesertifikat')) {
            //remove old image
            Storage::disk('local')->delete('public/sertifikat/dispora/' . basename($nonAkademik->imagesertifikat));

            //upload new transkip
            $imagesertifikat = $request->file('imagesertifikat');
            $imagesertifikat->storeAs('public/sertifikat/dispora', $imagesertifikat->hashName());

            $nonAkademik->update([
                'semester'       => $request->semester,
                'jenis_sertifikat'       => $request->jenis_sertifikat,
                'tingkat_sertifikat'       => $request->tingkat_sertifikat,
                'tahun'       => $request->tahun,
                'imagesertifikat'       => $imagesertifikat->hashName(),
            ]);
        }

        $nonAkademik->update([
            'semester'       => $request->semester,
            'jenis_sertifikat'       => $request->jenis_sertifikat,
            'tingkat_sertifikat'       => $request->tingkat_sertifikat,
            'tahun'       => $request->tahun,
        ]);

        if ($nonAkademik) {
            //return success with Api Resource
            return new NonAkademikResource(true, 'Data User Berhasil Disimpan!', $nonAkademik);
        }

        //return failed with Api Resource
        return new NonAkademikResource(false, 'Data User Gagal Disimpan!', null);
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
        ]);

        if ($user) {
            //return success with Api Resource
            return new UserResource(true, 'Verifikasi Data Berhasil Disimpan!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Verifikasi Data Gagal Disimpan!', null);
    }
}
