
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
  
</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        <div class="card-header">

        </div>
        <div class="card-body">
            <table id="revTagihan" class="table table-bordered table-hover yajra-datatable" width="100%">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>No. Nota</th>
                        <th>Tgl Nota</th>
                        <th>Jatuh Tempo</th>
                        <th>Total Bayar</th>
                        <th width="30"></th>
                    </tr>
                </thead>
                <tbody id="hasil">
                    
                </tbody>
            </table>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('revisi_tagihan_rekanan.load_data') }}",
            
            // order: [
            //     [0, 'asc'], // 0 = grup
            // ],
            // rowGroup: {
            //     dataSrc: [0] // di order grup dulu, baru customer
            // },
            columns: [
                // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'id_supplier', name: 'id_supplier'},
                {data: 'no_nota', name: 'no_nota'},
                {data: 'tgl_nota', name: 'tgl_nota'},
                {data: 'jatuh_tempo', name: 'jatuh_tempo'},
                {data: 'total_bayar', name: 'total_bayar'},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false
                },
            ]
        });
    });
</script>
@endsection