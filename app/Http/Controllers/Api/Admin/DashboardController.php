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
        $jumlahSudahVerifAkademik = User::where('tipe_beasiswa', 1)
                                ->whereNotNull('jenis_verif')
                                ->count();
        $jumlahSudahVerifNonAkademik = User::where('tipe_beasiswa', 2)
                                ->whereNotNull('jenis_verif')
                                ->count();
        $jumlahSudahVerifKesra = User::where('tipe_beasiswa', 3)
                                ->whereNotNull('jenis_verif')
                                ->count();
        $jumlahSudahVerifDinsos = User::where('tipe_beasiswa', 4)
                                ->whereNotNull('jenis_verif')
                                ->count();
        $jumlahSudahVerifLuarNegeri = User::where('tipe_beasiswa', 4)
                                ->whereNotNull('jenis_verif')
                                ->count();
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
                'jumlahSudahVerifAkademik' => $jumlahSudahVerifAkademik,
                'jumlahSudahVerifNonAkademik' => $jumlahSudahVerifNonAkademik,
                'jumlahSudahVerifKesra' => $jumlahSudahVerifKesra,
                'jumlahSudahVerifDinsos' => $jumlahSudahVerifDinsos,
                'jumlahSudahVerifLuarNegeri' => $jumlahSudahVerifLuarNegeri,
            ]
        ]);
    }
}
