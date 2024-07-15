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
        <button id="takeabsen" class="btn btn-primary btn-block">
            <ion-icon name="camera-outline"></ion-icon>
            Absen Masuk
        </button>
    </div>
</div>
<div class="row mt-2">
    <div class="col">
        <div id="map"></div>
    </div>
</div>
@endsection

@push('myscript')
<script>
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

    }
</script>
@endpush