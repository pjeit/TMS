
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
  
@endsection

@section('content')
<style >
   .tinggi{
    height: 20px;
   }
</style>
<div class="container-fluid">
    <form action="{{ route('dalam_perjalanan.save_cancel_uang_jalan', [ $data['id_sewa'] ]) }}" method="POST" id="post_data">
        @csrf 
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('dalam_perjalanan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body">
               
               <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12" style=" border-right: 1px solid rgb(172, 172, 172);">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group ">
                                    <label for="tanggal_berangkat">Tanggal Berangkat<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input disabled type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse($data->tanggal_berangkat)->format('d-M-Y')}}">
                                    </div>
                                    <input type="hidden" name="id_sewa_hidden" value="{{$id_sewa}}">
                                    <input type="hidden" name="no_sewa" value="{{$data->no_sewa}}">
                                </div> 
                            </div>
                            <div class="col-6">
                                 <div class="form-group ">
                                    <label for="tanggal_cancel">Tanggal Cancel<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input disabled type="text" autocomplete="off"  class="form-control date" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse(now())->format('d-M-Y')}}">
                                        <input type="hidden" autocomplete="off" name="tanggal_cancel" class="form-control date" id="tanggal_cancel" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse(now())->format('d-M-Y')}}">
                                    </div>
                                </div> 
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <label for="no_akun">Customer</label>
                            <input type="text" id="customer" name="customer" class="form-control" value="[{{$data->getCustomer->kode}}] {{$data->getCustomer->nama}}" readonly>                         
                        </div>  

                        <div class="form-group ">
                            <label for="no_akun">Tujuan</label>
                            <input type="text" id="tujuan" name="tujuan" class="form-control" value="{{$data->nama_tujuan}}" readonly>                         
                        </div>  
                        <div class="form-group">
                            <label for="no_akun">No. Kontainer<span class="text-red">*</span></label>
                            @if ($data->no_kontainer_jod && $data->jenis_order =="INBOUND")
                                <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" readonly value="{{$data->no_kontainer_jod}}" >                         
                            @else
                                <input type="text" id="no_kontainer" required ="no_kontainer" class="form-control" readonly value="{{$data->no_kontainer}}" >                         
                            @endif
                        </div> 
                            @if ($data->seal_pelayaran_jod&&$data->jenis_order =="INBOUND")
                            <div class="form-group ">
                                <label for="seal">Segel Kontainer</label>
                                <input readonly type="text" id="seal" name="seal" class="form-control"value="{{$data->seal_pelayaran_jod}}" >
                            </div> 
                        @endif
                        
                        <div class="form-group">
                            <label for="no_akun">No. Surat Jalan<span class="text-red">*</span></label>
                            <input type="text" readonly id="surat_jalan" required name="surat_jalan" class="form-control" value="{{$data->no_surat_jalan}}" >                         
                        </div> 

                        {{-- <div class="form-group">
                            <label for="alasan_cancel">Alasan Cancel Perjalanan<span style="color: red;">*</span></label>
                            <textarea name="alasan_cancel" required class="form-control" id="alasan_cancel" rows="5" value=""></textarea>
                        </div> --}}
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="tanggal_pencairan">Tanggal Kembali</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input disabled type="text" autocomplete="off" class="form-control date" placeholder="dd-M-yyyy" value="{{\Carbon\Carbon::parse(now())->format('d-M-Y')}}">
                                    <input type="hidden" autocomplete="off" name="tanggal_kembali" class="form-control date" id="tanggal_kembali" placeholder="dd-M-yyyy" value="{{\Carbon\Carbon::parse(now())->format('d-M-Y')}}">
                                </div>
                            </div> 

                            <div class="form-group col-4">
                                <label for="no_akun">Kendaraan</label>
                                <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{$data->no_polisi}}" readonly>                         
                            </div>  

                            <div class="form-group col-8">
                                <label for="no_akun">Driver</label>
                                <input type="text" id="driver" name="driver" class="form-control" value="{{$data->nama_driver}}" readonly>     
                                <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{$data->id_karyawan}}">                    
                            </div> 

                            <div class="form-group col-12">
                                <label for="total_uang_jalan">Total Uang Jalan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input readonly="" value="{{ number_format(($data->getUJRiwayat[0]->total_uang_jalan + $data->getUJRiwayat[0]->total_tl)-$data->getUJRiwayat[0]->potong_hutang )  }}" type="text" name="total_uang_jalan" class="form-control numaja uang" id="total_uang_jalan" placeholder="">
                                </div>
                            </div>

                            {{-- <div class="form-group col-12">
                                <label for="uang_jalan_kembali">Uang Jalan Kembali<span class="text-red">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="uang_jalan_kembali" required id="uang_jalan_kembali" class="form-control numaja uang" >
                                </div>
                            </div> --}}
{{-- 
                            <div class="form-group col-12">
                                <label for="">Kas / Bank<span class="text-red">*</span></label>
                                <select class="form-control select2" name="pembayaran" id="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    @foreach ($dataKas as $kb)
                                        <option value="{{$kb->id}}" <?= $kb->id == 1 ? 'selected':''; ?> >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                    @endforeach
                                        <option value="HUTANG KARYAWAN">HUTANG KARYAWAN</option>
                                </select>
                            </div> --}}
                            
                        </div>
                    </div>
               </div>
            </div>
        </div> 
    </form>
</div>


         
 
<script type="text/javascript">
$(document).ready(function() {

    $(document).on('keyup', '#uang_jalan_kembali', function(){ 
        var total_uang_jalan = $('#total_uang_jalan').val();
        if(parseFloat(escapeComma(this.value)) > parseFloat(escapeComma(total_uang_jalan))){
            $('#uang_jalan_kembali').val(total_uang_jalan);
        }
    });
    $(document).on('focusout', '#uang_jalan_kembali', function(){ 
        check();
    });
    // document.getElementById("uang_jalan_kembali").addEventListener("focusout", myFunction);

    // function myFunction() {
    //     check();
    // }
    function check(){
        var total_uang_jalan = parseFloat(escapeComma($('#total_uang_jalan').val()));
        var uang_jalan_kembali = parseFloat(escapeComma($('#uang_jalan_kembali').val()));

        if(uang_jalan_kembali > total_uang_jalan){
            $('#uang_jalan_kembali').val(moneyMask(total_uang_jalan));
        }
    }
    
    $('#post_data').submit(function(event) {
            event.preventDefault();

            Swal.fire({
                 title: 'Apakah Anda yakin akan membatalkan uang jalan ?',
                text: "Konfirmasi kembali",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }else{
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


