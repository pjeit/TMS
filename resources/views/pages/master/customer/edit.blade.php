
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
<li class="breadcrumb-item">Edit</li>

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
    <form action="{{ route('customer.update', ['customer' => $data->id]) }}" method="POST" >
    @method('PUT')
    @csrf
     <div class="card radiusSendiri">
        <div class="card-header">
            <a href="{{ route('customer.index') }}"class="btn btn-secondary radiusSendiri float-left"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
            <button type="submit" class="btn btn-success radiusSendiri float-right"><i class="fa fa-fw fa-save"></i> Simpan</button>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <h5 class="card-title">Data</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-12 col-md-3 col-lg-3'>
                              <label for="kode">Kode<span style='color:red'>*</span></label>
                              <input type="text" required name="kode" class="form-control" id="kode" placeholder="3 digit" maxlength=3  style="text-transform:uppercase" value="{{ $data->kode }}">    
                            </div>
                            <div class='col-12 col-md-9 col-lg-9'>
                              <label for="nama">Nama Customer<span style='color:red'>*</span></label>
                              <input type="text" required name="nama" class="form-control" id="nama" placeholder="" value="{{ $data->nama}}"> 
                            </div>
                        </div>
                    </div> 

                    <div class="form-group">
                        <label for="">Parent Grup</label>
                        <select class="form-control select2" required style="width: 100%;" id='grup_id' name="grup_id">
                            <option value="0">&nbsp;</option>
                            @foreach ($grups as $grup)
                                <option value="{{$grup['id']}}" <?= ($grup['id']==$data->grup_id)? 'selected':''; ?> >{{ $grup['nama_grup'] }} </option>
                            @endforeach
                        </select>
                    </div>   

                    <div class="form-group">
                        <label for="">NPWP</label>
                        <input  type="text" name="npwp" class="form-control" value="{{$data->npwp}}" >                         
                    </div>
             
                    <div class="form-group">
                        <label for="">Alamat</label>
                        <input  type="text" name="alamat" class="form-control" value="{{$data->alamat}}" >                         
                    </div>

                    <div class="form-group">
                        <label for="">Kota</label>
                        <select class="form-control select2" style="width: 100%;" id='kota_id' name="kota_id">
                            <option value="0">&nbsp;</option>
                            @foreach ($kota as $city)
                                <option value="{{$city->id}}" <?= ($city['id']==$data->kota_id)? 'selected':'xxx' ?> >{{ $city->nama }}</option>
                            @endforeach
                        </select>
                    </div>   

                    {{-- <div class="form-group">
                        <div class='row'>
                            <div class='col-12 col-md-6 col-lg-6'>
                                <label for="telp_1">Telp 1</label>
                                <div class="input-group mb-0">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                  </div>
                                  <input type="text" name="telp1" class="form-control numaja" maxlength="15" id="telp1" placeholder="" value="{{$data->telp1}}">    
                                </div>
                            </div>
                            <div class='col-12 col-md-6 col-lg-6'>
                                <label for="telp_2">Telp 2</label>
                                <div class="input-group mb-0">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                  </div>
                                  <input type="text" name="telp2" class="form-control numaja" maxlength="15" id="telp2" placeholder="" value="{{$data->telp2}}">    
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    {{-- <div class="form-group">
                        <label for="email">Email</label>
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                          </div>
                          <input type="text" name="email" id="email" class="form-control" placeholder="" value="{{$data->email}}">
                      </div>
                    </div> --}}

                    <div class="form-group">
                        <label for="">Catatan</label>
                        <input  type="text" name="catatan" maxlength="150" class="form-control" value="{{$data->catatan}}" >                         
                    </div>

                </div>
                {{-- <div class="card-footer">
                    <div class="col">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div> --}}
            </div>
        </div>

        <div class="col-6">
            <div class="card radiusSendiri">
                <div class="card-header">
                <h3 class="card-title">Kredit & Ketentuan Pembayaran</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                    <div class='row'>
                        <div class='col-6 col-md-6 col-lg-6'>
                            <label for="kredit_sekarang">Kredit Sekarang</label>
                            <div class="input-group ">
                                <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                                </div>
                                <input type="text" name="kredit_sekarang" class="form-control numaja uang" id="kredit_sekarang" placeholder="" value="{{$data->kredit_sekarang}}" readonly>    
                            </div>
                        </div>
                          <div class="col-6 col-md-6 col-lg-6">
                            <label for="ketentuan_bayar">Ketentuan Pembayaran</label>
                            <div class="input-group ">
                                <input type="text" name="ketentuan_bayar" class="form-control numaja" id="ketentuan_bayar" placeholder="" value="{{number_format($data->ketentuan_bayar)}}">
                                <div class="input-group-append">
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                        </div>
                        {{-- <div class='col-12 col-md-6 col-lg-6'>
                            <label for="max_kredit">Maks Kredit</label>
                            <div class="input-group ">
                                <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                                </div>
                                <input type="text" name="max_kredit" class="form-control numaja uang" id="max_kredit" placeholder="" value="{{number_format($data->max_kredit)}}">    
                            </div>
                        </div> --}}
                    </div>
                    </div>
                    <div class="form-group">
               
                    </div>
                </div>
            </div>
        </div>
    </div>

    </form>

</div>
@endsection
