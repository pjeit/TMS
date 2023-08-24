
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
    .irs--flat .irs-handle>i:first-child{
        background-color: #fff;
    }
    .irs--flat .irs-handle.state_hover>i:first-child, .irs--flat .irs-handle:hover>i:first-child {
        background-color: #fff;
    }
    .irs--flat .irs-bar {
        background-color: #86eeff;
    }
    .irs--flat .irs-handle{
        background-color: #0011ff;
    }
    .irs--flat .irs-from, .irs--flat .irs-to, .irs--flat .irs-single{
        background-color: #0011ff;
    }
</style>
<div class="container-fluid">
        <form action="{{ route('pengaturan_keuangan.update', [$data->id]) }}" method="POST" >
        @csrf
        @method('PUT')

        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header ">
                    <div class="float-left">
                        <button type="submit" name="save" id="save" value="save" class="btn ml-auto btn-success radiusSendiri"><i class="fa fa-fw fa-save"></i>Simpan</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Uang Jalan</label>
                                <select class="form-control select2" style="width: 100%;" id='uang_jalan' name="uang_jalan">
                                    @foreach ($dataMKas as $item)
                                        <option value="{{ $item->id }}" <?= ($item->id == $data->uang_jajan)? 'selected':''; ?> >{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
        
                            <div class="form-group">
                                <label>Reimburse</label>
                                <select class="form-control select2" style="width: 100%;" id='reimburse' name="reimburse">
                                    @foreach ($dataMKas as $item)
                                        <option value="{{ $item->id }}" <?= ($item->id == $data->reimburse)? 'selected':''; ?> >{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
            
                            <div class="form-group">
                                <label>Penerimaan Customer</label>
                                <select class="form-control select2" style="width: 100%;" id='penerimaan_customer' name="penerimaan_customer">
                                    @foreach ($dataMKas as $item)
                                        <option value="{{ $item->id }}" <?= ($item->id == $data->penerimaan_customer)? 'selected':''; ?> >{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
            
                            <div class="form-group">
                                <label>Pembayaran Supplier</label>
                                <select class="form-control select2" style="width: 100%;" id='pembayaran_supplier' name="pembayaran_supplier">
                                    @foreach ($dataMKas as $item)
                                        <option value="{{ $item->id }}" <?= ($item->id == $data->pembayaran_supplier)? 'selected':''; ?> >{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="form-group">
                                <label>Pembayaran Gaji</label>
                                <select class="form-control select2" style="width: 100%;" id='pembayaran_gaji' name="pembayaran_gaji">
                                    @foreach ($dataMKas as $item)
                                        <option value="{{ $item->id }}" <?= ($item->id == $data->pembayaran_gaji)? 'selected':''; ?>  >{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
            
                            <div class="form-group">
                                <label>Hutang Karyawan</label>
                                <select class="form-control select2" style="width: 100%;" id='hutang_karyawan' name="hutang_karyawan">
                                    @foreach ($dataMKas as $item)
                                        <option value="{{ $item->id }}" <?= ($item->id == $data->hutang_karyawan)? 'selected':''; ?> >{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
        
                            <div class="form-group">
                                <label>Klaim Supir</label>
                                <select class="form-control select2" style="width: 100%;" id='klaim_supir' name="klaim_supir">
                                    @foreach ($dataMKas as $item)
                                        <option value="{{ $item->id }}" <?= ($item->id == $data->klaim_supir)? 'selected':''; ?> >{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
        
                            <div class="form-group">
                                <label>Batas Pemutihan </label>
                                <br>
                                <!-- <input type="number" class="form-control" name="batas_pemutihan" id="batas_pemutihan" value="<?= isset($data->batas_pemutihan) ? $data->batas_pemutihan:null; ?>"> -->
                                <div class="col-sm-12">
                                    <input id="batas_pemutihan" type="text" name="batas_pemutihan" value="<?= ($data->batas_pemutihan)? $data->batas_pemutihan:0; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    {{-- &nbsp; --}}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">

                                <div class="form-group col-6">
                                    <label>THC 20ft</label>
                                    <input type="text" class="form-control" name="thc_20ft" id="thc_20ft" value="{{$data->thc_20ft}}">
                                </div>
                                <div class="form-group col-6">
                                    <label>LOLO 20ft</label>
                                    <input type="text" class="form-control" name="lolo_20ft" id="lolo_20ft" value="{{$data->lolo_20ft}}">
                                </div>
                                <div class="form-group col-6">
                                    <label>APBS 20ft</label>
                                    <input type="text" class="form-control" name="apbs_20ft" id="apbs_20ft" value="{{$data->apbs_20ft}}">
                                </div>
                                <div class="form-group col-6">
                                    <label>CLEANING 20ft</label>
                                    <input type="text" class="form-control" name="cleaning_20ft" id="cleaning_20ft" value="{{$data->cleaning_20ft}}">
                                </div>
                                <div class="form-group col-6">
                                    <label>DOCFEE 20ft</label>
                                    <input type="text" class="form-control" name="doc_fee_20ft" id="doc_fee_20ft" value="{{$data->doc_fee_20ft}}">
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label>THC 40ft</label>
                                    <input type="text" class="form-control" name="thc_40ft" id="thc_40ft" value="{{$data->thc_40ft}}">
                                </div>
                                <div class="form-group col-6">
                                    <label>LOLO 40ft</label>
                                    <input type="text" class="form-control" name="lolo_40ft" id="lolo_40ft" value="{{$data->lolo_40ft}}">
                                </div>
                                <div class="form-group col-6">
                                    <label>APBS 40ft</label>
                                    <input type="text" class="form-control" name="apbs_40ft" id="apbs_40ft" value="{{$data->apbs_40ft}}">
                                </div>
                                <div class="form-group col-6">
                                    <label>CLEANING 40ft</label>
                                    <input type="text" class="form-control" name="cleaning_40ft" id="cleaning_40ft" value="{{$data->cleaning_40ft}}">
                                </div>
                                <div class="form-group col-6">
                                    <label>DOCFEE 40ft</label>
                                    <input type="text" class="form-control" name="doc_fee_40ft" id="doc_fee_40ft" value="{{$data->doc_fee_40ft}}">
                                </div>
                            </div>
        
                            
                        </div>
                        
                     
                    </div>
                </div>
            </div>
        </div>
    
        </form>
</div>

<script>


    
$(function () {
    $('#batas_pemutihan').ionRangeSlider({
      min     : 0,
      max     : 10000,
      type    : 'single',
      step    : 1,
    //   postfix : ' mm',
      prettify: false,
      hasGrid : true
    })
});
</script>
@endsection
