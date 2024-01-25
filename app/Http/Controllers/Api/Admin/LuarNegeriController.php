<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\LuarNegeriResource;
use App\Models\LuarNegeri;
use Illuminate\Http\Request;

class LuarNegeriController extends Controller
{
    public function index()
    {
        $luarNegeris = LuarNegeri::with('user')
            ->where('user_id', auth()->user()->id)->first();

        //return with Api Resource
        return new LuarNegeriResource(true, 'List Data Luar Negeri', $luarNegeris);
    }
}
