
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('customer.index')}}">Customer</a></li>
<li class="breadcrumb-item">Create</li>

@endsection

@section('content')
<div class="container-fluid">
  
    @if ($errors->any())
        @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endforeach

    @endif
    <form action="{{ route('customer.store') }}" method="POST" >
    @csrf
  
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{ route('customer.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                    <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-12 col-md-5 col-lg-5'>
                                <label for="">Grup <span style='color:red'>*</span></label>
                                <select class="form-control select2" required style="width: 100%;" id='grup_id' name="grup_id">
                                    @foreach ($grups as $grup)
                                        <option value="{{$grup['id']}}">{{ $grup['nama_grup'] }} </option>
                                    @endforeach
                                </select>
                            </div>   
                            <div class='col-12 col-md-2 col-lg-2'>
                              <label for="kode">Kode <span style='color:red'>*</span></label>
                              <input type="text" required name="kode" class="form-control" id="kode" placeholder="3 digit" maxlength=3  style="text-transform:uppercase" value="">    
                            </div>
                            <div class='col-12 col-md-5 col-lg-5'>
                              <label for="nama">Nama Customer <span style='color:red'>*</span></label>
                              <input type="text" required name="nama" class="form-control" id="nama" placeholder="" value=""> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                <label for="">NPWP</label>
                                <input  type="text" name="npwp" class="form-control" maxlength="20" value="{{old('npwp','')}}" >                         
                            </div>

                            <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                <label for="">Kota</label>
                                <select class="form-control select2" style="width: 100%;" id='kota_id' name="kota_id">
                                    <option value="0">&nbsp;</option>
                                    @foreach ($kota as $city)
                                        <option value="{{$city->id}}">{{ $city->nama }}</option>
                                    @endforeach
                                </select>
                            </div>  
                     
                            <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                <label for="">Alamat</label>
                                <input  type="text" name="alamat" class="form-control" value="{{old('alamat','')}}" >                         
                            </div>
        
                            <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                <label for="">Catatan</label>
                                <input  type="text" name="catatan" maxlength="150" class="form-control" value="{{old('catatan','')}}" >                         
                            </div>

                            <div class='col-sm-12 col-md-3 col-lg-3'>
                                <label for="kredit_sekarang">Kredit Sekarang</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="kredit_sekarang" class="form-control numaja uang" id="kredit_sekarang" placeholder="" value="0" readonly>    
                                </div>
                            </div>
                               <div class="col-sm-12 col-md-3 col-lg-3">
                                <label for="ketentuan_pembayaran">Ketentuan Pembayaran</label>
                                <div class="input-group mb-0">
                                    <input type="text" name="ketentuan_bayar" class="form-control numaja" id="ketentuan_bayar" placeholder="" value="30">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Hari</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div> 

                   

                </div>
        
            </div>
        </div>

    </div>

    </form>

</div>
@endsection
