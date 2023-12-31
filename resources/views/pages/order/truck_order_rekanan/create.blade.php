
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
        background-color: #e0efff;
        /* border-block-end: 1px solid #007bff; */

        /* border-block-start: 1px solid #007bff; */


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
    <form action="{{ route('truck_order_rekanan.store') }}" method="POST" id="post_data">
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
                                                <option value="{{$jo->id}}-{{$jo->id_customer}}">{{ $jo->no_bl }}/{{ $jo->id_customer }}/{{ $jo->id_supplier }}/{{ $jo->no_bl }}</option>
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
                                    <input type="hidden" id="jenis_tujuan" name="jenis_tujuan" value="">
                                    <input type="hidden" id="harga_per_kg" name="harga_per_kg" value="0">
                                    <input type="hidden" id="min_muatan" name="min_muatan" value="0">

                                    <input type="hidden" id="plastik" name="plastik" value="">
                                    <input type="hidden" id="tally" name="tally" value="">
                                    <input type="hidden" id="kargo" name="kargo" value="">

                                    <input type="hidden" id="kontainer" name="kontainer" value="">

                                    <input type="hidden" id="biayaDetail" name="biayaDetail">
                                    <input type="hidden" id="biayaTambahTarif" name="biayaTambahTarif">
                                    <input type="hidden" id="biayaTambahSDT" name="biayaTambahSDT">
                                </div>
                                <div class="form-group">
                                    <label for="supplier">Supplier<span class="text-red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='supplier' name="supplier">
                                        <option value="">Pilih Supplier</option>
                                         @foreach ($supplier as $s)
                                            <option value="{{$s->id}}" nama_supplier='{{$s->nama}}'>{{ $s->nama }}</option>
                                        @endforeach 
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="form-group" id="inboundDataKontainer">
                                                <label for="">Tipe Kontainer<span class="text-red">*</span></label>
                                                <input type="text" class="form-control" id="tipe_kontainer_in" placeholder="" readonly="" value="">    
                                            </div>
                                            <div class="form-group" id="outbondDataKontainer">
                                                <label for="">Tipe Kontainer<span class="text-red">*</span></label>
                                                <select class="form-control select2 tipeKontainer" id="tipe_kontainer_out"  data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                    <option value="">── Tipe ──</option>
                                                    <option value='20'>20"</option>
                                                    <option value='40'>40"</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="tipe_kontainer" id="tipe_kontainer">
                                        </div>                                    
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="no_polisi">No. polisi rekanan</label>
                                            <input type="text" maxlength="11" name="no_polisi" class="form-control" id="no_polisi" name="no_polisi" placeholder="" value=""> 
                                            <input type="hidden"  name="driver_nama" class="form-control" id="driver_nama" name="driver_nama" placeholder="" value="" readonly> 
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="harga_jual">Harga Jual<span class="text-red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" name="harga_jual" class="form-control numaja uang" id="harga_jual"  min="0">
                                    
                                    </div>
                                </div>

                                 <div class="form-group">
                                    <label for="select_driver">Stack TL</label>
                                        <select class="form-control select2" style="width: 100%;" id='stack_tl' name="stack_tl">
                                        <option value="">── Pilih TL ──</option>
                                        <option value="tl_perak">Perak</option>
                                        <option value="tl_priuk">Priuk</option>
                                        <option value="tl_teluk_lamong">Teluk Lamong</option>
                                    </select>
                                    <input type="hidden" id="stack_teluk_lamong_hidden" name="stack_teluk_lamong_hidden" value="" placeholder="stack_teluk_lamong_hidden">
                                    {{-- <input type="text" id="value_jenis_tl" name="value_jenis_tl" value=""> --}}


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
        // $('#inboundData').hide();
        // $('#garisInbound').hide();
        // $('#jenis_order').val('');

         $('#inboundData').hide();
          $('#tipe_kontainer_in').val();
        $('#inboundDataKontainer').hide();

        $('#garisInbound').hide();
        $("#inbound").removeClass("aktif");
        $("#outbond").addClass("aktif");
        $('#jenis_order').val('OUTBOUND');
        
        $('body').on('click','#inbound',function()
		{
            // console.log('pencet');
            $('#inboundDataKontainer').show();
            $('#outbondDataKontainer').hide();

            $('#inboundData').show();
            $('#garisInbound').show();
             $('#tipe_kontainer').val();
            $('#tipe_kontainer_in').val();
            $('#outbondData').hide();
            $('#garisOutbond').hide();
            $('#select_booking').val('').trigger('change');
            getDate();
            $('#jenis_order').val('INBOUND');
            $('#select_jo_detail').val('').trigger('change');
            $('#select_jo').val('').trigger('change');

            $('#select_customer').attr('disabled',true).val('').trigger('change');
            $('#select_grup_tujuan').attr('disabled',true).val('').trigger('change');

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
            $('#plastik').val('');
            $('#tally').val('');
            $('#kargo').val('');
            $('#biayaDetail').val('');
            $('#biayaTambahTarif').val('');

            $('#kontainer').val('');

		});

        $('body').on('click','#outbond',function()
		{
            // $(this).animate({ "color": "red" }, 1500);
              $('#inboundDataKontainer').hide();
            $('#outbondDataKontainer').show();
            $('#tipe_kontainer').val();

            $('#select_booking').val('').trigger('change');
            $('#inboundData').hide();
            $('#garisInbound').hide();
            $('#outbondData').show();
            $('#garisOutbond').show();
            $('#select_customer').attr('disabled',false).val('').trigger('change');
            $('#select_grup_tujuan').attr('disabled',false).val('').trigger('change');
            
            $('#select_jo_detail').val('').trigger('change');
            $('#select_jo').val('').trigger('change');
            $('#jenis_order').val('OUTBOUND');

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
            $('#plastik').val('');
            $('#tally').val('');
            $('#kargo').val('');
            $('#biayaDetail').val('');
            $('#biayaTambahTarif').val('');
            $('#kontainer').val('');
            getDate();
		});

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
                    if(response/*&&customerLoad*/)
                    {
                        var jo_detail = $('#select_jo_detail');
                        jo_detail.attr('disabled',false);
                        jo_detail.empty(); 
                        jo_detail.append('<option value="">Pilih Kontainer</option>');
                        // if(selectedValue!="")
                        // {
                            response.forEach(joDetail => {
                                const option = document.createElement('option');
                                option.value = joDetail.id+"-"+joDetail.id_grup_tujuan+"-"+joDetail.no_kontainer+'-'+joDetail.seal+"-"+joDetail.tipe_kontainer;
                                option.setAttribute('booking_id', joDetail.booking_id);
                                option.setAttribute('pick_up', joDetail.pick_up);
                                option.textContent = joDetail.no_kontainer ;
                                // if (selected_marketing == marketing.id) {
                                //     option.selected = true;
                                // }
                                 jo_detail.append(option);
                            });
                        // }

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
            var pick_up = selectedOption.attr('pick_up');

            if(pick_up=='TTL')
            {
                
                 $('#stack_tl').val('tl_teluk_lamong').trigger('change');

            }
            else if (pick_up=='DEPO')
            {
                 $('#stack_tl').val('tl_perak').trigger('change');

            }
            else
            {
                 $('#stack_tl').val('').trigger('change');

            }
            $('#select_grup_tujuan').val(idTujuan).trigger('change');
            $('#booking_id').val(bookingId);
            $('#id_jo_detail').val(idJoDetail);
            $('#kontainer').val(no_kontainer);
            var kontainer = '';
            if(tipe_kontainer != undefined){
                kontainer = tipe_kontainer + `"`;
            }
            $('#tipe_kontainer_in').val(kontainer);
            $('#tipe_kontainer').val(tipe_kontainer);
            var baseUrl = "{{ asset('') }}";
            // var myjson;
            var array_tambahan_sdt = [];
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
            $('#jenis_tujuan').val('');
            //ltl
            $('#harga_per_kg').val('');
            $('#min_muatan').val('');
            $('#plastik').val('');
            $('#tally').val('');
            $('#kargo').val('');
            $('#biayaDetail').val('');
            $('#biayaTambahTarif').val('');

        
            var select_grup_tujuan = $('#select_grup_tujuan');

            $.ajax({
                url: `${baseUrl}truck_order/getTujuanCust/${selectedValue}`, 
                method: 'GET', 
                success: function(response) {
                    if(response)
                    {
    
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
            // seal_pje
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
        $('body').on('change','#supplier',function()
		{
            var selectedOption = $(this).find('option:selected');
            var nama_supplier = selectedOption.attr('nama_supplier');
            
            console.log(nama_supplier);
            if(nama_supplier==undefined)
            {
                $('#driver_nama').val('');

            }
            else
            {
                $('#driver_nama').val('DRIVER '+ nama_supplier);

            }

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
        $('#post_data').submit(function(event) {
            var no_polisi = $('#no_polisi').val();
            var supplier = $('#supplier').val();
            var harga_jual = $('#harga_jual').val();

            var tarif = $('#tarif').val();
            var uang_jalan = $('#uang_jalan').val();
            var total_uang_jalan = parseFloat(tarif)+parseFloat(uang_jalan);
            // if(no_polisi.trim()=='')
            // {
            //     event.preventDefault();
            //     const Toast = Swal.mixin({
            //             toast: true,
            //             position: 'top-end',
            //             timer: 2500,
            //             showConfirmButton: false,
            //             timerProgressBar: true,
            //             didOpen: (toast) => {
            //                 toast.addEventListener('mouseenter', Swal.stopTimer)
            //                 toast.addEventListener('mouseleave', Swal.resumeTimer)
            //             }
            //         })

            //         Toast.fire({
            //             icon: 'error',
            //             title: 'Nomor Polisi Rekanan Harus diisi!'
            //         })
            //     return;
            // }
            if(supplier.trim()=='')
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
                        title: 'Supplier Harus dipilih!'
                    })
                return;
            }
            if(harga_jual.trim()=='')
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
                        title: 'Harga Jual Rekanan Harus Diisi!'
                    })
                return;
            }
            // if( parseFloat(harga_jual)<total_uang_jalan )
            // {
            //     event.preventDefault();
            //     const Toast = Swal.mixin({
            //             toast: true,
            //             position: 'top-end',
            //             timer: 2500,
            //             showConfirmButton: false,
            //             timerProgressBar: true,
            //             didOpen: (toast) => {
            //                 toast.addEventListener('mouseenter', Swal.stopTimer)
            //                 toast.addEventListener('mouseleave', Swal.resumeTimer)
            //             }
            //         })

            //         Toast.fire({
            //             icon: 'error',
            //             title: `Harga Jual Rekanan Harus lebih besar dari Rp. ${moneyMask(total_uang_jalan)}`
            //         })
            //     return;
            // }
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

        // $('body').on('change','#select_kendaraan',function()
		// {
        //     var selectedValue = $(this).val();
        //     var split = selectedValue.split("-");

        //     var idKendaraan = split[0];
        //     var idChassis = split[1];
        //     var nopol = split[2];
        //     var supir = split[3];


        //     console.log(idChassis);
        //     // kendaraan_id
        //     // no_polisi
        //     // select_chassis
        //     $('#kendaraan_id').val(idKendaraan);
        //     $('#no_polisi').val(nopol);
        //     $('#select_chassis').val(idChassis).trigger('change');
        //     $('#ekor_id').val(idChassis);
        //     $('#select_driver').val(supir).trigger('change');

		// });

        // $('body').on('change','#select_driver',function()
		// {
        //     var selectedValue = $(this).val();
        //     var split = selectedValue.split("-");

        //     var idKendaraan = split[0];
        //     var idChassis = split[1];
        //     var nopol = split[2];
        //     $('#driver_nama').val(idChassis);
		// });
   
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
