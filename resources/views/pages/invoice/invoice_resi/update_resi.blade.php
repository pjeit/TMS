@extends('layouts.home_master')

@if(session()->has('message'))
<div class="alert alert-success alert-dismissible">
    {{ session()->get('message') }}
</div>
@endif

@section('pathjudul')
@endsection

@section('content')
<style>
    .shipment-status {
      margin-top: 50px;
    }
    .status-title {
      font-weight: bold;
      font-size: 1.2rem;
    }
    .shipment-list {
      list-style-type: none;
    }
    .shipment-list li {
      position: relative;
      padding-left: 20px;
      margin-bottom: 10px;
    }
    .shipment-list li::before {
      content: "";
      position: absolute;
      top: 5px;
      left: 0;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: black;
    }
  </style>
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
<section class="container-fluid">
    <form action="{{ route('invoice_resi.update_resi',[$resi->id]) }}" id="save" method="POST">
        @csrf
        <div class="card radiusSendiri">
            <div class="card-header radiusSendiri sticky-top">
                <a href="{{ route('invoice_resi.index') }}" class="btn btn-secondary radiusSendiri"><i
                        class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i
                        class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body radiusSendiri">
                    <div class="row">
                    
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="">Tanggal Resi</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" autocomplete="off" name="tgl_resi" class="form-control date" value="{{date('d-M-Y',strtotime($resi->tanggal_resi)) }}" id="tgl_nota" disabled>
                            </div>
                        </div>

                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="refund">Kurir</label>
                            <select class="form-control select2" id="jenis_pengiriman" name="jenis_pengiriman" data-live-search="true" data-show-subtext="true" data-placement="bottom" width="100" disabled>
                                <option value="tiki" {{$resi->jenis_pengiriman?'tiki':'selected'}}>TIKI</option>
                                <option value="lion" {{$resi->jenis_pengiriman?'lion':'selected'}}>LION PARCEL</option>
                                <option value="jne" {{$resi->jenis_pengiriman?'jne':'selected'}}>JNE</option>
                                <option value="jnt" {{$resi->jenis_pengiriman?'jnt':'selected'}}>JNT</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="">No. Resi</label>
                            <input type="text" name="no_resi" id="no_resi" maxlength="12" class="form-control numaja"
                                readonly value="{{$resi->no_resi}}">
                        </div>
                    </div>
                <div style="overflow: auto;">
                    <table class="table table-hover table-bordered table-striped " width='100%' id="tabel_tagihan">
                        <thead>
                            <tr>
                                <th>Nomor Invoice</th>
                                <th>Tanggal Invoice</th>
                                <th>Billing to</th>
                                <th>Total Tagihan</th>
                                <th>Jatuh Tempo Lama</th>
                                <th>Jatuh Tempo Baru</th>
                            </tr>
                        </thead>
                        <tbody id="hasil">
                            @if (isset($resi->get_invoice_resi_detail))
                                @foreach ($resi->get_invoice_resi_detail as $key => $value)
                                    <tr id="{{$key}}">
                                        <td>{{$value->get_invoice->no_invoice}}</td>
                                        <td>{{ date('d-M-Y',strtotime($value->get_invoice->tgl_invoice))}}</td>
                                        <td>{{$value->get_invoice->getBillingTo->nama}}</td>
                                        <td>
                                            {{ number_format($value->get_invoice->total_tagihan)}}
                                            <input type="hidden" id="hidden_id_invoice_{{$key}}" value="{{$value->id}}" name="detail[{{$key}}][id_resi_detail]"/>
                                            <input type="hidden" id="hidden_id_invoice_{{$key}}" value="{{$value->id_invoice}}" name="detail[{{$key}}][id_invoice]"/>
                                            <input type="hidden" class="tgl_invoice" id="hidden_tgl_invoice_{{$key}}" value="{{$value->get_invoice->tgl_invoice}}"/>
                                            <input type="hidden" id="hidden_selisih_invoice_{{$key}}" value="0"/>
                                        </td>
                                        <td>{{date('d-M-Y',strtotime($value->jatuh_tempo_lama))}}</td>
                                        <td>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" autocomplete="off" name="detail[{{$key}}][jatuh_tempo_baru]" selisih='0' class="form-control jatuh_tempo_baru" value="{{date('d-M-Y',strtotime($value->jatuh_tempo_lama)) }}" id="jatuh_tempo_baru" required>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="container">
                    <ul id="history_kirim">
                        {{-- <li>
                            <div class="shipment-status">
                                <div class="status-title">Shipment</div>
                                <div class="card radiusSendiri">
                                  <div class="card-body">
                                     Shipment details here
                                  </div>
                                </div>
                              </div>
                        </li> --}}
                    </ul>
                </div>
            </div>
        </div>
    </form>
