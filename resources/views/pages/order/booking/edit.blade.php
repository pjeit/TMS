
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
    <form action="{{ route('booking.update', ['booking' => $booking->id]) }}" method="POST" >
    @method('PUT')
    @csrf
        <div class="row">
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('booking.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    </div>
                    <div class="card-body">
                        <div class='row'>
                            <div class="form-group col-12 col-md-3 col-lg-3">
                                <label>No Booking </label>
                                <input type="text" name="no_booking" id="no_booking" class="form-control" readonly value="{{old('no_booking',$booking->no_booking) }}">    
                            </div>
                            <div class="form-group col-12 col-md-3 col-lg-3">
                                <label>Tgl Booking</label>
                                <div class="input-group mb-0 ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" name="tgl_booking" class="date form-control" id="tgl_booking" disabled value="{{old('tgl_booking',\Carbon\Carbon::parse($booking->tgl_booking)->format('d-M-Y')) }}">
                                </div>
                            </div>
                            <div class="form-group col-12 col-md-6 col-lg-6">
                                <label>Customer <span class="text-red">*</span></label>
                                <select class="form-control select2" style="width: 100%;" id='id_customer' name="id_customer" required>
                                    <option value="0">&nbsp;</option>
                                    @foreach ($customers as $item)
                                        <option value="{{$item->id}}" {{$booking->id_customer == $item->id? 'selected' :'' }}>{{ $item['kode'] }} - {{ $item['nama'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="form-group col-12 col-md-4 col-lg-4">
                                <label>No Kontainer </label>
                                <input type="text" name="no_kontainer" id="no_kontainer" class="form-control" value="{{old('no_booking',$booking->no_kontainer) }}">    
                            </div> --}}
                            {{-- <div class="form-group col-12 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>Tgl Berangkat <span class="text-red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" name="tgl_berangkat" autocomplete="off" class="date form-control" id="tgl_berangkat" placeholder="dd-M-yyyy" value="{{old('tgl_berangkat',\Carbon\Carbon::parse($booking->tgl_berangkat)->format('d-M-Y')) }}">     
                                    </div>
                                </div>
                            </div> --}}
                        </div> 
                        <div class='row'>
                            
                            <div class="form-group col-12 col-md-6 col-lg-6">
                                <label>Catatan </label>
                            <textarea type="text" name="catatan" id="catatan" class="form-control" cols="30" rows="3">{{old('no_booking',$booking->catatan) }}</textarea>
                                {{-- <input type="text" name="catatan" id="catatan" class="form-control" value="{{old('no_booking',$booking->catatan) }}">     --}}

                            </div>
                            
                            <div class="form-group col-12 col-md-6 col-lg-6">
                                <label>Tujuan <span class="text-red">*</span></label>
                                <select id="id_tujuan" name="id_tujuan" style="width: 100%" class="select2"></select>
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
        var selectedValue = $('#id_customer').val();
        var datacUST = <?php echo json_encode($booking); ?>;

          $.ajax({
                url: '/booking/getTujuan/'+ selectedValue, 
                method: 'GET', 
                success: function(response) {
                    $('#result').html(response); 
                    var tujuanSelect = $('#id_tujuan');
                    tujuanSelect.empty(); // Clear previous options

                    response.forEach(tujuan => {
                        //option itu create element option
                        const option = document.createElement('option');
                        option.value = tujuan.id;
                        option.textContent = tujuan.nama_tujuan;
                        if (datacUST.id_grup_tujuan == tujuan.id) {
                            option.selected = true;
                        }
                        id_tujuanSelect.appendChild(option);
                    });

                    // Trigger change event to refresh select2
                    tujuanSelect.trigger('change');
            
                    // var kode = $('#select2-id_customer-container').text();
                    // kode  = kode.substring(0, 3);
                    // //trim untuk ngilangin spasi
                    // $('#kode_cust').val(kode).trim();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        

        $('#id_customer').on('change', function() {
            var selectedValue = $(this).val();
      
            $.ajax({
                url: '/booking/getTujuan/'+selectedValue, 
                method: 'GET', 
                success: function(response) {
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
            
                    // var kode = $('#select2-id_customer-container').text();
                    // kode  = kode.substring(0, 3);
                    // //trim untuk ngilangin spasi
                    // $('#kode_cust').val(kode).trim();
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
            todayHighlight: false,
            language:'en',
        });
        $('#tgl_berangkat').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
        });
    });
   
</script>
@endsection
