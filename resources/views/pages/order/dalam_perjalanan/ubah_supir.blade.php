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
    <form action="{{ route('dalam_perjalanan.save_ubah_supir', ['id' => $data['id_sewa']]) }}" method="POST" id="post_data">
        @csrf 
        {{-- @method('POST') --}}
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('dalam_perjalanan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12" style=" border-right: 1px solid rgb(172, 172, 172);">
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
                        <div class="form-group ">
                            <label for="no_akun">Customer</label>
                            <input type="text" id="customer" name="customer" class="form-control" value="[{{$data->getCustomer->kode}}] {{$data->getCustomer->nama}}" readonly>                         
                            <input type="hidden" id="jenis_order" name="jenis_order" value="{{$data->jenis_order}}" placeholder="jenis_order">
                        
                        </div>  
                        <div class="form-group ">
                            <label for="no_akun">Tujuan</label>
                            <input type="text" id="tujuan" name="tujuan" class="form-control" value="{{$data->nama_tujuan}}" readonly>                         
                            <input type="hidden" id="tujuan_id" name="tujuan_id" value="{{$data->id_grup_tujuan}}" placeholder="tujuan_id">
                            <input type="hidden" name="id_jo_detail" id="id_jo_detail" value="{{$data->id_jo_detail}}" placeholder="id_jo_detail">
                            <input type="hidden" name="id_jo" id="id_jo" value="{{$data->id_jo}}" placeholder="id_jo">
                            <input type="hidden" id="nama_tujuan" name="nama_tujuan" value="{{$data->nama_tujuan}}"placeholder="nama_tujuan">
                            <input type="hidden" id="alamat_tujuan" name="alamat_tujuan" value="{{$data->alamat_tujuan}}"placeholder="alamat_tujuan">
                            <input type="hidden" id="tarif" name="tarif" value="{{$data->total_tarif}}"placeholder="tarif">
                            <input type="hidden" id="uang_jalan" name="uang_jalan" value="{{$data->total_uang_jalan}}"placeholder="uang_jalan">
                            <input type="hidden" id="jenis_tujuan" name="jenis_tujuan" value="{{$data->jenis_tujuan}}"placeholder="jenis_tujuan">
                        </div>  
                        
                        <div class="form-group " id="driver_div">
                            <label for="select_driver">Driver<span style="color:red">*</span></label>
                                <select class="form-control select2" style="width: 100%;" id='select_driver' name="select_driver" required {{ $data['status']== 'PROSES DOORING'&&$data['jenis_order']=="INBOUND" || $data['status']== 'PROSES DOORING'&&$data['jenis_order']=="OUTBOUND"?'readonly':''}}>
                                <option value="">Pilih Driver</option>
                                @foreach ($dataDriver as $drvr)
                                    <option value="{{$drvr->id}}" 
                                        nama_driver="{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})"
                                        karyawan_hutang = "{{$drvr->total_hutang}}"
                                        potong_hutang = "{{$drvr->potong_hutang}}"
                                        {{$drvr->id==$data['id_karyawan']? 'selected':''}}>{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})</option>
                                @endforeach
                            </select>
                            
                            <input type="hidden" id="driver_nama" name="driver_nama" value="{{$data->nama_driver}}" placeholder="driver_nama">
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-3 col-md-6 col-sm-12" id="kontainer_div">
                                <div class="form-group" id="inboundDataKontainer">
                                    <label for="">Tipe Kontainer<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" id="tipe_kontainer_in" placeholder="" readonly="" value="{{$data['tipe_kontainer']}}">    
                                    {{-- <input type="hidden" id="status" value=""> --}}
                                </div>
                                <input type="hidden" name="tipe_kontainer" id="tipe_kontainer" value="{{$data['tipe_kontainer']}}">
                            </div> 
                            <div class="form-group col-lg-4 col-md-6 col-sm-12" id="kendaraan_div">
                                <label for="select_kendaraan">Kendaraan<span style="color:red">*</span></label>
                                <select class="form-control select2" style="width: 100%;" id='select_kendaraan' name="select_kendaraan" {{ $data['status']== 'PROSES DOORING'? 'readonly':'' }}>
                                    <option value="">Pilih Kendaraan</option>

                                    @foreach ($dataKendaraan as $kendaraan)
                                    
                                        <option value="{{$kendaraan->kendaraanId}}"
                                            idChassis='{{$kendaraan->chassisId}}'
                                            noPol='{{$kendaraan->no_polisi}}'
                                            idDriver='{{$kendaraan->driver_id}}'
                                            kategoriKendaraan='{{$kendaraan->kategoriKendaraan}}'
                                            tipeKontainerKendaraanDariChassis = '{{$kendaraan->tipeKontainerKendaraanDariChassis}}'
                                            {{$kendaraan->kendaraanId == $data['id_kendaraan']? 'selected':''}}
                                            >{{ $kendaraan->no_polisi }} ({{$kendaraan->kategoriKendaraan}})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="kendaraan_id" name="kendaraan_id" value="{{$data->id_kendaraan}}" placeholder="kendaraan_id">
                                <input type="hidden" id="no_polisi" name="no_polisi" value="{{$data->no_polisi}}" placeholder="no_polisi">
                                <input type="hidden" id="tipeKontainerKendaraanDariChassis" name="tipeKontainerKendaraanDariChassis" value="" placeholder="tipeKontainerKendaraanDariChassis">
                            </div>   
                            <div class="form-group col-lg-5 col-md-6 col-sm-12" id="chassis_div">
                                <label for="select_ekor">Chassis<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_chassis' name="select_chassis" {{ $data['status']== 'PROSES DOORING'&&$data['jenis_order']=="INBOUND" || $data['status']== 'PROSES DOORING'&&$data['jenis_order']=="OUTBOUND"?'readonly':''}}>
                                    <option value="">Pilih Chassis</option>
                                    @foreach ($dataChassis as $cha)
                                        <option value="{{$cha->idChassis}}" modelChassis="{{ $cha->modelChassis }}" karoseris="{{ $cha->karoseri }}" {{$cha->idChassis==$data['id_chassis']? 'selected':''}}>{{ $cha->kode }} - {{ $cha->karoseri }} ({{$cha->modelChassis}})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="karoseri" name="karoseri" value="{{$data->karoseri}}" placeholder="karoseri">
                            </div>
                        </div>
                    </div>
                    @if ($data['jenis_tujuan'] == 'FTL' )
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="total_hutang" >Total Hutang</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_hutang" name="total_hutang" class="form-control uang numaja" value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12 mb-0" 
                                    @if (isset($data->getKaryawan->getHutang) && $data->getKaryawan->getHutang->total_hutang > 0)
                                        style="background: hsl(0, 100%, 93%); border: 1px red solid;"
                                    @endif>
                                    <label for="potong_hutang"><span class="text-red">Potong Hutang</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" onkeyup="cek_potongan_hutang();hitung_total();" maxlength="100" id="potong_hutang" name="potong_hutang" class="form-control uang numaja" value="" >                         
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                {{-- @if ($data->jenis_tujuan =='FTL') --}}
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="total_uang_jalan">Total Uang Jalan</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input readonly="" value="{{ number_format($data['total_uang_jalan'] + $dataUangJalanRiwayat->total_tl) }}" type="text" name="total_uang_jalan" class="form-control numaja uang" id="total_uang_jalan" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="total_diterima">Total Diberikan</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang " value="" readonly>                         
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="">Kas / Bank<span class="text-red">*</span></label>
                                        <select class="form-control select2" name="pembayaran" id="pembayaran" {{$dataUangJalanRiwayat->kas_bank_id? 'disabled':''}} >
                                            @foreach ($dataKas as $kb)
                                                <option value="{{$kb->id}}" {{ $dataUangJalanRiwayat->kas_bank_id==$kb->id ? 'selected':''; }} >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                            @endforeach
                                                {{-- <option value="HUTANG KARYAWAN">HUTANG KARYAWAN</option> --}}
                                        </select>
                                        <input type="hidden" name="pembayaran_defaulth" value="{{$dataUangJalanRiwayat->kas_bank_id}}">
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="catatan">Catatan</label>
                                        <input type="text" name="catatan" class="form-control" id="catatan" placeholder="" value="">
                                        <input type="hidden" name="catatan_awal" id="catatan_awal" value=""> 
                                    </div>
                                {{-- @endif --}}
                            </div>
                        </div>
                    @else
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="total_hutang" >Total Hutang</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_hutang" name="total_hutang" class="form-control uang numaja" value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12 mb-0" 
                                    @if (isset($data->getKaryawan->getHutang) && $data->getKaryawan->getHutang->total_hutang > 0)
                                        style="background: hsl(0, 100%, 93%); border: 1px red solid;"
                                    @endif>
                                    <label for="potong_hutang"><span class="text-red">Potong Hutang</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" onkeyup="cek_potongan_hutang();hitung_total();" maxlength="100" id="potong_hutang" name="potong_hutang" class="form-control uang numaja" value="" >                         
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="total_uang_jalan">Total Uang Jalan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        @php
                                            $tl = isset($dataUangJalanRiwayat)? $dataUangJalanRiwayat->total_tl:0;
                                        @endphp
                                        <input readonly="" value="{{ number_format($data['total_uang_jalan'] + $tl) }}" type="text" name="total_uang_jalan" class="form-control numaja uang" id="total_uang_jalan" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="total_diterima">Total Diberikan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang " value="" readonly>                         
                                    </div>
                                </div>
                                @isset($dataUangJalanRiwayat)
                                    <div class="form-group col-12">
                                        <label for="">Kas / Bank<span class="text-red">*</span></label>
                                        <select class="form-control select2" name="pembayaran" id="pembayaran" {{$dataUangJalanRiwayat->kas_bank_id? 'disabled':''}} >
                                            @foreach ($dataKas as $kb)
                                                <option value="{{$kb->id}}" {{ $dataUangJalanRiwayat->kas_bank_id==$kb->id ? 'selected':''; }} >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                            @endforeach
                                                {{-- <option value="HUTANG KARYAWAN">HUTANG KARYAWAN</option> --}}
                                        </select>
                                        <input type="hidden" name="pembayaran_defaulth" value="{{$dataUangJalanRiwayat->kas_bank_id}}">
                                    </div>
                                @endisset
                                <div class="form-group col-12">
                                    <label for="catatan">Catatan</label>
                                    <input type="text" name="catatan" class="form-control" id="catatan" placeholder="" value="">
                                    <input type="hidden" name="catatan_awal" id="catatan_awal" value=""> 
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div> 
    </form>
