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
  .events li { 
  display: flex; 
  color: #474646;
}

.events time { 
  position: relative;
  padding: 0 1.5em;  }

.events time::after { 
   content: "";
   position: absolute;
   z-index: 2;
   right: 0;
   top: 0;
   transform: translateX(50%);
   border-radius: 50%;
   background: #fff;
   border: 1px #3e30ff solid;
   width: .8em;
   height: .8em;
}


.events span {
  padding: 0 1.5em 1.5em 1.5em;
  position: relative;
}

.events span::before {
   content: "";
   position: absolute;
   z-index: 1;
   left: 0;
   height: 100%;
   border-left: 1px #ccc solid;
}

.events strong {
   display: block;
   font-weight: bolder;
}

.events { margin: 1em; width: 50%; }
.events, 
.events *::before, 
.events *::after { box-sizing: border-box; font-family: arial; }
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
                @if ($resi->status_resi=="DALAM PROSES")
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i
                                class="fa fa-fw fa-save" ></i> Simpan</button>
                        <button type="button" id="cek_resi" class="btn btn-primary radiusSendiri ml-2"><i
                            class="fa fa-fw fa-info"></i> Cek Resi</button>
                @endif
            </div>
            <div class="card-body radiusSendiri">
                    <div class="row">
                    
                        <div class="form-group col-lg-3 col-md-3 col-sm-12">
                            <label for="">Tanggal Resi</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" autocomplete="off" name="tgl_resi" class="form-control date_resi" value="{{date('d-M-Y',strtotime($resi->tanggal_resi)) }}" id="tgl_nota" disabled>
                            </div>
                        </div>

                        <div class="form-group col-lg-3 col-md-3 col-sm-12">
                            <label for="refund">Kurir</label>
                            <select class="form-control select2" id="jenis_pengiriman" name="jenis_pengiriman" data-live-search="true" data-show-subtext="true" data-placement="bottom" width="100" disabled>
                                <option value="tiki" {{$resi->jenis_pengiriman?'tiki':'selected'}}>TIKI</option>
                                <option value="lion" {{$resi->jenis_pengiriman?'lion':'selected'}}>LION PARCEL</option>
                                <option value="jne" {{$resi->jenis_pengiriman?'jne':'selected'}}>JNE</option>
                                <option value="jnt" {{$resi->jenis_pengiriman?'jnt':'selected'}}>JNT</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-3 col-md-3 col-sm-12">
                            <label for="">No. Resi</label>
                            <input type="text" name="no_resi" id="no_resi" maxlength="12" class="form-control numaja"
                                readonly value="{{$resi->no_resi}}">
                        </div>
                        <div class="form-group col-lg-3 col-md-3 col-sm-12">
                            <label for="">Status Resi</label>
                            <input type="text" name="status" id="status" maxlength="12" class="form-control numaja"
                                readonly value="{{$resi->status_resi}}">
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
                                            <input type="hidden" id="hidden_id_detail_{{$key}}" value="{{$value->id}}" name="detail[{{$key}}][id_resi_detail]"/>
                                            <input type="hidden" id="hidden_id_invoice_{{$key}}" value="{{$value->id_invoice}}" name="detail[{{$key}}][id_invoice]"/>
                                            <input type="hidden" class="tgl_invoice" id="hidden_tgl_invoice_{{$key}}"  value="{{$value->get_invoice->tgl_invoice}}"/>
                                        </td>
                                        <td>{{date('d-M-Y',strtotime($value->jatuh_tempo_lama))}}
                                            <input type="hidden" autocomplete="off"  class="form-control jatuh_tempo_lama" value="{{date('d-M-Y',strtotime($value->jatuh_tempo_lama)) }}" id="jatuh_tempo_baru" required {{$resi->status_resi=="DITERIMA"?"disabled":'' }}>
                                        </td>
                                        <td>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" autocomplete="off" name="detail[{{$key}}][jatuh_tempo_baru]" class="form-control jatuh_tempo_baru" value="{{date('d-M-Y',strtotime($value->jatuh_tempo_baru?$value->jatuh_tempo_baru:$value->jatuh_tempo_lama)) }}" id="jatuh_tempo_baru" required>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    
                </div>
               
            </div>
            
        </div>
        @if ($resi->status_resi=="DALAM PROSES")
            <div class="row">
                <div class="col-6">
                    <h5> <b>Order History:</b> </h5>
                </div>
                <div class="col-6">
                    <h5> <b>Status Resi:</b> </h5>
                </div>
                
            </div>
            <div class="row">
                <div class="col-12">
                    <div id="loading-spinner" ><i class="fas fa-spinner fa-spin"></i> Harap tunggu data sedang di proses...</div>
            </div>
                <div class="col-6">
                    <ul id="history_kirim" class="events">
                    
                    </ul>
                </div>
                <div class="col-6">
                    <div class="order_history">
                    
                    </div>
                </div>
                
            </div>
        @endif
    </form>
