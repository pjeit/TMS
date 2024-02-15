
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
    <form action="{{ route('lembur_mekanik.update',[$dataLemburMekanik->id]) }}" method="POST" id="post_data" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('lembur_mekanik.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body" >
                <ul class="nav nav-tabs mb-3 mt-3 nav-fill" id="justifyTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link nav-link-tab active" id="justify-data-tab" data-toggle="tab" href="#justify-data" role="tab" aria-controls="justify-data" aria-selected="true">Data</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-tab" id="justify-kendaraan-tab" data-toggle="tab" href="#justify-kendaraan" role="tab" aria-controls="justify-kendaraan" aria-selected="false">Kendaraan</a>
                            </li>
                        </ul>

                        <div class="tab-content">
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
                            {{-- data --}}

                                <div class="tab-pane fade show active" id="justify-data" role="tabpanel" aria-labelledby="justify-data-tab">
                                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}
                                        <div class='row'>
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                        <label for="tanggal_lembur">Tanggal Lembur<span style="color:red">*</span></label>
                                                        <div class="input-group mb-0">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                            </div>
                                                            <input type="text" autocomplete="off" name="tanggal_lembur" class="form-control date @error('tanggal_lembur') is-invalid @enderror" id="tanggal_lembur" placeholder="dd-M-yyyy" value="{{old('tanggal_lembur',date("d-M-Y", strtotime($dataLemburMekanik->tanggal_lembur)))}}">
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
                                                        <input class="form-control" name="jam_mulai" type="time" id="jam_mulai" step="3600" value="{{old('jam_mulai',$dataLemburMekanik->jam_mulai_lembur)}}">
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
                                                        <input class="form-control" name="jam_selesai" type="time" id="jam_selesai" step="3600" value="{{old('jam_mulai',$dataLemburMekanik->jam_akhir_lembur)}}">
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
                                                            <select class="form-control select2  @error('select_mekanik') is-invalid @enderror" style="width: 100%;" id='select_mekanik' name="select_mekanik">
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
                                                        <input type="hidden" id="driver_nama" name="driver_nama" value="" placeholder="driver_nama">
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
                                </div>
                            {{-- end data --}}
                            {{-- kendaraan --}}
                                <div class="tab-pane fade" id="justify-kendaraan" role="tabpanel" aria-labelledby="justify-kendaraan-tab">

                                    <button class="btn btn-primary radiusSendiri mt-2 mb-2" type="button" id="btn_tambah">Tambah Kendaraan</button>
                                    <table class="table table-bordered" id="tabel_kendaraan_parent">
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
                                                $counter = 0;
                                            @endphp
                                            @foreach ($dataLemburMekanikKendaraan as $data)
                                                @php
                                                    $counter ++;
                                                @endphp
                                                <tr id="{{$counter}}" id_database="{{$data->id}}">
                                                    <td>
                                                        <div class="form-group">
                                                            <select class="form-control select_kendaraan select2 @error('select_kendaraan') is-invalid @enderror" style="width: 100%;" id='select_kendaraan_{{$counter}}' name="kendaraan[{{$counter}}][select_kendaraan]">
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
                                                            <input type="hidden" id="no_polisi_{{$counter}}" name="kendaraan[{{$counter}}][no_polisi]" value="{{$data->kendaraan->no_polisi}}" placeholder="no_polisi">
                                                        </div>  
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control keterangan @error('keterangan') is-invalid @enderror" id="keterangan_{{$counter}}" name="kendaraan[{{$counter}}][keterangan]" value="{{old('keterangan',$data->keterangan)}}">
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
                                                                <a href="#" class="pop">
                                                                    <img src="{{ $data->foto_lembur ? asset($data->foto_lembur) : asset('img/gambar_add.png') }}" class="img-fluid preview_foto_lembur" id_preview_lembur="{{$counter}}" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_lembur_{{$counter}}">
                                                                </a>
                                                            </div>
                                                            <div class="custom-file text-center" id="div_foto_lembur_{{$counter}}">
                                                                <input type="file" class="custom-file-input form-control foto_lembur @error('foto_lembur') is-invalid @enderror" id_lembur="{{$counter}}" id="foto_lembur_{{$counter}}" name="kendaraan[{{$counter}}][foto_lembur]" accept="image/jpeg" value="" >
                                                                <label class="btn btn-outline-primary radiusSendiri" for="foto_lembur_{{$counter}}" style="text-align: center">Pilih Foto Lembur</label>
                                                                @error('foto_lembur')
                                                                    <div class="invalid-feedback">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror   
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td align="center" class="text-danger">
                                                        <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" id_database="{{$data->id}}"  class="btn btn-danger radiusSendiri btnDelete">
                                                            <i class="fa fa-fw fa-trash-alt"></i>
                                                        </button>
                                                        <input type="hidden" name="kendaraan[{{$counter}}][is_aktif]" value="{{$data->is_aktif}}" id="is_aktif_{{$counter}}">
                                                        <input type="hidden" name="kendaraan[{{$counter}}][id_database]" value="{{$data->id}}" id="id_database_{{$counter}}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <input type="hidden" id="maxID" value="{{$counter}}">
                                    </table>
                                </div>
                            {{-- end kendaraan --}}
                        </div>
            </div>
        </div>
    </form>
