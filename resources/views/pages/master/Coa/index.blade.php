@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif

@section('judul')
COA
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
                    <a href="{{route('coa.create')}}" class="btn btn-primary btn-responsive float-right">Tambah COA
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                        </svg>
                    </a> 
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>jenis</th>
                              <th>No. Akun</th>
                              <th>Tipe</th>
                              <th>catatan</th>
                              <th>Handle</th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($dataCOA as $d)
                             <tr>
                                <td>{{$d->nama_jenis}}</td>
                                <td>{{$d->no_akun}}</td>  
                                <td>{{$d->tipe}}</td>  
                                <td>{{$d->catatan}}</td>  
                      
                                <td>  
                                   
                                    <a class="btn btn-default bg-info" href="{{route('coa.edit',[$d->id])}}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>            
                                    <form action="{{route('coa.destroy',[$d->id])}}" method="POST" class="btn btn-responsive">
                                        @csrf
                                        @method('DELETE')
                                          <button action="{{route('coa.destroy',[$d->id])}}" class="btn btn-default bg-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                          </button>
                                    </form>  
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                              <th>jenis</th>
                              <th>No. Akun</th>
                              <th>Tipe</th>
                              <th>catatan</th>
                              <th>Handle</th>
                             </tr>
                        </tfoot>
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


