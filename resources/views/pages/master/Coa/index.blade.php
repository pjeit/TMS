@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('coa.index')}}">COA</a></li>
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
                    <a href="{{route('coa.create')}}" class="btn btn-primary radiusSendiri btn-responsive float-left">
                        <i class="fa fa-plus-circle"> </i> Tambah Data
                    </a> 
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                              <th>jenis</th>
                              <th>No. Akun</th>
                              <th>Tipe</th>
                              <th>catatan</th>
                              <th></th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($dataCOA as $d)
                             <tr>
                                <td>{{$d->nama_jenis}}</td>
                                <td>{{$d->no_akun}}</td>  
                                <td>{{strtoupper($d->tipe) }}</td>  
                                <td>{{$d->catatan}}</td>  
                      
                                <td>                                    
                                    {{-- <a class="btn btn-default bg-info radiusSendiri" href="{{route('coa.edit',[$d->id])}}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>   
     
                                    <a href="{{ route('coa.destroy', $d->id) }}" class="btn btn-danger radiusSendiri" data-confirm-delete="true"><i class="fas fa-trash"></i> Hapus</a> --}}

                                           
                                     <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{route('coa.edit',[$d->id])}}" class="dropdown-item">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                            
                                            <a href="{{ route('coa.destroy', $d->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Delete
                                            </a>
                                            
                                        </div>
                                    </div>
                                </td>
                                                   
                                
                                <!-- Modal -->
                               
                            </tr>
                            @endforeach
                        </tbody>
                      
                    </table>
                {{-- {{ $dataCOA->links('pagination::bootstrap-4') }} --}}

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
      var table = $('#myTable').DataTable({
        scrollX: true,
      });
    });
</script>
@endsection


