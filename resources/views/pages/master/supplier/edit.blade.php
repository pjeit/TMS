
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('supplier.index')}}">Supplier</a></li>
<li class="breadcrumb-item">Edit</li>

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
    <form action="{{ route('supplier.update', [$data->id]) }}" method="POST" id="post">
    @csrf
    @method('PUT')
    <div class="row">
        {{-- <div class="col-12 ">
            <div class="card radiusSendiri">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <a href="{{ route('supplier.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                    </div>
                    <button type="submit" name="save" id="save" value="save" class="btn ml-auto btn-success radiusSendiri"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div> --}}
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                     <a href="{{ route('supplier.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                    <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
                </div>
                <div class="card-body">
                    <div class="row col-12">
                        <div class="form-group col-4">
                            <label for="">Nama Supplier*</label>
                            <input required type="text"  name="nama" class="form-control" value="{{$data->nama}}" >                         
                        </div>
    
                        <div class="form-group col-4">
                            <label for="">Jenis Supplier </label>
                            <select class="form-control select2" style="width: 100%;" id='jenis_supplier_id' name="jenis_supplier_id">
                                @foreach ($jenis_supplier as $jenis)
                                    <option value="{{$jenis->id}}" <?= ($jenis->id == $data->jenis_supplier_id)? 'Selected':''; ?> >{{ $jenis->nama }}</option>
                                @endforeach
                            </select>
                        </div>   

                        <div class="form-group col-4">
                            <label for="">Kota</label>
                            <select class="form-control select2" style="width: 100%;" id='kota_id' name="kota_id">
                                <option value="">── PILIH KOTA ──</option>
                                @foreach ($kota as $city)
                                    <option value="{{$city->id}}" <?= ($city->id==$data->kota_id)? 'selected':''; ?> >{{ $city->nama }}</option>
                                @endforeach
                            </select>
                        </div>   
                    </div>
                    <div class="row col-12">
                        <div class="form-group col-4">
                            <label for="">Alamat</label>
                            <input required type="text" name="alamat" class="form-control" value="{{$data->alamat}}" >                         
                        </div>
                        
                        <div class="form-group col-4">
                            <label for="">Telp</label>
                            <div class="input-group ">
                                <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><strong>+62</strong></i>
                                  </span>
                                </div>
                                <input type="text" class="form-control numaja" name="telp" id='telp1' value="{{ $data->telp }}" >
                            </div>
                        </div>
    
                        <div class="form-group col-4">
                            <label for="">Email</label>
                            <div class="input-group ">
                                <div class="input-group-prepend">
                                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope" aria-hidden="true"></i>
                                  </span>
                                </div>
                                <input type="email" class="form-control" name="email"  value="{{ $data->email }}" >
                            </div>
                        </div>

                    </div>
                    <div class="row col-12">
                        <div class="form-group col-4">
                            <label for="">NPWP</label>
                            <input  type="text" name="npwp" class="form-control" value="{{ $data->npwp }}" >                         
                        </div>
    
                        <div class="form-group col-4">
                            <label for="">Catatan</label>
                            <input type="text" name="catatan" class="form-control" value="{{ $data->catatan }}" >                         
                        </div>
                             
                        <div class="form-group col-4">
                            <label for="tanggal_keluar">PPH</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><input type="checkbox" id="cekPPH" name="cekPPH" {{($data->pph)? 'checked':'';}}></span>
                                </div>
                                <input step=".01" type="number" name="pph" class="form-control" id="pph" value="{{ $data->pph }}"  min="0" readonly>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div> 
                    </div>
                    

                   

                    

                </div>
              
            </div>
        </div>

        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <h5 class="card-title">Rekening bank</h5>
                </div>
                <div class="card-body">
                    <div class="row col-12">
                        {{-- <div class="form-group col-6">
                            <label for="">No. Rekening</label>
                            <input required type="text" name="no_rek" class="form-control" value="{{ $data->no_rek }}" >                         
                        </div> --}}
                        <div class="form-group col-6">
                                <label for="tanggal_keluar">No. Rekening <span style="opacity: 40%">(Harap dicentang apabila virtual account)</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><input type="checkbox" id="cekVirtual" name="cekVirtual" value="{{$data->is_virtual_acc}}"{{$data->is_virtual_acc=='Y'?'checked':''}}></span>
                                        <input type="hidden" id="hiddenVirtual" name="hiddenVirtual" value="{{$data->is_virtual_acc}}"></span>

                                    </div>
                                     <input  type="text" name="no_rek" class="form-control" value="{{old('no_rek',$data->no_rek)}}" > 
                                </div>
                        </div>
                 
                        <div class="form-group col-6">
                            <label for="">Atas Nama</label>
                            <input  type="text" name="rek_nama" class="form-control" value="{{ $data->rek_nama }}" >                         
                        </div>
                    </div>
                    <div class="row col-12">
                        <div class="form-group col-6">
                            <label for="">Bank</label>
                            <input  type="text" name="bank" class="form-control" value="{{ $data->bank }}" >                         
                        </div>
                
                        <div class="form-group col-6">
                            <label for="">Cabang</label>
                            <input  type="text" name="cabang" class="form-control" value="{{ $data->cabang }}" >                         
                        </div>
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
$(document).ready(function(){
     $('#cekVirtual').click(function(){
            if($(this).is(":checked")){
              
                $('#hiddenVirtual').val('Y');
                
                // console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#hiddenVirtual').val('N');
        
                // console.log("Checkbox is unchecked.");
            }
        });
   $('#cekPPH').click(function(){
            if($(this).is(":checked")){
                $('#pph').attr('readonly',false);
                
                $('#pph').val('');

                
                // console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#pph').val(0);

                $('#pph').attr('readonly',true);
                // console.log("Checkbox is unchecked.");
            }
        });
});

</script>
@endsection
