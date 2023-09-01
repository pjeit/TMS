
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
    <div class="card">
        {{-- <div class="row"> --}}
            <div class="card-header ">
                {{-- <div class="" style="position: relative; left: 0px; top: 0px; background-color:#edf4fc;"> --}}
                    <div class="card-header">
                            <form id="form_report" action="{{ route('laporan_kas.index') }}" method="GET">
                                <div class="form-group">
                                    <label for="periode">Periode</label>
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                    </div>
                                                    <input type="text" name="tanggal_awal" autocomplete="off" class="date  form-control" id="tanggal_awal" placeholder="dd-M-yyyy" value="{{ $request['tanggal_awal'] ?? '' }}">     
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="periode" class="mt-2 ml-2 mr-2">s/d</label>  
                                            </div>
                                            <div class="form-group">
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                    </div>
                                                    <input type="text" name="tanggal_akhir" autocomplete="off" class="date  form-control" id="tanggal_akhir" placeholder="dd-M-yyyy" value="{{ $request['tanggal_akhir'] ?? '' }}">     
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-sm btn-primary ml-3 mt-1 radiusSendiri" onclick="show_report()"><i class="fas fa-search"></i> <b>Tampilkan Data</b></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="form-group">
                                {{-- <button type="button" class="btn btn-sm btn-success" onclick="download_report()"><i class="fas fa-file-excel"></i> Export to Excel</button> --}}
                            </div>
                    </div><!-- /.card-header -->
                {{-- </div> --}}
            </div>
            
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th style="width:1px; white-space: nowrap;">Tgl. Transaksi</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Debit</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Kredit</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                            @if (isset($data))
                                <tr>
                                    {{-- <td colspan="7">KAS KECIL {{number_format($kas->saldo_sekarang)}} | DEBIT: {{number_format($sumDebit)}} | KREDIT: {{number_format($sumKredit)}} | TOT SKRG: ({{number_format($kas->saldo_sekarang + $sumDebit - $sumKredit)}})</td> --}}
                                    <td colspan="7">KAS KECIL (Saldo: {{number_format($kas->saldo_sekarang + $sumDebit - $sumKredit)}})</td>
                                </tr>
                                @foreach ($data as $item)
                                <tr>
                                    <td></td>
                                    <td>{{date('d-M-Y', strtotime($item->tanggal)) }}</td>
                                    <td>{{$item->jenis}}</td>
                                    <td>{{$item->keterangan_transaksi}}</td>
                                    <td>{{number_format($item->debit)}}</td>
                                    <td>{{number_format($item->kredit)}}</td>
                                    <td>{{number_format($item->total)}}</td>
                                </tr>
                                @endforeach
                            @endif
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
        });
        $('#tanggal_akhir').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
        });
    });

</script>
@endsection
