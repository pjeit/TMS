
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
   
</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        {{-- <div class="row"> --}}
            <div class="card-header ">
                
            </div>
            
            <div class="card-body">
                    <table id="" class="table table-bordered table-striped" width=''>
                        <thead>
                            <tr>
                                <th>No. Polisi</th>
                                <th>No. Sewa</th>
                                <th>Tanggal Berangkat</th>
                                <th>Tujuan</th>
                                <th>Driver</th>
                                <th style="width:30px"></th>
                            </tr>
                        </thead>
                        <tbody id="hasil">
                        @if (isset($sewa))
                            @php
                                $simpenIdCust = null; 
                            @endphp
                            @foreach($sewa as $item)
                                
                                @if($item->id_cust != $simpenIdCust)
                                    @php
                                        $simpenIdCust = $item->id_cust; 
                                    @endphp
                                    <tr>
                                        <th colspan="6">{{ $item->nama_cust }}</th>
                                    </tr>
                                @endif
                                <tr>
                                    <td width="140">{{ $item->no_polisi}}</td>
                                    <td>{{ $item->no_sewa }}</td>
                                    <td width="125">{{ date('d-M-Y', strtotime($item->tanggal_berangkat)) }}</td>
                                    <td>{{ $item->nama_tujuan }}</td>
                                    <td>{{ $item->supir }} (0{{ trim($item->telpSupir) }})</td>
                                    <td>
                                        {{-- <form method="POST" action="{{ route('pencairan_uang_jalan.form') }}">
                                            @csrf
                                            <input type="hidden" name="id_sewa" value="{{ $item->id_sewa }}">
                                            <button type="submit" class="btn btn-success radiusSendiri">
                                                <i class="fas fa-credit-card"></i> Pencairan
                                            </button>
                                        </form> --}}
                                        <a class="btn btn-success radiusSendiri" href="{{route('pencairan_uang_jalan.edit',[$item->id_sewa])}}">
                                            <i class="fas fa-credit-card"></i> Pencairan
                                        </a>  
                                        {{-- <a class="dropdown-item" href="{{ route('pencairan_uang_jalan.edit', [$item->id_sewa]) }}"><span class="fas fa-edit" style="width:24px"></span>Pencairan</a> --}}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
            </div>
        {{-- </div> --}}
    </div>
</div>
<script>
    // $(document).ready(function() {
    //     var formElement = document.querySelector("#form_report");
    //     var formData = new FormData(formElement);
    //     $("#loading-spinner").show();
    //     // showTable(formData);

    //     $(document).on('click','#btnKu',function(e){
    //         var formElement = document.querySelector("#form_report");
    //         var formData = new FormData(formElement);
    //         $("#loading-spinner").show();
    //         showTable(formData);
	// 	});
        
    //     function showTable(formData){
    //         $.ajax({
    //             method: 'POST',
    //             url: '{{ route('pembayaran_sdt.load_data') }}',
    //             data: formData,
    //             dataType: 'JSON',
    //             contentType: false,
    //             cache: false,
    //             processData:false,
    //             success: function(response) {
    //                 $("#loading-spinner").hide();
    //                 console.log(response);
    //                 var data = response.data;
    //                 console.log('data '+data);
    //                     $("#hasil").html(" ");

    //                     var nyimpenIdBapakJO = null;
    //                     for (var i = 0; i < data.length; i++) {
    //                         if (data[i].id_jo !== nyimpenIdBapakJO) {
    //                             var row = $("<tr></tr>");
    //                             row.append(`<td style='background: #efefef'> <a class="btn btn-sm btn-primary radiusSendiri" href="{!! url('/pembayaran_sdt/${data[i].id_jo}/edit') !!}">
    //                                     <span class="fa fa-share" ></span> 
    //                                 </a> </td>`)
    //                             row.append("<td colspan='5' style='background: #efefef'>" + "No. JO : " + data[i].no_jo + "<br> No. BL : " + data[i].no_bl + "</td>");
    //                             $("#hasil").append(row);
    //                             nyimpenIdBapakJO = data[i].id_jo;
    //                         }

    //                         var row = $("<tr></tr>");
    //                         // row.append(`
    //                         //     <td>
    //                         //         <a class="btn btn-sm btn-primary radiusSendiri" href="{!! url('/pembayaran_sdt/${data[i].id}/edit') !!}">
    //                         //             <span class="fas fa-edit" ></span> <b></b>
    //                         //         </a>
    //                         //     </td>
    //                         // `); 
                            
    //                         row.append("<td> </td>");
    //                         row.append("<td>" + data[i].no_kontainer + "</td>");
    //                         row.append("<td>" + data[i].kode + " - " + data[i].nama_cust + "</td>");
    //                         row.append("<td>" + data[i].nama_supp + "</td>");
    //                         row.append("<td>" + data[i].statusDetail + "</td>");
                            
    //                         $("#hasil").append(row);
    //                     }
    //             },error: function (xhr, status, error) {
    //                     $("#loading-spinner").hide();
    //                 if ( xhr.responseJSON.result == 'error') {
    //                     console.log("Error:", xhr.responseJSON.message);
    //                     console.log("XHR status:", status);
    //                     console.log("Error:", error);
    //                     console.log("Response:", xhr.responseJSON);
    //                 } else {
    //                     toastr.error("Terjadi kesalahan saat menerima data. " + error);
    //                 }
    //             }
    //         });
    //     }
       
    // });

</script>
@endsection