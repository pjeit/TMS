
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
@endsection

@section('content')
@include('sweetalert::alert')
<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('marketing.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                              <th>Nama</th>
                              <th>Grup</th>
                              <th>Role</th>
                              <th><div class="btn-group"></div></th>
                            </tr>
                          </thead>
                        <tbody>
                            <?php if(!empty($data)){ ?>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->nama_grup }}</td>  
                                    <td>{{ $item->nama_role }}</td>  
                                    <td>                                    
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{route('marketing.edit',[$item->id])}}" class="dropdown-item ">
                                                    <span class="fas fa-edit mr-3"></span> Edit
                                                </a>
                                                <a href="{{ route('marketing.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                    <span class="fas fa-trash mr-3"></span> Delete
                                                </a>
            
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            <?php } ?>
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
        responsive: true,
      });
    });
</script>
@endsection
