
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
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-3 col-md-3 col-lg-3'>
                              <label for="kode">Kode<span style='color:red'>*</span></label>
                              <input type="text" required name="kode" class="form-control" id="kode" placeholder="3 digit" maxlength=3  style="text-transform:uppercase" value="">    
                            </div>
                            <div class='col-9 col-md-9 col-lg-9'>
                              <label for="nama">Nama Customer<span style='color:red'>*</span></label>
                              <input type="text" required name="nama" class="form-control" id="nama" placeholder="" value=""> 
                            </div>
                        </div>
                    </div> 

                    <div class="form-group">
                        <label for="">Parent Grup</label>
                        <select class="form-control select2" required style="width: 100%;" id='grup_id' name="grup_id">
                            @foreach ($grups as $grup)
                                <option value="{{$grup['id']}}">{{ $grup['nama_grup'] }} </option>
                            @endforeach
                        </select>
                    </div>   

                    <div class="form-group">
                        <label for="">NPWP</label>
                        <input  type="text" name="npwp" class="form-control" value="{{old('npwp','')}}" >                         
                    </div>
             
                    <div class="form-group">
                        <label for="">Alamat</label>
                        <input  type="text" name="alamat" class="form-control" value="{{old('alamat','')}}" >                         
                    </div>

                    <div class="form-group">
                        <label for="">Kota</label>
                        <select class="form-control select2" style="width: 100%;" id='kota_id' name="kota_id">
                            <option value="0">&nbsp;</option>
                            @foreach ($kota as $city)
                                <option value="{{$city->id}}">{{ $city->nama }}</option>
                            @endforeach
                        </select>
                    </div>   

                    <div class="form-group">
                        <div class='row'>
                            <div class='col-6 col-md-6 col-lg-6'>
                                <label for="telp_1">Telp 1</label>
                                <div class="input-group mb-0">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                  </div>
                                  <input type="text" name="telp1" class="form-control numaja" maxlength="15" id="telp1" placeholder="" value="">    
                                </div>
                            </div>
                            <div class='col-6 col-md-6 col-lg-6'>
                                <label for="telp_2">Telp 2</label>
                                <div class="input-group mb-0">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                  </div>
                                  <input type="text" name="telp2" class="form-control numaja" maxlength="15" id="telp2" placeholder="" value="">    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                          </div>
                          <input type="text" name="email" id="email" class="form-control" placeholder="" value="">
                      </div>
                    </div>

                    <div class="form-group">
                        <label for="">Catatan</label>
                        <input  type="text" name="catatan" maxlength="150" class="form-control" value="{{old('catatan','')}}" >                         
                    </div>

                </div>
                <div class="card-footer">
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card ">
                <div class="card-header">
                <h3 class="card-title">Kredit & Ketentuan Pembayaran</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                    <div class='row'>
                        <div class='col-6 col-md-6 col-lg-6'>
                            <label for="kredit_sekarang">Kredit Sekarang</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                                </div>
                                <input type="text" name="kredit_sekarang" class="form-control numaja uang" id="kredit_sekarang" placeholder="" value="0" readonly>    
                            </div>
                        </div>
                        <div class='col-6 col-md-6 col-lg-6'>
                            <label for="maks_kredit">Maks Kredit</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                                </div>
                                <input type="text" name="maks_kredit" class="form-control numaja uang" id="maks_kredit" placeholder="" value="">    
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="form-group">
                    <div class="row">
                        <div class="col-6 col-md-6 col-lg-6">
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
