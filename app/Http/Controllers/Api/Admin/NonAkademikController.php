<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\NonAkademikResource;
use App\Models\NonAkademik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NonAkademikController extends Controller
{
    public function index()
    {
        $akademiks = NonAkademik::with('user')
            ->where('user_id', auth()->user()->id)->first();

        //return with Api Resource
        return new NonAkademikResource(true, 'List Data Akademiks', $akademiks);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'semester'         => 'required',
            'akredetasi_kampus'       => 'required',
            'jenis_sertifikat'       => 'required',
            'imagesertifikat'         => 'required|mimes:pdf|max:2000',
            'tahun'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image sertifikat
        $imagesertifikat = $request->file('imagesertifikat');
        $imagesertifikat->storeAs('public/sertifikat/dispora', $imagesertifikat->hashName());

        $nonakademik = NonAkademik::create([
            'user_id'     => auth()->guard('api')->user()->id,
            'name'       => "non akademik",
            'semester'       => $request->semester,
            'akredetasi_kampus'       => $request->akredetasi_kampus,
            'jenis_sertifikat'       => $request->jenis_sertifikat,
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
}
