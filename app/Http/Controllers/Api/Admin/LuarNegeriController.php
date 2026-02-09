<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\LuarNegeriResource;
use App\Http\Resources\UserResource;
use App\Models\LuarNegeri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function getDataLuarNegeri()
    {
        $searchString = request()->search;

        $luarNegeris = LuarNegeri::whereHas('user', function ($query) use ($searchString) {
            $query->where('nik', 'like', '%' . $searchString . '%');
        })
            ->with(['user' => function ($query) use ($searchString) {
                $query->where('nik', 'like', '%' . $searchString . '%');
            }])->latest()->paginate(10);

        $luarNegeris->appends(['search' => request()->search]);

        //return with Api Resource
        return new LuarNegeriResource(true, 'List Data Luar Negeri', $luarNegeris);
    }

    public function show($id)
    {
        //get akademiks
        $luarNegeris = LuarNegeri::with('user')->whereId($id)->first();;

        if ($luarNegeris) {
            //return success with Api Resource
            return new LuarNegeriResource(true, 'Detail Data Luar Negeri!', $luarNegeris);
        }

        //return failed with Api Resource
        return new LuarNegeriResource(false, 'Detail Data Luar Negeri Tidak Ditemukan!', null);
    }

    public function showUuid($id)
    {
        //get luarnegeri
        $luarNegeris = LuarNegeri::with('user')->where('id', $id)->first();

        if ($luarNegeris) {
            //return success with Api Resource
            return new LuarNegeriResource(true, 'Detail Data Luar Negeri!', $luarNegeris);
        }

        //return failed with Api Resource
        return new LuarNegeriResource(false, 'Detail Data Luar Negeri Tidak Ditemukan!', null);
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
            'uuid'     => $request->uuid,
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
            return new LuarNegeriResource(true, 'Data Berhasil Disimpan!', $luarNegeris);
        }
    }

    public function updateLuarNegeri(Request $request, LuarNegeri $luarNegeri)
    {

        if ($request->file('imagetranskrip')) {
            //remove old image
            Storage::disk('local')->delete('public/luarnegeri/' . basename($luarNegeri->imagetranskrip));

            //upload new surat sktm
            $imagetranskrip = $request->file('imagetranskrip');
            $imagetranskrip->storeAs('public/luarnegeri', $imagetranskrip->hashName());

            $luarNegeri->update([
                'ipk'       => $request->ipk,
                'imagetranskrip'       => $imagetranskrip->hashName(),
            ]);
        }

        if ($request->file('imageipk')) {
            //remove old image
            Storage::disk('local')->delete('public/transkrip/' . basename($luarNegeri->imageipk));

            //upload new surat sktm
            $imageipk = $request->file('imageipk');
            $imageipk->storeAs('public/transkrip', $imageipk->hashName());

            $luarNegeri->update([
                'ipk'       => $request->ipk,
                'imageipk'       => $imageipk->hashName(),
            ]);
        }


        $luarNegeri->update([
            'ipk'       => $request->ipk,
        ]);

        if ($luarNegeri) {
            //return success with Api Resource
            return new LuarNegeriResource(true, 'Data User Berhasil Disimpan!', $luarNegeri);
        }

        //return failed with Api Resource
        return new LuarNegeriResource(false, 'Data User Gagal Disimpan!', null);
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
