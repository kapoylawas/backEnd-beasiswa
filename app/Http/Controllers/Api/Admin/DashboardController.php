<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akademik;
use App\Models\Dinsos;
use App\Models\Kesra;
use App\Models\LuarNegeri;
use App\Models\NonAkademik;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // count users registed
        $users = User::count();
        $akademiks = Akademik::count();
        $nonAkademiks = NonAkademik::count();
        $dinsoses = Dinsos::count();
        $kesras = Kesra::count();
        $luarNegeris = LuarNegeri::count();

        //return response json
        return response()->json([
            'success'   => true,
            'message'   => 'List Data on Dashboard',
            'data'      => [
                'users' => $users,
                'akademiks' => $akademiks,
                'dinsoses' => $dinsoses,
                'kesras' => $kesras,
                'luarNegeris' => $luarNegeris,
                'nonAkademiks' => $nonAkademiks,
            ]
        ]);
    }
}
