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
    <div class="card">
        <div class="card-header ">
            <div class="card-header" style="border: 2px solid #bbbbbb;">
                <form id="form_report" action="{{ route('storage_demurage.index') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="">Pengirim</label>
                                <select class="form-control select2" name="pengirim" id="pengirim"
                                    data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                    <option value="">­­— SEMUA DATA —</option>
                                    @foreach ($customer as $cust)
                                    <option value="{{$cust->id}}">[{{ $cust->kode }}] {{$cust->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="">Pelayaran</label>
                                <select class="form-control select2" name="pelayaran" id="pelayaran"
                                    data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                    <option value="">­­— SEMUA DATA —</option>
                                    @foreach ($supplier as $supp)
                                    <option value="{{$supp->id}}">{{$supp->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <label for="">&nbsp;</label>
                            <div class="d-flex justify-content-start col-12" style="gap: 5px;">
                                <button type="button" id="btnKu" class=" btn btn-primary radiusSendiri col-6"
                                    onclick=""><i class="fas fa-search"></i> <b> Filter</b></button>
                                {{-- <button type="button" class=" btn btn-success radiusSendiri col-6" onclick=""><i
                                        class="fas fa-file-excel"></i> <b> Excel</b></button> --}}
                            </div>
                        </div>

                    </div>

                </form>
                <div class="form-group">
                    {{-- <button type="button" class="btn btn-sm btn-success" onclick="download_report()"><i
                            class="fas fa-file-excel"></i> Export to Excel</button> --}}
                </div>
            </div>
        </div>

        <div class="card-body">
            <section class="col-lg-12" id="show_report">
                <table class="table table-bordered table-striped" id="tabel">
                    <thead>
                        <tr>
                            <th></th>
                            <th>No. Kontainer</th>
                            <th>Pengirim</th>
                            <th>Pelayaran</th>
                            <th>Status Kontainer</th>
                            <th style="width:30px">
                                <div class="btn-group"></div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                        {{-- <tr id="loading-spinner" style="display: none;">
                            <td colspan="6">
                                <i class="fas fa-spinner fa-spin"></i> Harap tunggu data sedang di proses...
                            </td>
                        </tr> --}}
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var formElement = document.querySelector("#form_report");
        var formData = new FormData(formElement);
        $("#loading-spinner").show();
        console.log('formData ');
        showTable(formData);

        $(document).on('click','#btnKu',function(e){
            var formElement = document.querySelector("#form_report");
            var formData = new FormData(formElement);
            $("#loading-spinner").show();
            showTable(formData);
		});
        function showTable(formData) {
            var fileName = 'SDT ' +  dateMask(Date.now());
            if ($.fn.DataTable.isDataTable('#tabel')) {
                // Destroy the existing DataTable
                $('#tabel').DataTable().destroy();
            }

            var table = $('#tabel').DataTable({
                    order: [
                        [0, 'asc'],
                    ],
                    rowGroup: {
                        dataSrc: [0] // kalau mau grouping pake ini
                    },
                    columnDefs: [
                        {
                            targets: [0],
                            visible: false
                        },
                        { orderable: true, targets: 0 }, // Enable ordering for the first column (index 0)
                        { orderable: false, targets: '_all' } // Disable ordering for all other columns
                    ],
                    info: false,
                    searching: true,
                    paging: true,
                    language: {
                        emptyTable: "Data tidak ditemukan."
                    }
                });

            $.ajax({
                method: 'POST',
                url: '{{ route('storage_demurage.load_data') }}',
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    // Clear existing table data
                    table.clear().draw();

                    var data = response.data;

                    var nyimpenIdBapakJO = null;
                    for (var i = 0; i < data.length; i++) {
                        var row = `<tr>
                            <td><b>◾ ${data[i].no_bl} - ${data[i].statusJO} </b></td>
                            <td>${data[i].no_kontainer} - ${data[i].nama_cust}</td>
                            <td>[${data[i].kode}]</td>
                            <td>[${data[i].nama_supp}]</td>
                            <td>${data[i].statusDetail}</td>
                            <td>
                                <a class="btn btn-sm btn-primary radiusSendiri" href="{!! url('/storage_demurage/${data[i].id}/edit') !!}">
                                    <span class="fas fa-edit"></span> <b>Input S/D/T</b>
                                </a>
                            </td>
                        </tr>`;

                        table.row.add($(row));  // Add the new row to DataTable
                    }

                    table.draw();  // Redraw the DataTable
                },
                error: function (xhr, status, error) {
                    $("#loading-spinner").hide();
                    if (xhr.responseJSON.result == 'error') {
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


        // function showTable(formData){
        //     $.ajax({
        //         method: 'POST',
        //         url: '{{ route('storage_demurage.load_data') }}',
        //         data: formData,
        //         dataType: 'JSON',
        //         contentType: false,
        //         cache: false,
        //         processData:false,
        //         success: function(response) {
        //                 // $("#loading-spinner").hide();
        //                 // console.log(response);
        //                 var data = response.data;
        //                 // $("#hasil").html(" ");
        //                 $('#tabel').DataTable().destroy();
        //                 $('#tabel tbody').empty();

        //                 var nyimpenIdBapakJO = null;
        //                 for (var i = 0; i < data.length; i++) {
        //                     // if (data[i].id_jo !== nyimpenIdBapakJO) {
        //                     //     var row = $("<tr></tr>");
        //                     //     row.append("<td colspan=''><b>" + data[i].no_jo + "</b><br> Status Jo: " + data[i].statusJO + "</td>");
        //                     //     $("#hasil").append(row);
        //                     //     nyimpenIdBapakJO = data[i].id_jo;
        //                     // }

        //                     // var row = $("<tr></tr>");
                            
        //                     // row.append("<td>" + data[i].no_kontainer + "</td>");
        //                     // row.append("<td> [" + data[i].kode + "] " + data[i].nama_cust + "</td>");
        //                     // row.append("<td>" + data[i].nama_supp + "</td>");
        //                     // row.append("<td>" + data[i].statusDetail + "</td>");
        //                     // row.append(`
        //                     //     <td>
        //                     //         <a class="btn btn-sm btn-primary radiusSendiri" href="{!! url('/storage_demurage/${data[i].id}/edit') !!}">
        //                     //             <span class="fas fa-edit" ></span> <b>Input S/D/T</b>
        //                     //         </a>
        //                     //     </td>
        //                     //     `); 
        //                     var row = `<tr>
        //                         <td><b>◾ ${data[i].no_bl} - ${data[i].statusJO} </b></td>
        //                         <td>${data[i].no_kontainer}${ data[i].nama_cust}</td>
        //                         <td>[${data[i].kode}]</td>
        //                         <td>[${data[i].nama_supp}]</td>
        //                         <td>${data[i].statusDetail}</td>
        //                         <td>
        //                             <a class="btn btn-sm btn-primary radiusSendiri" href="{!! url('/storage_demurage/${data[i].id}/edit') !!}">
        //                                 <span class="fas fa-edit" ></span> <b>Input S/D/T</b>
        //                             </a>
        //                         </td>
        //                     </tr>`
                            
        //                     $("#hasil").append(row);
        //                 }
        //                 $('#tabel').DataTable({
        //                     // dom: 'Bfrtip',
        //                     // buttons: [
        //                     //     {
        //                     //         extend: 'excel',
        //                     //         // filename: fileName,
        //                     //     }
        //                     // ],
        //                     order: [
        //                         [0, 'asc'], 
        //                     ],
        //                     rowGroup: {
        //                         dataSrc: [0] 
        //                     },
        //                     columnDefs: [
        //                         {
        //                             targets: [0], 
        //                             visible: false
        //                         },
        //                         { orderable: true, targets: 0 }, // Enable ordering for the first column (index 0)
        //                         { orderable: false, targets: '_all' } // Disable ordering for all other columns
        //                     ],
        //                     // destroy: true,      // destroy old data and create new one
        //                     info: false,        // Disable showing entries
        //                     searching: true,   // Disable searching
        //                     paging: true,      // Disable pagination
        //                     // ordering: false,    // Disable ordering
        //                     "language": {
        //                         "emptyTable": "Data tidak ditemukan."
        //                     }
        //                 });
        //         },error: function (xhr, status, error) {
        //                 $("#loading-spinner").hide();
        //             if ( xhr.responseJSON.result == 'error') {
        //                 console.log("Error:", xhr.responseJSON.message);
        //                 console.log("XHR status:", status);
        //                 console.log("Error:", error);
        //                 console.log("Response:", xhr.responseJSON);
        //             } else {
        //                 toastr.error("Terjadi kesalahan saat menerima data. " + error);
        //             }
        //         }
        //     });
        // }

       

    });

</script>
@endsection