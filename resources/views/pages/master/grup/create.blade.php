@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')

@endsection
<style>

</style>
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
    
    <form action="{{ route('grup.store') }}" method="POST">

        @csrf
         <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('grup.index') }}"class="btn btn-secondary radiusSendiri float-left"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
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
                            <label for="">Nama Grup<span class="text-red">*</span></label>
                            <input required type="text" name="nama_grup" class="form-control" value="{{old('nama_grup','')}}" >
                        </div>
                        <div class="form-group">
                            <label for="">Total Kredit</label>
                            <div class="input-group ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" class="form-control numaja uang" disabled value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Total Max Kredit</label>
                            <div class="input-group ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input required type="text" name="total_max_kredit" class="form-control numaja uang" value="{{old('total_max_kredit','')}}" id='total_max_kredit' >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <h5 class="card-title">PIC</h5>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Nama PIC<span class="text-red">*</span></label>
                            <input required type="text" name="nama_pic" class="form-control" value="{{old('nama_pic','')}}" >
                        </div>           
                        <div class="form-group">
                            <label for="">Email</label>
                            <input  type="email" name="email" class="form-control" value="{{old('email','')}}" >
                        </div>           
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="">Telp 1<span class="text-red">*</span></label>
                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+62</span>
                                    </div>
                                    <input required type="text" maxlength="12" name="telp1" class="form-control numaja " value="{{old('telp1','')}}" id='telp1' >
                                </div>
                            </div>      
                            <div class="form-group col-6">
                                <label for="">Telp 2</label>
                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">+62</span>
                                    </div>
                                    <input  type="text" maxlength="12" name="telp2" class="form-control numaja " value="{{old('telp2','')}}" id='telp2' >
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
