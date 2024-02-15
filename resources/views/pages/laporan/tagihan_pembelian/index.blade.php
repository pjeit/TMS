
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
    .dtrg-end {
        padding: 0px!important;
        margin: 0px!important;
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
                                <label for="">Supplier<span class="text-red">*</span></label>
                                <select width="100%" class="form-control select2" name="supplier" id="supplier" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    <option value="SEMUA SUPPLIER">── Semua Supplier ──</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier['id'] }}">{{ $supplier['nama'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group ">
                                <label for="">Status <span class="text-red">*</span></label>
                                <select width="100%" class="form-control select2" name="status" id="status" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    <option value="SEMUA STATUS">SEMUA STATUS</option>
                                    <option value="LUNAS">LUNAS</option>
                                    <option value="BELUM LUNAS">BELUM LUNAS</option>
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
            <table class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;" id="tagihan_pembelian">
                <thead id="header">
                    <tr>
                        <th>Supplier</th>
                        <th>No. Nota</th>
                        <th>Tgl. Nota</th>
                        <th>Jatuh Tempo</th>
                        <th>Tgl. Pembayaran Terakhir</th>
                        <th>Tagihan</th>
                        <th>PPh23</th>
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
            let supplier    = $('#supplier').val();
            let status      = $('#status').val();   

            // Build the URL with parameters
            let url = `laporan_tagihan_pembelian/load_data?tgl_mulai=${tgl_mulai}&tgl_akhir=${tgl_akhir}&supplier=${supplier}&status=${status}`;

            fetch(url)
            .then(response => response.json())
            .then(datas => {
                $('#tagihan_pembelian').DataTable().destroy();
                // $('#klaim thead').empty();
                $('#tagihan_pembelian tbody').empty();

                if(datas.result == 'success'){
                    const data = datas.data;
                    // console.log('datas: '+ JSON.stringify(datas, null, 2));

                    if(data.length > 0){
                        for (let i = 0; i < data.length; i++) {
                            var row = $("<tr></tr>");
                            row.append(`<td>${data[i].get_supplier.nama}</td>`);
                            row.append(`<td>${data[i].no_nota}</td>`);
                            row.append(`<td>${dateMask(data[i].tgl_nota)}</td>`);
                            row.append(`<td>${dateMask(data[i].jatuh_tempo)}</td>`);
                            row.append(`<td>${data[i].get_pembayaran != null? dateMask(data[i].get_pembayaran.tgl_bayar):'-'}</td>`);
                            row.append(`<td style='text-align: end'>${data[i].total_tagihan != null? moneyMask(data[i].total_tagihan):0}</td>`);
                            row.append(`<td style='text-align: end'>${data[i].pph != null? moneyMask(data[i].pph):0}</td>`);
                            row.append(`<td style='text-align: end'>${data[i].tagihan_dibayarkan != null? moneyMask(data[i].tagihan_dibayarkan):0}</td>`);
                            row.append(`<td style='text-align: end'>${data[i].sisa_tagihan != null? moneyMask(data[i].sisa_tagihan):0}</td>`);
                            $("#result").append(row);
                        }
                    }
                }
                
                $('#tagihan_pembelian').DataTable({
                    "responsive": true,
                    order: [[0, 'asc']],
                    rowGroup: {
                        dataSrc: [0] 
                    },
                    rowGroup: {
                        endRender: function ( rows, group ) {
                            let tagihan = pph = bayar = sisa = 0;

                            for (let i = 0; i < rows.data().length ; i++) {
                                let colTagihan = rows.data().pluck(5)[i];
                                let colPph = rows.data().pluck(6)[i];
                                let colBayar = rows.data().pluck(7)[i];
                                let colSisa = rows.data().pluck(8)[i];

                                tagihan += parseFloat(colTagihan.replace(/,/g, ''));
                                pph += parseFloat(colPph.replace(/,/g, ''));
                                bayar += parseFloat(colBayar.replace(/,/g, ''));
                                sisa += parseFloat(colSisa.replace(/,/g, ''));
                            }
                            return $('<tr/>')
                                        .append('<th style="background: #8fff91" colspan="4"><strong>Total :</strong></th>')
                                        .append('<th style="text-align: end; background: #8fff91;"><strong>' + moneyMask(tagihan) + '</strong></th>')
                                        .append('<th style="text-align: end; background: #8fff91;"><strong>' + moneyMask(pph) + '</strong></th>')
                                        .append('<th style="text-align: end; background: #8fff91;"><strong>' + moneyMask(bayar) + '</strong></th>')
                                        .append('<th style="text-align: end; background: #8fff91;"><strong>' + moneyMask(sisa) + '</strong></th>');
                        }, 
                        // startRender: null // ini buat nge hide row group yg di atas
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
