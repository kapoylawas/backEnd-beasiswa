<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        //get users
        $users = User::when(request()->search, function($users) {
            $users = $users->where('name', 'like', '%'. request()->search . '%');
        })->with('roles')->latest()->paginate(5);

        //append query string to pagination links
        $users->appends(['search' => request()->search]);
        
        //return with Api Resource
        return new UserResource(true, 'List Data Users', $users);
    }

    public function userbyid()
     {
        try {
            $userbyid = User::where('id', auth()->user()->id)->first();
        } catch (\Throwable $th) {
            dd($th);
        }
 
         //return with Api Resource
         return new UserResource(true, 'List Data User', $userbyid);
     }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik'    => 'required|unique:users|max:16|min:16',
            'nokk'    => 'required|unique:users|max:16|min:16',
            'name'     => 'required',
            'nohp'     => 'required',
            'email'    => 'required|unique:users',
            'gender'     => 'required',
            'kecamatan'     => 'required',
            'codepos'     => 'required',
            'rt'     => 'required',
            'rw'     => 'required',
            'alamat'     => 'required',
            'imagektp'         => 'required|mimes:pdf|max:2000',
            'imagekk'         => 'required|mimes:pdf|max:2000',
            'password' => 'required|confirmed' 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload imagektp
        $imagektp = $request->file('imagektp');
        $imagektp->storeAs('public/ktp', $imagektp->hashName());

        //upload imagekk
        $imagekk = $request->file('imagekk');
        $imagekk->storeAs('public/kk', $imagekk->hashName());

        //create user
        $user = User::create([
            'nik'     => $request->nik,
            'nokk'     => $request->nokk,
            'name'      => $request->name,
            'nohp'      => $request->nohp,
            'email'     => $request->email,
            'gender'     => $request->gender,
            'kecamatan'     => $request->kecamatan,
            'codepos'     => $request->codepos,
            'rt'     => $request->rt,
            'rw'     => $request->rw,
            'alamat'     => $request->alamat,
            'status'     => 2,
            'imagektp'       => $imagektp->hashName(),
            'imagekk'       => $imagekk->hashName(),
            'password'  => bcrypt($request->password)
        ]);

        //assign roles to user
        $user->assignRole($request->roles);

        if($user) {
            //return success with Api Resource
            return new UserResource(true, 'Data User Berhasil Disimpan!', $user);
        }

        //return failed with Api Resource
        return new UserResource(false, 'Data User Gagal Disimpan!', null);
    }
}
