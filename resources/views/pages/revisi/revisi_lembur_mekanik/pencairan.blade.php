
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
{{-- <li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('coa.index')}}">COA</a></li>
<li class="breadcrumb-item">Edit</li> --}}
@endsection
@section('content')
<style>
#preview_foto_lembur {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}
</style>
<div class="container-fluid">
    @if ($errors->any())
        @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endforeach

    @endif
    <form action="{{ route('pencairan_lembur_mekanik_revisi.save',[$dataLemburMekanik->id]) }}" method="POST" id="post" >
        @csrf
        @method('post')
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('lembur_mekanik_revisi.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body" >
                        <ul class="nav nav-tabs mb-3 mt-3 nav-fill" id="justifyTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link nav-link-tab active" id="justify-pencairan-tab" data-toggle="tab" href="#justify-pencairan" role="tab" aria-controls="justify-pencairan" aria-selected="true">Pencairan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-tab " id="justify-data-tab" data-toggle="tab" href="#justify-data" role="tab" aria-controls="justify-data" aria-selected="false">Informasi Lembur</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a class="nav-link nav-link-tab" id="justify-foto-tab" data-toggle="tab" href="#justify-foto" role="tab" aria-controls="justify-foto" aria-selected="false">Foto</a>
                            </li> --}}
                        </ul>

                        <div class="tab-content">
                            {{-- Pencairan --}}
                                <div class="tab-pane fade show active" id="justify-pencairan" role="tabpanel" aria-labelledby="justify-pencairan-tab">

                                    <div class="row">
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="row">
                                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                    <label for="tanggal_klaim">Tanggal Pencairan<span style="color:red">*</span></label>
                                                    <div class="input-group mb-0">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                        </div>
                                                        <input type="text" autocomplete="off" name="tanggal_pencairan" class="form-control date @error('tanggal_pencairan') is-invalid @enderror" id="tanggal_pencairan" placeholder="dd-M-yyyy" value="{{old('tanggal_pencairan',!empty($dataLemburMekanikRiwayat->tanggal_pencairan)?\Carbon\Carbon::parse($dataLemburMekanikRiwayat->tanggal_pencairan)->format('d-M-Y'):'')}}">
                                                        {{-- <input type="text" autocomplete="off" name="tanggal_pencairan" class="form-control date @error('tanggal_pencairan') is-invalid @enderror" id="tanggal_pencairan" placeholder="dd-M-yyyy" value="{{old('tanggal_pencairan')}}"> --}}
                                                        @error('tanggal_pencairan')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                                    <label for="tanggal_pencatatan">Tanggal Pencatatan<span style="color:red">*</span></label>
                                                    <div class="input-group mb-0">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                        </div>
                                                        <input type="text" autocomplete="off" name="tanggal_pencatatan" class="form-control date @error('tanggal_pencatatan') is-invalid @enderror" id="tanggal_pencatatan" placeholder="dd-M-yyyy" value="{{old('tanggal_pencatatan',!empty($klaim_supir_riwayat->tanggal_pencatatan)?\Carbon\Carbon::parse($klaim_supir_riwayat->tanggal_pencatatan)->format('d-M-Y'):'')}}">
                                                        
                                                        @error('tanggal_pencatatan')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div> --}}
                                            </div>
                                            
                                            <div class="form-group" id="div_catatan_pencairan">
                                                <label for="total_reimburse">Catatan Pencairan</label>
                                                <div class="form-group">
                                                    <input type="text" name="catatan_pencairan" class="form-control @error('catatan_pencairan') is-invalid @enderror" id="catatan_pencairan" placeholder="" value="{{old('catatan_pencairan',!empty($klaim_supir_riwayat->catatan_pencairan)? $klaim_supir_riwayat->catatan_pencairan:'')}}">
                                                    {{-- <input type="text" name="catatan_pencairan" class="form-control @error('catatan_pencairan') is-invalid @enderror" id="catatan_pencairan" placeholder="" value="{{old('catatan_pencairan')}}"> --}}
                                                    
                                                    @error('catatan_pencairan')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div> 
                                            <div class="form-group" id="div_alasan_tolak">
                                                <label for="total_reimburse">Alasan Tolak</label>
                                                <div class="form-group">
                                                    <input type="text" name="alasan_tolak" class="form-control @error('alasan_tolak') is-invalid @enderror" id="alasan_tolak" placeholder="" value="{{old('alasan_tolak',!empty($dataLemburMekanikRiwayat->alasan_tolak)? $dataLemburMekanikRiwayat->alasan_tolak:'')}}">
                                                    {{-- <input type="text" name="alasan_tolak" class="form-control @error('alasan_tolak') is-invalid @enderror" id="alasan_tolak" placeholder="" value="{{old('alasan_tolak')}}"> --}}

                                                    @error('alasan_tolak')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <label for="tipe">Status Pencairan :</label>
                                                {{-- <br> --}}
                                                {{-- <div class="icheck-primary d-inline">
                                                    <input id="PENDING" type="radio" name="status" value="PENDING" {{'PENDING'== $dataLemburMekanik->status? 'checked' :'' }}>
                                                    <label class="form-check-label" for="PENDING">Pending</label>
                                                </div> --}}
                                                <div class="icheck-primary d-inline ml-3">
                                                    <input id="ACCEPTED" type="radio" name="status" value="ACCEPTED" checked {{--{{'ACCEPTED' == $dataLemburMekanik->status? 'checked' :'' }}--}}>
                                                    <label class="form-check-label" for="ACCEPTED">Terima</label>
                                                </div>
                                                <div class="icheck-danger d-inline ml-3">
                                                    <input id="REJECTED" type="radio" name="status" value="REJECTED" {{'REJECTED'== $dataLemburMekanik->status? 'checked' :'' }}>
                                                    <label class="form-check-label" for="REJECTED">Tolak</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label for="total_reimburse">Nominal Lembur<span style="color:red">*</span></label>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input disabled type="text" name="nominal_lembur" class="form-control numaja uang @error('nominal_lembur') is-invalid @enderror" id="nominal_lembur" placeholder="" value="{{old('total_lembur',number_format($dataLemburMekanik->nominal_lembur))}}">
                                                    @error('nominal_lembur')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <label for="total_reimburse">Jumlah Lembur Dicairkan <span style="color:red">*</span></label>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" name="total_pencairan" onkeyup="cek_max_pencairan();" class="form-control numaja uang @error('total_pencairan') is-invalid @enderror" id="total_pencairan" placeholder="" value="{{old('total_pencairan',!empty($dataLemburMekanikRiwayat->total_pencairan)? number_format($dataLemburMekanikRiwayat->total_pencairan):'')}}">
                                                    {{-- <input type="text" name="total_pencairan" onkeyup="cek_max_pencairan();" class="form-control numaja uang @error('total_pencairan') is-invalid @enderror" id="total_pencairan" placeholder="" value="{{old('total_pencairan')}}"> --}}

                                                    @error('total_pencairan')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <label for="">Pilih Kas<span class="text-red">*</span> </label>
                                                <select name="kas" class="select2" style="width: 100%" id="kas" required>
                                                    <option value="">── PILIH KAS ──</option>
                                                    @foreach ($dataKas as $kas)
                                                        <option value="{{ $kas->id }}" {{ $kas->id == 1? 'selected':''}}>{{ $kas->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>  
                                        </div>
                                    </div>
                                </div>
                            {{-- end Pencairan --}}
                            {{-- data --}}
                                <div class="tab-pane fade " id="justify-data" role="tabpanel" aria-labelledby="justify-data-tab">
                                    <div class='row'>
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                        <label for="tanggal_lembur">Tanggal Lembur<span style="color:red">*</span></label>
                                                        <div class="input-group mb-0">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                            </div>
                                                            <input type="text" autocomplete="off" name="tanggal_lembur" class="form-control date @error('tanggal_lembur') is-invalid @enderror" id="tanggal_lembur" placeholder="dd-M-yyyy" value="{{old('tanggal_lembur',date("d-M-Y", strtotime($dataLemburMekanik->tanggal_lembur)))}}" disabled>
                                                            @error('tanggal_lembur')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                        <label for="jam_mulai">Jam Mulai<span style="color:red">*</span></label>
                                                        <input class="form-control" name="jam_mulai" type="time" id="jam_mulai" step="3600" value="{{old('jam_mulai',$dataLemburMekanik->jam_mulai_lembur)}}"readonly>
                                                        {{-- <select class="form-control select2" name="jam_mulai" data-live-search="true" data-show-subtext="true">
                                                            <option value="">--Jam Mulai--</option>
                                                            <?php for ($i = 1; $i <= 24; $i++) : ?>
                                                                <option value="{{$i}}">{{str_pad($i, 2, '0', STR_PAD_LEFT)}}:00<nbsp>
                                                                </option>
                                                            <?php endfor; ?>
                                                        </select> --}}
                                                        @error('jam_mulai')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                        <label for="jam_selesai">Jam Selesai<span style="color:red">*</span></label>
                                                        <input class="form-control" name="jam_selesai" type="time" id="jam_selesai" step="3600" value="{{old('jam_mulai',$dataLemburMekanik->jam_akhir_lembur)}}" readonly>
                                                        {{-- <select class="form-control select2" name="jam_selesai" data-live-search="true" data-show-subtext="true">
                                                            <option value="">--Jam Selesai--</option>
                                                            <?php for ($i = 1; $i <= 24; $i++) : ?>
                                                                <option value="{{$i}}">{{str_pad($i, 2, '0', STR_PAD_LEFT)}}:00<nbsp>
                                                                </option>
                                                            <?php endfor; ?>
                                                        </select> --}}
                                                        @error('jam_selesai')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                        <label for="select_mekanik">Mekanik<span style="color:red">*</span></label>
                                                            <select class="form-control select2  @error('select_mekanik') is-invalid @enderror" style="width: 100%;" id='select_mekanik' name="select_mekanik" disabled>
                                                            <option value="">Pilih Mekanik</option>
                                                            @foreach ($dataMekanik as $mekanik)
                                                                <option value="{{$mekanik->id}}" {{$dataLemburMekanik->id_karyawan==$mekanik->id?'selected':''}} nama_driver="{{ $mekanik->nama_panggilan }} - ({{ $mekanik->telp1 }})">{{ $mekanik->nama_panggilan }} - ({{ $mekanik->telp1 }})</option>
                                                            @endforeach
                                                        </select>
                                                        @error('select_mekanik')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror   
                                                        <input type="hidden" id="nama_mekanik" name="nama_mekanik" value="{{$dataLemburMekanik->karyawan->nama_lengkap}}({{$dataLemburMekanik->karyawan->nama_panggilan}})" placeholder="nama_mekanik">
                                                    </div>

                                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                        <label for="total_nominal">Nominal Lembur<span style="color:red">*</span></label>
                                                        <div class="input-group mb-0">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Rp</span>
                                                            </div>
                                                            <input type="text" name="total_nominal" class="form-control numaja uang @error('total_nominal') is-invalid @enderror" id="total_nominal" placeholder="" value="{{old('total_nominal',number_format($dataLemburMekanik->nominal_lembur))}}" readonly>
                                                            @error('total_nominal')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>  
                                                </div>
                                            </div>
                                        </div>
                                    <div class="row">
                                        <div class=" col-lg-12 col-md-12 col-sm-12">
                                            <input type="hidden" id="maxID" value="0">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Kendaraan</th>
                                                        <th>Keterangan</th>
                                                        <th>Foto</th>
                                                        <th>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tabel_kendaraan">
                                                    @php
                                                        $counter = 1;
                                                    @endphp
                                                    @foreach ($dataLemburMekanikKendaraan as $data)
                                                        <tr id="{{$counter}}">
                                                            <td>
                                                                <div class="form-group">
                                                                    <select class="form-control select_kendaraan select2 @error('select_kendaraan') is-invalid @enderror" style="width: 100%;" id='select_kendaraan_{{$counter}}' name="kendaraan[{{$counter}}][select_kendaraan]" disabled>
                                                                        <option value="">Pilih Kendaraan</option>
                                                                        @foreach ($dataKendaraan as $kendaraan)
                                                                            <option value="{{$kendaraan->kendaraanId}}"
                                                                                noPol='{{$kendaraan->no_polisi}}'
                                                                                kategoriKendaraan='{{$kendaraan->kategoriKendaraan}}'
                                                                                tipeKontainerKendaraanDariChassis = '{{$kendaraan->tipeKontainerKendaraanDariChassis}}'
                                                                                id_counter = '{{$counter}}'
                                                                                {{$data->id_kendaraan == $kendaraan->kendaraanId?'selected':''}}
                                                                                >{{ $kendaraan->no_polisi }} ({{$kendaraan->kategoriKendaraan}})</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('select_kendaraan')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                    @error('keterangan')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                    {{-- <input type="text" class="form-control @error('keterangan') is-invalid @enderror"  id="no_polisi_{{$counter}}" readonly name="kendaraan[{{$counter}}][no_polisi]" value="{{$data->no_pol}}" placeholder="no_polisi"> --}}
                                                                </div>  
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control @error('keterangan') is-invalid @enderror" id="keterangan_{{$counter}}" name="kendaraan[{{$counter}}][keterangan]" value="{{old('keterangan',$data->keterangan)}}" readonly>
                                                                    @error('keterangan')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>  
                                                            </td>
                                                            <td>
                                                                <div class=" col-lg-12 col-md-12 col-sm-12">
                                                                    <div class="form-group text-center">
                                                                            <img src="{{ $data->foto_lembur ? asset($data->foto_lembur) : asset('img/gambar_add.png') }}" class="img-fluid preview_foto_lembur" id_preview_lembur="{{$counter}}" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_lembur_{{$counter}}">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            {{-- <td align="center" class="text-danger">
                                                                <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" id_database="{{$data->id}}"  class="btn btn-danger radiusSendiri btnDelete">
                                                                    <i class="fa fa-fw fa-trash-alt"></i>
                                                                </button>
                                                                <input type="hidden" name="kendaraan[{{$counter}}][is_aktif]" value="{{$data->is_aktif}}" id="is_aktif">
                                                                <input type="hidden" name="kendaraan[{{$counter}}][id_database]" value="{{$data->id}}" id="id_database">
                                                            </td> --}}
                                                        </tr>
                                                    @php
                                                        $counter ++;
                                                    @endphp
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            {{-- end data --}}
                        </div>
            </div>
        </div>
    </form>
