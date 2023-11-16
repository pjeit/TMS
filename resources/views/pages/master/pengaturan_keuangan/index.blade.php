
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Pengaturan Keuangan</li>
    <li class="breadcrumb-item"><a href="{{route('pengaturan_keuangan.index')}}">COA</a></li>
@endsection

@section('content')
<br>
<style>
    /* custom range slider */
    /* .irs--flat .irs-handle>i:first-child{
        background-color: #fff;
    }
    .irs--flat .irs-handle.state_hover>i:first-child, .irs--flat .irs-handle:hover>i:first-child {
        background-color: #fff;
         font-size: 156px;
    }
    .irs--flat .irs-bar {
        background-color: #86eeff;
    }
    .irs--flat .irs-handle{
        background-color: #0011ff;
    }
    .irs--flat .irs-from, .irs--flat .irs-to, .irs--flat .irs-single{
        background-color: #0011ff;
    } */
     .irs--flat .irs-handle>i:first-child {
    background-color: #4e73df!important;//Replace With Your color code
}
.irs--flat .irs-bar {
    background-color: #4e73df!important;//Replace With Your color code
}
.irs--flat .irs-from, .irs--flat .irs-to, .irs--flat .irs-single {
    background-color: #4e73df!important;//Replace With Your color code
}
.irs--flat .irs-from:before, .irs--flat .irs-to:before, .irs--flat .irs-single:before {
    border-top-color: #4e73df!important;//Replace With Your color code
}
</style>
<div class="container-fluid">
    <div class="row">
        <form action="{{ route('pengaturan_keuangan.update', [$data->id]) }}" method="POST" id="save" >
            @csrf
            @method('PUT')

            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header ">
                        <div class="float-left">
                            <button type="submit" name="submitButton" id="submitButton" class="btn ml-auto btn-success radiusSendiri"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Uang Jalan</label>
                                        <select class="form-control select2" style="width: 100%;" id='uang_jalan' name="uang_jalan">
                                            @foreach ($dataMKas as $item)
                                                <option value="{{ $item->id }}" <?= ($item->id == $data->uang_jajan)? 'selected':''; ?> >{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Reimburse</label>
                                        <select class="form-control select2" style="width: 100%;" id='reimburse' name="reimburse">
                                            @foreach ($dataMKas as $item)
                                                <option value="{{ $item->id }}" <?= ($item->id == $data->reimburse)? 'selected':''; ?> >{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                    
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Penerimaan Customer</label>
                                        <select class="form-control select2" style="width: 100%;" id='penerimaan_customer' name="penerimaan_customer">
                                            @foreach ($dataMKas as $item)
                                                <option value="{{ $item->id }}" <?= ($item->id == $data->penerimaan_customer)? 'selected':''; ?> >{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Pembayaran Supplier</label>
                                        <select class="form-control select2" style="width: 100%;" id='pembayaran_supplier' name="pembayaran_supplier">
                                            @foreach ($dataMKas as $item)
                                                <option value="{{ $item->id }}" <?= ($item->id == $data->pembayaran_supplier)? 'selected':''; ?> >{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Pembayaran Gaji</label>
                                        <select class="form-control select2" style="width: 100%;" id='pembayaran_gaji' name="pembayaran_gaji">
                                            @foreach ($dataMKas as $item)
                                                <option value="{{ $item->id }}" <?= ($item->id == $data->pembayaran_gaji)? 'selected':''; ?>  >{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                    
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Hutang Karyawan</label>
                                        <select class="form-control select2" style="width: 100%;" id='hutang_karyawan' name="hutang_karyawan">
                                            @foreach ($dataMKas as $item)
                                                <option value="{{ $item->id }}" <?= ($item->id == $data->hutang_karyawan)? 'selected':''; ?> >{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Klaim Supir</label>
                                        <select class="form-control select2" style="width: 100%;" id='klaim_supir' name="klaim_supir">
                                            @foreach ($dataMKas as $item)
                                                <option value="{{ $item->id }}" <?= ($item->id == $data->klaim_supir)? 'selected':''; ?> >{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
    
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Doc Fee</label>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input required type="text" name="doc_fee" class="form-control numaja uang" value="{{number_format($data->doc_fee)}}" id='doc_fee' >
                                        </div>
                                    </div>
                
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Batas Pemutihan <span id="value_pemutihan" class="badge badge-dark">Rp. <?= ($data->batas_pemutihan)? $data->batas_pemutihan:0; ?></span></label>
                                        <br>
                                        <!-- <input type="number" class="form-control uang numaja" name="batas_pemutihan" id="batas_pemutihan" value="<?= isset($data->batas_pemutihan) ? $data->batas_pemutihan:null; ?>"> -->
                                        <div class="col-sm-12">
                                            <input id="batas_pemutihan" type="text" name="batas_pemutihan" value="<?= ($data->batas_pemutihan)? $data->batas_pemutihan:0; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="col-12">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                        <label>Seal PJE</label>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input required type="text" name="seal_pje" id="seal_pje" class="form-control numaja uang" value="{{number_format($data->seal_pje)}}">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                        <label>Seal Pelayaran</label>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input required type="text" name="seal_pelayaran" id="seal_pelayaran" class="form-control numaja uang" value="{{number_format($data->seal_pelayaran)}}">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                        <label>Tally</label>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input required type="text" name="tally" id="tally" class="form-control numaja uang" value="{{number_format($data->tally)}}">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-3 col-lg-3">
                                        <label>Plastik</label>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input required type="text" name="plastik" id="plastik" class="form-control numaja uang" value="{{number_format($data->plastik)}}">
                                        </div>
                                    </div>
                
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                                <span class="col-sm-12 col-md-12 col-lg-12 d-flex justify-content-center" ><label>Stack TL</label></span>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Perak</label>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input required type="text" name="tl_perak" id="tl_perak" class="form-control numaja uang" value="{{number_format($data->tl_perak)}}">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Priuk</label>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input required type="text" name="tl_priuk" id="tl_priuk" class="form-control numaja uang" value="{{number_format($data->tl_priuk)}}">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-4 col-lg-4">
                                        <label>Teluk Lamong</label>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input required type="text" name="tl_teluk_lamong" id="tl_teluk_lamong" class="form-control numaja uang" value="{{number_format($data->tl_teluk_lamong)}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card radiusSendiri card-outline card-primary">
                            <div class="card-header">
                                <h5 class="mt-2">20 Ft</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <label>THC Luar</label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="thc_20ft_luar" id="thc_20ft_luar" value="{{number_format($data->thc_20ft_luar)}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-6">
                                                <label>THC Dalam</label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="thc_20ft_dalam" id="thc_20ft_dalam" value="{{number_format($data->thc_20ft_dalam)}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-6">
                                                <label>LOLO Luar</label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="lolo_20ft_luar" id="lolo_20ft_luar" value="{{number_format($data->lolo_20ft_luar)}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-6">
                                                <label>LOLO Dalam</label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="lolo_20ft_dalam" id="lolo_20ft_dalam" value="{{number_format($data->lolo_20ft_dalam)}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-6">
                                                <label>APBS </label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="apbs_20ft" id="apbs_20ft" value="{{number_format($data->apbs_20ft)}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-6">
                                                <label>CLEANING </label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="cleaning_20ft" id="cleaning_20ft" value="{{number_format($data->cleaning_20ft)}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <div class="card radiusSendiri card-outline card-primary">
                            <div class="card-header">
                                <h5 class="mt-2">40 Ft</h5>
                            </div>
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <label>THC Luar</label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="thc_40ft_luar" id="thc_40ft_luar" value="{{number_format($data->thc_40ft_luar)}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-6">
                                                <label>THC Dalam</label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="thc_40ft_dalam" id="thc_40ft_dalam" value="{{number_format($data->thc_40ft_dalam)}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-6">
                                                <label>LOLO Luar</label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="lolo_40ft_luar" id="lolo_40ft_luar" value="{{number_format($data->lolo_40ft_luar)}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-6">
                                                <label>LOLO Dalam</label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="lolo_40ft_dalam" id="lolo_40ft_dalam" value="{{number_format($data->lolo_40ft_dalam)}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-6">
                                                <label>APBS 40ft</label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="apbs_40ft" id="apbs_40ft" value="{{number_format($data->apbs_40ft)}}">
                                                </div>
                                            </div>
                                            <div class="form-group col-6">
                                                <label>CLEANING 40ft</label>
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja" name="cleaning_40ft" id="cleaning_40ft" value="{{number_format($data->cleaning_40ft)}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        // logic save
        $( document ).on( 'click', '#submitButton', function (event) {
            event.preventDefault();
            // pop up confirmation
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
                        $("#save").submit();
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
                        // return;
                    }
                })
            // pop up confirmation
        });
    });
</script>
<script>    
$(function () {
    $('#batas_pemutihan').ionRangeSlider({
      min     : 0,
      max     : 10000,
      type    : 'single',
      step    : 1,
    //   postfix : ' mm',
      prettify: false,
    //   hasGrid : true,
        grid_num: 4,
         onChange: function (data) {
            $('#value_pemutihan').html('Rp. '+data.from_pretty);
            // console.table(data.from_pretty);
        }
    })
});
</script>
@endsection
