
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
    .select2 {
        width:100%!important;
    }
</style>
<div class="container-fluid">
    <div class="card">
        <div class="card-header ">
            <div class="" style="position: relative; left: 0px; top: 0px; background-color:#edf4fc;">
                <div class="card-header" style="border: 2px solid #bbbbbb;">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="periode">Tanggal Mulai</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" name="tanggal_awal" autocomplete="off" class="date form-control" id="tanggal_awal" placeholder="dd-M-yyyy" value="{{ isset($request['tanggal_awal'])? $request['tanggal_awal'] ?? '':date("d-M-Y") }}">     
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="periode">Tanggal Akhir</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" name="tanggal_akhir" autocomplete="off" class="date  form-control" id="tanggal_akhir" placeholder="dd-M-yyyy" value="{{ isset($request['tanggal_akhir'])? $request['tanggal_akhir'] ?? '':date("d-M-Y") }}">     
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-4 col-sm-12 d-flex flex-column" style="border-left: 1px solid gray">
                            <div class="form-group ">
                                <label for="">Billing to<span class="text-red">*</span></label>
                                <select width="100%" class="form-control select2" name="customer" id="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    <option value="SEMUA CUSTOMER">── Semua Customer ──</option>
                                    @foreach ($customers as $item)
                                        <option value="{{ $item->id }}">[{{ $item->kode }}] {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="form-group ">
                                <label for="">Status <span class="text-red">*</span></label>
                                <select width="100%" class="form-control select2" name="status" id="status" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    <option value="SEMUA STATUS">SEMUA STATUS</option>
                                    <option value="ACCEPTED">DITERIMA</option>
                                    <option value="REJECTED">DITOLAK</option>
                                    <option value="PENDING">PENDING</option>
                                </select>
                            </div> --}}
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-12 d-flex flex-column align-items-center justify-content-center" style="border-left: 1px solid gray">
                            <div class="form-group ">
                                <button type="button" class="btn btn-primary radiusSendiri show"><i class="fas fa-search"></i> <b> Tampilkan Data</b></button>
                            </div>

                            {{-- <div class="form-group ">
                                <button type="button" class="btn btn-success radiusSendiri excel"><i class="fas fa-file-excel"></i> <b> Export Excel</b></button>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body" style="overflow: auto;">
            <table class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;" id="pemutihan">
                <thead id="header">
                    <tr>
                        <th>Customer</th>
                        <th>No. Invoice</th>
                        <th>Tgl Pemutihan</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody id="result">
                
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#tanggal_awal').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            orientation: "bottom",
        });
        $('#tanggal_akhir').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            orientation: "bottom",
        });

        $(document).on('click', '.show', function(e){
            let tgl_mulai   = $('#tanggal_awal').val();
            let tgl_akhir   = $('#tanggal_akhir').val();
            let customer    = $('#customer').val();

            // Build the URL with parameters
            let url = `laporan_pemutihan/load_data?tgl_mulai=${tgl_mulai}&tgl_akhir=${tgl_akhir}&customer=${customer}`;

            fetch(url)
            .then(response => response.json())
            .then(datas => {
                $('#pemutihan').DataTable().destroy();
                // $('#pemutihan thead').empty();
                $('#pemutihan tbody').empty();

                if(datas.result == 'success'){
                    const data = datas.data;
                    console.log('datas: '+ JSON.stringify(datas, null, 2));

                    if(data.length > 0){
                        for (let i = 0; i < data.length; i++) {
                            var row = $("<tr></tr>");
                            row.append(`<td>${data[i].invoice.get_billing_to.nama}</td>`);
                            row.append(`<td>${data[i].invoice.no_invoice}</td>`);
                            row.append(`<td>${dateMask(data[i].tanggal)}</td>`);
                            row.append(`<td>${moneyMask(data[i].nominal_pemutihan)}</td>`);
                            $("#result").append(row);
                        }
                    }
                }
                
                var fileName = 'Laporan Pemutihan ' +  dateMask(Date.now());
                
                $('#pemutihan').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excel',
                            filename: fileName,
                        }
                    ],
                    order: [[0, 'asc']],
                    rowGroup: {
                        dataSrc: [0] 
                    },
                    columnDefs: [
                        {
                            targets: [0], 
                            visible: false
                        },
                        { orderable: true, targets: 0 }, // Enable ordering for the first column (index 0)
                        { orderable: false, targets: '_all' } // Disable ordering for all other columns
                    ],
                    // destroy: true,      // destroy old data and create new one
                    info: false,        // Disable showing entries
                    searching: false,   // Disable searching
                    paging: false,      // Disable pagination
                    // ordering: false,    // Disable ordering
                    "language": {
                        "emptyTable": "Data tidak ditemukan."
                    }
                });
                
            }).catch(error => {
                console.error('Error:', error);
            })
        })

        
    });
</script>
@endsection