</div>
<script>
$(document).ready(function () {

    $('#tanggal_lembur').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
        });
    var Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    timer: 2500,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
    //=======LOGIC JAM MULAI, SELESAI=========
    $('body').on('input', '#jam_mulai, #jam_selesai', function () {
        var jamMulaiValue = $('#jam_mulai').val();
        var jamSelesaiValue = $('#jam_selesai').val();

        var set_jam_mulai = jamMulaiValue < '17:00'?'17:00':jamMulaiValue;

        $('#jam_mulai').val(set_jam_mulai);

        
        if (jamMulaiValue && jamSelesaiValue) {

            var jamMulai = new Date();
            var jamSelesai = new Date();

            jamMulai.setHours(parseInt(jamMulaiValue.split(':')[0], 10), parseInt(jamMulaiValue.split(':')[1], 10), 0, 0);
            jamSelesai.setHours(parseInt(jamSelesaiValue.split(':')[0], 10), parseInt(jamSelesaiValue.split(':')[1], 10), 0, 0);
            // var set_jam_selesai = jamSelesaiValue < '17:00'?'17:00':jamSelesaiValue;
            // $('#jam_selesai').val(set_jam_selesai);


            // if(jamMulai>jamSelesai)
            // {
                
            //     event.preventDefault(); 
            //     Toast.fire({
            //         icon: 'error',
            //         text: `JAM MULAI TIDAK BOLEH LEBIH BESAR DARI JAM SELESAI!`,
            //     })
            //     $('#jam_mulai').val('17:00');
            //     $('#jam_selesai').val('');
            //     return;
            // }
            if(jamSelesai<jamMulai)
            {
                // $('#jam_selesai').val('');
                jamSelesai.setDate(jamSelesai.getDate() + 1);
                $('#jam_selesai').val(`${jamSelesai.getHours().toString().padStart(2, '0')}:${jamSelesai.getMinutes().toString().padStart(2, '0')}`);
            }
            else
            {
                // Calculate the difference in milliseconds
                var differenceInMs = jamSelesai - jamMulai;
                // Convert milliseconds to hours
                var differenceInHours = differenceInMs / (1000 * 60 * 60);
                var nominalLembur = 25000;
                if(differenceInHours >=0 && differenceInHours<=1)
                {
                    $('#total_nominal').val(moneyMask(nominalLembur));
                }
                else if(differenceInHours >1 && differenceInHours<=2)
                {
                    $('#total_nominal').val(moneyMask(nominalLembur*2));
                }
                else if(differenceInHours >2 )
                {
                    $('#total_nominal').val(moneyMask(nominalLembur*3));
                }
                // console.log('Difference in hours:', differenceInHours);
            }
            // Add your logic here with the calculated difference in hours
        }
    });
    //=======END LOGIC JAM MULAI, SELESAI=========
    

    $('body').on('click','#btn_tambah',function()
    {
        var maxID = $('#maxID').val();
        maxID++;
        $('#tabel_kendaraan').append(
            `
                <tr id="${maxID}" id_database="data_baru">
                    <td>
                        <div class="form-group">
                            <select class="form-control select_kendaraan select2 @error('select_kendaraan') is-invalid @enderror" style="width: 100%;" id='select_kendaraan_${maxID}' name="kendaraan[${maxID}][select_kendaraan]">
                                <option value="">Pilih Kendaraan</option>
                                @foreach ($dataKendaraan as $kendaraan)
                                    <option value="{{$kendaraan->kendaraanId}}"
                                        noPol='{{$kendaraan->no_polisi}}'
                                        kategoriKendaraan='{{$kendaraan->kategoriKendaraan}}'
                                        tipeKontainerKendaraanDariChassis = '{{$kendaraan->tipeKontainerKendaraanDariChassis}}'
                                        id_counter = '${maxID}'
                                        >{{ $kendaraan->no_polisi }} ({{$kendaraan->kategoriKendaraan}})</option>
                                @endforeach
                            </select>
                            @error('select_kendaraan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <input type="hidden" id="no_polisi_${maxID}" name="kendaraan[${maxID}][no_polisi]" value="" placeholder="no_polisi">
                        </div>  
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control keterangan @error('keterangan') is-invalid @enderror" id="keterangan_${maxID}" name="kendaraan[${maxID}][keterangan]" value="{{old('keterangan')}}">
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
                                <a href="#" class="pop">
                                    <img src="{{asset('img/gambar_add.png')}}" class="img-fluid preview_foto_lembur" id_preview_lembur="${maxID}" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_lembur_${maxID}">
                                </a>
                            </div>
                            <div class="custom-file text-center" id="div_foto_lembur_${maxID}">
                                <input type="file" class="custom-file-input form-control foto_lembur @error('foto_lembur') is-invalid @enderror" id_lembur="${maxID}" id="foto_lembur_${maxID}" name="kendaraan[${maxID}][foto_lembur]" accept="image/jpeg" value="" >
                                <label class="btn btn-outline-primary radiusSendiri" for="foto_lembur_${maxID}" style="text-align: center">Pilih Foto Lembur</label>
                                @error('foto_lembur')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror   
                            </div>
                        </div>
                    </td>
                    <td align="center" class="text-danger">
                        <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" id_database="data_baru" class="btn btn-danger radiusSendiri btnDelete">
                            <i class="fa fa-fw fa-trash-alt"></i>
                        </button>
                        <input type="hidden" name="kendaraan[${maxID}][is_aktif]" value="Y" id="is_aktif_${maxID}">
                        <input type="hidden" name="kendaraan[${maxID}][id_database]" value="data_baru" id="id_database_${maxID}">
                    </td>
                </tr>
            `
        )
        $('.select2').select2();
        $('#maxID').val(maxID);
    });
    $(document).on('click','.btnDelete',function(){
        var maxID = $('#maxID').val();
        if ($(this).closest('tr').attr('id_database')!="data_baru") {
            $(this).closest('tr').hide();
            $('#is_aktif_'+$(this).closest('tr').attr('id')).val("N");
        } else {
            $(this).closest('tr').remove();
            if($(this).closest('tr').attr('id') == maxID)
            {
                maxID--;
            }
            $('#maxID').val(maxID);
        }
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
        Toast.fire({
            icon: 'success',
            title: 'Data dihapus'
        })
        // cekCheckbox();
    });

    // ==================ini untuk preview foto lemburrr=================
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
    $('body').on('change','.select_kendaraan',function()
    {
        var idKendaraan = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var nopol = selectedOption.attr('noPol');
        var id_counter = selectedOption.attr('id_counter');
        $('#no_polisi_'+id_counter).val(nopol);

    });
    $('body').on('change','#select_mekanik',function()
    {
        var selectedOption = $(this).find('option:selected');
        var nama_driver = selectedOption.attr('nama_driver');

    });
    
    new DataTable('#TabelLembur', {
        order: [
            [0, 'asc'],
        ],
        rowGroup: {
            dataSrc: [0]
        },
        columnDefs: [
            {
                targets: [0],
                visible: false
            },
            {
                "orderable": false,
                "targets": [0,1,2,3,4,5,6]
            }
    
        ],
    }); 
    $('#post_data').submit(function(event) {
        
            
             if($("#tanggal_lembur").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `TANGGAL LEMBUR BELUM DIISI!`,
                })
                
                return;
            }
            if($("#jam_mulai").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `JAM MULAI BELUM DIPILIH!`,
                })
                
                return;
            }
            if($("#jam_selesai").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `JAM SELESAI BELUM DIPILIH!`,
                })
                
                return;
            }
            // if($("#select_kendaraan").val()=='')
            // {
            //     event.preventDefault(); 
            //     Toast.fire({
            //         icon: 'error',
            //         text: `KENDARAAN BELUM DIPILIH!`,
            //     })
                
            //     return;
            // }
            if($("#select_mekanik").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `MEKANIK BELUM DIPILIH!`,
                })
                
                return;
            }
            
            if($("#total_nominal").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `TOTAL NOMINAL BELUM DIISI (harap pilih jam milai-selesai)!`,
                })
                
                return;
            }
            
            let barisTabel = $("#tabel_kendaraan_parent > tbody tr");
            console.log(barisTabel.length + 'baris tabel');
            if (barisTabel.length == 0) {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `Detail kendaraan Tidak boleh Kosong!`,
                })
                return;
                
            }
            var flagError = false;
            for (var i = 0; i < $(".foto_lembur").length; i++) {
                var indexFoto = $(".foto_lembur").eq(i);
                var row = indexFoto.closest('tr');
                var select_kendaraan=row.find('.select_kendaraan').val();
                // var foto_lembur=row.find('.foto_lembur').val();
                var keterangan=row.find('.keterangan').val();

                
                if(select_kendaraan=="" ||keterangan=="")
                {
                    flagError = true;
                    break; 
                }

            }
            if (flagError) {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `detail kendaraan harus diisi`,
                })
                return;
                
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
                    }, 20); // 2000 milliseconds = 2 seconds
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
