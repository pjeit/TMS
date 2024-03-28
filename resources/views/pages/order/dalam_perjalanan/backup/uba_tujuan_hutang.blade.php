
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
    <form action="{{ route('dalam_perjalanan.save_ubah_tujuan', [ $data['id_sewa'] ]) }}" method="POST" id="post_data">
        @csrf 
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('dalam_perjalanan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                <span class="badge badge-dark float-right">{{ $data->jenis_order}} ORDER</span>

            </div>
            <div class="card-body">
                
                <div class="row">
                    <div class="form-group col-lg-4 col-md-12 col-sm-12">
                        <label for="tanggal_berangkat">Tanggal Berangkat</label>
                        <div class="input-group mb-0">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input disabled type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse($data->tanggal_berangkat)->format('d-M-Y')}}">
                        </div>
                        <input type="hidden" name="id_sewa_hidden" value="{{$id_sewa}}">
                        <input type="hidden" name="no_sewa" value="{{$data->no_sewa}}">
                    </div> 
                    <div class="form-group col-lg-4 col-md-12 col-sm-12">
                        <label for="no_akun">Kendaraan</label>
                        <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{$data->no_polisi}}" readonly>                         
                    </div>  
                    <div class="form-group col-lg-4 col-md-12 col-sm-12">
                        <label for="no_akun">Driver</label>
                        @if ($data->id_supplier==null)
                            <input type="text" id="driver" name="driver" class="form-control" value="{{$data->nama_driver}}" readonly>     
                            <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{$data->id_karyawan}}"> 
                        @else
                            <input type="text" class="form-control" readonly="" name="driver" value="DRIVER REKANAN {{ $supplier->nama }}">
                        @endif
                    </div> 
                    
                    <div class="col-lg-6 col-md-6 col-sm-12" style=" border-right: 1px solid rgb(172, 172, 172);">
                        
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="no_akun">Customer Awal</label>
                                <input type="text" id="customer_awal" name="customer_awal" class="form-control" value="[{{$data->getCustomer->kode}}] {{$data->getCustomer->nama}}" readonly>                         
                            </div>  
                            <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                <label for="no_akun">Tujuan Awal</label>
                                <input type="text" id="tujuan_awal" name="tujuan_awal" class="form-control" value="{{$data->nama_tujuan}}" readonly>                         
                            </div>  
                            <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                <label for="no_akun">Tarif Awal</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input readonly="" value="{{ number_format($data->total_tarif)}}" type="text" name="tarif_awal" class="form-control numaja uang" id="tarif_awal" placeholder="">
                                </div>
                            </div>  
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="total_uang_jalan_lama">
                                    Total Uang Jalan actual
                                    (<span class="badge badge-primary">UJ : {{number_format($ujr->total_uang_jalan) }} </span> +
                                    <span class="badge badge-success">TL : {{ number_format($ujr->total_tl)}} </span>) =
                                    <span class="badge badge-secondary">{{ number_format($total_uang_jalan)}}</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input readonly="" value="{{ number_format($total_uang_jalan ) }}" type="text" name="total_uang_jalan_lama" class="form-control numaja uang" id="total_uang_jalan_lama" placeholder="">
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="total_uang_jalan_diterima">
                                    Total Uang Jalan Yang diterima supir
                                    (<span class="badge badge-primary">UJ : {{number_format($ujr->total_uang_jalan) }} </span> +
                                    <span class="badge badge-success">TL : {{ number_format($ujr->total_tl)}} </span>) -
                                    <span class="badge badge-danger">Potong Hutang : {{ number_format($ujr->potong_hutang)}}</span> = 
                                    <span class="badge badge-secondary">{{ number_format($total_uang_jalan_diterima)}}</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input readonly="" value="{{ number_format($total_uang_jalan_diterima ) }}" type="text" name="total_uang_jalan_diterima" class="form-control numaja uang" id="total_uang_jalan_diterima" placeholder="">
                                </div>
                            </div>
                           
                            {{-- <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="alasan_ubah_tujuan">Alasan Ubah Tujuan<span style="color: red;">*</span></label>
                                <textarea name="alasan_ubah_tujuan" required class="form-control" id="alasan_ubah_tujuan" rows="5" value=""></textarea>
                            </div> --}}
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            @if (isset($ujr))
                                <div class="form-group col-12">
                                    <label for="select_customer">Customer<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_customer' name="select_customer" {{$data->jenis_order=="INBOUND"?'disabled':''}} >
                                        <option value="">Pilih Customer</option>
                                        @foreach ($dataCustomer as $cust)                                        
                                            <option value="{{$cust->idCustomer}}" nama_cust="{{$cust->namaCustomer}}" kode_cust="{{$cust->kodeCustomer}}"{{/*$data->jenis_order=="INBOUND" &&*/ $data->id_customer==$cust->idCustomer ?'selected':''}}> {{ $cust->kodeCustomer }} - {{ $cust->namaCustomer }} / {{ $cust->namaGrup }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="customer_id" name="customer_id" value="{{$data['id_customer']}}" placeholder="customer_id">
                                    <input type="hidden" id="customer_nama_baru" name="customer_nama_baru" value="{{$data->getCustomer->nama}}" placeholder="customer_nama_baru">
                                    <input type="hidden" id="customer_kode_baru" name="customer_kode_baru" value="{{$data->getCustomer->kode}}" placeholder="customer_nama_baru">


                                    <input type="hidden" id="booking_id" name="booking_id" value="" placeholder="booking_id">
                                    <input type="hidden" id="jenis_order" name="jenis_order" value="{{$data['jenis_order']}}" placeholder="jenis_order">
                                </div>
                                <div class="form-group col-6">
                                    
                                    <label for="select_tujuan">Tujuan Baru<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_grup_tujuan' name="select_grup_tujuan" {{$data['id_booking']!=null ||$data['jenis_order']=="INBOUND" || $data['status']== 'PROSES DOORING'&&$data['jenis_order']=="OUTBOUND"?'readonly':''}} >
                                        {{-- @isset($data['id_grup_tujuan'])
                                            <option value="{{$data['id_grup_tujuan']}}">{{$data->getTujuan->nama_tujuan}}</option>
                                        @endisset --}}
                                    </select>
                                    <input type="hidden" id="tujuan_id" name="tujuan_id" value="" placeholder="tujuan_id">
                                    <input type="hidden" name="id_jo_detail" id="id_jo_detail" value="{{!empty($data['id_jo_detail'])? $data['id_jo_detail']:''}}" placeholder="id_jo_detail">
                                    <input type="hidden" name="id_jo" id="id_jo" value="{{!empty($data['id_jo'])?$data['id_jo']:''}}" placeholder="id_jo">
                                    <input type="hidden" id="nama_tujuan" name="nama_tujuan" value=""placeholder="nama_tujuan">
                                    <input type="hidden" id="alamat_tujuan" name="alamat_tujuan" value=""placeholder="alamat_tujuan">
                                    <input type="hidden" id="tarif" name="tarif" value=""placeholder="tarif">
                                    <input type="hidden" id="uang_jalan" name="uang_jalan" value=""placeholder="uang_jalan">
                                    <input type="hidden" id="komisi" name="komisi" value=""placeholder="komisi">
                                    <input type="hidden" id="komisi_driver" name="komisi_driver" value=""placeholder="komisi_driver">
                                    <input type="hidden" id="jenis_tujuan" name="jenis_tujuan" value=""placeholder="jenis_tujuan">
                                    <input type="hidden" id="harga_per_kg" name="harga_per_kg" value="0"placeholder="harga_per_kg">
                                    <input type="hidden" id="min_muatan" name="min_muatan" value="0"placeholder="min_muatan">
                                    <input type="hidden" id="plastik" name="plastik" value=""placeholder="plastik">
                                    <input type="hidden" id="tally" name="tally" value=""placeholder="tally">
                                    <input type="hidden" id="kargo" name="kargo" value=""placeholder="kargo">
                                    <input type="hidden" id="biayaDetail" name="biayaDetail"placeholder="biayaDetail">
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Tarif Baru<span class="text-red">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" name="tarif_baru" id="tarif_baru" class="form-control numaja uang" value="" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 col-md-12 col-sm-12">
                                    <label for="">Uang Jalan Baru<span class="text-red">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" name="uang_jalan_baru" id="uang_jalan_baru" class="form-control numaja uang" value="" readonly>
                                    </div>
                                </div>
                                
                                <div class="form-group col-lg-3 col-md-12 col-sm-12" id="stack_tl_form">
                                    <label for="select_driver" id="stack_tl_label">Stack Full</label>
                                        <select class="form-control select2" style="width: 100%;" id='stack_tl' name="stack_tl">
                                        <option value="">── Pilih Stack ──</option>
                                            <option value="tl_perak" {{ $data->stack_tl == 'tl_perak'? 'selected':'' }}>Perak</option>
                                        <option value="tl_priuk" {{ $data->stack_tl == 'tl_priuk'? 'selected':'' }}>Priuk</option>
                                        <option value="tl_teluk_lamong" {{ $data->stack_tl == 'tl_teluk_lamong'? 'selected':'' }}>Teluk Lamong</option>
                                    </select>
                                    <input type="hidden" id="stack_teluk_lamong_hidden" name="stack_teluk_lamong_hidden" value="{{$ujr->total_tl}}" placeholder="stack_teluk_lamong_hidden">
                                </div>
                                <div class="form-group col-lg-3 col-md-3 col-sm-12">
                                    <label for="">Selisih Uang Jalan<span class="text-red">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" name="selisih_uang_jalan" id="selisih_uang_jalan" class="form-control numaja uang" value="" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 col-md-3 col-sm-12">
                                    <label for="">Total hutang lama<span class="text-red">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" name="total_hutang_lama" id="total_hutang_lama" class="form-control numaja uang" value="{{number_format($ujr->potong_hutang)}}" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="total_hutang">Total Hutang</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        {{-- kenapa di tambah potong hutang soalnya kalau hutangnya 0 dan mau handel potong, gabisa nanti --}}
                                        <input type="text" maxlength="100" id="total_hutang" name="total_hutang" class="form-control uang numaja" 
                                        value="{{number_format($data->getKaryawan->getHutang->total_hutang)}}" readonly>                         
                                    </div>
                                </div>
                             
                                <div class="form-group col-lg-6 col-md-12 col-sm-12 " 
                                    @if (isset($data->getKaryawan->getHutang) && $data->getKaryawan->getHutang->total_hutang > 0 ||$ujr->potong_hutang !=0)
                                        style="background: hsl(0, 100%, 93%); border: 1px red solid;"
                                    @endif>
                                    <label for="potong_hutang">
                                        @if (isset($data->getKaryawan->getHutang) && $data->getKaryawan->getHutang->total_hutang > 0  ||$ujr->potong_hutang!=0)
                                            <span class="text-red">Potong Hutang</span>
                                        @else
                                            <span>Potong Hutang</span>
                                        @endif
                                    </label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text"  maxlength="100" id="potong_hutang" name="potong_hutang" class="form-control uang numaja" value="" {{isset($data->getKaryawan->getHutang) && $data->getKaryawan->getHutang->total_hutang <= 0? 'disabled':''}}>                         
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                    <label for="">Tanggal Pencairan<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="tanggal_pencairan" class="form-control date" id="tanggal_pencairan" value="">
                                    </div>
                                </div>  
                                <div class="form-group col-lg-6 col-md-6 col-sm-12 ">
                                    <label for="total_akhir" id="total_akhir_label">Total Diberikan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_akhir" name="total_akhir" class="form-control uang " value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Kas / Bank<span class="text-red">*</span></label>
                                    <select class="form-control select2" name="pembayaran" id="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                        @foreach ($dataKas as $kb)
                                            <option value="{{$kb->id}}" >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                        @endforeach
                                            <option value="HUTANG">HUTANG KARYAWAN</option>
                                            <option value="TIDAK_ADA_TRANSAKSI">TIDAK ADA TRANSAKSI</option>
                                    </select>
                                    <input type="hidden" name="jenis_masuk" id="jenis_masuk">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
            </div>
        </div> 
       
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#post_data').submit(function(event) {
                event.preventDefault();
    
                Swal.fire({
                    title: 'Apakah Anda yakin data sudah benar ?',
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
<script type="text/javascript">
$(document).ready(function() {
    function hitung_total(){
        if($('#selisih_uang_jalan').val()!=''){
            var selisih_uang_jalan=removePeriod($('#selisih_uang_jalan').val(),',');
            // var selisih_uang_jalan=normalize($('#selisih_uang_jalan').val()); 
        }else{
            var selisih_uang_jalan=0;
        }
        
        if($('#total_hutang_lama').val()!=''){
            var total_hutang_lama=removePeriod($('#total_hutang_lama').val(),',');
            // var total_hutang_lama=normalize($('#total_hutang_lama').val()); 
        }else{
            var total_hutang_lama=0;
        }
        
        if($('#potong_hutang').val()!=''){
            var potong_hutang=removePeriod($('#potong_hutang').val(),',');  
            // var potong_hutang=normalize($('#potong_hutang').val());  
        }else{
            var potong_hutang=0;
        }
        var total_akhir=(parseFloat(Math.abs(selisih_uang_jalan))+parseFloat(total_hutang_lama)) - parseFloat(potong_hutang);
        // var total_akhir=Math.abs(selisih_uang_jalan)-potong_hutang;
        console.log(total_akhir);
        if(total_akhir!=0){
            if (potong_hutang>parseFloat(Math.abs(selisih_uang_jalan))+parseFloat(total_hutang_lama)) {
                $('#total_akhir').val(0);
            }
            else
            {
                // $('#total_akhir').val(moneyMask(total_akhir));
                $('#total_akhir').val(addPeriodType(total_akhir,','));
            }

        }else{
            $('#total_akhir').val(0);
        }
    }

    function cek_potongan_hutang(){
        if($('#total_hutang').val()!=''){
            var total_hutang =removePeriod($('#total_hutang').val(),',');
        }else{
            var total_hutang =0;
        }
        if($('#selisih_uang_jalan').val()!=''){
            var selisih_uang_jalan=removePeriod($('#selisih_uang_jalan').val(),',');
        }else{
            var selisih_uang_jalan=0;
        }
        if($('#total_hutang_lama').val()!=''){
            var total_hutang_lama=removePeriod($('#total_hutang_lama').val(),',');
            // var total_hutang_lama=normalize($('#total_hutang_lama').val()); 
        }else{
            var total_hutang_lama=0;
        }

        if($('#potong_hutang').val()!=''){
            var potong_hutang=removePeriod($('#potong_hutang').val(),',');
        }else{
            var potong_hutang=0;
        }
        var selisih_uang_jalan=Math.abs(parseFloat(selisih_uang_jalan))+parseFloat(total_hutang_lama);
        var potong_hutang = removePeriod($('#potong_hutang').val(),',');
        if(parseFloat(potong_hutang)>parseFloat(total_hutang)){
            $('#potong_hutang').val(addPeriodType(total_hutang,','));
        }
        else{
            $('#potong_hutang').val(addPeriodType(potong_hutang,','));
        }
        //kalau hutang misal 500k dan uang jalannya 300k, jadi maks pencairan yang 300k bukan 500k,
        //karena kalau 500k nanti jadi minus, kalau 300k, berarti gak tf sama sekali cuman potong hutang, gausah milih kas bank
        if(parseFloat(potong_hutang)>parseFloat(selisih_uang_jalan)+parseFloat(total_hutang_lama) && parseFloat(total_hutang)>parseFloat(selisih_uang_jalan)+parseFloat(total_hutang_lama)){
            $('#potong_hutang').val(addPeriodType(selisih_uang_jalan,','));
        }
            
    }
    var max_uang_jalan_tl = 1000000;
    var dataTelukLamong =  <?php echo json_encode($dataPengaturanKeuangan); ?>;
    //get data
    get_tujuan( $('#customer_id').val());
    check_data();
    var today = new Date();
    $('#tanggal_pencairan').datepicker({
        autoclose: true,
        format: "dd-M-yyyy",
        todayHighlight: true,
        language: 'en',
        // startDate: today,
    }).datepicker("setDate", today);
    
    
    $(document).on('keyup', '#potong_hutang', function(){ 
        hitung_total();
        cek_potongan_hutang();
        // check();
    });
    // $(document).on('focusout', '#selisih_uang_jalan', function(){ 
    //     check();
    // });
    $('body').on('change','#select_customer',function()
    {
        var selected_cust = $(this).find('option:selected');
        var nama_cust=selected_cust.attr('nama_cust');
        var kode_cust=selected_cust.attr('kode_cust');

        var selectedValue = $(this).val();
        $('#customer_id').val(selectedValue);
        $('#customer_nama_baru').val(`[${kode_cust}] ${nama_cust}`);
        $('#customer_kode_baru').val(kode_cust);
        
        get_tujuan(selectedValue);
        check();
        // $('#uang_jalan_baru').val('')
        // $('#selisih_uang_jalan').val('');
        // $('#total_akhir').val('');
        // $('#tarif_baru').val('');
    });
    $('body').on('change','#select_grup_tujuan',function(){
        var selectedValue = $(this).val();
        get_tujuan_biaya(selectedValue);
        check();

    });
    $('body').on('change','#stack_tl',function()
    {
        var selectedOption = $(this).val();
        var uang_jalan_actual = parseFloat(escapeComma($('#total_uang_jalan_lama').val()));
        var uang_jalan_hidden = parseFloat(escapeComma($('#uang_jalan').val()));
        var cekNan_uj= !isNaN(uang_jalan_hidden)?uang_jalan_hidden:0;
        var uang_jalan_sum_tl=0;
        var selisih_akhir = 0;
        // check();
        if(cekNan_uj != 0 )
        {
          
            if(selectedOption=='tl_teluk_lamong'&& $('#uang_jalan').val()<1000000)
            {
                uang_jalan_sum_tl = cekNan_uj+dataTelukLamong.tl_teluk_lamong;
                selisih_akhir = uang_jalan_actual - uang_jalan_sum_tl;
                $('#stack_teluk_lamong_hidden').val(dataTelukLamong.tl_teluk_lamong);
                $('#uang_jalan_baru').val(moneyMask(uang_jalan_sum_tl));
                $('#selisih_uang_jalan').val(moneyMask(selisih_akhir));
                $('#total_akhir').val(moneyMask( Math.abs(selisih_akhir)));
                check();
                hitung_total();
                cek_potongan_hutang();
            }
            else
            {
                uang_jalan_sum_tl = cekNan_uj;
                selisih_akhir = uang_jalan_actual - uang_jalan_sum_tl;
                $('#stack_teluk_lamong_hidden').val(0);
                $('#uang_jalan_baru').val(moneyMask(cekNan_uj));
                $('#selisih_uang_jalan').val(moneyMask(selisih_akhir));
                $('#total_akhir').val(moneyMask( Math.abs(selisih_akhir)));
                check();
                hitung_total();
                cek_potongan_hutang();
            }

        }
        else
        {
            if(selectedOption=='tl_teluk_lamong'&& $('#uang_jalan').val()<1000000)
            {
                $('#stack_teluk_lamong_hidden').val(dataTelukLamong.tl_teluk_lamong);
                $('#uang_jalan_baru').val(moneyMask(dataTelukLamong.tl_teluk_lamong));
                check();
                hitung_total();
                cek_potongan_hutang();
            }
            else
            {
                $('#stack_teluk_lamong_hidden').val(0);
                $('#uang_jalan_baru').val('');
                check();
                hitung_total();
                cek_potongan_hutang();
            }
        }

    });
  
    function get_tujuan( id_customer)
    {
        var baseUrl = "{{ asset('') }}";

        //hadle booking bug
        $('#tujuan_id').val('');
        $('#nama_tujuan').val('');
        $('#alamat_tujuan').val('');
        $('#tarif').val('');
        $('#uang_jalan').val('');
        $('#komisi').val('');
        $('#komisi_driver').val('');
        $('#jenis_tujuan').val('');
        //ltl
        $('#harga_per_kg').val('');
        $('#min_muatan').val('');
        $('#plastik').val('');
        $('#tally').val('');
        $('#kargo').val('');
        $('#biayaDetail').val('');
        $('#uang_jalan_baru').val('')
        $('#selisih_uang_jalan').val('');
        $('#total_akhir').val('');
        $('#tarif_baru').val('');
        var select_grup_tujuan = $('#select_grup_tujuan');
        // hitungTarif();
        $.ajax({
            url: `${baseUrl}truck_order/getTujuanCust/${id_customer}`, 
            method: 'GET', 
            success: function(response) {
                if(response)
                {
                    select_grup_tujuan.empty(); 
                    select_grup_tujuan.append('<option value="">Pilih Tujuan</option>');
                    if(id_customer!="")
                    {
                        response.dataTujuan.forEach(tujuan => {
                            var option = document.createElement('option');
                            option.value = tujuan.id;
                            option.textContent = tujuan.nama_tujuan+ ` ( ${tujuan.jenis_tujuan} )` +  ` [${tujuan.getMarketing?tujuan.getMarketing.nama:'-'} ]`;
                            // if(idTujuan!=''|| idTujuan!='[]'|| idTujuan!=null)
                            // {
                            //     if (idTujuan == tujuan.id) {
                            //         option.selected = true;
                            //     }

                            // }
                            if (tujuan.jenis_tujuan == "LTL"||tujuan.jenis_tujuan=='') {
                                // option.dis('disabled', true);
                                option.disabled = true;
                            }
                            select_grup_tujuan.append(option);
                        });
                    }

                }
                else
                {
                        select_grup_tujuan.empty(); 
                        select_grup_tujuan.append('<option value="">Pilih Tujuan</option>');
                }

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    function get_tujuan_biaya (id_tujuan)
    {
        var stack_tl = $('#stack_tl').val();

        var baseUrl = "{{ asset('') }}";

        //hadle booking bug
        var array_detail_biaya = [];
        // hitungTarif();
        $.ajax({
            url: `${baseUrl}truck_order/getTujuanBiaya/${id_tujuan}`, 
            method: 'GET', 
            success: function(response) {
                // console.log(response.dataTujuan);

                if(!response.dataTujuan)
                {
                    $('#tujuan_id').val('');
                    $('#nama_tujuan').val('');
                    $('#alamat_tujuan').val('');
                    $('#tarif').val('');
                    $('#uang_jalan').val('');
                    $('#uang_jalan_baru').val('');
                    $('#tarif_baru').val('');
                    $('#komisi').val('');
                    $('#komisi_driver').val('');
                    $('#jenis_tujuan').val('');
                    //ltl
                    $('#harga_per_kg').val('');
                    $('#min_muatan').val('');
                    $('#plastik').val('');
                    $('#tally').val('');
                    $('#kargo').val('');
                    $('#biayaDetail').val('');
                    check();
                    check_data();
                    hitung_total();
                    cek_potongan_hutang();
                    
                    array_detail_biaya = []
                }
                else
                {
                    $('#tujuan_id').val(response.dataTujuan.id);
                    $('#nama_tujuan').val(response.dataTujuan.nama_tujuan);
                    $('#alamat_tujuan').val(response.dataTujuan.alamat);
                    $('#tarif').val(response.dataTujuan.tarif);
                    $('#uang_jalan').val(response.dataTujuan.uang_jalan);
                    if(stack_tl=='tl_teluk_lamong'&&response.dataTujuan.uang_jalan<max_uang_jalan_tl)
                    {
                        $('#uang_jalan_baru').val(moneyMask(response.dataTujuan.uang_jalan + dataTelukLamong.tl_teluk_lamong));
                    }
                    else
                    {
                        $('#uang_jalan_baru').val(moneyMask(response.dataTujuan.uang_jalan));
                    }

                    $('#tarif_baru').val(moneyMask(response.dataTujuan.tarif));
                    $('#komisi').val(response.dataTujuan.komisi);
                    $('#komisi_driver').val(response.dataTujuan.komisi_driver);

                    $('#jenis_tujuan').val(response.dataTujuan.jenis_tujuan);
                    //ltl
                    $('#harga_per_kg').val(response.dataTujuan.harga_per_kg);
                    $('#min_muatan').val(response.dataTujuan.min_muatan);
                    $('#kargo').val(response.dataTujuan.kargo);
                    var dataBiaya = response.dataTujuanBiaya;
                    for (var i in dataBiaya) {
                        var obj = {
                            deskripsi: dataBiaya[i].deskripsi,
                            biaya: dataBiaya[i].biaya,
                            catatan: dataBiaya[i].catatan,
                        };
                        array_detail_biaya.push(obj);
                    }
                    $('#plastik').val(response.dataTujuan.plastik);
                    $('#tally').val(response.dataTujuan.tally);
                    $('#biayaDetail').val(JSON.stringify(array_detail_biaya));
                    // hitungTarif();
                    check();
                    check_data();
                    hitung_total();
                    cek_potongan_hutang();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    function check(){
        var total_uang_jalan = parseFloat(escapeComma($('#total_uang_jalan_lama').val()));
        var selisih_uang_jalan = parseFloat(escapeComma($('#selisih_uang_jalan').val()));

        var uang_jalan_baru = parseFloat(escapeComma($('#uang_jalan_baru').val()));
        var cekNan_uj_baru= !isNaN(uang_jalan_baru)?uang_jalan_baru:0
        console.log(cekNan_uj_baru);
        if(cekNan_uj_baru != 0 )
        {
            console.log('masuk sini');
            const selisih_akhir = total_uang_jalan-cekNan_uj_baru;
            $('#selisih_uang_jalan').val(moneyMask(selisih_akhir));
            if(selisih_akhir<0)
            {
                $('#total_akhir_label').text('Total Diberikan');
                $('#total_akhir').val(moneyMask( Math.abs(selisih_akhir)));
                $('#jenis_masuk').val('pencairan');
                $('#pembayaran').val('1').trigger('change');

            }
            else if(selisih_akhir>0)
            {
                $('#total_akhir_label').text('Total kembali');
                $('#total_akhir').val(moneyMask( Math.abs(selisih_akhir)));
                $('#jenis_masuk').val('');
                $('#jenis_masuk').val('masuk_hutang');
                $('#pembayaran').val('HUTANG').trigger('change');

            }
            else
            {
                $('#total_akhir_label').text('Tidak ada pencairan');
                $('#total_akhir').val(moneyMask( Math.abs(selisih_akhir)));
                $('#jenis_masuk').val('tidak_ada_transaksi');
                $('#pembayaran').val('TIDAK_ADA_TRANSAKSI').trigger('change');

            }
        }
        else
        {
            $('#uang_jalan_baru').val('')
            $('#selisih_uang_jalan').val('');
            $('#total_akhir').val('');
            $('#total_akhir_label').text('Total Diberikan');
            $('#jenis_masuk').val('pencairan');
            $('#pembayaran').val('1').trigger('change');
        }
        // if(selisih_uang_jalan > total_uang_jalan){
        //     $('#selisih_uang_jalan').val(moneyMask(total_uang_jalan));
        // }
    }
    function check_data()
    {
        console.log($('#tujuan_id').val()=='');
        if (!$('#tujuan_id').val()) {
            $('#potong_hutang').attr('disabled',true);
        }
        else
        {
            if (normalize($('#total_hutang').val())<=0) {
                $('#potong_hutang').attr('disabled',true);
            }
            else
            {
                $('#potong_hutang').attr('disabled',false);
            }
        }
    }
   
});
</script>

@endsection


