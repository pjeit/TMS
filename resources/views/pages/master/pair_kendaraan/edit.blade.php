
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('pair_kendaraan.index')}}">Pair Kendaraan</a></li>
<li class="breadcrumb-item">Edit</li>

@endsection

@section('content')
<div class="container-fluid">
  
    @if ($errors->any())
    {{-- <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div> --}}
        @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endforeach

    @endif
    <form action="{{ route('pair_kendaraan.update', [$dataKendaraan[0]->id]) }}" method="POST" >
    @csrf
    @method('PUT')
     <div class="card">
                <div class="card-header">
                     <tr>
                        <td colspan="6">
                            <a href="javascript:;" class="btn btn-danger" id="addmore"><i class="fa fa-fw fa-plus-circle"></i>Tambah Chassis</a>
                            <button type="submit" name="save" id="save" value="save" class="btn btn-primary"><i class="fa fa-fw fa-save"></i>Simpan</button>
                        </td>
                    </tr>
                </div>
                <div class="card-body">
                    <input type="hidden" name="action" value="saveAddMore">
                    <table class="table table-bordered table-striped" id="sortable">
                        <thead>
                            <tr>
                                {{-- <th width="120" class="text-center">Insetion Date</th>
                                <th>User Name</th> --}}
                                <th width="250">Model Chassis</th>
                                {{-- <th>User Email</th>
                                <th>User Phone#</th> --}}
                                <th width="20" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                            <tbody id="tb"> 
                                @if(!empty($dataPaired))

                                    @foreach ($dataPaired as $dataP)
                                    <tr>
                                        <td>
                                            <input type="hidden" name='idPairedNya[]' value="{{$dataP->id}}">
                                            <select class="form-control selectpicker" name="chasis[]" id="chasis" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                <option value="">--Pilih Chasis--</option>
                                                @foreach($dataChassis as $data)
                                                    <option value="{{$data->id}}" {{($dataP->chassis_id == $data->id)? 'selected':'';}}>{{$data->kode}} - {{$data->karoseri}} - {{$data->namaModel}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data chassis ini?')){$(this).closest('tr').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
                                    </tr>
                                    
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2" class="bg-light text-center"><strong>No Record(s) Found!</strong></td>
                                    </tr>

                                @endif
                            </tbody>
                            <tfoot>
                            
                            </tfoot>
                    </table>
                </div>
                {{-- <div class="card-footer">
               
                        <button type="submit" class="btn btn-primary">Simpan</button>
                
                </div> --}}
            </div>
   
    </form>

</div>

<script type="text/javascript">
$(document).ready(function(){
  $("#addmore").on("click",function(){
         
    $('#tb').append(
        `<tr>
            <td>
               <input type="hidden" name='idPairedNya[]' value="">
                <select class="form-control selectpicker" name="chasis[]" id="chasis" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                    <option value="">--Pilih Chasis--</option>
                    @foreach($dataChassis as $data)
                        <option value="{{$data->id}}">{{$data->kode}} - {{$data->karoseri}} - {{$data->namaModel}}</option>
                    @endforeach
                </select>
            </td>
            <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data chassis ini?')){$(this).closest('tr').remove();}" class="btn btn-danger"><i class="fa fa-fw fa-trash-alt"></i></button></td>
        </tr>`
    );
    $('.selectpicker').selectpicker('refresh');
    // $('#save').removeAttr('hidden',true);
               
    });
});

</script>
@endsection
