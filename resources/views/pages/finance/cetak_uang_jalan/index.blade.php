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
            {{-- <a href="{{route('karyawan.create')}}" >
            </a>  --}}
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="TabelKlaim" class="table table-bordered table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Tgl. Pencairan</th>
                        <th>No. Sewa</th>
                        <th>Tujuan</th>
                        <th>Kendaraan & Driver</th>
                        <th>Uang Diterima</th>
                        <th></th>
                    </tr>
                    </thead>
                <tbody>
                    @if (isset($dataKlaimSupir))
                        @foreach ($dataKlaimSupir as $item)
                        <tr>
                            <td>{{ $item->nama_supir}} ({{ $item->telp}})</td>
                            <td>{{ $item->jenis_klaim}}</td>
                            <td>{{ $item->tanggal_klaim }}</td>
                            <td>Rp. {{number_format($item->total_klaim,2)  }}</td>
                            <td>{{ $item->status_klaim }}</td>
                            <td>{{ $item->keterangan_klaim }}</td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{route('klaim_supir.edit',[$item->id_klaim])}}" class="dropdown-item ">
                                            <span class="fas fa-edit mr-3"></span> Edit
                                        </a>
                                        <a href="{{route('pencairan_klaim_supir.edit',[$item->id_klaim])}}" class="dropdown-item ">
                                            <span class="nav-icon fas fa-dollar-sign mr-3"></span> Pencairan
                                        </a>
                                        <a href="{{ route('klaim_supir.destroy', [$item->id_klaim]) }}" class="dropdown-item" data-confirm-delete="true">
                                            <span class="fas fa-trash mr-3"></span> Delete
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
        rowGroup: {
            dataSrc: [0]
        },
        columnDefs: [
            {
                targets: [0],
                visible: false
            },
            {
                "orderable": false,
                "targets": [0,1,2,3,4,5,6]
            }
        ],
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


