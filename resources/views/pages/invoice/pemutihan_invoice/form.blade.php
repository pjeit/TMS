
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
{{-- <li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('coa.index')}}">COA</a></li>
<li class="breadcrumb-item">Edit</li> --}}
@endsection
@section('content')
<style>
#preview_foto_nota {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}

#preview_foto_barang {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}
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
    <form action="{{ route('pemutihan_invoice.update',[$pemutihan_invoice->id]) }}" method="POST" id="post" >
        @csrf
        @method('post')
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('klaim_supir_revisi.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="no_invoice">No.Invoice</label>
                            <input type="text" name="no_invoice" id="no_invoice" class="form-control" value="{{$pemutihan_invoice->no_invoice}}" readonly>
                        </div>
                      
                        <div class="form-group">
                            <label for="customer_id">Customer<span style='color:red'>*</span></label>
                            <select disabled id="select_customer" style="width:100%" data-placeholder="Pilih Customer">
                                <option value=''></option>
                                <option value="121" selected="selected">Bapak Adi</option>
                            </select>
                            <input type='hidden' id='customer_id' name='customer_id' value="121">
                        </div>
                        <div class="form-group">
                            <div class='row'>
                                <div class="col-6">
                                    <label for="total_bayar">Total Pembayaran</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" name="total_bayar" class="form-control numajaMinDesimal uang" id="total_bayar" placeholder="" readonly value="{{number_format($pemutihan_invoice->total_dibayar)}}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="total_bayar_aktual">Total Tagihan Yang Harus Dibayar</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" name="total_bayar_aktual" class="form-control numajaMinDesimal uang" id="total_bayar_aktual" placeholder="" readonly value="{{number_format($pemutihan_invoice->total_sisa)}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="total_sisa">Total Sisa Tagihan</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="total_sisa" class="form-control numajaMinDesimal uang" id="total_sisa" placeholder="" readonly value="{{number_format($pemutihan_invoice->total_sisa)}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="tanggal_pemutihan">Tanggal Pemutihan<span style='color:red'>*</span></label>
                            <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" autocomplete="off" name="tanggal_pemutihan" class="date form-control" id="tanggal_pemutihan"  placeholder="dd-M-yyyy" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="jumlah_pemutihan">Jumlah Pemutihan<span style='color:red'>*</span></label>
                            <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" onkeyup="cek_jumlah_pemutihan();" name="jumlah_pemutihan" class="form-control numajaMinDesimal uang" id="jumlah_pemutihan" placeholder="" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="catatan_pemutihan">Catatan</label>
                            <input type="text" name="catatan" class="form-control" id="catatan_pemutihan" placeholder="" value=""> 
                        </div>
                    </div>
                </div>
            </div>

            </div>
        </div>
    </form>
</div>
<script>
	function cek_jumlah_pemutihan(){
        if($('#total_sisa').val()!=''){
            var total_sisa =removePeriod($('#total_sisa').val(),',');
        }else{
            var total_sisa =0;
        }
        var jumlah_pemutihan = removePeriod($('#jumlah_pemutihan').val(),',');
        if(parseFloat(jumlah_pemutihan)>parseFloat(total_sisa)){
            $('#jumlah_pemutihan').val(addPeriod(total_sisa,','));
        }else{
            $('#jumlah_pemutihan').val(addPeriod(jumlah_pemutihan,','));
        }
    }    
$(document).ready(function() {
    
        $('#tanggal').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            endDate: "0d"
        });
		$('#tanggal_pemutihan').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            endDate: "0d"
        });
		$('#jumlah_pemutihan').val(addPeriod($('#total_sisa').val(),','));
    $('#post').submit(function(event) {
        var statusKlaim = $("input[name='status_klaim']:checked").val();
        var tanggal_pencairan = $("#tanggal_pencairan").val();
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
                })
        if(statusKlaim=="REJECTED")
        {
            if(alasan_tolak.trim()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `ALASAN TOLAK WAJIB DIISI!`,
                })
                return;
            }
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
