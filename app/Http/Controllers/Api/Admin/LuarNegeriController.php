<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\LuarNegeriResource;
use App\Models\LuarNegeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class LuarNegeriController extends Controller
{
    public function index()
    {
        $luarNegeris = LuarNegeri::with('user')
            ->where('user_id', auth()->user()->id)->first();

        //return with Api Resource
        return new LuarNegeriResource(true, 'List Data Luar Negeri', $luarNegeris);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ipk'         => 'required',
            'semester'       => 'required',
            'akredetasi_kampus'       => 'required',
            'imagetranskrip'         => 'required|mimes:pdf|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image transkrip
        $imagetranskrip = $request->file('imagetranskrip');
        $imagetranskrip->storeAs('public/transkrip', $imagetranskrip->hashName());

        $luarNegeris = LuarNegeri::create([
            'user_id'     => auth()->guard('api')->user()->id,
            'name'       => "luar",
            'ipk'       => $request->ipk,
            'semester'       => $request->semester,
            'akredetasi_kampus'       => $request->akredetasi_kampus,
            'imagetranskrip'       => $imagetranskrip->hashName(),
        ]);

        if ($luarNegeris) {
            LuarNegeri::where('id', auth()->guard('api')->user()->id)->update([
                'status_pendaftar' => 1,
                'step'     => 3,
                'tipe_beasiswa'     => 5,
            ]);
            //return success with Api Resource
            return new LuarNegeriResource(true, 'Data Post Berhasil Disimpan!', $luarNegeris);
        }
    }
}
