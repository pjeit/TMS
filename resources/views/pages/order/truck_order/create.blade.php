
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
    <div class="row ">
        <div class="col-6 text-center">
            <a href="https://cyclist.greenonthel.com/history/ongoingHistory" class="rubik-heading-2" style="text-decoration: none; color:black;">
                Bongkar (INBOUND)
            </a>
            <br>
            <div class="bottom-nav-indicator active-green" ></div>
        </div>

        <div class="col-6 text-center">
            <a href="https://cyclist.greenonthel.com/history/orderHistory" class="rubik-heading-2" style="text-decoration: none; color:black;">
                Muat (OUTBOND)
            </a>
            <br>
            <div class="bottom-nav-indicator " ></div>
        </div>
    </div>
    {{-- <div class="row mt-2">
        <div class="col">
            <div class="card radiusSendiri">
                <div class="card-body">
                    <label for="credit_customer">Credit Customer</label>
                 
                    <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="" aria-valuemin="100" aria-valuemax="100" name="credit_customer" id="credit_customer" style="width: 50%; color: black;"></div>
                    </div>
                    <div class="d-flex justify-content-center mt-1">
                        <span class="rubik-w400-12">50%</span>
                    </div>
                    <input type="hidden" name="cred_now" id="cred_now" class="form-control" value="0">
                    <input type="hidden" name="cred_val" id="cred_val" class="form-control" value="0">
                    <input type="hidden" name="cred_val_max" id="cred_val_max" class="form-control" value="0">
                </div>
            </div>
        </div>
    </div> --}}
    <div class="row mt-2">
        <div class="col-lg-6 col-md-6 col-12" data-select2-id="select2-data-17-6c7n">
            <div class="card" data-select2-id="select2-data-16-ni8k">
              
              <div class="card-body" data-select2-id="select2-data-15-17ec">
                  <div class="form-group" data-select2-id="select2-data-14-0uwz">
                    <label for="select_booking">No. Booking</label>
                    <select id="select_booking" style="width:100%" data-placeholder="Pilih No. Booking (Optional)" data-select2-id="select2-data-select_booking" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                        <option value="" data-select2-id="select2-data-2-1tn4"></option>
                                            </select><span class="select2 select2-container select2-container--default select2-container--below" dir="ltr" data-select2-id="select2-data-1-rtpp" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-select_booking-container"><span class="select2-selection__rendered" id="select2-select_booking-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Pilih No. Booking (Optional)</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                    <input type="hidden" id="booking_id" name="booking_id" value="">
                  </div>
                  <div class="form-group" data-select2-id="select2-data-14-0uwz">
                    <label for="select_jo">No. Job Order</label>
                    <select id="select_jo" style="width:100%" data-placeholder="Pilih No. JO (Optional)" data-select2-id="select2-data-select_jo" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                        <option value="" data-select2-id="select2-data-2-1tn4"></option>
                                            </select><span class="select2 select2-container select2-container--default select2-container--below" dir="ltr" data-select2-id="select2-data-1-rtpp" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-select_booking-container"><span class="select2-selection__rendered" id="select2-select_booking-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Pilih No. Booking (Optional)</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                    <input type="hidden" id="jo_id" name="jo_id" value="">
                  </div>
                  <div class="form-group" data-select2-id="select2-data-14-0uwz">
                    <label for="select_jo_detail">No. Kontainer</label>
                    <select id="select_jo_detail" style="width:100%" data-placeholder="Pilih kontainer (Optional)" data-select2-id="select2-data-select_jo_detail" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                        <option value="" data-select2-id="select2-data-2-1tn4"></option>
                                            </select><span class="select2 select2-container select2-container--default select2-container--below" dir="ltr" data-select2-id="select2-data-1-rtpp" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-select_booking-container"><span class="select2-selection__rendered" id="select2-select_booking-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Pilih No. Booking (Optional)</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                    <input type="hidden" id="jo_detail_id" name="jo_detail_id" value="">
                  </div>
                  <div class="form-group">
                    <div class="row">
                        <div class="col-7 col-md-7 col-lg-7">
                            <label for="no_sewa">No. Sewa</label>
                            <input type="text" class="form-control" id="no_sewa" placeholder="Otomatis" readonly="" value="">    
                        </div>
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
                    <input type="text" name="catatan" class="form-control" id="catatan" placeholder="" value=""> 
                  </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
              <div class="card-body">
			  	<div class="form-group">
                    <label for="credit_customer">Credit Customer</label>
                    <div class="progress">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="" aria-valuemin="100" aria-valuemax="100" name="credit_customer" id="credit_customer" style="width: 50%; color: black;"></div>
                    </div>
                    <div class="d-flex justify-content-center mt-1">
                        <span class="rubik-w400-12">50%</span>
                    </div>
                    <input type="hidden" name="cred_now" id="cred_now" class="form-control" value="0">
                    <input type="hidden" name="cred_val" id="cred_val" class="form-control" value="0">
                    <input type="hidden" name="cred_val_max" id="cred_val_max" class="form-control" value="0">
				</div>
				<div class="form-group">
					<label for="select_customer">Customer<span style="color:red">*</span></label>
					<select id="select_customer" style="width:100%" data-placeholder="Pilih Customer" data-select2-id="select2-data-select_customer" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
						<option value="" data-select2-id="select2-data-4-qmrt"></option>
											</select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="select2-data-3-2gaj" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-select_customer-container"><span class="select2-selection__rendered" id="select2-select_customer-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Pilih Customer</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
					<input type="hidden" id="customer_id" name="customer_id" value="">
				</div>
                  <div class="form-group">
                    <label for="select_tujuan">Tujuan<span style="color:red">*</span></label>
                    <select id="select_tujuan" style="width:100%" data-placeholder="Pilih Tujuan" data-select2-id="select2-data-select_tujuan" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                        <option value="" data-select2-id="select2-data-6-pf66"></option>
                                            </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="select2-data-5-8wc2" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-select_tujuan-container"><span class="select2-selection__rendered" id="select2-select_tujuan-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Pilih Tujuan</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                    <input type="hidden" id="tujuan_id" name="tujuan_id" value="">
                    <input type="hidden" id="nama_tujuan" name="nama_tujuan" value="">
                    <input type="hidden" id="alamat_tujuan" name="alamat_tujuan" value="">
                    <input type="hidden" id="tarif" name="tarif" value="">
                    <input type="hidden" id="uang_jalan" name="uang_jalan" value="">
                    <input type="hidden" id="komisi" name="komisi" value="">
					<input type="hidden" id="jenis_tujuan" name="jenis_tujuan" value="">
					<input type="hidden" id="harga_per_kg" name="harga_per_kg" value="0">
					<input type="hidden" id="min_muatan" name="min_muatan" value="0">
                    
                  </div>
                  <div class="form-group">
                    <label for="select_kendaraan">Kendaraan<span style="color:red">*</span></label>
                    <select id="select_kendaraan" name="kendaraan_id" style="width:100%" data-placeholder="Pilih Kendaraan" data-select2-id="select2-data-select_kendaraan" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                        <option value="" data-select2-id="select2-data-8-pm4y"></option>
                                            </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="select2-data-7-jvk3" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-select_kendaraan-container"><span class="select2-selection__rendered" id="select2-select_kendaraan-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Pilih Kendaraan</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                    <input type="hidden" id="kendaraan_id" name="kendaraan_id" value="">
                  </div>
                  <div class="form-group">
                    <label for="select_ekor">Chassis<span style="color:red">*</span></label>
                    <select id="select_ekor" name="ekor_id" style="width:100%" data-placeholder="Pilih Chassis" data-select2-id="select2-data-select_ekor" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                        <option value="" data-select2-id="select2-data-10-ekow"></option>
                                            </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="select2-data-9-coo5" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-select_ekor-container"><span class="select2-selection__rendered" id="select2-select_ekor-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Pilih Chassis</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                    <input type="hidden" id="ekor_id" name="ekor_id" value="">
                  </div>
                  <div class="form-group">
                    <label for="select_driver">Driver<span style="color:red">*</span></label>
                    <select id="select_driver" style="width:100%" data-placeholder="Pilih Driver" data-select2-id="select2-data-select_driver" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                        <option value="" data-select2-id="select2-data-12-2hc0"></option>
                                            </select><span class="select2 select2-container select2-container--default" dir="ltr" data-select2-id="select2-data-11-ky7n" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-select_driver-container"><span class="select2-selection__rendered" id="select2-select_driver-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Pilih Driver</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                    <input type="hidden" id="driver_id" name="driver_id" value="">
                  </div>
                </div>
            </div>
        </div>
    </div>
    </form>

