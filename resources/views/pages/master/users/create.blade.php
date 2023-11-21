
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
    <form action="{{ route('users.store') }}" method="POST" id="post">
      @csrf
       
        <div class="row">
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                            <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
                    </div>
                <div class="card-body">
                    <div class="form-group" id="karyawanForm">
                        <label for="karyawan_id">Karyawan</label>
                        <select class="form-control selectpicker" name="karyawan" id="karyawan" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Karyawan">
                            <option value="">--Pilih karyawan--</option>
                            @foreach($dataKaryawan as $data)
                                <option value="{{$data->id}}">{{$data->nama_panggilan}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nama">Username<span style='color:red'>*</span></label>
                        <input type="text" name="username" class="form-control alfanumber" id="username" placeholder="" value="" required> 
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
                                <input id="karyawanRadio" type="radio" name="status_karyawan" checked  >
                                <label class="form-check-label" for="karyawanRadio">Karyawan</label>
                            </div>
                            <div class="icheck-primary d-inline ml-4">
                                <input id="customerRadio" type="radio" name="status_karyawan" >
                                <label class="form-check-label" for="customerRadio">Customer</label><br>
                            </div>
                        </div>
    
    

                    <div class="form-group" id="customerForm">
                        <label for="customer_id">Customer</label>
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
                                <option value="{{$data->id}}">{{$data->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </form>

<script>
    $(document).ready(function() {
        $('#post').submit(function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin data sudah benar?',
                text: "Periksa kembali data anda",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'success',
                        title: 'Data Disimpan'
                    })

                    setTimeout(() => {
                        this.submit();
                    }, 1000); // 2000 milliseconds = 2 seconds
                }else{
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'warning',
                        title: 'Batal Disimpan'
                    })
                    event.preventDefault();
                }
            })
        });
    });
</script>
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
    $('#save').click(function(){
        if( $('#karyawan').val()==''&&$('#customer').val()=='')
        {
            $('#save').attr("type", "button");
            toastr.error("Status user harap dipilih salah satu");
        }
        else if($('#karyawan').val()&&$('#customer').val())
        {   
            $('#save').attr("type", "button");
            $("#karyawan").val('').selectpicker('refresh');
            $("#customer").val('').selectpicker('refresh');
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
        }
    });
    $('#customerRadio').click(function() {
        if ($(this).prop('checked')) {
            $('#karyawanForm').hide();
            $('#customerForm').show();
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
