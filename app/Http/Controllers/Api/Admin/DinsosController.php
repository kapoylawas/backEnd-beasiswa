<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DinsosResource;
use App\Models\Dinsos;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe_daftar'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image sertifikat
        $imagesktm = $request->file('imagesktm');
        $imagesktm->storeAs('public/sertifikat/dinsos', $imagesktm->hashName());

        $dinsos = Dinsos::create([
            'user_id'     => auth()->guard('api')->user()->id,
            'name'       => "dinsos",
            'tipe_daftar'       => $request->tipe_daftar,
            'imagesktm'       => $imagesktm->hashName(),
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
}
