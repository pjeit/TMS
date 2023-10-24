
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
  
@endsection

@section('content')
<style >
   .tinggi{
    height: 20px;
   }
</style>
<div class="container-fluid">
    <form action="{{ route('insertCancel.insert') }}" id="post_data" method="POST" >
        @csrf 
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('dalam_perjalanan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                {{-- <a href="{{ route('dalam_perjalanan.index') }}" class="btn btn-success radiusSendiri"><i class="fa fa-save"></i> Simpan</a> --}}
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>

            </div>
            <div class="card-body">
               
               <div class="row">
                    <div class="col-6" style=" border-right: 1px solid rgb(172, 172, 172);">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group ">
                                    <label for="tanggal_berangkat">Tanggal Berangkat<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input disabled type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse($data->tanggal_berangkat)->format('d-M-Y')}}">
                                    </div>
                                    <input type="hidden" name="id_sewa_hidden" value="{{$id_sewa}}">
                                    <input type="hidden" name="no_sewa" value="{{$data->no_sewa}}">

                                </div> 
                            </div>
                            <div class="col-6">
                                 <div class="form-group ">
                                    <label for="tanggal_cancel">Tanggal Cancel<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input disabled type="text" autocomplete="off" name="tanggal_cancel" class="form-control date" id="tanggal_cancel" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse(now())->format('d-M-Y')}}">
                                    </div>
                                </div> 
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <label for="no_akun">Customer</label>
                            <input type="text" id="customer" name="customer" class="form-control" value="{{$data->nama_cust}}" readonly>                         
                        </div>  

                        <div class="form-group ">
                            <label for="no_akun">Tujuan</label>
                            <input type="text" id="tujuan" name="tujuan" class="form-control" value="{{$data->nama_tujuan}}" readonly>                         
                        </div>  
                        <div class="form-group">
                            <label for="no_akun">No. Kontainer</label>
                            @if ($data->no_kontainer_jod&&$data->jenis_order =="INBOUND")
                                <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" readonly value="{{$data->no_kontainer_jod}}" >                         
                            @else
                                <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" value="{{$data->no_kontainer}}" >                         

                            @endif
                        </div> 
                            @if ($data->seal_pelayaran_jod&&$data->jenis_order =="INBOUND")
                            <div class="form-group ">
                                <label for="seal">Segel Kontainer</label>
                                <input readonly type="text" id="seal" name="seal" class="form-control"value="{{$data->seal_pelayaran_jod}}" >
                            </div> 
                        @endif
                        
                        <div class="form-group">
                            <label for="no_akun">No. Surat Jalan</label>
                            <input type="text" id="surat_jalan" name="surat_jalan" class="form-control" value="{{$data->no_surat_jalan}}" >                         
                        </div> 

                        <div class="form-group ">
                            <label for="no_akun">Catatan</label>
                            <input type="text" id="catatan" name="catatan" class="form-control" value="{{$data->catatan}}" >                         
                        </div> 
                        {{-- <div class="form-group ">
                            <label for="alasan_cancel">Alasan Cancel</label>
                            <textarea name="alasan_cancel" id="alasan_cancel" cols="30" rows="10"></textarea>
                        </div>  --}}
                        <div class="form-group">
                                <label for="alasan_cancel">Alasan Cancel Perjalanan <span style="color: red;">*</span></label>
                                <textarea name="alasan_cancel" class="form-control" id="alasan_cancel" rows="5" value=""></textarea>
                            </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="tanggal_pencairan">Tgl. Kembali Surat Jalan</label>
                            <input disabled type="text" autocomplete="off" name="tanggal_kembali" class="form-control date" id="tanggal_kembali" placeholder="dd-M-yyyy" value="{{\Carbon\Carbon::parse(now())->format('d-M-Y')}}">
                        </div> 
                        <div class="row">
                            {{-- <div class="form-group col-12">
                                Data Kendaraan
                            <hr>

                            </div> --}}
                            <div class="form-group col-4">
                                <label for="no_akun">Kendaraan</label>
                                <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{$data->no_polisi}}" readonly>                         
                            </div>  

                            {{-- @if ($sewa->supir) --}}
                            <div class="form-group col-8">
                                <label for="no_akun">Driver</label>
                                @if ($data->id_supplier)
                                <input type="text" id="driver" name="driver" class="form-control" value="DRIVER REKANAN ({{$data->namaSupplier}})" readonly>     
                                    
                                @else
                                <input type="text" id="driver" name="driver" class="form-control" value="{{$data->supir}} ({{$data->telpSupir}})" readonly>     
                                <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{$data->id_karyawan}}">                    
                                    
                                @endif
                            </div> 
                            {{-- @endif --}}

                        </div>
                        {{-- <div class="col-lg-6 col-md-12"> --}}
                            <div class="form-group">
                                <label for="total_uang_jalan">Total Uang Jalan Diterima Supir</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input readonly="" type="text" name="total_uang_jalan" class="form-control numaja uang" id="total_uang_jalan" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nominal_kembali_kas">Uang Jalan Kembali Sebagai Kas</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="nominal_kembali_kas" class="form-control numaja uang"  id="nominal_kembali_kas" placeholder="" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nominal_kembali_hutang">Uang Jalan Kembali Sebagai Hutang</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" name="nominal_kembali_hutang" id="nominal_kembali_hutang" class="form-control numaja uang" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Kas / Bank <span class="text-red">*</span></label>
                                <select class="form-control select2" name="pembayaran" id="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    @foreach ($dataKas as $kb)
                                        <option value="{{$kb->id}}" <?= $kb->id == 1 ? 'selected':''; ?> >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                    @endforeach
                                </select>
                            </div>
                        {{-- </div> --}}
                            
                    </div>
               </div>
            </div>
        </div> 
    </form>
