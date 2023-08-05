
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
<li class="breadcrumb-item">Create</li>

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
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data</h5>
                </div>
                <div class="card-body">
                     <div class="form-group">
                        <label for="">No. Polisi*</label>
                        <input required type="text" maxlength="20" name="no_polisi" class="form-control" value="{{old('no_polisi','')}}" >                         
                    </div>
                    <div class="form-group">
                        <label for="">No. Mesin</label>
                        <input required type="text" maxlength="20" name="no_mesin" class="form-control" value="{{old('no_mesin','')}}" >
                    </div>           
                    <div class="form-group">
                        <label for="">No. Rangka</label>
                        <input required type="text" maxlength="20" name="no_rangka" class="form-control" value="{{old('no_rangka','')}}" >
                    </div>           
                    <div class="form-group">
                        <label for="">Merk & Model</label>
                        <input required type="text" name="merk_model" class="form-control" value="{{old('merk_model','')}}" >
                    </div>           
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Detail</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="">Tahun Pembuatan</label>
                            <input required type="text" name="tahun_pembuatan" maxlength="4" class="form-control" value="{{old('tahun_pembuatan','')}}" >
                        </div>          
                        <div class="form-group col-6">
                            <label for="">Warna</label>
                            <input required type="text" name="warna" class="form-control" value="{{old('warna','')}}" >
                        </div>          
                    </div>
                    <div class="form-group">
                        <label for="">Chasis (data masih dummy)</label>
                        <select class="form-control select2" style="width: 100%;" id='chassis_id' name="chassis_id">
                                <option value="1">Chasis 1</option>
                                <option value="2">Chasis 2</option>
                                <option value="3">Chasis 3</option>
                        </select>
                    </div>   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
