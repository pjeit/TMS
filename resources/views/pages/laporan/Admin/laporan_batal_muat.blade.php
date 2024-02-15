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
                                    <label for="">Group Berdasarkan</label>
                                    <select class="form-control select2" id="tipe_group" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                            <option value="customer">Customer</option>
                                            <option value="kendaraan">Kendaraan</option>
                                            <option value="driver">Driver</option>
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
                <table id="tabel_batal" class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th style="width:1px; white-space: nowrap;">Sewa</th>
                            <th>Tujuan & Kendaraan</th>
                            <th>Driver</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Tanggal Cancel</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Alasan Cancel</th>    
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
            var tipe_group = $("#tipe_group").val();
            console.log(tanggal_awal);
            console.log(tanggal_akhir);

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

            if(tanggal_awal> tanggal_akhir)
            {
                event.preventDefault();
                    Toast.fire({
                        icon: 'error',
                        title: 'Tanggal awal harus lebih kecil dari tanggal akhir!'
                    })
                return;
            }
            $.ajax({
                method: 'GET',
                url: "{{ route('laporan_batal_muat.load_data_ajax') }}",
                // dataType: 'JSON',
                // contentType: false,
                // cache: false,
                // processData:false,
                data: {
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    tipe_group: tipe_group,
                },
                success: function(response) {
                    $('#tabel_batal').DataTable().destroy();
                    $('#tabel_batal tbody').html('');

                    // $("th").remove();
                    var customer_th = `
                        <tr>
                            <th>Customer</th>
                            <th style="width:1px; white-space: nowrap;">Sewa</th>
                            <th>Tujuan & Kendaraan</th>
                            <th>Driver</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Tanggal Cancel</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Alasan Cancel</th>    
                        </tr>`
                    var kendaraan_th = `
                        <tr>
                            <th>Kendaraan</th>
                            <th style="width:1px; white-space: nowrap;">Sewa</th>
                            <th>Customer & Tujuan</th>
                            <th>Driver</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Tanggal Cancel</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Alasan Cancel</th>      
                        </tr> `
                    var driver_th = `
                        <tr>
                            <th>Driver</th>
                            <th style="width:1px; white-space: nowrap;">Sewa</th>
                            <th>Customer & Tujuan</th>
                            <th>Kendaraan</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Tanggal Cancel</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Alasan Cancel</th>                  
                        </tr>`
                    if(tipe_group=='customer')
                    {
                        $("#tabel_batal thead").html(customer_th);
                    }   
                    else if(tipe_group=='kendaraan')
                    {
                        $("#tabel_batal thead").html(kendaraan_th);
                    }
                    else if(tipe_group=='driver')
                    {
                        $("#tabel_batal thead").html(driver_th);
                    }
                    var data = response.data;
                    console.log(data);
                    if(data.length > 0){
                        for (var i = 0; i <data.length; i++) {
                            var row = $("<tr></tr>");
                            if(tipe_group=='customer')
                            {
                                row.append(`<td>${data[i].get_customer.nama}</td>`);//customer
                                row.append(`<td>${data[i].no_sewa} ${dateMask(data[i].tanggal_berangkat)}</td>`);//sewa
                                row.append(`<td>${data[i].nama_tujuan} - [${data[i].no_polisi} (${data[i].karoseri?data[i].karoseri:'-'})]</td>`);//tujuan dan kendaraan
                                row.append(`<td>${data[i].get_karyawan?data[i].get_karyawan.nama_panggilan:'DRIVER REKANAN : '+data[i].get_supplier.nama}</td>`);//driver
                              
                            }   
                            else if(tipe_group=='kendaraan')
                            {
                                row.append(`<td>${data[i].no_polisi} (${data[i].karoseri?data[i].karoseri:'-'})</td>`);//nopol
                                row.append(`<td>${data[i].no_sewa} ${dateMask(data[i].tanggal_berangkat)}</td>`);//sewa
                                row.append(`<td>${data[i].get_customer.nama} - [${data[i].nama_tujuan}]</td>`);//customer dan tujuan
                                row.append(`<td>${data[i].get_karyawan?data[i].get_karyawan.nama_panggilan:'DRIVER REKANAN : '+data[i].get_supplier.nama}</td>`);//driver
                            }
                            else if(tipe_group=='driver')
                            {
                                row.append(`<td>${data[i].get_karyawan?data[i].get_karyawan.nama_panggilan:'DRIVER REKANAN : '+data[i].get_supplier.nama}</td>`);//driver
                                row.append(`<td>${data[i].no_sewa} ${dateMask(data[i].tanggal_berangkat)}</td>`);//sewa
                                row.append(`<td>${data[i].get_customer.nama} - [${data[i].nama_tujuan}]</td>`);//customer dan tujuan
                                row.append(`<td>${data[i].no_polisi} (${data[i].karoseri?data[i].karoseri:'-'})</td>`);//kendaraan
                            }
                            row.append(`<td>${dateMask(data[i].get_batal_cancel.tgl_batal_muat_cancel)}</td>`);
                            row.append(`<td>${data[i].get_batal_cancel.alasan_batal}</td>`);
                            $("#tabel_batal").append(row);
                        }
                    }
                        $('#tabel_batal').DataTable({
                            order: [
                                [0, 'asc'], 
                            ],
                            rowGroup: {
                                dataSrc: [0] 
                            },
                            // destroy: true,      
                            info: false,       
                            searching: true,  
                            paging: false,      
                            ordering: false,    
                            "language": {
                                "emptyTable": "Data tidak ditemukan."
                            },
                             columnDefs: [
                                {
                                    targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                                    visible: false
                                },
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
                                        return 'Lap Batal Muat (' + formattedDate + ')';
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
