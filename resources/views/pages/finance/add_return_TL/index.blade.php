
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')

@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        <div class="card-header">
            {{-- <ul class="list-inline"> --}}
                {{-- <div class="row"> --}}
                    
                {{-- </div> --}}
            {{-- </ul> --}}
        </div>
        
        <div class="card-body">
            <div class="col-sm-12 col-md-3 col-lg-3 ">
                <div class="form-group">
                    <label for="">Status TL</label> 
                    <select class="form-control selectpicker" required name="status_tl" id="status_tl" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                        <option value="Add TL">Tambah TL</option>
                        <option value="Return TL">Kembalikan TL</option>
                    </select>
                </div>
            </div>
            <hr>
                <table id="datatable" class="table table-bordered table-striped" width='100%'>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>No. Polisi</th>
                            <th>No. Sewa</th>
                            <th>Tgl Berangkat</th>
                            <th>Tujuan</th>
                            <th>Driver</th>
                            <th>Keterangan</th>
                            <th style="width:30px"></th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                    @if (isset($data))
                        @foreach($data as $item)
                            <tr>
                                <td>{{ $item->nama_customer }}</td>
                                <td>{{ $item->no_polisi }}</td>
                                <td>{{ $item->no_sewa }}</td>
                                <td>{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}</td>
                                <td>{{ $item->nama_tujuan }}</td>
                                <td>{{ $item->nama_lengkap }}</td>
                                <td>{{ $item->status }}</td>
                                <td>                                    
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{route('add_return_tl.edit',[$item->id_sewa])}}" class="dropdown-item">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                            <a href="{{route('add_return_tl.cair',['id' => $item->id_sewa])}}" class="dropdown-item">
                                                <span class="fa fa-credit-card mr-3"></span> Cair/Return
                                            </a>
                                            {{-- <a href="{{ route('truck_order.destroy', $item->id_sewa) }}" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Delete
                                            </a> --}}
                                        </div>
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