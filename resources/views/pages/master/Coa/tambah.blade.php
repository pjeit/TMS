
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif

@section('content')
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tambah Coa</h5>
                </div>

                <form action="{{ route('coa.store') }}" method="POST" >
                    @csrf
                    <div class="card-body">
                        <div class="row justify-content-center g-2">
                            <div class="col">
                                <div class="form-group">
                                    <label for="nama_jenis">Nama Jenis</label>
                                    <input require type="text" maxlength="20" name="nama_jenis" class="form-control" value="{{old('nama_jenis','')}}" >                         
                                </div>
                                <div class="form-group">
                                    <label for="no_akun">No. akun</label>
                                    <input require type="number" maxlength="10" name="no_akun" class="form-control" value="{{old('no_akun','')}}" >                         
                                </div>  
                                <div class="form-group">
                                    <label for="tipe">Tipe</label>
                                    <br>

                                    <div class="icheck-primary d-inline">
                                        <input id="setuju" type="radio" name="tipe" value="1" {{'1' == old('tipe','')? 'checked' :'' }}>
                                        <label class="form-check-label" for="setuju">Pengeluaran</label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input id="tdkSetuju" type="radio" name="tipe" value="2" {{'2'== old('tipe','')? 'checked' :'' }}>
                                        <label class="form-check-label" for="tdkSetuju">Penerimaan</label><br>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="catatan">Catatan</label>
                                    <input require type="text" maxlength="100" name="catatan" class="form-control" value="{{old('catatan','')}}" >                         
                                </div>  
                            </div>
                           
                        </div>

                        <button type="submit" class="btn btn-success"><strong>Simpan</strong></button>
                        <a href="{{ route('coa.index') }}" class="btn btn-info"><strong>Kembali</strong></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
