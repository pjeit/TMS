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
            <table id="tabelSewa" class="table table-bordered table-striped responsive">
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
                            @php
                                $cek_operasional = false;
                                foreach ($sewa_operasional as  $value) {
                                    if( $value->id_sewa== $item->id_sewa )
                                    {
                                        $cek_operasional = true;
                                        break;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $item->nama_cust}}</td>
                                <td>
                                    {{ $item->nama_tujuan }}
                                    @if ($cek_operasional)
                                        <span class="badge badge-danger">ada operasional !</span>
                                    @endif

                                </td>
                                <td>
                                    {{ $item->no_polisi}}
                                    @if ($item->jenis_tujuan=="FTL")
                                        <span class="badge badge-primary">{{ $item->jenis_tujuan }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ $item->jenis_tujuan }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}
                                    @if ($item->jenis_order=="OUTBOUND")
                                        <span class="badge badge-dark">{{ $item->jenis_order }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $item->jenis_order }}</span>
                                    @endif
                                </td>
                                @if ($item->id_supplier)
                                <td>DRIVER REKANAN  ({{ $item->namaSupplier }})</td>
                                @else
                                <td>{{ $item->supir }} (0{{ trim($item->telpSupir) }})</td>
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
                                            @if ($cek_operasional)
                                            <a href="{{route('dalam_perjalanan.refund_operasional',[$item->id_sewa])}}" class="dropdown-item">
                                                <span class="nav-icon fas fa-times mr-3"></span> Refund Operasional
                                            </a>
                                            @endif
                                            @if ($item->id_supplier)
                                                @can('EDIT_DALAM_PERJALANAN')
                                                    <a href="{{route('truck_order_rekanan.edit',[$item->id_sewa])}}" class="dropdown-item">
                                                        <span class="nav-icon fas fa-edit mr-3"></span> Edit Sewa Rekanan
                                                    </a>
                                                @endcan
                                                @can('CANCEL_DALAM_PERJALANAN')
                                                    <a href="{{route('dalam_perjalanan.batal_muat',[$item->id_sewa])}}" class="dropdown-item">
                                                        <span class="nav-icon fas fa-undo mr-3"></span> Batal muat Rekanan
                                                    </a>
                                                    <a href="{{route('dalam_perjalanan.cancel',[$item->id_sewa])}}" class="dropdown-item">
                                                        <span class="nav-icon fas fa-times mr-3"></span> Cancel Rekanan
                                                    </a>
                                                @endcan
                                            @else
                                                @can('EDIT_DALAM_PERJALANAN')
                                                    <a href="{{route('truck_order.edit',[$item->id_sewa])}}" class="dropdown-item">
                                                        <span class="nav-icon fas fa-edit mr-3"></span> Edit Sewa PJE
                                                    </a>
                                                @endcan
                                                {{-- @if ($item->jenis_order == 'OUTBOUND') --}}
                                                    @can('CANCEL_DALAM_PERJALANAN')
                                                        <a href="{{route('dalam_perjalanan.batal_muat',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-undo mr-3"></span> Batal muat
                                                        </a>
                                                        <a href="{{route('dalam_perjalanan.cancel',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-times mr-3"></span> Cancel
                                                        </a>
                                                    @endcan
                                                {{-- @else
                                                    @can('CANCEL_DALAM_PERJALANAN')
                                                        <a href="{{route('dalam_perjalanan.cancel',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-times mr-3"></span> Cancel
                                                        </a>
                                                    @endcan
                                                @endif --}}
                                                    <a href="{{route('dalam_perjalanan.ubah_supir',[$item->id_sewa])}}" class="dropdown-item">
                                                        <span class="nav-icon fas fa-user mr-3"></span> Ubah Supir
                                                    </a>
                                                @if ($item->jenis_tujuan == 'FTL')
                                                    {{-- @can('CANCEL_DALAM_PERJALANAN')
                                                        <a href="{{route('dalam_perjalanan.cancel_uang_jalan',[$item->id_sewa])}}" class="dropdown-item">
                                                            <span class="nav-icon fas fa-dollar-sign mr-3"></span> Cancel Uang Jalan
                                                        </a>
                                                    @endcan --}}
                                                @endif
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

<script>
$(document).ready(function () {
    new DataTable('#tabelSewa', {
        // "ordering": true,
        responsive: true,
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