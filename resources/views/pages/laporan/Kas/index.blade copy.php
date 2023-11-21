
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
                    <div class="card-header" style="border: 2px solid #bbbbbb;">
                            <form id="form_report" action="{{ route('laporan_kas.index') }}" method="GET">
                                <div class="row" >
                                    <div class="col-2">
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
                                    <div class="col-2">
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
                                    {{-- <div class="col-4">
                                        <div class="form-group">
                                            <label for="">Kas / Bank <span class="text-red">*</span></label>
                                            <select class="form-control selectpicker" name="tipe" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                                @foreach ($kasBank as $kb)
                                                    <option value="{{$kb->id}}" <?= $request['tipe'] == $kb->id ? 'selected':''; ?> >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-4">
                                        <label for="">&nbsp;</label>
                                        <div class="d-flex justify-content-start" style="gap: 5px;">
                                            <button type="submit" class="btn btn-primary radiusSendiri " onclick=""><i class="fas fa-search"></i> <b> Tampilkan Data</b></button>
                                            <button type="button" class="btn btn-success radiusSendiri " onclick=""><i class="fas fa-file-excel"></i> <b> Export Excel</b></button>
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
                <table class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;">
                    <thead>
                        <tr>
                            {{-- <th></th> --}}
                            <th style="width:1px; white-space: nowrap;">Tgl. Transaksi</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Debit</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Kredit</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                            @php
                                $total_debit=$total_kredit=0;
                            @endphp
                            @if (isset($data))
                                <tr>
                                    {{-- <td colspan="7">KAS KECIL {{number_format($kas->saldo_sekarang)}} | DEBIT: {{number_format($sumDebit)}} | KREDIT: {{number_format($sumKredit)}} | TOT SKRG: ({{number_format($kas->saldo_sekarang + $sumDebit - $sumKredit)}})</td> --}}
                                    <td colspan="6">{{$kas->nama}} (Saldo: {{number_format($kas->saldo_sekarang, 2)}})</td>
                                </tr>
                                @foreach ($data as $key => $item)
                                    @php
                                        // ngitung jumlah kredit sama debit
                                        $total_kredit += $item->kredit;
                                        $total_debit += $item->debit;
                                    @endphp
                                <tr>
                                    {{-- <td>{{$key}}</td> --}}
                                    {{-- <td>{{date('d-M-Y', strtotime($item->tanggal)) }}</td> --}}
                                    <td>{{ $item->keterangan_transaksi == 'Saldo Awal'? '':date('d-M-Y', strtotime($item->tanggal)) }}</td>
                                    <td>{{$item->jenis_deskripsi}}</td>
                                    @if ($item->keterangan_transaksi == 'Saldo Awal')
                                        <td><b>{{$item->keterangan_transaksi}}</b></td>                                    
                                    @else
                                        <td>{{$item->keterangan_transaksi}}</td>
                                    @endif
                                    <td>{{number_format($item->debit, 2)}}</td>
                                    <td>{{number_format($item->kredit, 2)}}</td>
                                    <td>{{number_format($item->total, 2)}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan='3' style='text-align:right'><label>Total</label></td>
                                    <td style='text-align:right'><label><?= number_format($total_debit, 2);?></label></td>
                                    <td style='text-align:right'><label><?= number_format($total_kredit, 2);?></label></td>
                                    <td></td>
                                </tr>
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
