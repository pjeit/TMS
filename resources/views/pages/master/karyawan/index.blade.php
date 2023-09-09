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
@include('sweetalert::alert')

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
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('karyawan.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle"> </i> Tambah Data
                    </a> 
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="datatable" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Cabang</th>
                                <th>Role</th>
                                <th>Nama Panggilan</th>
                                <th>Telp</th>
                                <th>Alamat</th>
                                <th></th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($dataKaryawan as $d)
                             <tr>
                                 <td>{{$d->cabang}}</td>  
                                 <td>{{$d->posisi}}</td>  
                                 <td>{{$d->nama_panggilan}}</td>
                                 <td>{{$d->telp1}}</td>  
                                 <td>{{$d->alamat_domisili}}</td>  
                      
                                <td>                                    
                                    {{-- <a class="btn btn-default bg-info radiusSendiri" href="{{route('karyawan.edit',[$d->id])}}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>   
                                            <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-danger radiusSendiri" data-toggle="modal" data-target="#modalHapus">
                                               <i class="fas fa-trash"></i> Hapus
                                    </button>           --}}
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{route('karyawan.edit',[$d->id])}}" class="dropdown-item">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                            
                                            <a href="{{ route('karyawan.destroy', $d->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Delete
                                            </a>
                                            
                                        </div>
                                    </div>
                                </td>
                                                   
                                
                               
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
<script type="text/javascript">
    $(function () {
     
    });
</script>
@endsection


