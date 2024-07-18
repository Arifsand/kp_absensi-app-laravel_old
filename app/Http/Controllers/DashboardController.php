<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // ## Untuk Menampilkan Data Absensi Hari Ini di Dashboard ## 
        $hariini = date("Y-m-d");
        $bulanini = date("m"); // # Bulan Sekarang #
        $tahunini = date("Y"); // # Tahun Sekarang #

        $nik = Auth::guard('karyawan')->user()->nik;
        $presensihariini = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $hariini)->first();
        // ## Menampilkan Histori User Selama Sebulan di Dashboard ##
        $historibulanini = DB::table('presensi')->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->orderBy('tgl_presensi')
            ->get();
        // dd($historibulanini);
        return view('dashboard.dashboard', compact('presensihariini', 'historibulanini'));
    }
}
