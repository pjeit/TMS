
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
                    <table id="datatable" class="table table-bordered table-striped table-hover" width='100%'>
                        <thead>
                            <tr>
                              <th>Nama</th>
                              <th>Alamat</th>
                              <th>Kredit Sekarang</th>
                              <th></th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($data as $item)
                             <tr>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->alamat }}</td>  
                                <td>{{ number_format($item->kredit_sekarang) }}</td>  
                                <td>                                    
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{route('customer.edit',[$item->id])}}" class="dropdown-item ">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                            <a href="{{ route('customer.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
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

@endsection
