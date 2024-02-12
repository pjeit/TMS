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
    a {
        color: inherit;
        text-decoration: none !important;
    } 
    .info-box{
      border-radius: 10px !important;
    }
    .info-box:hover{
        transition: transform 0.4s ease;
        transform: scale(1.1);
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <a href="">
                <div class="info-box shadow">
                    {{-- <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span> --}}
                    <div class="info-box-content">
                        <span class="info-box-text">Pengingat Dokumen Kendaraan</span>
                        <span class="info-box-number">{{$dokumen['data']->info}}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <a href="{{route('dalam_perjalanan.index')}}">
                <div class="info-box shadow">
                    {{-- <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span> --}}
                    <div class="info-box-content">
                        <span class="info-box-text">Belum Kembali</span>
                        <span class="info-box-number">{{$dalam_perjalanan['data']->info}}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <a href="{{route('status_kendaraan.index')}}">
                <div class="info-box shadow">
                    {{-- <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span> --}}
                    <div class="info-box-content">
                        <span class="info-box-text">Maintenance</span>
                        <span class="info-box-number">{{$status_kendaraan['data']->info}}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <a href="{{route('booking.index')}}">
                <div class="info-box shadow">
                    {{-- <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span> --}}
                    <div class="info-box-content">
                        <span class="info-box-text">Booking</span>
                        <span class="info-box-number">{{$booking['data']->info}}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <a href="{{route('belum_invoice.index')}}">
                <div class="info-box shadow">
                    {{-- <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span> --}}
                    <div class="info-box-content">
                        <span class="info-box-text">Belum Invoice</span>
                        <span class="info-box-number">{{$belum_invoice['data']->info}}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <a href="{{route('pembayaran_invoice.index')}}">
                <div class="info-box shadow">
                    {{-- <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span> --}}
                    <div class="info-box-content">
                        <span class="info-box-text">Customer Jatuh Tempo</span>
                        <span class="info-box-number">{{$pembayaran_invoice_jatuh_tempo['data']->info}}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <a href="{{route('tagihan_pembelian.index')}}">
                <div class="info-box shadow">
                    {{-- <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span> --}}
                    <div class="info-box-content">
                        <span class="info-box-text">Nota Jatuh Tempo</span>
                        <span class="info-box-number">{{$tagihan_pembelian['data']->info}}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3">
            <a href="{{route('dalam_perjalanan.index')}}">
                <div class="info-box shadow">
                    {{-- <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span> --}}
                    <div class="info-box-content">
                        <span class="info-box-text">Pencairan Uang Jalan</span>
                        <span class="info-box-number">{{$menunggu_uang_jalan['data']->info}}</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
    <!-- Left col -->
    <section class="col-lg-12">
        <!-- Custom tabs (Charts with tabs)-->
        <div class="card" style="position: relative; left: 0px; top: 0px;" id='table_jadwal'>
        <div class="card-header">
            <h3 class="card-title" style='font-size:12pt;'>
            <i class="fas fa-calendar mr-1"></i>Periode: {{$week_start}} s/d {{$week_end}}
            </h3>
            <div class=sr-only>
                <input type='hidden' id="prev_week" value="{{ $weeknumber-1}}">
                <input type='hidden' id="next_week" value="{{ $weeknumber+1}}">
            </div>
            <div class="card-tools">
            <ul class="nav nav-pills ml-auto">
                <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" onclick="get_jadwal('prev')" style='border:1px'><i class="fas fa-caret-left"></i></a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="javascript:void(0)" onclick="get_jadwal('next')" style='border:1px'><i class="fas fa-caret-right"></i></a>
                </li>
            </ul>
            </div>
        </div><!-- /.card-header -->
        <div class="card-body">
            <span style='font-size:11pt;' class="badge bg-primary">Menunggu Persetujuan</span> <span style='font-size:11pt;' class="badge bg-warning">Menunggu Uang Jalan</span>  <span style='font-size:11pt;' class="badge bg-success">Dalam Perjalanan</span>  <span style='font-size:11pt;' class="badge bg-info">Selesai</span>  <span style='font-size:11pt;' class="badge bg-danger">Maintenance</span> <span style='font-size:11pt;' class="badge bg-dark">Batal Muat</span>    
            <table class="default_table table table-bordered table-striped">
                <thead>
                <tr>
                <th style="width:12%">Nopol</th>
                <th style="width:11%">Driver</th>
                <th style="width:11%">Senin</th>
                <th style="width:11%">Selasa</th>
                <th style="width:11%">Rabu</th>
                <th style="width:11%">Kamis</th>
                <th style="width:11%">Jumat</th>
                <th style="width:11%">Sabtu</th>
                <th style="width:11%">Minggu</th>
                </tr>
                </thead>
                <!--<tbody>
                    <?php if(!empty($data)){
                    foreach($data as $kendaraan=>$jadwal){$info=explode('_',$kendaraan);?>
                        <tr>
                            <td><?= $info[0];?></td>
                            <td><?= $info[1];?></td>
                            <?php foreach($jadwal as $hari=>$value){ ?>
                            <td>
                            <?php foreach($value as $objek){
                                $color='';
                                if(strtolower($objek->status)=='open'){$color='bg-info';}
                        elseif(strtolower($objek->status)=='approved' && $objek->total_uang_jalan > 0){$color='bg-warning';}
                        elseif(strtolower($objek->status)=='released' || (strtolower($objek->status)=='approved' && $objek->total_uang_jalan == 0)){$color='bg-success';}
                                elseif(strtolower($objek->status)=='finished'){$color='bg-info';}
                                elseif(strtolower($objek->status)=='maintenance'){$color='bg-danger';}
                                                            elseif(strtolower($objek->status)=='canceled'){$color='bg-dark';}
                            ?>
                                <span class="badge <?= $color; ?>">
                                    <?php if($objek->nama_tujuan!='' && !empty($objek->nama_tujuan)){echo $objek->nama_tujuan;}?></span>
                            <?php }?>
                            </td>
                            <?php }?>
                        </tr>
                    <?php }}?>
                </tbody>-->
            </table>
        </div><!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>
    </div>
</div>

<script>
$(document).ready(function () {
    new DataTable('#tabelSewa', {
        // "ordering": true,
        responsive: true,
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
            // {
            //     "orderable": false,
            //     "targets": [0,1,2,3,4,5,6]
            // }
        ],
    }); 
});

</script>
@endsection