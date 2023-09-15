
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('supplier.index')}}">Supplier</a></li>
@endsection

@section('content')
@include('sweetalert::alert')

<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('supplier.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    {{-- <div class="form-group">
                        <label>Filter Supplier</label>
                        <form action="/supplier/jenisFilter/" method="get">
                            <div class="input-group col-md-4">
                                <select class="form-control selectpicker" name="jenisFilter" id="jenisFilter" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Agama">
                                        <option value="">--Pilih Jenis Supplier--</option>
                                    
                                    @foreach($dataJenisFilter as $dat)
                                        <option value="{{$dat->id}}">{{$dat->nama}}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-lg btn-default">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div> --}}
                    <div class="form-group w-25">
                        <form id="filterForm" action="{{ route('filterSupplier.cari')}}" method="get">
                            <label>Filter Supplier</label>
                             <select class="form-control select2" style="width: 100%;" id='jenisFilter' name="jenisFilter">
                                <option value="">ALL</option>
        
                                @foreach($dataJenisFilter as $dat)
                                        <option value="{{$dat->id}}" id="">{{$dat->nama}}</option>
                                @endforeach
                                <input type="hidden" id="SimpenId">
                                
                            </select>
                                {{-- <select class="form-control select2" name="jenisFilter" id="jenisFilter" data-live-search="true" data-show-subtext="true" data-placement="bottom" data-placeholder="Pilih Agama">
                                    @foreach($dataJenisFilter as $dat)
                                        <option value="{{$dat->id}}" id="">{{$dat->nama}}</option>
                                    @endforeach
                                    <option value="">ALL</option>

                                </select>
                                <input type="hidden" id="SimpenId"> --}}
                        </form>
                    </div>
                    <div id="data">
                        <table id="datatable" class="table table-bordered table-striped" width='100%'>
                            <thead>
                                <tr>
                                <th>Jenis Supplier</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Telp</th>
                                <th>Lokasi Supplier</th>
                                <th>Catatan</th>
                                <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->jenis }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->alamat }}</td>  
                                    <td>0{{ $item->telp }}</td>  
                                    <td>{{ $item->kota }}</td>
                                    <td>{{ $item->catatan }}</td>
                                    <td>                                    
                                        {{-- <a class="btn btn-default bg-info radiusSendiri" href="{{route('supplier.edit',[$item->id])}}">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>   
                                                <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-danger radiusSendiri" data-toggle="modal" data-target="#modalHapus">
                                                <i class="fas fa-trash"></i> Hapus
                                        </button>   
                                         --}}
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{route('supplier.edit',[$item->id])}}" class="dropdown-item">
                                                    <span class="fas fa-edit mr-3"></span> Edit
                                                </a>
                                                
                                                <a href="{{ route('supplier.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                    <span class="fas fa-trash mr-3"></span> Delete
                                                </a>
                                                
                                            </div>
                                        </div>   
                                        
                                    </td>
                                    {{-- <!-- Modal -->
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
                                                <form action="{{route('supplier.destroy',[$item->id])}}" method="POST" class="btn btn-responsive">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button action="{{route('supplier.destroy',[$item->id])}}" class="btn btn-primary">Ya</button>
                                                </form>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal" >Tidak</button>
                                        </div>
                                        </div>
                                        </div>
                                    </div> --}}
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
</div>

<script type="text/javascript">
var currentUrl = window.location.href;
var baseUrl = currentUrl.split('=');
var idUrl = parseFloat(baseUrl[1]);
// $('#jenisFilter').val(idUrl).selectpicker("refresh");

 $(document).ready(function() {
    var id =localStorage.getItem("SimpenId");
    console.log(id);
    if (!isNaN(idUrl) &&idUrl==id) {
        $(`#jenisFilter option[value="${idUrl}"]`).prop('selected', true);
    }
    $('#jenisFilter').change(function (e) {
      localStorage.setItem("SimpenId", $(this).val());
      e.preventDefault();
      
        $('#filterForm').submit();
    });
 });
</script>  
 <script type="text/javascript">
//  $(document).ready(function() {
   
//     $('#jenisFilter').change(function (e) {
//     //   $.get('/supplier/filter?jenisFilter='+$(this).val(), function (data) {

//     //     var arr = data;
//     //             console.log(data);

//     //   });

//        $.ajax({
//             url:'/supplier/filter?jenisFilter='+$(this).val(), // Update this URL to your actual endpoint
//             type: 'GET',
//             // data: { jenisFilter: $(this).val() },
//             success: function(data) {
//                 // Update the table body with the fetched data
//                 // var arr = JSON.parse(data);
//                 console.log(data);
//                 // var komik = arr["data"];
//                 // $('#example1 tbody').html(data);
//             },
//             error: function() {
//                 console.log('Error fetching filtered data');
//             }
//         });
//     });
//  });
   
// </script> 

@endsection
