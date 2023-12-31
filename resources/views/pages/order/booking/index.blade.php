
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')

@endsection

@section('content')
@include('sweetalert::alert')

<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('booking.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <table id="datatable" class="table table-bordered table-striped" width='100%'>
                        <thead>
                            <tr>
                              <th>No Booking</th>
                              <th>Customer</th>
                              <th>Tujuan</th>
                              {{-- <th>Tujuan</th> --}}
                              {{-- <th>Catatan</th> --}}
                              <th></th>
                            </tr>
                          </thead>
                        <tbody>

                            @foreach($data as $item)
                             <tr>
                                <td>{{ $item->no_booking }}</td>
                                <td>{{ $item->namaCustomer }}</td>  
                                <td>{{  $item->namaTujuan}}</td>  
                                <td>    
                                    
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{route('booking.edit',[$item->id])}}" class="dropdown-item">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                            <a href="{{ route('booking.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Delete
                                            </a>
                                        </div>
                                    </div>
                                    {{-- <a class="btn btn-default bg-info radiusSendiri" href="{{route('booking.edit',[$item->id])}}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>   
                                    <!-- Bu tton trigger modal -->
                                    <button type="button" class="btn btn-danger radiusSendiri" data-toggle="modal" data-target="#modalHapus_{{ $item->id }}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>           --}}
                                </td>
                                <!-- Modal -->
                                <div class="modal fade" id="modalHapus_{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Hapus Data</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah anda yakin ingin menghapus data secara permanen?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tidak</button>
                                            <form action="{{route('booking.destroy',[$item->id])}}" method="POST" >
                                                @csrf
                                                @method('DELETE')
                                                <button action="{{route('booking.destroy',[$item->id])}}" class="btn btn-primary">Ya</button>
                                            </form>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- {{ $data->links('pagination::bootstrap-4') }} --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
