
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
                    <a href="{{route('grup.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Nama Grup</th>
                              <th>Nama PIC</th>
                              <th>Total Kredit</th>
                              <th>Total Max Kredit</th>
                              <th>Aksi</th>
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
                                    <a class="btn btn-default bg-info radiusSendiri" href="{{route('grup.edit',[$item->id])}}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>   

                                    <a href="{{ route('grup.destroy', $item->id) }}" class="btn btn-danger radiusSendiri" data-confirm-delete="true"><i class="fas fa-trash"></i> Hapus</a>
                                    

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

<script>
    
    
    var sessionMessage = "{{ session()->has('message') ? session('message') : '' }}";
    if (sessionMessage !== '') {
        toastr.success(sessionMessage);
    }
</script>
@endsection
