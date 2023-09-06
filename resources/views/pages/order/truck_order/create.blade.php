
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
                {{-- <a href="" class="rubik-heading-2" style="text-decoration: none; color:black;">
                    Bongkar (INBOUND)
                </a> --}}
                <label class="rubik-heading-2">Bongkar (INBOUND)</label>
                <br>
                <div class="bottom-nav-indicator active-green" ></div>
                <hr style="border: 1px solid #007bff;">

            </div>

            <div class="col-6 text-center">
                {{-- <a href="" class="rubik-heading-2" style="text-decoration: none; color:black;">
                    Muat (OUTBOND)
                </a> --}}
                <label class="rubik-heading-2">Muat (OUTBOND)</label>
                <br>
                <div class="bottom-nav-indicator " ></div>
                <hr style="border: 1px solid #007bff; display:none;">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card radiusSendiri">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
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
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">No.Booking</label>
                                    <select class="form-control select2" style="width: 100%;" id='id_book' name="id_book">
                                        <option value="">Pilih No Booking</option>
                
                                        {{-- @foreach ($kota as $city)
                                            <option value="{{$city->id}}">{{ $city->nama }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>  
                                <div class="form-group">
                                    <label for="">No. Job Order</label>
                                    <select class="form-control select2" style="width: 100%;" id='id_jo' name="id_jo">
                                        <option value="">Pilih No JO</option>
                
                                        {{-- @foreach ($kota as $city)
                                            <option value="{{$city->id}}">{{ $city->nama }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>  
                                <div class="form-group">
                                    <label for="">No. Kontainer</label>
                                    <select class="form-control select2" style="width: 100%;" id='id_jo_detail' name="id_jo_detail">
                                        <option value="">Pilih Kontainer</option>
                
                                        {{-- @foreach ($kota as $city)
                                            <option value="{{$city->id}}">{{ $city->nama }}</option>
                                        @endforeach --}}
                                    </select>
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
                                    <div class="form-group">
                                        <label for="catatan">Catatan</label>
                                        <input type="text" name="catatan" class="form-control" id="catatan" placeholder="" value=""> 
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-6">
                               
                                <div class="form-group">
                                    <label for="select_customer">Customer<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='id_customer' name="id_customer">
                                        <option value="">Pilih Customer</option>

                                        {{-- @foreach ($kota as $city)
                                            <option value="{{$city->id}}">{{ $city->nama }}</option>
                                        @endforeach --}}
                                    </select>
                                    <input type="hidden" id="customer_id" name="customer_id" value="">
                                </div>
                                <div class="form-group">
                                    <label for="select_tujuan">Tujuan<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='id_grup_tujuan' name="id_grup_tujuan">
                                        <option value="">Pilih Tujuan</option>

                                        {{-- @foreach ($kota as $city)
                                            <option value="{{$city->id}}">{{ $city->nama }}</option>
                                        @endforeach --}}
                                    </select>
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
                                    <select class="form-control select2" style="width: 100%;" id='id_kendaraan' name="id_kendaraan">
                                        <option value="">Pilih Kendaraan</option>

                                        {{-- @foreach ($kota as $city)
                                            <option value="{{$city->id}}">{{ $city->nama }}</option>
                                        @endforeach --}}
                                    </select>
                                    <input type="hidden" id="kendaraan_id" name="kendaraan_id" value="">
                                </div>
                                <div class="form-group">
                                    <label for="select_ekor">Chassis<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='id_chassis' name="id_chassis">
                                        <option value="">Pilih Chassis</option>

                                        {{-- @foreach ($kota as $city)
                                            <option value="{{$city->id}}">{{ $city->nama }}</option>
                                        @endforeach --}}
                                    </select>
                                    <input type="hidden" id="ekor_id" name="ekor_id" value="">
                                </div>
                                <div class="form-group">
                                    <label for="select_driver">Driver<span style="color:red">*</span></label>
                                        <select class="form-control select2" style="width: 100%;" id='id_driver' name="id_driver">
                                        <option value="">Pilih Kendaraan</option>

                                        {{-- @foreach ($kota as $city)
                                            <option value="{{$city->id}}">{{ $city->nama }}</option>
                                        @endforeach --}}
                                    </select>
                                    <input type="hidden" id="driver_id" name="driver_id" value="">
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
        const id_tujuanSelect = document.getElementById('id_tujuan');
        $('#tanggal_berangkat').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
        });
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
