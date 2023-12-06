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
#preview_foto_nota {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}

#preview_foto_barang {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}
</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        <div class="card-header">
            <a href="{{route('pembayaran_gaji.create')}}" class="btn btn-primary radiusSendiri btn-responsive float-left">
                <i class="fa fa-plus-circle"> </i> Tambah Data Pembayaran Gaji
            </a> 
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="TabelKlaim" class="table table-bordered table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Tgl. Dibayarkan</th>
                        <th>Periode</th>
                        <th>Total</th>
                        <th>Cara Pembayaran</th>
                        <th></th>
                    </tr>
                    </thead>
                <tbody>
                    @if (isset($dataPembayaranGaji))
                        @foreach ($dataPembayaranGaji as $item)
                        <tr>
                            <td>{{\Carbon\Carbon::parse($item->tanggal_catat)->format('d-M-Y')}}</td>
                            <td>{{ $item->nama_periode}} </td>
                            <td>{{ number_format($item->total) }}</td>
                            <td>{{ number_format($item->kas_bank_id) }}</td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{route('pembayaran_gaji.edit',[$item->id])}}" class="dropdown-item" target='_blank'>
                                            <span class="fas fa-edit mr-3"></span> Edit
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

<script type="text/javascript">
$(document).ready(function () {

    new DataTable('#TabelKlaim', {
        order: [
            [0, 'asc'],
        ],
        // rowGroup: {
        //     dataSrc: [0]
        // },
        // columnDefs: [
        //     {
        //         targets: [0],
        //         visible: false
        //     },
        //     {
        //         "orderable": false,
        //         "targets": [0,1,2,3,4,5,6]
        //     }
        // ],
    }); 
    $('#post_data').submit(function(event) {
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
                    }, 1000); // 2000 milliseconds = 2 seconds
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


