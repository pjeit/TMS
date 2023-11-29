
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
            {{-- <div class="" style="position: relative; left: 0px; top: 0px; background-color:#edf4fc;"> --}}
                <div class="card-header" style="border: 2px solid #bbbbbb;">
                    {{-- <form id="form_report" action="{{ route('laporan_bank.index') }}" method="GET"> --}}
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
                                        <option value="ALL DATA">── Semua Customer ──</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">[{{ $customer->kode }}] {{ $customer->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group ">
                                    <label for="">Status <span class="text-red">*</span></label>
                                    <select width="100%" class="form-control select2" name="status" id="status" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                        <option value="LUNAS">LUNAS</option>
                                        <option value="BELUM LUNAS">BELUM LUNAS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-12 d-flex flex-column align-items-center justify-content-center" style="border-left: 1px solid gray">
                                <div class="form-group ">
                                    <button type="button" class="btn btn-primary radiusSendiri show"><i class="fas fa-search"></i> <b> Tampilkan Data</b></button>
                                </div>

                                <div class="form-group ">
                                    <button type="button" class="btn btn-success radiusSendiri excel"><i class="fas fa-file-excel"></i> <b> Export Excel</b></button>
                                </div>
                            </div>
                        </div>
                    {{-- </form> --}}
                </div>
            {{-- </div> --}}
        </div>
        
        <div class="card-body" style="overflow: auto;">
            <table class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;" id="invoice">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>No. Invoice</th>
                        <th>Tgl. Invoice</th>
                        <th>Jatuh Tempo</th>
                        <th>Tgl. Pembayaran Terakhir</th>
                        <th>Tagihan	PPh23</th>
                        <th>Bayar</th>
                        <th>Sisa Tagihan</th>
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
            let status      = $('#status').val();   

            // Build the URL with parameters
            let url = `load_data?tgl_mulai=${tgl_mulai}&tgl_akhir=${tgl_akhir}&customer=${customer}&status=${status}`;

            fetch(url)
            .then(response => response.json())
            .then(datas => {
                $('#invoice').DataTable().destroy();
                $('#invoice tbody').empty();

                if(datas.result == 'success'){
                    const data = datas.data;
                    // console.log('datas: '+ JSON.stringify(datas, null, 2));

                    if(data.length > 0){
                        for (let i = 0; i < data.length; i++) {
                            var row = $("<tr></tr>");
                            row.append(`<td>[${data[i].get_billing_to.kode}] ${data[i].get_billing_to.nama}</td>`);
                            row.append(`<td>${data[i].no_invoice}</td>`);
                            row.append(`<td>${dateMask(data[i].tgl_invoice)}</td>`);
                            row.append(`<td>${dateMask(data[i].jatuh_tempo)}</td>`);
                            row.append(`<td>${dateMask(Date(data[i].updated_at))}</td>`);
                            row.append(`<td>${moneyMask(data[i].pph)}</td>`);
                            row.append(`<td>${moneyMask(data[i].total_dibayar)}</td>`);
                            row.append(`<td>${moneyMask(data[i].total_sisa)}</td>`);
                            $("#result").append(row);
                        }
                    }
                }
                
                $('#invoice').DataTable({
                    order: [
                        [0, 'asc'], 
                    ],
                    rowGroup: {
                        dataSrc: [0] 
                    },
                    // destroy: true,      // destroy old data and create new one
                    info: false,        // Disable showing entries
                    searching: false,   // Disable searching
                    paging: false,      // Disable pagination
                    ordering: false,    // Disable ordering
                    "language": {
                        "emptyTable": "Data tidak ditemukan."
                    }
                });
                
            }).catch(error => {
                // Handle errors here
                console.error('Error:', error);
            })
        })

        
    });
</script>
@endsection
