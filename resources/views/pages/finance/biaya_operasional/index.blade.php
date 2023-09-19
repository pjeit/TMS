
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
    tr.group,
    tr.group:hover {
        background-color: #ddd !important;
    }
</style>
<div class="container-fluid">
    {{-- <div class="radiusSendiri sticky-top " style="margin-bottom: -15px;">
        <div class="card radiusSendiri" style="">
            <div class="p-3">
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
        </div>
    </div> --}}
    <div class="card radiusSendiri">
        {{-- <div class="row"> --}}
            <div class="card-header ">
                {{-- <div class="" style="position: relative; left: 0px; top: 0px; background-color:#edf4fc;"> --}}
                    <div class="card-header" style="border: 2px solid #bbbbbb;">
                            <form id="form_report" action="{{ route('biaya_operasional.load_data') }}" method="POST">
                                @csrf
                                <div class="row" >
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="">ITEM</label>
                                            <select class="form-control selectpicker" name="item" id="item" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                <option value="">­­— PILIH DATA —</option>
                                                <option value="TALLY">TALLY</option>
                                                <option value="PLASTIK">PLASTIK</option>
                                                <option value="SEAL">SEAL</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label for="">&nbsp;</label>
                                        <div class="d-flex justify-content-start col-12" style="gap: 5px;">
                                            <button type="button" id="btnKu" class=" btn btn-primary radiusSendiri col-6" onclick=""><i class="fas fa-search"></i> <b> Filter</b></button>
                                            <button type="button" class=" btn btn-success radiusSendiri col-6 ml-5" onclick=""><i class="fa fa-fw fa-save"></i> <b> Simpan</b></button>
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

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No. Kontainer</th>
                            <th>Status Kontainer</th>
                            <th style="width:30px"><div class="btn-group"></div></th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                    </tbody>
                </table>
               </section>
            </div>
        {{-- </div> --}}
    </div>
</div>
<script>
    $(document).ready(function() {
        var formElement = document.querySelector("#form_report");
        var formData = new FormData(formElement);
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
                url: '{{ route('biaya_operasional.load_data') }}',
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    $("#loading-spinner").hide();
                    var item = $('#item').val();
                    var data = response.data;

                    $("th").remove();
                    $("thead tr").append("<th>###</th><th>Status Kontainer</th>");
              
                    if(item == 'PLASTIK'){
                        $("thead tr").append("<th>Plastik</th>");
                        $("thead tr").append("<th class='text-center'><input class='check_all' type='checkbox' id='check_all'></th>");
                    } else if(item == 'TALLY'){
                        $("thead tr").append("<th>Tally</th>");
                        $("thead tr").append("<th class='text-center'><input class='check_all' type='checkbox' id='check_all'></th>");
                    } else if(item == 'SEAL'){
                        $("thead tr").append("<th>Seal Pelayaran</th>");
                        $("thead tr").append("<th class='text-center'><input class='check_all' type='checkbox' id='check_all'></th>");
                        $("thead tr").append("<th>Seal PJE</th>");
                        $("thead tr").append("<th class='text-center'><input class='check_all_pje' type='checkbox' id='check_all_pje'></th>");
                    }
                        $("#hasil").html(" ");

                        var dataJO = null;
                        for (var i = 0; i < data.length; i++) {
                            if (data[i].id_jo !== dataJO) {
                                var row = $("<tr></tr>");
                                
                                if(item == 'SEAL'){
                                    var colspan = 3;
                                    row.append(`<td colspan='${colspan}' style='background: #efefef'><b> ${data[i].no_bl} / ${data[i].customer} </b></td>`);
                                    row.append(`<td style='background: #efefef' class='text-center'><input class='check_all${data[i].id_jo} check_item' type='checkbox' id='check_all${data[i].id_jo}'></td>`);
                                    row.append(`<td style='background: #efefef'></td>`);
                                    row.append(`<td style='background: #efefef' class='text-center'><input class='check_all_pje${data[i].id_jo} check_all_pje check_pje' type='checkbox' id='check_all_pje${data[i].id_jo}'></td>`);
                                }else{
                                    var colspan = 3;
                                    row.append(`<td colspan='${colspan}' style='background: #efefef'><b> ${data[i].no_bl} / ${data[i].customer} </b></td>`);
                                    row.append(`<td style='background: #efefef' class='text-center'><input class='check_item check_cust check_cust_${data[i].id_jo}' check='${data[i].id_jo}' type='checkbox' id='check_all${data[i].id_jo}'></td>`);
                                }

                                $("#hasil").append(row);
                                dataJO = data[i].id_jo;
                            }
                            var row = $("<tr></tr>");
                            row.append("<td>" + data[i].no_kontainer + ' / ' + data[i].nama_tujuan + "</td>");
                            row.append("<td>" + data[i].status_jod + "</td>");
                            if(item == 'PLASTIK'){
                                row.append(`<td> ${data[i].plastik.toLocaleString()} <input type="hidden" id="plastik_${data[i].plastik}" value="${data[i].plastik}" /></td>`);
                                row.append(`<td class='text-center'> <input class='check_item check_container' ${data[i].id_jo}='cek' id_jo="${data[i].id_jo}" type='checkbox' id='${data[i].id_jo}_${data[i].id_sewa}'> </td>`);
                            } else if(item == 'TALLY'){
                                row.append(`<td> ${data[i].tally.toLocaleString()} <input type="hidden" id="tally_${data[i].tally}" value="${data[i].tally}" /></td>`);
                                row.append(`<td class='text-center'> <input class='check_item check_container' ${data[i].id_jo}='cek' id_jo="${data[i].id_jo}" type='checkbox' id='${data[i].id_jo}_${data[i].id_sewa}'> </td>`);
                            } else if(item == 'SEAL'){
                                row.append(`<td> ${data[i].seal_pelayaran.toLocaleString()} <input type="hidden" id="seal_pelayaran_${data[i].seal_pelayaran}" value="${data[i].seal_pelayaran}" /></td>`);
                                row.append(`<td class='text-center'> <input class='check_item' type='checkbox' id='${data[i].id_jo}_${data[i].id_sewa}'> </td>`);
                                row.append(`<td> ${data[i].seal_pje.toLocaleString()} <input type="hidden" id="seal_pje_${data[i].seal_pje}" value="${data[i].seal_pje}" /></td>`);
                                row.append(`<td class='text-center'> <input class='check_pje' type='checkbox' id='${data[i].id_jo}_${data[i].id_sewa}'> </td>`);

                            }
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
 
        
        // check all
            $(document).on('change', '#check_all', function() {  
                $(".check_item").prop('checked', $(this).prop('checked'));
            });

            $(document).on('change', '#check_all_pje', function() {  
                $(".check_pje").prop('checked', $(this).prop('checked'));
            });
        // 

        // check per customer
            $(document).on('change', '.check_cust', function() {
                var cust_id = $(this).attr('check');
                var checkElement = $(`input[${cust_id}="cek"]`);
                checkElement.prop('checked', $(this).prop('checked'));
            });
        //

        // uncheck all
            $(document).on('click', '.check_container', function (event) {
                var id_jo = $(this).attr('id_jo');
                $("#check_all").prop('checked', false);
                $(`.check_cust_${id_jo}`).prop('checked', false);
            });
        //
    });


</script>
@endsection