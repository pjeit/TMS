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
            <a href="{{route('pencairan_uang_jalan.index')}}">
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
        <div class="card radiusSendiri" style="position: relative; left: 0px; top: 0px;" id='table_jadwal'>
            <div class="card-header">
                <h3 class="card-title" style='font-size:12pt;' id="tanggal_periode">
                    <i class="fas fa-calendar mr-1"></i>Periode: {{$week_start_tampil}} s/d {{$week_end_tampil}}
                </h3>
                <div class=sr-only>
                    <input type='hidden' id="prev_week" value="{{ $week_start}}">
                    <input type='hidden' id="next_week" value="{{ $week_end}}">
                </div>
                <div class="card-tools">
                <ul class="nav nav-pills ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)"  style='border:1px' id="prev"><i class="fas fa-caret-left"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)" style='border:1px'  id="next"><i class="fas fa-caret-right"></i></a>
                    </li>
                </ul>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body">
                <span style='font-size:11pt;' class="badge bg-primary">Menunggu Persetujuan</span> <span style='font-size:11pt;' class="badge bg-warning">Menunggu Uang Jalan</span>  <span style='font-size:11pt;' class="badge bg-success">Dalam Perjalanan</span>  <span style='font-size:11pt;' class="badge bg-info">Selesai</span>  <span style='font-size:11pt;' class="badge bg-danger">Maintenance</span> <span style='font-size:11pt;' class="badge bg-dark">Batal Muat</span>    
                <table class="default_table table table-bordered table-striped">
                    <thead id="thead_tabel">
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
                    <tbody id="tbody_tabel">
                     
                    </tbody>
                </table>
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>
    </div>
</div>

<script>
$(document).ready(function () {
    var tanggal_mulai = $('#prev_week').val();
    var tanggal_akhir = $('#next_week').val();

    var counterTambah=0;
    var counterKurang=0;

    $(document).on('click', '#next', function(e){
        counterTambah++;
        counterKurang = 0;
        showTable(tanggal_mulai,tanggal_akhir,counterTambah,counterKurang);
      
        // console.log("tambah : "+counterTambah);
        // console.log("kurang : "+counterKurang);

    });
    $(document).on('click', '#prev', function(e){
        counterKurang++;
        counterTambah = 0;
        showTable(tanggal_mulai,tanggal_akhir,counterTambah,counterKurang);
        
        console.log("tambah : "+counterTambah);
        console.log("kurang : "+counterKurang);
    });
    showTable(tanggal_mulai,tanggal_akhir,0,0);
    function showTable(tanggal_mulai,tanggal_akhir,tambah,kurang){
            var baseUrl = "{{ asset('') }}";
            var url = baseUrl+`dashboard/data/${tanggal_mulai}/${tanggal_akhir}/${tambah}/${kurang}`;
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    $("#thead_tabel").empty();
                    $("#tbody_tabel").empty();
                    

                    // var item = $('#item').val();
                    var data = response.data;

                    console.log( response);
                    $("#tanggal_periode").html(`<i class="fas fa-calendar mr-1"></i> Periode: ${response.tgl_minggu_awal_convert} s/d ${response.tgl_minggu_akhir_convert} `);
                    if(data != ''){
                        $("#thead_tabel").append(`
                            <tr>
                                <th style="width:12%">
                                    Nopol
                                </th>
                                <th style="width:11%">
                                    Driver
                                </th>
                             

                                ${response.semua_tanggal.map((day, index) => `
                                    <th style="width:11%">
                                        ${index === 0 ? 'Senin' : ''}
                                        ${index === 1 ? 'Selasa' : ''}
                                        ${index === 2 ? 'Rabu' : ''}
                                        ${index === 3 ? 'Kamis' : ''}
                                        ${index === 4 ? 'Jumat' : ''}
                                        ${index === 5 ? 'Sabtu' : ''}
                                        ${index === 6 ? 'Minggu' : ''}
                                        <input type="hidden" id="tanggal_hari_${index}" value="${day}">
                                    </th>
                                `).join('')}
                            </tr>
                        `);
                        for (var i = 0; i <data.length; i++) {
                            let cek_status = data[i].get_operasional_detail;
                            if(cek_status != 'SELESAI' || cek_status != 'MENUNGGU PEMBAYARAN INVOICE'){
                                console.log('cek_status', data[i].get_operasional_detail);
                                var row = $("<tr></tr>");
                                
                                row.append(`<td>${data[i].get_operasional_detail.map(item => 
                                `
                                <span class="badge badge-primary"> ${item.get_sewa_detail.no_polisi} (${item.get_sewa_detail.get_karyawan ? item.get_sewa_detail.get_karyawan.nama_panggilan : 'REKANAN'} </span><br>
                                <span class="badge badge-secondary"> ${dateMask(item.get_sewa_detail.tanggal_berangkat)} </span>
                                <span class="badge badge-success"> ${item.get_sewa_detail.no_sewa} </span>

                                `
                                ).join('<br> <br>')}</td>`);
                                row.append(`<td> <span class="badge badge-warning">Total dicairkan: ${moneyMask(data[i].total_dicairkan) }</span>
                                                                                        </td>`);
                                row.append(`<td> 

                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                    <div class="dropdown-menu" >
                                        
                                        <a href="${baseUrl}refund_biaya_operasional/${data[i].id}/edit" class="dropdown-item btn-danger">
                                            <span class="nav-icon fas fa-undo mr-3"></span> Refund
                                        </a>
                                        
                                    </div>
                            </div>
                                    </td>`);
                                $("#tbodyId").append(row);
                            }
                        }
                    }
                    else{
                        console.log('else');
                        $("thead tr").append(`<th>Revisi Biaya Operasional</th>`);
                        // $("#rowGroup").dataTable();
                        $('#rowGroup').DataTable().draw();

                        // $('#rowGroup').DataTable().clear().draw();
                    }
                },error: function (xhr, status, error) {
                    if ( xhr.responseJSON.result == 'error') {
                        console.log("Error:", xhr.responseJSON.message);
                        console.log("XHR status:", status);
                        console.log("Error:", error);
                        console.log("Response:", xhr.responseJSON);
                    } else {
                        toastr.error("Terjadi kesalahan saat menerima data. " + error);
                    }
                }
            });
        }
    // new DataTable('#tabelSewa', {
    //     // "ordering": true,
    //     responsive: true,
    //     order: [
    //         [0, 'asc'],
    //     ],
    //     rowGroup: {
    //         dataSrc: [0]
    //     },
    //     columnDefs: [
    //         {
    //             targets: [0],
    //             visible: false
    //         },
    //         // {
    //         //     "orderable": false,
    //         //     "targets": [0,1,2,3,4,5,6]
    //         // }
    //     ],
    // }); 
});

</script>
@endsection