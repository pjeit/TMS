
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('content')
@include('sweetalert::alert')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    {{-- <a href="{{route('grup_tujuan.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a>  --}}
                </div>
                <div class="card-body">
                    <table id="datatable" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Grup</th>
                                <th></th>
                            </tr>
                            </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>{{ $item->nama_grup }}</td>
                                <td style="text-align: center">                                    
                                    {{-- <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                        </div>
                                    </div> --}}
                                    
                                    <a class="btn btn-primary radiusSendiri" href="{{route('grup_tujuan.edit',[$item->id])}}">
                                        <span class="fas fa-edit "></span> Edit
                                    </a>   
                                    
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
    
</script>
<script>
    var sessionMessage = "{{ session()->has('message') ? session('message') : '' }}";
    if (sessionMessage !== '') {
        toastr.success(sessionMessage);
    }
</script>
@endsection
