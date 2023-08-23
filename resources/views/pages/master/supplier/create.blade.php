
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
    <form action="{{ route('supplier.store') }}" method="POST" >
    @csrf
    <div class="row">
        <div class="col-12 ">
            <div class="card radiusSendiri">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <a href="{{ route('supplier.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                    </div>
                    <button type="submit" name="save" id="save" value="save" class="btn ml-auto btn-success radiusSendiri"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <h5 class="card-title"><b>Data</b></h5>
                </div>
                <div class="card-body">
                    <div class="row col-12">
                        <div class="form-group col-4">
                            <label for="">Nama Supplier <span style="color: red">*</span></label>
                            <input required type="text"  name="nama" class="form-control" value="{{old('nama','')}}" >                         
                        </div>
    
                        <div class="form-group col-4">
                            <label for="">Jenis Supplier</label>
                            <select class="form-control select2" style="width: 100%;" id='jenis_supplier_id' name="jenis_supplier_id">
                                @foreach ($jenis_supplier as $jenis)
                                    <option value="{{$jenis->id}}">{{ $jenis->nama }}</option>
                                @endforeach
                            </select>
                        </div>   
    
                        <div class="form-group col-4">
                            <label for="">Kota</label>
                            <select class="form-control select2" style="width: 100%;" id='kota_id' name="kota_id">
                                @foreach ($kota as $city)
                                    <option value="{{$city->id}}">{{ $city->nama }}</option>
                                @endforeach
                            </select>
                        </div>   
                    </div>
                   
                    <div class="row col-12">
                        <div class="form-group col-4">
                            <label for="">Alamat</label>
                            <input required type="text" name="alamat" class="form-control" value="{{old('alamat','')}}" >                         
                        </div>
    
    
                        <div class="form-group col-4">
                            <label for="">Telp</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">+62</i>
                                </span>
                                </div>
                                <input type="text" class="form-control numaja" name="telp"  value="{{old('telp','')}}" >
                            </div>
                        </div>
    
                        <div class="form-group col-4">
                            <label for="">Email</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope" aria-hidden="true"></i>
                                </span>
                                </div>
                                <input type="email" class="form-control" name="email"  value="{{old('email','')}}" >
                            </div>
                        </div>
                    </div>

                    

                    <div class="row col-12">
                        <div class="form-group col-4">
                            <label for="">NPWP / KTP</label>
                            <input required type="text" name="npwp" class="form-control" value="{{old('npwp','')}}" >                         
                        </div>
                        <div class="form-group col-4">
                            <label for="">Catatan</label>
                            <input type="text" name="catatan" class="form-control" value="{{old('catatan','')}}" >                         
                        </div>

                        <div class="form-group col-4">
                            <label for="tanggal_keluar">PPH</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><input type="checkbox" id="cekPPH" name="cekPPH"></span>
                                </div>
                                <input type="number" step=".01" name="pph" class="form-control" id="pph" value="0" min="0" readonly>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 radiusSendiri">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><b>Rekening bank</b></h5>
                </div>
                <div class="card-body">
                    <div class="row col-12">
                        <div class="form-group col-6">
                            <label for="">No. Rekening</label>
                            <input required type="text" name="no_rek" class="form-control" value="{{old('no_rek','')}}" >                         
                        </div>
                 
                        <div class="form-group col-6">
                            <label for="">Atas Nama</label>
                            <input required type="text" name="rek_nama" class="form-control" value="{{old('rek_nama','')}}" >                         
                        </div>
                    </div>
                    <div class="row col-12">
                        <div class="form-group col-6">
                            <label for="">Bank</label>
                            <input required type="text" name="bank" class="form-control" value="{{old('bank','')}}" >                         
                        </div>
                 
                        <div class="form-group col-6">
                            <label for="">Cabang</label>
                            <input required type="text" name="cabang" class="form-control" value="{{old('cabang','')}}" >                         
                        </div>
                    </div>
                </div>
     
            </div>
        </div>
           
    </div>
    </form>

</div>

<script type="text/javascript">
$(document).ready(function(){
   $('#cekPPH').click(function(){
            if($(this).is(":checked")){
                $('#pph').attr('readonly',false);
                $('#pph').val('');
                
                // console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#pph').val(0);
                $('#pph').attr('readonly',true);
                // console.log("Checkbox is unchecked.");
            }
        });
});

</script>
@endsection

