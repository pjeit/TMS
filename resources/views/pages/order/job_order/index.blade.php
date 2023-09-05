
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
                              <th>Aksi</th>
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
                                        <div class="dropdown custom-dropdown">
                                            <a href="#" data-toggle="dropdown" class="dropdown-link bg-gray rounded-circle" aria-haspopup="true" aria-expanded="false">
                                                <span class="fa fa-bolt "></span>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href="{{route('job_order.edit',[$item->id])}}" class="dropdown-item">
                                                    <span class="fas fa-edit mr-3"></span> Edit
                                                </a>
                                                <a href="{{route('job_order.storage_demurage',[$item->id])}}" method="get" rel="noopener" target="_blank"  class="dropdown-item">
                                                    <span class="fas fa-inbox mr-3"></span> Input Storage/Demurage
                                                </a>
                                                <a href="/job_order/printJob/{{$item->id}}" method="get" rel="noopener" target="_blank"  class="dropdown-item">
                                                    <span class="fas fa-print mr-3"></span> Export PDF
                                                </a>
                                                <a href="{{ route('job_order.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                    <span class="fas fa-trash mr-3"></span> Delete
                                                </a>
                                              
                                            </div>
                                        </div>
                                        {{-- <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                            <div class="btn-group" role="group">
                                              <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-bolt"></i>
                                              </button>
                                              <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                <a class="dropdown-item bg-info radiusSendiri" href="{{route('job_order.edit',[$item->id])}}">
                                                    <i class="fas fa-edit"></i> 
                                                </a>   
                                                <a href="/job_order/printJob/{{$item->id}}" method="get" rel="noopener" target="_blank" class="dropdown-item bg-fuchsia radiusSendiri"><i class="fas fa-print"></i> </a>
                                                <a href="{{ route('job_order.destroy', $item->id) }}" class="dropdown-item bg-danger radiusSendiri" data-confirm-delete="true"><i class="fas fa-trash"></i> </a>
                                              </div>
                                            </div>
                                          </div> --}}
                                       
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
