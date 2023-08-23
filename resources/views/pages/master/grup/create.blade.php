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
        <div class="row">
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('grup.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    </div>
                    <div class="card-body">
                        <div class="row col-12">
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="">Nama Grup<span class="text-red">*</span></label>
                                <input required type="text" name="nama_grup" class="form-control" value="{{old('nama_grup','')}}" >
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="">Total Max Kredit</label>
                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input required type="text" name="total_max_kredit" class="form-control numaja uang" value="{{old('total_max_kredit','')}}" id='total_max_kredit' >
                                </div>
                            </div>
                        </div>
                        <div class="row col-12">
                            <div class="form-group col-md-6">
                                <label for="">Nama PIC<span class="text-red">*</span></label>
                                <input required type="text" name="nama_pic" class="form-control" value="{{old('nama_pic','')}}" >
                            </div>           
                            <div class="form-group col-md-6">
                                <label for="">Email</label>
                                <input  type="email" name="email" class="form-control" value="{{old('email','')}}" >
                            </div>           
                        </div>
                        <div class="row col-12">
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
<script>
    $(document).ready(function() {
      // Listen for input events on all input fields
      $('input[type="text"]').on('input', function() {
        var inputValue = $(this).val();
        var uppercaseValue = inputValue.toUpperCase();
        $(this).val(uppercaseValue);
      });
  
    });
</script>

@endsection
