
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
            <div style="overflow: auto;">
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
                  
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var status = $('#status_tl').val();
        showTable(status);

        $(document).on('change', '#status_tl', function(e) {  
            showTable(this.value)
		});        

        function showTable(status){
            $.ajax({
                method: 'GET',
                url: `add_return_tl/getData/${status}`,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    var table = $('#datatable').DataTable();
                    table.clear().destroy();

                    $("#hasil").append(row);
                    $("#loading-spinner").hide();
                    var data = response;
                    console.log('response', data);
                    for (var i = 0; i < data.length; i++) {
                        var row = $("<tr></tr>");
                        row.append(`<td>${data[i].nama_customer}</td>`);
                        row.append(`<td>${data[i].no_polisi}</td>`);
                        row.append(`<td>${data[i].no_sewa}</td>`);
                        row.append(`<td>${data[i].tanggal_berangkat}</td>`);
                        row.append(`<td>${data[i].alamat_tujuan}</td>`);
                        row.append(`<td>${data[i].nama_driver}</td>`);
                        row.append(`<td>${data[i].status}</td>`);
                        if(status == 'Add TL'){
                            var jenisTL =  `<a href="add_return_tl/cair/${data[i].id_sewa}" class="dropdown-item">
                                                <span class="fa fa-credit-card mr-3"></span> Cairkan TL
                                            </a>`;
                        }else{
                            var jenisTL =  `<a href="add_return_tl/refund/${data[i].id_sewa}" class="dropdown-item">
                                                <span class="fa fa-credit-card mr-3"></span> Kembalikan TL
                                            </a>`;
                        }
                        row.append(`<td class='text-center'> 
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                `+
                                                jenisTL
                                                +`
                                            </div>
                                        </div>
                                    </td>`);
                        $("#hasil").append(row);
                        $("#datatable").dataTable();
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
        };
    });
</script>
@endsection