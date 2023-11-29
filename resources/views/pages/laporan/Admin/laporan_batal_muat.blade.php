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
                            <div class="col-4">
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
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Group Berdasarkan</label>
                                    <select class="form-control select2" id="tipe_group" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                            <option value="customer">Customer</option>
                                            <option value="kendaraan">Kendaraan</option>
                                            <option value="driver">Driver</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <label for="">&nbsp;</label>
                                <div class="d-flex justify-content-start" style="gap: 5px;">
                                    <button type="submit" class="btn btn-primary radiusSendiri " ><i class="fas fa-search"></i> <b> Tampilkan Data</b></button>
                                    <button type="button" class="btn btn-success radiusSendiri " ><i class="fas fa-file-excel"></i> <b> Export Excel</b></button>
                                </div>
                            </div>
                        </div>
                    {{-- </form> --}}

                    <div class="form-group">
                        {{-- <button type="button" class="btn btn-sm btn-success" onclick="download_report()"><i class="fas fa-file-excel"></i> Export to Excel</button> --}}
                    </div>
            </div><!-- /.card-header -->
            <div class="card-body" style="overflow: auto;">
                <table id="tabel_batal" class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;">
                    <thead>
                        {{-- <tr>
                            <th style="width:1px; white-space: nowrap;">Sewa</th>
                            <th>Tujuan & Kendaraan</th>
                            <th>Driver</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Tanggal Cancel</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Alasan Cancel</th>
                        </tr> --}}
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
        function showTable(item){
            // var baseUrl = "{{ asset('') }}";
            // var url = baseUrl+`pencairan_uang_jalan_ltl/getData/${item}`;
            var tanggal_awal = $("#tanggal_awal").val();
            var tanggal_akhir = $("#tanggal_akhir").val();
            var tipe_group = $("#tipe_group").val();

            $.ajax({
                method: 'GET',
                url: "{{ route('laporan_batal_muat.load_data_ajax') }}",
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                data: {
                    tanggal_awal: tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                    tipe_group: tipe_group,
                },
                success: function(response) {
                    // $("#hasil").empty();
                    $('#tabel_batal').dataTable().fnClearTable();
                    $("#tabel_batal").dataTable().fnDestroy();

                    $("th").remove();
                    var customer_th = `
                            <th style="width:1px; white-space: nowrap;">Sewa</th>
                            <th>Tujuan & Kendaraan</th>
                            <th>Driver</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Tanggal Cancel</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Alasan Cancel</th>              
                            `
                    var kendaraan_th = `
                            <th style="width:1px; white-space: nowrap;">Sewa</th>
                            <th>Customer & Tujuan</th>
                            <th>Driver</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Tanggal Cancel</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Alasan Cancel</th>                  
                            `
                    var driver_th = `
                            <th style="width:1px; white-space: nowrap;">Sewa</th>
                            <th>Customer & Tujuan</th>
                            <th>Driver</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Tanggal Cancel</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Alasan Cancel</th>                  
                            `
                    if(tipe_group=='customer')
                    {
                        $("thead tr").append(customer_th);
                    }   
                    else if(tipe_group=='kendaraan')
                    {
                        $("thead tr").append(kendaraan_th);

                    }
                    else if(tipe_group=='driver')
                    {
                        $("thead tr").append(driver_th);
                    }
                    var data = response.data;
                    if(data.length > 0){
                        for (var i = 0; i <data.length; i++) {
                                var row = $("<tr></tr>");

                                row.append(`<td style='background: #efefef' > 
                                        <div class="d-flex justify-content-between ">
                                            <div>
                                                <b> <span>► ${data[i].get_customer.nama}</span> (${data[i].no_polisi}) - ${data[i].nama_driver} </b>
                                                <input type="hidden" value="${hutangKaryawan}" id="hutang" />
                                            </div>
                                            <div>
                                                <button class="btn btn-primary btn-sm radiusSendiri openModal" value="${data[0].id_sewa}">
                                                    <span class="fas fa-sticky-note mr-1"></span> Input UJ
                                                </button>
                                            </div>
                                        </div>
                                    </td>`);
                                row.append(`<td></td>`);
                                row.append(`<td>${dateMask(data[i].tanggal_berangkat)}</td>`);
                                row.append(`<td>${data[i].nama_tujuan}</td>`);
                                $("#hasil").append(row);
                        }

                        new DataTable('#tabel_batal', {
                            searching: false, paging: false, info: false, ordering: false,
                            rowGroup: {
                                dataSrc: [0] // di order grup dulu, baru customer
                            },
                            columnDefs: [
                                {
                                    targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                                    visible: false
                                },
                                {
                                    // targets: [ord, ord-1],
                                    // orderable: false, // matiin sortir kolom centang
                                },
                            ],
                        });
                    }
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
        }
    });

</script>
@endsection
