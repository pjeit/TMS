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
            <div class="card-header" >
                    {{-- <form id="form_report" action="{{ route('laporan_bank.index') }}" method="GET"> --}}
                        <div class="row" >
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="periode">Periode:</label>
                                    <div class="d-flex" style="gap: 10px;">
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" name="tanggal_awal" autocomplete="off" class="date form-control" id="tanggal_awal" placeholder="dd-M-yyyy" value="{{ isset($request['tanggal_awal'])? $request['tanggal_awal'] ?? '':date("d-M-Y") }}">     
                                        </div>
                                        <span for="periode" class="text-bold mt-2"> s/d </span>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" name="tanggal_akhir" autocomplete="off" class="date  form-control" id="tanggal_akhir" placeholder="dd-M-yyyy" value="{{ isset($request['tanggal_akhir'])? $request['tanggal_akhir'] ?? '':date("d-M-Y") }}">     
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="">Customer</label>
                                    <select class="form-control select2" id="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                            <option value="">Pilih Customer</option>
                                            @foreach ($dataCustomer as $customer)
                                                <option value="{{$customer->id}}">{{$customer->nama}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="">&nbsp;</label>
                                <div class="d-flex justify-content-start" style="gap: 5px;">
                                    <button type="submit" class="btn btn-primary radiusSendiri " id="btnCari"><i class="fas fa-search"></i> <b> Tampilkan Data</b></button>
                                    {{-- <button type="button" class="btn btn-success radiusSendiri " ><i class="fas fa-file-excel"></i> <b> Export Excel</b></button> --}}
                                </div>
                            </div>
                        </div>
                    {{-- </form> --}}      
            </div><!-- /.card-header -->
            <div class="card-body" style="overflow: auto;">
                <table id="tabel_packing" class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;">
                    <thead>
                        <tr>
                            <th>Nopol</th>
                            <th>No. Kontainer</th>
                            <th>Seal</th>
                            <th>Seal PJE</th>
                            <th>Kapal</th>
                            <th>Tgl. Berangkat</th>
                            <th>Customer</th>
                            <th>Tujuan</th>
                        </tr>
                    </thead>
                    <tbody >
                            
                    </tbody>
                </table>
            </div>
        {{-- </div> --}}
    </div>
</div>
<script>
    $(document).ready(function() {
        
        $('#tanggal_awal').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate:'+1d'
        });
        $('#tanggal_akhir').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate:'+1d'
        });
        $('body').on('click','#btnCari', function (){
            
            var tanggal_awal = $("#tanggal_awal").val();
            var tanggal_akhir = $("#tanggal_akhir").val();
            var customer = $("#customer").val();
        

            const Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    timer: 2500,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

            
            $.ajax({
                method: 'GET',
                url: "{{ route('laporan_packing_list.load_data_ajax') }}",
                // dataType: 'JSON',
                // contentType: false,
                // cache: false,
                // processData:false,
                data: {
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    customer: customer,
                },
                success: function(response) {
                    $('#tabel_packing').DataTable().destroy();
                    $('#tabel_packing tbody').html('');

                    
                    var data = response.data;
                    console.log(data);
                    if(data.length > 0){
                        for (var i = 0; i <data.length; i++) {
                            var row = $("<tr></tr>");
                            // <th>Nopol</th>
                            // <th>No. Kontainer</th>
                            // <th>Seal</th>
                            // <th>Seal PJE</th>
                            // <th>Kapal</th>
                            // <th>Tgl. Berangkat</th>
                            // <th>Customer</th>
                            // <th>Tujuan</th>
                            row.append(`<td>${data[i].no_polisi} </td>`);//kendaraan
                            row.append(`<td>${data[i].no_kontainer}</td>`);
                            row.append(`<td>${data[i].seal_pelayaran?data[i].seal_pelayaran:'-'} </td>`);
                            row.append(`<td>${data[i].seal_pje?data[i].seal_pje:'-'} </td>`);
                            row.append(`<td contenteditable='true'></td>`);
                            row.append(`<td>${data[i].tanggal_berangkat?dateMask(data[i].tanggal_berangkat):'-'}</td>`);
                            row.append(`<td>${data[i].get_customer.nama}</td>`);//customer
                            row.append(`<td>${data[i].nama_tujuan} </td>`);//tujuan
                            $("#tabel_packing").append(row);
                        }
                    }
                    $('#tabel_packing').DataTable({
                        order: [
                            [0, 'asc'], 
                        ],
                        // rowGroup: {
                        //     dataSrc: [0] 
                        // },
                        // destroy: true,      
                        info: false,       
                        searching: true,  
                        paging: false,      
                        ordering: false,    
                        "language": {
                            "emptyTable": "Data tidak ditemukan."
                        },
                        columnDefs: [
                            // {
                            //     targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                            //     visible: false
                            // },
                            {
                                targets: [0,1,2,3,4,5],
                                // orderable: false, // matiin sortir kolom centang
                            },
                        ],
                        dom: 'Bfrtip', // Add the dom option to include export buttons
                        buttons: [
                            {
                                extend: 'excelHtml5', // Add the Excel export button
                                text: '<i class="fas fa-file-excel"></i> <b> Export Excel</b>', // Customize the button text
                                className: 'btn btn-success radiusSendiri', // Add your custom class
                                filename: function () {
                                    const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };
                                    const formattedDate = new Date().toLocaleDateString('en-US', dateOptions);
                                    return 'Lap Packing List (' + formattedDate + ')';
                                },
                                
                            },
                        ],
                        // Configure export options
                        "aoColumnDefs": [
                            { "bVisible": false, "aTargets": [ 0 ] } // Hide the first column in the exported file
                        ],
                    });
                },error: function (xhr, status, error) {
                    // $('#ltl').dataTable().fnClearTable();
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
        });
    });
</script>
@endsection
