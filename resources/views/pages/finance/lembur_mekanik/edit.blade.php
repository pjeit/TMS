
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
    <form action="{{ route('lembur_mekanik.update',[$dataLemburMekanik->id]) }}" method="POST" id="post" enctype="multipart/form-data">
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
                                <a class="nav-link nav-link-tab" id="justify-foto-tab" data-toggle="tab" href="#justify-foto" role="tab" aria-controls="justify-foto" aria-selected="false">Foto</a>
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

                                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
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
                            {{-- foto --}}
                                <div class="tab-pane fade" id="justify-foto" role="tabpanel" aria-labelledby="justify-foto-tab">
                                   
                                </div>
                            {{-- end foto --}}
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
    
    $('body').on('input', '#jam_mulai, #jam_selesai', function () {
        var jamMulaiValue = $('#jam_mulai').val();
        var jamSelesaiValue = $('#jam_selesai').val();

        if (jamMulaiValue && jamSelesaiValue) {

            var jamMulai = new Date();
            var jamSelesai = new Date();

            jamMulai.setHours(parseInt(jamMulaiValue.split(':')[0], 10), parseInt(jamMulaiValue.split(':')[1], 10), 0, 0);
            jamSelesai.setHours(parseInt(jamSelesaiValue.split(':')[0], 10), parseInt(jamSelesaiValue.split(':')[1], 10), 0, 0);

            if(jamMulai>jamSelesai)
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `JAM MULAI TIDAK BOLEH LEBIH BESAR DARI JAM SELESAI!`,
                })
                $('#jam_mulai').val('');
                $('#jam_selesai').val('');
                return;
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
    $('body').on('change','#select_mekanik',function()
    {
        var selectedOption = $(this).find('option:selected');
        var nama_driver = selectedOption.attr('nama_driver');
        
        $('#driver_nama').val(nama_driver);

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
            if($("#select_kendaraan").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `KENDARAAN BELUM DIPILIH!`,
                })
                
                return;
            }
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
            if($("#keterangan").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `KETERANGAN KLAIM BELUM DIISI!`,
                })
                
                return;
            }
            if( $('#foto_lembur')[0].files.length === 0)
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `FOTO BUKTI LEMBUR TIDAK BOLEH KOSONG!`,
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
