
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
    <div class="card">
        {{-- <div class="row"> --}}
            <div class="card-header ">
                    <div class="row">
                        <div class="col-6">
                            <a href="{{route('mutasi_kendaraan.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                                <i class="fa fa-plus-circle" aria-hidden="true"> </i> Buat Mutasi Kendaraan
                            </a> 
                        </div>
                        {{-- <div class="col-6 ">
                            <div class="row">
                                <div class="form-group col-4">
                                </div>
                                <div class="form-group col-4">
                                    <select class="form-control selectpicker text-right" name="cabang" id="cabang" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                        <option value="">—­­— Pilih Cabang ——</option>
                                        @foreach ($data as $cabang)
                                            <option value="{{$cabang->id}}">{{$cabang->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-4" >
                                    <button type="button" id="btnKu" class="btn btn-primary radiusSendiri pull-right" onclick=""><i class="fas fa-search"></i> <b> Filter</b></button>
                                </div>
                            </div>
                        </div> --}}
                    </div>
            </div>
            
            <div class="card-body">
               <section class="col-lg-12" id="show_report">
                <table id="datatable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Jenis Kendaraan</th>
                            <th>Nopol</th>
                            <th>Cabang</th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                        @isset($dataKendaraan)
                            @foreach ($dataKendaraan as $item)
                                <tr>
                                    <td>{{$item->kategori}}</td>
                                    <td>{{$item->no_polisi}}</td>
                                    <td>{{$item->cabang}}</td>
                                </tr>
                            @endforeach
                            
                        @endisset
                         {{-- <tr id="loading-spinner" style="display: none;">
                            <td colspan="6"><i class="fas fa-spinner fa-spin"></i> Harap tunggu data sedang di proses...</td>
                        </tr> --}}
                    </tbody>
                </table>
               </section>
            </div>
        {{-- </div> --}}
    </div>
</div>
<script>
    $(document).ready(function() {
        // var formElement = document.querySelector("#form_report");
        // var formData = new FormData(formElement);
        // $("#loading-spinner").show();
        // showTable(formData);

        $(document).on('click','#btnKu',function(e){
            var formElement = document.querySelector("#form_report");
            var formData = new FormData(formElement);
            $("#loading-spinner").show();
            showTable(formData);
		});
        
        function showTable(formData){
            $.ajax({
                method: 'POST',
                url: '{{ route('storage_demurage.load_data') }}',
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    $("#loading-spinner").hide();
                    console.log(response);
                    var data = response.data;
                        $("#hasil").html(" ");

                        var nyimpenIdBapakJO = null;
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].id_jo !== nyimpenIdBapakJO) {
                                var row = $("<tr></tr>");
                                // row.append(`<td>
                                //     <div class="btn-group ">
                                //         <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                //             <i class="fa fa-list"></i>
                                //         </button>
                                //         <div class="dropdown-menu">
                                //             <a href="#" class="dropdown-item">
                                //                 <span class="fas fa-edit mr-3"></span> Edit
                                //             </a>
                                //             <a href="#" class="dropdown-item">
                                //                 <span class="fas fa-edit mr-3"></span> Edit
                                //             </a>
                                //             <a href="#" method="get" rel="noopener" target="_blank"  class="dropdown-item">
                                //                 <span class="fas fa-print mr-3"></span> Export PDF
                                //             </a>
                                //             <a href="#" class="dropdown-item" data-confirm-delete="true">
                                //                 <span class="fas fa-trash mr-3"></span> Delete
                                //             </a>
                                //         </div>
                                //     </div>
                                // </td>`);
                                row.append("<td colspan='5'>" + data[i].no_jo + "<br> Status Jo: " + data[i].statusJO + "</td>");
                                $("#hasil").append(row);
                                nyimpenIdBapakJO = data[i].id_jo;
                            }

                            var row = $("<tr></tr>");
                      
                            
                            row.append("<td>" + data[i].no_kontainer + "</td>");
                            row.append("<td>" + data[i].kode + " - " + data[i].nama_cust + "</td>");
                            row.append("<td>" + data[i].nama_supp + "</td>");
                            row.append("<td>" + data[i].statusDetail + "</td>");
                            row.append(`
                                <td>
                                    <a class="btn btn-sm btn-primary radiusSendiri" href="{!! url('/storage_demurage/${data[i].id}/edit') !!}">
                                        <span class="fas fa-edit" ></span> <b>Input S/D/T</b>
                                    </a>
                                </td>
                                `); 
                            
                            $("#hasil").append(row);
                        }
                },error: function (xhr, status, error) {
                        $("#loading-spinner").hide();
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
    });

</script>
@endsection
