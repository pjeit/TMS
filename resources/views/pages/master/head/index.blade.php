
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('content')
<style>

</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        <div class="card-header">
            <a href="{{route('head.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
            </a> 
        </div>
        <div class="card-body">
            <table id="myTable" class="table table-bordered table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Cabang</th>
                        <th>No Polisi</th>
                        <th>No. Mesin & Rangka</th>
                        <th>Merk & Model</th>
                        <th>Tahun & Warna</th>
                        <th>Driver</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if($data != null)
                        @foreach($data as $item)
                        <tr>
                            <td>{{$item->cabangPje}}</td>

                            <td>{{$item->no_polisi}}</td>
                            <!-- ganti kolom no_kendaraan menjadi no_mesin di DB tabel kendaraan -->
                            <td>{{$item->no_mesin}} - {{$item->no_rangka}} </td>  
                            <td>{{$item->merk_model}}</td>  
                            <td>{{$item->tahun_pembuatan}} - {{$item->warna}} </td>  
                            <td>{{ $item->nama_lengkap }}</td>
                            <td>                                    
                                {{-- <a class="btn btn-default bg-info radiusSendiri" href="{{route('head.edit',[$item->id])}}">
                                    <i class="fas fa-edit"></i> Edit
                                </a>   
                                <button type="button" class="btn btn-danger radiusSendiri" data-toggle="modal" data-target="#modalHapus">
                                        <i class="fas fa-trash"></i> Hapus
                                </button>      --}}
                                
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('EDIT_HEAD')
                                            <a href="{{route('head.edit',[$item->id])}}" class="dropdown-item">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                        @endcan
                                        
                                        @can('DELETE_HEAD')
                                            {{-- <a href="{{ route('head.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Delete
                                            </a> --}}
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="margin-right: -1.75rem">Tidak</button>

                                    <form action="{{route('head.destroy',[$item->id])}}" method="POST" class="btn btn-responsive">
                                        @csrf
                                        @method('DELETE')
                                        <button action="{{route('head.destroy',[$item->id])}}" class="btn btn-primary">Ya</button>
                                    </form>
                            </div>
                            </div>
                            </div>
                        </div>

                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        var table = $('#myTable').DataTable({
            responsive: true,
            // scrollX: true,
        });
    });
</script>

<script>

</script>
@endsection
