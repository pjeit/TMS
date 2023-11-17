@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('content')
@include('sweetalert::alert')
<meta name="csrf-token" content="{{ csrf_token() }}" />

<style>
   
</style>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <div class="">
                        {{-- <a href="{{ route("invoice.create") }}" class="btn btn-primary btn-responsive radiusSendiri"  id="sewaAdd">
                            <i class="fa fa-plus-circle" aria-hidden="true"> </i> Buat Invoice
                        </a>  --}}
                          {{-- <button type="submit" class="btn btn-primary btn-responsive radiusSendiri" id="sewaAdd">
                             <i class="fa fa-credit-card"></i> Bayar
                        </button> --}}
                    </div>
                </div>
                <div class="card-body">
                    <table id="tabelInvoice" class="table table-bordered" width='100%'>
                        <thead>
                            <tr>
                                <th>Grup</th>
                                <th>Customer</th>
                                <th>No. Invoice</th>
                                <th width='120'>Tgl Invoice</th>
                                <th width='120'>Jatuh Tempo</th>
                                <th>Sisa Tagihan</th>
                                <th>Catatan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($dataInvoice))
                                @foreach($dataInvoice as $item)
                                    <tr>
                                        <td >{{ $item->nama_grup }} </td>
                                        <td >{{ $item->nama_cust }} </td>
                                        <td>{{ $item->no_invoice }}</td>
                                        <td>{{ date("d-M-Y", strtotime($item->tgl_invoice)) }}</td>
                                        <td>{{ date("d-M-Y", strtotime($item->jatuh_tempo)) }}</td>
                                        <td class="float-right">{{ number_format($item->total_sisa) }}
                                        <td>{{ $item->catatan }}
                                           
                                        </td>
                                        <td style="text-align: center;"> 
                                              <div class="btn-group dropleft">
                                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-list"></i>
                                                    </button>
                                                    <div class="dropdown-menu" >
                                                        <a target="_blank"  class="dropdown-item" href="{{route('belum_invoice.print',[$item->id])}}"><span class="fas fa-print" style="width:24px"></span>Cetak</a>
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
    </div>
</div>

{{-- modal loading --}}
<div class="modal" id="modal-loading" data-backdrop="static">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center">
        <div class="cv-spinner">
            <span class="loader"></span>
         </div>
         <div>Harap Tunggu Sistem Sedang Memproses....</div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
 $(document).ready(function () {
     new DataTable('#tabelInvoice', {
        order: [
            [0, 'asc'],
            [1, 'asc']
        ],
        rowGroup: {
            dataSrc: [0, 1]
        },
        columnDefs: [
            {
                targets: [0, 1],
                visible: false
            },
            {
                "orderable": false,
                "targets": [0,1,2,3,4,5,6,7]
            }
       
        ],
    });

});

</script>

@if (session('id_print_invoice'))
<script>
    window.open("/invoice/print/{{ session('id_print_invoice') }}", "_blank");

    // di set null biar ga open new tab terus2an 
    setTimeout(function() {
        sessionStorage.setItem('id_print_invoice', null);
    }, 1000); // Adjust the delay (in milliseconds) as needed
</script>
@endif

@endsection