</div>
<script type="text/javascript">
    function hitung_total(){
        if($('#total_uang_jalan').val()!=''){
            var total_uang_jalan=removePeriod($('#total_uang_jalan').val(),',');
        }else{
            var total_uang_jalan=0;
        }
        
        if($('#potong_hutang').val()!=''){
            var potong_hutang=removePeriod($('#potong_hutang').val(),',');
        }else{
            var potong_hutang=0;
        }
        
        var total_diterima=parseFloat(total_uang_jalan)-parseFloat(potong_hutang);
        // console.table(total_uang_jalan);
        // console.table(potong_hutang);
        // console.table(total_diterima);
        if(total_diterima!=0){
            $('#total_diterima').val(addPeriod(total_diterima,','));
        }else{
            $('#total_diterima').val(0);
        }

        
    }
    function cek_potongan_hutang(){
         if($('#total_uang_jalan').val()!=''){
            var total_uang_jalan=removePeriod($('#total_uang_jalan').val(),',');
        }else{
            var total_uang_jalan=0;
        }
        if($('#total_hutang').val()!=''){
            var total_hutang =removePeriod($('#total_hutang').val(),',');
        }else{
            var total_hutang =0;
        }
        
        var potong_hutang = removePeriod($('#potong_hutang').val(),',');
        if(parseFloat(potong_hutang)>parseFloat(total_hutang)){
            $('#potong_hutang').val(addPeriod(total_hutang,','));
        }else{
            $('#potong_hutang').val(addPeriod(potong_hutang,','));
        }
       
        if(parseFloat(potong_hutang)>parseFloat(total_uang_jalan) && parseFloat(total_hutang)>parseFloat(total_uang_jalan)){
                $('#potong_hutang').val(addPeriodType(total_uang_jalan,','));
            }
    }
