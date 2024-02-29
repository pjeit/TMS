
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')

@endsection

@section('content')
@include('sweetalert::alert')

<style>
  
</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        <div class="card-header">
            <input type="hidden" id="token" value="{{ csrf_token() }}">
            {{-- <button class="deleteProduct" data-id="{{ $comment->id }}" data-token="{{  }}" >Delete Task</button> --}}
        </div>
        <div class="card-body">
            <table id="table_invoice_k" class="table table-bordered table-hover yajra-datatable" width="100%">
                <thead>
                    <tr>
                        <th style="width: 250px;">Customer</th>
                        <th>No. Invoice</th>
                        <th>Tgl Pembayaran</th>
                        <th>Total Diterima</th>
                        <th width="30"></th>
                    </tr>
                </thead>
                <tbody id="hasil">
                    @if(isset($data))
                        @php
                            $no_invoice ='';
                        @endphp
                        @foreach ($data as $item)
                           <tr>
                                <td>{{$item->billing_to_pembayaran->nama}}</td>
                                <td>
                                    @foreach ($item->detail_invoice as $no_invoices)
                                        <small class="font-weight-bold">#{{$no_invoices->no_invoice_k}}</small><br>
                                    @endforeach
                                </td>
                                <td>{{date('d-M-Y',strtotime($item->tgl_pembayaran))}}</td>
                                <td>Rp. {{number_format($item->total_diterima)}}</td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            {{-- <a href="{{route('revisi_invoice_karantina.edit',[$item->id])}}" class="dropdown-item ">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a> --}}
                                            <a href="{{ route('revisi_invoice_karantina.destroy', [$item->id]) }}" class="dropdown-item" data-confirm-delete="true">
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
    </div>
</div>

<script>
    $(document).ready(function() {
        // $('#table_invoice_k').DataTable({
        //         order: [
        //             [0, 'asc'], // 0 = grup
        //         ],
        //         rowGroup: {
        //             dataSrc: [0] // di order grup dulu, baru customer
        //         },
        //         columns: [
        //             {
        //                 data: 'action', 
        //                 name: 'action', 
        //                 orderable: false, 
        //                 searchable: true
        //             },
        //         ]
        //     });
        // var table = $('#table_invoice_k').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: "{{ route('revisi_invoice_trucking.load_data') }}",
            
        //     // order: [
        //     //     [0, 'asc'], // 0 = grup
        //     // ],
        //     // rowGroup: {
        //     //     dataSrc: [0] // di order grup dulu, baru customer
        //     // },
        //     columns: [
        //         // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
        //         {data: 'billing_to', name: 'billing_to'},
        //         // {data: 'grup', name: 'grup'},
        //         {data: 'customer', name: 'customer'},
        //         {data: 'no_invoice', name: 'no_invoice'},
        //         {data: 'tgl_pembayaran', name: 'tgl_pembayaran'},
        //         {data: 'total_diterima', name: 'total_diterima'},
        //         {
        //             data: 'action', 
        //             name: 'action', 
        //             orderable: false, 
        //             searchable: false
        //         },
        //     ]
        // });

        // $(document).on('click', '.delete', function (event){
        //     let id = this.value;
        //     var baseUrl = "{{ asset('') }}";
        //     var url = baseUrl+`{{ url('revisi_invoice_trucking/delete/${id}') }}`;
        //     var token = $('#token').val();

        //     Swal.fire({
        //         title: 'Apakah Anda yakin menghapus data ini?',
        //         text: "Periksa kembali data anda",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         cancelButtonColor: '#d33',
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonText: 'Batal',
        //         confirmButtonText: 'Ya',
        //         reverseButtons: true
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: url,
        //                 type: 'GET',
        //                 dataType: "JSON",
        //                 data: {
        //                     "id": id,
        //                     "_method": 'GET',
        //                     "_token": token,
        //                 },
        //                 success: function(resp){
        //                     if(resp.status == 'success'){
        //                         window.location.reload();
        //                     }
        //                 }
        //             });
        //         }else{
        //             const Toast = Swal.mixin({
        //                 toast: true,
        //                 position: 'top',
        //                 timer: 2500,
        //                 showConfirmButton: false,
        //                 timerProgressBar: true,
        //                 didOpen: (toast) => {
        //                     toast.addEventListener('mouseenter', Swal.stopTimer)
        //                     toast.addEventListener('mouseleave', Swal.resumeTimer)
        //                 }
        //             })

        //             Toast.fire({
        //                 icon: 'warning',
        //                 title: 'Batal Disimpan'
        //             })
        //             event.preventDefault();
        //         }
        //     })
        // });
    });
</script>
@endsection