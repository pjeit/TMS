
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
        <div class="card-header ">
            
        </div>
        
        <div class="card-body">
                <table id="tabelSewa" class="table table-bordered table-striped" width=''>
                    <thead>
                        <tr>
                            <th>Custoemer</th>
                            <th>Tujuan</th>
                            <th>No. Polisi</th>
                            <th>Tanggal Berangkat</th>
                            <th>Driver</th>
                            <th style="width:30px"></th>
                        </tr>
                    </thead>
                    <tbody id="">
                        @if (isset($dataSewa))
                            @php
                                $simpenIdCust = null; 
                            @endphp
                            @foreach($dataSewa as $item)
                                
                                {{-- @if($item->id_cust != $simpenIdCust)
                                    @php
                                        $simpenIdCust = $item->id_cust; 
                                    @endphp
                                    <tr>
                                        <th colspan="6">{{ $item->nama_cust }}</th>
                                    </tr>
                                @endif --}}
                                <tr>
                                    <td>{{ $item->nama_cust}}</td>
                                    <td>
                                        {{ $item->nama_tujuan }}

                                       
                                    </td>
                                    <td>
                                        {{ $item->no_polisi}}
                                         @if ($item->jenis_tujuan=="FTL")
                                            <span class="badge badge-primary">{{ $item->jenis_tujuan }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ $item->jenis_tujuan }}</span>
                                        @endif
                                    
                                    </td>
                                    <td>{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}</td>
                                    @if ($item->id_supplier)
                                    <td>DRIVER REKANAN  ({{ $item->namaSupplier }})</td>
                                    @else
                                    <td>{{ $item->supir }} ({{ $item->telpSupir }})</td>
                                    @endif
                                    <td style="text-align: center">
                                            <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu" >
                                                <a href="{{route('dalam_perjalanan.edit',[$item->id_sewa])}}" class="dropdown-item">
                                                    <span class="fas fa-truck mr-3"></span> Input Kendaraan Kembali
                                                </a>
                                               
                                                @if ($item->id_supplier)
                                                    <a href="{{route('truck_order_rekanan.edit',[$item->id_sewa])}}" class="dropdown-item">
                                                        <span class="nav-icon fas fa-edit mr-3"></span> Edit Sewa Rekanan
                                                    </a>
                                                    {{-- @if ($item->jenis_tujuan == 'FTL') --}}
                                                        <a href="{{route('dalam_perjalanan.batal_muat',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-undo mr-3"></span> Batal muat Rekanan
                                                        </a>
                                                        <a href="{{route('dalam_perjalanan.cancel',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-times mr-3"></span> Cancel Rekanan
                                                        </a>
                                                    {{-- @endif --}}
                                                @else
                                                <a href="{{route('dalam_perjalanan.batal_muat',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-undo mr-3"></span> Batal muat
                                                        </a>
                                                        <a href="{{route('dalam_perjalanan.cancel',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-times mr-3"></span> Cancel
                                                        </a>
                                                    @if ($item->jenis_tujuan == 'FTL')
                                                        <a href="{{route('truck_order.edit',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-edit mr-3"></span> Edit Sewa PJE
                                                        </a>
                                                        <a href="{{route('dalam_perjalanan.ubah_supir',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-user mr-3"></span> Ubah Supir
                                                        </a>
                                                        
                                                        <a href="{{route('dalam_perjalanan.cancel_uang_jalan',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-dollar-sign mr-3"></span> Cancel Uang Jalan
                                                        </a>
                                                    @else
                                                        <a href="{{route('truck_order.edit',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-edit mr-3"></span> Edit Sewa PJE
                                                        </a>
                                                        {{-- <a href="{{route('dalam_perjalanan.cancel',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="fas fa-times mr-3"></span> Cancel
                                                        </a> --}}
                                                        
                                                    @endif
                                                    
                                                @endif
                                                
                                            </div>
                                        </div>
                                        {{-- <form method="POST" action="{{ route('pencairan_uang_jalan_ftl.form') }}">
                                            @csrf
                                            <input type="hidden" name="id_sewa" value="{{ $item->id_sewa }}">
                                            <button type="submit" class="btn btn-success radiusSendiri">
                                                <i class="fas fa-credit-card"></i> Pencairan
                                            </button>
                                        </form> --}}
                                        {{-- <a class="btn btn-success radiusSendiri" href="{{route('pencairan_uang_jalan_ftl.edit',[$item->id_sewa])}}">
                                                <i class="fas fa-credit-card"></i> Pencairan
                                        </a>   --}}
                                        {{-- <a class="dropdown-item" href="{{ route('pencairan_uang_jalan_ftl.edit', [$item->id_sewa]) }}"><span class="fas fa-edit" style="width:24px"></span>Pencairan</a> --}}
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
$(document).ready(function () {
    new DataTable('#tabelSewa', {
        "ordering": false,
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
            // {
            //     "orderable": false,
            //     "targets": [0,1,2,3,4,5,6]
            // }
        ],
    }); 
});

</script>
@endsection