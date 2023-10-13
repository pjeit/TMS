
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('booking.index')}}">Customer</a></li>
<li class="breadcrumb-item">Create</li>

@endsection

@section('content')
<style>
    #inbound {
        /* cursor: pointer; */
    }
    #outbound {
        /* cursor: pointer; */
    }
     #inbound:hover,#outbound:hover {
        /* background-color: rgb(196, 223, 255); */
        /* border-block-end: 1px solid #007bff; */
        /* border-block-start: 1px solid #007bff; */
    }
    .aktif {
        background-color: #e0efff;
    }

    /*Select2 ReadOnly Start*/
        select[readonly].select2-hidden-accessible + .select2-container {
            pointer-events: none;
            touch-action: none;
        }
        select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
            background: #eee;
            box-shadow: none;
        }
        select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
            display: none;
        }
    /*Select2 ReadOnly End*/
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
    <form action="{{ route('truck_order.update', ['truck_order' => $data]) }}" method="POST" >
    @method('PUT')
    @csrf
    {{-- <div class="row">
        <div class="col">
  
        </div>
    </div>
    <hr> --}}
        <div class="row">
            <div class="col">
                <div class="card radiusSendiri card-outline card-primary">
                    <div class="card-header">
                        @if ($data['status']=="MENUNGGU UANG JALAN")
                            <a href="{{ route('truck_order.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        @else
                            <a href="{{ route('dalam_perjalanan.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                            
                        @endif
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        {{-- <button type="submit">wet</button> --}}
                    </div>
                    <div class="card-body">
                         <div class="row mb-2">
                            <div class="col-6 text-center radiusSendiri " id="inbound" >
                                <label class="p-1">BONGKAR (INBOUND)</label>
                                <hr style="border: 0.5px solid #007bff; " id="garisInbound">
                            </div>

                            <div class="col-6 text-center radiusSendiri"id="outbound" >
                                <label class=" p-1">MUAT (OUTBOUND)</label>
                                <hr style="border: 0.5px solid #007bff;" id="garisOutbound">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                 <div class="form-group">
                                    <label for="credit_customer">Kredit Customer</label>
                                    <div class="progress">
                                        <div class="progress-bar " role="progressbar" aria-valuenow="" aria-valuemin="100" aria-valuemax="100" name="credit_customer" id="credit_customer" style=""></div>
                                    </div>
                                    <div class="d-flex justify-content-center mt-1">
                                        <span class="rubik-w400-12" id="persenanCredit"></span>
                                    </div>
                                    <input type="hidden" name="sewa_id" id="sewa_id" class="form-control" value="{{$data['id_sewa']}}">
                                    <input type="hidden" name="cred_now" id="cred_now" class="form-control" value="0">
                                    <input type="hidden" name="cred_val" id="cred_val" class="form-control" value="0">
                                    <input type="hidden" name="cred_val_max" id="cred_val_max" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group" id="outboundData">
                                    <label for="">No. Booking</label>
                                    <select class="form-control select2" style="width: 100%;" id='select_booking' name="select_booking" disabled>
                                        <option value="">Pilih No Booking</option>
                                        @foreach ($dataBooking as $book)
                                            <option value="{{$book->idBooking}}-{{$book->id_customer}}-{{$book->id_grup_tujuan}}-{{ \Carbon\Carbon::parse($book->tgl_booking)->format('d-M-Y')}}" {{$book->idBooking==$data['id_booking']? 'selected':''}} >{{ \Carbon\Carbon::parse($book->tgl_booking)->format('d-M-Y') }} / {{ $book->nama_tujuan }} / {{ $book->kode }}</option>
                                        @endforeach
                                    </select>
                                </div>  
                                <div class="form-group" id="inboundData">
                                    <div class="form-group">
                                        <label for="">No. Job Order</label>
                                        <select class="form-control select2" style="width: 100%;" id='select_jo' name="select_jo" disabled>
                                            <option value="">Pilih No JO</option>
                                            @foreach ($datajO as $jo)
                                                <option value="{{$jo->id}}-{{$jo->id_customer}}" {{$jo->id == $data['id_jo']? 'selected':''}}>{{ $jo->no_bl }} / {{ $jo->getCustomer->kode }} / {{ $jo->getSupplier->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>  
                                    <div class="form-group">
                                        <label for="">No. Kontainer</label>
                                        <select class="form-control select2" style="width: 100%;" id='select_jo_detail' name="select_jo_detail" disabled>
                                            @isset($data['id_jo_detail'])
                                                <option value="{{$data->getJOD->id}}-{{$data->getJOD->id_grup_tujuan}}-{{$data->getJOD->no_kontainer}}" selected>{{$data->getJOD->no_kontainer}}</option>
                                            @endisset
                                        </select>
                                        <input type="hidden" name="no_kontainer" id="no_kontainer" value="" placeholder="no_kontainer">
                                    </div> 
                                </div>
                                {{-- <div class="form-group">
                                        <label for="no_sewa">No. Sewa</label>
                                        <input type="text" class="form-control" id="no_sewa" placeholder="Otomatis" readonly="" value="{{$data["no_sewa"]}}">    
                                        <input type="hidden" id="status" value="">
                                </div> --}}
                                <div class="form-group">
                                    <label for="tanggal_berangkat">Tanggal Berangkat<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input {{ $data['status']== 'PROSES DOORING'? 'disabled':'' }} type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="{{old('tanggal_berangkat',\Carbon\Carbon::parse($data['tanggal_berangkat'])->format('d-M-Y')) }}">
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                        <label for="catatan">Catatan</label>
                                        <input type="text" name="catatan" class="form-control" id="catatan" name="catatan" placeholder="" value="{{$data['catatan']}}"> 
                                    </div>
                            </div>
                            <div class="col-6">
                               
                                <div class="form-group">
                                    <label for="select_customer">Customer<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_customer' name="select_customer" {{$data['jenis_order']=="INBOUND" || $data['status']== 'PROSES DOORING'&&$data['jenis_order']=="OUTBOUND"?'readonly':''}} >
                                        <option value="">Pilih Customer</option>
                                        @foreach ($dataCustomer as $cust)                                        
                                            <option value="{{$cust->idCustomer}}" <?= $cust->idCustomer==$data['id_customer']? 'selected':''  ?> > {{ $cust->kodeCustomer }} - {{ $cust->namaCustomer }} / {{ $cust->namaGrup }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" id="customer_id" name="customer_id" value="{{$data['id_customer']}}" placeholder="customer_id">
                                    <input type="text" id="booking_id" name="booking_id" value="" placeholder="booking_id">
                                    <input type="text" id="jenis_order" name="jenis_order" value="{{$data['jenis_order']}}" placeholder="jenis_order">
                                </div>
                                <div class="form-group">
                                    
                                    <label for="select_tujuan">Tujuan<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_grup_tujuan' name="select_grup_tujuan" {{$data['jenis_order']=="INBOUND" || $data['status']== 'PROSES DOORING'&&$data['jenis_order']=="OUTBOUND"?'readonly':''}} >
                                        {{-- @isset($data['id_grup_tujuan'])
                                            <option value="{{$data['id_grup_tujuan']}}">{{$data->getTujuan->nama_tujuan}}</option>
                                        @endisset --}}
                                    </select>

                                     <input type="text" id="tujuan_id" name="tujuan_id" value="{{$data['id_grup_tujuan']}}" placeholder="tujuan_id">
                                    <input type="text" name="id_jo_detail" id="id_jo_detail" value="{{!empty($data['id_jo_detail'])? $data['id_jo_detail']:''}}" placeholder="id_jo_detail">
                                    <input type="text" name="id_jo" id="id_jo" value="{{!empty($data['id_jo'])?$data['id_jo']:''}}" placeholder="id_jo">
                                    <input type="text" id="nama_tujuan" name="nama_tujuan" value=""placeholder="nama_tujuan">
                                    <input type="text" id="alamat_tujuan" name="alamat_tujuan" value=""placeholder="alamat_tujuan">
                                    <input type="text" id="tarif" name="tarif" value=""placeholder="tarif">
                                    <input type="text" id="uang_jalan" name="uang_jalan" value=""placeholder="uang_jalan">
                                    <input type="text" id="komisi" name="komisi" value=""placeholder="komisi">
                                    <input type="text" id="komisi_driver" name="komisi_driver" value=""placeholder="komisi_driver">
                                    <input type="text" id="jenis_tujuan" name="jenis_tujuan" value=""placeholder="jenis_tujuan">
                                    <input type="text" id="harga_per_kg" name="harga_per_kg" value="0"placeholder="harga_per_kg">
                                    <input type="text" id="min_muatan" name="min_muatan" value="0"placeholder="min_muatan">

                                    <input type="text" id="plastik" name="plastik" value=""placeholder="plastik">
                                    <input type="text" id="tally" name="tally" value=""placeholder="tally">
                                    <input type="text" id="kargo" name="kargo" value=""placeholder="kargo">


                                    <input type="text" id="biayaDetail" name="biayaDetail"placeholder="biayaDetail">
                                    {{-- <input type="text" id="biayaTambahSDT" name="biayaTambahSDT"placeholder="biayaTambahSDT"> --}}
                                </div>

                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group" id="inboundDataKontainer">
                                            <label for="">Tipe Kontainer<span class="text-red">*</span></label>
                                            <input type="text" class="form-control" id="tipe_kontainer_in" placeholder="" readonly="" value="{{$data['tipe_kontainer']}}">    
                                            {{-- <input type="hidden" id="status" value=""> --}}
                                        </div>
                                        <div class="form-group" id="outbondDataKontainer">
                                            <label for="">Tipe Kontainer<span class="text-red">*</span></label>
                                            <select class="form-control select2 tipeKontainer" {{ $data['status']== 'PROSES DOORING'? 'readonly':'' }} id="tipe_kontainer_out"  data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                <option value="">── Tipe ──</option>
                                                <option value='20' {{ $data['tipe_kontainer'] == '20'? 'selected':'' }}>20"</option>
                                                <option value='40' {{ $data['tipe_kontainer'] == '40'? 'selected':'' }}>40"</option>
                                            </select>
                                        </div>
                                        <input type="text" name="tipe_kontainer" id="tipe_kontainer" value="{{$data['tipe_kontainer']}}">
                                    </div> 
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
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
                                        <input type="text" id="kendaraan_id" name="kendaraan_id" value="" placeholder="kendaraan_id">
                                        <input type="text" id="no_polisi" name="no_polisi" value="" placeholder="no_polisi">
                                        <input type="text" id="tipeKontainerKendaraanDariChassis" name="tipeKontainerKendaraanDariChassis" value="" placeholder="tipeKontainerKendaraanDariChassis">
                                    </div>   
                                    {{-- <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="select_kendaraan">Kendaraan<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='select_kendaraan' name="select_kendaraan" {{ $data['status']== 'PROSES DOORING'? 'readonly':'' }}>
                                            <option value="">Pilih Kendaraan</option>
                                            @foreach ($dataKendaraan as $kendaraan)
                                                <option value="{{$kendaraan->kendaraanId}}-{{$kendaraan->chassisId}}-{{$kendaraan->no_polisi}}-{{$kendaraan->driver_id}}"  {{$kendaraan->kendaraanId == $data['id_kendaraan']? 'selected':''}}>{{ $kendaraan->no_polisi }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="kendaraan_id" name="kendaraan_id" value="">
                                        <input type="hidden" id="no_polisi" name="no_polisi" value="">
                                    </div> --}}
                                </div>

                                <div class="form-group">
                                    <label for="select_ekor">Chassis<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='select_chassis' name="select_chassis" {{$data['jenis_order']=="INBOUND" || $data['status']== 'PROSES DOORING'&&$data['jenis_order']=="OUTBOUND"?'readonly':''}}>
                                        <option value="">Pilih Chassis</option>

                                        @foreach ($dataChassis as $cha)
                                            <option value="{{$cha->idChassis}}" modelChassis="{{ $cha->modelChassis }}" karoseris="{{ $cha->karoseri }}" {{$cha->id==$data['id_chassis']? 'selected':''}}>{{ $cha->kode }} - {{ $cha->karoseri }} ({{$cha->modelChassis}})</option>
                                        @endforeach
                                    </select>
                                    <input type="text" id="karoseri" name="karoseri" value="" placeholder="karoseri">

                                </div>
                                <div class="form-group">
                                    <label for="select_driver">Driver<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='select_driver' name="select_driver" required {{$data['jenis_order']=="INBOUND" || $data['status']== 'PROSES DOORING'&&$data['jenis_order']=="OUTBOUND"?'readonly':''}}>
                                        <option value="">Pilih Driver</option>
                                        @foreach ($dataDriver as $drvr)
                                            <option value="{{$drvr->id}}" nama_driver="{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})" {{$drvr->id==$data['id_karyawan']? 'selected':''}}>{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})</option>
                                        @endforeach
                                    </select>
                                    <input type="text" id="driver_nama" name="driver_nama" value="" placeholder="driver_nama">
                                </div>
                              
                                {{-- <div class="form-group">
                                    <label for="select_ekor">Chassis<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='select_chassis' name="select_chassis" {{ $data['status']== 'PROSES DOORING'? 'readonly':'' }} >
                                        <option value="">Pilih Chassis</option>

                                        @foreach ($dataChassis as $cha)
                                            <option value="{{$cha->id}}" {{$cha->id==$data['id_chassis']? 'selected':''}}>{{ $cha->kode }} - {{ $cha->karoseri }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="ekor_id" name="ekor_id" value="">
                                    <input type="hidden" id="karoseri" name="karoseri" value="">

                                </div>
                                <div class="form-group">
                                    <label for="select_driver">Driver<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='select_driver' name="select_driver" {{ $data['status']== 'PROSES DOORING'? 'readonly':'' }} >
                                        <option value="">Pilih Driver</option>

                                        @foreach ($dataDriver as $drvr)
                                            <option value="{{$drvr->id}}" {{$drvr->id==$data['id_karyawan']? 'selected':''}}>{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="driver_nama" name="driver_nama" value="">
                                </div> --}}
                                <div class="form-group">
                                    <label for="select_driver">Stack TL</label>
                                        <select class="form-control select2" style="width: 100%;" id='stack_tl' name="stack_tl">
                                        <option value="">Pilih TL</option>
                                        {{-- <option value="tl_perak" {{ isset($checkTL)? ($checkTL['catatan'] == 'tl_perak'? 'selected':''):'' }}>Perak</option>
                                        <option value="tl_priuk" {{ isset($checkTL)? ($checkTL['catatan'] == 'tl_priuk'? 'selected':''):'' }}>Priuk</option>
                                        <option value="tl_teluk_lamong" {{ isset($checkTL)? ($checkTL['catatan'] == 'tl_teluk_lamong'? 'selected':''):'' }}>Teluk Lamong</option> --}}
                                        <option value="tl_perak" {{ $data['stack_tl'] == 'tl_perak'? 'selected':'' }}>Perak</option>
                                        <option value="tl_priuk" {{ $data['stack_tl'] == 'tl_priuk'? 'selected':'' }}>Priuk</option>
                                        <option value="tl_teluk_lamong" {{ $data['stack_tl'] == 'tl_teluk_lamong'? 'selected':'' }}>Teluk Lamong</option>
                                    </select>
                                    <input type="text" id="stack_teluk_lamong_hidden" name="stack_teluk_lamong_hidden" value="" placeholder="stack_teluk_lamong_hidden">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        getDate();
        var jenis = $('#jenis_order').val();

        if(jenis == 'INBOUND'){
            // $("#inbound").addClass("aktif");
            $("#outbound").removeClass("aktif");
            $("#outbound").hide();
            $('#inboundDataKontainer').show();
            $('#outbondDataKontainer').hide();
            $("#inbound").removeClass("col-6");
            $("#inbound").addClass("col-12");
            $('#inboundData').show();
            $('#garisInbound').show();
            $('#outboundData').hide();
            $('#garisOutbound').hide();
        } else {
            $("#inbound").removeClass("aktif");
             $('#inboundDataKontainer').hide();
            $('#outbondDataKontainer').show();

            $("#inbound").hide();
            $("#outbound").removeClass("col-6");
            $("#outbound").addClass("col-12");
            // $("#outbound").addClass("aktif");
            $('#inboundData').hide();
            $('#garisInbound').hide();
            $('#outboundData').show();
            $('#garisOutbound').show();
        }
        
        $('body').on('change','#select_booking',function()
		{
            var selectedValue = $(this).val();
            var splitValue = selectedValue.split('-');
            var booking_id=splitValue[0];
            var idCustomer=splitValue[1];
            var idTujuan=splitValue[2];
            var tanggalBerangkat=splitValue[3];
            var bulanBerangkat=splitValue[4];
            var tahunBerangkat=splitValue[5];
            var gabungan = tanggalBerangkat+"-"+bulanBerangkat+"-"+tahunBerangkat
            $('#select_customer').val(idCustomer).trigger('change');
            $('#select_grup_tujuan').val(idTujuan).trigger('change');
            $('#booking_id').val(booking_id).trigger('change');
            $('#select_customer').attr('disabled',true);
            $('#select_grup_tujuan').attr('disabled',true);
            // $('#tanggal_berangkat').val(gabungan);
            if(selectedValue=="")
            {
              $('#select_customer').attr('disabled',false).val('').trigger('change');
              $('#select_grup_tujuan').attr('disabled',false).val('').trigger('change');
            }
		});

        var  customerLoad = false;
        $('body').on('change','#select_customer',function()
		{
            var selectedValue = $(this).val();
            $('#customer_id').val(selectedValue);
            var baseUrl = "{{ asset('') }}";

            //hadle booking bug
            var selectBooking = $('#select_booking').val();
            var splitValue = selectBooking.split('-');
            var idTujuan=splitValue[2];
            
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
            console.log(selectedValue);
            var select_grup_tujuan = $('#select_grup_tujuan');

            $.ajax({
                url: `${baseUrl}truck_order/getTujuanCust/${selectedValue}`, 
                method: 'GET', 
                success: function(response) {
                    if(response)
                    {
                        customerLoad = true;

                        // ==============================kredit=================
           
                        let creds_now = (response.dataKredit.kreditCustomer/response.dataKredit.maxGrup) * 100;
                        creds_now = creds_now.toFixed(1);
                        // persenanCredit
                        const persen = document.getElementById('persenanCredit');

                        const cred = document.getElementById('credit_customer');
                        if(creds_now<80)
                        {
                            persen.innerHTML = creds_now+"%";
                            cred.style.width = creds_now+"%";
                            cred.style.backgroundColor = "#53de02";
                            cred.style.color = "black";
                            
                        }
                        else if(creds_now >=80 && creds_now <= 90)
                        {
                            persen.innerHTML = creds_now+"%";
                            cred.style.width = creds_now+"%";
                            cred.style.backgroundColor = "#deab02";
                            cred.style.color = "black";
                        }
                        else if(creds_now>=90)
                        {
                            persen.innerHTML = creds_now+"%";
                            cred.style.width = creds_now+"%";
                            cred.style.backgroundColor = "#de0202";
                            cred.style.color = "black";
                        }
                        else if(creds_now>100)
                        {
                            persen.innerHTML = creds_now+"%";
                            cred.style.width = "100%";
                            cred.style.backgroundColor = "#de0202";
                            cred.style.color = "black";
                        }
                        // ==============================kredit=================


                        select_grup_tujuan.empty(); 
                        select_grup_tujuan.append('<option value="">Pilih Tujuan</option>');
                        if(selectedValue!="")
                        {
                            response.dataTujuan.forEach(tujuan => {
                                const option = document.createElement('option');
                                option.value = tujuan.id;
                                option.textContent = tujuan.nama_tujuan;
                                if(idTujuan!=''|| idTujuan!='[]'|| idTujuan!=null)
                                {
                                    if (idTujuan == tujuan.id) {
                                        option.selected = true;
                                    }
    
                                }
                                 select_grup_tujuan.append(option);
                            });
                        }
                        // else
                        // {
                        //       select_grup_tujuan.empty(); 
                        //       select_grup_tujuan.append('<option value="">Pilih Tujuan</option>');
                        // }

                    }
                    else
                    {

                            customerLoad = false;
                            const persen = document.getElementById('persenanCredit');
                            const cred = document.getElementById('credit_customer');
                            persen.innerHTML = 0+"%";
                            cred.style.width = 0+"%";
                            cred.style.backgroundColor = "#53de02";
                            cred.style.color = "black";
                            select_grup_tujuan.empty(); 
                            select_grup_tujuan.append('<option value="">Pilih Tujuan</option>');
                    }
                    // jo_detail.trigger('change');
        
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
           


		});
        $('body').on('change','#select_grup_tujuan',function(){
            var selectedValue = $(this).val();
            var baseUrl = "{{ asset('') }}";

            //hadle booking bug
            var selectBooking = $('#select_booking').val();
            var splitValue = selectBooking.split('-');
            var idTujuan=splitValue[2];
            var array_detail_biaya = [];
            $.ajax({
                url: `${baseUrl}truck_order/getTujuanBiaya/${idTujuan??selectedValue}`, 
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

                        array_detail_biaya = []

                    }
                    else
                    {
                        $('#tujuan_id').val(response.dataTujuan.id);
                       

                        // JSON.stringify(array_detail_biaya)

                        $('#nama_tujuan').val(response.dataTujuan.nama_tujuan);
                        $('#alamat_tujuan').val(response.dataTujuan.alamat);
                        //   if(response.dataTujuan.jenis_tujuan =="LTL")
                        // {
                        //      $('#tarif').val(response.dataTujuan.min_muatan*response.dataTujuan.harga_per_kg );
                        // }
                        $('#tarif').val(response.dataTujuan.tarif);
                        $('#uang_jalan').val(response.dataTujuan.uang_jalan);
                        $('#komisi').val(response.dataTujuan.komisi);
                        $('#komisi_driver').val(response.dataTujuan.komisi_driver);

                        $('#jenis_tujuan').val(response.dataTujuan.jenis_tujuan);
                        //ltl
                        $('#harga_per_kg').val(response.dataTujuan.harga_per_kg);
                        $('#min_muatan').val(response.dataTujuan.min_muatan);
                     
                        $('#kargo').val(response.dataTujuan.kargo);

                         // console.log( response.dataTujuanBiaya);
                        var dataBiaya = response.dataTujuanBiaya;
                        for (var i in dataBiaya) {
                            // if(dataBiaya[i].deskripsi!= 'TL')
                            // {
                                var obj = {
                                    deskripsi: dataBiaya[i].deskripsi,
                                    biaya: dataBiaya[i].biaya,
                                    catatan: dataBiaya[i].catatan,
                                };

                            // }
                            array_detail_biaya.push(obj);
                        }
                        

                        $('#plastik').val(response.dataTujuan.plastik);
                        $('#tally').val(response.dataTujuan.tally);

                        $('#biayaDetail').val(JSON.stringify(array_detail_biaya));

                    }
         
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
           
            // console.log(array_detail_biaya);

		});
        $('body').on('change','#tipe_kontainer_out', function (){
            
            // var return_option = $("#select_chassis").select2().find(":selected")[0];    
            // var modelChassis = $( return_option ).attr('modelChassis');
            // if(this.value != modelChassis){
            $('#tipe_kontainer').val(this.value);
                setKendaraan($(this).val())
                setChassis($(this).val())
                // $('#select_driver').val('').trigger('change');
                
            // }
            // select_chassis
        })
        setKendaraan($('#tipe_kontainer').val())
        setChassis($('#tipe_kontainer').val())
        
        $('body').on('change','#select_kendaraan',function()
		{
            var idKendaraan = $(this).val();
            var selectedOption = $(this).find('option:selected');
            var idChassis = selectedOption.attr('idChassis');
            var nopol = selectedOption.attr('noPol');
            var supir = selectedOption.attr('idDriver');
            // console.log(idKendaraan);
            if(idKendaraan != '')
            {
                var tipeKontainerKendaraanDariChassis = selectedOption.attr('tipeKontainerKendaraanDariChassis').replace(/'/g, '');
            }

            // console.log(tipeKontainerKendaraanDariChassis);
            // kendaraan_id
            // no_polisi
            // select_chassis
            $('#kendaraan_id').val(idKendaraan);
            $('#no_polisi').val(nopol);
            $('#tipeKontainerKendaraanDariChassis').val(tipeKontainerKendaraanDariChassis);

            // if ( $('#jenis_order').val()=='OUTBOND') {
            //     $('#tipe_kontainer_out').val(tipeKontainerKendaraanDariChassis).trigger('change');
            // }
            $('#select_chassis').val(idChassis).trigger('change');
            $('#select_driver').val(supir).trigger('change');

		});

        

        $('body').on('change','#select_chassis',function()
		{
            var selectedOption = $(this).find('option:selected');
            var karoseris = selectedOption.attr('karoseris');
            
            $('#karoseri').val(karoseris);

		});

        $('body').on('change','#select_driver',function()
		{
            var selectedOption = $(this).find('option:selected');
            var nama_driver = selectedOption.attr('nama_driver');
            
            $('#driver_nama').val(nama_driver);

		});
        $('body').on('change','#stack_tl',function()
		{
            var selectedOption = $(this).val();
            var dataTelukLamong =  <?php echo json_encode($dataPengaturanKeuangan); ?>;
            // console.log(dataTelukLamong.tl_teluk_lamong);
            
            // $('#value_jenis_tl').val(selectedOption);

                if(selectedOption=='tl_teluk_lamong')
                {
                    $('#stack_teluk_lamong_hidden').val(dataTelukLamong.tl_teluk_lamong);
                }
                else
                {
                    $('#stack_teluk_lamong_hidden').val('');
                    
                }

		});
       
   
        function getDate(){
            // var today = new Date();
            // var tomorrow = new Date(today);
            // tomorrow.setDate(today.getDate() + 1);

            $('#tanggal_berangkat').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                // startDate: today,
            })/*.datepicker("setDate", tomorrow)*/;
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
        getDefaultValueEdit();
        function getDefaultValueEdit()
        {
            var baseUrl = "{{ asset('') }}";
            var customerLoad = false;
            // logic select jo jika ada
            var selectedJO = $('#select_jo').val();
            
            if(selectedJO > 0){
                var splitValue = selectedJO.split('-');
                var idJo=splitValue[0];
                var idCustomer=splitValue[1];
                $('#select_customer').val(idCustomer).trigger('change');
                $('#customer_id').val(idCustomer);
                $('#id_jo').val(idJo);

                $.ajax({
                    url: `${baseUrl}truck_order/getJoDetail/${idJo}`, 
                    method: 'GET', 
                    success: function(response) {
                        if(response&&customerLoad)
                        {
                            var jo_detail = $('#select_jo_detail');
                            jo_detail.attr('disabled',false);
                            jo_detail.empty(); 
                            jo_detail.append('<option value="">Pilih Kontainer</option>');
                            if(selectedJO!="")
                            {
                                response.forEach(joDetail => {
                                    const option = document.createElement('option');
                                    option.value = joDetail.id+"-"+joDetail.id_grup_tujuan+"-"+joDetail.no_kontainer;
                                    option.setAttribute('booking_id', joDetail.booking_id);
                                    option.textContent = joDetail.no_kontainer ;
                                    // if (selected_marketing == marketing.id) {
                                    //     option.selected = true;
                                    // }
                                    jo_detail.append(option);
                                });
                            }

                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        
                var selectedValue = $('#select_customer').val();
                $('#customer_id').val(selectedValue);
                //hadle booking bug
                var selectBooking = $('#select_booking').val();
                var splitValue = selectBooking.split('-');
                var idTujuan=splitValue[2];
                 var select_grup_tujuan = $('#select_grup_tujuan');

                $.ajax({
                    url: `${baseUrl}truck_order/getTujuanCust/${selectedValue}`, 
                    method: 'GET', 
                    success: function(response) {
                        if(response)
                        {
                            customerLoad = true;
                            // console.log(customerLoad);
                            // console.log(response.dataKredit.kreditCustomer);
                            // console.log(response.dataKredit.maxGrup);

                            // ==============================kredit=================
                            
                            let creds_now = (response.dataKredit.kreditCustomer/response.dataKredit.maxGrup) * 100;
                            creds_now = creds_now.toFixed(1);
                            // persenanCredit
                            const persen = document.getElementById('persenanCredit');

                            const cred = document.getElementById('credit_customer');
                            if(creds_now<80)
                            {
                                persen.innerHTML = creds_now+"%";
                                cred.style.width = creds_now+"%";
                                cred.style.backgroundColor = "#53de02";
                                cred.style.color = "black";
                                
                            }
                            else if(creds_now >=80 && creds_now <= 90)
                            {
                                persen.innerHTML = creds_now+"%";
                                cred.style.width = creds_now+"%";
                                cred.style.backgroundColor = "#deab02";
                                cred.style.color = "black";
                            }
                            else if(creds_now>=90)
                            {
                                persen.innerHTML = creds_now+"%";
                                cred.style.width = creds_now+"%";
                                cred.style.backgroundColor = "#de0202";
                                cred.style.color = "black";
                            }
                            else if(creds_now>100)
                            {
                                persen.innerHTML = creds_now+"%";
                                cred.style.width = "100%";
                                cred.style.backgroundColor = "#de0202";
                                cred.style.color = "black";
                            }
                            // ==============================kredit=================
                            
                        select_grup_tujuan.empty(); 
                        select_grup_tujuan.append('<option value="">Pilih Tujuan</option>');
                        if(selectedValue!="")
                        {
                            response.dataTujuan.forEach(tujuan => {
                                const option = document.createElement('option');
                                option.value = tujuan.id;
                                option.textContent = tujuan.nama_tujuan;
                                if(idTujuan!=''|| idTujuan!='[]'|| idTujuan!=null)
                                {
                                    if ($('#tujuan_id').val() == tujuan.id) {
                                        option.selected = true;
                                    }
    
                                }
                                 select_grup_tujuan.append(option);
                            });
                        }
                        // else
                        // {
                        //       select_grup_tujuan.empty(); 
                        //       select_grup_tujuan.append('<option value="">Pilih Tujuan</option>');
                        // }

                        }else{
                            customerLoad = false;
                                const persen = document.getElementById('persenanCredit');
                                const cred = document.getElementById('credit_customer');
                                persen.innerHTML = 0+"%";
                                cred.style.width = 0+"%";
                                cred.style.backgroundColor = "#53de02";
                                cred.style.color = "black";

                        }
            
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            
                //hadle booking bug
                var selectedValue = $('#select_grup_tujuan').val();
                var selectBooking = $('#select_booking').val();
                var splitValue = selectBooking.split('-');
                var idTujuan=splitValue[2];
                var array_detail_biaya = [];
                $.ajax({
                    url: `${baseUrl}truck_order/getTujuanBiaya/${idTujuan??selectedValue??$('#tujuan_id').val()}`, 
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

                            array_detail_biaya = []

                        }
                        else
                        {
                            $('#tujuan_id').val(response.dataTujuan.id);
                        

                            // JSON.stringify(array_detail_biaya)

                            $('#nama_tujuan').val(response.dataTujuan.nama_tujuan);
                            $('#alamat_tujuan').val(response.dataTujuan.alamat);
                            //   if(response.dataTujuan.jenis_tujuan =="LTL")
                            // {
                            //      $('#tarif').val(response.dataTujuan.min_muatan*response.dataTujuan.harga_per_kg );
                            // }
                            $('#tarif').val(response.dataTujuan.tarif);
                            $('#uang_jalan').val(response.dataTujuan.uang_jalan);
                            $('#komisi').val(response.dataTujuan.komisi);
                            $('#komisi_driver').val(response.dataTujuan.komisi_driver);

                            $('#jenis_tujuan').val(response.dataTujuan.jenis_tujuan);
                            //ltl
                            $('#harga_per_kg').val(response.dataTujuan.harga_per_kg);
                            $('#min_muatan').val(response.dataTujuan.min_muatan);
                        
                            $('#kargo').val(response.dataTujuan.kargo);

                            // console.log( response.dataTujuanBiaya);
                            var dataBiaya = response.dataTujuanBiaya;
                            for (var i in dataBiaya) {
                                   
                                var obj = {
                                    deskripsi: dataBiaya[i].deskripsi,
                                    biaya: dataBiaya[i].biaya,
                                    catatan: dataBiaya[i].catatan,
                                };

                                    
                                // if(dataBiaya[i].deskripsi!= 'TL')
                                // {
                                    array_detail_biaya.push(obj);
                                // }
                            }
                            

                            $('#plastik').val(response.dataTujuan.plastik);
                            $('#tally').val(response.dataTujuan.tally);

                            $('#biayaDetail').val(JSON.stringify(array_detail_biaya));

                        }
            
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            // =========================================
            // ===============================KENDARAAN===========================================
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

            $('#select_chassis').val(idChassis).trigger('change');
            $('#select_driver').val(supir).trigger('change');
            // ===============================KENDARAAN===========================================
            // =========================================

            // =========================================
            // ===============================CHASSIS===========================================

            var selectedOption = $('#select_chassis').find('option:selected');
            var karoseris = selectedOption.attr('karoseris');
                
            $('#karoseri').val(karoseris);
            // ===============================CHASSIS===========================================
            // =========================================

            // =========================================
            // ===============================DRIVER===========================================

            var selectedOption = $('#select_driver').find('option:selected');
            var nama_driver = selectedOption.attr('nama_driver');
                
            $('#driver_nama').val(nama_driver);
            // ===============================DRIVER===========================================
            // =========================================
            var selectedOption = $('#stack_tl').val();
            var dataTelukLamong =  <?php echo json_encode($dataPengaturanKeuangan); ?>;
            // console.log(dataTelukLamong.tl_teluk_lamong);
            
            // $('#value_jenis_tl').val(selectedOption);

                if(selectedOption=='tl_teluk_lamong')
                {
                    $('#stack_teluk_lamong_hidden').val(dataTelukLamong.tl_teluk_lamong);
                }
                else
                {
                    $('#stack_teluk_lamong_hidden').val('');
                    
                }
        }
    });
   
</script>
@endsection
