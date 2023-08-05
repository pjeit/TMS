
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('head.index')}}">Head</a></li>
<li class="breadcrumb-item">Edit</li>

@endsection
@section('content')

<div class="container-fluid">
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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Jenis Transaksi</h5>
                </div>

                <form action="{{ route('coa.update',[$coa->id]) }}" method="POST" >
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row justify-content-center g-2">
                            <div class="col">
                                <div class="form-group">
                                    <label for="nama_jenis">Nama Jenis</label>
                                    <input required type="text" maxlength="20" name="nama_jenis" class="form-control" value="{{old('nama_jenis',$coa->nama_jenis)}}" >                         
                                </div>
                                <div class="form-group">
                                    <label for="no_akun">No. akun</label>
                                    <input required type="number" maxlength="10" name="no_akun" class="form-control" value="{{old('no_akun',$coa->no_akun)}}" >                         
                                </div>  
                                <div class="form-group">
                                    <label for="tipe">Tipe</label>
                                    <br>

                                    <div class="icheck-primary d-inline">
                                        <input id="setuju" type="radio" name="tipe" value="1" {{'pengeluaran' == old('tipe',$coa->tipe)? 'checked' :'' }}>
                                        <label class="form-check-label" for="setuju">Pengeluaran</label>
                                    </div>
                                    <div class="icheck-primary d-inline">
                                        <input id="tdkSetuju" type="radio" name="tipe" value="2" {{'penerimaan'== old('tipe',$coa->tipe)? 'checked' :'' }}>
                                        <label class="form-check-label" for="tdkSetuju">Penerimaan</label><br>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="catatan">Catatan</label>
                                    <input type="text" maxlength="100" name="catatan" class="form-control" value="{{old('catatan',$coa->catatan)}}" >                         
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
