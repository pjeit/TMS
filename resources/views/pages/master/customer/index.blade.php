
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('customer.index')}}">Customer</a></li>
@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('customer.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Nama</th>
                              <th>Alamat</th>
                              {{-- <th>Telp</th> --}}
                              <th>Kredit Sekarang</th>
                              {{-- <th>Kredit Max</th> --}}
                              <th>Aksi</th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($data as $item)
                             <tr>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->alamat }}</td>  
                                {{-- <td>{{ $item->telp1 }}</td>   --}}
                                <td>{{ number_format($item->kredit_sekarang) }}</td>  
                                {{-- <td>{{ number_format($item->max_kredit) }}</td> --}}
                                <td>                                    
                                    <a class="btn btn-default bg-info radiusSendiri" href="{{route('customer.edit',[$item->id])}}">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>   
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-danger radiusSendiri" data-toggle="modal" data-target="#modalHapus_{{ $item->id }}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>          
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
                                            <form action="{{route('customer.destroy',[$item->id])}}" method="POST" >
                                                @csrf
                                                @method('DELETE')
                                                <button action="{{route('customer.destroy',[$item->id])}}" class="btn btn-primary">Ya</button>
                                            </form>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
