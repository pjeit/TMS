
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
    #inbound,#outbond {
        cursor: pointer;
    }
    #inbound:hover,#outbond:hover {
        background-color: rgb(196, 223, 255);
        /* border-block-end: 1px solid #007bff; */
        /* border-block-start: 1px solid #007bff; */
    }
    .aktif {
        background-color: #e0efff;
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
    <form action="{{ route('truck_order.store') }}" method="POST" id="post_data">
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
                        <a href="{{ route('truck_order.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        {{-- <button type="submit">wet</button> --}}
                    </div>
                    <div class="card-body">
                         <div class="row mb-2">
                            <div class="col-6 text-center radiusSendiri" id="inbound">
                                {{-- <a href="" class="rubik-heading-2" style="text-decoration: none; color:black;">
                                    Bongkar (INBOUND)
                                </a> --}}
                                <label class="p-1">BONGKAR (INBOUND)</label>
                                <hr style="border: 0.5px solid #007bff; " id="garisInbound">

                            </div>

                            <div class="col-6 text-center radiusSendiri"id="outbond">
                                {{-- <a href="" class="" style="text-decoration: none; color:black;">
                                    Muat (OUTBOND)
                                </a> --}}
                                <label class=" p-1">MUAT (OUTBOND)</label>
                                <hr style="border: 0.5px solid #007bff;" id="garisOutbond">
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
                                    <input type="hidden" name="cred_now" id="cred_now" class="form-control" value="0">
                                    <input type="hidden" name="cred_val" id="cred_val" class="form-control" value="0">
                                    <input type="hidden" name="cred_val_max" id="cred_val_max" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group" id="outbondData">
                                    <label for="">No.Booking</label>
                                    <select class="form-control select2" style="width: 100%;" id='select_booking' name="select_booking">
                                        <option value="">Pilih No Booking</option>
                                        @foreach ($dataBooking as $book)
                                            <option value="{{$book->idBooking}}-{{$book->id_customer}}-{{$book->id_grup_tujuan}}-{{ \Carbon\Carbon::parse($book->tgl_booking)->format('d-M-Y')}}">{{ \Carbon\Carbon::parse($book->tgl_booking)->format('d-M-Y') }} / {{ $book->nama_tujuan }}  / {{ $book->kode }}</option>
                                        @endforeach
                                    </select>
                                </div>  
                                <div id="inboundData">
                                    <div class="form-group">
                                        <label for="">No. Job Order</label>
                                        <select class="form-control select2" style="width: 100%;" id='select_jo' name="select_jo">
                                            <option value="">Pilih No JO</option>
                                            @foreach ($datajO as $jo)
                                                <option value="{{$jo->id}}-{{$jo->id_customer}}">{{ $jo->no_bl }} / {{ $jo->getCustomer->kode }} / {{ $jo->getSupplier->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>  
                                    <div class="form-group">
                                        <label for="">No. Kontainer</label>
                                        <select class="form-control select2" style="width: 100%;" id='select_jo_detail' name="select_jo_detail">
                                            <option value="">Pilih Kontainer</option>
                                        </select>
                                        <input type="hidden" name="no_kontainer" id="no_kontainer" value="" placeholder="no_kontainer">
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="form-group col-7">
                                        <label for="no_sewa">No. Sewa</label>
                                        <input type="text" class="form-control" id="no_sewa" placeholder="Otomatis" readonly="" value="">    
                                        <input type="hidden" id="status" value="">
                                    </div>
                                    <div class="form-group col-5">
                                        <div class="form-group" id="inboundDataKontainer">
                                            <label for="">Tipe Kontainer<span class="text-red">*</span></label>
                                            <input type="text" class="form-control" id="tipe_kontainer_in" placeholder="" readonly="" value="">    
                                        </div>
                                        <div class="form-group" id="outbondDataKontainer">
                                            <label for="">Tipe Kontainer<span class="text-red">*</span></label>
                                            <select class="form-control selectpicker tipeKontainer" id="tipe_kontainer_out"  data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                <option value="">── Tipe ──</option>
                                                <option value='20'>20"</option>
                                                <option value='40'>40"</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="tipe_kontainer" id="tipe_kontainer">
                                    </div>                                    
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_berangkat">Tanggal Berangkat<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="">
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                        <label for="catatan">Catatan</label>
                                        {{-- <input type="text" name="catatan" class="form-control" id="catatan" name="catatan" placeholder="" value="">  --}}
                                    <textarea name="catatan" class="form-control" id="catatan" cols="20" rows="4" placeholder="" value=""></textarea>
                                
                                </div>
                            </div>
                            <div class="col-6">
                               
                                <div class="form-group">
                                    <label for="select_customer">Customer<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_customer' name="select_customer" required>
                                        <option value="">Pilih Customer</option>

                                        @foreach ($dataCustomer as $cust)
                                            <option value="{{$cust->idCustomer}}">{{ $cust->kodeCustomer }} - {{ $cust->namaCustomer }} / {{ $cust->namaGrup }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="customer_id" name="customer_id" value="" placeholder="customer_id">
                                    <input type="hidden" id="booking_id" name="booking_id" value="" placeholder="booking_id">
                                    <input type="hidden" id="jenis_order" name="jenis_order" value="" placeholder="jenis_order">
                                </div>
                                <div class="form-group">
                                    <label for="select_tujuan">Tujuan<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_grup_tujuan' name="select_grup_tujuan" required>
                                        <option value="">Pilih Tujuan</option>

                                        {{-- @foreach ($kota as $city)
                                            <option value="{{$city->id}}">{{ $city->nama }}</option>
                                        @endforeach --}}
                                    </select>

                                    <input type="hidden" id="tujuan_id" name="tujuan_id" value="" placeholder="tujuan_id">
                                    <input type="hidden" name="id_jo_detail" id="id_jo_detail" value="" placeholder="id_jo_detail">
                                    <input type="hidden" name="id_jo" id="id_jo" value="" placeholder="id_jo">
                                    <input type="hidden" id="nama_tujuan" name="nama_tujuan" value="">
                                    <input type="hidden" id="alamat_tujuan" name="alamat_tujuan" value="">
                                    <input type="hidden" id="tarif" name="tarif" value="">
                                    <input type="hidden" id="uang_jalan" name="uang_jalan" value="">
                                    <input type="hidden" id="komisi" name="komisi" value="">
                                    <input type="hidden" id="komisi_driver" name="komisi_driver" value="">
                                    <input type="hidden" id="jenis_tujuan" name="jenis_tujuan" value="">
                                    <input type="hidden" id="harga_per_kg" name="harga_per_kg" value="0">
                                    <input type="hidden" id="min_muatan" name="min_muatan" value="0">

                                    <input type="hidden" id="plastik" name="plastik" value="">
                                    <input type="hidden" id="tally" name="tally" value="">
                                    <input type="hidden" id="kargo" name="kargo" value="">


                                    <input type="hidden" id="biayaDetail" name="biayaDetail">
                                    <input type="hidden" id="biayaTambahTarif" name="biayaTambahTarif">
                                    <input type="hidden" id="biayaTambahSDT" name="biayaTambahSDT">
                                </div>
                                {{-- <div class="row">
                                    <div class="col">
                                          <div class="form-group">
                                            <label for="select_kategori_kendaraan">Kategori Kendaraan<span style="color:red">*</span></label>
                                            <select class="form-control select2" style="width: 100%;" id='select_kategori_kendaraan' name="select_kategori_kendaraan">
                                                <option value="">Pilih Kendaraan</option>

                                         
                                            </select>
                                            <input type="hidden" id="kendaraan_id" name="kendaraan_id" value="">
                                        </div>
                                    </div>
                                    <div class="col"> --}}
                                          <div class="form-group">
                                            <label for="select_kendaraan">Kendaraan<span style="color:red">*</span></label>
                                            <select class="form-control select2" style="width: 100%;" id='select_kendaraan' name="select_kendaraan" required>
                                                <option value="">Pilih Kendaraan</option>
 
                                                @foreach ($dataKendaraan as $kendaraan)
                                                
                                                    <option value="{{$kendaraan->kendaraanId}}"
                                                        idChassis='{{$kendaraan->chassisId}}'
                                                        noPol='{{$kendaraan->no_polisi}}'
                                                        idDriver='{{$kendaraan->driver_id}}'
                                                        kategoriKendaraan='{{$kendaraan->kategoriKendaraan}}'
                                                        >{{ $kendaraan->no_polisi }} ({{$kendaraan->kategoriKendaraan}})</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" id="kendaraan_id" name="kendaraan_id" value="">
                                            <input type="hidden" id="no_polisi" name="no_polisi" value="">
                                        </div>
                                    {{-- </div>

                                </div> --}}
                              
                                <div class="form-group">
                                    <label for="select_ekor">Chassis<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='select_chassis' name="select_chassis">
                                        <option value="">Pilih Chassis</option>

                                        @foreach ($dataChassis as $cha)
                                            <option value="{{$cha->idChassis}}" karoseris="{{$cha->karoseri}}">{{ $cha->kode }} - {{ $cha->karoseri }} ({{$cha->modelChassis}})</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="karoseri" name="karoseri" value="">

                                </div>
                                <div class="form-group">
                                    <label for="select_driver">Driver<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='select_driver' name="select_driver" required>
                                        <option value="">Pilih Driver</option>

                                        @foreach ($dataDriver as $drvr)
                                            <option value="{{$drvr->id}}">{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="driver_nama" name="driver_nama" value="">
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
      
        // $('#select_customer').attr('disabled',true).val('').trigger('change');
        // $('#select_grup_tujuan').attr('disabled',true).val('').trigger('change');
        $('#inboundData').hide();
        $('#tipe_kontainer_in').val();
        $('#inboundDataKontainer').hide();
        $('#garisInbound').hide();
        $("#inbound").removeClass("aktif");
        $("#outbond").addClass("aktif");
        $('#jenis_order').val('OUTBOND');
        
        $('body').on('click','#inbound',function()
		{
            // console.log('pencet');
            $('#tipe_kontainer').val();
            $('#tipe_kontainer_in').val();
            $("#inbound").addClass("aktif");
            $("#outbond").removeClass("aktif");
            $('#inboundData').show();
            $('#inboundDataKontainer').show();
            $('#garisInbound').show();

            $('#outbondData').hide();
            $('#outbondDataKontainer').hide();
            $('#garisOutbond').hide();
            $('#select_booking').val('').trigger('change');
            getDate();
            $('#jenis_order').val('INBOUND');
            $('#select_jo_detail').val('').trigger('change');
            $('#select_jo').val('').trigger('change');

            $('#select_customer').attr('disabled',true).val('').trigger('change');
            $('#select_grup_tujuan').attr('disabled',true).val('').trigger('change');
            $('#karoseri').val('');
            $('#tipe_kontainer_out').val('').trigger('change');

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
            $('#biayaTambahTarif').val('');
		});

        $('body').on('click','#outbond',function()
		{
            // $(this).animate({ "color": "red" }, 1500);
            $('#tipe_kontainer').val();
            $("#inbound").removeClass("aktif");
            $("#outbond").addClass("aktif");
            $('#select_booking').val('').trigger('change');
            $('#inboundData').hide();
            $('#inboundDataKontainer').hide();
            $('#garisInbound').hide();
            $('#outbondData').show();
            $('#outbondDataKontainer').show();
            $('#garisOutbond').show();
            $('#select_customer').attr('disabled',false).val('').trigger('change');
            $('#select_grup_tujuan').attr('disabled',false).val('').trigger('change');
            $('#tipe_kontainer_out').val('').trigger('change');

            
            $('#select_jo_detail').val('').trigger('change');
            $('#select_jo').val('').trigger('change');
            $('#jenis_order').val('OUTBOND');
            $('#karoseri').val('');

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
            $('#biayaTambahTarif').val('');
            getDate();
		});

        $('body').on('change','#select_booking',function()
		{
            var selectedValue = $(this).val();
            var splitValue = selectedValue.split('-');
            // console.log('splitValue '+splitValue);
            var booking_id=splitValue[0];
            var idCustomer=splitValue[1];
            var idTujuan=splitValue[2];
            var tanggalBerangkat=splitValue[3];
            var bulanBerangkat=splitValue[4];
            var tahunBerangkat=splitValue[5];
            var gabungan = tanggalBerangkat+"-"+bulanBerangkat+"-"+tahunBerangkat
            // console.log(tanggalBerangkat+"-"+bulanBerangkat+"-"+tahunBerangkat);
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
        
        $('#select_jo_detail').attr('disabled',true);

        var customerLoad = false;

        $('body').on('change','#select_jo',function()
		{
            var selectedValue = $(this).val();
            var splitValue = selectedValue.split('-');
            var idJo=splitValue[0];
            var idCustomer=splitValue[1];
            $('#select_customer').val(idCustomer).trigger('change');
            $('#customer_id').val(idCustomer);
            $('#id_jo').val(idJo);

            var baseUrl = "{{ asset('') }}";
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
                        if(selectedValue!="")
                        {
                            response.forEach(joDetail => {
                                const option = document.createElement('option');
                                option.value = joDetail.id+"-"+joDetail.id_grup_tujuan+"-"+joDetail.no_kontainer+'-'+joDetail.seal+"-"+joDetail.tipe_kontainer;
                                option.setAttribute('booking_id', joDetail.booking_id);
                                option.textContent = joDetail.no_kontainer ;
                                // if (selected_marketing == marketing.id) {
                                //     option.selected = true;
                                // }
                                 jo_detail.append(option);
                            });
                        }

                    }
                    else
                    {
                        $('#select_jo_detail').empty(); 
                        $('#select_jo_detail').append('<option value="">Pilih Kontainer</option>');
                        $('#select_jo_detail').attr('disabled',true).val('').trigger('change');

                    }
                    // jo_detail.trigger('change');
        
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
           


		});

        $('body').on('change','#tipe_kontainer_out',function(){
            $('#tipe_kontainer').val(this.value);
        })

        $('body').on('change','#select_jo_detail',function()
		{
            var selectedValue = $(this).val();
            var splitValue = selectedValue.split('-');
            var idJoDetail=splitValue[0];
            var idTujuan=splitValue[1];
            var no_kontainer=splitValue[2];
            var seal=splitValue[3];
            var tipe_kontainer=splitValue[4];
            
            var selectedOption = $(this).find('option:selected');
            var bookingId = selectedOption.attr('booking_id');            

            $('#select_grup_tujuan').val(idTujuan).trigger('change');
            $('#booking_id').val(bookingId);
            $('#id_jo_detail').val(idJoDetail);
            $('#no_kontainer').val(no_kontainer);
            var kontainer = '';
            if(tipe_kontainer != undefined){
                kontainer = tipe_kontainer + `"`;
            }
            $('#tipe_kontainer_in').val(kontainer);
            $('#tipe_kontainer').val(tipe_kontainer);
            var baseUrl = "{{ asset('') }}";
            // var myjson;
            var array_tambahan_sdt = [];
            setKendaraan(tipe_kontainer)
            setChassis(tipe_kontainer)
            // $.ajax({
            //     url: `${baseUrl}truck_order/getDetailJOBiaya/${idJoDetail}`, 
            //     method: 'GET', 
            //     success: function(response) {
            //         if(!response)
            //         {
            //             array_tambahan_sdt = [];
            //         }
            //         else
            //         {
            //             for (var i in response) {
            //                 if(response[i].storage || response[i].storage!=0)
            //                 {
            //                     var objSTORAGE = {
            //                             deskripsi: 'STORAGE',
            //                             biaya: response[i].storage,
            //                         };
            //                     array_tambahan_sdt.push(objSTORAGE);
            //                 } 
            //                 if(response[i].demurage||response[i].demurage!=0)
            //                 {
            //                     var objDEMURAGE = {
            //                             deskripsi: 'DEMURAGE',
            //                             biaya: response[i].demurage,
            //                         };
            //                     array_tambahan_sdt.push(objDEMURAGE);
            //                 } 
            //                 if(response[i].detention||response[i].detention!=0)
            //                 {
            //                     var objDETENTION = {
            //                             deskripsi: 'DETENTION',
            //                             biaya: response[i].detention,
            //                         };
            //                     array_tambahan_sdt.push(objDETENTION);
            //                 } 
                                
            //             }
            //             $('#biayaTambahSDT').val(JSON.stringify(array_tambahan_sdt));
            //             console.log('array_tambahan_sdt '+array_tambahan_sdt);

            //         }
            //     },
            //     error: function(xhr, status, error) {
            //         console.error('Error:', error);
            //     }
            // });

            // get data booking
            // $.ajax({
            //     url: `${baseUrl}truck_order/getDataBooking/${idJoDetail}`, 
            //     method: 'GET', 
            //     success: function(response) {
            //         // console.log('response '+response.tgl_booking);
            //         // console.log('today '+today);

            //         // if(!response){
            //         //     array_tambahan_sdt = [];
            //         // }
            //     },
            //     error: function(xhr, status, error) {
            //         console.error('Error:', error);
            //     }
            // });
           

		});

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
            $('#biayaTambahTarif').val('');

            // let creds = $('#cred_val').val();
            // let creds_max = $('#cred_val_max').val();
            // creds = creds.replace(/,/g,'');
            // creds_max = creds_max.replace(/,/g,'');
            // //debug sini 2
           
            $.ajax({
                url: `${baseUrl}truck_order/getTujuanCust/${selectedValue}`, 
                method: 'GET', 
                success: function(response) {
                    if(response)
                    {
                        customerLoad = true;
                        console.log(customerLoad);
                        // console.log(response.dataKredit.kreditCustomer);
                        // console.log(response.dataKredit.maxGrup);

                        // ==============================kredit=================
                        
                        // let creds = $('#cred_val').val();
                        // let creds_max = $('#cred_val_max').val();
                        // creds = creds.replace(/,/g,'');
                        // creds_max = creds_max.replace(/,/g,'');
                        // //debug sini 2
                        // creds = parseInt(creds) + parseInt(total_tarif);
           
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


                        var select_grup_tujuan = $('#select_grup_tujuan');
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

            // var myjson;
            var array_detail_biaya = [];
            var array_tambahan_tarif = [];

            //
            // customer_id
            // tujuan_id
            // nama_tujuan
            // alamat_tujuan
            // tarif
            // uang_jalan
            // komisi
            // jenis_tujuan
            // harga_per_kg
            // min_muatan
            // seal_pelayaran
            // plastik
            // tally
            // kargo
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
                        $('#biayaTambahTarif').val('');

                        array_detail_biaya = []
                        array_tambahan_tarif = [];

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
                            array_detail_biaya.push(obj);
                        }
                        
                        // var obj=JSON.parse(myjson);
                        // array_detail_biaya.push(obj);
                        if(response.dataTujuan.plastik)
                        {
                            
                            var objpLASTIK = {
                                       deskripsi: 'PLASTIK',
                                       biaya: response.dataTujuan.plastik,
                                   };
                               array_tambahan_tarif.push(objpLASTIK);
                        }

                        if(response.dataTujuan.tally)
                        {
                            
                            var objtally = {
                                       deskripsi: 'TALLY',
                                       biaya: response.dataTujuan.tally,
                                   };
                               array_tambahan_tarif.push(objtally);
                        }

                        $('#plastik').val(response.dataTujuan.plastik);
                        $('#tally').val(response.dataTujuan.tally);

                        $('#biayaDetail').val(JSON.stringify(array_detail_biaya));
                        $('#biayaTambahTarif').val(JSON.stringify(array_tambahan_tarif));

                        // console.log(array_detail_biaya);

                        // console.log(array_tambahan_tarif);


                    }

         
                    // jo_detail.trigger('change');
        
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
           


		});
        $('body').on('change','#tipe_kontainer_out', function (){
      //fire your ajax call  
            setKendaraan($(this).val())
            setChassis($(this).val())



        })

        function setKendaraan(tipeKontainer)
        {
            // console.log(tipeKontainer);
            var kontainerSemua =  <?php echo json_encode($dataKendaraan); ?>;
            var select_kendaraan = $('#select_kendaraan');

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


                    option.textContent = kendaraan.no_polisi + `(${kendaraan.kategoriKendaraan})` ;
                    select_kendaraan.append(option);
                });
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
                            // console.log(response);
                            select_kendaraan.empty(); 
                            select_kendaraan.append('<option value="">Pilih Kendaraan</option>');
                            if(tipeKontainer!=""|| tipeKontainer!= undefined)
                            {
                                response.forEach(kendaraan => {
                                    const option = document.createElement('option');
                                    option.value = kendaraan.kendaraanId;
                                    option.setAttribute('idChassis', kendaraan.chassisId);
                                    option.setAttribute('noPol', kendaraan.no_polisi);
                                    option.setAttribute('idDriver', kendaraan.driver_id);
                                    option.setAttribute('kategoriKendaraan', kendaraan.kategoriKendaraan);

                                    option.textContent = kendaraan.no_polisi + `(${kendaraan.kategoriKendaraan})` ;
                                    // if (selected_marketing == marketing.id) {
                                    //     option.selected = true;
                                    // }
                                     select_kendaraan.append(option);
                                });
                            }
    
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

            if(tipeKontainer==''|| tipeKontainer== undefined)
            {
                select_chassis.empty(); 
                select_chassis.append('<option value="">Pilih Chassis</option>');
                         
                chassisSemua.forEach(chassis => {
                    const option = document.createElement('option');
                    option.value = chassis.idChassis;
                    option.setAttribute('karoseris', chassis.karoseri);
                    option.textContent = `${chassis.karoseri} - ${chassis.kode} (${chassis.modelChassis})` ;
                    select_chassis.append(option);
                });

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
                            
                            console.log(response);
                            select_chassis.empty(); 
                            select_chassis.append('<option value="">Pilih Chassis</option>');
                            if(tipeKontainer!=""|| tipeKontainer!= undefined)
                            {
                                
                                 response.forEach(chassis => {
                                    const option = document.createElement('option');
                                    option.value = chassis.idChassis;
                                    option.setAttribute('karoseris', chassis.karoseri);
                                    option.textContent = `${chassis.karoseri} - ${chassis.kode} (${chassis.modelChassis})` ;
                                    select_chassis.append(option);
                                });
                            }
    
                        }
                     
            
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

        }
         


        $('body').on('change','#select_kendaraan',function()
		{
            var idKendaraan = $(this).val();
            var selectedOption = $(this).find('option:selected');
            var idChassis = selectedOption.attr('idChassis');
            var nopol = selectedOption.attr('noPol');
            var supir = selectedOption.attr('idDriver');
            console.log(idChassis);
            // kendaraan_id
            // no_polisi
            // select_chassis
            $('#kendaraan_id').val(idKendaraan);
            $('#no_polisi').val(nopol);
            $('#select_chassis').val(idChassis).trigger('change');
            $('#select_driver').val(supir).trigger('change');

		});

        $('body').on('change','#select_chassis',function()
		{
            var selectedOption = $(this).find('option:selected');
            var karoseris = selectedOption.attr('karoseris');
            
            console.log(karoseris);
            $('#karoseri').val(karoseris);

		});
         $('#post_data').submit(function(event) {
            var jenis_order = $('#jenis_order').val();
            var selectKendaraan = $('#select_kendaraan').find('option:selected');
            var kategoriKendaraan = selectKendaraan.attr('kategoriKendaraan');
            if($('#select_kendaraan').val()=='')
            {
                event.preventDefault();
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
                        icon: 'error',
                        title: 'Kendaraan Harus dipilih'
                    })
                return;
            }
             if($('#select_chassis').val()=='')
            {
                event.preventDefault();
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
                        icon: 'error',
                        title: 'chassis Harus dipilih'
                    })
                return;
            }
            if($('#tipe_kontainer').val()=='' && kategoriKendaraan =='Trailer')
            {
                
                event.preventDefault();
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
                        icon: 'error',
                        title: 'Tipe Kontainer Harus dipilih'
                    })
                return;
            }

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
                    }, 800); // 2000 milliseconds = 2 seconds
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
        function getDate(){
            var today = new Date();
            var tomorrow = new Date(today);
            tomorrow.setDate(today.getDate() + 1);

            $('#tanggal_berangkat').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                startDate: tomorrow,
            }).datepicker("setDate", tomorrow);
        }
    });
   
</script>
@endsection
