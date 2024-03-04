
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
#preview_foto_klaim {
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
    <form action="{{ route('klaim_operasional.update',[$klaimOperasional->id]) }}" method="post" id="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('klaim_supir.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body" >
                <ul class="nav nav-tabs mb-3 mt-3 nav-fill" id="justifyTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link nav-link-tab active" id="justify-data-tab" data-toggle="tab" href="#justify-data" role="tab" aria-controls="justify-data" aria-selected="true">Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-tab" id="justify-foto-tab" data-toggle="tab" href="#justify-foto" role="tab" aria-controls="justify-foto" aria-selected="false">Foto</a>
                    </li>
                </ul>
                <div class="tab-content">
                    {{-- data --}}
                            <div class="tab-pane fade show active" id="justify-data" role="tabpanel" aria-labelledby="justify-data-tab">
                                <div class='row'>
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                <label for="tanggal_klaim">Tanggal Klaim<span style="color:red">*</span></label>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                    </div>
                                                    <input type="text" autocomplete="off" name="tanggal_klaim" class="form-control date @error('tanggal_klaim') is-invalid @enderror" id="tanggal_klaim" placeholder="dd-M-yyyy" value="{{old('tanggal_klaim',date('d-M-Y',strtotime($klaimOperasional->tanggal_klaim)))}}">
                                                    @error('tanggal_klaim')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                <label for="">Jenis Klaim<span class="text-red">*</span></label>
                                                <select class="form-control select2  @error('select_klaim') is-invalid @enderror" style="width: 100%;" id='select_klaim' name="select_klaim" disabled>
                                                    <option value="" >Pilih Jenis Klaim</option>
                                                    <option value="BURUH" {{$klaimOperasional->jenis_klaim=='BURUH'?'selected':''}}>BURUH</option>
                                                    <option value="TIMBANG" {{$klaimOperasional->jenis_klaim=='TIMBANG'?'selected':''}}>TIMBANG</option>
                                                </select>
                                                @error('select_klaim')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror  
                                            </div> 
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label for="select_driver">Driver<span style="color:red">*</span></label>
                                                    <select class="form-control select2  @error('select_driver') is-invalid @enderror" style="width: 100%;" id='select_driver' name="select_driver" disabled>
                                                    <option value="">Pilih Driver</option>
                                                    @foreach ($dataDriver as $drvr)
                                                        <option value="{{$drvr->id}}" {{$klaimOperasional->id_karyawan==$drvr->id?'selected':''}} nama_driver="{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})">{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})</option>
                                                    @endforeach
                                                </select>
                                                @error('select_driver')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror   
                                                <input type="hidden" id="driver_nama" name="driver_nama" value="" placeholder="driver_nama">
                                                <input type="hidden" id="id_driver_hidden_defaulth"  value="{{$klaimOperasional->id_karyawan}}" placeholder="id_driver_hidden_defaulth">
                                                <input type="hidden" id="jenis_klaim_defaulth"  value="{{$klaimOperasional->jenis_klaim}}" placeholder="jenis_klaim_defaulth">
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label for="select_kendaraan">Kendaraan<span style="color:red">*</span></label>
                                                <select class="form-control select2 @error('select_kendaraan') is-invalid @enderror" style="width: 100%;" id='select_kendaraan' name="select_kendaraan" disabled>
                                                    <option value="">Pilih Kendaraan</option>

                                                    @foreach ($dataKendaraan as $kendaraan)
                                                    
                                                        <option value="{{$kendaraan->kendaraanId}}"
                                                            idChassis='{{$kendaraan->chassisId}}'
                                                            noPol='{{$kendaraan->no_polisi}}'
                                                            idDriver='{{$kendaraan->driver_id}}'
                                                            kategoriKendaraan='{{$kendaraan->kategoriKendaraan}}'
                                                            tipeKontainerKendaraanDariChassis = '{{$kendaraan->tipeKontainerKendaraanDariChassis}}'
                                                            {{$klaimOperasional->id_kendaraan== $kendaraan->kendaraanId?'selected':''}}
                                                            >{{ $kendaraan->no_polisi }} ({{$kendaraan->kategoriKendaraan}})</option>
                                                    @endforeach
                                                </select>
                                                @error('select_kendaraan')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                <label for="select_sewa">Order Supir<span style="color:red">*</span></label>
                                                    <select class="form-control select2  @error('select_sewa') is-invalid @enderror" style="width: 100%;" id='select_sewa' name="select_sewa" disabled>
                                                    <option value="">{{$klaimOperasional->sewa_klaim_ops->getCustomer->nama}} ({{$klaimOperasional->sewa_klaim_ops->nama_tujuan}})</option>
                                                </select>
                                                @error('select_sewa')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror   
                                                <input type="hidden" id="id_sewa_hidden" name="id_sewa_hidden" value="{{$klaimOperasional->id_sewa}}" placeholder="id_sewa_hidden">
                                            </div>
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                <label for="total_reimburse">Total Klaim <span style="color:red">*</span></label>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" name="total_klaim" class="form-control numaja uang @error('total_klaim') is-invalid @enderror" id="total_klaim" placeholder="" value="{{old('total_klaim',number_format($klaimOperasional->total_klaim))}}">
                                                    @error('total_klaim')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>  
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                <label for="keterangan_klaim">Keterangan Klaim</label>
                                                <input type="text" class="form-control @error('keterangan_klaim') is-invalid @enderror" id="keterangan_klaim" name="keterangan_klaim" value="{{old('keterangan_klaim',$klaimOperasional->keterangan_klaim)}}">
                                                @error('keterangan_klaim')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                    {{-- end data --}}
                    {{-- foto --}}
                        <div class="tab-pane fade" id="justify-foto" role="tabpanel" aria-labelledby="justify-foto-tab">
                            <div class="row">
                                <div class=" col-lg-12 col-md-12 col-sm-12">
                                    <div class="form-group text-center">
                                        <a href="#" class="pop">
                                            <img src="{{ $klaimOperasional->foto_klaim ? asset($klaimOperasional->foto_klaim) : asset('img/gambar_add.png') }}" class="img-fluid" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_klaim">
                                        </a>
                                    </div>
                                    <div class="custom-file text-center" name="div_foto_klaim" id="div_foto_klaim">
                                        <input type="file" class="custom-file-input form-control @error('foto_klaim') is-invalid @enderror" id="foto_klaim" name="foto_klaim" accept="image/jpeg" value="" hidden="">
                                        <label class="btn btn-outline-primary radiusSendiri" for="foto_klaim" style="text-align: center">Pilih Foto Klaim</label>
                                        @error('foto_klaim')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror   
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{-- end foto --}}
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        $('#tanggal_klaim').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
        });
    function readURLNota(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#preview_foto_klaim').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }
   
    $("#foto_klaim").change(function() {
        readURLNota(this);
    });
    
    let isScaled2 = false; // Track the current state
   
     $('body').on('click','#preview_foto_klaim',function()
    {
         if (isScaled2) {
                // If the element is already scaled, reset it to normal size
                $(this).css('transform', 'scale(1)');
                $('#div_foto_klaim').show();

            } else {
                // If the element is not scaled, apply the scaling effect
                $(this).css('transform', 'scale(3.5)');
                 $('#div_foto_klaim').hide();

            }

            // Toggle the state
            isScaled2 = !isScaled2;

    });
    $('body').on('change','#select_kendaraan',function()
    {
        var idKendaraan = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var idChassis = selectedOption.attr('idChassis');
        var nopol = selectedOption.attr('noPol');
        var supir = selectedOption.attr('idDriver');
        
        $('#kendaraan_id').val(idKendaraan);
        $('#no_polisi').val(nopol);
        
        $('#select_chassis').val(idChassis).trigger('change');
        // $('#select_driver').val(supir).trigger('change');

    });
    $('body').on('change','#select_driver',function()
    {
        var idDriver = $(this).val();
        var jenis_klaim = $('#select_klaim').val();

        var selectedOption = $(this).find('option:selected');
        var nama_driver = selectedOption.attr('nama_driver');
        
        $('#select_kendaraan').val(idDriver).trigger('change');
        $('#driver_nama').val(nama_driver);
        get_order(idDriver,'edit',jenis_klaim);

    });
    $('body').on('change','#select_klaim',function()
    {
        var jenis_klaim = $(this).val();

        if(jenis_klaim=="")
        {
            $('#select_driver').val('').trigger('change');
            $('#select_driver').attr('disabled',true); 
            $('#select_kendaraan').val('').trigger('change');
            $('#select_kendaraan').attr('disabled',true); 

            $('#select_sewa').empty(); 
            $('#select_sewa').append('<option value="">Tidak ada order supir</option>');
            $('#select_sewa').attr('disabled',true); 
        }
        else
        {
            $('#select_driver').val('').trigger('change');
            $('#select_kendaraan').val('').trigger('change');
            $('#select_driver').attr('disabled',false); 
            $('#select_kendaraan').attr('disabled',false); 

            $('#select_sewa').empty(); 
            $('#select_sewa').append('<option value="">Tidak ada order supir</option>');
            $('#select_sewa').attr('disabled',true); 
        }

    });
    $('body').on('change','#select_sewa',function()
    {
        var id_sewa = $(this).val();
        // var selectedOption = $(this).find('option:selected');
        // var nama_driver = selectedOption.attr('nama_driver');
        $('#id_sewa_hidden').val(id_sewa).trigger('change');
    });
    // get_order($('#id_driver_hidden_defaulth').val(),'edit',$('#jenis_klaim_defaulth').val());
    // function get_order(id_supir,tipe,jenis_klaim)
    // {
    //     var baseUrl = "{{ asset('') }}";
    //     var select_sewa = $('#select_sewa');
    //     // hitungTarif();
    //     $.ajax({
    //         url: `${baseUrl}sewa_by_supir/${id_supir}/${tipe}/${jenis_klaim}`, 
    //         method: 'GET', 
    //         success: function(response) {
    //             console.log(response);

    //             if(response.data_sewa.length>0)
    //             {
    //                 select_sewa.empty(); 
    //                 $('#id_sewa_hidden').val('');
    //                 select_sewa.attr('disabled',false); 
    //                 select_sewa.append('<option value="">Pilih Order Supir</option>');
    //                 if(id_supir!="")
    //                 {
    //                     response.data_sewa.forEach(sewa => {
    //                         var option = document.createElement('option');
    //                         option.value = sewa.id_sewa;
    //                         option.textContent = sewa.nama_customer+ ` ( ${sewa.nama_tujuan} )` ;
    //                         // if(idTujuan!=''|| idTujuan!='[]'|| idTujuan!=null)
    //                         // {
    //                             // if (id_supir == sewa.id_supir_kueri) {
    //                             //     option.selected = true;
    //                             //     $('#id_sewa_hidden').val(sewa.id_sewa);
    //                             // }

    //                         // }
    //                         // if (tujuan.jenis_tujuan == "LTL"||tujuan.jenis_tujuan=='') {
    //                         //     // option.dis('disabled', true);
    //                         //     option.disabled = true;
    //                         // }
    //                         select_sewa.append(option);
    //                     });
    //                 }

    //             }
    //             else
    //             {
    //                     select_sewa.empty(); 
    //                     select_sewa.append('<option value="">Tidak ada order supir</option>');
    //                     select_sewa.attr('disabled',true); 
    //                     $('#id_sewa_hidden').val('');
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             console.error('Error:', error);
    //         }
    //     });
    // }
    
    $('#post').submit(function(event) {
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
                    });
            if($("#tanggal_klaim").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `TANGGAL KLAIM BELUM DIISI!`,
                })
                
                return;
            }
            if($("#select_kendaraan").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `KENDARAAN BELUM DIPILIH!`,
                })
                
                return;
            }
            if($("#select_driver").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `DRVER BELUM DIPILIH!`,
                })
                
                return;
            }
            if($("#select_klaim").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `JENIS KLAIM BELUM DIPILIH!`,
                })
                
                return;
            }
            if($("#total_klaim").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `TOTAL KLAIM BELUM DIISI!`,
                })
                
                return;
            }
            if($("#keterangan_klaim").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `KETERANGAN KLAIM BELUM DIISI!`,
                })
                
                return;
            }
            var preview_foto_klaim = $('#preview_foto_klaim').attr('src');

            console.log("{{asset('img/gambar_add.png')}}");
            if(preview_foto_klaim == "{{asset('img/gambar_add.png')}}")
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `FOTO NOTA TIDAK BOLEH KOSONG!`,
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