$(document).ready(function() {
    
   
    
    
    function selectKendaraan()
    {
        var idKendaraan = $('#select_kendaraan').val();
        var selectedOption = $('#select_kendaraan').find('option:selected');
        var idChassis = selectedOption.attr('idChassis');
        var nopol = selectedOption.attr('noPol');
        var supir = selectedOption.attr('idDriver');
        // console.log(idKendaraan);
        if(idKendaraan != '')
        {
            var tipeKontainerKendaraanDariChassis = selectedOption.attr('tipeKontainerKendaraanDariChassis').replace(/'/g, '');
        }
        $('#kendaraan_id').val(idKendaraan);
        $('#no_polisi').val(nopol);
        $('#tipeKontainerKendaraanDariChassis').val(tipeKontainerKendaraanDariChassis);
        if (idChassis!=''&&idChassis!='null') {
            $('#select_chassis').val(idChassis).trigger('change');
        }
        else
        {
            $('#select_chassis').val('').trigger('change');
        }
        if(supir!='')
        {
            $('#select_driver').val(supir).trigger('change');
        }
        else
        {
            $('#select_driver').val('').trigger('change');
        }
    }
    function selectChasis(){
        var selectedOption = $('#select_chassis').find('option:selected');
            var karoseris = selectedOption.attr('karoseris');
            
            $('#karoseri').val(karoseris);
    }
    function selectDriver()
    {
        var id_karyawan = $('#select_driver').val();
        var selectedOption = $('#select_driver').find('option:selected');
        var karyawan_hutang = selectedOption.attr('karyawan_hutang');
        var potong_hutang = selectedOption.attr('potong_hutang');
        var nama_driver = selectedOption.attr('nama_driver');

        
        $('#total_hutang').val(karyawan_hutang?addPeriod(karyawan_hutang,','):0);
        $('#potong_hutang').val(potong_hutang?addPeriod(potong_hutang,','):0);
        $('#driver_nama').val(nama_driver);
        if(potong_hutang>0)
        {
            $('#potong_hutang').prop('disabled',true);
        }
        else
        {
            $('#potong_hutang').prop('disabled',false);
        }
             hitung_total();
        cek_potongan_hutang();
    }
    function hideMenuTujuan(){
        var jenisTujuan=$('#jenis_tujuan').val();
        var jenisOrder =$('#jenis_order').val();
        var kendaraan_div =$('#kendaraan_div');
        console.log(jenisTujuan);
        // console.log(kendaraan_div);

        // if(jenisOrder=='OUTBOUND')
        // {
            if(jenisTujuan=='FTL' || jenisTujuan=='')
            {
                $('#kontainer_div').show();
                $('#chassis_div').show();
                $('#stack_tl_form').show();
                // kendaraan_div.removeClass('col-lg-12 col-md-12 col-sm-12');
                // kendaraan_div.addClass('col-lg-4 col-md-6 col-sm-12');
            }
            else
            {
                // console.log('masuk else');
                $('#kontainer_div').hide();
                $('#chassis_div').hide();
                $('#stack_tl_form').hide();
                kendaraan_div.removeClass('col-lg-4 col-md-6 col-sm-12');
                kendaraan_div.addClass('col-lg-12 col-md-12 col-sm-12');
            }
        // }
        // else
        // {
        //     $('#kontainer_div').show();
        //     $('#chassis_div').show();
        //     $('#driver_div').show();
        // }
    }
    function setKendaraan(tipeKontainer)
    {
        // console.log(tipeKontainer);
        var kontainerSemua =  <?php echo json_encode($dataKendaraan); ?>;
        var select_kendaraan = $('#select_kendaraan');
        console.log($('#kendaraan_id').val() );
        if(tipeKontainer==''|| tipeKontainer== undefined)
        {
            select_kendaraan.empty(); 
            select_kendaraan.append('<option value="">Pilih Kendaraan</option>');
            
            kontainerSemua.forEach(kendaraan => {
                const option = document.createElement('option');
                option.value = kendaraan.kendaraanId;
                option.setAttribute('idChassis', kendaraan.chassisId);
                option.setAttribute('noPol', kendaraan.no_polisi);
                option.setAttribute('idDriver', kendaraan.driver_id);
                option.setAttribute('kategoriKendaraan', kendaraan.kategoriKendaraan);
                option.setAttribute('tipeKontainerKendaraanDariChassis', kendaraan.tipeKontainerKendaraanDariChassis);
                option.textContent = kendaraan.no_polisi + ` (${kendaraan.kategoriKendaraan})` ;
                select_kendaraan.append(option);
            });
            $('#kendaraan_id').val('');
            $('#no_polisi').val('');
            $('#tipeKontainerKendaraanDariChassis').val('');
            $('#select_driver').val('').trigger('change');
            $('#karoseri').val('');
        }
        else
        {
            var baseUrl = "{{ asset('') }}";
            $.ajax({
                url: `${baseUrl}truck_order/getDataKendaraanByModel/${tipeKontainer}`, 
                method: 'GET', 
                success: function(response) {
                    if(response)
                    {
                        console.log(response);
                        select_kendaraan.empty(); 
                        select_kendaraan.append('<option value="">Pilih Kendaraan</option>');
                        
                            response.forEach(kendaraan => {
                                const option = document.createElement('option');
                                option.value = kendaraan.kendaraanId;
                                option.setAttribute('idChassis', kendaraan.chassisId);
                                option.setAttribute('noPol', kendaraan.no_polisi);
                                option.setAttribute('idDriver', kendaraan.driver_id);
                                option.setAttribute('kategoriKendaraan', kendaraan.kategoriKendaraan);
                                option.setAttribute('tipeKontainerKendaraanDariChassis', kendaraan.tipeKontainerKendaraanDariChassis);

                                option.textContent = kendaraan.no_polisi + ` (${kendaraan.kategoriKendaraan})` ;
                                //kendaraan_id itu yang hidden,tipe kontainer itu buat selected di tipeoutbound
                                if ($('#kendaraan_id').val() == kendaraan.kendaraanId && tipeKontainer == $('#tipeKontainerKendaraanDariChassis').val()) {
                                        option.selected = true;
                                        $('#select_driver').val(kendaraan.driver_id).trigger('change');
                                }
                                //kendaraan_id itu yang hidden,tipe kontainer itu buat selected di tipeoutbound
                                if($('#kendaraan_id').val() != kendaraan.kendaraanId && tipeKontainer != $('#tipeKontainerKendaraanDariChassis').val())
                                {
                                    console.log('masuk else');
                                    $('#kendaraan_id').val('');
                                    $('#no_polisi').val('');
                                    $('#tipeKontainerKendaraanDariChassis').val('');
                                    $('#select_driver').val('').trigger('change');
                                    $('#karoseri').val('');
                                }
                                
                                    select_kendaraan.append(option);
                            });

                    }
        
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        }

    }
    function setChassis(tipeKontainer)
    {
        // console.log(tipeKontainer);

        var chassisSemua =  <?php echo json_encode($dataChassis); ?>;
        var select_chassis= $('#select_chassis');
        var selectedOption = $('#select_kendaraan').find('option:selected');
        var idChassis = selectedOption.attr('idChassis');
        if(tipeKontainer==''|| tipeKontainer== undefined)
        {
            select_chassis.empty(); 
            select_chassis.append('<option value="">Pilih Chassis</option>');
                        
            chassisSemua.forEach(chassis => {
                const option = document.createElement('option');
                option.value = chassis.idChassis;
                option.setAttribute('modelChassis', chassis.modelChassis);
                option.setAttribute('karoseris', chassis.karoseri);
                option.textContent = `${chassis.karoseri} - ${chassis.kode} (${chassis.modelChassis})` ;
                select_chassis.append(option);
            });
            $('#karoseri').val('');

        }
        else
        {
            var baseUrl = "{{ asset('') }}";
            $.ajax({
                url: `${baseUrl}truck_order/getDataChassisByModel/${tipeKontainer}`, 
                method: 'GET', 
                success: function(response) {
                    if(response)
                    {
                        
                        select_chassis.empty(); 
                        select_chassis.append('<option value="">Pilih Chassis</option>');
                        // if(tipeKontainer!=""|| tipeKontainer!= undefined)
                        // {
                            
                                response.forEach(chassis => {
                                const option = document.createElement('option');
                                option.value = chassis.idChassis;
                                option.setAttribute('modelChassis', chassis.modelChassis);
                                option.setAttribute('karoseris', chassis.karoseri);
                                option.textContent = `${chassis.karoseri} - ${chassis.kode} (${chassis.modelChassis})` ;
                                //idChassis itu ambil attribut dari kendaraan
                                if ( idChassis == chassis.idChassis) {
                                        option.selected = true;
                                        $('#karoseri').val(chassis.karoseri);

                                }
                                // if (idChassis != chassis.idChassis)
                                // {
                                //     $('#karoseri').val('');
                                //     select_chassis.val('').trigger('change');
                                // }
                                select_chassis.append(option);
                            });
                        // }
                    }
        
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

    }
   
    $('body').on('change','#select_driver',selectDriver);
    $('body').on('change','#select_kendaraan',selectKendaraan);
    $('body').on('change','#select_chassis',selectChasis);
    if($('#jenis_tujuan').val()=="FTL")
    {
        // setKendaraan($('#tipe_kontainer').val());
        // setChassis($('#tipe_kontainer').val());
    }
    hideMenuTujuan();
    // selectKendaraan();
    // selectChasis();
    selectDriver();
    hitung_total();
    cek_potongan_hutang();
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

@endsection


