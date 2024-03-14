
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
#preview_foto_nota {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}

#preview_foto_barang {
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
    <form action="{{ route('pembayaran_dokumen_kendaraan.update',[$pembayaran_dokumen_kendaraan->id]) }}" method="POST" id="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('pembayaran_dokumen_kendaraan.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body" >
                <div class='row'>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="tanggal_pembayaran">Tanggal Pembayaran<span style="color:red">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" autocomplete="off" name="tanggal_pembayaran" class="form-control date @error('tanggal_pembayaran') is-invalid @enderror" id="tanggal_pembayaran" placeholder="dd-M-yyyy" value="{{old('tanggal_pembayaran',date('d-M-Y'))}}">
                                    @error('tanggal_pembayaran')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="total_nominal">Total Nominal Pembayaran<span style="color:red">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="total_nominal" class="form-control numaja uang @error('total_nominal') is-invalid @enderror" id="total_nominal" placeholder="" value="{{number_format($pembayaran_dokumen_kendaraan->nominal_bayar)}}" readonly>
                                    @error('total_nominal')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Jenis Dokumen<span class="text-red">*</span></label>
                                <select class="form-control select2  @error('select_dokumen') is-invalid @enderror" style="width: 100%;" id='select_dokumen' name="select_dokumen">
                                    <option value="STNK" {{$pembayaran_dokumen_kendaraan->jenis_dokumen=='STNK'?'selected':''}}>STNK</option>
                                    <option value="KIR" {{$pembayaran_dokumen_kendaraan->jenis_dokumen=='KIR'?'selected':''}}>KIR</option>
                                    <option value="PAJAK" {{$pembayaran_dokumen_kendaraan->jenis_dokumen=='PAJAK'?'selected':''}}>PAJAK</option>
                                </select>
                                @error('select_dokumen')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror  
                            </div> 
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="pembayaran">Pilih Kas/Bank</label>
                                <select class="form-control select2" name="pembayaran" id="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    @foreach ($dataKas as $kb)
                                        <option value="{{$kb->id}}"  >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Catatan Pembayaran</label>
                                <input type="text" class="form-control catatan_pembayaran @error('catatan_pembayaran') is-invalid @enderror" id="catatan_pembayaran" name="catatan_pembayaran" value="{{$pembayaran_dokumen_kendaraan->catatan}}">
                                @error('catatan_pembayaran')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>  
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <input type="hidden" id="maxID" value="{{$count_detail}}">
                        <button class="btn btn-primary radiusSendiri mt-2 mb-2" type="button" id="btn_tambah">
                            <i class="fa fa-plus-circle"> </i> Tambah Kendaraan
                        </button>
                        <table class="table table-bordered" id="tabel_kendaraan_parent">
                            <thead>
                                <tr>
                                    <th>Kendaraan</th>
                                    <th>Nominal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="tabel_kendaraan">
                               @if (isset($pembayaran_dokumen_kendaraan->pembayaran_dokumen_detail))
                                    @foreach ( $pembayaran_dokumen_kendaraan->pembayaran_dokumen_detail as $key => $item)
                                            <tr id="{{$key}}">
                                                <td>
                                                    <div class="form-group">
                                                        <select class="form-control select_kendaraan select2 @error('select_kendaraan') is-invalid @enderror" style="width: 100%;" id='select_kendaraan_{{$key}}' name="kendaraan[{{$key}}][select_kendaraan]">
                                                            <option value="">Pilih Kendaraan</option>
                                                            @foreach ($dataKendaraan as $kendaraan)
                                                                <option value="{{$kendaraan->kendaraanId}}"
                                                                    noPol='{{$kendaraan->no_polisi}}'
                                                                    kategoriKendaraan='{{$kendaraan->kategoriKendaraan}}'
                                                                    tipeKontainerKendaraanDariChassis = '{{$kendaraan->tipeKontainerKendaraanDariChassis}}'
                                                                    id_counter = '{{$key}}'
                                                                    {{$kendaraan->kendaraanId==$item->id_kendaraan?'selected':''}}
                                                                    >{{ $kendaraan->no_polisi }} ({{$kendaraan->kategoriKendaraan}})</option>
                                                            @endforeach
                                                        </select>
                                                        @error('select_kendaraan')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                        <input type="hidden" id="id_detail_{{$key}}" name="kendaraan[{{$key}}][id_detail]" value="{{$item->id}}" placeholder="id_detail">
                                                        <input type="hidden" id="is_aktif_{{$key}}" name="kendaraan[{{$key}}][is_aktif]" value="{{$item->is_aktif}}" placeholder="is_aktif">
                                                        <input type="hidden" id="no_polisi_{{$key}}" name="kendaraan[{{$key}}][no_polisi]" value="{{$item->no_pol}}" placeholder="no_polisi">
                                                    </div>  
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control nominal numaja uang @error('nominal') is-invalid @enderror" id="nominal_{{$key}}" name="kendaraan[{{$key}}][nominal]" value="{{number_format($item->nominal)}}">
                                                        @error('nominal')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>  
                                                </td>
                                                <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove"  class="btn btn-danger radiusSendiri btnDelete"><i class="fa fa-fw fa-trash-alt"></i></button></td>
                                            </tr>
                                    @endforeach
                               @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        $('#tanggal_pembayaran').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            startDate: "0d"
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
    
   

    $('body').on('click','#btn_tambah',function()
    {
        var maxID = $('#maxID').val();
        $('#tabel_kendaraan').append(
            `
                <tr id="${maxID}">
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
                            <input type="hidden" id="is_aktif_${maxID}" name="kendaraan[${maxID}][is_aktif]" value="Y" placeholder="is_aktif">
                            <input type="hidden" id="id_detail_${maxID}" name="kendaraan[${maxID}][id_detail]" value="baru" placeholder="id_detail">
                            <input type="hidden" id="no_polisi_${maxID}" name="kendaraan[${maxID}][no_polisi]" value="" placeholder="no_polisi">
                        </div>  
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control nominal numaja uang @error('nominal') is-invalid @enderror" id="nominal_${maxID}" name="kendaraan[${maxID}][nominal]" value="{{old('nominal')}}">
                            @error('nominal')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>  
                    </td>
                    <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove"  class="btn btn-danger radiusSendiri btnDelete"><i class="fa fa-fw fa-trash-alt"></i></button></td>
                </tr>
            `
        )
        $('.select2').select2();
        maxID++;
        $('#maxID').val(maxID);
    });
    $(document).on('click','.btnDelete',function(){
        var maxID = $('#maxID').val();
        var id = $(this).closest('tr').attr('id');
        if($('#id_detail_'+id).val()!='baru')
        {
            $(this).closest('tr').hide();
            $('#nominal_'+id).val(0);
            $('#is_aktif_'+id).val('N');
            
        }
        else
        {
             $(this).closest('tr').remove();
        }
        if($(this).closest('tr').attr('id') == maxID)
        {
            maxID--;
        }
        hitungTotal();
        $('#maxID').val(maxID);
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
    $( document ).on( 'keyup', '.nominal', function (event) {
                hitungTotal();
    });
    function hitungTotal(){
     
        var nominal = 0;
        $('.nominal').each(function () {
            var value_nominal = $(this).val() ? parseFloat($(this).val().replace(/,/g, '')) : 0;
            nominal += value_nominal;
        });
        var total = parseFloat(nominal);
        $('#total_nominal').val(total.toLocaleString());
    }
   
    $('#post').submit(function(event) {
            
        if($("#tanggal_pembayaran").val()=='')
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `TANGGAL PEMBAYARAN BELUM DIISI!`,
            })
            return;
        }
        
        if($("#total_nominal").val()=='' || normalize($("#total_nominal").val())==0)
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `Total pembayaran harus diisi`,
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
        for (var i = 0; i < $(".catatan").length; i++) {
            var indexFoto = $(".catatan").eq(i);
            var row = indexFoto.closest('tr');
            var select_kendaraan=row.find('.select_kendaraan').val();

            
            if(select_kendaraan=="")
            {
                flagError = true;
                break; 
            }

        }
        if (flagError) {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `Kendaraan tidak boleh kosong`,
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
