<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AkademikResource;
use App\Models\Akademik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


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

    public function show($id)
    {
        //get akademiks
        $akademiks = Akademik::with('user')->whereId($id)->first();;

        if($akademiks) {
            //return success with Api Resource
            return new AkademikResource(true, 'Detail Data Akademik!', $akademiks);
        }

        //return failed with Api Resource
        return new AkademikResource(false, 'Detail Data Akademik Tidak Ditemukan!', null);
    }

    public function getData()
    {
        $searchString = request()->search;

        $akademiks = Akademik::whereHas('user', function ($query) use ($searchString) {
            $query->where('nik', 'like', '%' . $searchString . '%');
        })
            ->with(['user' => function ($query) use ($searchString) {
                $query->where('nik', 'like', '%' . $searchString . '%');
            }])->latest()->paginate(10);

        $akademiks->appends(['search' => request()->search]);
        
        //return with Api Resource
        return new AkademikResource(true, 'List Data Akademiks', $akademiks);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ipk'         => 'required',
            'semester'       => 'required',
            'akredetasi_kampus'       => 'required',
            'progam_pendidikan'       => 'required',
            'imagetranskrip'         => 'required|mimes:pdf|max:2000',
            // 'imageketerangan'         => 'required|mimes:pdf|max:2000',
            'imagebanpt'         => 'required|mimes:pdf|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image transkrip
        $imagetranskrip = $request->file('imagetranskrip');
        $imagetranskrip->storeAs('public/transkrip', $imagetranskrip->hashName());

        //upload image banpt
        $imagebanpt = $request->file('imagebanpt');
        $imagebanpt->storeAs('public/banpt', $imagebanpt->hashName());

        // //upload image surat keterangan
        // $imageketerangan = $request->file('imageketerangan');
        // $imageketerangan->storeAs('public/suratketerangan', $imageketerangan->hashName());


        $akademik = Akademik::create([
            'user_id'     => auth()->guard('api')->user()->id,
            'name'       => "akademik",
            'ipk'       => $request->ipk,
            'semester'       => $request->semester,
            'akredetasi_kampus'       => $request->akredetasi_kampus,
            'progam_pendidikan'       => $request->progam_pendidikan,
            'imagetranskrip'       => $imagetranskrip->hashName(),
            // 'imageketerangan'       => $imageketerangan->hashName(),
            'imagebanpt'       => $imagebanpt->hashName(),
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

    public function updateAkademik(Request $request, Akademik $akademik)
    {
        if ($request->file('imagetranskrip')) {
            //remove old image
            Storage::disk('local')->delete('public/transkrip/' . basename($akademik->imagetranskrip));

            //upload new transkip
            $imagetranskrip = $request->file('imagetranskrip');
            $imagetranskrip->storeAs('public/transkrip', $imagetranskrip->hashName());

            $akademik->update([
                'ipk'       => $request->ipk,
                'semester'       => $request->semester,
                'akredetasi_kampus'       => $request->akredetasi_kampus,
                'progam_pendidikan'       => $request->progam_pendidikan,
                'imagetranskrip'       => $imagetranskrip->hashName(),
            ]);
        }

        if ($request->file('imagebanpt')) {
            //remove old image
            Storage::disk('local')->delete('public/banpt/' . basename($akademik->imagebanpt));

            //upload new surat keterangan
            $imagebanpt = $request->file('imagebanpt');
            $imagebanpt->storeAs('public/banpt', $imagebanpt->hashName());

            $akademik->update([
                'ipk'       => $request->ipk,
                'semester'       => $request->semester,
                'akredetasi_kampus'       => $request->akredetasi_kampus,
                'progam_pendidikan'       => $request->progam_pendidikan,
                'imagebanpt'       => $imagebanpt->hashName(),
            ]);
        }

        $akademik->update([
            'ipk'       => $request->ipk,
            'semester'       => $request->semester,
            'akredetasi_kampus'       => $request->akredetasi_kampus,
            'progam_pendidikan'       => $request->progam_pendidikan,
        ]);

        if ($akademik) {
            //return success with Api Resource
            return new AkademikResource(true, 'Data User Berhasil Disimpan!', $akademik);
        }

        //return failed with Api Resource
        return new AkademikResource(false, 'Data User Gagal Disimpan!', null);
    }
}
