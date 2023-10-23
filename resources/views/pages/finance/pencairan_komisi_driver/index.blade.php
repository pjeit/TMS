
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
            <div class="card-header" style="border: 2px solid #bbbbbb;">
                <form id="form_report" action="{{ route('laporan_bank.index') }}" method="GET">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="karyawan">Pilih Driver<span class="text-red">*</span></label>
                                <select class="form-control select2" name="karyawan" id="karyawan" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    @foreach ($dataDriver as $data)
                                        <option value="{{$data->id}}" >{{ $data->nama_panggilan }} - ({{$data->telp1}})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row" >
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="periode">Tanggal Berangkat</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                        <input type="text" name="tanggal_awal" autocomplete="off" class="date form-control" id="tanggal_awal" placeholder="dd-M-yyyy" value="{{ isset($request['tanggal_awal'])? $request['tanggal_awal'] ?? '':date("d-M-Y") }}">  
                                        <span style="margin-left: 20px;">-</span>   
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                     <div class="form-group">
                                        <label for="periode" style="opacity: 0%;">Tanggal Akhir</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" name="tanggal_akhir" autocomplete="off" class="date  form-control" id="tanggal_akhir" placeholder="dd-M-yyyy" value="{{ isset($request['tanggal_akhir'])? $request['tanggal_akhir'] ?? '':date("d-M-Y") }}">  
                                        </div>
                                    </div>
                                </div>
                             
                            </div>
                             <button type="button" id="btnFilter" class="btn btn-primary radiusSendiri" ><i class="fas fa-search"></i> <b> Filter</b></button>
                            
                        </div>
                        <div class="col-6">
                            <label for="Total">Total</label>
                            <ul class="list-group mb-1">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Total (IDR)</span>
                                    <input type="hidden" name="total_pencairan" value="1000000">
                                    <strong>Rp. 1,000,000.00</strong>
                                </li>
                            </ul>
                            <label for="tipe">Pilih Kas/Bank</label>
                            <div class="input-group" style="gap: 10px;">
                                <select class="form-control select2" name="tipe" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    @foreach ($kasBank as $kb)
                                        <option value="{{$kb->id}}" <?= $kb->id == 1 ? 'selected':''; ?> >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-success radiusSendiri" id="bttonBayar"><i class="fa fa-credit-card" aria-hidden="true"></i> Pencairan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- /.card-header -->
            <div class="card-body" style="overflow: auto;">
                <table class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;">
                    <thead>
                        <tr>
                            {{-- <th></th> --}}
                            <th style="width:1px; white-space: nowrap;">Tgl. Kembali</th>
                            <th>Nama Tujuan</th>
                            <th>Alamat Tujuan</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Komisi Driver</th>
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
        });
        $('#tanggal_akhir').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
        });

        // console.log($('#tanggal_awal').val());
        // console.log($('#tanggal_akhir').val());
        // console.log($('#karyawan').val());
        var baseUrl = "{{ asset('') }}";
        $('body').on('click','#btnFilter', function (){
            $.ajax({
                    url: `${baseUrl}pencairan_komisi_driver/load_data`, 
                    method: 'GET', 
                    data: {
                        tanggal_awal: $('#tanggal_awal').val(),
                        tanggal_akhir: $('#tanggal_akhir').val(),
                        karyawan: $('#karyawan').val()
                    },
                    success: function(response) {
                        if(response)
                        {
                            console.log(response);
                        }
                      
            
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
           
        })
                
    });

</script>
@endsection
