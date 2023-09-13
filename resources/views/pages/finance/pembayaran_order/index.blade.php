
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
                    {{-- <a href="{{route('persetujuan_jo.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah JO
                    </a>  --}}
                </div>
                <div class="card-body">
                    <table id="datatable" class="table table-bordered table-striped" width='100%'>
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
                                @foreach($data as $item)
                                 <tr>
                                    <td>{{ $item->no_jo }}</td>
                                    <td>{{ $item->namaCustomer }}</td>
                                    <td>{{ $item->namaSupplier }}</td>
                                    <td>{{ $item->status}}</td>
                                    <td>                                    
                                        <a class="btn btn-success radiusSendiri" href="{{route('pembayaran_jo.edit',[$item->id])}}">
                                              <i class="fas fa-credit-card"></i> Pembayaran
                                        </a>   
                                    </td>
                                </tr>
                                @endforeach
                     
                        </tbody>
                    </table>
                        {{ $data->links('pagination::bootstrap-4') }}
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
