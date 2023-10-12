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
<div class="container-fluid">
    <form action="{{ route('revisi_uang_jalan.store') }}" id="post_data" method="POST" >
      @csrf
        <div class="row m-2">
            <div class="col">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('revisi_uang_jalan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left mr-1"></i> Kembali</a>
                        <button type="submit" class="btn btn-success radiusSendiri"><i class="fa fa-credit-card mr-1" aria-hidden="true"></i> Simpan</button>
                    </div>
                    <div class="card-body" >
                        <div class="" style="">
                            <div class="row">
                                <div class="form-group col-lg-4 col-md-5 col-sm-12">
                                    <label for="no_akun">Customer</label>
                                    <input type="text" id="customer" name="customer" class="form-control" value="{{ $sewa->nama_cust }}" readonly>                         
                                </div>  
                                <div class="form-group col-lg-5 col-md-5 col-sm-12">
                                    <label for="select_customer">No. Sewa<span style="color:red">*</span></label>
                                    <input class="form-control" value="{{ $sewa->nama_tujuan }} - {{ $sewa->no_sewa }}" readonly>
                                    <input type="hidden" value="{{$sewa->no_sewa}}" id="no_sewa" name="no_sewa">
                                    <input type="hidden" value="{{$sewa->id_sewa}}" id="id_sewa_defaulth" name="id_sewa_defaulth">
                                    <input type="hidden" value="{{$sewa->nama_tujuan}}" id="tujuan" name="tujuan">
                                </div>
    
                                <div class="form-group col-lg-3 col-md-2 col-sm-12">
                                    <label for="tanggal_pencairan">Tanggal Berangkat</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input disabled type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse($sewa->tanggal_berangkat)->format('d-M-Y') }}">
                                    </div>
                                </div>  
    
                    
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="no_akun">Kendaraan</label>
                                    <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{ $sewa->no_polisi }}" readonly>                         
                                </div>  
    
                                <div class="form-group col-6">
                                    <label for="no_akun">Driver</label>
                                    <input type="text" id="driver" name="driver" class="form-control" value="{{ $sewa->nama_driver }}" readonly>     
                                    <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{ $sewa->id_karyawan }}">                    
                                </div> 
    
                                <div class="form-group col-6">
                                    <label for="total_hutang">Total Hutang</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_hutang" name="total_hutang" class="form-control uang " value="{{ number_format($sewa->total_hutang) }}" readonly>                         
                                    </div>
                                </div>
    
                                <div class="form-group col-6">
                                    <label for="potong_hutang">Potong Hutang Karyawan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" id="potong_hutang" name="potong_hutang" class="form-control uang " value="" >                         
                                    </div>
                                </div>

                                <div class="form-group col-6">
                                    <label for="uang_jalan">Tambahan Uang Jalan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="uang_jalan" name="uang_jalan" class="form-control uang " value="{{ number_format($sewa->uang_jalan_gt - $sewa->total_uang_jalan) }}" readonly>                         
                                        <input type="hidden" name="jenis" value="tambahan">
                                    </div>
                                </div>

                                <div class="form-group col-6">
                                    <label for="total_diterima">Total</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang " value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label for="tanggal_pencairan">Tanggal Pencairan<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="tanggal_pencairan" class="form-control date" id="tanggal_pencairan" placeholder="dd-M-yyyy" value="">
                                    </div>
                                </div>  
                                <div class="form-group col-6">
                                    <label for="">PILIH PEMBAYARAN <span class="text-red">*</span></label>      
                                    <div class="d-flex" style="gap: 10px;">
                                        <select class="form-control select2" required style="width: 100%;" id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                            <option value="">── PILIH KAS ──</option>
                                            @foreach ($dataKas as $kas)
                                                <option value="{{$kas->id}}">{{ $kas->nama }}</option>
                                            @endforeach
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
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var today = new Date();
        $('#tanggal_pencairan').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
        }).datepicker("setDate", today);

        if(escapeComma($('#total_hutang').val()) == 0){
            // kalau total hutang 0, di disable inputan potong hutangnya
            $('#potong_hutang').attr('readonly', 'readonly');
        }

        $('#potong_hutang').on('keyup', function(event){
            cek_potongan_hutang();
            hitung_total();
        });
        function cek_potongan_hutang(){
            var potong_hutang = escapeComma($('#potong_hutang').val());
            var uang_jalan = escapeComma($('#uang_jalan').val());

            console.log('potong_hutang', potong_hutang);
            console.log('uang_jalan', uang_jalan);            
            if(parseFloat(potong_hutang) > parseFloat(uang_jalan)){
                $('#potong_hutang').val(uang_jalan);
            }
        }
        function hitung_total(){
            var potong_hutang = escapeComma($('#potong_hutang').val());
            var uang_jalan = escapeComma($('#uang_jalan').val());
            var total = parseFloat(uang_jalan) - parseFloat(potong_hutang);
            $('#total_diterima').val(moneyMask(total));
        }
    });

</script>

@endsection


