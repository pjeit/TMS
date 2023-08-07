
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
    <li class="breadcrumb-item">Create</li>
@endsection

@section('content')
<br>
<style>
   
</style>

<div class="container">
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
     <form action="{{ route('kas_bank.store') }}" method="POST" >
      @csrf
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Data</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nama_jenis">Nama Kas / Bank</label>
                            <input required type="text" placeholder="contoh: KAS KECIL / KAS BESAR [BCA]" maxlength="45" name="nama" class="form-control" value="{{old('nama','')}}" >                         
                        </div>
                        <div class="form-group">
                            <label for="no_akun">No. akun</label>
                            <input type="number" maxlength="20" name="no_akun" class="form-control" value="{{old('no_akun','')}}" >                         
                        </div>  
                        <div class="form-group">
                            <label for="tipe">Tipe</label>
                            <br>

                            <div class="icheck-primary d-inline">
                                <input id="kasRadio" type="radio" name="tipe" value="1" {{'1' == old('tipe','')? 'checked' :'' }}>
                                <label class="form-check-label" for="kasRadio">Kas</label>
                            </div>
                            <div class="icheck-primary d-inline">
                                <input id="bankRadio" type="radio" name="tipe" value="2" {{'2'== old('tipe','')? 'checked' :'' }}>
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
                                <input type="text" oninput="formatNumber(this)" maxlength="100" name="saldo_awal" class="form-control" value="{{old('saldo_awal','')}}" >                         
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Tanggal Pembuatan</label>
                            <input type="date" class="form-control" id="tanggalDibuat" placeholder="" value="" required="" name="tgl_saldo">
                            <div class="invalid-feedback"> Valid last name is required. </div>
                        </div>
                        <button type="submit" class="btn btn-success"><strong>Simpan</strong></button>
                        <a href="{{ route('kas_bank.index') }}" class="btn btn-info"><strong>Kembali</strong></a>
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
                            <input type="number" maxlength="25" id="no_rek" name="no_rek" class="form-control" value="{{old('no_rek','')}}" >                         
                        </div>  
                        <div class="form-group">
                            <label for="no_akun">Atas Nama</label>
                            <input type="text" maxlength="45" id="rek_nama"  name="rek_nama" class="form-control" value="{{old('rek_nama','')}}" >                         
                        </div>  
                        <div class="form-group">
                            <label for="no_akun">Nama Bank</label>
                            <input type="text" maxlength="45" id="bank" name="bank" class="form-control" value="{{old('bank','')}}" >                         
                        </div>  
                        <div class="form-group">
                            <label for="no_akun">Kantor Cabang</label>
                            <input type="text" maxlength="100" id="cabang" name="cabang" class="form-control" value="{{old('cabang','')}}" >                         
                        </div>  
                    </div>
                </div>
            </div>  
            
        </div>
    </form>
</div>

<script type="text/javascript">


//   $(document).ready(function() {

//    const no_rek = document.querySelectorAll('#no_rek');
//    const rek_nama = document.querySelectorAll('#rek_nama');
//    const bank = document.querySelectorAll('#bank');
//    const cabang = document.querySelectorAll('#cabang');
   
//    no_rek.setAttribute('readonly', true);
//    rek_nama.setAttribute('readonly', true);
//    bank.setAttribute('readonly', true);
//    cabang.setAttribute('readonly', true);
//   });
  $(document).ready(function() {
    $('#no_rek, #rek_nama, #bank, #cabang').prop('readonly', true);

     $('#bankRadio').click(function() {
      if ($(this).prop('checked')) {
        $('#no_rek, #rek_nama, #bank, #cabang').prop('readonly', false);
        // var value = $(this).val(); // Get the value of the clicked radio button
        // console.log('Clicked radio button with value: ' + value);       
      }
     });
     $('#kasRadio').click(function() {
      if ($(this).prop('checked')) {
        $('#no_rek, #rek_nama, #bank, #cabang').prop('readonly', true);
      }
     });
  
  });
//  document.addEventListener('DOMContentLoaded', function () {
//     const noRek = document.getElementById('no_rek');
//     const rekNama = document.getElementById('rek_nama');
//     const bank = document.getElementById('bank');
//     const cabang = document.getElementById('cabang');
// // 
//     noRek.setAttribute('readonly', true);
//     rekNama.setAttribute('readonly', true);
//     bank.setAttribute('readonly', true);
//     cabang.setAttribute('readonly', true);
//   });
</script>
@endsection
