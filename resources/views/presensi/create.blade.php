@extends('layouts.presensi')
@section('header')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">E-Presensi</div>
    <div class="right"></div>
</div>
<!-- * App Header -->

<style>
    .webcam-capture,
    .webcam-capture video {
        display: inline-block;
        width: 100% !important;
        margin: auto;
        height: auto !important;
        border-radius: 15px;
    }

    #map {
        height: 200px;
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<!-- Make sure you put this AFTER Leaflet's CSS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@endsection

@section('content')
<div class="row" style="margin-top: 70px">
    <div class="col">
        <!-- ## Menampilkan/tidak kolom radius ## -->
        <!-- <input type="text" id="lokasi"> -->
        <input type="hidden" id="lokasi">

        <div class="webcam-capture"></div>
    </div>
</div>
<div class="row">
    <div class="col">
        @if ($cek > 0)
        <button id="takeabsen" class="btn btn-danger btn-block">
            <ion-icon name="camera-outline"></ion-icon>
            Absen Pulang
        </button>
        @else
        <button id="takeabsen" class="btn btn-primary btn-block">
            <ion-icon name="camera-outline"></ion-icon>
            Absen Masuk
        </button>
        @endif
    </div>
</div>
<div class="row mt-2">
    <div class="col">
        <div id="map"></div>
    </div>
</div>

<audio id="notifikasi_in">
    <source src="{{ asset('assets/sound/notifikasi_in.mp3') }}" type="audio/mpeg">
</audio>
<audio id="notifikasi_out">
    <source src="{{ asset('assets/sound/notifikasi_out.mp3') }}" type="audio/mpeg">
</audio>
@endsection

@push('myscript')

<script>
    // ## Inisialisasi Audio ##
    var notifikasi_in = document.getElementById('notifikasi_in');
    var notifikasi_out = document.getElementById('notifikasi_out');
    // ##

    Webcam.set({
        width: 640,
        height: 480,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    Webcam.attach('.webcam-capture');

    var lokasi = document.getElementById('lokasi');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position) {
        lokasi.value = position.coords.latitude + ',' + position.coords.longitude;

        var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 18);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);

        // ## Menentukan Radius Absensi, Pilih Salah Satu ##
        // # Yang Atas Radius Untuk latihan (menggunakan radius lokasi terkini) #
        //  # Yang Bawah Radius Kantor Alan bakery #
        var circle = L.circle([position.coords.latitude, position.coords.longitude], {
            // var circle = L.circle([0.4490211, 101.455997], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            //  ## Mengatur Jarak Radius (dalam meter) ##
            radius: 15
        }).addTo(map);
    }

    function errorCallback() {
        alert("Maaf, Kami Tidak Dapat Mengakses Lokasi Anda, Silahkan Refresh Laman Absensi");
    }

    //  ## Buat Menyimpan Data, Saat Tombol TakeAbsen di Klik ##
    $("#takeabsen").click(function(e) {
        // alert('test');

        // ## Perintah mengambil gambar ##
        // ## 'uri' berguna untuk mengenkripsi/encode gambar dalam enkripsi base64 ##
        Webcam.snap(function(uri) {
            image = uri;
        });

        // ## Untuk Mendapatkan Lokasinya ##
        var lokasi = $("#lokasi").val();
        // alert(lokasi);

        // ## Proses Simpan Data, Mengguankan AJAX ##
        $.ajax({
            type: 'POST',
            url: '/presensi/store',
            data: {
                _token: "{{ csrf_token() }}",
                image: image,
                lokasi: lokasi
            },
            cache: false,
            success: function(respond) {
                // ## var status, Untuk mensplit data yang di presensicontroller, bagaian:
                // ## echo "success|Terimakasih Telah Melakukan Absen Pulang, Hati-hati di Jalan|out"; ##
                // ## split kalau  di php namanya explode, yaitu memecah data menjadi array 0, 1, 2, dst ##
                var status = respond.split("|");
                if (status[0] == "success") {
                    // alert('success');

                    // ## Insert Audio Ketika Berhasil Absensi ##
                    if (status[2] == "in") {
                        notifikasi_in.play();
                    } else {
                        notifikasi_out.play();
                    }
                    // ## SWEEET ALERT ##
                    Swal.fire({
                        title: 'Berhasil!',
                        text: status[1], // ## Pesannya mengambil array ke 1 dari success|Terimakasih Telah Melakukan Absen Pulang/masuk (yang ada di presensicontroller) ##
                        icon: 'success'
                    });
                    setTimeout("location.href='/dashboard'", 4000);

                } else {
                    // alert('error');

                    // ## SWEEET ALERT ##
                    Swal.fire({
                        title: 'Error!',
                        text: 'Absensi Gagal Dilaksanakan, Silahkan Hubungi Tim IT',
                        icon: 'error'
                    });
                }
            }
        });
    });
</script>
@endpush