
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('marketing.index')}}">Grup Member</a></li>
<li class="breadcrumb-item">Create</li>
@endsection

@section('content')
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
    <form action="{{ route('marketing.store') }}" method="POST" id="post">
    @csrf

    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{ route('marketing.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                    <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="">Grup<span class="text-red">*</span></label>
                                <select class="form-control select2" style="width: 100%;" id='grup_id' name="grup_id" required>
                                    <option value="">── Pilih Grup ──</option>
                                    @foreach ($grup as $item)
                                        <option value="{{$item->id}}">{{ $item['nama_grup'] }}</option>
                                    @endforeach
                                </select>
                            </div>   
        
                            <div class="form-group ">
                                <label for="">Kota<span class="text-red">*</span></label>
                                <select class="form-control select2" style="width: 100%;" id='kota_id' name="kota_id" required>
                                    <option value="">── Pilih Kota ──</option>
                                    @foreach ($kota as $kt)
                                        <option value="{{$kt->id}}">{{ $kt['nama'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <label for="">Nama Marketing<span class="text-red">*</span></label>
                                <input  type="text" required name="nama" class="form-control" value="{{old('nama','')}}" >                         
                            </div>

                            <div class="form-group ">
                                <label for="telp_1">Telp<span class="text-red">*</span></label>
                                <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">+62</span>
                                </div>
                                <input type="text" name="telp1" class="form-control numaja" maxlength="14" id="telp1" placeholder="" value="" required>    
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-sm-12">
                            <div class="form-group ">
                                <label for="">No Rekening</label>
                                <input type="text" name="no_rek" maxlength="20" class="form-control numaja" value="{{old('no_rek','')}}" required>                         
                            </div>
                            <div class="form-group ">
                                <label for="">Atas Nama Rekening</label>
                                <input type="text" name="atas_nama" maxlength="30" class="form-control " value="{{old('atas_nama','')}}" required>                         
                            </div>
                             <div class="form-group ">
                                <label for="">Cabang Bank</label>
                                <input type="text" name="cabang" maxlength="25" class="form-control " value="{{old('cabang','')}}" >                         
                            </div>
                            <div class="form-group ">
                                <label for="">Nama Bank</label>
                                <input type="text" name="bank" maxlength="15" class="form-control " value="{{old('bank','')}}" required>                         
                            </div>
                           
                        </div>
                       
                        

                        
                    </div>


                </div>
            </div>
        </div>
      
    </div>

    </form>

</div>
{{-- sweet save --}}
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
                        timer: 800,
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
<script>
    $("#telp1").on("change", function() {
        var inputValue = $(this).val();
        if (inputValue.startsWith("08")) {
            inputValue = "8" + inputValue.substring(2);
            $(this).val(inputValue);
        }else if(inputValue.startsWith("628")){
            inputValue = "8" + inputValue.substring(3);
            $(this).val(inputValue);
        }
    });
</script>
@endsection
