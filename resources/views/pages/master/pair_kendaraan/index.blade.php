
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('supplier.index')}}">Pair Kendaraan</a></li>
@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                {{-- <div class="card-header">
                    <a href="{{route('pair_kendaraan.create')}}" class="btn btn-secondary btn-responsive float-left">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div> --}}
                <div class="card-body">
                      <div class="form-group w-25">
                        <form id="filterForm" action="{{ route('pair_kendaraan.cari')}}" method="get">
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
                    <table id="datatable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                              <th>No Polisi Kendaraan</th>
                              <th>Chassis Kendaraan</th>
                              <th>Kategori Kendaraan</th>
                              <th>Letak Kendaraan</th>
                              <th>Aksi</th>
                            </tr>
                          </thead>
                        <tbody>
                            @foreach($dataPair as $item)
                             <tr>
                                <td>{{ $item->no_polisi }}</td>
                                <td>{{ $item->chassis_model }}</td>  
                                <td>{{ $item->kategoriKendaraan }}</td>  
                                <td>{{ $item->namaKota }}</td>  
                                <td>                                    
                                    <a class="btn btn-default bg-info radiusSendiri" href="{{route('pair_kendaraan.edit',[$item->id])}}">
                                        <i class="far nav-icon fa fa-truck">+</i> Pairing Chassis
                                    </a>   
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
<script>

</script>
@endsection
