
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Master</li>
    <li class="breadcrumb-item"><a href="{{route('kas_bank.index')}}">Kas Bank</a></li>
    <li class="breadcrumb-item">Edit</li>
@endsection

@section('content')
<br>
<style>
   
</style>

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
     <form action="{{ route('kas_bank.update',[$KasBank->id]) }}" method="POST" id="post">
      @csrf
      @method('PUT')

        <div class="row">
            {{-- <div class="col-12 ">
                <div class="card radiusSendiri">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <a href="{{ route('kas_bank.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                        </div>
                        <button type="submit" name="save" id="save" value="save" class="btn ml-auto btn-success radiusSendiri"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    </div>
                </div>
            </div> --}}
            <div class="col-lg-6 col-md-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('kas_bank.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                        <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nama_jenis">Nama Kas / Bank</label>
                            <input required type="text" placeholder="contoh: KAS KECIL / KAS BESAR [BCA]" maxlength="45" name="nama" class="form-control" value="{{old('nama',$KasBank->nama)}}" >                         
                        </div>
                        <div class="form-group">
                            <label for="no_akun">No. akun</label>
                            <input type="number" maxlength="20" name="no_akun" class="form-control" value="{{old('no_akun',$KasBank->no_akun)}}" >                         
                        </div>  
                        <div class="form-group">
                            <label for="tipe">Tipe</label>
                            <br>

                            <div class="icheck-primary d-inline">
                                <input id="kasRadio" type="radio" name="tipe" value="1" {{'Kas' == old('tipe',$KasBank->tipe)? 'checked' :'' }}>
                                <label class="form-check-label" for="kasRadio">Kas</label>
                            </div>
                            <div class="icheck-primary d-inline ml-5">
                                <input id="bankRadio" type="radio" name="tipe" value="2" {{'Bank'== old('tipe',$KasBank->tipe)? 'checked' :'' }}>
                                <label class="form-check-label" for="bankRadio">Bank</label><br>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            <label for="catatan">Saldo Awal</label>
                            <input type="text" oninput="formatNumber(this)" maxlength="100" name="catatan" class="form-control" value="{{old('catatan','')}}" >                         
                        </div>  --}}
                        <div class="form-group">
                            <label for="saldo_awal">Saldo Awal</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input readonly type="text" id="saldo" maxlength="100" name="saldo_awal" class="form-control uang numajaMinDesimal" value="{{old('saldo_awal', number_format($KasBank->saldo_awal,2))}}" >                         
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            <label for="lastName">Tanggal Pembuatan</label>
                            <input type="date" class="form-control" id="tanggalDibuat" placeholder="" value="" required="" name="tgl_saldo" value="{{old('tgl_saldo','')}}">
                            <div class="invalid-feedback"> Valid last name is required. </div>
                            <label for="lastName">Tanggal Pembuatan</label>
                            <input type="text" class="form-control" id="tanggalDibuatDisplay" placeholder="DD-MMM-YYYY" value="{{old('tgl_saldo','')}}" required>
                            <input type="hidden" id="tanggalDibuat" name="tgl_saldo">
                        </div> --}}
                        <div class="form-group">
                            <label for="tgl_saldo">Tanggal Pembuatan</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" name="tgl_saldo" autocomplete="off" class="date form-control" id="tgl_saldo" placeholder="dd-M-yyyy" value="{{old('tgl_saldo',\Carbon\Carbon::parse($KasBank->tgl_saldo)->format('d-M-Y')) }}">     
                            </div>
                        </div>
                      
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Rekening Bank</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="no_akun">No. Rekening</label>
                            <input type="number" maxlength="25" id="no_rek" name="no_rek" class="form-control" value="{{old('no_rek',$KasBank->no_rek)}}" >                         
                        </div>  
                        <div class="form-group">
                            <label for="no_akun">Atas Nama</label>
                            <input type="text" maxlength="45" id="rek_nama"  name="rek_nama" class="form-control" value="{{old('rek_nama',$KasBank->rek_nama)}}" >                         
                        </div>  
                        <div class="form-group">
                            <label for="no_akun">Nama Bank</label>
                            <input type="text" maxlength="45" id="bank" name="bank" class="form-control" value="{{old('bank',$KasBank->bank)}}" >                         
                        </div>  
                        <div class="form-group">
                            <label for="no_akun">Kantor Cabang</label>
                            <input type="text" maxlength="100" id="cabang" name="cabang" class="form-control" value="{{old('cabang',$KasBank->cabang)}}" >                         
                        </div>  
                    </div>
                </div>
            </div>  
            
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        $('#post').submit(function(event) {
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
                    }, 1000); // 2000 milliseconds = 2 seconds
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
<script type="text/javascript">

  $(document).ready(function() {
    const saldoAwal = "{{$KasBank->saldo_awal}}"; 

    console.log(saldoAwal);
    // ====================================logic tanggal =================================
    // const initialDate = "{{ $KasBank->tgl_saldo }}"; 
    // console.log(initialDate);
    // const dateDisplay = moment(initialDate).format('DD-MMM-YYYY');
    // const dateHiddenDB = moment(initialDate).format('YYYY-MM-DD');


    // $('input[id="tanggalDibuatDisplay"]').daterangepicker({
    //     opens: 'center',
    //     drops: 'up',
    //     singleDatePicker: true,
    //     showDropdowns: true,
    //     autoApply: true,
    //     startDate: moment(initialDate),
    //     locale: {
    //         format: 'DD-MMM-YYYY',
    //     }
    // }, function(start, end, label) {
    //     const formattedDate = start.format('DD-MMM-YYYY');
    //     $('#tanggalDibuatDisplay').val(formattedDate);
    //     $('#tanggalDibuat').val(start.format('YYYY-MM-DD'));
    //     console.log("A new date selection was made: " + formattedDate);
    // });
    // $('#tanggalDibuatDisplay').val(dateDisplay);
    // $('#tanggalDibuat').val(dateHiddenDB);
      $('#tgl_saldo').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            endDate: "0d"
        });

    // ====================================logic tanggal =================================


    if($('#bankRadio').prop('checked'))
    {
      $('#no_rek, #rek_nama, #bank, #cabang').prop('readonly', false);
    }
    else
    {
      $('#no_rek, #rek_nama, #bank, #cabang').prop('readonly', true);

      
    }
     $('#bankRadio').click(function() {
      if ($(this).prop('checked')) {
        $('#no_rek, #rek_nama, #bank, #cabang').prop('readonly', false);
      }
     });
     $('#kasRadio').click(function() {
      if ($(this).prop('checked')) {
        $('#no_rek, #rek_nama, #bank, #cabang').prop('readonly', true);
        // $('#no_rek, #rek_nama, #bank, #cabang').val(''); // tanya dulu y/n

      }
     });
  
  });

</script>

<script>
    
</script>

<script>
   
</script>
@endsection
