
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
    <form action="{{ route('klaim_supir.update',[$klaimSupir->id]) }}" method="POST" id="post" enctype="multipart/form-data">
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
                                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}
                                        <div class='row'>
                                            <div class="col-lg-12">
                                                <div class="row">
                                        
                                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                        <label for="tanggal_klaim">Tanggal Klaim<span style="color:red">*</span></label>
                                                        <div class="input-group mb-0">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                            </div>
                                                            <input type="text" autocomplete="off" name="tanggal_klaim" class="form-control date @error('tanggal_klaim') is-invalid @enderror" id="tanggal_klaim" placeholder="dd-M-yyyy" value="{{old('tanggal_klaim',\Carbon\Carbon::parse($klaimSupir->tanggal_klaim)->format('d-M-Y'))}}">
                                                            @error('tanggal_klaim')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>


                                                    
                                                </div>
                                                
                                                <div class="row">

                                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                        <label for="select_kendaraan">Kendaraan<span style="color:red">*</span></label>
                                                        <select class="form-control select2 @error('select_kendaraan') is-invalid @enderror" style="width: 100%;" id='select_kendaraan' name="select_kendaraan">
                                                            <option value="">Pilih Kendaraan</option>

                                                            @foreach ($dataKendaraan as $kendaraan)
                                                            
                                                                <option value="{{$kendaraan->kendaraanId}}"
                                                                    idChassis='{{$kendaraan->chassisId}}'
                                                                    noPol='{{$kendaraan->no_polisi}}'
                                                                    idDriver='{{$kendaraan->driver_id}}'
                                                                    kategoriKendaraan='{{$kendaraan->kategoriKendaraan}}'
                                                                    tipeKontainerKendaraanDariChassis = '{{$kendaraan->tipeKontainerKendaraanDariChassis}}'
                                                                    {{$klaimSupir->kendaraan_id==$kendaraan->kendaraanId?'selected':''}}
                                                                    >{{ $kendaraan->no_polisi }} ({{$kendaraan->kategoriKendaraan}})</option>
                                                            @endforeach
                                                        </select>
                                                        @error('select_kendaraan')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                        <input type="hidden" id="kendaraan_id" name="kendaraan_id" value="" placeholder="kendaraan_id">
                                                        <input type="hidden" id="no_polisi" name="no_polisi" value="" placeholder="no_polisi">
                                                    </div>
                                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                        <label for="select_driver">Driver<span style="color:red">*</span></label>
                                                            <select class="form-control select2  @error('select_driver') is-invalid @enderror" style="width: 100%;" id='select_driver' name="select_driver">
                                                            <option value="">Pilih Driver</option>
                                                            @foreach ($dataDriver as $drvr)
                                                                <option value="{{$drvr->id}}" {{$klaimSupir->karyawan_id==$drvr->id?'selected':''}} nama_driver="{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})">{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})</option>
                                                            @endforeach
                                                        </select>
                                                        @error('select_driver')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror   
                                                        <input type="hidden" id="driver_nama" name="driver_nama" value="" placeholder="driver_nama">
                                                    </div>

                                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                        <label for="">Jenis Klaim<span class="text-red">*</span></label>
                                                        <select class="form-control select2  @error('select_klaim') is-invalid @enderror" style="width: 100%;" id='select_klaim' name="select_klaim">
                                                            <option value="" >Pilih Jenis Klaim</option>
                                                            <option value="Ban" {{$klaimSupir->jenis_klaim=='Ban'?'selected':''}}>Ban</option>
                                                            <option value="CuciMobil" {{$klaimSupir->jenis_klaim=='CuciMobil'?'selected':''}}>Cuci Mobil</option>
                                                            <option value="Sparepart" {{$klaimSupir->jenis_klaim=='Sparepart'?'selected':''}}>Sparepart</option>
                                                            <option value="Tol" {{$klaimSupir->jenis_klaim=='Tol'?'selected':''}}>Tol</option>
                                                            <option value="Lainlain" {{$klaimSupir->jenis_klaim=='Lainlain'?'selected':''}}>Lain-lain</option>

                                                            {{-- @foreach ($datajO as $jo)
                                                                <option value="{{$jo->id}}-{{$jo->id_customer}}">{{ $jo->no_bl }} / {{ $jo->getCustomer->kode }} / {{ $jo->getSupplier->nama }}</option>
                                                            @endforeach --}}
                                                        </select>
                                                        @error('select_klaim')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror  
                                                    </div> 

                                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                        <label for="total_reimburse">Total Klaim <span style="color:red">*</span></label>
                                                        <div class="input-group mb-0">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Rp</span>
                                                            </div>
                                                            <input type="text" name="total_klaim" class="form-control numaja uang @error('total_klaim') is-invalid @enderror" id="total_klaim" placeholder="" value="{{old('total_klaim',number_format($klaimSupir->total_klaim))}}">
                                                            @error('total_klaim')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>  
                                                    
                                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                        <label for="keterangan_klaim">Keterangan Klaim</label>
                                                        <input type="text" class="form-control @error('keterangan_klaim') is-invalid @enderror" id="keterangan_klaim" name="keterangan_klaim" value="{{old('keterangan_klaim',$klaimSupir->keterangan_klaim)}}">
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
                                        <div class=" col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group text-center">
                                                <a href="#" class="pop">
                                                    <img src="{{ $klaimSupir->foto_nota ? asset($klaimSupir->foto_nota) : asset('img/gambar_add.png') }}" class="img-fluid" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_nota">
                                                </a>
                                            </div>
                                            <div class="custom-file text-center" name="div_foto_nota" id="div_foto_nota">
                                                <input type="file" class="custom-file-input form-control @error('foto_nota') is-invalid @enderror" id="foto_nota" name="foto_nota" accept="image/jpeg" value="" hidden="">
                                                <label class="btn btn-outline-primary radiusSendiri" for="foto_nota" style="text-align: center">Pilih Foto Nota</label>
                                                @error('foto_nota')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror   
                                            </div>
                                        </div>
                                        <div class=" col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group text-center">
                                                <a href="#" class="pop">
                                                    <img src="{{ $klaimSupir->foto_barang ? asset($klaimSupir->foto_barang) : asset('img/gambar_add.png') }}" class="img-fluid" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_barang">
                                                </a>
                                            </div>
                                            <div class="custom-file text-center" name="div_foto_barang" id="div_foto_barang">
                                                <input type="file" class="custom-file-input form-control @error('foto_barang') is-invalid @enderror" id="foto_barang" name="foto_barang" accept="image/jpeg" value="" hidden="">
                                                <label class="btn btn-outline-primary radiusSendiri" for="foto_barang" style="text-align: center">Pilih Foto Barang</label>
                                                @error('foto_barang')
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
            $('#preview_foto_nota').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }
    function readURLBarang(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#preview_foto_barang').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }
    $("#foto_barang").change(function() {
        readURLBarang(this);
    });
    $("#foto_nota").change(function() {
        readURLNota(this);
    });
    $(".bukakModalCreate").click(function () {
            $("#modal").modal("show");
        });
    var cekerror= <?php echo json_encode($errors->any()); ?>;
    
    if (cekerror) {
            $("#modal").modal("show");
        
    }
    let isScaled1 = false; // Track the current state
    let isScaled2 = false; // Track the current state
    // Define a variable to track the current scale factor
    $('body').on('click','#preview_foto_barang',function()
    {
            if (isScaled1) {
                // If the element is already scaled, reset it to normal size
                $(this).css('transform', 'scale(1)');
                 $('#div_foto_barang').show();

            } else {
                // If the element is not scaled, apply the scaling effect
                $(this).css('transform', 'scale(3.5)');
                 $('#div_foto_barang').hide();

            }

            // Toggle the state
            isScaled1 = !isScaled1;

    });
     $('body').on('click','#preview_foto_nota',function()
    {
         if (isScaled2) {
                // If the element is already scaled, reset it to normal size
                $(this).css('transform', 'scale(1)');
                $('#div_foto_nota').show();

            } else {
                // If the element is not scaled, apply the scaling effect
                $(this).css('transform', 'scale(3.5)');
                 $('#div_foto_nota').hide();

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
        $('#select_driver').val(supir).trigger('change');

    });
     $('body').on('change','#select_driver',function()
    {
        var selectedOption = $(this).find('option:selected');
        var nama_driver = selectedOption.attr('nama_driver');
        
        $('#driver_nama').val(nama_driver);

    });
    
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
            var preview_foto_nota = $('#preview_foto_nota').attr('src');
            var preview_foto_barang = $('#preview_foto_barang').attr('src');

            console.log("{{asset('img/gambar_add.png')}}");
            if(preview_foto_nota == "{{asset('img/gambar_add.png')}}")
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `FOTO NOTA TIDAK BOLEH KOSONG!`,
                })
                
                return;
            }
            if( preview_foto_barang == "{{asset('img/gambar_add.png')}}")
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `FOTO BARANG TIDAK BOLEH KOSONG!`,
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
