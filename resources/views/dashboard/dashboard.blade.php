@extends('layouts.presensi')
@section('content')
<div class="section" id="user-section">
    <div id="user-detail">
        <div class="avatar">
            <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded">
        </div>
        <div id="user-info">
            <h2 id="user-name">Muhamad Arif Sandaya</h2>
            <span id="user-role">Head of IT</span>
        </div>
    </div>
</div>

<div class="section" id="menu-section">
    <div class="card">
        <div class="card-body text-center">
            <div class="list-menu">
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="" class="green" style="font-size: 40px;">
                            <ion-icon name="person-sharp"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        <span class="text-center">Profil</span>
                    </div>
                </div>
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="" class="danger" style="font-size: 40px;">
                            <ion-icon name="calendar-number"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        <span class="text-center">Cuti</span>
                    </div>
                </div>
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="" class="warning" style="font-size: 40px;">
                            <ion-icon name="document-text"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        <span class="text-center">Histori</span>
                    </div>
                </div>
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="" class="orange" style="font-size: 40px;">
                            <ion-icon name="location"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        Lokasi
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="section mt-2" id="presence-section">
    <div class="todaypresence">
        <div class="row">
            <div class="col-6">
                <div class="card gradasigreen">
                    <div class="card-body">
                        <div class="presencecontent">
                            <div class="iconpresence">
                                <!-- ## Menampilkan Foto Absensi Masuk Di Dashboard, Jika User Sudah Melalukan Absensi Masuk Hari Ini ## -->
                                <!-- # '!=' artinya tidak sama dengan # -->
                                <!-- # $presensihariini diambil dari DashboardController # -->
                                <!-- # foto_in adalah kolom/field didalam DB # -->
                                @if ($presensihariini != null)
                                @php
                                $path = Storage::url('/uploads/absensi/'.$presensihariini->foto_in);
                                @endphp
                                <img src="{{ url($path) }}" alt="" class="imaged w64">
                                <!-- Jika Tidak Ada Foto Absensi Masuknya, Maka Munculkan ikon Camera -->
                                @else
                                <ion-icon name="camera"></ion-icon>
                                @endif
                            </div>
                            <div class="presencedetail">
                                <h4 class="presencetitle">Masuk</h4>
                                <!-- ## Jika Hari Ini User sudah Melakukan Absensi Masuk (not null/tidak sama dengan null), Maka Tampilkan jam absensi masuknya/jam_in, Jika Belum Absensi Masuk (null) Maka Tampilkan 'Belum Absen' ##  -->
                                <!-- # $presensihariini diambil dari DashboardController # -->
                                <span>{{ $presensihariini != null ? $presensihariini->jam_in : 'Belum Absen' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card gradasired">
                    <div class="card-body">
                        <div class="presencecontent">
                            <div class="iconpresence">
                                <!-- ## Menampilkan Foto Absensi Pulang Di Dashboard, Jika User Sudah Melalukan Absensi Hari Ini & Absensi Pulangnya Tidak Kosong  ## -->
                                <!-- # '!=' artinya tidak sama dengan # -->
                                <!-- # $presensihariini diambil dari DashboardController # -->
                                <!-- # foto_in adalah kolom/field didalam DB # -->
                                @if ($presensihariini != null && $presensihariini->jam_out != null)
                                @php
                                $path = Storage::url('/uploads/absensi/'.$presensihariini->foto_out);
                                @endphp
                                <img src="{{ url($path) }}" alt="" class="imaged w64">
                                <!-- Jika Tidak Ada Foto Absensi Pulangnya, Maka Munculkan ikon Camera -->
                                @else
                                <ion-icon name="camera"></ion-icon>
                                @endif
                            </div>
                            <div class="presencedetail">
                                <h4 class="presencetitle">Pulang</h4>
                                <!-- ## Jika Hari Ini User sudah Melakukan Absensi (not null), dan Jika Hari ini User sudah Melakukan Absensi Pulang (not null), Maka Tampilkan jam absensi pulangnya/jam_out, Jika Belum Absensi Pulang (null) Maka Tampilkan 'Belum Absen' ##  -->
                                <!-- # $presensihariini diambil dari DashboardController # -->
                                <span>{{ $presensihariini != null && $presensihariini->jam_out != null ? $presensihariini->jam_out : 'Belum Absen' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="presencetab mt-2">
        <div class="tab-pane fade show active" id="pilled" role="tabpanel">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                        Bulan Ini
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#profile" role="tab">
                        Leaderboard
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content mt-2" style="margin-bottom:100px;">
            <div class="tab-pane fade show active" id="home" role="tabpanel">
                <ul class="listview image-listview">
                    <!-- ## Start - Menampilkan Histori Abseensi User Selama Sebulan ## -->
                    @foreach ($historibulanini as $d)
                    @php
                    $path = Storage::url('/uploads/absensi/'.$d->foto_in);
                    @endphp
                    <li>
                        <div class="item">
                            <div class="icon-box bg-primary">
                                <img src="{{ url($path) }}" alt="" class="imaged w64">
                                <!-- <ion-icon name="finger-print-outline"></ion-icon> -->
                            </div>
                            <div class="in">
                                <div>{{ date("d-m-Y", strtotime($d->tgl_presensi)) }}</div>
                                <span class="badge badge-success">{{ $d->jam_in }}</span>
                                <!-- ## Jika Hari Ini User Sudah Melalukan Absensi, dan Jika Hari Ini User Sudah Melalukan Absen Pulang, maka Tampilkan Jam Pulang, jika tidak Tampilkan 'Belum Absen'  -->
                                <span class="badge badge-danger">{{ $presensihariini != null && $d->jam_out != null ? $d->jam_out : 'Belum Absen' }}</span>
                            </div>
                        </div>
                    </li>
                    @endforeach
                    <!-- ## End - Menampilkan Histori Abseensi User Selama Sebulan ## -->
                </ul>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <ul class="listview image-listview">
                    <li>
                        <div class="item">
                            <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                            <div class="in">
                                <div>Edward Lindgren</div>
                                <span class="text-muted">Designer</span>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item">
                            <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                            <div class="in">
                                <div>Emelda Scandroot</div>
                                <span class="badge badge-primary">3</span>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item">
                            <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                            <div class="in">
                                <div>Henry Bove</div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item">
                            <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                            <div class="in">
                                <div>Henry Bove</div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item">
                            <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                            <div class="in">
                                <div>Henry Bove</div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection