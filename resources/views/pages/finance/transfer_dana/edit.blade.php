
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif


@section('content')
<br>
<style>
</style>

<div class="container-fluid">
    {{-- @if ($errors->any())
        @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endforeach

    @endif --}}
    <form action="{{ route('transfer_dana.update',[$dataKasTransfer->id]) }}" method="POST" id="post">
        @csrf
        @method('PUT')
        {{-- <div class='row'>
            <div class="col-lg-12"> --}}
              <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('transfer_dana.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                        <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12" id="tanggal_transaksi_div">
                                <label for="tanggal_transaksi">Tanggal Transaksi<span style="color:red">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" autocomplete="off" name="tanggal_transaksi" class="form-control date @error('tanggal_transaksi') is-invalid @enderror" id="tanggal_transaksi" placeholder="dd-M-yyyy" value="{{old('tanggal_transaksi',\Carbon\Carbon::parse($dataKasTransfer->tanggal)->format('d-M-Y'))}}">
                                    @error('tanggal_transaksi')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12" id="select_bank_dari_div">
                                <label for="select_bank_dari">Dari<span style="color:red">*</span></label>
                                    <select class="form-control select2  @error('select_bank_dari') is-invalid @enderror" style="width: 100%;" id='select_bank_dari' name="select_bank_dari">
                                    <option value="">Pilih Kas/Bank</option>
                                    @foreach ($dataKas as $data)
                                        <option value="{{$data->id}}" {{$dataKasTransfer->kas_bank_id_dari==$data->id?'selected':''}} >{{ $data->nama }}</option>
                                    @endforeach
                                </select>
                                @error('select_bank_dari')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror   
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12"  id="select_bank_ke_div">
                                <label for="select_bank_ke">ke<span style="color:red">*</span></label>
                                    <select class="form-control select2  @error('select_bank_ke') is-invalid @enderror" style="width: 100%;" id='select_bank_ke' name="select_bank_ke">
                                    <option value="">Pilih Kas/Bank</option>
                                    @foreach ($dataKas as $data)
                                        <option value="{{$data->id}}" {{$dataKasTransfer->kas_bank_id_ke==$data->id?'selected':''}} >{{ $data->nama }}</option>
                                    @endforeach
                                </select>
                                @error('select_bank_ke')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror   
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12" id="total_div">
                                <label for="total">Total Nominal<span style="color:red">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="total" class="form-control numaja uang @error('total') is-invalid @enderror" id="total" placeholder="" value="{{old('total',number_format( $dataKasTransfer->total))}}">
                                    @error('total')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>  
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="keterangan_klaim">Catatan</label>
                                <input type="text" class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" value="{{old('catatan',$dataKasTransfer->catatan)}}">
                                @error('catatan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>  
                        </div>
                    </div>
              </div>
            {{-- </div>
        </div> --}}
    </form>

<script type="text/javascript">

$(document).ready(function() {

});

</script>

<script>
    $(document).ready(function() {
        $('#tanggal_transaksi').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            endDate: "0d",

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

                        



            if($("#tanggal_transaksi").val().trim()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `TANGGAL TRANSAKSI WAJIB DI ISI!`,
                })
                // $("#tanggal_transaksi").addClass('is-invalid');
                // $("#tanggal_transaksi").append(
                //     `<div class="invalid-feedback">
                //             Tanggal transaksi wajib diisi!
                //     </div>`
                // );
                
                return;
            }
            if($("#select_bank_dari").val().trim()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `BANK ASAL WAJIB DI PILIH!`,
                })
                //  $("#select_bank_dari_div").append(
                //     `<div class="invalid-feedback">
                //             Tanggal transaksi wajib diisi!
                //     </div>`
                // );
                
                return;
            }
            if($("#select_bank_ke").val().trim()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `BANK TUJUAN WAJIB DI PILIH!`,
                })
                //  $("#select_bank_ke_div").append(
                //     `<div class="invalid-feedback">
                //             Tanggal transaksi wajib diisi!
                //     </div>`
                // );
            
                return;
            }
            if($("#select_bank_dari").val()==$("#select_bank_ke").val())
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `BANK ASAL DAN TUJUAN TIDAK BOLEH SAMA!`,
                })
                return;
            }
            if($("#total").val().trim()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `TOTAL NOMINAL WAJIB DI ISI!`,
                })
                // console.log('masuk');
                // $("#total").addClass('is-invalid');
                //  $("#total_div").append(
                //     `<div class="invalid-feedback">
                //             Tanggal transaksi wajib diisi!
                //     </div>`
                // );
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

                    // setTimeout(() => {
                    //     this.submit();
                    // }, 200); // 2000 milliseconds = 2 seconds
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
