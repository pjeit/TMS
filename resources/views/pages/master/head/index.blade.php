
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('head.index')}}">Head</a></li>
@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{route('head.create')}}" class="btn btn-secondary btn-responsive float-left">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>No Polisi</th>
                              <th>No. Mesin & Rangka</th>
                              <th>Merk & Model</th>
                              <th>Tahun & Warna</th>
                              <th>Driver (tunggu menu karyawan)</th>
                              <th>Aksi</th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($data as $item)
                             <tr>
                                <td>{{$item->no_polisi}}</td>
                                <!-- ganti kolom no_kendaraan menjadi no_mesin di DB tabel kendaraan -->
                                <td>{{$item->no_mesin}} - {{$item->no_rangka}} </td>  
                                <td>{{$item->merk_model}}</td>  
                                <td>{{$item->tahun_pembuatan}} - {{$item->warna}} </td>  
                                <td>{{ $item->driver_id }}</td>
                                <td>                                    
                                    <a class="btn btn-default bg-info" href="{{route('head.edit',[$item->id])}}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>   
                                            <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalHapus_{{$item->id}}">
                                               <i class="fas fa-trash"></i> Hapus
                                    </button>          
                                    
                                </td>
                                                   
                                 <!-- Modal -->
                                <div class="modal fade" id="modalHapus_{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Hapus Data</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                        <p>Apakah anda yakin ingin menghapus data secara permanen?</p>
                                        </div>
                                    <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="margin-right: -1.75rem">Tidak</button>

                                            <form action="{{route('head.destroy',[$item->id])}}" method="POST" class="btn btn-responsive">
                                                @csrf
                                                @method('DELETE')
                                                <button action="{{route('head.destroy',[$item->id])}}" id="del_{{$item->id}}" class="btn btn-primary">Ya</button>
                                            </form>
                                    </div>
                                    </div>
                                    </div>
                                </div>

                               
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
