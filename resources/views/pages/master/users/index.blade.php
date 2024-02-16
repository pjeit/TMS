@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('users.index')}}">Users</a></li>
@endsection
@include('sweetalert::alert')

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
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('users.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                              <th>Username</th>
                              <th>Posisi</th>
                              <th>Karyawan</th>
                              <th>Cabang Kantor</th>
                              <th></th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($dataUser as $d)
                             <tr>
                                <td>{{$d->username}}</td>
                                <td>{{$d->role->name}}</td>
                                <td>{{$d->karyawan?$d->karyawan->nama_panggilan:''}}</td>
                                <td>{{$d->karyawan?$d->karyawan->cabang->nama:''}}</td>
                                <td>                                    
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{route('users.edit',[$d->id])}}" class="dropdown-item">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                            
                                            <a href="{{ route('users.destroy', $d->id) }}" class="dropdown-item" data-confirm-delete="true">
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
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
      var table = $('#myTable').DataTable({
        scrollX: true,
      });
    });
</script>
@endsection


