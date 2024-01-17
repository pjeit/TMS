
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
                                    <label for="">Tanggal Mulai <sup><i class="fa fa-question-circle" title="Tanggal JO dibuat"></i></sup> </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" name="tanggal_awal" autocomplete="off" class="date form-control" id="tanggal_awal" placeholder="dd-M-yyyy" value="{{ isset($request['tanggal_awal'])? $request['tanggal_awal'] ?? '':date("d-M-Y") }}">     
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">Tanggal Akhir <sup><i class="fa fa-question-circle" title="Tanggal JO dibuat"></i></sup> </label>
                                    <div class="input-group">
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
                                <label for="">Pengirim<span class="text-red">*</span></label>
                                <select width="100%" class="form-control select2" name="customer" id="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    <option value="SEMUA DATA">── Semua Customer ──</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">[{{ $customer->kode }}] {{ $customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Pelayaran<span class="text-red">*</span></label>
                                <select class="form-control select2" name="pelayaran" id="pelayaran"
                                    data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                    <option value="SEMUA DATA">── Semua Pelayaran ──</option>
                                    @foreach ($supplier as $supp)
                                    <option value="{{$supp->id}}">{{$supp->nama}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group ">
                                <label for="">Status Kontainer<span class="text-red">*</span> </label>
                                <select width="100%" class="form-control select2" name="status" id="status" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    <option value="SEMUA STATUS">SEMUA STATUS</option>
                                    <option value="BELUM DOORING">BELUM DOORING</option>
                                    <option value="PROSES DOORING">PROSES DOORING</option>
                                    <option value="MENUNGGU INVOICE">MENUNGGU INVOICE</option>
                                    <option value="MENUNGGU PEMBAYARAN INVOICE">MENUNGGU PEMBAYARAN INVOICE</option>
                                    <option value="SELESAI">SELESAI</option>
                                </select>
                            </div>
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
            <table class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;" id="invoice">
                <thead>
                    <tr>
                        <th>No. BL</th>
                        <th>No. Kontainer</th>
                        <th>Pengirim</th>
                        <th>Pelayaran</th>
                        <th>Tgl Dooring</th>
                        <th>Tgl Sandar</th>
                        <th>Tgl JO Dibuat</th>
                        <th>Status Kontainer</th>
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
            let pelayaran    = $('#pelayaran').val();
            let status      = $('#status').val();   

            // Build the URL with parameters
            let url = `laporan_job_order/load_data?tgl_mulai=${tgl_mulai}&tgl_akhir=${tgl_akhir}&customer=${customer}&pelayaran=${pelayaran}&status=${status}`;

            fetch(url)
            .then(response => response.json())
            .then(datas => {
                $('#invoice').DataTable().destroy();
                $('#invoice tbody').empty();

                if(datas.result == 'success'){
                    const data = datas.data;
                    // console.log('datas: '+ JSON.stringify(datas, null, 2));

                    if (data.length > 0) {
                        const rows = data.map(item => (
                            `<tr>
                                <td><b>◾ ${item.get_j_o.no_bl}</b></td>
                                <td>${item.no_kontainer}</td>
                                <td> [${item.get_j_o.get_customer.kode}] ${item.get_j_o.get_customer.nama}</td>
                                <td>${item.get_j_o.get_supplier.nama}</td>
                                <td><small>${item.tgl_dooring != null? dateMask(item.tgl_dooring):''}</small></td>
                                <td><small>${dateMask(item.get_j_o.tgl_sandar)}</small></td>
                                <td><small>${dateMask(item.get_j_o.created_at)}</small></td>
                                <td><small>${item.status}</small></td>
                            </tr>`
                        ));
                        $("#result").append(rows);
                    }
                }
                
                var fileName = 'Laporan Invoice Trucking ' +  dateMask(Date.now());
                
                $('#invoice').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excel',
                            filename: fileName,
                        }
                    ],
                    order: [
                        [0, 'asc'], 
                    ],
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
