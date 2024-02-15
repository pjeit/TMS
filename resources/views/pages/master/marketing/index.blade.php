
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
@endsection

@section('content')

<style>

</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        <div class="card-header">
            <a href="{{route('marketing.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
            </a> 
        </div>
        <div class="card-body">
            <table id="myTable" class="table table-bordered table-striped table-hover" width="100%">
                <thead>
                    <tr>
                        <th>Grup</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>No Telp</th>
                        <th><div class="btn-group"></div></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data)){ ?>
                        @foreach($data as $item)
                        <tr>
                            <td>{{ $item->nama_grup }}</td>  
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->nama_role }}</td>  
                            <td>0{{ $item->telp1 }}</td>  
                            <td>                                    
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('EDIT_MARKETING')
                                            <a href="{{route('marketing.edit',[$item->id])}}" class="dropdown-item ">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                        @endcan
                                        @can('DELETE_MARKETING')
                                            <a href="{{ route('marketing.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Delete
                                            </a>
                                        @endcan
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
<script type="text/javascript">
    $(function () {
        var table = $('#myTable').DataTable({
            // responsive: true,
            scrollX: true
        });
    });
</script>
@endsection
