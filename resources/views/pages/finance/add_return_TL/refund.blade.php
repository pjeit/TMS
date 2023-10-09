
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
    <form action="{{ route('pencairan_uang_jalan_ftl.store') }}" id="post_data" method="POST" >
      @csrf
        <div class="row m-2">
        
            <div class="col">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('pencairan_uang_jalan_ftl.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    </div>
                    <div class="card-body" >
                        <div class="d-flex" style="gap: 20px">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="">Tanggal Pencairan<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="tanggal_pencairan" class="form-control date" id="tanggal_pencairan" placeholder="dd-M-yyyy" value="">
                                    </div>
                                </div>  
                                <div class="form-group col-12">
                                    <label for="select_customer">No. Sewa<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_sewa' name="select_sewa">
                                        <option value="{{$sewa->id_sewa}}" selected>{{ $sewa->supir }} / {{ $sewa->nama_tujuan }} - {{ $sewa->no_sewa }} ({{ \Carbon\Carbon::parse($sewa->tanggal_berangkat)->format('d-M-Y') }}) </option>
                                    </select>
                                    <input type="hidden" value="{{$id_sewa_defaulth}}" id="id_sewa_defaulth">
                                </div>

                                <div class="form-group col-12">
                                    <label for="">Tanggal Berangkat<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input disabled type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="">
                                    </div>
                                </div>  

                                <div class="form-group col-12">
                                    <label for="no_akun">Customer</label>
                                    <input type="text" id="customer" name="customer" class="form-control" value="" readonly>                         
                                </div>  

                                <div class="form-group col-12">
                                    <label for="no_akun">Tujuan</label>
                                    <input type="text" id="tujuan" name="tujuan" class="form-control" value="" readonly>                         
                                </div>  
                            </div>
                            <div class="row">
                                <div class="form-group col-5">
                                    <label for="no_akun">Kendaraan</label>
                                    <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="" readonly>                         
                                </div>  

                                <div class="form-group col-7">
                                    <label for="no_akun">Driver</label>
                                    <input type="text" id="driver" name="driver" class="form-control" value="" readonly>     
                                    <input type="hidden" name="id_karyawan" id="id_karyawan">                    
                                </div> 

                                <div class="form-group col-7">
                                    <label for="total_hutang">Total Hutang</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_hutang" name="total_hutang" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>

                                <div class="form-group col-5">
                                    <label for="potong_hutang">Potong Hutang</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" onkeyup="cek_potongan_hutang();hitung_total();" maxlength="100" id="potong_hutang" name="potong_hutang" class="form-control uang numajaMinDesimal" value="" >                         
                                    </div>
                                </div>

                                <div class="form-group col-6">
                                    <label for="uang_jalan">Uang Jalan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="uang_jalan" name="uang_jalan" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>

                                <div class="form-group col-6">
                                    <label for="total_diterima">Total Diterima</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label for="">PILIH PEMBAYARAN</label>      
                                    <div class="d-flex" style="gap: 10px;">
                                        <select class="form-control select2" style="width: 100%;" id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                            <option value="">--PILIH KAS--</option>
                                            @foreach ($dataKas as $kas)
                                                <option value="{{$kas->id}}">{{ $kas->nama }}</option>
                                            @endforeach
                                        </select>

                                        <button type="submit" class="btn btn-success radiusSendiri"><i class="fa fa-credit-card" aria-hidden="true"></i> Bayar</button>
                                    </div>  
                                </div>  
                                 <div class="form-group col-12">
                                    <label for="no_akun">Catatan</label>
                                    <input type="text" id="catatan" name="catatan" class="form-control" value="" >                         
                                </div> 
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
 
    </form>
<script type="text/javascript">
    $(document).ready(function() {
        var today = new Date();

        $('#tanggal_pencairan').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
        }).datepicker("setDate", today);
    });
</script>

@endsection


