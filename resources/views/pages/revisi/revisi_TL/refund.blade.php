
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

</style>
    <form action="{{ route('revisi_tl.save_refund') }}" id="post_data" method="POST" >
        @csrf
        <div class="container-fluid">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{ route('revisi_tl.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" class="btn btn-success radiusSendiri"><i class="fa fa-fw fa-save" aria-hidden="true"></i> Simpan</button>
                </div>
                <div class="card-body" >
                    <div class="d-flex" style="gap: 20px">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="">Tanggal Pengembalian<span style="color:red">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" autocomplete="off" name="tanggal_pengembalian" class="form-control date" id="tanggal_pengembalian" placeholder="dd-M-yyyy" value="">
                                </div>
                            </div>  
                            <div class="form-group col-12">
                                <label for="select_customer">No. Sewa<span style="color:red">*</span></label>
                                <select class="form-control select2" style="width: 100%;" id='select_sewa' name="select_sewa" disabled>
                                    <option value="{{$sewa->id_sewa}}" selected>{{ $sewa->supir }} / {{ $sewa->nama_tujuan }} - {{ $sewa->no_sewa }} ({{ \Carbon\Carbon::parse($sewa->tanggal_berangkat)->format('d-M-Y') }}) </option>
                                </select>
                                <input type="hidden" name="id_sewa" value="{{$id_sewa}}" id="id_sewa">
                                <input type="hidden" name="no_sewa" value="{{$sewa->no_sewa}}" id="no_sewa">

                            </div>

                            <div class="form-group col-12">
                                <label for="">Customer</label>
                                <input type="text" id="customer" name="customer" class="form-control" value="{{ $sewa->getCustomer->nama }}" readonly>                         
                            </div>  

                            <div class="form-group col-12">
                                <label for="">Tujuan</label>
                                <input type="text" id="tujuan" name="tujuan" class="form-control" value="{{ $sewa->nama_tujuan }}" readonly>                         
                            </div>  
                        </div>
                        <div class="row">
                            <div class="form-group col-5">
                                <label for="">Kendaraan</label>
                                <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{ $sewa->no_polisi }}" readonly>                         
                            </div>  

                            <div class="form-group col-7">
                                <label for="">Driver</label>
                                <input type="text" id="driver" name="driver" class="form-control" value="{{ $sewa->nama_driver }}" readonly>     
                                <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{ $sewa->id_karyawan }}">                    
                            </div> 

                            <div class="form-group col-6">
                                <label for="uang_jalan">Stack TL</label>
                                <input type="text" id="cuman_deskripsi" name="cuman_deskripsi" class="form-control" value="BIAYA TELUK LAMONG" readonly> 
                                <input type="hidden" name="deskripsi_sewa_biaya" value="{{$checkTL->deskripsi}}">    
                                <input type="hidden" name="id_sewa_biaya" value="{{$checkTL->id_biaya}}">    

                                {{-- <select class="form-control select2" style="width: 100%;" id='stack_tl' name="stack_tl" disabled>
                                    <option value="" {{ $sewa->stack_tl == ''? 'selected':'' }}>── Pilih TL ──</option>
                                    <option value="tl_perak" {{ $sewa->stack_tl == 'tl_perak'? 'selected':'' }}>Perak</option>
                                    <option value="tl_teluk_lamong" {{ $sewa->stack_tl == 'tl_teluk_lamong'? 'selected':'' }}>Teluk Lamong</option>
                                    <option value="tl_priuk" {{ $sewa->stack_tl == 'tl_priuk'? 'selected':'' }}>Priuk</option>
                                </select> --}}
                            </div>

                            <div class="form-group col-6">
                                <label for="total_diterima">Jumlah</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" id="jumlah"name="jumlah" class="form-control uang numajaMinDesimal" value="{{ number_format($checkTL->biaya) }}" readonly>                         
                                </div>
                            </div>

                            <div class="form-group col-12">
                                <label for="">Dikembalikan Sebagai</label>      
                                <div class="d-flex" style="gap: 10px;">
                                    <select class="form-control select2" style="width: 100%;" id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                        <option value="">--PILIH OPSI--</option>
                                        @foreach ($dataKas as $kas)
                                            <option value="{{$kas->id}}">{{ $kas->nama }}</option>
                                        @endforeach
                                        <option value="hutang_karyawan">KEMBALI SEBAGAI HUTANG KARYAWAN</option>

                                    </select>
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
    </form>
<script type="text/javascript">
    $(document).ready(function() {
        var today = new Date();
        $('#tanggal_pengembalian').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
        }).datepicker("setDate", today);
    });
</script>

@endsection


