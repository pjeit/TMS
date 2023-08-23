
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('supplier.index')}}">Supplier</a></li>
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
                    <a href="{{route('supplier.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <div class="form-group">
                            <label>Filter Supplier</label>
                            <form action="/supplier/jenisFilter/" method="get">
                            <div class="input-group col-md-4">
                                <select class="form-control selectpicker" name="jenisFilter" id="jenisFilter" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Agama">
                                        <option value="">--Pilih Jenis Supplier--</option>
                                    
                                    @foreach($dataJenisFilter as $dat)
                                        <option value="{{$dat->id}}">{{$dat->nama}}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-lg btn-default">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Nama</th>
                              <th>Alamat</th>
                              <th>Telp</th>
                              <th>Catatan</th>
                              <th>Jenis Supplier</th>
                              <th>Lokasi Supplier</th>
                              <th>Aksi</th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($data as $item)
                             <tr>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->alamat }}</td>  
                                <td>{{ $item->telp }}</td>  
                                <td>{{ $item->catatan }}</td>
                                <td>{{ $item->jenis }}</td>
                                <td>{{ $item->kota }}</td>
                                <td>                                    
                                    <a class="btn btn-default bg-info radiusSendiri" href="{{route('supplier.edit',[$item->id])}}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>   
                                            <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-danger radiusSendiri" data-toggle="modal" data-target="#modalHapus">
                                               <i class="fas fa-trash"></i> Hapus
                                    </button>          
                                    
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
                                            <form action="{{route('supplier.destroy',[$item->id])}}" method="POST" class="btn btn-responsive">
                                                @csrf
                                                @method('DELETE')
                                                <button action="{{route('supplier.destroy',[$item->id])}}" class="btn btn-primary">Ya</button>
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
                </div>
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection
