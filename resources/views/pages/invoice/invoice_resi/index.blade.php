@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')

@endsection

@section('content')
@include('sweetalert::alert')

<style>

.besarin_gambar {
    transform: scale(3.5);
    transition: transform 0.5s ease; /* Adjust the transition duration and easing as needed */
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    {{-- <button class="btn btn-primary btn-responsive float-left radiusSendiri bukakModalCreate">
                        <i class="fa fa-plus-circle"> </i> Tambah Data
                    </button> --}}
                    <a  class="btn btn-primary btn-responsive float-left radiusSendiri" href="{{route('invoice_resi.create')}}" >
                        <i class="fa fa-plus-circle"> </i> Tambah Data
                    </a> 
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="tabel_resi" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Jenis Pengiriman</th>
                                <th>Nomor Resi</th>
                                <th>Status Resi</th>
                                <th>Tanggal Resi</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($data))
                                @foreach ($data as $item)
                                <tr>
                                    <td>{{ $item->jenis_pengiriman }}</td>
                                    <td>{{ $item->no_resi }}</td>
                                    <td>{{ $item->status_resi }}</td>
                                    <td>{{ date("d-M-Y", strtotime($item->tanggal_resi))}}</td>
                                    <td>
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{route('invoice_resi.show',[$item->id])}}" class="dropdown-item ">
                                                    <span class="fas fa-edit mr-3"></span> Update Resi
                                                </a>
                                                <a href="{{ route('invoice_resi.destroy', [$item->id]) }}" class="dropdown-item" data-confirm-delete="true">
                                                    <span class="fas fa-trash mr-3"></span> Hapus
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>


<script type="text/javascript">
$(document).ready(function () {
    $('#tabel_resi').DataTable({
                    // order: [
                    //     [0, 'asc'],
                    // ],
                    // rowGroup: {
                    //     dataSrc: [0] // kalau mau grouping pake ini
                    // },
                    columnDefs: [
                        // {
                        //     targets: [0],
                        //     visible: false
                        // },
                        // { orderable: true, targets: 0 }, // Enable ordering for the first column (index 0)
                        { orderable: false, targets: '_all' } // Disable ordering for all other columns
                    ],
                    info: false,
                    searching: true,
                    paging: true,
                    language: {
                        emptyTable: "Data tidak ditemukan."
                    }
        });
    $('#tanggal_pembayaran').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            startDate: "0d"
        });
    var Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    timer: 2500,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
   
    $('#post_data').submit(function(event) {
            
        if($("#tanggal_pembayaran").val()=='')
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `TANGGAL PEMBAYARAN BELUM DIISI!`,
            })
            
            return;
        }
        
        if($("#total_nominal").val()=='' || normalize($("#total_nominal").val())==0)
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `Total pembayaran harus diisi`,
            })
            
            return;
        }
        
        let barisTabel = $("#tabel_kendaraan_parent > tbody tr");
        console.log(barisTabel.length + 'baris tabel');
        if (barisTabel.length == 0) {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `Detail kendaraan Tidak boleh Kosong!`,
            })
            return;
            
        }
        var flagError = false;
        for (var i = 0; i < $(".catatan").length; i++) {
            var indexFoto = $(".catatan").eq(i);
            var row = indexFoto.closest('tr');
            var select_kendaraan=row.find('.select_kendaraan').val();

            
            if(select_kendaraan=="")
            {
                flagError = true;
                break; 
            }

        }
        if (flagError) {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `Kendaraan tidak boleh kosong`,
            })
            return;
            
        }
        event.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin data sudah benar?',
            text: "Periksa kembali data anda",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            confirmButtonColor: '#3085d6',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    timer: 2500,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: 'success',
                    title: 'Data Disimpan'
                })

                setTimeout(() => {
                    this.submit();
                }, 20); // 2000 milliseconds = 2 seconds
            }else{
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
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
                    title: 'Batal Disimpan'
                })
                event.preventDefault();
            }
        })
    });
});
</script>
@endsection


