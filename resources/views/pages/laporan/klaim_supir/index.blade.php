
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
                                <label for="">Jenis<span class="text-red">*</span></label>
                                <select width="100%" class="form-control select2" name="jenis" id="jenis" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    <option value="">── Pilih ──</option>
                                    <option value="KENDARAAN">Kendaraan</option>
                                    <option value="DRIVER">Driver</option>
                                    <option value="JENIS">Jenis Klaim</option>
                                </select>
                            </div>

                            <div class="form-group ">
                                <label for="">Status <span class="text-red">*</span></label>
                                <select width="100%" class="form-control select2" name="status" id="status" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    <option value="SEMUA STATUS">SEMUA STATUS</option>
                                    <option value="ACCEPTED">DITERIMA</option>
                                    <option value="REJECTED">DITOLAK</option>
                                    <option value="PENDING">PENDING</option>
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
            <table class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;" id="klaim">
                <thead id="header">
                    <tr>
                        <th>LAPORAN KLAIM SUPIR</th>
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
            let jenis       = $('#jenis').val();
            let status      = $('#status').val();   

            if(tgl_mulai == '' || tgl_akhir == '' || jenis == '' || status == ''){
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    timer: 2500,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: 'warning',
                    title: 'Harap lengkapi filter'
                })
                e.preventDefault();
                return false;
            }

            // Build the URL with parameters
            let url = `laporan_klaim_supir/load_data?tgl_mulai=${tgl_mulai}&tgl_akhir=${tgl_akhir}&jenis=${jenis}&status=${status}`;

            fetch(url)
            .then(response => response.json())
            .then(datas => {
                $('#klaim').DataTable().destroy();
                $('#klaim thead').empty();
                $('#klaim tbody').empty();

                if(datas.result == 'success'){
                    const data = datas.data;
                    console.log('datas: '+ JSON.stringify(datas, null, 2));
                    if(jenis == 'KENDARAAN'){
                        var row = $("<tr></tr>");
                        row.append(`
                            <th>Kendaraan</th>
                            <th>Jenis Klaim</th>
                            <th>Driver</th>
                        `);
                    }else if(jenis == 'DRIVER'){
                        var row = $("<tr></tr>");
                        row.append(`
                            <th>Driver</th>
                            <th>Jenis Klaim</th>
                            <th>Kendaraan</th>
                        `);
                    }else if(jenis == 'JENIS'){
                        var row = $("<tr></tr>");
                        row.append(`
                            <th>Jenis Klaim</th>
                            <th>Driver</th>
                            <th>Kendaraan</th>
                        `);
                    }
                    row.append(`
                        <th>Tanggal Klaim</th>
                        <th>Tanggal Pencairan</th>
                        <th>Status Klaim</th>
                        <th>Jumlah Klaim</th>
                        <th>Jumlah Dicairkan</th>
                        <th>Keterangan Klaim</th>
                    `);
                    $("#header").append(row);

                    if(data.length > 0){
                        for (let i = 0; i < data.length; i++) {
                            var row = $("<tr></tr>");
                            if(jenis == 'KENDARAAN'){
                                row.append(`<td>${data[i].kendaraan.no_polisi}</td>`);
                                row.append(`<td>${data[i].jenis_klaim}</td>`);
                                row.append(`<td>${data[i].karyawan.nama_panggilan}</td>`);
                            }else if(jenis == 'DRIVER'){
                                row.append(`<td>${data[i].karyawan.nama_panggilan}</td>`);
                                row.append(`<td>${data[i].jenis_klaim}</td>`);
                                row.append(`<td>${data[i].kendaraan.no_polisi}</td>`);
                            }else if(jenis == 'JENIS'){
                                row.append(`<td>${data[i].jenis_klaim}</td>`);
                                row.append(`<td>${data[i].karyawan.nama_panggilan}</td>`);
                                row.append(`<td>${data[i].kendaraan.no_polisi}</td>`);
                            }
                            row.append(`<td>${dateMask(data[i].tanggal_klaim)}</td>`);
                            row.append(`<td>${data[i].klaim_riwayat? dateMask(data[i].klaim_riwayat.tanggal_pencairan):'-'}</td>`);
                            row.append(`<td>${data[i].status_klaim}</td>`);
                            row.append(`<td>${moneyMask(data[i].total_klaim)}</td>`);
                            row.append(`<td>${data[i].klaim_riwayat? moneyMask(data[i].klaim_riwayat.total_pencairan):0}</td>`);
                            row.append(`<td>${data[i].keterangan_klaim}</td>`);
                            $("#result").append(row);
                        }
                    }
                }

                var fileName = 'Laporan Klaim Supir ' +  dateMask(Date.now());
                
                $('#klaim').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excel',
                            filename: fileName,
                        }
                    ],
                    order: [[0, 'asc']],
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
