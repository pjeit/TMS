
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
                                <i class="fa fa-plus-circle" aria-hidden="true"> </i> Mutasi Kendaraan
                            </a> 
                        </div>
                    
                    </div>
            </div>
            
            <div class="card-body">
               <section class="col-lg-12" id="show_report">
                <form action="{{ url('mutasi_kendaraan')}}" method="get">
                    <div class="row">
                        <div class="form-group col-lg-3 col-md-4 col-sm-4">
                            <label>Cabang Asal</label>
                            <select class="form-control select2" style="width: 100%;" id='cabang_asal' name="cabang_asal">
                                <option value="">SEMUA DATA</option>
                                @foreach($dataJenisFilter as $dat)
                                        <option value="{{$dat->id}}" {{ isset($request['cabang_asal'])? $request['cabang_asal'] == $dat->id ? 'selected':'' :'' }} >{{$dat->nama}}</option>
                                @endforeach
                                <input type="hidden" id="SimpenId">
                            </select>
                        </div>
                        <div class="form-group col-lg-3 col-md-4 col-sm-4">
                            <label>Cabang Tujuan</label>
                            <select class="form-control select2" style="width: 100%;" id='cabang_tujuan' name="cabang_tujuan">
                                <option value="">SEMUA DATA</option>
                                @foreach($dataJenisFilter as $dat)
                                        <option value="{{$dat->id}}" {{ isset($request['cabang_tujuan'])? $request['cabang_tujuan'] == $dat->id ? 'selected':'' :'' }} >{{$dat->nama}}</option>
                                @endforeach
                                <input type="hidden" id="SimpenId">
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-2 col-sm-4">
                            <label class="d-none d-sm-block">&nbsp;</label>
                            <button class="btn btn-secondary form-control radiusSendiri"><span class="fa fa-search"></span> Filter</button>
                        </div>
                    </div>
                </form>
                
                <table id="datatable" class="table table-bordered table-striped table-hover" width='100%'>
                    <thead>
                        <tr>
                            <th>Cabang Asal</th>
                            <th>Cabang Tujuan</th>
                            <th>Kategori Kendaraan</th>
                            <th>No. Polisi</th>
                            <th>Chassis</th>
                            <th>Tgl Mutasi</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                        @isset($dataKendaraan)
                            @foreach ($dataKendaraan as $item)
                                <tr>
                                    <td>{{$item->cabangAsal}}</td>
                                    <td>{{$item->cabangBaru}}</td>
                                    <td>{{$item->kategori}}</td>
                                    <td>{{$item->no_polisi}}</td>
                                    <td>{{$item->chassis}}</td>
                                    <td>{{date("d-M-Y", strtotime($item->created_at))}}</td>
                                    <td>{{$item->catatan}}</td>
                                </tr>
                            @endforeach
                        @endisset
                      
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