</div>
<script>
    function cek_max_pencairan(){
        if($('#total_pencairan').val()!=''){
            var total_pencairan =removePeriod($('#total_pencairan').val(),',');
        }else{
            var total_pencairan =0;
        }
        
        var nominal_lembur = removePeriod($('#nominal_lembur').val(),',');
        if(parseFloat(total_pencairan)>parseFloat(nominal_lembur)){
            $('#total_pencairan').val(addPeriodType(nominal_lembur,','));
        }else{
            $('#total_pencairan').val(addPeriodType(total_pencairan,','));
        }
    }

   
    $(document).ready(function() {
          function readURLLembur(input,id_foto_lembur) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                //load ke previewnya
                reader.onload = function(e) {
                    $("#preview_foto_lembur_"+id_foto_lembur).attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }
        $('body').on('change','.foto_lembur',function(){

            var id_inputan_foto =$(this).attr('id_lembur');
            console.log(id_inputan_foto);
            readURLLembur(this,id_inputan_foto); // this itu input filenya dr input type file
        });
    // ==================end untuk preview foto lemburrr=================
    var zoom_in = false;
    function ZoomPreviewFoto(id) {
        if (zoom_in) {
            $('#preview_foto_lembur_' + id).css('transform', 'scale(1)');
            $('#preview_foto_lembur_' + id).css('transform', 'scale(1)');
            $('#preview_foto_lembur_' + id).css('transition', 'transform 0.5s ease');
            $('#preview_foto_lembur_' + id).css('z-index', '100');

            $('#div_foto_lembur_' + id).show();
        } else {
            $('#preview_foto_lembur_' + id).css('transform', 'scale(3.5)');
            $('#preview_foto_lembur_' + id).css('transition', 'transform 0.5s ease');
            $('#preview_foto_lembur_' + id).css('z-index', '100');
            $('#div_foto_lembur_' + id).hide();
        }
        zoom_in = !zoom_in;
    }
    $('body').on('click','.preview_foto_lembur',function(){
        var id =$(this).attr('id_preview_lembur');
        console.log(id);
        ZoomPreviewFoto(id);
    });
        function cek_status(){
            var status = $("input[name='status']:checked").val();
            if(status=="PENDING")
            {
                $('#kas').attr('disabled',true);
                $('#total_pencairan').attr('disabled',true);
                $('#catatan_pencairan').attr('disabled',true);
                // $('#tanggal_pencatatan').attr('disabled',true);
                // $('#tanggal_pencatatan').val('');

                $('#div_alasan_tolak').hide();
                $('#div_catatan_pencairan').show();


            }
            else if(status=="REJECTED")
            {
                $('#kas').attr('disabled',true);
                $('#total_pencairan').attr('disabled',true);
                $('#catatan_pencairan').attr('disabled',true);
                $('#tanggal_pencairan').attr('disabled',true);
                $('#tanggal_pencairan').val('');
                $('#div_alasan_tolak').show();
                $('#div_catatan_pencairan   ').hide();

            }
            else //ACCEPTED
            {
                $('#kas').attr('disabled',false);
                $('#total_pencairan').attr('disabled',false);
                $('#catatan_pencairan').attr('disabled',false);
                $('#tanggal_pencairan').attr('disabled',false);
                $('#div_alasan_tolak').hide();
                $('#div_catatan_pencairan').show();
            }
        }
        cek_status();
        $("input[name='status']").change(function() {
            cek_status();
        });
        $('#tanggal_klaim').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            startDate: "0d"
        });
        $('#tanggal_pencairan').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // startDate: "0d",
            endDate: "0d"
        });
    $('#post').submit(function(event) {

            var status = $("input[name='status']:checked").val();

            var tanggal_pencairan = $("#tanggal_pencairan").val();
            var catatan_pencairan = $("#catatan_pencairan").val();
            // var tanggal_pencatatan = $("#tanggal_pencatatan").val();
            var total_pencairan = $("#total_pencairan").val();
            var kas = $("#kas").val();
            var alasan_tolak = $("#alasan_tolak").val();

            const Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })
            /*if(statusKlaim=="PENDING")
            {
                if(tanggal_pencairan.trim()=='')
                {
                    event.preventDefault(); 
                    Toast.fire({
                        icon: 'error',
                        text: `TANGGAL PENCAIRAN WAJIB DIISI!`,
                    })
                    return;
                }
            }
            else */if(status=="REJECTED")
            {
                // if(tanggal_pencairan.trim()=='')
                // {
                //     event.preventDefault(); 
                //     Toast.fire({
                //         icon: 'error',
                //         text: `TANGGAL PENCAIRAN WAJIB DIISI!`,
                //     })
                //     return;
                // }
                if(alasan_tolak.trim()=='')
                {
                    event.preventDefault(); 
                    Toast.fire({
                        icon: 'error',
                        text: `ALASAN TOLAK WAJIB DIISI!`,
                    })
                    return;
                }

            }
            else if(status=="ACCEPTED") //ACCEPTED
            {
                
                if(tanggal_pencairan.trim()=='')
                {
                    event.preventDefault(); 
                    Toast.fire({
                        icon: 'error',
                        text: `TANGGAL PENCAIRAN WAJIB DIISI!`,
                    })
                    return;
                }
                if(total_pencairan.trim()=='')
                {
                    event.preventDefault(); 
                    Toast.fire({
                        icon: 'error',
                        text: `JUMLAH KLAIM DICAIRKAN WAJIB DIISI!`,
                    })
                    return;
                }
                if(total_pencairan<=0)
                {
                    event.preventDefault(); 
                    Toast.fire({
                        icon: 'error',
                        text: `TOTAL PENCAIRAN HARUS LEBIH BESAR DARI RP.0 !`,
                    })
                    return;
                }
                if(kas=='')
                {
                    event.preventDefault(); 
                    Toast.fire({
                        icon: 'error',
                        text: `KAS BANK WAJIB DIPILIH!`,
                    })
                    return;
                }
                // if(tanggal_pencatatan.trim()=='')
                // {
                //     event.preventDefault(); 
                //     Toast.fire({
                //         icon: 'error',
                //         text: `TANGGAL PENCATATANWAJIB DIISI!`,
                //     })
                //     return;
                // }
                // if(catatan_pencairan.trim()=='')
                // {
                //     event.preventDefault(); 
                //     Toast.fire({
                //         icon: 'error',
                //         text: `CATATAN PENCAIRAN WAJIB DIISI!`,
                //     })
                //     return;
                // }
            }
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin data sudah benar?',
                text: "Periksa kembali data anda",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'success',
                        title: 'Data Disimpan'
                    })

                    setTimeout(() => {
                        this.submit();
                    }, 200); // 2000 milliseconds = 2 seconds
                }else{
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'warning',
                        title: 'Batal Disimpan'
                    })
                    event.preventDefault();
                }
            })
        });
    });
</script>
@endsection
