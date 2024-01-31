<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\LuarNegeriResource;
use App\Models\LuarNegeri;
use App\Models\User;
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
            'imagetranskrip'         => 'required|mimes:pdf|max:2000',
            'imageipk'         => 'required|mimes:pdf|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image transkrip
        $imagetranskrip = $request->file('imagetranskrip');
        $imagetranskrip->storeAs('public/luarnegeri', $imagetranskrip->hashName());

        //upload image ipk
        $imageipk = $request->file('imageipk');
        $imageipk->storeAs('public/transkrip', $imageipk->hashName());

        $luarNegeris = LuarNegeri::create([
            'user_id'     => auth()->guard('api')->user()->id,
            'name'       => "luar",
            'ipk'       => $request->ipk,
            'imagetranskrip'       => $imagetranskrip->hashName(),
            'imageipk'       => $imageipk->hashName(),
        ]);

        if ($luarNegeris) {
            User::where('id', auth()->guard('api')->user()->id)->update([
                'status_pendaftar' => 1,
                'step'     => 3,
                'tipe_beasiswa'     => 5,
            ]);
            //return success with Api Resource
            return new LuarNegeriResource(true, 'Data Post Berhasil Disimpan!', $luarNegeris);
        }
    }
}
