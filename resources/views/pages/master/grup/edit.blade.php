
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('grup.index')}}">Grup</a></li>
<li class="breadcrumb-item">Edit</li>

@endsection

<head>
    <!-- ... -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- ... -->
</head>

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
    <form action="{{ route('grup.update', [$data->id]) }}" method="POST" >
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Data</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Nama Grup<span class="text-red">*</span></label>
                            <input required type="text" name="nama_grup" class="form-control" value="{{$data->nama_grup}}" >
                        </div>

                        <div class="form-group">
                            <label for="">Total Kredit</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                  <span class="input-group-text">Rp.</span>
                                </div>
                                <input type="text" name="total_kredit" class="form-control numaja uang" value="{{ $data->total_kredit }}" id="total_kredit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Total Max Kredit</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                  <span class="input-group-text">Rp.</span>
                                </div>
                                <input required type="text" name="total_max_kredit" class="form-control numaja uang" value="{{$data->total_max_kredit}}" id="total_max_kredit" >
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">PIC</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Nama PIC<span class="text-red">*</span></label>
                            <input required type="text" name="nama_pic" class="form-control" value="{{$data->nama_pic}}" >
                        </div>           
                        <div class="form-group">
                            <label for="">Email<span class="text-red">*</span></label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input required type="email" name="email" class="form-control" value="{{$data->email}}" >
                            </div>
                        </div>           
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="">Telp 1<span class="text-red">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input required type="text" name="telp1" class="form-control" value="{{$data->telp1}}" >
                                  </div>
                            </div>          
                            <div class="form-group col-6">
                                <label for="">Telp 2</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" name="telp2" class="form-control" value="{{$data->telp2}}" >
                                  </div>
                            </div>          
                        </div>    
                    </div>
                </div>
            </div>
            <div class="col-6">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </form>

    {{-- <script> aktifkan jika perlu, lalu ubah tipe dari number menjadi text
        // Ambil elemen input
        var inputTotalKredit = document.getElementById('total_kredit');
    
        // Tambahkan event listener saat nilai berubah
        inputTotalKredit.addEventListener('input', function() {
            // Ambil nilai dari input
            var inputValue = this.value.replace(/\D/g, '');
    
            // Format nilai menjadi angka dengan dua desimal dan ribuan dipisahkan dengan titik dan koma
            var formattedValue = (Number(inputValue) / 100).toLocaleString('id-ID', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
    
            // Setel nilai yang sudah diformat ke input
            this.value = formattedValue;
        });
    </script>
         --}}
    <script>
        $(document).ready(function(e){
            if($('#total_kredit').val() != 0){
                let total_kredit = $('#total_kredit').val();
                total_kredit = addPeriod(total_kredit,',');
                $('#total_kredit').val(total_kredit);
            }
            if($('#total_max_kredit').val() != 0){
                let total_max_kredit = $('#total_max_kredit').val();
                total_max_kredit = addPeriod(total_max_kredit,',');
                $('#total_max_kredit').val(total_max_kredit);
            }
        });
    </script>
</div>
@endsection
