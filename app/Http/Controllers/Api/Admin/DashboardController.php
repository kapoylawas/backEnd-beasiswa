<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akademik;
use App\Models\Dinsos;
use App\Models\Kesra;
use App\Models\LuarNegeri;
use App\Models\NonAkademik;
use App\Models\User;
use App\Models\YatimPiatu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // count users registed
        $users = User::where('status', 2)->count();
        $akademiks = Akademik::count();
        $nonAkademiks = NonAkademik::count();
        $dinsoses = Dinsos::count();
        $kesras = Kesra::count();
        $luarNegeris = LuarNegeri::count();
        $yatims = YatimPiatu::count();
        //hitung sudah verif akademik
        $jumlahLolosVerifAkademik = User::where('tipe_beasiswa', 1)
            ->where('jenis_verif', 'lolos')
            ->count();
        $jumlahTidakVerifAkademik = User::where('tipe_beasiswa', 1)
            ->where('jenis_verif', 'tidak')
            ->count();
        $jumlahSudahVerifAkademik = $jumlahLolosVerifAkademik + $jumlahTidakVerifAkademik;

        //hitung sudah verif nonakademik
        $jumlahLolosVerifNonAkademik = User::where('tipe_beasiswa', 2)
            ->where('jenis_verif', 'lolos')
            ->count();
        $jumlahTidakVerifNonAkademik = User::where('tipe_beasiswa', 2)
            ->where('jenis_verif', 'tidak')
            ->count();
        $jumlahSudahVerifNonAkademik = $jumlahLolosVerifNonAkademik + $jumlahTidakVerifNonAkademik;

        //hitung sudah verif kesra
        $jumlahLolosVerifKesra = User::where('tipe_beasiswa', 3)
            ->where('jenis_verif', 'lolos')
            ->count();
        $jumlahTidakVerifKesra = User::where('tipe_beasiswa', 3)
            ->where('jenis_verif', 'tidak')
            ->count();
        $jumlahSudahVerifKesra = $jumlahLolosVerifKesra + $jumlahTidakVerifKesra;

        //hitung sudah verif dinsos
        $jumlahLolosVerifDinsos = User::where('tipe_beasiswa', 4)
            ->where('jenis_verif', 'lolos')
            ->count();
        $jumlahTidakVerifDinsos = User::where('tipe_beasiswa', 4)
            ->where('jenis_verif', 'tidak')
            ->count();
        $jumlahSudahVerifDinsos = $jumlahLolosVerifDinsos + $jumlahTidakVerifDinsos;

        //hitung sudah verif luarnegeri
        $jumlahLolosVerifNikLuarNegeri = User::where('tipe_beasiswa', 5)
            ->where('jenis_verif', 'lolos')
            ->count();
        $jumlahTidakVerifNikLuarNegeri = User::where('tipe_beasiswa', 5)
            ->where('jenis_verif', 'tidak')
            ->count();
        $jumlahSudahVerifLuarNegeri = $jumlahLolosVerifNikLuarNegeri + $jumlahTidakVerifNikLuarNegeri;

        /* count verifk nik */
        $jumlahSudahVerifNikAkademik = User::where('tipe_beasiswa', 1)
            ->whereNotNull('jenis_verif_nik')
            ->count();
        $jumlahSudahVerifNikNonAkademik = User::where('tipe_beasiswa', 2)
            ->whereNotNull('jenis_verif_nik')
            ->count();
        $jumlahSudahVerifNikKesra = User::where('tipe_beasiswa', 3)
            ->whereNotNull('jenis_verif_nik')
            ->count();
        $jumlahSudahVerifNikDinsos = User::where('tipe_beasiswa', 4)
            ->whereNotNull('jenis_verif_nik')
            ->count();
        $jumlahSudahVerifNikLuarNegeri = User::where('tipe_beasiswa', 5)
            ->whereNotNull('jenis_verif_nik')
            ->count();
        $jumlahSudahVerifYatim = YatimPiatu::whereNotNull('status_data')
            ->count();
        // $jumlahSudahVerifYatimKK = YatimPiatu::whereNotNull('verif_kk')
        //     ->count();

        // Mengambil data dengan join
        $terdaftar = DB::table('users')
            ->join('terdaftar', 'users.nik', '=', 'terdaftar.nik')
            ->select('terdaftar.name', 'terdaftar.nim', 'terdaftar.universitas', 'terdaftar.nik', 'terdaftar.tahun')
            ->first();
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
                'yatims' => $yatims,
                'nonAkademiks' => $nonAkademiks,
                'jumlahSudahVerifAkademik' => $jumlahSudahVerifAkademik,
                'jumlahSudahVerifNonAkademik' => $jumlahSudahVerifNonAkademik,
                'jumlahSudahVerifKesra' => $jumlahSudahVerifKesra,
                'jumlahSudahVerifDinsos' => $jumlahSudahVerifDinsos,
                'jumlahSudahVerifLuarNegeri' => $jumlahSudahVerifLuarNegeri,
                'jumlahSudahVerifNikAkademik' => $jumlahSudahVerifNikAkademik,
                'jumlahSudahVerifNikNonAkademik' => $jumlahSudahVerifNikNonAkademik,
                'jumlahSudahVerifNikKesra' => $jumlahSudahVerifNikKesra,
                'jumlahSudahVerifNikDinsos' => $jumlahSudahVerifNikDinsos,
                'jumlahSudahVerifNikLuarNegeri' => $jumlahSudahVerifNikLuarNegeri,
                'jumlahSudahVerifYatim' => $jumlahSudahVerifYatim,
                // 'jumlahSudahVerifYatimKK' => $jumlahSudahVerifYatimKK,
            ],
            'terdaftar' => $terdaftar
        ]);
    }
}
