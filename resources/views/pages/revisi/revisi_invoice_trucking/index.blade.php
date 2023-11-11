
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
            <input type="hidden" id="token" value="{{ csrf_token() }}">
            {{-- <button class="deleteProduct" data-id="{{ $comment->id }}" data-token="{{  }}" >Delete Task</button> --}}
        </div>
        <div class="card-body">
            <table id="revTagihan" class="table table-bordered table-hover yajra-datatable" width="100%">
                <thead>
                    <tr>
                        <th>Billing To</th>
                        <th>Grup</th>
                        <th style="width: 250px;">Customer</th>
                        <th>No. Invoice</th>
                        <th>Tgl Pembayaran</th>
                        <th>Total Diterima</th>
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
            ajax: "{{ route('revisi_invoice_trucking.load_data') }}",
            
            // order: [
            //     [0, 'asc'], // 0 = grup
            // ],
            // rowGroup: {
            //     dataSrc: [0] // di order grup dulu, baru customer
            // },
            columns: [
                // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'billing_to', name: 'billing_to'},
                {data: 'grup', name: 'grup'},
                {data: 'customer', name: 'customer'},
                {data: 'no_invoice', name: 'no_invoice'},
                {data: 'tgl_pembayaran', name: 'tgl_pembayaran'},
                {data: 'total_diterima', name: 'total_diterima'},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false
                },
            ]
        });

        $(document).on('click', '.delete', function (event){
            let id = this.value;
            console.log('id', id);
            url = `{{ url('revisi_invoice_trucking/delete/${id}') }}`;
            var token = $('#token').val();
            console.log('url', url);

            Swal.fire({
                title: 'Apakah Anda yakin menghapus data ini?',
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
                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: "JSON",
                        data: {
                            "id": id,
                            "_method": 'GET',
                            "_token": token,
                        },
                        success: function(resp){
                            if(resp.status == 'success'){
                                window.location.reload();
                            }
                        }
                    });
                }else{
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
                        title: 'Batal Disimpan'
                    })
                    event.preventDefault();
                }
            })
        });
    });
</script>
@endsection