@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('karyawan.index')}}">Karyawan</a></li>
@endsection
@section('content')
<!-- <div class="container-fluid">
        <h2 class="text-center display-4">Cari Nama COA</h2>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <form action="/coae/searchname/" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg" name="searchname" placeholder="Nama COA">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-lg btn-default">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>
<br> -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{route('karyawan.create')}}" class="btn btn-secondary btn-responsive float-left">
                        <i class="fa fa-plus-circle"> </i> Tambah Data
                    </a> 
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="karyawanTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Nama Panggilan</th>
                              <th>Tempat Lahir</th>
                              <th>Alamat</th>
                              <th>Telp</th>
                              <th>Posisi</th>
                              <th>Handle</th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($dataKaryawan as $d)
                             <tr>
                                <td>{{$d->nama_panggilan}}</td>
                                <td>{{$d->tempat_lahir}}</td>  
                                <td>{{$d->alamat_domisili}}</td>  
                                <td>{{$d->telp1}}</td>  
                                <td>{{$d->posisi}}</td>  
                      
                                <td>                                    
                                    <a class="btn btn-default bg-info" href="{{route('karyawan.edit',[$d->id])}}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>   
                                            <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalHapus">
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
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="margin-right: -1.75rem">Tidak</button>

                                            <form action="{{route('karyawan.destroy',[$d->id])}}" method="POST" class="btn btn-responsive">
                                                @csrf
                                                @method('DELETE')
                                                <button action="{{route('karyawan.destroy',[$d->id])}}" class="btn btn-primary">Ya</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>

@endsection

