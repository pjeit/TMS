
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
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('head.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Cabang</th>
                              <th>No Polisi</th>
                              <th>No. Mesin & Rangka</th>
                              <th>Merk & Model</th>
                              <th>Tahun & Warna</th>
                              <th>Driver</th>
                              <th>Aksi</th>
                            </tr>
                          </thead>
                        <tbody>
                            @if($data != null)
                                @foreach($data as $item)
                                <tr>
                                    <td>{{$item->cabangPje}}</td>

                                    <td>{{$item->no_polisi}}</td>
                                    <!-- ganti kolom no_kendaraan menjadi no_mesin di DB tabel kendaraan -->
                                    <td>{{$item->no_mesin}} - {{$item->no_rangka}} </td>  
                                    <td>{{$item->merk_model}}</td>  
                                    <td>{{$item->tahun_pembuatan}} - {{$item->warna}} </td>  
                                    <td>{{ $item->nama_lengkap }}</td>
                                    <td>                                    
                                        <a class="btn btn-default bg-info radiusSendiri" href="{{route('head.edit',[$item->id])}}">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>   
                                        <button type="button" class="btn btn-danger radiusSendiri" data-toggle="modal" data-target="#modalHapus">
                                                <i class="fas fa-trash"></i> Hapus
                                        </button>          
                                        
                                    </td>
                                                    
                                    
                                
                                </tr>
                                                            <!-- Modal -->
                            <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                            <button action="{{route('head.destroy',[$item->id])}}" class="btn btn-primary">Ya</button>
                                        </form>
                                </div>
                                </div>
                                </div>
                            </div>

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
