
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('role.index')}}">Role</a></li>
    <li class="breadcrumb-item">Edit</li>
@endsection

@section('content')
<br>
<style>
   
</style>

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
    <form action="{{ route('role.update',[$role->id]) }}" method="POST" >
    @csrf
    @method('PUT')
        <div class="row">
            {{-- <div class="col-12 ">
                <div class="card radiusSendiri">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <a href="{{ route('role.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                        </div>
                        <button type="submit" name="save" id="save" value="save" class="btn ml-auto btn-success radiusSendiri"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    </div>
                </div>
            </div> --}}
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('role.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                        <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
                    </div>
                    <div class="card-body">
                        <div class="form-group col-md-4">
                            <label for="nama_jenis">Nama Role</label>
                            <input required type="text" placeholder="contoh: Admin Staff"  name="nama" class="form-control" value="{{old('nama',$role->nama)}}" >                         
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

<script type="text/javascript">

  $(document).ready(function() {
 
  });

</script>

<script>
    
</script>

<script>
   
</script>
@endsection