</section>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('#loading-spinner').hide();
        cek_save();
        
        $(document).on('click', '#cek_resi', function(e){   
             get_api($('#jenis_pengiriman').val(),$('#no_resi').val());
            // get_api_dummy();
            $('#cek_resi').attr('disabled',true);
            cek_save();
        });

        function cek_save(){
            if( $('#status').val()=="DITERIMA")
            {
                $('#submitButton').attr('disabled',false);
            }
            else
            {
                $('#submitButton').attr('disabled',true);
            }
        }
        function get_api(jenis_kirim,resi){
            var url =`https://api.binderbyte.com/v1/track?api_key=8f2d73a3e76e9494ce797068a3f7a8192504efe3114e950b2390c2e786d5d70c&courier=${jenis_kirim}&awb=${resi}`;
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function () {
                    // Show loading spinner before the request is sent
                    $('#loading-spinner').show();
                },
                complete: function () {
                    // Hide loading spinner when the request is complete
                    $('#loading-spinner').hide();
                },
                success: function(response) {
                    console.log(response);
                    if(response.status == 200){
                        $('#history_kirim').empty();
                        $('.order_history').empty();
                        var data = response.data;
                        var history = data.history;
                        var summary = data.summary;
                        var detail = data.detail;
                        for (var i = history.length - 1; i >= 0; i--) {
                            
                            $('#history_kirim').append(
                                `
                                <li>
                                    <time></time> 
                                    <span><strong>${history[i].desc} </strong> ${dateMask(history[i].date)}</span>
                                </li>
                                   
                                `
                            )
                        }

                        $('.order_history').append(
                            `
                                <p>
                                    ${summary.status == "DELIVERED" ? '<span class="badge badge-success">Diterima Customer</span>' : ' <span class="badge badge-secondary">'+summary.status+'</span>'}
                                </p>
                                <p>
                                    Tanggal Status :  ${dateMask(summary.date)}
                                </p>
                                <p>
                                    Dikirim Dari :  ${detail.origin}
                                </p>
                                <p>
                                    Tujuan :  ${detail.destination}
                                </p>
                               
                                <p>
                                    Pengirim :  ${detail.shipper}
                                </p>
                                <p>
                                    Penerima :  ${detail.receiver?detail.receiver:''}
                                </p>
                            `
                        )
                        if(summary.status == "DELIVERED")
                        {
                            $('#status').val('DITERIMA');
                             cek_save();
                            var tanggal_sampai = new Date(summary.date);
                            var selisih_array = [];
                            //buat dapetin tanggal invoice - tanggal sampai dokumen
                            $('.tgl_invoice').each(function () {
                                var tgl_invoice = new Date($(this).val());
                                var selisih_tanggal = Math.ceil(( tanggal_sampai - tgl_invoice) / (1000 * 60 * 60 * 24)) ; // Convert difference to days
                                selisih_array.push(selisih_tanggal);
                            });
                            if(selisih_array.length>0)
                            {
                                // $('.jatuh_tempo_baru').each(function () {
                                //     var currentDate = new Date($(this).val());
                                //     currentDate.setDate(currentDate.getDate() + 5);
                                //     $(this).val(dateMask(currentDate))
                                // });
                                var jatuh_tempo_baru_elements = $('.jatuh_tempo_baru');
                                var jatuh_tempo_lama_elements = $('.jatuh_tempo_lama');

                                for (var i = 0; i < jatuh_tempo_baru_elements.length; i++) {
                                    var tempo_lama = new Date($(jatuh_tempo_lama_elements[i]).val());
                                    var tempo_baru = new Date($(jatuh_tempo_baru_elements[i]).val());
                                    if(tempo_lama<tempo_baru.setDate(tempo_baru.getDate() + selisih_array[i]))
                                    {
                                        tempo_baru.setDate(tempo_baru.getDate() + selisih_array[i]);
                                        $(jatuh_tempo_baru_elements[i]).val(dateMask(tempo_baru));
                                    }
                                }
                            }
                            console.log(selisih_array);
                        }
                        
                    }
                
                },error: function (xhr, status, error) {
                    if ( xhr.responseJSON.result == 'error') {
                        console.log("Error:", xhr.responseJSON.message);
                        console.log("XHR status:", status);
                        console.log("Error:", error);
                        console.log("Response:", xhr.responseJSON);
                    } else {
                        toastr.error("Terjadi kesalahan saat menerima data. " + xhr.responseJSON.message);
                        $('#history_kirim').empty();
                        $('.order_history').empty();
                        $('.order_history').append(
                            `
                                <p>
                                    Terjadi kesalahan saat menerima data harap hubungi IT : ${xhr.responseJSON.message}
                                </p>
                            `
                        )
                    }
                }
            });
        }

        function get_api_dummy()
        {
            $('#loading-spinner').show();
            $('#history_kirim').empty();
            $('.order_history').empty();
            var response = 
            {
                status: 200,
                message: "Successfully tracked package",
                data: {
                    summary: {
                        awb: "600008413419",
                        courier: "Tiki",
                        service: "",
                        status: "DELIVERED",
                        date: "2024-03-18 10:01:20",
                        desc: "",
                        amount: "",
                        weight: ""
                    },
                    detail: {
                        origin: "JALAN IKAN MUNGSING VII NO 61 SURABAYA",
                        destination: "JL. MANUNGGAL PERUM HARAPAN INDAH B/7",
                        shipper: "PRIMATRANS JAYA EXPRESS PT",
                        receiver: "BP. ARIYADI"
                    },
                    history: [
                    {
                        date: "2024-03-18 10:01:20",
                        desc: "ITEM HAS BEEN DELIVERED.",
                        location: ""
                    },
                    {
                        date: "2024-03-18 05:23:32",
                        desc: "WITH DELIVERY COURIER AT SAM RATULANGI [KENDARI]",
                        location: ""
                    },
                    {
                        date: "2024-03-16 01:06:45",
                        desc: "ARRIVED AT TIKI KENDARI",
                        location: ""
                    },
                    {
                        date: "2024-03-13 16:10:35",
                        desc: "DEPARTED [M] BY RDSUB01AKDI01A24030011 TO KENDARI",
                        location: ""
                    },
                    {
                        date: "2024-03-13 13:46:21",
                        desc: "DEPARTED FROM KEDUNGSARI [SURABAYA]",
                        location: ""
                    },
                    {
                        date: "2024-03-13 11:17:08",
                        desc: "SHIPMENT DATA ENTRY AT SURABAYA",
                        location: ""
                    }
                    ]
                }
            }
            ;
            // $('.jatuh_tempo_baru').each(function () {
            //     var currentDate = new Date($(this).val());
            //     currentDate.setDate(currentDate.getDate() + 5);
            //     $(this).val(dateMask(currentDate))
            // });
            if(response)
            {
                setTimeout(() => {
                    var data = response.data;
                    var history = data.history;
                    var summary = data.summary;

                    // console.log(history);
                    for (var i = history.length - 1; i >= 0; i--) {
                        $('#history_kirim').append(
                            `
                                <li>
                                    <time></time> 
                                    <span><strong>${history[i].desc} </strong> ${dateMask(history[i].date)}</span>
                                </li>
                            `
                        )
                    }
                    
                    $('.order_history').append(
                        `
                            <p>
                                ${summary.status == "DELIVERED" ? '<span class="badge badge-success">Diterima Customer</span>' : ' <span class="badge badge-secondary">'+summary.status+'</span>'}
                            </p>
                            <p>
                                Tanggal Status :  ${dateMask(summary.date)}
                            </p>
                        `
                    )
                    if(summary.status == "DELIVERED")
                    {

                         $('#status').val('DITERIMA');
                         cek_save();
                        var tanggal_sampai = new Date(summary.date);
                        var selisih_array = [];
                        //buat dapetin tanggal invoice - tanggal sampai dokumen
                        $('.tgl_invoice').each(function () {
                            var tgl_invoice = new Date($(this).val());
                            var selisih_tanggal = Math.ceil(( tanggal_sampai - tgl_invoice) / (1000 * 60 * 60 * 24)) ; // Convert difference to days
                            selisih_array.push(selisih_tanggal);
                        });
                        if(selisih_array.length>0)
                        {
                            // $('.jatuh_tempo_baru').each(function () {
                            //     var currentDate = new Date($(this).val());
                            //     currentDate.setDate(currentDate.getDate() + 5);
                            //     $(this).val(dateMask(currentDate))
                            // });
                            var jatuh_tempo_baru_elements = $('.jatuh_tempo_baru');
                            for (var i = 0; i < jatuh_tempo_baru_elements.length; i++) {
                                var currentDate = new Date($(jatuh_tempo_baru_elements[i]).val());
                                currentDate.setDate(currentDate.getDate() + selisih_array[i]);
                                $(jatuh_tempo_baru_elements[i]).val(dateMask(currentDate));
                            }
                        }
                        console.log(selisih_array);
                    }
                    $('#loading-spinner').hide();
                }, 2000);
               
            }
            else
            {
                $('#history_kirim').append(
                            `
                                <li>
                                    Tidak ada data
                                </li>
                            `
                        )
                $('.order_history').append(
                    `
                        <p>
                            Tidak ada data
                        </p>
                    `
                )
            }
           
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
        $('.jatuh_tempo_baru').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            // startDate: today,
        });
        // $('#jatuh_tempo').datepicker({
        //     autoclose: true,
        //     format: "dd-M-yyyy",
        //     todayHighlight: true,
        //     language: 'en',
        //     // startDate: today,
        // });
    });
</script>

@endsection