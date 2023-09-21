
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
    tr.odd td:first-child,tr.even td:first-child {
        padding-left: 4em;
    }
</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        {{-- <div class="row"> --}}
            <div class="card-header ">
             
            </div>

            <div class="card-body">
                <table id="rowGroup" class="table table-bordered " width=''>
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Customer</th>
                            <th>Tujuan</th>
                            <th>No. Polisi</th>
                            <th>Driver</th>
                            <th>No. Sewa</th>
                            <th>Tanggal Berangkat</th>
                            <th style="width:30px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($data))
                            @foreach($data as $item)
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col-8">
                                                {{ $item->getCustomer->getGrup->nama_grup}}
                                            </div>
                                            <div class="col-4">
                                                <a class="btn btn-success radiusSendiri float-right" href="{{route('pencairan_operasional.pencairan',[$item->getCustomer->grup_id])}}">
                                                    <i class="fas fa-credit-card mr-2"></i> <b>Pencairan</b>
                                                </a>
                                            </div>
                                        </div> 
                                    </td>
                                    <td><li>{{ $item->getCustomer->nama}}</li></td>
                                    <td>{{ $item->nama_tujuan }}</td>
                                    <td>{{ $item->no_polisi}}</td>
                                    <td>{{ $item->supir }} ({{ $item->telpSupir }})</td>
                                    <td>{{ $item->no_sewa }}</td>
                                    <td>{{date("d-M-Y", strtotime($item->tanggal_berangkat))}}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        {{-- </div> --}}
    </div>
</div>

<script>
$( document ).ready(function() {
    new DataTable('#rowGroup', {
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
            }
        ]
    });
});
</script>

@endsection