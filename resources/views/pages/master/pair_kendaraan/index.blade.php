
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('supplier.index')}}">Pair Kendaraan</a></li>
@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                {{-- <div class="card-header">
                    <a href="{{route('pair_kendaraan.create')}}" class="btn btn-secondary btn-responsive float-left">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div> --}}
                <div class="card-body">
                    <table id="datatable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                              <th>No Polisi Kendaraan</th>
                              <th>Chassis Kendaraan</th>
                              <th>Kategori Kendaraan</th>
                              <th>Letak Kendaraan</th>
                              <th>Aksi</th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($dataPair as $item)
                             <tr>
                                <td>{{ $item->no_polisi }}</td>
                                <td>{{ $item->chassis_model }}</td>  
                                <td>{{ $item->kategoriKendaraan }}</td>  
                                <td>{{ $item->namaKota }}</td>  

                                <td>                                    
                                    <a class="btn btn-default bg-info radiusSendiri" href="{{route('pair_kendaraan.edit',[$item->id])}}">
                                        <i class="far nav-icon fa fa-truck">+</i> Pairing Chassis
                                    </a>   
                                </td>
                              
                            </tr>
                            @endforeach
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
