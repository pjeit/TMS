
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
  
@endsection

@section('content')
<style>
   
</style>

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
    <form action="{{ route('users.store') }}" method="POST" >
      @csrf
        <div class="col-12 ">
            <div class="card radiusSendiri">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                    </div>
                    <button type="submit" name="save" id="save" value="save" class="btn ml-auto btn-success radiusSendiri"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card radiusSendiri">
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
                    <label for="karyawan_id">Customer</label>
                    <select class="form-control selectpicker" name="customer" id="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Karyawan">
                        <option value="">--Pilih customer--</option>
                        @foreach($dataCustomer as $data)
                            <option value="{{$data->id}}">{{$data->nama}}</option>
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
        </div>
    </form>

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
