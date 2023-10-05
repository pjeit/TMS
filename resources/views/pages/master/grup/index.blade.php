
@extends('layouts.home_master')

@section('content')
@include('sweetalert::alert')
<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('grup.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped table-hover" width="100%">
                        <thead>
                            <tr>
                                <th>Nama Grup</th>
                                <th>Nama PIC</th>
                                <th>Total Kredit</th>
                                <th>Total Max Kredit</th>
                                <th ></th>
                            </tr>
                            </thead>
                        <tbody>
                            @foreach($data as $item)
                                <tr>
                                <td>{{ $item->nama_grup }}</td>
                                <td>{{ $item->nama_pic }}</td>
                                <td>{{ number_format($item->total_kredit,0,",",".") }}</td>
                                <td>{{ number_format($item->total_max_kredit,0,",",".") }}</td>
                                <td>                                    
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{route('grup.edit',[$item->id])}}" class="dropdown-item ">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                            <a href="{{ route('grup.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
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
        // responsive: true,
        scrollX: true
      });
    });
</script>

@endsection