</section>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        get_api($('#jenis_pengiriman').val(),$('#no_resi').val());
        // var data = [
        //     {date: '2024-03-18 10:01:20', desc: 'ITEM HAS BEEN DELIVERED.', location: ''},
        //     {date: '2024-03-18 05:23:32', desc: 'WITH DELIVERY COURIER AT SAM RATULANGI [KENDARI]', location: ''},
        //     {date: '2024-03-16 01:06:45', desc: 'ARRIVED AT TIKI KENDARI', location: ''},
        //     {date: '2024-03-13 16:10:35', desc: 'DEPARTED [M] BY RDSUB01AKDI01A24030011 TO KENDARI', location: ''},
        //     {date: '2024-03-13 13:46:21', desc: 'DEPARTED FROM KEDUNGSARI [SURABAYA]', location: ''},
        //     {date: '2024-03-13 11:17:08', desc: 'SHIPMENT DATA ENTRY AT SURABAYA', location: ''}
        // ];
        $('.jatuh_tempo_baru').each(function () {
            var currentDate = new Date($(this).val());
            currentDate.setDate(currentDate.getDate() + 5);
            $(this).val(dateMask(currentDate))
        });
        // for (var i = data.length - 1; i >= 0; i--) {
        //                     $('#history_kirim').append(
        //                         `
        //                         <li>
        //                             <div class="shipment-status">
        //                                 <div class="card radiusSendiri">
        //                                 <div class="card-body">
        //                                    ${data[i].desc}
        //                                    <br>
        //                                    ${dateMask( data[i].date)}
        //                                 </div>
        //                                 </div>
        //                             </div>
        //                         </li>
        //                         `
        //                     )
        //                 }
        // // Extract the date strings from the first and last objects
        // var firstDateStr = data[0].date;
        // var lastDateStr = data[data.length - 1].date;

        // // Convert date strings to JavaScript Date objects
        // var firstDate = new Date(firstDateStr);
        // var lastDate = new Date(lastDateStr);

        // // Calculate the difference in milliseconds
        // var differenceMs = lastDate - firstDate;

        // // Convert milliseconds to days
        // var differenceDays = Math.floor(Math.abs( differenceMs / (1000 * 60 * 60 * 24)));

        // console.log('Difference in days:', differenceDays);

        function get_api(jenis_kirim,resi){
            var url =`https://api.binderbyte.com/v1/track?api_key=8f2d73a3e76e9494ce797068a3f7a8192504efe3114e950b2390c2e786d5d70c&courier=${jenis_kirim}&awb=${resi}`;
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    console.log(response);
                    if(response.status == 200){
                        var data = response.data;
                        var history = data.history;
                        var summary = data.summary;
                        for (var i = history.length - 1; i >= 0; i--) {
                            $('#history_kirim').append(
                                `
                                <li>
                                    <div class="shipment-status">
                                        <div class="card radiusSendiri">
                                        <div class="card-body">
                                           ${history[i].desc}
                                        </div>
                                        </div>
                                    </div>
                                </li>
                                `
                            )
                        }

                        if(summary.status == "DELIVERED")
                        {
                            var tanggal_sampai = new Date(summary.date);

                        }
                        
                    }
                
                },error: function (xhr, status, error) {
                    if ( xhr.responseJSON.result == 'error') {
                        console.log("Error:", xhr.responseJSON.message);
                        console.log("XHR status:", status);
                        console.log("Error:", error);
                        console.log("Response:", xhr.responseJSON);
                    } else {
                        toastr.error("Terjadi kesalahan saat menerima data. " + error);
                    }
                }
            });
        }
        $('#save').submit(function(event) {

            event.preventDefault(); // Prevent form submission
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
        // $(document).on('change', '#supplier', function(){
        //     if(this.value != null){
        //         showTable(this.value);
        //     }
        //     $('#tagihan').val(0);

        // });
        
        var today = new Date();
        $('#tgl_nota').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            // startDate: today,
        });
        $('#jatuh_tempo').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            // startDate: today,
        });
    });
</script>

@endsection