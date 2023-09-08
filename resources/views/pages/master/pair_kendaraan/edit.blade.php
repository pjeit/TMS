
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
    <form action="{{ route('pair_kendaraan.update', [$dataKendaraan[0]->id]) }}" method="POST" id="formPair">
        @csrf
        @method('PUT')
        {{-- <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('pair_kendaraan.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                <button type="submit" class="btn btn-success radiusSendiri float-right" name="save" id="save" value="save" ><i class="fa fa-fw fa-save"></i>Simpan</button>
            </div>
        </div> --}}
        <div class="row">
            <div class="col">
                <div class="card radiusSendiri" >
                    <div class="card-header">
                        <a href="{{ route('pair_kendaraan.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                        <button type="submit" class="btn btn-success radiusSendiri float-right" name="save" id="save" value="save" ><i class="fa fa-fw fa-save"></i>Simpan</button>
                        {{-- <h3 class="card-title">Keterangan Kendaraan</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                            </button>
                        </div> --}}
                    </div>
                    <div class="card-body" >
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                <label for="inputName">Nomor Polisi</label>
                                <input type="text" id="inputName" class="form-control" value="{{$dataKendaraan[0]->no_polisi}}" readonly>
                            </div>
                            <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                <label for="inputProjectLeader">Tahun Pembuatan</label>
                                <input type="text" id="inputProjectLeader" class="form-control" value="{{$dataKendaraan[0]->tahun_pembuatan}}" readonly>
                            </div>
                          
                            
                            <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                <label for="inputProjectLeader">Merk</label>
                                <input type="text" id="inputProjectLeader" class="form-control" value="{{$dataKendaraan[0]->merk_model}}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                <label for="inputClientCompany">Nomor Mesin</label>
                                <input type="text" id="inputClientCompany" class="form-control" value="{{$dataKendaraan[0]->no_mesin}}" readonly>
                            </div>
                            <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                <label for="inputProjectLeader">Nomor Rangka</label>
                                <input type="text" id="inputProjectLeader" class="form-control" value="{{$dataKendaraan[0]->no_rangka}}" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                <label for="chassisKendaraan">Ekor Kendaraan</label>
                                   @if($dataPaired=='[]')
                                        <input type="hidden" name='idPairedNya[]' value="">
                                        <select class="form-control selectpicker" name="chasis[]" id="chasis" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                            <option value="">--Pilih Chasis--</option>
                                            @foreach($dataChassis as $data)
                                            <option value="{{$data->id}}">{{$data->kode}} - {{$data->karoseri}} - {{$data->namaModel}}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        @foreach ($dataPaired as $dataP)
                                            <input type="hidden" name='idPairedNya[]' value="{{$dataP->id}}">
                                            <input type="hidden" name='isAktif[]' value="{{$dataP->is_aktif}}">
                                            <select class="form-control selectpicker" name="chasis[]" id="chasis" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                <option value="">--Pilih Chasis--</option>
                                                @foreach($dataChassis as $data)
                                                    <option value="{{$data->id}}" {{($dataP->chassis_id == $data->id)? 'selected':'';}}>{{$data->kode}} - {{$data->karoseri}} - {{$data->namaModel}}</option>
                                                @endforeach
                                            </select>
                                        @endforeach
                                    @endif
                            </div>
                            <div class="form-group col-sm-12 col-md-6 col-lg-6">
                                <label for="chassisKendaraan">Driver</label>
                                @if($dataPaired=='[]')
                                    <select class="form-control selectpicker" name="driver" id="driver" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                        <option value="">--Pilih Driver--</option>
                                        @foreach($dataDriver as $driver)
                                            <option value="{{$driver->id}}" >{{$driver->nama_lengkap}} </option>
                                        @endforeach
                                    </select>
                                @else
                                    <select class="form-control selectpicker" name="driver" id="driver" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                        <option value="">--Pilih Driver--</option>
                                        @foreach($dataDriver as $driver)
                                            <option value="{{$driver->id}}" {{($driver->id == $dataPaired[0]->driver_id)? 'selected':'';}}>{{$driver->nama_lengkap}} </option>
                                        @endforeach
                                    </select>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- 
                <div class="col-6">
                    <div class="card radiusSendiri" >
                        <div class="card-header">
                            <tr>
                                <td >
                                    <a href="javascript:;" class="btn btn-secondary radiusSendiri" id="addmore"><i class="fa fa-fw fa-plus-circle"></i>Tambah Chassis</a>
                                </td>
                            </tr>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: scroll;">
                            <input type="hidden" name="action" value="saveAddMore">
                            <table class="table" id="sortable">
                                <thead>
                                    <tr>
                                        <th width="250">Model Chassis</th>
                                        <th width="20" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                    <tbody id="tb"> 
                                        @if($dataPaired=='[]')
                                            <tr>
                                                <td>
                                                    <input type="hidden" name='idPairedNya[]' value="">
                                                    <select class="form-control selectpicker" name="chasis[]" id="chasis" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                        <option value="">--Pilih Chasis--</option>
                                                        @foreach($dataChassis as $data)
                                                        <option value="{{$data->id}}">{{$data->kode}} - {{$data->karoseri}} - {{$data->namaModel}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @else
                                            @foreach ($dataPaired as $dataP)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name='idPairedNya[]' value="{{$dataP->id}}">
                                                    <input type="hidden" name='isAktif[]' value="{{$dataP->is_aktif}}">
                                                    <select class="form-control selectpicker" name="chasis[]" id="chasis" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                        <option value="">--Pilih Chasis--</option>
                                                        @foreach($dataChassis as $data)
                                                            <option value="{{$data->id}}" {{($dataP->chassis_id == $data->id)? 'selected':'';}}>{{$data->kode}} - {{$data->karoseri}} - {{$data->namaModel}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            <td align="center" class="text-danger">
                                                    <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data chassis ini?')){ $(this).closest('tr').hide(); $(this).closest('tr').find('input[name^=\'isAktif\']').val('N');}" class="btn btn-danger radiusSendiri">
                                                        <i class="fa fa-fw fa-trash-alt"></i>
                                                    </button>
                                            </td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                    
                                    </tfoot>
                            </table>
                        </div>
                    </div>
                    
                </div> 
            --}}
        </div>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function(){
    var idBarang = $("#barang").val();
    
    $("#formPair").on("submit",function(e){
            var url = $("#formPair").attr('action');
            var formElement = document.getElementById("formPair"); 
            var formData = new FormData(formElement);
            e.preventDefault();
            $.ajax({
                method: 'POST',
                url: url,
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    if (response.hasOwnProperty('id')) {
                        toastr.success(response.message);
                        console.log(response);

                        window.location.href = '{{ route("pair_kendaraan.index") }}';
                    } else {
                        toastr.error(response.message);
                    }
                },
                 error: function (xhr, status, error) {
                    if (xhr.responseJSON && xhr.responseJSON.errorsCatch) {
                        var pesanError = xhr.responseJSON.errorsCatch;

                        for (var i in pesanError) {
                            toastr.error(pesanError[i]);
                        }

                    } 
                    else if (xhr.responseJSON && xhr.responseJSON.errorServer) {
                        var pesanError = xhr.responseJSON.errorServer;
                        console.table(pesanError);

                    }
                    
                    else {
                        toastr.error("Terjadi kesalahan saat mengirim data. " + error);
                    }

                    console.log("XHR status:", status);
                    console.log("Error:", error);
                    console.log("Response:", xhr.responseJSON);
                }
            });
    });
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
                <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="{$(this).closest('tr').remove();}" class="btn btn-danger radiusSendiri"><i class="fa fa-fw fa-trash-alt"></i></button></td>
            </tr>`
        );
        $('.selectpicker').selectpicker('refresh');
        // $('#save').removeAttr('hidden',true);
    });
});

</script>
@endsection
