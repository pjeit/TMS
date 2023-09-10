
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
                <div class="form-group w-25">
                        <form id="filterForm" action="{{ route('filterMutasi.cari')}}" method="get">
                            <label>Filter Cabang</label>
                             <select class="form-control select2" style="width: 100%;" id='jenisFilter' name="jenisFilter">
                                <option value="">ALL</option>
                                @foreach($dataJenisFilter as $dat)
                                        <option value="{{$dat->id}}" id="">{{$dat->nama}}</option>
                                @endforeach
                                <input type="hidden" id="SimpenId">
                                
                            </select>
                           
                        </form>
                    </div>
                <table id="datatable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Cabang Asal</th>
                            <th>Cabang Tujuan</th>
                            <th>Kategori Kendaraan</th>
                            <th>Nopol</th>
                            <th>Ekor</th>
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
<script type="text/javascript">
var currentUrl = window.location.href;
var baseUrl = currentUrl.split('=');
var idUrl = parseFloat(baseUrl[1]);
// $('#jenisFilter').val(idUrl).selectpicker("refresh");

 $(document).ready(function() {
    var id =localStorage.getItem("SimpenId");
    console.log(id);
    if (!isNaN(idUrl) &&idUrl==id) {
        $(`#jenisFilter option[value="${idUrl}"]`).prop('selected', true);
    }
    $('#jenisFilter').change(function (e) {
      localStorage.setItem("SimpenId", $(this).val());
      e.preventDefault();
      
        $('#filterForm').submit();
    });
 });
</script>  
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
