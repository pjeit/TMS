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

                    
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="TabelLembur" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Tanggal Lembur</th>
                                <th>Mekanik</th>
                                <th>Jam Mulai Lembur</th>
                                <th>Jam Selesai Lembur</th>
                                <th>Nominal Lembur</th>
                                <th>Status</th>
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

    $('#tanggal_lembur').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
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
                
    var table = $('#TabelLembur').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        "responsive": true,
        // lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Rows per page options
        pageLength: 10, // Default number of rows per page
        // ajax: "{{ route('transaksi_lain.index_server') }}",
        ajax: {
            url: "{{ route('revisi_lembur_mekanik.load_data_server') }}",
            type: "GET",
            data: function (d) {
                console.log(d);
            },
            // success: function(response) {
            //     console.log(response); // Log campur_data
            // }
        },
        columns: [
            {data: 'nama_mekanik_server', name: 'mekanik'},
            {data: 'tanggal_lembur_server', name: 'tanggal_lembur'},
            {data: 'jam_mulai_server', name: 'jam_mulai'},
            {data: 'jam_akhir_server', name: 'jam_akhir'},
            {data: 'nominal_lembur_server', name: 'nominal_lembur'},
            {data: 'status_lembur_server', name: 'status_lemmbur'},
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
                dataSrc: ['nama_mekanik_server']//grouping per supir pake nama datanya dr controller, kalo bukan serverside nembak index, misal [0]
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
    
    
});
</script>
@endsection