</div>
<script>
    $(document).ready(function() {
        const id_tujuanSelect = document.getElementById('id_tujuan');

        $('#id_customer').on('change', function() {
            var selectedValue = $(this).val();
      
            $.ajax({
                url: '/booking/getTujuan/'+selectedValue, 
                method: 'GET', 
                success: function(response) {
                    console.log(response);
                    $('#result').html(response); 
                    var tujuanSelect = $('#id_tujuan');
                    tujuanSelect.empty(); // Clear previous options

                    response.forEach(tujuan => {
                        const option = document.createElement('option');
                        option.value = tujuan.id;
                        option.textContent = tujuan.nama_tujuan;
                        // if (selected_marketing == marketing.id) {
                        //     option.selected = true;
                        // }
                        id_tujuanSelect.appendChild(option);
                    });

                    // Trigger change event to refresh select2
                    tujuanSelect.trigger('change');
            
                    var kode = $('#select2-id_customer-container').text();
                    kode  = kode.substring(0, 3);
                    //trim untuk ngilangin spasi
                    $('#kode_cust').val(kode).trim();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

        var today = new Date();
        var day = String(today.getDate()).padStart(2, '0');
        var month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
        var year = today.getFullYear();
        var formattedDate = year + '-' + month + '-' + day;

        // $('#tgl_booking').datepicker({
        //         format:'dd-M-yyyy',
        // }).datepicker("setDate",'now');

        $('#tgl_booking').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
        });
    });
   
</script>
@endsection
