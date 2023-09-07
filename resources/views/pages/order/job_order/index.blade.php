
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('content')
@include('sweetalert::alert')
<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('job_order.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah JO
                    </a> 
                </div>
                <div class="card-body">
                    <table id="datatb" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Kode JO</th>
                              <th>Pengirim (Customer)</th>
                              <th>Pelayaran (Supplier)</th>
                              <th>Status</th>
                              <th></th>
                            </tr>
                          </thead>
                        <tbody>
                            @if (isset($dataJO))
                                @foreach($dataJO as $item)
                                <tr>
                                    <td>{{ $item->no_jo }}</td>
                                    <td>{{ $item->kode }} - {{ $item->nama_cust }}</td>
                                    <td>{{ $item->nama_supp }}</td>
                                    <td>{{ $item->status}}</td>
                                    <td>                                    
                                        <!-- Default dropleft button -->
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{route('job_order.edit',[$item->id])}}" class="dropdown-item">
                                                    <span class="fas fa-edit mr-3"></span> Edit
                                                </a>
                                                <a href="/job_order/printJob/{{$item->id}}" method="get" rel="noopener" target="_blank"  class="dropdown-item">
                                                    <span class="fas fa-print mr-3"></span> Export PDF
                                                </a>
                                                <a href="{{ route('job_order.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                    <span class="fas fa-trash mr-3"></span> Delete
                                                </a>
                                            </div>
                                        </div>
                                       
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#datatb').dataTable({
            // responsive: true,
            // scrollX: true
        });
    } );
</script>
<script>
 

    var sessionMessage = "{{ session()->has('message') ? session('message') : '' }}";
    if (sessionMessage !== '') {
        toastr.success(sessionMessage);
    }
</script>
@endsection
