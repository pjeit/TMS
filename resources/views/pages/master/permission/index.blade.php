@extends('layouts.home_master')
@include('sweetalert::alert')

@section('content')
<div class="container-fluid">
    <div class="card radiusSendiri">
        <div class="card-header">
            <a href="{{route('permission.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                <i class="fa fa-plus-circle"> </i> Tambah Data
            </a> 
        </div>
        <div class="card-body">
            <table id="myTable" class="table table-bordered table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Menu</th>
                        <th>Action</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($data as $key => $item)
                <tr>
                    <td>{{ $item->menu }}</td>
                    <td>{{ $item->name }}</td>
                    <td>       
                        <div class="btn-group dropleft">
                           <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                               <i class="fa fa-list"></i>
                           </button>
                           <div class="dropdown-menu">
                               <a href="{{ route('permission.edit', [$item->id]) }}" class="dropdown-item">
                                   <span class="fas fa-edit mr-3"></span> Edit
                               </a>
                               <a href="{{ route('permission.delete', [$item->id]) }}" class="dropdown-item" data-confirm-delete="true">
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

<script type="text/javascript">
    $(function () {
        var table = $('#myTable').DataTable({
            // responsive: true,
            // scrollX: true
        });
    });
</script>
@endsection


