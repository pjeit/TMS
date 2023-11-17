
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('coa.index')}}">COA</a></li>
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
    <div class="row">
        {{-- <div class="col-12 ">
            <div class="card radiusSendiri">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <a href="{{ route('coa.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                    </div>
                    <button type="submit" name="save" id="save" value="save" class="btn ml-auto btn-success radiusSendiri"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div> --}}
        <div class="col-12">
            <div class="card radiusSendiri">
        
                <form action="{{ route('coa.update',[$coa->id]) }}" method="POST" id="post">
                    @csrf
                    @method('PUT')
                    <div class="card-header">
                        <a href="{{ route('coa.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                        <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
                    </div>
                    <div class="card-body">
                        {{-- <div class="row justify-content-center g-2"> --}}
                            <div class="form-group">
                                <label for="nama_jenis">Nama Jenis</label>
                                <input required type="text" maxlength="20" name="nama_jenis" class="form-control" value="{{old('nama_jenis',$coa->nama_jenis)}}" >                         
                            </div>
                            <div class="form-group">
                                <label for="no_akun">No. akun</label>
                                <input required type="number" maxlength="10" name="no_akun" class="form-control" value="{{old('no_akun',$coa->no_akun)}}" >                         
                            </div>  
                            <div class="form-group">
                                <label for="tipe">Tipe</label>
                                <br>
                                <div class="icheck-primary d-inline">
                                    <input id="setuju" type="radio" name="tipe" value="1" {{'pengeluaran' == old('tipe',$coa->tipe)? 'checked' :'' }}>
                                    <label class="form-check-label" for="setuju">Pengeluaran</label>
                                </div>
                                <div class="icheck-primary d-inline ml-5">
                                    <input id="tdkSetuju" type="radio" name="tipe" value="2" {{'penerimaan'== old('tipe',$coa->tipe)? 'checked' :'' }}>
                                    <label class="form-check-label" for="tdkSetuju">Penerimaan</label><br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="catatan">Catatan</label>
                                <input type="text" maxlength="100" name="catatan" class="form-control" value="{{old('catatan',$coa->catatan)}}" >                         
                            </div>  
                            <div class="form-group ">
                                <label for="tipe">Masuk dalam transaksi non operasional ?</label>
                                <br>
                                <div class="icheck-primary d-inline">
                                    <input id="is_kas_bank_lain_Y" type="radio" name="is_kas_bank_lain" value="Y" {{$coa->is_kas_bank_lain== 'Y'? 'checked' :'' }}>
                                    <label class="form-check-label" for="is_kas_bank_lain_Y">Ya</label>
                                </div>
                                <div class="icheck-primary d-inline ml-5">
                                    <input id="is_kas_bank_lain_N" type="radio" name="is_kas_bank_lain" value="N" {{$coa->is_kas_bank_lain== 'N'? 'checked' :'' }}>
                                    <label class="form-check-label" for="is_kas_bank_lain_N">Tidak</label><br>
                                </div>
                            </div>
                        {{-- </div> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
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
@endsection
