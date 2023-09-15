
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
                        <a href="{{ route('truck_order.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
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
                                                <option value="{{$jo->id}}-{{$jo->id_customer}}" {{$jo->id == $data['id_jo']? 'selected':''}}>{{ $jo->no_bl }}</option>
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
                                <div class="form-group">
                                        <label for="no_sewa">No. Sewa</label>
                                        <input type="text" class="form-control" id="no_sewa" placeholder="Otomatis" readonly="" value="{{$data["no_sewa"]}}">    
                                        <input type="hidden" id="status" value="">
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
                                        <input type="text" name="catatan" class="form-control" id="catatan" name="catatan" placeholder="" value="{{$data['catatan']}}"> 
                                    </div>
                            </div>
                            <div class="col-6">
                               
                                <div class="form-group">
                                    <label for="select_customer">Customer<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_customer' name="select_customer" disabled>
                                        <option value="">Pilih Customer</option>
                                        @foreach ($dataCustomer as $cust)                                        
                                            <option value="{{$cust->idCustomer}}" <?= $cust->idCustomer==$data['id_customer']? 'selected':''  ?> > {{ $cust->kodeCustomer }} - {{ $cust->namaCustomer }} / {{ $cust->namaGrup }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="customer_id" name="customer_id" value="" placeholder="customer_id">
                                    <input type="hidden" id="booking_id" name="booking_id" value="" placeholder="booking_id">
                                    <input type="hidden" id="jenis_order" name="jenis_order" value="{{$data['jenis_order']}}" placeholder="jenis_order">
                                </div>
                                <div class="form-group">
                                    <label for="select_tujuan">Tujuan<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_grup_tujuan' name="select_grup_tujuan" disabled>
                                        @isset($data['id_grup_tujuan'])
                                            <option value="{{$data['id_grup_tujuan']}}">{{$data->getTujuan->nama_tujuan}}</option>
                                        @endisset
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
                              
                                <div class="form-group">
                                    <label for="select_ekor">Chassis<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='select_chassis' name="select_chassis">
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
                                        <select class="form-control select2" style="width: 100%;" id='select_driver' name="select_driver">
                                        <option value="">Pilih Driver</option>

                                        @foreach ($dataDriver as $drvr)
                                            <option value="{{$drvr->id}}" {{$drvr->id==$data['id_karyawan']? 'selected':''}}>{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})</option>
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
        var selectedJO = $('#select_jo').val();
        // console.log('splitValuex '+ selectedJO.length);
        if(selectedJO > 0){
            var splitValue = selectedJO.split('-');
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
    
        // logic ganti kendaraan
            // var selectedKendaraan = $('#select_kendaraan').val();
            // if(selectedKendaraan != null){
            //     changeKendaraan(selectedKendaraan);
            // }
            setValKendaraan();
            
            $('#select_kendaraan').on("select2:select", function(e) { 
                var selectedKendaraan = $('#select_kendaraan').val();
                changeKendaraan(selectedKendaraan);
            });

            function changeKendaraan(selectedKendaraan){
                var split = selectedKendaraan.split("-");
                var idKendaraan = split[0];
                var idChassis = split[1];
                var nopol = split[2];
                var supir = split[3];
                console.log(split);
                setValKendaraan();
                
                $('#select_chassis').val(idChassis).trigger('change');
                $('#select_driver').val(supir).trigger('change');
            }
            function setValKendaraan(){
                var selectedKendaraan = $('#select_kendaraan').val();
                var split = selectedKendaraan.split("-");
                var idKendaraan = split[0];
                var idChassis = split[1];
                var nopol = split[2];
                var supir = split[3];

                $('#kendaraan_id').val(idKendaraan);
                $('#no_polisi').val(nopol);
                $('#ekor_id').val(idChassis);
            }
        //


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
