
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
                        <label for="select_bank">Bank</label>
                        <div class="col-lg-12">
                            <div class="row">
                                <select id="select_bank" name="select_bank" style="width:271px" data-placeholder="Pilih Bank" data-select2-id="select2-data-select_bank" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                                 <option value="1" data-select2-id="select2-data-11-e6lc">KAS BESAR [BCA]</option>
                                </select><span class="select2 select2-container select2-container--default select2-container--below" dir="ltr" data-select2-id="select2-data-1-swgg" style="width: 271px;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-select_bank-container"><span class="select2-selection__rendered" id="select2-select_bank-container" role="textbox" aria-readonly="true"><span class="select2-selection__placeholder">Pilih Bank</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                                <input type="hidden" name="kas_bank_id" id="kas_bank_id" value="">
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
                        <tr>
                            <td colspan="7">KAS KECIL (Saldo:&nbsp;4,009,100)</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>31-Aug-2023</td>
                            <td></td>
                            <td>Saldo Awal</td>
                            <td style="text-align:right">0</td>
                            <td style="text-align:right">1,079,900</td>
                            <td style="text-align:right">-1,079,900</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align:right"><label>Total</label></td>
                            <td style="text-align:right"><label>0</label></td>
                            <td style="text-align:right"><label>1,079,900</label></td>
                            <td></td>
                        </tr>
                </tbody>
            </table>
        </section>
    </div>
</div>

@endsection
