
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
                    <table id="example1" class="table table-bordered table-striped">
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
                                        <i class="far nav-icon fa fa-truck">+</i> Tambah Chassis
                                    </a>   
                                            <!-- Button trigger modal -->
                                    {{-- <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalHapus">
                                               <i class="fas fa-trash"></i> Hapus
                                    </button>           --}}
                                    
                                </td>
                                                   
                                
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
                                            <form action="{{route('pair_kendaraan.destroy',[$item->id])}}" method="POST" class="btn btn-responsive">
                                                @csrf
                                                @method('DELETE')
                                                <button action="{{route('pair_kendaraan.destroy',[$item->id])}}" class="btn btn-primary">Ya</button>
                                            </form>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tidak</button>
                                       </div>
                                    </div>
                                    </div>
                                </div>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
{{ $dataPair->links('pagination::bootstrap-4') }}

                </div>
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection
