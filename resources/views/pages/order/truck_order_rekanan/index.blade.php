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
                    <a href="{{route('truck_order_rekanan.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Order
                    </a> 
                </div>
                <div class="card-body">
                    <table id="datatable" class="table table-bordered table-striped" width='100%'>
                        <thead>
                            <tr>
                                <th>No. Polisi Kendaraan</th>
                                <th>No. Sewa</th>
                                <th>Tgl Berangkat</th>
                                <th>Tujuan</th>
                                <!-- <th>Driver</th> -->
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($dataSewa))
                                @foreach($dataSewa as $item)
                                    <tr>
                                        <td>{{ $item->no_polisi }}</td>
                                        <td>{{ $item->no_sewa }}</td>
                                        <td>{{date("d-M-Y", strtotime($item->tanggal_berangkat))}}</td>
                                        <td>{{ $item->nama_tujuan }}</td>
                                        <!-- <td>{{ $item->nama_lengkap }}</td> -->
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
    </div>
</div>
<script>
 
</script>
@endsection
