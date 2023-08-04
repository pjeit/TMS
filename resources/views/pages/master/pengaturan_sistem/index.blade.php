
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('judul')
Pengaturan Sistem
@endsection


@section('content')
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pengaturan Default </h5>
                </div>
                
                <form action="{{ route('pengaturan_sistem.store') }}" method="POST" >
                    @csrf
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
                                    <label>Batas Pemutihan</label>
                                    <input type="number" class="form-control" name="batas_pemutihan" id="batas_pemutihan" value="<?= isset($dataPengaturanSistem->batas_pemutihan) ? $dataPengaturanSistem->batas_pemutihan:null; ?>">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success"><strong>Simpan</strong></button>
                        <a href="{{ route('pengaturan_sistem.index') }}" class="btn btn-info"><strong>Kembali</strong></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
