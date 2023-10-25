
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
        <div class="card-header">

        </div>
        
        <div class="card-body">
            <div style="overflow: auto;">
                <table id="datatable" class="table table-bordered table-striped" width='100%'>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Supplier</th>
                            <th>No. BL</th>
                            <th>Catatan</th>
                            <th>Total</th>
                            <th style="width:30px"></th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $item->getCustomer->nama }}</td>
                                <td>{{ $item->getSupplier->nama }}</td>
                                <td>{{ $item->no_bl }}</td>
                                <td>{{ $item->catatan }}</td>
                                <td>{{ $item->jaminan->nominal }}</td>
                                <td><button id="{{ $item }}" class="btn btn-primary radiusSendiri showModal"> <i class="fas fa-dollar-sign"> </i> <i class="fa fa-reply-all" ></i> </button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal" tabindex='-1'>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id='form_add_detail'>
                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}

                        <div class='row'>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="sewa">Customer</label>
                                        <input type="text" class="form-control" id="customer" readonly>
                                    </div>   

                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="sewa">Supplier</label>
                                        <input type="text" class="form-control" id="supplier" readonly>
                                    </div>   
                                    
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="sewa">Catatan</label>
                                        <input type="text" class="form-control" id="catatan" readonly>
                                    </div>   

                                </div>
                                
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">No. Kontainer</label>
                                        <input  type="text" class="form-control" maxlength="50" id="no_kontainer"> 
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Seal Pelayaran</label>
                                        <input  type="text" class="form-control" maxlength="50" id="no_seal"> 
                                    </div>
                                </div>
                            </div>

                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                    <button type="button" class="btn btn-sm btn-success save_detail" id="" style='width:85px'>OK</button> 
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".showModal").click(function () {
            $("#modal").modal("show");
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
                    var baseUrl = "{{ asset('') }}";

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
                            var jenisTL =  `<a href="${baseUrl}add_return_tl/cair/${data[i].id_sewa}" class="dropdown-item">
                                                <span class="fa fa-credit-card mr-3"></span> Cairkan TL
                                            </a>`;
                        }else{
                            var jenisTL =  `<a href="${baseUrl}add_return_tl/refund/${data[i].id_sewa}" class="dropdown-item">
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