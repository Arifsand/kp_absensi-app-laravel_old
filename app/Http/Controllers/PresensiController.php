<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class PresensiController extends Controller
{
    public function create()
    {
        // ## Mengecek Apakah Karyawan Sudah Melakukan Absensi Hari Ini Atau Belum ##
        $hariini = date("Y-m-d");
        $nik = Auth::guard('karyawan')->user()->nik;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nik', $nik)->count();
        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        //  ## Menentukan Lokasi Kantor ##
        // # Lokasi Toko Alan Bakery (Untuk Latihan) #
        // $latitudekantor = 0.45041767668282207;
        // $longitudekantor = 101.4560184987767;
        // # Lokasi Kantor Alan Bakery #
        $latitudekantor = 0.4490211;
        $longitudekantor = 101.455997;

        // ## Lokasi user ##
        $lokasi = $request->lokasi;
        // ## Memecah latitude & Longitude dari lokasi ##
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]); // Fungsi Round Untuk Menggenapkan Angka pada Jarak nya ##
        // $radius = $jarak["meters"];
        // dd($radius);

        // ## Mengecek Data Absensi di Database ##
        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->count();

        if ($cek > 0) {  // Lebih Dari nol, berarti User sudah melakukan Absensi ##
            $ket = "out";
        } else {   // Jika nol, berarti User belum melakukan Absensi ##
            $ket = "in";
        }

        $image = $request->image;
        // ## Tempat Menyimpan gambar ##
        $folderPath = "public/uploads/absensi/";

        // ## Format Gambar Yang Akan Disimpan ##
        $formatName = $nik . "_" . $tgl_presensi . "_" . $ket . "_" . date("H-i-s");

        // ## Karena Tadi Image nya di Encode Menggunakan Base64, Maka Sekarang Akan di Decode File image nya ##
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);

        // ## Konfigurasi Tipe Data ##
        $fileName = $formatName . ".png";

        // ## Simpan File Gambar ##
        $file = $folderPath . $fileName;
        $data = [
            'nik' => $nik,
            'tgl_presensi' => $tgl_presensi,
            'jam_in' => $jam,
            'foto_in' => $fileName,
            'lokasi_in' => $lokasi
        ];

        // ## Mengecek Jika Karyawan Sudah Melaksanakan Absensi Dihari Itu, Maka Perintahnya Bukan Simpan Lagi, tapi Update ##

        // ## IF Else untuk mengecek lokasi user berada didalam/diluar radius ##
        if ($radius > 10) {  // # jarak dalam meter #
            echo "error|Maaf Anda Berada Diluar Radius, Jarak Anda " . $radius . " meter Dari Kantor|radius";
        } else {
            // ## IF Else untuk mengecek apakah user akan melakukan absensi masuk / absensi pulang ##
            if ($cek > 0) {
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out' => $fileName,
                    'lokasi_out' => $lokasi
                ];
                $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_pulang);
                if ($update) {
                    // echo 0;
                    echo "success|Terimakasih Telah Melakukan Absen Pulang, Hati-hati di Jalan|out";
                    Storage::put($file, $image_base64);
                } else {
                    // echo 1;
                    echo "error|Absensi Gagal Dilaksanakan, Silahkan Hubungi Tim IT|out";
                }

                // ## Jika Karyawan Belum Melakukan Absensi Dihari Itu, Maka Perintahnya Simpan ##
            } else {
                $data = [
                    'nik' => $nik,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'foto_in' => $fileName,
                    'lokasi_in' => $lokasi
                ];

                // ## Untuk Memastikan Data Tersimpan Dengan Benar ##
                $simpan = DB::table('presensi')->insert($data);
                if ($simpan) {
                    // echo 0;
                    echo "success|Terimakasih Telah Melakukan Absen Masuk, Selamat Bekerja|in";
                    Storage::put($file, $image_base64);
                } else {
                    // echo 1;
                    echo "error|Absensi Gagal Dilaksanakan, Silahkan Hubungi Tim IT|in";
                }
            }
        }
    }

    //Menghitung Jarak (Untuk Radius Kantor)
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
}
