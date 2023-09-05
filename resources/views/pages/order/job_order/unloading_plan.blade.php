
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
                {{-- <div class="" style="position: relative; left: 0px; top: 0px; background-color:#edf4fc;"> --}}
                    <div class="card-header" style="border: 2px solid #bbbbbb;">
                            <form id="form_report" action="{{ route('job_order.unloading_plan') }}" method="POST">
                                @csrf
                                <div class="row" >
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="">Status JO<span class="text-red">*</span></label>
                                            <select class="form-control selectpicker" name="statusJO" id="statusJO" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                <option value="">ALL</option>
                                                <option value="WAITING PAYMENT">WAITING PAYMENT</option>
                                                <option value="FINANCE PENDING">MENUNGGU FINANCE</option>
                                                <option value="FINANCE APPROVED">DISETUJUI FINANCE</option>

                                                {{-- @foreach ($kasBank as $kb)
                                                    <option value="{{$kb->id}}" <?= $request['tipe'] == $kb->id ? 'selected':''; ?> >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                                @endforeach --}}
                                            </select>
                                        </div>
                                    </div>
                                     <div class="col-4">
                                        <div class="form-group">
                                            <label for="">Status Kontainer<span class="text-red">*</span></label>
                                            <select class="form-control selectpicker" name="statusJODetail" id="statusJODetail" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                <option value="">ALL</option>
                                                <option value="WAITING PAYMENT">WAITING PAYMENT</option>
                                                <option value="FINANCE PENDING">MENUNGGU FINANCE</option>
                                                <option value="FINANCE APPROVED">DISETUJUI FINANCE</option>

                                                {{-- @foreach ($kasBank as $kb)
                                                    <option value="{{$kb->id}}" <?= $request['tipe'] == $kb->id ? 'selected':''; ?> >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                                @endforeach --}}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label for="">&nbsp;</label>
                                        <div class="d-flex justify-content-start" style="gap: 5px;">
                                               <button type="button" id="btnKu" class=" btn btn-primary radiusSendiri " onclick=""><i class="fas fa-search"></i> <b> Tampilkan Data</b></button>
                                               <button type="button" class=" btn btn-success radiusSendiri " onclick=""><i class="fas fa-file-excel"></i> <b> Export Excel</b></button>
                                        </div>
                                    </div>
                                    
                                </div>
                               
                            </form>
                            <div class="form-group">
                                {{-- <button type="button" class="btn btn-sm btn-success" onclick="download_report()"><i class="fas fa-file-excel"></i> Export to Excel</button> --}}
                            </div>
                    </div><!-- /.card-header -->
                {{-- </div> --}}
            </div>
            
            <div class="card-body">
               <section class="col-lg-12" id="show_report">

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                        <th style="width:30px"><div class="btn-group"></div></th>
                        <th>No. Kontainer</th>
                        <th>Pengirim</th>
                        <th>Pelayaran</th>
                        <th>Status Kontainer</th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                         <tr id="loading-spinner" style="display: none;">
                            <td colspan="6"><i class="fas fa-spinner fa-spin"></i> Harap tunggu data sedang di proses...</td>
                        </tr>
                    

                    </tbody>
                </table>
               </section>
            </div>
        {{-- </div> --}}
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on('click','#btnKu',function(e)
		{
            var formElement = document.querySelector("#form_report");
            var formData = new FormData(formElement);
             $("#loading-spinner").show();
             $.ajax({
                method: 'POST',
                url: '{{ route('job_order.unloading_data') }}',
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    $("#loading-spinner").hide();
                    console.log($("#statusJO").val());
                    console.log($("#statusJODetail").val());
                    console.log(response);
                    var data = response.data;
                     $("#hasil").html(" ");

                      var nyimpenIdBapakJO = null;


                     for (var i = 0; i < data.length; i++) {
                            if (data[i].id_jo !== nyimpenIdBapakJO) {
                                var row = $("<tr></tr>");
                                row.append("<td colspan='6'>" + data[i].no_jo + "<br> Status Jo: " + data[i].statusJO + "</td>");
                                $("#hasil").append(row);
                                nyimpenIdBapakJO = data[i].id_jo;
                            }

                            var row = $("<tr></tr>");
                            row.append(`
                                <td>
                                     <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button>
                                        <ul class="dropdown-menu" style="">
                                            <li><a class="dropdown-item" href="{!! url('/job_order/${data[i].id_jo}/edit') !!}"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li>
                                            
                                            <li><a class="dropdown-item" href="https://testapps.pjexpress.co.id/index.php/c_cetak_invoice/cetak/4891"><span class="fas fa-print" style="width:24px"></span>Cetak</a></li>
                                        </ul>
                                    </div>
                                
                                </td>
                                `); 
                            
                            row.append("<td>" + data[i].no_kontainer + "</td>");
                            row.append("<td>" + data[i].kode + " - " + data[i].nama_cust + "</td>");
                            row.append("<td>" + data[i].nama_supp + "</td>");
                            row.append("<td>" + data[i].statusDetail + "</td>");
                            
                            $("#hasil").append(row);
                        }

                   
                },
                 error: function (xhr, status, error) {
                     $("#loading-spinner").hide();
                    if ( xhr.responseJSON.result == 'error') {
                            console.log("Error:", xhr.responseJSON.message);
                            console.log("XHR status:", status);
                            console.log("Error:", error);
                            console.log("Response:", xhr.responseJSON);
                    }
                    else {
                        toastr.error("Terjadi kesalahan saat menerima data. " + error);
                    }

                 
                }
            });
			//disarankan seperti ini
			// $.get("{{ route('job_order.unloading_data') }}",
            // {
            //     statusJO : $("#statusJO").val(),
			// 	statusJODetail:$("#statusJODetail").val()
            // },function(d){
            //         var data = d.data;
            //          $("#hasil").html(" ");
            //             console.log(d);

            //           var nyimpenIdBapakJO = null;


            //          for (var i = 0; i < data.length; i++) {
                            
            //                 if (data[i].id_jo !== nyimpenIdBapakJO) {
            //                     var row = $("<tr></tr>");
            //                     row.append("<td colspan='6'>" + data[i].no_jo + "<br> Status Jo: " + data[i].statusJO + "</td>");
            //                     $("#hasil").append(row);
            //                     nyimpenIdBapakJO = data[i].id_jo;
            //                 }

            //                 var row = $("<tr></tr>");
            //                 row.append(`
            //                     <td>
            //                          <div class="btn-group">
            //                             <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button>
            //                             <ul class="dropdown-menu" style="">
            //                                 <li><a class="dropdown-item" href="{!! url('/job_order/${data[i].id_jo}/edit') !!}"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li>
                                            
            //                                 <li><a class="dropdown-item" href="https://testapps.pjexpress.co.id/index.php/c_cetak_invoice/cetak/4891"><span class="fas fa-print" style="width:24px"></span>Cetak</a></li>
            //                             </ul>
            //                         </div>
                                
            //                     </td>
            //                     `); 
                            
            //                 row.append("<td>" + data[i].no_kontainer + "</td>");
            //                 row.append("<td>" + data[i].kode + " - " + data[i].nama_cust + "</td>");
            //                 row.append("<td>" + data[i].nama_supp + "</td>");
            //                 row.append("<td>" + data[i].statusDetail + "</td>");
                            
            //                 $("#hasil").append(row);
            //             }


			// 	});


		});
    
        $('#tanggal_awal').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
        });
        $('#tanggal_akhir').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
        });
    });

</script>
@endsection
