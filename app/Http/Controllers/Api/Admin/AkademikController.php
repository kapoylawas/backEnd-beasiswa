<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AkademikResource;
use App\Models\Akademik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $akademiks = Akademik::with('user')
            ->where('user_id', auth()->user()->id)->first();

        //return with Api Resource
        return new AkademikResource(true, 'List Data Akademiks', $akademiks);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ipk'         => 'required',
            'semester'       => 'required',
            'akredetasi_kampus'       => 'required',
            'akredetasi_jurusan'       => 'required',
            'progam_pendidikan'       => 'required',
            'imagetranskrip'         => 'required|mimes:pdf|max:2000',
            'imageketerangan'         => 'required|mimes:pdf|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image transkrip
        $imagetranskrip = $request->file('imagetranskrip');
        $imagetranskrip->storeAs('public/transkrip', $imagetranskrip->hashName());

        //upload image surat keterangan
        $imageketerangan = $request->file('imageketerangan');
        $imageketerangan->storeAs('public/suratketerangan', $imageketerangan->hashName());

        $akademik = Akademik::create([
            'user_id'     => auth()->guard('api')->user()->id,
            'name'       => "akademik",
            'ipk'       => $request->ipk,
            'semester'       => $request->semester,
            'akredetasi_kampus'       => $request->akredetasi_kampus,
            'akredetasi_jurusan'       => $request->akredetasi_jurusan,
            'progam_pendidikan'       => $request->progam_pendidikan,
            'imagetranskrip'       => $imagetranskrip->hashName(),
            'imageketerangan'       => $imageketerangan->hashName(),
        ]);

        if ($akademik) {
            User::where('id', auth()->guard('api')->user()->id)->update([
                'status_pendaftar' => 1,
                'step'     => 3,
                'tipe_beasiswa'     => 1,
            ]);
            //return success with Api Resource
            return new AkademikResource(true, 'Data Post Berhasil Disimpan!', $akademik);
        }

        //return failed with Api Resource
        return new AkademikResource(false, 'Data Post Gagal Disimpan!', null);
    }
}
