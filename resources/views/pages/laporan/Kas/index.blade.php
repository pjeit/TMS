
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
    <div class="row">
        <section class="col-lg-12">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card radiusSendiri" style="position: relative; left: 0px; top: 0px; background-color:#edf4fc;">
            <div class="card-header ui-sortable-handle">
                <input type="hidden" value="https://testapps.pjexpress.co.id/index.php/C_lap_kas/export_to_excel" id="url_download">
                <form id="form_report" action="https://testapps.pjexpress.co.id/index.php/c_lap_kas/get_laporan">
                    <div class="form-group">
                        <label for="periode">Periode</label>
                        <div class="col-lg-12">
                            <div class="row">
                                <input type="text" name="tanggal_awal" autocomplete="off" class="date form-control" id="tanggal_awal" placeholder="dd-M-yyyy" style="width:120px;text-align:center">
                                <label for="periode">&nbsp; s/d &nbsp;</label>  
                                <input type="text" name="tanggal_akhir" autocomplete="off" class="date form-control" id="tanggal_akhir" placeholder="dd-M-yyyy" style="width:120px;text-align:center">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-sm btn-primary" onclick="show_report()"><i class="fas fa-search"></i> Tampilkan Data</button>
                        <button type="button" class="btn btn-sm btn-success" onclick="download_report()"><i class="fas fa-file-excel"></i> Export to Excel</button>
                    </div>
                </form>
            </div><!-- /.card-header -->
            </div>
            <!-- /.card -->
        </section>

        <section class="col-lg-12" id="show_report">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th style="width:1px; white-space: nowrap;">Tgl. Transaksi</th>
                        <th>Jenis</th>
                        <th>Deskripsi</th>
                        <th style="width:1px; white-space: nowrap; text-align:right;">Debit</th>
                        <th style="width:1px; white-space: nowrap; text-align:right;">Kredit</th>
                        <th style="width:1px; white-space: nowrap; text-align:right;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                        @if (isset($data))
                            <tr>
                                <td colspan="7">KAS KECIL {{$kas->saldo_sekarang}} {{$sumDebit}} {{$sumKredit}} ({{number_format($kas->saldo_sekarang + $sumDebit - $sumKredit)}})</td>
                            </tr>
                            @foreach ($data as $item)
                            <tr>
                                <td></td>
                                <td>{{$item->tanggal}}</td>
                                <td>{{$item->kode_coa}}</td>
                                <td>{{$item->keterangan_transaksi}}</td>
                                <td>{{number_format($item->debit)}}</td>
                                <td>{{number_format($item->kredit)}}</td>
                                <td></td>
                            </tr>
                            @endforeach
                        @endif
                </tbody>
            </table>
        </section>
    </div>
</div>

@endsection
