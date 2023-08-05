
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('kas_bank.index')}}">Kas Bank</a></li>
    <li class="breadcrumb-item">Create</li>
@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data</h5>
                </div>
                <div class="card-body">
                     <div class="form-group">
                        <label for="nama_jenis">Nama Kas / Bank</label>
                        <input required type="text" maxlength="45" name="nama_jenis" class="form-control" value="{{old('nama_jenis','')}}" >                         
                    </div>
                    <div class="form-group">
                        <label for="no_akun">No. akun</label>
                        <input required type="number" maxlength="20" name="no_akun" class="form-control" value="{{old('no_akun','')}}" >                         
                    </div>  
                    <div class="form-group">
                        <label for="tipe">Tipe</label>
                        <br>

                        <div class="icheck-primary d-inline">
                            <input id="kas" type="radio" name="tipe" value="1" {{'1' == old('tipe','')? 'checked' :'' }}>
                            <label class="form-check-label" for="kas">Kas</label>
                        </div>
                        <div class="icheck-primary d-inline">
                            <input id="bank" type="radio" name="tipe" value="2" {{'2'== old('tipe','')? 'checked' :'' }}>
                            <label class="form-check-label" for="bank">Bank</label><br>
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        <label for="catatan">Saldo Awal</label>
                        <input type="text" oninput="formatNumber(this)" maxlength="100" name="catatan" class="form-control" value="{{old('catatan','')}}" >                         
                    </div>  --}}
                    <div class="form-group">
                        <label for="saldo_awal">Saldo Awal</label>
                        <div class="input-group mb-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" oninput="formatNumber(this)" maxlength="100" name="catatan" class="form-control" value="{{old('catatan','')}}" >                         
                        </div>
                    </div>
                    <div class="form-group">
                         <label for="lastName">Tanggal Pembuatan</label>
                          <input type="date" class="form-control" id="tanggalDibuat" placeholder="" value="" required="" name="tanggalDibuat">
                          <div class="invalid-feedback"> Valid last name is required. </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Rekening Bank</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="no_akun">No. Rekening</label>
                        <input required type="number" maxlength="25" name="no_akun" class="form-control" value="{{old('no_akun','')}}" >                         
                    </div>  
                    <div class="form-group">
                        <label for="no_akun">Atas Nama</label>
                        <input required type="text" maxlength="45" name="no_akun" class="form-control" value="{{old('no_akun','')}}" >                         
                    </div>  
                    <div class="form-group">
                        <label for="no_akun">Nama Bank</label>
                        <input required type="text" maxlength="45" name="no_akun" class="form-control" value="{{old('no_akun','')}}" >                         
                    </div>  
                    <div class="form-group">
                        <label for="no_akun">Kantor Cabang</label>
                        <input required type="text" maxlength="100" name="no_akun" class="form-control" value="{{old('no_akun','')}}" >                         
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection
