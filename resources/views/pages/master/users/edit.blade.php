
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
    <li class="breadcrumb-item">Edit</li>
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
    <form action="{{ route('users.update',[$user->id]) }}" method="POST" >
      @csrf
      @method('PUT')
        {{-- <div class="card radiusSendiri">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                </div>
                <button type="submit" name="save" id="save" value="save" class="btn ml-auto btn-success radiusSendiri"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
        </div> --}}
        <div class="row">
            
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                      
                            <a href="{{ route('users.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                            <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
                        
                    </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="nama">Username<span style='color:red'>*</span></label>
                        <input type="text" name="username" class="form-control" id="username" placeholder="" value="{{old('username',$user->username)}}"> 
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
                        <label for="tipe">Status User</label>
                        <br>
                        <div class="icheck-primary d-inline">
                            <input id="karyawanRadio" type="radio" name="status_karyawan" {{$user->karyawan_id!=null? 'checked' :'' }}>
                            <label class="form-check-label" for="karyawanRadio">Karyawan</label>
                        </div>
                        <div class="icheck-primary d-inline ml-2">
                            <input id="customerRadio" type="radio" name="status_karyawan" {{$user->customer_id!=null? 'checked' :'' }}>
                            <label class="form-check-label" for="customerRadio">Customer</label><br>
                        </div>
                    </div>
                    <div class="form-group" id="karyawanForm">
                        <label for="karyawan_id">Karyawan</label>
                        <select class="form-control selectpicker" name="karyawan" id="karyawan" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Karyawan">
                            <option value="">--Pilih karyawan--</option>
                            @foreach($dataKaryawan as $data)
                                <option value="{{$data->id}}"{{$data->id == $user->karyawan_id? 'selected' :'' }}>{{$data->nama_panggilan}}</option>
                            @endforeach
                        </select>
                    </div>
                      <div class="form-group" id="customerForm">
                        <label for="customer_id">Customer</label>
                        <select class="form-control selectpicker" name="customer" id="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Karyawan">
                            <option value="">--Pilih customer--</option>
                            @foreach($dataCustomer as $data)
                                <option value="{{$data->id}}"{{$data->id == $user->customer_id? 'selected' :'' }}>{{$data->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                      <div class="form-group">
                        <label for="akses_id">Hak Akses<span style='color:red'>*</span></label>
                        <select class="form-control selectpicker" name="role" id="role" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Karyawan" required>
                            <option value="">--Pilih role--</option>
                            @foreach($dataRole as $data)
                                <option value="{{$data->id}}"{{$data->id == $user->role_id? 'selected' :'' }}>{{$data->nama}}</option>
                            @endforeach
                        </select>
                  
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
$(document).ready(function(){
            var dataStatus = <?php echo json_encode($user); ?>;

    $('#save').click(function(){
        if( $('#karyawan').val()==''&&$('#customer').val()=='')
        {
            $('#save').attr("type", "button");
            toastr.error("Status user harap dipilih salah satu");
        }
        else if($('#karyawan').val()&&$('#customer').val())
        {   
            console.table(dataStatus.customer_id);

            if(dataStatus.customer_id!=null)
            {
                $("#karyawan").val('').selectpicker('refresh');
                $('#save').attr("type", "button");
            }
            else if(dataStatus.karyawan_id!=null)
            {
                $('#save').attr("type", "button");
                $("#customer").val('').selectpicker('refresh');
            }
         

            toastr.error("Status user hanya dipilih satu");
        }
        else
        {
             $('#save').attr("type", "submit");
        }
    })
    $('#karyawanRadio').click(function() {
        if ($(this).prop('checked')) {
            $('#karyawanForm').show();
            $('#customerForm').hide();
           
            if(dataStatus.karyawan_id!=null)
            {
                $("#karyawan").val(dataStatus.karyawan_id).selectpicker('refresh');
                $("#customer").val('').selectpicker('refresh');

            }
            // console.log($("#karyawan").val());
            // console.log($("#customer").val());

        }
    });
    $('#customerRadio').click(function() {
        if ($(this).prop('checked')) {
            $('#karyawanForm').hide();
            $('#customerForm').show();
            if(dataStatus.customer_id!=null)
            {
                $("#karyawan").val('').selectpicker('refresh');
                $("#customer").val(dataStatus.customer_id).selectpicker('refresh');

            }
            // console.log($("#karyawan").val());
            // console.log($("#customer").val());
        }
    });
    if($('#karyawanRadio').prop("checked")){
        $('#karyawanForm').show();
        $('#customerForm').hide();
    } 
    if($('#customerRadio').prop("checked")){
        $('#karyawanForm').hide();
        $('#customerForm').show();
    
    }

});
</script>
@endsection
