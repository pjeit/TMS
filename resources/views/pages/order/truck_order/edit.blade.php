
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
        cursor: pointer;
    }
    #outbound {
        cursor: pointer;
    }
     #inbound:hover,#outbound:hover {
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
    <form action="{{ route('truck_order.store') }}" method="POST" >
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
                            <div class="col-6 text-center radiusSendiri " id="inbound" >
                                {{-- <a href="" class="rubik-heading-2" style="text-decoration: none; color:black;">
                                    Bongkar (INBOUND)
                                </a> --}}
                                <label class="p-1">BONGKAR (INBOUND)</label>
                                <hr style="border: 0.5px solid #007bff; " id="garisInbound">

                            </div>

                            <div class="col-6 text-center radiusSendiri"id="outbound" >
                                {{-- <a href="" class="" style="text-decoration: none; color:black;">
                                    Muat (OUTBOND)
                                </a> --}}
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
                                    <input type="hidden" name="cred_now" id="cred_now" class="form-control" value="0">
                                    <input type="hidden" name="cred_val" id="cred_val" class="form-control" value="0">
                                    <input type="hidden" name="cred_val_max" id="cred_val_max" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group" id="outboundData">
                                    <label for="">No.Booking</label>
                                    <select class="form-control select2" style="width: 100%;" id='select_booking' name="select_booking">
                                        <option value="">Pilih No Booking</option>
                                        @foreach ($dataBooking as $book)
                                            <option value="{{$book->idBooking}}-{{$book->id_customer}}-{{$book->id_grup_tujuan}}-{{ \Carbon\Carbon::parse($book->tgl_booking)->format('d-M-Y')}}">{{ \Carbon\Carbon::parse($book->tgl_booking)->format('d-M-Y') }} / {{ $book->nama_tujuan }}  / {{ $book->kode }}</option>
                                        @endforeach
                                    </select>
                                </div>  
                                <div class="form-group" id="inboundData">
                                    <div class="form-group">
                                        <label for="">No. Job Order</label>
                                        <select class="form-control select2" style="width: 100%;" id='select_jo' name="select_jo" disabled>
                                            <option value="">Pilih No JO</option>
                                            @foreach ($datajO as $jo)
                                                <option value="{{$jo->id}}-{{$jo->id_customer}}" {{$jo->id == $data['id_jo']? 'selected':''}}>{{ $jo->no_bl }}</option>
                                            @endforeach
                                        </select>
                                    </div>  
                                    <div class="form-group">
                                        <label for="">No. Kontainer</label>
                                        <select class="form-control select2" style="width: 100%;" id='select_jo_detail' name="select_jo_detail" disabled>
                                            <option value="{{$data->getJOD->id}}-{{$data->getJOD->id_grup_tujuan}}-{{$data->getJOD->no_kontainer}}" selected>{{$data->getJOD->no_kontainer}}</option>
                                        </select>
                                        <input type="hidden" name="no_kontainer" id="no_kontainer" value="" placeholder="no_kontainer">
                                    </div> 
                                </div>
                                <div class="form-group">
                                        <label for="no_sewa">No. Sewa</label>
                                        <input type="text" class="form-control" id="no_sewa" placeholder="Otomatis" readonly="" value="">    
                                        <input type="hidden" id="status" value="">
                                        <!-- <div class='col-5 col-md-5 col-lg-5'>
                                            <label for="status">Status</label>
                                            <select onchange='ganti_status(this)' class="form-control" id="status" value="" disabled>
                                                <option value='Open'>Open</option>
                                                <option value='Approved'>Setuju</option>
                                                <option value='Released' hidden>Perjalanan</option>
                                                <option value='Finished' hidden>Selesai</option>
                                            </select>
                                        </div> -->
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
                                        <input type="text" name="catatan" class="form-control" id="catatan" name="catatan" placeholder="" value=""> 
                                    </div>
                            </div>
                            <div class="col-6">
                               
                                <div class="form-group">
                                    <label for="select_customer">Customer<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_customer' name="select_customer" disabled>
                                        <option value="">Pilih Customer</option>
                                        @foreach ($dataCustomer as $cust)
                                            <option value="{{$cust->idCustomer}}">{{ $cust->kodeCustomer }} - {{ $cust->namaCustomer }} / {{ $cust->namaGrup }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="customer_id" name="customer_id" value="" placeholder="customer_id">
                                    <input type="hidden" id="booking_id" name="booking_id" value="" placeholder="booking_id">
                                    <input type="hidden" id="jenis_order" name="jenis_order" value="{{$data['jenis_order']}}" placeholder="jenis_order">
                                </div>
                                <div class="form-group">
                                    <label for="select_tujuan">Tujuan<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_grup_tujuan' name="select_grup_tujuan" disabled>
                                        <option value="">{{$data->getJOD->nama_tujuan}}</option>
                                    </select>

                                    <input type="hidden" id="tujuan_id" name="tujuan_id" value="" placeholder="tujuan_id">
                                    <input type="hidden" name="id_jo_detail" id="id_jo_detail" value="" placeholder="id_jo_detail">
                                    <input type="hidden" name="id_jo" id="id_jo" value="" placeholder="id_jo">
                                    <input type="hidden" id="nama_tujuan" name="nama_tujuan" value="">
                                    <input type="hidden" id="alamat_tujuan" name="alamat_tujuan" value="">
                                    <input type="hidden" id="tarif" name="tarif" value="">
                                    <input type="hidden" id="uang_jalan" name="uang_jalan" value="">
                                    <input type="hidden" id="komisi" name="komisi" value="">
                                    <input type="hidden" id="jenis_tujuan" name="jenis_tujuan" value="">
                                    <input type="hidden" id="harga_per_kg" name="harga_per_kg" value="0">
                                    <input type="hidden" id="min_muatan" name="min_muatan" value="0">

                                    <input type="hidden" id="seal_pje" name="seal_pje" value="">
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
                                            <select class="form-control select2" style="width: 100%;" id='select_kendaraan' name="select_kendaraan">
                                                <option value="">Pilih Kendaraan</option>
                                                @foreach ($dataKendaraan as $kendaraan)
                                                    <option value="{{$kendaraan->kendaraanId}}-{{$kendaraan->chassisId}}-{{$kendaraan->no_polisi}}-{{$kendaraan->driver_id}}"  {{$kendaraan->kendaraanId == $data['id_kendaraan']? 'selected':''}}>{{ $kendaraan->no_polisi }}</option>
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
                                            <option value="{{$cha->id}}">{{ $cha->kode }} - {{ $cha->karoseri }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="ekor_id" name="ekor_id" value="">
                                    <input type="hidden" id="karoseri" name="karoseri" value="">

                                </div>
                                <div class="form-group">
                                    <label for="select_driver">Driver<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='select_driver' name="select_driver">
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
        var jenis = $('#jenis_order').val();

        if(jenis == 'INBOUND'){
            $("#inbound").addClass("aktif");
            $("#outbound").removeClass("aktif");
            $('#inboundData').show();
            $('#garisInbound').show();
            $('#outboundData').hide();
            $('#garisOutbound').hide();
        } else {
        $("#inbound").removeClass("aktif");
        $("#outbound").addClass("aktif");
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
        
        var customerLoad = false;

        // logic select jo jika ada
        var selectedValue = $('#select_jo').val();
        if(selectedValue != null){
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
            $('#jenis_tujuan').val('');
            //ltl
            $('#harga_per_kg').val('');
            $('#min_muatan').val('');
            $('#seal_pje').val('');
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
                        $('#jenis_tujuan').val('');
                        //ltl
                        $('#harga_per_kg').val('');
                        $('#min_muatan').val('');
                        $('#seal_pje').val('');
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
                        if(response.dataTujuan.seal_pje)
                        {
                            
                            var objSeal = {
                                       deskripsi: 'SEAL PJE',
                                       biaya: response.dataTujuan.seal_pje,
                                   };
                               array_tambahan_tarif.push(objSeal);
                        }

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

                   

                        $('#seal_pje').val(response.dataTujuan.seal_pje);
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

        $('body').on('change','#select_kendaraan',function()
		{
            var selectedValue = $(this).val();
            var split = selectedValue.split("-");

            var idKendaraan = split[0];
            var idChassis = split[1];
            var nopol = split[2];
            var supir = split[3];


            console.log(idChassis);
            // kendaraan_id
            // no_polisi
            // select_chassis
            $('#kendaraan_id').val(idKendaraan);
            $('#no_polisi').val(nopol);
            $('#select_chassis').val(idChassis).trigger('change');
            $('#ekor_id').val(idChassis);
            $('#select_driver').val(supir).trigger('change');

		});

        $('body').on('change','#select_driver',function()
		{
            var selectedValue = $(this).val();
            var split = selectedValue.split("-");

            var idKendaraan = split[0];
            var idChassis = split[1];
            var nopol = split[2];
            $('#driver_nama').val(idChassis);
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
