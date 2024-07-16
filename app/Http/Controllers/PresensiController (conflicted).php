<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function create()
    {
        return view('presensi.create');
    }

    public function store(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        $lokasi = $request->lokasi;
        $image_base64 = $request->image;

        // ## Tempat Menyimpan gambar ##
        $folderPath = "public/uploads/absensi/";

        // ## Format Gambar Yang Akan Disimpan ##
        $formatName = $nik . "-" . $tgl_presensi;

        // ## Karena Tadi Image nya di Encode Menggunakan Base64, Maka Sekarang Akan di Decode File image nya ##
        $image_parts = explode(";base_64", $image_base64);
        $image_base64 = base64_decode($image_parts[1]);

        // ## Konfigurasi Tipe Data ##
        $fileName = $formatName . "png";

        $file = $folderPath . $fileName;
        Storage::put($file, $image_base64);
    }
}
