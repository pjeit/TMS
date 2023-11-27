
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
    <form action="{{ route('transaksi_lain.update',[$dataKasLain->id_kas_lain]) }}" method="POST" id="post">
        @csrf
        @method('PUT')
        {{-- <div class='row'>
            <div class="col-lg-12"> --}}
              <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('transaksi_lain.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                        <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="tanggal_transaksi">Tanggal Transaksi<span style="color:red">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" autocomplete="off" name="tanggal_transaksi" class="form-control date @error('tanggal_transaksi') is-invalid @enderror" id="tanggal_transaksi" placeholder="dd-M-yyyy" value="{{old('tanggal_transaksi',\Carbon\Carbon::parse($dataKasLain->tanggal)->format('d-M-Y') )}}">
                                    @error('tanggal_transaksi')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="tipe">Tipe Transaksi</label>
                                <br>
                                <div class="icheck-primary d-inline ">
                                    <input id="sudahNikah" type="radio" name="tipe_transaksi" value="pengeluaran" {{$dataKasLain->tipe_coa=='pengeluaran'?'checked':''}}>
                                    <label class="form-check-label" for="sudahNikah">Pengeluaran</label>
                                </div>
                                <div class="icheck-primary d-inline ml-3">
                                    <input id="belumNikah" type="radio" name="tipe_transaksi" value="penerimaan" {{$dataKasLain->tipe_coa=='penerimaan'?'checked':''}}>
                                    <label class="form-check-label" for="belumNikah">Penerimaan</label>
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="select_coa">Jenis Transaksi<span style="color:red">*</span></label>
                                    <select class="form-control select2  @error('select_coa') is-invalid @enderror" style="width: 100%;" id='select_coa' name="select_coa">
                                    <option value="">Pilih Jenis Transaksi</option>
                                    @foreach ($dataCOA as $data)
                                        <option value="{{$data->id}}" {{$dataKasLain->coa_id==$data->id?'selected':''}} id_coa='{{$data->no_akun}}' nama_coa='{{$data->nama_jenis}}' tipe='{{$data->tipe}}'>{{ $data->nama_jenis }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="id_coa_hidden" id="id_coa_hidden">
                                <input type="hidden" name="nama_coa_hidden" id="nama_coa_hidden">

                                @error('select_coa')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror   
                            </div>
                        
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="select_bank">Kas/Bank<span style="color:red">*</span></label>
                                    <select class="form-control select2  @error('select_bank') is-invalid @enderror" style="width: 100%;" id='select_bank' name="select_bank">
                                    <option value="">Pilih Kas/Bank</option>
                                    @foreach ($dataKas as $data)
                                        <option value="{{$data->id}}" {{$dataKasLain->kas_bank_id==$data->id?'selected':''}} >{{ $data->nama }}</option>
                                    @endforeach
                                </select>
                                @error('select_bank')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror   
                            </div>

                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="total">Total Nominal<span style="color:red">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="total" class="form-control numaja uang @error('total') is-invalid @enderror" id="total" placeholder="" value="{{old('total',number_format( $dataKasLain->total))}}">
                                    @error('total')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>  
                            
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="keterangan_klaim">Catatan</label>
                                <input type="text" class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" value="{{old('catatan',$dataKasLain->catatan)}}">
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


<script>
    $(document).ready(function() {
        $('#tanggal_transaksi').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            endDate: "0d",

        });
        var selectedOption = $('#select_coa').find('option:selected');
        var id_coa_dari_select = selectedOption.attr('id_coa');
        var nama_coa_dari_select = selectedOption.attr('nama_coa');

        $('#id_coa_hidden').val(id_coa_dari_select);
        $('#nama_coa_hidden').val(nama_coa_dari_select);

        filterCoa($('input[name="tipe_transaksi"]:checked').val());
        $(document).on('change','input[name="tipe_transaksi"]', function() {
            filterCoa($(this).val());
            $('#id_coa_hidden').val('');
            $('#nama_coa_hidden').val('');
        });
        function filterCoa(selectedTipe) {
            var dataCOA =  <?php echo json_encode($dataCOA); ?>;
            var select_coa = $('#select_coa');
            //select dr db buat filter
            const filterCoa = dataCOA.filter(coa => {
                if (selectedTipe.toLowerCase() === 'penerimaan') {
                    return coa.tipe.toLowerCase() === `penerimaan`;
                } else if (selectedTipe.toLowerCase() === 'pengeluaran') {
                    return coa.tipe.toLowerCase() === `pengeluaran`;
                }
                return true; 
            });
                
            select_coa.empty();
            select_coa.append('<option value="">Pilih Jenis Transaksi</option>');
            filterCoa.forEach(coaValue => {
                const option = document.createElement('option');
                option.value = coaValue.id;
                option.setAttribute('id_coa', coaValue.no_akun);
                option.setAttribute('nama_coa', coaValue.nama_jenis);
                option.setAttribute('tipe', coaValue.tipe);
                if ( $('#id_coa_hidden').val() == coaValue.no_akun) {
                    option.selected = true;
                }
                option.textContent = coaValue.nama_jenis + ` (${coaValue.tipe})`;
                select_coa.append(option);
            });
            //di kosongin kalau ganti tipe
           
        }
        $('body').on('change','#select_coa',function()
        {
            var selectedOption = $(this).find('option:selected');
            var id_coa_dari_select = selectedOption.attr('id_coa');
            var nama_coa_dari_select = selectedOption.attr('nama_coa');

            $('#id_coa_hidden').val(id_coa_dari_select);
            $('#nama_coa_hidden').val(nama_coa_dari_select);

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
                
                return;
            }
            if($("#select_coa").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `JENIS TRANSAKSI WAJIB DI ISI!`,
                })
                
                return;
            }
            if($("#select_bank").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `BANK WAJIB DI PILIH!`,
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
