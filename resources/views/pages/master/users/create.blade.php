
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('users.index')}}">User</a></li>
    <li class="breadcrumb-item">Create</li>
@endsection

@section('content')
<br>
<style>
   
</style>

<div class="container">
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
    <form action="{{ route('users.store') }}" method="POST" >
      @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Data</h5>
                    </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama">Username<span style='color:red'>*</span></label>
                        <input type="text" name="username" class="form-control" id="username" placeholder="" value=""> 
                    </div>
                    <div class="form-group">
                        <label for="nama">Password<span style='color:red'>*</span></label>
                        <div class="input-group mb-0">
                            <input type="password" name="password" class="form-control" id="password" placeholder="Diisi untuk me-reset password / data baru" value=""> 
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default" onclick='showpassowrd()'><i id='showpassword' class='fa fa-eye'></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="karyawan_id">Karyawan</label>
                        <select class="form-control selectpicker" name="karyawan" id="karyawan" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Karyawan">
                            <option value="">--Pilih karyawan--</option>
                            @foreach($dataKaryawan as $data)
                                <option value="{{$data->id}}">{{$data->nama_panggilan}}</option>
                            @endforeach
                        </select>
                    </div>
                      <div class="form-group">
                        <label for="akses_id">Hak Akses<span style='color:red'>*</span></label>
                        <select class="form-control selectpicker" name="role" id="role" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Karyawan" required>
                            <option value="">--Pilih role--</option>
                            @foreach($dataRole as $data)
                                <option value="{{$data->id}}">{{$data->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                        <a href="{{ route('users.index') }}" class="btn btn-info"><strong>Kembali</strong></a>
                        <button type="submit" class="btn btn-success"><strong>Simpan</strong></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
function showpassowrd() {
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }
</script>
@endsection
