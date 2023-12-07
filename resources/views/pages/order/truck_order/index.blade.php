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
    <div class="card radiusSendiri">
        <div class="card-header">
            <div class="">
                <a href="{{route('truck_order.create')}}" class="btn btn-primary btn-responsive radiusSendiri">
                    <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Order PJE
                </a> 
                <a href="{{route('truck_order_rekanan.create')}}" class="btn btn-default btn-outline-dark btn-responsive radiusSendiri ml-3">
                    <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Order Rekanan 
                </a> 
            </div>
        </div>
        <div class="card-body">
            <table id="datatable" class="table table-bordered table-striped" width='100%'>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Tujuan</th>
                        <th>No. Polisi</th>
                        <th>Driver</th>
                        <th>Tgl Berangkat</th>
                        {{-- <th>Marketing</th> --}}
                        <th>No. Sewa</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="hasil">
                    @if (isset($dataSewa))
                        @foreach($dataSewa as $item)
                            <tr>
                                <td>{{ $item->nama_customer }}</td>
                                <td>{{ $item->nama_tujuan }}</td>
                                <td>{{ $item->no_polisi }}</td>
                                <td>{{ $item->nama_lengkap }}</td>
                                <td>{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}</td>
                                {{-- <td>{{ $item->no_sewa }}</td> --}}
                                <td>{{ $item->no_sewa }}</td>
                                <td>{{ $item->status }}</td>
                                <td>                                    
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{route('truck_order.edit',[$item->id_sewa])}}" class="dropdown-item">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                            <a href="{{ route('truck_order.destroy', $item->id_sewa) }}" class="dropdown-item" data-confirm-delete="true">
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
    </div>
</div>
<script>
    $(document).ready(function() {
       
    });
</script>
@endsection
