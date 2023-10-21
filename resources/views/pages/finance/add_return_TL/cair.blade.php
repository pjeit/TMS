
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
    <form action="{{ route('add_return_tl.store') }}" id="post_data" method="POST" >
      @csrf
        <div class="row m-2">
        
            <div class="col">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('add_return_tl.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
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
                                        <select class="form-control select2" style="width: 100%;" id='select_sewa' name="select_sewa" disabled>
                                            <option value="{{$sewa->id_sewa}}" selected>{{ $sewa->supir }} / {{ $sewa->nama_tujuan }} - {{ $sewa->no_sewa }} ({{ \Carbon\Carbon::parse($sewa->tanggal_berangkat)->format('d-M-Y') }}) </option>
                                        </select>
                                        <input type="hidden" name="id_sewa_defaulth" value="{{$id_sewa_defaulth}}" id="id_sewa_defaulth">
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
                                    <div class="form-group col-12" style="opacity: 0%;">
                                        <label for="" >a</label>
                                        <input type="text" id="hidden" name="hidden" class="form-control"  >                         
                                    </div>  
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="">Kendaraan</label>
                                        <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{ $sewa->no_polisi }}" readonly>                         
                                    </div>  

                                    <div class="form-group col-6">
                                        <label for="">Driver</label>
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
                                        <input type="text" id="potong_hutang" name="potong_hutang" class="form-control numaja uang " value="0" >                         
                                    </div>
                                </div>

                                <div class="form-group col-4">
                                    <label for="uang_jalan">Stack TL</label>
                                    <select class="form-control select2" style="width: 100%;" disabled>
                                        <option value="" {{ $sewa->stack_tl == ''? 'selected':'' }}>── Pilih TL ──</option>
                                        <option value="tl_perak" {{ $sewa->stack_tl == 'tl_perak'? 'selected':'' }}>Perak</option>
                                        <option value="tl_teluk_lamong" {{ $sewa->stack_tl == 'tl_teluk_lamong'? 'selected':'' }}>Teluk Lamong</option>
                                        <option value="tl_priuk" {{ $sewa->stack_tl == 'tl_priuk'? 'selected':'' }}>Priuk</option>
                                    </select>
                                    <input type="hidden" value="{{ $sewa->stack_tl }}" name='stack_tl'>
                                    <input type="hidden" name="stack_tl_hidden_value" value="{{$sewa->stack_tl}}">
                                </div>

                                <div class="form-group col-4">
                                    <label for="total_diterima">Jumlah</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <!-- <input type="text" class="form-control uang numajaMinDesimal" value="{{ number_format($jumlah) }}" readonly>                          -->
                                        {{-- <input type="hidden" name="jumlah" value="{{ $jumlah }}"> --}}
                                        <input type="text" id="jumlah" name="jumlah" class="form-control uang numajaMinDesimal" value="{{ number_format($jumlah) }}" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-4">
                                    <label for="total_diterima">Total</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang " value="" readonly>                         
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label for="">PILIH PEMBAYARAN</label>      
                                    <div class="d-flex" style="gap: 10px;">
                                        <select class="form-control select2" style="width: 100%;" id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                            <option value="">--PILIH PEMBAYARAN--</option>
                                            @foreach ($dataKas as $kas)
                                                <option value="{{$kas->id}}">{{ $kas->nama }}</option>
                                            @endforeach
                                                <!-- <option value="hutang_karyawan">POTONG HUTANG KARYAWAN</option> -->
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

        if(escapeComma($('#total_hutang').val()) == 0){
            // kalau total hutang 0, di disable inputan potong hutangnya
            $('#potong_hutang').attr('readonly', 'readonly');
        }
        cek_potongan_hutang();
        hitung_total();
        $('#potong_hutang').on('keyup', function(event){
            cek_potongan_hutang();
            hitung_total();
        });
        function cek_potongan_hutang(){
            var potong_hutang = escapeComma($('#potong_hutang').val());
            var jumlah = escapeComma($('#jumlah').val());
            var total_hutang = escapeComma($('#total_hutang').val());


            console.log('potong_hutang', potong_hutang);
            console.log('jumlah', jumlah);            
            if(parseFloat(potong_hutang) > parseFloat(jumlah)){
                $('#potong_hutang').val(jumlah);
            }
            if(parseFloat(potong_hutang) > parseFloat(total_hutang)){
                $('#potong_hutang').val(total_hutang);
            }
            if(potong_hutang.trim()=='')
            {
                potong_hutang = 0;
            }
        }
        function hitung_total(){
            var potong_hutang = escapeComma($('#potong_hutang').val());
             if($('#potong_hutang').val()!=''){
                var potong_hutang=removePeriod($('#potong_hutang').val(),',');
            }else{
                var potong_hutang=0;
            }
            var jumlah = escapeComma($('#jumlah').val());
            var total = parseFloat(jumlah) - parseFloat(potong_hutang);
            $('#total_diterima').val(moneyMask(total));
        }

        $('#tanggal_pencairan').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
        }).datepicker("setDate", today);
    });
</script>

@endsection


