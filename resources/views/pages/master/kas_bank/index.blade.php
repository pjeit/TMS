@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('coa.index')}}">Kas Bank</a></li>
@endsection
@include('sweetalert::alert')

@section('content')
{{-- <div class="container-fluid"> --}}
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('kas_bank.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle"> </i> Tambah Data
                    </a> 
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="myTable" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                              <th>Nama Kas / Bank </th>
                              <th>No. Akun</th>
                              <th>Tipe</th>
                              <th>Rekening Bank</th>
                              <th></th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($dataKas as $d)
                             <tr>
                                <td>{{$d->nama}}</td>
                                <td>{{$d->no_akun}}</td>  
                                <td>{{$d->tipe}}</td>  
                                @if($d->tipe == "Bank")
                                 <td>{{$d->bank}} - {{$d->no_akun}} ({{$d->rek_nama}})</td>  
                                @else
                                 <td></td>
                                @endif
                                <td>                                    
                                     <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{route('kas_bank.edit',[$d->id])}}" class="dropdown-item">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                            
                                            <a href="{{ route('kas_bank.destroy', $d->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Delete
                                            </a>
                                            
                                        </div>
                                    </div>
                                </td>
                                                   
                                
                            </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
            {{-- {{ $dataKas->links('pagination::bootstrap-4') }} --}}

                </div>
                <!-- /.card-body -->

            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
{{-- </div> --}}
<script type="text/javascript">
    $(function () {
      var table = $('#myTable').DataTable({
        // responsive: true,
        scrollX: true


      });
    });
</script>
@endsection


