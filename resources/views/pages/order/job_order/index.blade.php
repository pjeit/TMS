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
    /* table .dropdown-menu {
        position: fixed !important;
        top: 45% !important;
        left: 90% !important;
        transform: translate(-90%, -45%) !important;
    } */
</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        <div class="card-header">
            {{-- jika user yg login punya akses create JO, maka tombol akan muncul --}}
            {{-- jika user tidak punya akses create JO, maka tombol create akan di hide --}}
            {{-- @if (auth()->user()->can('create JO'))  --}}
                {{-- <a href="{{route('job_order.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                    <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah JO
                </a> 
            @endif --}}
            {{-- atau bisa pakai cara ini juga --}}
            {{-- @can('create JO')
                <a href="{{route('job_order.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                    <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah JO
                </a> 
            @endcan --}}
            <a href="{{route('job_order.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah JO
            </a> 
        </div>
        <div class="card-body">
            <table id="dt" class="table table-bordered table-striped" width='100%'>
                <thead>
                    <tr>
                        <th>Kode JO</th>
                        <th>No BL</th>
                        <th>Pengirim (Customer)</th>
                        <th>Pelayaran (Supplier)</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($dataJO))
                        @foreach($dataJO as $item)
                        <tr>
                            <td>{{ $item->no_jo }}</td>
                            <td>{{ $item->no_bl }}</td>
                            <td>{{ $item->kode }} - {{ $item->nama_cust }}</td>
                            <td>{{ $item->nama_supp }}</td>
                            <td>{{ $item->status}}</td>
                            <td >                                    
                                <!-- Default dropleft button -->
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        <a href="{{route('job_order.edit',[$item->id])}}" class="dropdown-item">
                                            <span class="fas fa-edit mr-3"></span> Edit
                                        </a>
                                        <a href="{{route('cetak_job_order.print',[$item->id])}}" method="get" rel="noopener" target="_blank"  class="dropdown-item">
                                            <span class="fas fa-print mr-3"></span> Cetak JO
                                        </a>
                                        @php
                                            $kondisi = $item->Jumlah_sblm_dooring==0&&$item->idJaminan==null
                                        @endphp
                                        @if(!$kondisi)
                                            <a href="{{route('job_order.print',[$item->id])}}" method="get" rel="noopener" target="_blank"  class="dropdown-item">
                                                <span class="fas fa-print mr-3"></span> Cetak Nota
                                            </a>
                                        @endif
                                        @if ($item->status == 'MENUNGGU PEMBAYARAN')
                                            <a href="{{ route('job_order.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Delete
                                            </a>
                                        @endif
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
@if (session('id_print_jo'))
<script>
    var baseUrl = "{{ asset('') }}";
    window.open(`${baseUrl}job_order/printJob/{{ session('id_print_jo') }}`, "_blank");
    // di set null biar ga open new tab terus2an 
    setTimeout(function() {
        sessionStorage.setItem('id_print_jo', null);
    }, 1000); // Adjust the delay (in milliseconds) as needed
</script>
@endif

<script>
    var sessionMessage = "{{ session()->has('message') ? session('message') : '' }}";
    if (sessionMessage !== '') {
        toastr.success(sessionMessage);
    }

    $( document ).ready(function() {
        
    });

</script>
@endsection
