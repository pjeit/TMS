
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('grup_member.index')}}">Grup Member</a></li>
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
    <form action="{{ route('grup_member.store') }}" method="POST" >
    @csrf
      <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('grup_member.index') }}"class="btn btn-secondary radiusSendiri float-left"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
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
                    <div class="row">
                        <div class="form-group  col-12 col-md-4 col-sm-12">
                            <label for="">Grup</label>
                            <select class="form-control select2" style="width: 100%;" id='grup_id' name="grup_id" required>
                                <option value="0">&nbsp;</option>
                                @foreach ($grup as $item)
                                    <option value="{{$item->id}}">{{ $item['nama_grup'] }}</option>
                                @endforeach
                            </select>
                        </div>   
    
                        <div class="form-group  col-12 col-md-4 col-sm-12">
                            <label for="">Nama</label>
                            <input  type="text" required name="nama" class="form-control" value="{{old('nama','')}}" >                         
                        </div>
                 
                        <div class="form-group  col-12 col-md-4 col-sm-12">
                            <label for="">No Rekening</label>
                            <input type="text" name="no_rek" maxlength="25" class="form-control numaja" value="{{old('no_rek','')}}" >                         
                        </div>
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

                </div>
                {{-- <div class="card-footer">
                     <button type="submit" class="btn btn-success float-left">Simpan</button>
                </div> --}}
            </div>
        </div>
      
    </div>

    </form>

</div>
@endsection
