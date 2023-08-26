
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
                    <table id="example1" class="table table-bordered table-striped">
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
                            <?php if(isset($dataJO)){ ?>
                                @foreach($dataJO as $item)
                                 <tr>
                                    <td>{{ $item->no_jo }}</td>
                                    <td>{{ $item->id_customer }}</td>
                                    <td>{{ $item->id_supplier }}</td>
                                    <td>{{ $item->status}}</td>
                                    <td>                                    
                                        <a class="btn btn-default bg-info radiusSendiri" href="{{route('persetujuan_jo.edit',[$item->id])}}">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>   
                                        <a href="{{ route('persetujuan_jo.destroy', $item->id) }}" class="btn btn-danger radiusSendiri" data-confirm-delete="true"><i class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                                @endforeach
                            <?php } ?>
                            <tr>
                                    <td>JO/TAN/2008001</td>
                                    <td>PT. AGUNG SEJAHTERAH</td>
                                    <td>PT. TANTO</td>
                                    <td>Menunggu Pembayaran Finance</td>
                                    <td>                                    
                                        <a class="btn btn-default bg-success radiusSendiri" href="{{route('persetujuan_jo.create')}}">
                                            <i class="fas fa-credit-card"></i> Pembayaran
                                        </a>   
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                    <?php if(isset($dataJO)){ ?>
                        {{ $dataJO->links('pagination::bootstrap-4') }}
                    <?php } ?>

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
