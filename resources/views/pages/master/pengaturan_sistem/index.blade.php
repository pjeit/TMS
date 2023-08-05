
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Pengaturan Sistem</li>
    <li class="breadcrumb-item"><a href="{{route('pengaturan_sistem.index')}}">COA</a></li>
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
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pengaturan Sistem</h5>
                </div>
                
                <form action="{{ route('pengaturan_sistem.update', [$dataPengaturanSistem->id]) }}" method="POST" >
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row justify-content-center  g-2">
                            <div class="col">
                                <div class="form-group">
                                    <label>Uang Jalan</label>
                                    <select class="form-control select2" style="width: 100%;" id='uang_jalan' name="uang_jalan">
                                        @foreach ($dataMKas as $item)
                                            <option value="{{ $item->id }}" <?= ($item->id == $dataPengaturanSistem->uang_jajan)? 'selected':''; ?> >{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
            
                                <div class="form-group">
                                    <label>Reimburse</label>
                                    <select class="form-control select2" style="width: 100%;" id='reimburse' name="reimburse">
                                        @foreach ($dataMKas as $item)
                                            <option value="{{ $item->id }}" <?= ($item->id == $dataPengaturanSistem->reimburse)? 'selected':''; ?> >{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                
                                <div class="form-group">
                                    <label>Penerimaan Customer</label>
                                    <select class="form-control select2" style="width: 100%;" id='penerimaan_customer' name="penerimaan_customer">
                                        @foreach ($dataMKas as $item)
                                            <option value="{{ $item->id }}" <?= ($item->id == $dataPengaturanSistem->penerimaan_customer)? 'selected':''; ?> >{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                
                                <div class="form-group">
                                    <label>Pembayaran Supplier</label>
                                    <select class="form-control select2" style="width: 100%;" id='pembayaran_supplier' name="pembayaran_supplier">
                                        @foreach ($dataMKas as $item)
                                            <option value="{{ $item->id }}" <?= ($item->id == $dataPengaturanSistem->pembayaran_supplier)? 'selected':''; ?> >{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Pembayaran Gaji</label>
                                    <select class="form-control select2" style="width: 100%;" id='pembayaran_gaji' name="pembayaran_gaji">
                                        @foreach ($dataMKas as $item)
                                            <option value="{{ $item->id }}" <?= ($item->id == $dataPengaturanSistem->pembayaran_gaji)? 'selected':''; ?>  >{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                
                                <div class="form-group">
                                    <label>Hutang Karyawan</label>
                                    <select class="form-control select2" style="width: 100%;" id='hutang_karyawan' name="hutang_karyawan">
                                        @foreach ($dataMKas as $item)
                                            <option value="{{ $item->id }}" <?= ($item->id == $dataPengaturanSistem->hutang_karyawan)? 'selected':''; ?> >{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Klaim Supir</label>
                                    <select class="form-control select2" style="width: 100%;" id='klaim_supir' name="klaim_supir">
                                        @foreach ($dataMKas as $item)
                                            <option value="{{ $item->id }}" <?= ($item->id == $dataPengaturanSistem->klaim_supir)? 'selected':''; ?> >{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <div class="col">
                                <div class="form-group">
                                    <label>Batas Pemutihan </label>
                                    <br>
                                    <!-- <input type="number" class="form-control" name="batas_pemutihan" id="batas_pemutihan" value="<?= isset($dataPengaturanSistem->batas_pemutihan) ? $dataPengaturanSistem->batas_pemutihan:null; ?>"> -->
                                    <div class="col-sm-12">
                                        <input id="batas_pemutihan" type="text" name="batas_pemutihan" value="<?= ($dataPengaturanSistem->batas_pemutihan)? $dataPengaturanSistem->batas_pemutihan:0; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success"><strong>Simpan</strong></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
