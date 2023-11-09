
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif


@section('content')
<br>
<style>
</style>

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
    <form action="{{ route('status_kendaraan.update',[$dataStatusKendaraan->id]) }}" method="POST" id="post">
        @csrf
        @method('PUT')
        <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{ route('status_kendaraan.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                    <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                            <label for="tanggal_mulai">Tanggal Mulai<span style='color:red'>*</span></label>
                            <div class="input-group mb-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" autocomplete="off" name="tanggal_mulai" class="form-control" id="tanggal_mulai" placeholder="dd-M-yyyy" value="{{old('tanggal_mulai',\Carbon\Carbon::parse($dataStatusKendaraan->tanggal_mulai)->format('d-M-Y'))}}">
                            </div>
                        </div>
                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                            <label for="tanggal_selesai">Tanggal Selesai</label>
                            <div class="input-group mb-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><input type="checkbox" id="check_is_selesai" {{$dataStatusKendaraan->is_selesai == 'Y'?'checked':''}}></span>
                            </div>
                            <input type="hidden" id="is_selesai" name='is_selesai' value="{{old('is_selesai',$dataStatusKendaraan->is_selesai)}}">
                            <input type="text" autocomplete="off" name="tanggal_selesai" class="form-control" id="tanggal_selesai" placeholder="dd-M-yyyy" {{$dataStatusKendaraan->is_selesai == 'N'?'readonly':''}} value="{{old('tanggal_selesai',\Carbon\Carbon::parse($dataStatusKendaraan->tanggal_selesai)->format('d-M-Y'))}}">
                            </div>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                            <label for="select_kendaraan">Kendaraan<span style="color:red">*</span></label>
                                <select class="form-control select2  @error('select_kendaraan') is-invalid @enderror" style="width: 100%;" id='select_kendaraan' name="select_kendaraan">
                                <option value="">Pilih Jenis Kendaraan</option>
                                @foreach ($dataKendaraan as $data)
                                    <option value="{{$data->id}}" {{$dataStatusKendaraan->kendaraan_id==$data->id?'selected':''}} >{{ $data->no_polisi }} ({{$data->kategoriKendaraan}})</option>
                                @endforeach
                            </select>
                            @error('select_kendaraan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror   
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                            <label for="detail_perawatan">Detail Perawatan<span style='color:red'>*</span></label>
                            <textarea rows="4" name="detail_perawatan" class="form-control" id="detail_perawatan" placeholder="" >{{old('detail_perawatan',$dataStatusKendaraan->detail_perawatan)}}</textarea> 
                        </div>
                    </div>
                </div>
        </div>
    </form>
<script>
$(document).ready(function () {
        $('#tanggal_mulai').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
           // endDate: "0d"
        });
        if($('#check_is_selesai').is(":checked")){
                $('#is_selesai').val('Y');
                $('#tanggal_selesai').attr('readonly',false);
                $('#tanggal_selesai').datepicker({
                    autoclose: true,
                    format: "dd-M-yyyy",
                    todayHighlight: true,
                    language:'en'
                });
				$('#tanggal_selesai').val(get_date_now);
				// console.log("Checkbox is checked.");
            }else if($('#check_is_selesai').is(":not(:checked)")){
                $('#is_selesai').val('N');
                $('#tanggal_selesai').val('');
                $('#tanggal_selesai').attr('readonly',true);
                $('#tanggal_selesai').datepicker('destroy');
                // console.log("Checkbox is unchecked.");
            }
        $('#check_is_selesai').click(function(){
            if($(this).is(":checked")){
                $('#is_selesai').val('Y');
                $('#tanggal_selesai').attr('readonly',false);
                $('#tanggal_selesai').datepicker({
                    autoclose: true,
                    format: "dd-M-yyyy",
                    todayHighlight: true,
                    language:'en'
                });
				$('#tanggal_selesai').val(get_date_now);
				// console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#is_selesai').val('N');
                $('#tanggal_selesai').val('');
                $('#tanggal_selesai').attr('readonly',true);
                $('#tanggal_selesai').datepicker('destroy');
                // console.log("Checkbox is unchecked.");
            }
        });
    $('#post_data').submit(function(event) {
    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

    if($("#select_kendaraan").val()=='')
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `KENDARAAN BELUM DIPILIH!`,
            })
            
            return;
        }
        
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
                }, 200); // 2000 milliseconds = 2 seconds
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
@endsection
