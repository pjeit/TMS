
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('karyawan.index')}}">Karyawan</a></li>
    <li class="breadcrumb-item">Edit</li>
@endsection

@section('content')
<br>
<style>
   
</style>

<div class="container">
        @if ($errors->any())
    {{-- <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div> --}}
        @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endforeach
    @endif
    <div class="container mb-3">
        <div id="stepper-example" class="bs-stepper stepper-horizontal">
            <div class="bs-stepper-header">
                <div class="step1" data-target="#test-l-1">
                    <button type="button" class="btn btn-secondary radiusSendiri" ><b>Data Pribadi</b></button>
                </div>
                <div class="line"></div>
                <div class="step2" data-target="#test-l-2">
                    <a href="#">
                     <button type="button" class="btn btn-outline-secondary radiusSendiri"><b>Alamat & Kontak</b></button>
                    </a>
                </div>
                <div class="line"></div>
                <div class="step3" data-target="#test-l-3">
                     <button type="button" class="btn btn-outline-secondary radiusSendiri"><b>Kontak Darurat</b></button>
                </div>
                <div class="line"></div>
                
                <div class="step4" data-target="#test-l-3">
                     <button type="button" class="btn btn-outline-secondary radiusSendiri"><b>Status Karyawan</b></button>
                </div>
              
            </div>
        </div>
    </div>
    {{-- <div class="container mb-3">
        <div id="stepper-example" class="bs-stepper">
            <div class="bs-stepper-header row">
                <div class="col-md step1" data-target="#test-l-1">
                    <button type="button" class="btn btn-secondary">Data Pribadi</button>
                </div>
                    <div class="line"  style="opacity: 0%"></div>
                <div class="col-md step2" data-target="#test-l-2">
                        <button type="button" class="btn btn-outline-secondary">Alamat & Kontak</button>
                </div>
                    <div class="line" style="opacity: 0%"></div>
                <div class="col-md step" data-target="#test-l-3">
                    <button type="button" class="btn btn-outline-secondary">Bergabung</button>
                </div>
            </div>
        </div>
    </div> --}}
    
    <div class="row">
        <div class="col-12">
            <form action="{{ route('karyawan.update',[$karyawan->id]) }}" method="POST" enctype="multipart/form-data" id="formDataKaryawan">
                @csrf
                 @method('PUT')

                  {{-- ============Data pribadi============ --}}
                <div class="card radiusSendiri" id="satu">
                    <div class="card-header">
                        <h5 class="card-title">Data Pribadi</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group text-center">
                            <img src="{{ $karyawan->foto ? asset($karyawan->foto) : asset('img/photo.png') }}" class='img-fluid' style='width:225px;height:225px' id='preview_img'> 
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="foto" name='foto' accept="image/png, image/jpeg">
                                <label class="custom-file-label" for="foto" style="text-align: left">Pilih Foto</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class='row'>
                                <div class='col-lg-3 col-md-12'>
                                    <label for="nik">NPK <span style="opacity: 40%">(Nomor Pokok Karyawan)</span></label>
                                    <input type="text" class="form-control" id="nik" name="nik" placeholder="Otomatis" readonly value="{{old('nik',$karyawan->nik)}}">    
                                </div>
                                <div class='col-lg-9 col-md-12'>
                                    <label for="nik">Nama Panggilan<span style='color:red'>*</span></label>
                                    <input  ="text" name="nama_panggilan" class="form-control" id="panggilan" placeholder="" value="{{old('nama_panggilan',$karyawan->nama_panggilan)}}">    
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama Lengkap<span style='color:red'>*</span></label>
                            <input  ="text" name="nama_lengkap" class="form-control" id="nama" placeholder="Nama sesuai KTP" value="{{old('nama_lengkap',$karyawan->nama_lengkap)}}"> 
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-12">
                                <div class="form-group">
                                    <label for="jumlah_anak">Jumlah Anak</label>
                                    <input type="number" class="form-control " name="jumlah_anak" id="jumlah_anak" value="{{old('jumlah_anak',$karyawan->jumlah_anak)}}" min="0" max="3">
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-12">
                                  <div class="form-group">
                                    <label for="tipe">Status kawin</label>
                                    <br>
                                    <div class="icheck-primary d-inline">
                                        <input id="belumNikah" type="radio" name="status_menikah" value="0" {{'0' == old('status_menikah',$karyawan->status_menikah)? 'checked' :'' }}>
                                        <label class="form-check-label" for="belumNikah">Belum Menikah</label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input id="sudahNikah" type="radio" name="status_menikah" value="1" {{'1'== old('status_menikah',$karyawan->status_menikah)? 'checked' :'' }}>
                                        <label class="form-check-label" for="sudahNikah">Sudah Menikah</label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input id="cerai" type="radio" name="status_menikah" value="2" {{'2'== old('status_menikah',$karyawan->status_menikah)? 'checked' :'' }}>
                                        <label class="form-check-label" for="cerai">Cerai</label><br>
                                    </div>
                                 </div>
                            </div>
                            <div class="col-lg-4 col-md-12">
                                <div class="form-group">
                                    <label for="tipe">Jenis Kelamin</label>
                                    <br>
                                    <div class="icheck-primary d-inline">
                                        <input id="laki" type="radio" name="jenis_kelamin" value="L" {{'L' == old('jenis_kelamin',$karyawan->jenis_kelamin)? 'checked' :'' }} checked>
                                        <label class="form-check-label" for="laki">Laki-laki</label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input id="perempuan" type="radio" name="jenis_kelamin" value="P" {{'P'== old('jenis_kelamin',$karyawan->jenis_kelamin)? 'checked' :'' }}>
                                        <label class="form-check-label" for="perempuan">Perempuan</label><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>PTKP</label>
                            <select class="form-control selectpicker" name="ptkp" id="ptkp" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih ptkp">
                                <option value="">--Pilih PTKP--</option>
                                 @foreach($dataPtkp as $data)
                                    <option value="{{$data->id}}"{{$karyawan->ptkp_id == $data->id? 'selected' :'' }}>{{$data->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-12">
                                <div class="form-group">
                                        <label for="tempat_lahir">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" class="form-control" id="tempat_lahir" placeholder="" value="{{old('tempat_lahir',$karyawan->tempat_lahir)}}">
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-12">
                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" name="tanggal_lahir" autocomplete="off" class="date form-control" id="tanggal_lahir" placeholder="dd-M-yyyy" value="{{old('tanggal_lahir',\Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d-M-Y')) }}">     
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                      
                      
                        <div class="form-group">
                            <label>Agama</label>
                            <select class="form-control selectpicker" name="agama" id="agama" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Agama">
                               @foreach($dataAgama as $data)
                                    <option value="{{$data->id}}"{{$karyawan->agama == $data->id? 'selected' :'' }}>{{$data->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" id="nextDariPribadi" class="btn btn-success float-right radiusSendiri"><strong>Next</strong></button>
                        <a href="{{ route('karyawan.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>

                    </div>
                </div>
                {{-- ============End Data pribadi============ --}}

                {{-- ============Alamat & Kontak============ --}}
                
                <div class="card radiusSendiri" id="dua">
                    <div class="card-header">
                        <h5 class="card-title">Alamat & Kontak</h5>
                    </div>  
                    <div class="card-body">
                         <div class="row">
                            <div class="col-lg col-md-12">
                                 <div class="form-group">
                                    <label for="alamat">Alamat Tinggal Sekarang</label>
                                    <input type="text" name="alamat_sekarang" class="form-control" id="alamat" placeholder="" value="{{old('alamat_sekarang',$karyawan->alamat_domisili)}}">
                                </div>
                            </div>

                            <div class="col-lg col-md-12">
                                <div class="form-group">
                                    <label for="kota">Kota Tinggal Sekarang</label>
                                    <input type="text" name="kota_sekarang" class="form-control" id="kota" placeholder="" value="{{old('kota_sekarang',$karyawan->kota_domisili)}}">
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-lg col-md-12">
                                <div class="form-group">
                                    <label for="alamat">Alamat Sesuai KTP</label>
                                    <input type="text" name="alamat_ktp" class="form-control" id="alamat" placeholder="" value="{{old('alamat_ktp',$karyawan->alamat_ktp)}}">
                                </div>
                            </div>

                            <div class="col-lg col-md-12">
                                <div class="form-group">
                                    <label for="kota">Kota Sesuai KTP</label>
                                    <input type="text" name="kota_ktp" class="form-control" id="kota" placeholder="" value="{{old('kota_ktp',$karyawan->kota_ktp)}}">
                                </div>
                            </div>
                        </div>
                       
                     
                     
                        <div class="row">
                            <div class="col-lg col-md-12">
                                 <div class="form-group">
                                    <label for="telp">Telp 1<span style='color:red'>*</span></label>
                                    <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+62</span>
                                    </div>
                                    <input type="text" class="form-control numaja" id="telp1" name="telp1"  placeholder="" value="{{old('telp1',$karyawan->telp1)}}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg col-md-12">
                                <div class="form-group">
                                    <label for="telp">Telp 2</label>
                                    <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+62</span>
                                    </div>
                                    <input type="text" class="form-control numaja" id="telp2" name="telp2"  placeholder="" value="{{old('telp2',$karyawan->telp2)}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label for="alamat">Email</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" class="form-control" id="email" name="email" placeholder="" value="{{old('email',$karyawan->email)}}">
                            </div>
                        </div>
                         <div class="form-group">
                            <label for="no_rekening">No. Rekening</label>
                            <input type="text" name="no_rekening" class="form-control numaja" id="no_rekening" placeholder="" value="{{old('norek',$karyawan->norek)}}">
                        </div>
                        <div class="form-group">
                            <label for="atas_nama">Atas Nama</label>
                            <input type="text" name="atas_nama" class="form-control" id="atas_nama" placeholder="" value="{{old('rek_nama',$karyawan->rek_nama)}}">
                        </div>
                        <div class="row">
                            <div class="col-lg col-md-12">
                                <div class="form-group">
                                    <label for="nama_bank">Nama Bank</label>
                                    <input type="text" name="nama_bank" class="form-control" id="nama_bank" placeholder="" value="{{old('bank',$karyawan->bank)}}">
                                </div>
                            </div>
                            <div class="col-lg col-md-12">
                                <div class="form-group">
                                    <label for="cabang">Cabang</label>
                                    <input type="text" name="cabang_bank" class="form-control" id="cabang" placeholder="" value="{{old('cabang_bank',$karyawan->cabang_bank)}}">
                                </div>
                            </div>
                        </div>
                        <button type="button" id="BackDariAlamat" class="btn btn-outline-success float-left radiusSendiri"><strong>Back</strong></button>
                        <button type="button" id="nextDariAlamat" class="btn btn-success float-right radiusSendiri"><strong>Next</strong></button>

                    </div>
                           
                </div>
             
                {{-- ============End Alamat & Kontak============ --}}

                {{-- ============Kontak Darurat============ --}}
                <div class="card radiusSendiri" id="tiga">
                    <div class="card-header">
                        <h5 class="card-title">Kontak Darurat</h5>
                    </div>  
                    <div class="card-body">
                        <div id="parentDarurat">
                                <div class="form-group">
                                    <label for="nama_kontak_darurat">Nama</label>
                                    <input type="text" name="nama_kontak_darurat" class="form-control" placeholder="" value="{{old('nama_kontak_darurat',$karyawan->nama_kontak_darurat)}}">
                                </div>
                                <div class="form-group">
                                    <label for="hubungan_kontak_darurat">Hubungan</label>
                                    <input type="text" name="hubungan_kontak_darurat" class="form-control" placeholder="" value="{{old('hubungan_kontak_darurat',$karyawan->hubungan_kontak_darurat)}}">
                                </div>
                                <div class="form-group">
                                    <label for="nomor_kontak_darurat">Nomor Telepon<span style='color:red'>*</span></label>
                                    <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+62</span>
                                    </div>
                                    {{-- numaja itu js buat nganu number doang --}}
                                    <input type="text" class="form-control numaja" id="nomor_kontak_darurat" name="nomor_kontak_darurat"  placeholder="" value="{{old('nomor_kontak_darurat',$karyawan->nomor_kontak_darurat)}}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="alamat_kontak_darurat">Alamat</label>
                                    <input type="text" name="alamat_kontak_darurat" class="form-control" placeholder="" value="{{old('alamat_kontak_darurat',$karyawan->alamat_kontak_darurat)}}">
                                </div>

                                <button type="button" id="BackDariDarurat" class="btn btn-outline-success float-left radiusSendiri"><strong>Back</strong></button>
                                <button type="button" id="nextDariDarurat" class="btn btn-success float-right radiusSendiri"><strong>Next</strong></button>
                        </div>     
                    </div>           
                </div>
                {{-- ============End Kontak Darurat============ --}}

                {{-- ============Status Karyawan============ --}}
                <div class="card radiusSendiri" id="empat">
                    <div class="card-header">
                        <h5 class="card-title">Status Karyawan</h5>
                    </div>  
                    <div class="card-body">
                         <div class="form-group">
                            <label for="tipe">Tipe Karyawan</label>
                            <br>
                            <div class="icheck-primary d-inline">
                                <input id="Kontrak" type="radio" name="status_pegawai" value="Kontrak" {{'Kontrak' == old('status_pegawai',$karyawan->status_pegawai)? 'checked' :'' }}>
                                <label class="form-check-label" for="Kontrak">Kontrak</label>
                            </div>
                            <div class="icheck-primary d-inline ml-3">
                                <input id="Tetap" type="radio" name="status_pegawai" value="Tetap" {{'Tetap'== old('status_pegawai',$karyawan->status_pegawai)? 'checked' :'' }}>
                                <label class="form-check-label" for="Tetap">Tetap</label>
                            </div>
                            <div class="icheck-primary d-inline ml-3">
                                <input id="Magang" type="radio" name="status_pegawai" value="Magang" {{'Magang' == old('status_pegawai',$karyawan->status_pegawai)? 'checked' :'' }}>
                                <label class="form-check-label" for="Magang">Magang</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_gabung">Tanggal Bergabung</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" name="tanggal_gabung" autocomplete="off" class="date form-control" id="tanggal_gabung" placeholder="dd-M-yyyy" value="{{old('tgl_gabung',\Carbon\Carbon::parse($karyawan->tgl_gabung)->format('d-M-Y')) }}">     
                            </div>
                        </div>
                     
                        <div class="form-group" id="tglKontrakMulai">
                            <label for="tanggal_kontrak">Tanggal Mulai</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                                                                                                                                                    
                                <input type="text" name="tanggal_kontrak" autocomplete="off" class="date form-control" id="tanggal_kontrak" placeholder="dd-M-yyyy" value="{{old('tgl_mulai_kontrak',\Carbon\Carbon::parse($karyawan->tgl_mulai_kontrak)->format('d-M-Y'))}}">     
                            </div>
                        </div>
                        <div class="form-group" id="tglKontrakSelesai">
                            <label for="tanggal_selesai_kontrak">Tanggal Selesai</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" name="tanggal_selesai_kontrak" autocomplete="off" class="date form-control" id="tanggal_selesai_kontrak" placeholder="dd-M-yyyy" value="{{old('tgl_selesai_kontrak',\Carbon\Carbon::parse($karyawan->tgl_selesai_kontrak)->format('d-M-Y')) }}">     
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <label>Posisi<span style='color:red'>*</span></label>
                            <select class="form-control selectpicker" name="posisi" id="posisi" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih ptkp">
                                <option value="">--Pilih Posisi--</option>
                                @foreach($dataRole as $data)
                                    <option value="{{$data->id}}"{{$karyawan->posisi_id == $data->id? 'selected' :'' }}>{{$data->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <div class="form-group">
                            <label>Cabang Kantor<span style='color:red'>*</span></label>
                            <select class="form-control selectpicker" name="cabang_kantor" id="cabang_kantor" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih ptkp">
                                <option value="">--Pilih Cabang Kantor--</option>
                                @foreach($dataKota as $data)
                                    <option value="{{$data->id}}"{{$karyawan->m_kota_id == $data->id? 'selected' :'' }}>{{$data->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sisa_cuti">Sisa Cuti</label>
                            <input type="number" name="sisa_cuti" class="form-control" placeholder="" value="{{old('saldo_cuti',$karyawan->saldo_cuti)}}" >
                        </div>
                        <div class="form-group">
                            <label for="gaji">Gaji</label>
                            <div class="input-group mb-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" name="gaji" class="form-control numaja uang" id="gaji" placeholder="" value="{{old('gaji',number_format($karyawan->gaji))}}" readonly>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            <label for="tanggalLahir">Tanggal Keluar</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><input type="checkbox" id="check_is_keluar" ></span>
                                </div>
                                <input type="hidden" id="is_keluar" name='is_keluar' value="">
                                <input type="text" autocomplete="off" class="form-control" id="tanggalDibuatDisplay" placeholder="DD-MM-YYYY" value="{{old('tanggal_lahir','')}}" readonly>
                                <input type="hidden" id="tanggalDibuat" name="tanggal_keluar">
                            </div>
                        </div> --}}
                        <div class="form-group">
                            <label for="tanggal_keluar">Tanggal Keluar</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><input type="checkbox" id="check_is_keluar" name="is_keluar" {{$karyawan->is_keluar == "Y"? 'selected' :'' }}></span>
                                </div>
                                <input type="hidden" id="is_keluar" name='is_keluar' value="{{old('is_keluar',$karyawan->is_keluar)}}">
                                <input type="text" autocomplete="off" name="tanggal_keluar" class="form-control" id="tanggal_keluar" placeholder="dd-M-yyyy" {{$karyawan->is_keluar == "N"? 'readonly' :'' }} >
                            </div>
                        </div>
                        

                        <button type="button" id="BackDariStatus" class="btn btn-outline-success float-left radiusSendiri"><strong>Back</strong></button>
                        <button type="submit" class="btn btn-success float-right radiusSendiri" id="btnSimpan"><strong>Simpan</strong></button>
                        
                        {{-- <button type="button" id="btnCobaBuatData" class="btn btn-outline-success float-right"><strong>coba</strong></button> --}}


                    </div>           
                </div>
                {{-- ============End Status Karyawan============ --}}

                {{-- ============komponen============ --}}

                <div class='row' id="showKomponen">
                            {{-- ============komponen identitas============ --}}
                            <div class="col-lg-6 col-md-6 col-12">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="open_detail('')"><i class='fas fa-plus-circle'></i><b style="font-size:16px">&nbsp; IDENTITAS</b></button>
                                <div class="row" style='margin-top:5px;'>
                                    <div class='col-12 table-responsive'>
                                        <table class="table table-hover table-bordered table-striped text-nowrap" id='table_identitas'>
                                            <thead>
                                                <tr>
                                                <th style="width:30px"></th>
                                                <th>Jenis</th>
                                                <th>Nomor</th>
                                                <th>Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!empty($dataKaryawanIdentitas))
                                                    @foreach($dataKaryawanIdentitas as $key => $value)
                                                     <tr id='{{$key}}'>
                                                        <td>
                                                            <div class="btn-group">
                                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                            </button>
                                                            <ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail({{$key}})"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail({{$key}})"><span class="fas fa-eraser" style="width:24px"></span>Hapus</a></li>
                                                            </ul>
                                                            </div>
                                                        </td>
                                                        <td id='identitas_id_{{$key}}' hidden>{{$value->id}}</td>
                                                        <td id='jenis_id_{{$key}}' hidden>{{$value->m_jenis_identitas_id}}</td>
                                                        @foreach($dataJenis as $d)
                                                            @if($d->id==$value->m_jenis_identitas_id)
                                                            <td id='jenis_{{$key}}'>{{$d->nama}}</td>
                                                            @endif
                                                        @endforeach                                                       
                                                        <td id='nomor_{{$key}}'>{{$value->nomor}}</td>
                                                        <td id='catatan_{{$key}}'>{{$value->catatan}}</td>
                                                    </tr>

                                                    @endforeach
                                                @endif
                                                
                                            
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            {{-- ============end komponen identitas============ --}}

                            {{-- ============komponen gaji============ --}}
                            <div class="col-lg-6 col-md-6 col-12" style="padding-left:6px;">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="open_komponen('')"><i class='fas fa-plus-circle'></i><b style="font-size:16px">&nbsp; KOMPONEN GAJI</b></button>
                                <div class="row" style='margin-top:5px;'>
                                    <div class='col-12 table-responsive'>
                                        <table class="table table-hover table-bordered table-striped text-nowrap" id='table_komponen'>
                                            <thead>
                                                <tr>
                                                <th style="width:30px"></th>
                                                <th>Komponen</th>
                                                <th style="text-align:right;">Nominal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 @if(!empty($dataKaryawanKomponen))
                                                    @foreach($dataKaryawanKomponen as $key => $value)
                                                     <tr id='komponen_{{$key}}'>
                                                        <td>
                                                            <div class="btn-group">
                                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                            </button>
                                                            <ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="open_komponen({{$key}})"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li>
                                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_komponen({{$key}})"><span class="fas fa-eraser" style="width:24px"></span>Hapus</a></li>
                                                            </ul>
                                                            </div>
                                                        </td>
                                                        <td id='komponen_id_{{$key}}' hidden>{{$value->id}}</td>
                                                        <td id='is_aktif_{{$key}}' hidden>{{$value->is_aktif}}</td>
                                                        <td id='nama_{{$key}}'>{{$value->nama}}</td>
                                                        <td style="text-align:right;" id='nominal_{{$key}}'>{{number_format($value->nominal)}}</td>
                                                    </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{-- ============end komponen gaji============ --}}

                {{-- ============end komponen============ --}}

            </form>

        </div>
    </div>

{{-- modal --}}



<!-- Modal -->
<div class="modal fade" id="confirm_dialog_detail">
    <div class="modal-dialog modal-sm">
      <div class="modal-content ">
        <div class="modal-header">
          <h5 class="modal-title">Apakah anda yakin akan menghapus data ini?</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-footer">
            <form id='form_delete_detail'>
                <input type='hidden' id='identitas_id' name='identitas_id'>
                <input type='hidden' id='id_tombol'>
            </form>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
          <button type="button" class="btn btn-danger" onclick='delete_datadetail()'>Yes</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="identitas_dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Identitas</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <form id='form_add_detail'>
                <input type="hidden" name="key" id="key">
                {{-- <input type="hidden" name="identitas_id" id="identitas_id">     --}}
                 <div class="form-group">
                    <label>Jenis Dokumen<span style='color:red'>*</span></label>
                    <select class="form-control selectpicker" name="jenis" id="jenis" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih ptkp">
                        <option value="">--Pilih Jenis Dokumen--</option>
                        @foreach($dataJenis as $data)
                            <option value="{{$data->id}}-{{$data->nama}}"{{$data->nama == $data->id? 'selected' :'' }}>{{$data->nama}}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="form-group">
                    <label for="jenis">Jenis<span style='color:red'>*</span></label>
                    <input type="text" name="jenis" class="form-control" id="jenis" placeholder=""> 
                </div> --}}
                <div class="form-group">
                    <label for="nomor">Nomor<span style='color:red'>*</span></label>
                    <input type="text" name="nomor" class="form-control" id="nomor" placeholder=""> 
                </div>
                <div class="form-group">
                    <label for="catatan">Catatan</label>
                    <input type="text" name="catatan" class="form-control" id="catatan" placeholder=""> 
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger radiusSendiri" style='width:85px' data-dismiss="modal"><strong>Batal</strong> </button>
          <button type="button" class="btn btn-sm btn-success radiusSendiri" style='width:85px' onclick='save_detail()'><strong>Simpan</strong> </button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="komponen_dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Komponen Gaji</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <form id='form_add_komponen'>
                <input type="hidden" name="key" id="keyK">
                <input type="hidden" name="komponen_id" id="komponen_id">  
                <div class="form-group">
                    <label for="nama_komponen">Komponen<span style='color:red'>*</span></label>
                    <input type="text" name="nama_komponen" class="form-control" id="nama_komponen" placeholder=""> 
                </div>
                <div class="form-group">
                    <label for="gaji">Nominal</label>
                    <div class="input-group mb-0">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                      </div>
                      <input type="text" name="nominal" class="form-control numaja uang" id="nominal" placeholder=""> 
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger radiusSendiri" style='width:85px' data-dismiss="modal"><strong>Batal</strong> </button>
          <button type="button" class="btn btn-sm btn-success radiusSendiri" style='width:85px' onclick='save_komponen()'><strong>Simpan</strong> </button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

{{-- end modal --}}
<script type="text/javascript">
    $("#foto").change(function() {
        readURL(this);
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#preview_img').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }
       function open_detail(key){
        // var pisahIDJenisSamaNama = $('#jenis').val();
        // var tampungahJenis = pisahIDJenisSamaNama.split("-");
        if(key===''){
            var last_id=($('#table_identitas tr:last').attr('id'));
            if(typeof last_id === 'undefined') {
                var last_id=0;
            }else{
                var last_id=parseInt(last_id)+1
            }
            var idx=last_id;
            $('#jenis').val('');
            $('#nomor').val('');
            $('#catatan').val('');
            $('#identitas_id').val('');
        }else{
            var idx=key;
            $('#jenis').val($('#jenis_id_'+idx).text()+"-"+$('#jenis_'+idx).text());
            $('#nomor').val($('#nomor_'+idx).text());
            $('#catatan').val($('#catatan_'+idx).text());
            $('#identitas_id').val($('#identitas_id_'+idx).text());
        }
        
        $('#key').val(idx);
        $('#identitas_dialog').modal('show');
    }
    
    function save_detail(){
        var key=$('#key').val();
        var pisahIDJenisSamaNama = $('#jenis').val();
        var tampungahJenis = pisahIDJenisSamaNama.split("-");
        if($('#jenis').val()==''){toastr.error('Jenis identitas harus diisi');return;}
        if($('#nomor').val()==''){toastr.error('Nomor identitas harus diisi');return;}
        var exist=$('#table_identitas tbody').find('#'+key).attr('id');
        if(typeof exist === 'undefined') {
          
            var new_row='<tr id="'+key+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail('+key+')"><span class="fas fa-edit"></span> Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail('+key+')"><span class="fas fa-eraser"></span> Hapus</a></li></ul></div></td><td id="identitas_id_'+key+'" hidden>'+$('#identitas_id').val()+'</td><td id="jenis_id_'+key+'" hidden>'+tampungahJenis[0]+'</td><td id="jenis_'+key+'">'+tampungahJenis[1]+'</td><td id="nomor_'+key+'">'+$('#nomor').val()+'</td><td id="catatan_'+key+'">'+$('#catatan').val()+'</td></tr>';
            // pengecekan sebelum ditambah
            var existingRow = $('#table_identitas tbody').find('td[id^="jenis_id_"]').filter(function() {
                return $(this).text().trim() === tampungahJenis[0];
            }).closest('tr');

            // console.log($('#table_identitas tbody').find('td[id^="jenis_id_"]').text());
            
            if (existingRow.length > 0 ) {
                toastr.error('Identitas Sudah Ditambahkan');
                // $("#jenis").val('').selectpicker('refresh');

                return;
            }
            $('#table_identitas > tbody:last-child').append(new_row);
        }else{
            $('#jenis_id_'+key).text(tampungahJenis[0]);
            $('#jenis_'+key).text(tampungahJenis[1]);
            $('#nomor_'+key).text($('#nomor').val());
            $('#catatan_'+key).text($('#catatan').val());
            $('#identitas_id_'+key).text($('#identitas_id').val());
        }
        $('#identitas_dialog').modal('hide');
    }
    
    function delete_detail(id_tombol){
       if($('#identitas_id_'+id_tombol).text()!=''){
            $('#form_delete_detail').find('#identitas_id').val($('#identitas_id_'+id_tombol).text());
            $('#form_delete_detail').find('#id_tombol').val(id_tombol);
            
            $('#confirm_dialog_detail').modal('show');
        }else{
            $('#'+id_tombol).remove();
       }
    }
     function delete_datadetail(){
        var id_delete=$("#form_delete_detail").find('#id_tombol').val();
         $('#confirm_dialog_detail').modal('hide');
        $('#'+id_delete).remove();
       
    }
    

    
    // untuk komponen
    function open_komponen(key){
        if(key===''){
            //ini ambil dari <tr id= yang terakir misal terakir komponen_2 ya id= komponen_2 >
            var last_id=($('#table_komponen tr:last').attr('id'));
            if(typeof last_id === 'undefined') {
                var last_id=0;
            }else{
                var last_id=parseInt(($('#table_komponen tr:last').attr('id').split('_')[1]))+1;
            }
            var idx=last_id;
            $('#nama_komponen').val('');
            $('#nominal').val('');
            $('#komponen_id').val('');
                 console.log('masuk =if');

        }else{
                 console.log('masuk else');

            var idx=key;
            $('#nama_komponen').val($('#nama_'+idx).text());
            $('#nominal').val($('#nominal_'+idx).text());
            $('#komponen_id').val($('#komponen_id_'+idx).text());
        }
        
        $('#keyK').val(idx);
        console.log(key+'tese');
        $('#komponen_dialog').modal('show');
    }
    
    function save_komponen(){
        var key=$('#keyK').val();
        if($('#nama_komponen').val()==''){toastr.error('Komponen harus diisi');return;}
        if($('#nominal').val()==''){toastr.error('Nominal harus diisi');return;}
        var exist=$('#table_komponen tbody').find('#komponen_'+key).attr('id');
      
        if(typeof exist === 'undefined') {
              console.log(key+'sd');
            var new_row='<tr id="komponen_'+key+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_komponen('+key+')"><span class="fas fa-edit"></span> Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_komponen('+key+')"><span class="fas fa-eraser"></span> Hapus</a></li></ul></div></td><td id="komponen_id_'+key+'" hidden>'+$('#komponen_id').val()+'</td><td id="is_aktif_'+key+'" hidden>Y</td><td id="nama_'+key+'">'+$('#nama_komponen').val()+'</td><td id="nominal_'+key+'">'+$('#nominal').val()+'</td></tr>';
             var komponenName = $('#nama_komponen').val();
            // pengecekan data udah ada atau belom
            var dataKomponen = $('#table_komponen tbody').find('td[id^="nama_"]').filter(function() {
                return $(this).text().trim() === komponenName;
            }).closest('tr');

            // console.log($('#table_komponen tbody').find('td[id^="nama_"]').text());
            
            if (dataKomponen.length > 0 ) {
                toastr.error('Komponen Sudah Ditambahkan');
                $('#nama_komponen').val('');
                return;
            }
            $('#table_komponen > tbody:last-child').append(new_row);
        }else{
                          console.log("kkkk");

            $('#nama_'+key).text($('#nama_komponen').val());
            $('#nominal_'+key).text($('#nominal').val());
            $('#komponen_id_'+key).text($('#komponen_id').val());
            $('#is_aktif_'+key).text('Y');
        }
        $('#komponen_dialog').modal('hide');
        hitung_gaji();
    }
    
    function delete_komponen(id_tombol){
        if($('#komponen_id_'+id_tombol).text()!=''){
            $('#komponen_'+id_tombol).hide();
            $('#is_aktif_'+id_tombol).text('N');
        }else{
            $('#komponen_'+id_tombol).remove();
        }
        hitung_gaji();
    } 
    function hitung_gaji(){
        var total_gaji=0;
        $('#table_komponen > tbody  > tr').each(function(idx) {
            if($(this).is(":visible")){
                var id=$(this).attr('id').split('_')[1];
                console.log(id);
                if(typeof id !== 'undefined') {
                    total_gaji+=parseFloat(removePeriod($('#nominal_'+id).text(),','));
                }
            }
        });
        $('#gaji').val(addPeriod(total_gaji,','));
    }

  $(document).ready(function() {
//             var url = $("#formDataKaryawan").attr('action');
// alert(url);
         $('#showKomponen').hide();

       function hitungPtkp(){
            var dataPtkp = <?php echo json_encode($dataPtkp); ?>;
            // Ambil value jumlah anak
            var jumlahAnak = parseInt($("#jumlah_anak").val());
            // ambil value radio button
            var statusMenikah = $("input[name='status_menikah']:checked").val();
            $('#ptkp').prop('disabled', true);
            var ptkpValue = "";
            if (jumlahAnak === 0 && statusMenikah === "0" ||jumlahAnak === 0 && statusMenikah === "2") {
                ptkpValue = dataPtkp[0].id;
            } else if (jumlahAnak === 1 && statusMenikah === "0"||jumlahAnak === 1 && statusMenikah === "2") {
                ptkpValue = dataPtkp[1].id;
            }
            else if (jumlahAnak === 2 && statusMenikah === "0"||jumlahAnak === 2 && statusMenikah === "2") {
                ptkpValue = dataPtkp[2].id;
            } 
            else if (jumlahAnak >= 3 && statusMenikah === "0"||jumlahAnak >= 3 && statusMenikah === "2") {
                ptkpValue = dataPtkp[3].id;
            } 
            else if (jumlahAnak === 0 && statusMenikah === "1") {
                ptkpValue = dataPtkp[4].id;
            } 
            else if (jumlahAnak === 1 && statusMenikah === "1") {
                ptkpValue = dataPtkp[5].id;
            } 
            else if (jumlahAnak === 2 && statusMenikah === "1") {
                ptkpValue = dataPtkp[6].id;
            } 
            else if (jumlahAnak >= 3 && statusMenikah === "1") {
                ptkpValue = dataPtkp[7].id;
            } 
            // Set nilai pada select ptkp
            $("#ptkp").val(ptkpValue).selectpicker("refresh");
        }
        hitungPtkp();

        $("#jumlah_anak, input[name='status_menikah']").change(function() {
              hitungPtkp();
        });
        $('#formDataKaryawan').on('submit',function(e){
            // var formData = new FormData(document.getElementById('formDataKaryawan'));
            var formData = new FormData(this);
            var array_identitas=[];
            var myjson_identitas;
            
            $('#table_identitas > tbody  > tr').each(function(idx) {
                var id=$(this).attr('id');
                if(typeof id !== 'undefined') {
                    myjson_identitas='{"identitas_id":'+JSON.stringify($('#identitas_id_'+id).text())+', "m_jenis_identitas_id":'+JSON.stringify($('#jenis_id_'+id).text())+', "nomor":'+JSON.stringify($('#nomor_'+id).text())+', "catatan":'+JSON.stringify($('#catatan_'+id).text())+'}';
                    var obj=JSON.parse(myjson_identitas);
                    array_identitas.push(obj);
                }
            });
            console.log(array_identitas);
            formData.append('identitas', JSON.stringify(array_identitas));
            // for (const entry of formData.entries()) {
            //     console.log(entry[0], entry[1]);
            // }
            
            
            // untuk komponen
            var array_komponen=[];
            var myjson_komponen;
            
            $('#table_komponen > tbody  > tr').each(function(idx) {
                var id=$(this).attr('id').split('_')[1];
                if(typeof id !== 'undefined') {
                    myjson_komponen='{"komponen_id":'+JSON.stringify(parseInt($('#komponen_id_'+id).text()))+', "nama":'+JSON.stringify($('#nama_'+id).text())+', "nominal":'+JSON.stringify($('#nominal_'+id).text())+', "is_aktif":'+JSON.stringify($('#is_aktif_'+id).text())+'}';
                    var obj=JSON.parse(myjson_komponen);
                    array_komponen.push(obj);
                }
            });
            console.log(array_komponen);
            formData.append('komponen', JSON.stringify(array_komponen));

            var url = $(this).attr('action');

            e.preventDefault();

            $.ajax({
                method: 'POST',
                url: url,
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    if (response.hasOwnProperty('id')) {
                        // toastr.success(response.message);
                        // console.log(response);

                           event.preventDefault();
                        Swal.fire({
                            title: 'Apakah Anda yakin data sudah benar?',
                            text: "Periksa kembali data anda",
                            icon: 'warning',
                            showCancelButton: true,
                            cancelButtonColor: '#d33',
                            confirmButtonColor: '#3085d6',
                            cancelButtonText: 'Batal',
                            confirmButtonText: 'Ya',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                  window.location.href = '{{ route("karyawan.index") }}';
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    timer: 2500,
                                    showConfirmButton: false,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                })

                                Toast.fire({
                                    icon: 'success',
                                    title: 'Data Disimpan'
                                })

                                setTimeout(() => {
                                    this.submit();
                                }, 1000); // 2000 milliseconds = 2 seconds
                            }else{
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    timer: 2500,
                                    showConfirmButton: false,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                })

                                Toast.fire({
                                    icon: 'warning',
                                    title: 'Batal Disimpan'
                                })
                                event.preventDefault();
                            }
                        })
                    } else {
                        toastr.error(response.message);
                    }
                },
                 error: function (xhr, status, error) {
                    if (xhr.responseJSON && xhr.responseJSON.errorsCatch) {
                        var pesanError = xhr.responseJSON.errorsCatch;

                        for (var i in pesanError) {
                            toastr.error(pesanError[i]);
                        }

                    } 
                    else if (xhr.responseJSON && xhr.responseJSON.errorServer) {
                        var pesanError = xhr.responseJSON.errorServer;
                        console.table(pesanError);

                    }
                    
                    else {
                        toastr.error("Terjadi kesalahan saat mengirim data. " + error);
                    }

                    console.log("XHR status:", status);
                    console.log("Error:", error);
                    console.log("Response:", xhr.responseJSON);
                }
            });
    
        });
           
         $('#tanggal_lahir').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            endDate: "0d"
        });
        $('#tanggal_gabung').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
        });
        $('#tanggal_kontrak').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
        });
        $('#tanggal_selesai_kontrak').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
        });

        
        $('#check_is_keluar').click(function(){
            if($(this).is(":checked")){
                $('#is_keluar').val('Y');
                $('#tanggal_keluar').attr('readonly',false);
                $('#tanggal_keluar').datepicker({
                    autoclose: true,
                    format: "dd-M-yyyy",
                    todayHighlight: true,
                    language:'en'
                });
                // console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#is_keluar').val('N');
                $('#tanggal_keluar').val('');
                $('#tanggal_keluar').attr('readonly',true);
                $('#tanggal_keluar').datepicker('destroy');
                // console.log("Checkbox is unchecked.");
            }
        });
        $("#satu").show(); 
        $("#dua").hide(); 
        $("#tiga").hide(); 
        $("#empat").hide(); 
     $('.step1').click(function() {
         $(this).find('button').removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('.step2 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step3 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step4 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
            $('#ptkp').prop('disabled', true);

        $("#satu").show(); 
        $("#dua").hide(); 
        $("#tiga").hide(); 
        $("#empat").hide(); 
         $('#showKomponen').hide();

      
     });
     $('.step2').click(function() {
         $(this).find('button').removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('.step1 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step3 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step4 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
            $('#ptkp').prop('disabled', false);

        $("#satu").hide(); 
        $("#dua").show(); 
        $("#tiga").hide(); 
        $("#empat").hide(); 
         $('#showKomponen').hide();

     });
     $('.step3').click(function() {
         $(this).find('button').removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('.step1 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step2 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step4 button').removeClass('btn-secondary').addClass('btn-outline-secondary');

            $('#ptkp').prop('disabled', false);

        $("#satu").hide(); 
        $("#dua").hide(); 
        $("#tiga").show();  
        $("#empat").hide(); 
         $('#showKomponen').hide();

     });
     $('.step4').click(function() {
        $(this).find('button').removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('.step1 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step2 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step3 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $("#satu").hide(); 
        $("#dua").hide(); 
        $("#tiga").hide();  
        $("#empat").show(); 
            $('#ptkp').prop('disabled', false);
         $('#showKomponen').show();


     });
    // <button type="button" id="BackDariAlamat" class="btn btn-outline-success float-right"><strong>Back</strong></button>
    // <button type="button" id="nextDariAlamat" class="btn btn-success float-right"><strong>Next</strong></button>
    // <button type="button" id="BackDariDarurat" class="btn btn-outline-success float-right"><strong>Back</strong></button>
    // <button type="button" id="nextDariDarurat" class="btn btn-success float-right"><strong>Next</strong></button>
    // <button type="button" id="BackDariStatus" class="btn btn-outline-success float-right"><strong>Back</strong></button>
     $('#nextDariPribadi').click(function() {
        $('.step1 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step2 button').removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('.step3 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step4 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $("#satu").hide(); 
        $("#dua").show(); 
        $("#tiga").hide(); 
        $("#empat").hide(); 
            $('#ptkp').prop('disabled', false);
         $('#showKomponen').hide();

        // btn btn-secondary //kalo aktif
        // btn btn-outline-secondary // kalo ga aktif
     });
       $('#BackDariAlamat').click(function() {
        $('.step1 button').removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('.step2 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step3 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step4 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $("#satu").show(); 
        $("#dua").hide(); 
        $("#tiga").hide(); 
        $("#empat").hide(); 
            $('#ptkp').prop('disabled', false);
         $('#showKomponen').hide();

        // btn btn-secondary //kalo aktif
        // btn btn-outline-secondary // kalo ga aktif
     });
       $('#nextDariAlamat').click(function() {
        $('.step1 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step2 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step3 button').removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('.step4 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $("#satu").hide(); 
        $("#dua").hide(); 
        $("#tiga").show(); 
        $("#empat").hide(); 
        // btn btn-secondary //kalo aktif
        // btn btn-outline-secondary // kalo ga aktif
            $('#ptkp').prop('disabled', false);
         $('#showKomponen').hide();

     });
         $('#BackDariDarurat').click(function() {
        $('.step1 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step2 button').removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('.step3 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step4 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $("#satu").hide(); 
        $("#dua").show(); 
        $("#tiga").hide(); 
        $("#empat").hide(); 
            $('#ptkp').prop('disabled', false);
         $('#showKomponen').hide();

        // btn btn-secondary //kalo aktif
        // btn btn-outline-secondary // kalo ga aktif
     });
       $('#nextDariDarurat').click(function() {
        $('.step1 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step2 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step3 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step4 button').removeClass('btn-outline-secondary').addClass('btn-secondary');

        $("#satu").hide(); 
        $("#dua").hide(); 
        $("#tiga").hide(); 
        $("#empat").show(); 
            $('#ptkp').prop('disabled', false);
         $('#showKomponen').show();

        // btn btn-secondary //kalo aktif
        // btn btn-outline-secondary // kalo ga aktif
     });
     $('#BackDariStatus').click(function() {
        $('.step1 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step2 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $('.step3 button').removeClass('btn-outline-secondary').addClass('btn-secondary');
        $('.step4 button').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $("#satu").hide(); 
        $("#dua").hide(); 
        $("#tiga").show(); 
        $("#empat").hide(); 
            $('#ptkp').prop('disabled', false);
                  $('#showKomponen').hide();


        // btn btn-secondary //kalo aktif
        // btn btn-outline-secondary // kalo ga aktif
     });
     if ($("#Kontrak").prop('checked')) {
        $('#tglKontrakMulai, #tglKontrakSelesai').show();
      }
     if ($("#Tetap").prop('checked')) {
        $('#tglKontrakMulai, #tglKontrakSelesai').hide();
        $('#tanggal_kontrak, #tanggal_selesai_kontrak').val('');
      }
     $('#Kontrak').click(function() {
      if ($(this).prop('checked')) {
        $('#tglKontrakMulai, #tglKontrakSelesai').show();
      }
     });
        $('#Magang').click(function() {
      if ($(this).prop('checked')) {
        $('#tglKontrakMulai, #tglKontrakSelesai').show();
      }
     });
     $('#Tetap').click(function() {
      if ($(this).prop('checked')) {
        $('#tglKontrakMulai, #tglKontrakSelesai').hide();
        $('#tanggal_kontrak, #tanggal_selesai_kontrak').val('');

         
      }
     });
  
  });

</script>
@endsection
