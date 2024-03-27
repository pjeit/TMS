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
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">

                    {{-- <button class="btn btn-primary btn-responsive float-left radiusSendiri bukakModalCreate">
                        <i class="fa fa-plus-circle"> </i> Tambah Data

                    </button> --}}
                    {{-- <a href="{{route('karyawan.create')}}" >
                    </a>  --}}
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="TabelKlaim" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Supir</th>
                                <th>Jenis Klaim</th>
                                <th>Tanggal Klaim</th>
                                <th>Jumlah Klaim</th>
                                <th>Jumlah Dicairkan</th>
                                <th>Status Klaim</th>
                                <th>Keterangan</th>
                                <th></th>
                            </tr>
                          </thead>
                        <tbody>
                         
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
    var table = $('#TabelKlaim').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        "responsive": true,
        // lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Rows per page options
        pageLength: 10, // Default number of rows per page
        // ajax: "{{ route('transaksi_lain.index_server') }}",
        ajax: {
            // url: "{{ route('klaim_supir_revisi.load_data_revisi_server') }}",
            url: "{{ route('klaim_operasional_revisi.load_data_revisi_server') }}",

            type: "GET",
            data: function (d) {
                console.log(d);
            },
            // success: function(response) {
            //     console.log(response); // Log campur_data
            // }
        },
        columns: [
            {data: 'nama_supir_server', name: 'Supir'},
            {data: 'jenis_klaim_server', name: 'Jenis_Klaim'},
            {data: 'tanggal_klaim_server', name: 'Tanggal_Klaim'},
            {data: 'jumlah_klaim_server', name: 'Jumlah_Klaim'},
            {data: 'jumlah_dicairkan_server', name: 'Jumlah_Dicairkan'},
            {data: 'status_klaim_server', name: 'Status_Klaim'},
            {data: 'keterangan_server', name: 'Keterangan'},
            {
                data: 'action_server', 
                name: 'action', 
                orderable: false, 
                searchable: false
            },
        ],
        order: [
                    [0, 'asc'],
                ],
            rowGroup: {
                dataSrc: ['nama_supir_server']//grouping per supir pake nama datanya dr controller, kalo bukan serverside nembak index, misal [0]
            },
            columnDefs: [
                {
                    targets: [0],
                    visible: false
                },
                {
                    "orderable": false,
                    "targets": [0,1,2,3,4,5,6,7]
                }
        
            ],
    });
//  var table = $('#TabelKlaim').DataTable({
//             processing: true,
//             serverSide: true,
//             ajax: "{{ route('klaim_supir_revisi.load_data_revisi_server') }}",
           
//             columns: [
//                 {data: 'Supir', name: 'Supir'},
//                 {data: 'Jenis_Klaim', name: 'Jenis_Klaim'},
//                 {data: 'Tanggal_Klaim', name: 'Tanggal_Klaim'},
//                 {data: 'Jumlah_Klaim', name: 'Jumlah_Klaim'},
//                 {data: 'Jumlah_Dicairkan', name: 'Jumlah_Dicairkan'},
//                 {data: 'Status_Klaim', name: 'Status_Klaim'},
//                 {data: 'Keterangan', name: 'Keterangan'},
//                 {
//                     data: 'action', 
//                     name: 'action', 
//                     orderable: false, 
//                     searchable: false
//                 },
//             ],
//              order: [
//                     [0, 'asc'],
//                 ],
//             rowGroup: {
//                 dataSrc: ['Supir']//grouping per supir pake nama datanya, kalo bukan serverside nembak index
//             },
//             columnDefs: [
//                 {
//                     targets: [0],
//                     visible: false
//                 },
//                 {
//                     "orderable": false,
//                     "targets": [0,1,2,3,4,5,6,7]
//                 }
        
//             ],
//         });
    
});
</script>
@endsection


