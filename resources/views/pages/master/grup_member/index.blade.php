
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('grup_member.index')}}">Grup Member</a></li>
@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <a href="{{route('grup_member.create')}}" class="btn btn-secondary btn-responsive float-left">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Nama</th>
                              <th>Grup</th>
                              <th>Role</th>
                              <th>Aksi</th>
                            </tr>
                          </thead>
                        <tbody>
                            <?php if(!empty($data)){ ?>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->nama_grup }}</td>  
                                    <td>{{ $item->nama_role }}</td>  
                                    <td>                                    
                                        <a class="btn btn-default bg-info" href="{{route('grup_member.edit',[$item->id])}}">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>   
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalHapus_{{ $item->id }}">
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
                                                <form action="{{route('grup_member.destroy',[$item->id])}}" method="POST" >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button action="{{route('grup_member.destroy',[$item->id])}}" class="btn btn-primary">Ya</button>
                                                </form>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </tr>
                                @endforeach
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