</div>


         
 
<script type="text/javascript">
// function hitung_selisih(){
//     var uang_jalan = parseFloat(removePeriod($('#total_uang_jalan').val(),','));
//     console.log(uang_jalan);
//     var kembali_hutang = kembali_kas = total_kembali = 0;
//     if ($('#nominal_kembali_kas').val() != ''){
//         kembali_kas = parseFloat(removePeriod($('#nominal_kembali_kas').val(),','));
//     }else{
//         kembali_kas = 0;
//     }

//     if($('#nominal_kembali_hutang').val() != ''){
//         kembali_hutang = parseFloat(removePeriod($('#nominal_kembali_hutang').val(),','));
//     }else{
//         kembali_hutang = 0;
//     }
//     total_kembali = kembali_kas + kembali_hutang;
//     if (total_kembali > uang_jalan){
//         var sisa = total_kembali - uang_jalan;
//         console.log(sisa);
//         if(kembali_hutang > kembali_kas){
//             kembali_hutang = kembali_hutang - sisa;
//             console.log(kembali_hutang);
//             $('#nominal_kembali_hutang').val(addPeriod(kembali_hutang,','));
//         }
//         else{
//             kembali_kas = kembali_kas - sisa;
//             console.log(kembali_kas);
//             $('#nominal_kembali_kas').val(addPeriod(kembali_kas,','));
//         }
//     }
// }
$(document).ready(function() {
// nominal_kembali_kas
// nominal_kembali_hutang
    function hitungan(){
        var uang_jalan = parseFloat(removePeriod($('#total_uang_jalan').val(),','));
        // console.log(uang_jalan);
        var kembali_hutang = 0 ;
        var kembali_kas = 0;
        var total_kembali = 0;
        if ($('#nominal_kembali_kas').val() != ''){
            kembali_kas = parseFloat(removePeriod($('#nominal_kembali_kas').val(),','));
        }else{
            kembali_kas = 0;
        }

        if($('#nominal_kembali_hutang').val() != ''){
            kembali_hutang = parseFloat(removePeriod($('#nominal_kembali_hutang').val(),','));
        }else{
            kembali_hutang = 0;
        }

        
        total_kembali = kembali_kas + kembali_hutang;
        // if (total_kembali > uang_jalan){
            var sisa = total_kembali - uang_jalan;
            console.log(sisa);
            if(kembali_hutang > kembali_kas){
                kembali_hutang -=sisa;
                console.log(kembali_hutang);
                $('#nominal_kembali_hutang').val(addPeriod(kembali_hutang,','));
            }
            else{
                kembali_kas -=sisa;
                console.log(kembali_kas);
                $('#nominal_kembali_kas').val(addPeriod(kembali_kas,','));
            }
        // }

    }
        hitungan();

    $('#nominal_kembali_kas').on('keyup', function(event){
        hitungan();
    });
    $('#nominal_kembali_hutang').on('keyup', function(event){
        hitungan();
    });
     $('#post_data').submit(function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin data sudah benar ?',
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
                    this.submit();
                }else{
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


