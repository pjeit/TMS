
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')

@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        {{-- <div class="row"> --}}
            <div class="card-header ">
             
            </div>
            
            <div class="card-body">
                    <table id="" class="table table-bordered table-striped" width=''>
                        <thead>
                            <tr>
                                <th>No. Polisi</th>
                                <th>No. Sewa</th>
                                <th>Tanggal Berangkat</th>
                                <th>Tujuan</th>
                                <th>Driver</th>
                                <th style="width:30px"></th>
                            </tr>
                        </thead>
                        <tbody id="hasil">
                        @if (isset($dataSewa))
                            @php
                                $simpenIdCust = null; 
                            @endphp
                            @foreach($dataSewa as $item)
                                
                                @if($item->id_cust != $simpenIdCust)
                                    @php
                                        $simpenIdCust = $item->id_cust; 
                                    @endphp
                                    <tr>
                                        <th colspan="6">{{ $item->nama_cust }}</th>
                                    </tr>
                                @endif
                                <tr>
                                
                                    <td>{{ $item->no_polisi}}</td>
                                    <td>{{ $item->no_sewa }}</td>
                                    <td>{{ $item->tanggal_berangkat }}</td>
                                    <td>{{ $item->nama_tujuan }}</td>
                                    @if ($item->id_supplier)
                                    <td>DRIVER REKANAN  ({{ $item->nama_cust }})</td>

                                    @else
                                    <td>{{ $item->supir }} ({{ $item->telpSupir }})</td>

                                        
                                    @endif
                                    <td>
                                         <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu" >
                                                <a href="{{route('perjalanan_kembali.edit',[$item->id_sewa])}}" class="dropdown-item">
                                                    <span class="fas fa-truck mr-3"></span> Input Kendaraan Kembali
                                                </a>
                                               
                                               
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
        {{-- </div> --}}
    </div>
</div>
<script>


</script>
@endsection