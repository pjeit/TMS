
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('supplier.index')}}">Supplier</a></li>
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
    <form action="{{ route('supplier.update', [$data->id]) }}" method="POST" >
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="">Nama Supplier*</label>
                        <input required type="text"  name="nama" class="form-control" value="{{$data->nama}}" >                         
                    </div>

                    <div class="form-group">
                        <label for="">Jenis Supplier </label>
                        <select class="form-control select2" style="width: 100%;" id='jenis_supplier_id' name="jenis_supplier_id">
                            @foreach ($jenis_supplier as $jenis)
                                <option value="{{$jenis->id}}" <?= ($jenis->id == $data->jenis_supplier_id)? 'Selected':''; ?> >{{ $jenis->nama }}</option>
                            @endforeach
                        </select>
                    </div>   

                    <div class="form-group">
                        <label for="">Alamat</label>
                        <input required type="text" name="alamat" class="form-control" value="{{$data->alamat}}" >                         
                    </div>

                    <div class="form-group">
                        <label for="">Kota</label>
                        <select class="form-control select2" style="width: 100%;" id='kota_id' name="kota_id">
                            <option value="0">&nbsp;</option>
                            @foreach ($kota as $city)
                                <option value="{{$city->id}}" <?= ($city->id==$data->kota_id)? 'selected':''; ?> >{{ $city->nama }}</option>
                            @endforeach
                        </select>
                    </div>   

                    <label for="">Telp</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-phone" aria-hidden="true"></i>
                          </span>
                        </div>
                        <input type="text" class="form-control" name="telp"  value="{{ $data->telp }}" >
                    </div>

                    <br>
                    <label for="">Email</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope" aria-hidden="true"></i>
                          </span>
                        </div>
                        <input type="email" class="form-control" name="email"  value="{{ $data->email }}" >
                    </div>

                    <br>
                    <div class="form-group">
                        <label for="">NPWP</label>
                        <input required type="text" name="npwp" class="form-control" value="{{ $data->npwp }}" >                         
                    </div>

                    <div class="form-group">
                        <label for="">Catatan</label>
                        <input required type="text" name="catatan" class="form-control" value="{{ $data->catatan }}" >                         
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
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Rekening bank</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="">No. Rekening</label>
                        <input required type="text" name="no_rek" class="form-control" value="{{ $data->no_rek }}" >                         
                    </div>
             
                    <div class="form-group">
                        <label for="">Atas Nama</label>
                        <input required type="text" name="rek_nama" class="form-control" value="{{ $data->rek_nama }}" >                         
                    </div>
             
                    <div class="form-group">
                        <label for="">Bank</label>
                        <input required type="text" name="bank" class="form-control" value="{{ $data->bank }}" >                         
                    </div>
             
                    <div class="form-group">
                        <label for="">Cabang</label>
                        <input required type="text" name="cabang" class="form-control" value="{{ $data->cabang }}" >                         
                    </div>

                </div>
     
            </div>
        </div>
           
    </div>
    </form>

</div>
@endsection
