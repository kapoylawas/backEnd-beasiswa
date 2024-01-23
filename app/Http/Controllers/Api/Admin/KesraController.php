<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\KesraResource;
use App\Models\Kesra;
use App\Models\User;
use Illuminate\Http\Request;
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

        $kesra = Kesra::create([
            'user_id'     => auth()->guard('api')->user()->id,
            'name'       => "Kesra",
            'tipe_sertifikat'       => $request->tipe_sertifikat,
            'imagesertifikat'       => $imagesertifikat->hashName(),
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
}
