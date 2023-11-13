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
    .noClick {
        pointer-events: none;
    }
    table {
        /* display: block;
        overflow-x: auto;
        white-space: nowrap; */
    }
</style>
    <form action="{{ route('belum_invoice.store') }}" id="save" method="POST" >
        @csrf
        <div class="container-fluid">
            <div class="card radiusSendiri">
                <div class="card-header ">
                    <a href="{{ route('belum_invoice.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                     <button type="button" class="btn btn-success radiusSendiri" id="btnSimpan">
                        <i class="fa fa-fw fa-save"></i> Simpan    
                    </button>
                </div>
                <div class="card-body" >
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Grup</label>
                                    <input type="text" class="form-control" value="{{ $data->getGroup->nama_grup }}" readonly>                         
                                    <input type="hidden" id="grup_id" name="grup_id" class="form-control" value="{{ $data->id_grup }}" readonly>                         
                                    <input type="hidden" id="no_invoice" name="no_invoice" class="form-control" value="" placeholder="otomatis" readonly>   
                                </div>  
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="tanggal_invoice">Tanggal Invoice<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input readonly type="text" autocomplete="off" name="tanggal_invoice" class="form-control date" id="tanggal_invoice" placeholder="dd-M-yyyy" value="{{ date("d-M-Y", strtotime($data->tgl_invoice)) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Jatuh Tempo<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input name="jatuh_tempo" id="jatuh_tempo" class="form-control date" type="text" autocomplete="off" placeholder="dd-M-yyyy" value="{{ date("d-M-Y", strtotime($data->jatuh_tempo)) }}">
                                    </div>
                                </div>

                                <div class="form-group col-lg-6 col-md-6 col-sm-12" id="jatuh_tempo_pisah_kontainer">
                                    <label for="" style="font-size: 1em;">Jatuh Tempo Reimburse<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input name="jatuh_tempo_pisah" id="jatuh_tempo_pisah" class="form-control date" type="text" autocomplete="off" placeholder="dd-M-yyyy" value="{{ isset($reimburse)? date("d-M-Y", strtotime($reimburse->jatuh_tempo)):'' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Catatan</label>
                                    <textarea type="text" id="catatan_invoice" name="catatan_invoice" class="form-control" rows="4">{{ $data['catatan'] }}</textarea>                     
                                    <input type="hidden" id="is_pisah_invoice" name="is_pisah_invoice" value="FALSE">
                                </div>  
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Tagihan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_tagihan" name="total_tagihan" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Dibayar</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_dibayar" name="total_dibayar" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Jumlah Muatan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Kg</span>
                                        </div>
                                        <input type="number" maxlength="100" id="total_jumlah_muatan" name="total_jumlah_muatan" class="form-control" value="" readonly>                         
                                    </div>
                                </div>
    
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Sisa</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" id="total_sisa" name="total_sisa" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                        <input type="hidden" id="total_pisah" name="total_pisah" class="form-control uang numajaMinDesimal" value="" placeholder="total_pisah" readonly>                         
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <ul class="list-group ">
                                        <li class="list-group-item bg-light text-primary border-primary"><span class="font-weight-bold">BILLING TO</span></li>
                                        <li class="list-group-item bg-light border-primary">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <span class="text-bold">Grand Total</span>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <span id="total_tagihan_text" class="text-bold">Rp. 0</span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item bg-light border-primary">
                                            <div class="row">
                                                <div class="col-12">
                                                    <select name="billingTo" class="select2" style="width: 100%" id="billingTo" required>
                                                        <option value="">── BILLING TO ──</option>
                                                        {{-- @foreach ($dataCust as $cust)
                                                            <option value="{{ $cust->id }}" kode="{{ $cust->kode }}" ketentuan_bayar="{{ $cust->ketentuan_bayar }}" {{ $cust->id == $customer? 'selected':'' }}> {{ $cust->kode }} - {{ $cust->nama }}</option>
                                                        @endforeach --}}
                                                    </select>
                                                    <input type="hidden" name="kode_customer" id="kode_customer">
                                                </div>
                                            </div>
                                        </li>
                                      </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        {{-- style="overflow: auto;" --}}
        <div class="m-3" style="overflow-x:auto; overflow-y:hidden">
            <input type="text" name="deleted[]" id="deleted" value="" hidden/>
            <table class="table table-hover table-bordered table-striped" width='100%' id="table_invoice">
                <thead>
                    <tr class="bg-white">
                        <th style="text-align:center">Customer</th>
                        <th style="text-align:center">Tujuan</th>
                        <th style="text-align:center">Driver</th>
                        <th style="text-align:center">
                            <span style="font-size: 0.8em;">
                                {{-- @if ($checkLTL) --}}
                                <b>No. Koli &amp; SJ</b>
                                {{-- @else --}}
                                <b>Kontainer &amp; Segel</b>
                                {{-- @endif --}}
                            </span>
                            {{-- <input type="hidden" name="" id="checkTL" value="{{ $checkLTL }}"> --}}
                        </th>
                        <th style="text-align:center">Muatan</th>
                        <th style="text-align:center">Tarif</th>
                        <th style="text-align:center">Add Cost</th>
                        <th style="text-align:center">Diskon</th>
                        <th style="text-align:center">Subtotal</th>
                        <th style="text-align:center">Catatan</th>
                        <th style="width:30px"></th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $i = 0;
                @endphp
                @isset($data)
                    @foreach ($data->invoiceDetails as $key => $item_raw)
                        @php
                            $item = $item_raw->sewa;
                        @endphp
                        <tr id="{{ $i }}">
                            <td> 
                                {{ $item->getCustomer->nama }} 
                                <input type="hidden" name="detail[{{ $item->id_sewa }}][id_customer]" value="{{ ($item->id_customer) }}" />
                                <input type="hidden" name="detail[{{ $item->id_sewa }}][id_jo_hidden]" value="{{ $item->id_jo }}" />
                                <input type="hidden" name="detail[{{ $item->id_sewa }}][id_jo_detail_hidden]" value="{{ $item->id_jo_detail }}" />
                            </td>
                            <td> {{ $item->nama_tujuan }} <br> ({{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }})</td>
                            <td>
                                @php
                                    $driver = '';
                                    if($item->id_supplier){
                                        $driver = 'DRIVER REKANAN ' . '('.$item->namaSupplier.')';
                                    }else{
                                        $driver = $item->no_polisi . ' ('.$item->getKaryawan->nama_panggilan.')'; 
                                    }
                                @endphp
                                    {{ $driver }}
                            </td>
                            <td> <span id="no_kontainer_text_{{ $item->id_sewa }}">{{ isset($item->id_jo_detail)? $item->getJOD->no_kontainer:$item->no_kontainer }}</span> <br> <span id='no_seal_text_{{ $item->id_sewa }}'>{{ $item->seal_pelayaran }}</span> </td>
                            <td style="text-align:right" id="muatan_satuan_{{ $key }}">
                                {{ (isset($item->jumlah_muatan ))? number_format($item->jumlah_muatan,2)  : "-" }}
                                <input type="hidden" class="muatan_satuan" name='detail[{{ $item->id_sewa }}][muatan_satuan]' id='muatan_satuan_{{ $item->id_sewa }}' value="{{(isset($item->jumlah_muatan ))?$item->jumlah_muatan : 0}}">
                            </td>
                            <td style="text-align:right" id="tarif_{{ $key }}">{{ number_format($item->total_tarif) }}</td>
                            <td style="text-align:right">
                                @php
                                    $total_addcost = 0;
                                    $total_addcost_pisah = 0;
                                @endphp
                                @foreach ($item->sewaOperasional as $i => $oprs)
                                    @if ($oprs->is_aktif == 'Y' && 
                                         $oprs->status == 'SUDAH DICAIRKAN'&&$oprs->is_ditagihkan == 'Y'&&$oprs->is_dipisahkan == 'N'||
                                         $oprs->status == 'TAGIHKAN DI INVOICE' &&$oprs->is_ditagihkan == 'Y'&&$oprs->is_dipisahkan == 'N')
                                        <input type="hidden" class="addcost_{{ $item->id_sewa }} {{ $oprs->deskripsi }}" value="{{ $oprs->total_operasional }}">
                                        @php
                                            $total_addcost += $oprs->total_operasional;
                                        @endphp
                                    @elseif ($oprs->is_aktif == 'Y' && 
                                         $oprs->status == 'SUDAH DICAIRKAN'&&$oprs->is_ditagihkan == 'Y'&&$oprs->is_dipisahkan == 'Y'||
                                         $oprs->status == 'TAGIHKAN DI INVOICE' &&$oprs->is_ditagihkan == 'Y'&&$oprs->is_dipisahkan == 'Y')
                                        @php
                                            $total_addcost_pisah += $oprs->total_operasional;
                                        @endphp
                                    @endif
                                     {{-- @if($oprs->is_aktif == 'Y' && 
                                         $oprs->status == 'SUDAH DICAIRKAN'&&$oprs->is_ditagihkan == 'Y'&&$oprs->is_dipisahkan == 'Y'||
                                         $oprs->status == 'TAGIHKAN DI INVOICE' &&$oprs->is_ditagihkan == 'Y'&&$oprs->is_dipisahkan == 'Y')
                                        @php
                                            $total_addcost_pisah += $oprs->total_operasional;
                                        @endphp
                                    @endif --}}
                                @endforeach
                                <span class="text_addcost_{{ $item->id_sewa }}">{{ number_format($total_addcost) }}</span>
                                <input type="hidden" class="cek_detail_addcost" id_sewa="{{ $item->id_sewa }}" name="detail[{{ $item->id_sewa }}][addcost_details]" id="detail_addcost_{{ $item->id_sewa }}" value="{{ json_encode($item->sewaOperasional) }}" />
                                {{-- <input type="text" name="detail[{{ $item->id_sewa }}][addcost_details_pisah]" id="detail_addcost_pisah_{{ $item->id_sewa }}" value="{{ json_encode($item->sewaOperasionalPisah) }}" /> --}}
                                <input type="hidden" class="cek_detail_addcost_baru" id_sewa="{{ $item->id_sewa }}" name="detail[{{ $item->id_sewa }}][addcost_baru]" id="detail_addcost_baru_{{ $item->id_sewa }}" value="" />

                                <input type="hidden" class="addcost_{{ $item->id_sewa }} {{ $item->deskripsi }}" name='detail[{{ $item->id_sewa }}][addcost]' id='addcost_hidden_{{ $item->id_sewa }}' value="{{ $total_addcost }}">
                                <input type="hidden" class="addcost_pisah addcost_pisah_{{ $item->id_sewa }} {{ $item->deskripsi }}" name='detail[{{ $item->id_sewa }}][addcost_pisah]' id='addcost_pisah_hidden_{{ $item->id_sewa }}' value="{{ $total_addcost_pisah }}">

                            </td>
                            <td style="text-align:right">
                                <span id='diskon_text_{{ $item->id_sewa }}'></span>
                            </td>
                            <td style="text-align:right">
                                <span id='subtotal_text_{{ $item->id_sewa }}'>{{ number_format($total_addcost+$item->total_tarif) }}</span>
                                <input type="hidden" class="hitung_subtotal subtotal_hidden_{{ $item->id_sewa }} {{ $item->deskripsi }}" name='detail[{{ $item->id_sewa }}][subtotal]' id='subtotal_hidden_{{ $item->id_sewa }}' value="{{ $total_addcost+$item->total_tarif }}">
                            </td>
                            <td><span id="catatan_text_{{ $item->id_sewa }}">{{ $item->catatan }}</span>
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][nama_tujuan]' id='nama_tujuan_hidden_{{ $item->id_sewa }}' value="{{ $item->nama_tujuan }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][driver]' id='driver_hidden_{{ $item->id_sewa }}' value="{{ $driver }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][tgl_berangkat]' id='tgl_berangkat_hidden_{{ $item->id_sewa }}' value="{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][no_kontainer]' id='no_kontainer_hidden_{{ $item->id_sewa }}' value="{{ $item->no_kontainer }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][no_seal]' id='no_seal_hidden_{{ $item->id_sewa }}' value="{{ $item->seal_pelayaran }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][no_sj]' id='no_sj_hidden_{{ $item->id_sewa }}' value="{{ $item->no_surat_jalan }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][tarif]' id='tarif_hidden_{{ $item->id_sewa }}' value="{{ $item->total_tarif }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][catatan]' id='catatan_hidden_{{ $item->id_sewa }}' value="{{ $item->catatan }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][diskon]' id='diskon_hidden_{{ $item->id_sewa }}' >
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="button" name="detail" id="detail_{{$item->id_sewa}}" class="detail dropdown-item"> 
                                            <span class="fas fa-edit mr-3"></span> Detail
                                        </button>
                                        <button type="button" class="dropdown-item deleteParent" value="{{ $item->id_sewa }}">
                                            <span class="fas fa-trash mr-3"></span> Delete
                                        </button>
                                         {{-- <a href="{{route('dalam_perjalanan.edit',[$item->id_sewa])}}" class="dropdown-item" target=”_blank” >
                                                <span class="fas fa-truck mr-3"></span> Edit Sewa
                                            </a> --}}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @php
                        $i++;
                    @endphp
                @endisset
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="modal_detail" tabindex='-1'>
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id='form_add_detail'>
                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}

                        <div class='row'>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="sewa">Sewa <span style="color:red;">*</span></label>
                                        <select class="select2" style="width: 100%" id="addcost_sewa">
                                        </select>
                                    </div>   

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Tanggal Berangkat <span style="color:red;">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" autocomplete="off" class="form-control" id="tanggal_berangkat" placeholder="" readonly value="">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Tujuan <span style="color:red;">*</span></label>
                                        <input  type="text" class="form-control" id="nama_tujuan" readonly> 
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        {{-- @if ($checkLTL) --}}
                                            <label for="">No. Koli</label>
                                        {{-- @else --}}
                                            <label for="">No. Kontainer</label>
                                        {{-- @endif --}}
                                        <input type="text" class="form-control" maxlength="25" id="no_kontainer"> 
                                    </div>
                                    {{-- @if ($checkLTL) --}}
                                       <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Surat Jalan</label>
                                            <input  type="text" class="form-control" maxlength="25" id="no_sj"> 
                                        </div>
                                    {{-- @else --}}
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Seal Pelayaran</label>
                                            <input  type="text" class="form-control" maxlength="25" id="no_seal"> 
                                        </div>
                                        
                                    {{-- @endif --}}
                                    
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="tarif">Tarif</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="tarif" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Add Cost</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="addcost" placeholder="" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Diskon</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="diskon" placeholder="" >
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Subtotal</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="subtotal" placeholder="" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Catatan</label>
                                        <input type="text" class="form-control" maxlength="255" id="catatan"> 
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class="table-responsive p-0 mx-3">
                                <br>
                                <form name="add_addcost_detail" id="add_addcost_detail">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-bold">Detail Add Cost</span>
                                        <button type="button" id="tambah" class="btn btn-primary btn-sm mb-2"> <i class="fa fa-plus-circle"></i> Tambah Add Cost</button>
                                    </div>
                                    <input type="hidden" id="deleted_temp" name="deleted_temp" placeholder="deleted_temp">
                                    <table class="table table-hover table-bordered table-striped text-nowrap" id="tabel_addcost">
                                        <thead>
                                            <tr class="">
                                                <th style="">Deskripsi</th>
                                                <th style="width: 120px;">Jumlah</th>
                                                <th style="width: 60px;">Ditagihkan</th>
                                                <th style="width: 60px;">Dipisahkan</th>
                                                <th style="">Catatan</th>
                                                <th style="text-align: center; vertical-align: middle; width: 50x;">#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                    <button type="button" class="btn btn-sm btn-success save_detail" id="" style='width:85px'>OK</button> 
                </div>
            </div>
            <!-- /.modal-content -->
            </div>
        </div>
    </form>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '#btnSimpan', function(){ // kalau diskon berubah, hitung total 
            var total_pisah = $('#total_pisah').val();
            if (total_pisah>0) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    title: 'Apakah anda yakin tanggal jatuh tempo reimburse sudah benar?',
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
                        $('#save').submit();
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
                
            }
            else
            {
                $('#save').submit();
            }
        });

        $('#save').submit(function(event) {
            // set value kode_customer
                var kodeValue = $('#billingTo option:selected').attr('kode');
                $('#kode_customer').val(kodeValue);
            //

            event.preventDefault(); // Prevent form submission
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

<script type="text/javascript">
    $(document).ready(function() {
        // $('#table_invoice').DataTable({ 
        //     scrollX: true 
        // }); 
        var dataSewa = <?= isset($dataSewa)? $dataSewa:NULL; ?> ;
        console.log('dataSewa', dataSewa);
        // set value default tgl invoice
        var today = new Date();
         $('#tanggal_invoice').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            startDate: today,
        }).datepicker("setDate", today);

        var selectedOption = $('#billingTo').find('option:selected');
        var ketentuan_bayar = selectedOption.attr('ketentuan_bayar');
        
        if(ketentuan_bayar==undefined){
            getDate(0);
            addCostPisah(0);
        }else{
            getDate(parseFloat(ketentuan_bayar) );
            addCostPisah(parseFloat(ketentuan_bayar));
        }

        calculateGrandTotal(); // pas load awal langsung hitung grand total

        $(document).on('keyup', '#diskon', function(){ // kalau diskon berubah, hitung total 
            var id_sewa = $('#key').val();
            hitung(); // execute fungsi hitung tiap perubahan value diskon, (tarif + addcost - diskon)
        });

        $(document).on('change', '#diskon', function(){ // kalau diskon berubah, hitung total 
            var id_sewa = $('#key').val();
            hitung(); // execute fungsi hitung tiap perubahan value diskon, (tarif + addcost - diskon)
        });

        $('body').on('change','#billingTo',function(){
            var selectedOption = $(this).find('option:selected');
            var ketentuan_bayar = selectedOption.attr('ketentuan_bayar');
            
            if(ketentuan_bayar==undefined)
            {
                getDate(0);
                addCostPisah(0);
            }
            else
            {
                getDate(parseFloat(ketentuan_bayar) );
                addCostPisah(parseFloat(ketentuan_bayar));
            }

		});

        function getDate(hari){
            var today = new Date();
            var set_hari = new Date(today);
            set_hari.setDate(today.getDate() + hari);

            $('#jatuh_tempo').datepicker({
                autoclose: false,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                // endDate: '+0d',
                startDate: today,
            }).datepicker("setDate", set_hari);

           
        }

        function addCostPisah(set_hari_jatuh_tempo){
            var total = 0;
            $(".addcost_pisah").each(function() {
                var value = parseFloat($(this).val()) || 0;
                total += value;
            });
            $("#total_pisah").val(total);

            var today = new Date();
             var set_hari = new Date(today);
            set_hari.setDate(today.getDate() + set_hari_jatuh_tempo);

            $('#jatuh_tempo_pisah').datepicker({
                autoclose: false,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                startDate: today,
            }).datepicker("setDate", set_hari);
       
            if($("#total_pisah").val()==0)
            {
                $('#jatuh_tempo_pisah_kontainer').hide();
            }
            else
            {
                $('#jatuh_tempo_pisah_kontainer').show();
            }
        }

        function calculateGrandTotal(){ // hitung grand total buat ditagihkan 
            var grandTotal = 0; 
            var grandTotalMuatan = 0; 

            var grandTotalText = document.getElementById("total_tagihan_text");

            var subtotals = document.querySelectorAll('.hitung_subtotal');
            var muatan_satuan = document.querySelectorAll('.muatan_satuan');

            muatan_satuan.forEach(function(muatan) {
                grandTotalMuatan += parseFloat(muatan.value); // Convert the value to a number
            });

            subtotals.forEach(function(subtotal) {
                grandTotal += parseFloat(subtotal.value); // Convert the value to a number
            });
            if(grandTotal && grandTotal >= 0){
                $('#total_tagihan').val(moneyMask(grandTotal));
                var total_dibayar = $('#total_dibayar').val() == 0 || $('#total_dibayar').val() == NULL? 0:$('#total_dibayar').val();
                var total_sisa = grandTotal - parseFloat( total_dibayar );
                $('#total_sisa').val( moneyMask(total_sisa) );
                grandTotalText.textContent = "Rp. " + moneyMask(grandTotal); // Change the text content of the span
            }

            if(grandTotalMuatan && grandTotalMuatan >= 0)
            {
                $('#total_jumlah_muatan').val(grandTotalMuatan);
            }
        }

        $(document).on('click', '.detail', function(){ // open detail 
            clearData(); // execute clear data dulu tiap open modal
            $('#key').val(''); // key di clear dulu
            var button_id = $(this).attr("id"); // get value id
            var key = button_id.replace("detail_", ""); // hapus teks detail_
            $('#key').val(key); // id key buat nge get data yg di hidden, key = id_sewa

            $('#tanggal_berangkat').val( $('#tgl_berangkat_hidden_'+key).val() );
            $('#nama_tujuan').val( $('#nama_tujuan_hidden_'+key).val() ); 
            $('#no_kontainer').val( $('#no_kontainer_hidden_'+key).val() ); 
            $('#no_seal').val( $('#no_seal_hidden_'+key).val() ); 
            $('#no_sj').val( $('#no_sj_hidden_'+key).val() ); 

            $('#catatan').val( $('#catatan_hidden_'+key).val() ); 
            $('#tarif').val( moneyMask($('#tarif_hidden_'+key).val()) ); 
            $('#addcost').val( moneyMask($('#addcost_hidden_'+key).val()) ); 
            $('#diskon').val( moneyMask($('#diskon_hidden_'+key).val()) ); 

             dataSewa.forEach(function(item, index) {
                var option = $('<option>');
                option.text(item.no_sewa + ' - ' + item.nama_tujuan + ' - (' + dateMask(item.tanggal_berangkat) + ')');
                option.val(item.id_sewa);
                option.attr('index', index); // Adding the 'index' attribute with the value of the index variable
                if (item.id_sewa == key) {
                    option.prop('selected', true);
                }
                $('#addcost_sewa').append(option);
            });
            // $("#addcost_sewa").prop("disabled", true); // instead of $("select").enable(false);

            showAddcostDetails(key);
            showAddcostDetailsBaru(key);
            hitung();
            $('#modal_detail').modal('show');
        });

        $(document).on('change', '#addcost_sewa', function(){ 
            let index = $("#addcost_sewa option:selected").attr('index');
            console.log('first', dataSewa[index]);
            console.log('this.value', index);
        });

        $(document).on('click', '.save_detail', function(event){ // save detail
            var key = $('#key').val(); 

            $('#addcost_hidden_'+key).val( normalize($('#addcost').val()) );

            document.querySelector(".text_addcost_"+key).textContent = moneyMask( $('#addcost').val() );

            $('#no_kontainer_hidden_'+key).val( $('#no_kontainer').val() );
            $('#no_seal_hidden_'+key).val( $('#no_seal').val() );
            $('#no_sj_hidden_'+key).val( $('#no_sj').val() );
            $('#catatan_hidden_'+key).val( $('#catatan').val() );
            $('#diskon_hidden_'+key).val( $('#diskon').val() );
            $('#subtotal_hidden_'+key).val( escapeComma($('#subtotal').val()) );

            // Set text content using JavaScript
            var elementIds = ["no_kontainer", "no_seal", "no_sj","catatan", "diskon", "subtotal"];
            elementIds.forEach(function (id) {
                // console.log(id, $('#' + id).val());
                if($('#' + id).val() !== undefined){
                    document.getElementById(id + '_text_' + key).textContent = $('#' + id).val();
                }
            });
            
            updateAddCost(key); //update data addcost yg berubah
            calculateGrandTotal(); // pas load awal langsung hitung grand total
            cekPisahInvoice();
            clearData();
            $('#modal_detail').modal('hide'); // close modal
        });
        
        function updateAddCost(key){
            let cekAddCost = $('#tabel_addcost > tbody > tr');
            var array_add_cost = [];
            var array_add_cost_baru = [];

            if (cekAddCost.length > 0) {
                $('#tabel_addcost > tbody > tr').each(function(idx) {
                    let id = $(this).attr('id_addcost');
                    let is_new = false;

                    if (id.substring(0, 2) === 'x_') { // ini data baru
                        // id = id.substring(2);
                        is_new = true;
                    }

                    if($('#addcost_deskripsi_'+id).val() != ''){
                        const is_tagih = document.getElementById('addcost_is_ditagihkan_' + id); 
                        const is_pisah = document.getElementById('addcost_is_dipisahkan_' + id); 
                        let tagih = 'N';
                        let pisah = 'N';
                        if(is_tagih.checked == true){
                            tagih = 'Y'; 
                        }
                        if(is_pisah.checked == true){
                            pisah = 'Y'; 
                        }
    
                        myjson = `{"id":${JSON.stringify(id)},
                                    "id_sewa":${JSON.stringify(key)},
                                    "deskripsi":${JSON.stringify( $('#addcost_deskripsi_'+id).val() )},
                                    "total_operasional":${JSON.stringify( normalize($('#addcost_total_operasional_'+id).val()) )},
                                    "is_ditagihkan":${JSON.stringify( tagih )},
                                    "is_dipisahkan":${JSON.stringify( pisah )},
                                    "catatan":${JSON.stringify( $('#addcost_catatan_'+id).val() )}
                                  }`; 

                        if(is_new == true){
                            var obj=JSON.parse(myjson);
                            array_add_cost_baru.push(obj);
                        }else{
                            var obj=JSON.parse(myjson);
                            array_add_cost.push(obj);
                        }
                    }
                });

                $('#detail_addcost_baru_'+key).val(JSON.stringify(array_add_cost_baru));
                $('#detail_addcost_'+key).val(JSON.stringify(array_add_cost));
                // $('#uang_jalan_'+key).val(total_biaya.toLocaleString());
            }
        }

        function showAddcostDetails(key){
            var details = $('#detail_addcost_'+key).val(); 
            // console.log('details', details);
            if (details && (details != null)) { // cek apakah ada isi detail addcost
                JSON.parse(details).forEach(function(item, index) {
                    $('#tabel_addcost > tbody:last-child').append(
                        `
                            <tr id="${index}" id_addcost="${item.id}">
                                <td>
                                    <input type="text" id="addcost_deskripsi_${item.id}" value="${item.deskripsi}" class="form-control" readonly/>
                                </td>
                                <td>
                                    <input type="text" id="addcost_total_operasional_${item.id}" id_add_cost="${item.id}" value="${moneyMask(item.total_operasional)}" class="form-control numaja uang hitungBiaya hitungAddCost" readonly />
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" class="check_tagih" id="addcost_is_ditagihkan_${item.id}" id_tagih="${item.id}" name="addcost_is_ditagihkan_${item.id}" value="TAGIH_${item.id}" ${item.is_ditagihkan == 'Y'? 'checked':''} >
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" class="check_pisah" id="addcost_is_dipisahkan_${item.id}" id_pisah="${item.id}" name="addcost_is_dipisahkan_${item.id}" value="PISAH_${item.id}" ${item.is_dipisahkan == 'Y'? 'checked':''} >
                                </td>
                                <td>
                                    <input type="text" id="addcost_catatan_${item.id}" value="${item.catatan == null? '':item.catatan}" class="form-control w-auto" />
                                </td>
                                <td>
                                </td>
                            </tr>
                        `
                    );
                });
                hitungAddCost();
            }

            $('.select2').select2({
            })
        }

        function showAddcostDetailsBaru(key){
            var details = $('#detail_addcost_baru_'+key).val(); 
            // console.log('details', details);
            if (details && (details != null)) { // cek apakah ada isi detail addcost
                JSON.parse(details).forEach(function(item, index) {
                    console.log('item', item);
                    $('#tabel_addcost > tbody:last-child').append(
                        `
                            <tr id="${item.id.substring(2)}" id_addcost="${item.id}">
                                <td>
                                    <input type="text" id="addcost_deskripsi_${item.id}" value="${item.deskripsi}" class="form-control" />
                                </td>
                                <td>
                                    <input type="text" id="addcost_total_operasional_${item.id}" id_add_cost="${item.id}" value="${moneyMask(item.total_operasional)}" class="form-control numaja uang hitungBiaya hitungAddCost"  />
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" class="check_tagih" id="addcost_is_ditagihkan_${item.id}" id_tagih="${item.id}" name="addcost_is_ditagihkan_${item.id}" value="TAGIH_${item.id}" ${item.is_ditagihkan == 'Y'? 'checked':''} >
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" class="check_pisah" id="addcost_is_dipisahkan_${item.id}" id_pisah="${item.id}" name="addcost_is_dipisahkan_${item.id}" value="PISAH_${item.id}" ${item.is_dipisahkan == 'Y'? 'checked':''} >
                                </td>
                                <td>
                                    <input type="text" id="addcost_catatan_${item.id}" value="${item.catatan == null? '':item.catatan}" class="form-control w-auto" />
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger delete" value="${item.id}"><i class="fa fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        `
                    );
                });
                hitungAddCost();
            }

            $('.select2').select2({
            })
        }

        $(document).on('click', '.check_tagih', function (event) {
            const id_tagih = this.getAttribute('id_tagih');
            var di_pisah = document.getElementById('addcost_is_dipisahkan_' + id_tagih);

            if (this.checked == false) {
                di_pisah.checked = false;
                $("#addcost_is_dipisahkan_" + id_tagih).prop("disabled", true);
            }else{
                $("#addcost_is_dipisahkan_" + id_tagih).prop("disabled", false);
            }

            hitungAddCost();
        });
        
        $(document).on('click', '.check_pisah', function (event) {
            hitungAddCost();
        });

        $(document).on('click', '#tambah', function (event) {
            let lastRow = $("#tabel_addcost > tbody tr:last");
            let id = 0;
            
            if (lastRow.length >= 0) {
                id = lastRow.attr("id");
                if(id == undefined){
                    id = 0;
                }else{
                    id++;
                }
            }
            
            $('#tabel_addcost > tbody:last-child').append(
                `
                    <tr id="${id}" id_addcost="x_${id}">
                        <td>
                            <input type="text" id="addcost_deskripsi_x_${id}" class="form-control" />
                        </td>
                        <td>
                            <input type="text" id="addcost_total_operasional_x_${id}" id_add_cost="x_${id}" class="form-control numaja uang hitungBiaya hitungAddCost" />
                        </td>
                        <td style="text-align:center;">
                            <input type="checkbox" class="check_tagih" id="addcost_is_ditagihkan_x_${id}" id_tagih="x_${id}" name="addcost_is_ditagihkan_x_${id}" >
                        </td>
                        <td style="text-align:center;">
                            <input type="checkbox" class="check_pisah" id="addcost_is_dipisahkan_x_${id}" id_pisah="x_${id}" name="addcost_is_dipisahkan_x_${id}" >
                        </td>
                        <td>
                            <input type="text" id="addcost_catatan_x_${id}" class="form-control w-auto" />
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger delete" value="x_${id}"><i class="fa fa-trash-alt"></i></button>
                        </td>
                    </tr>
                `
            );

            $("input").focusout(function () {
                this.value = this.value.toLocaleUpperCase();
            });
            $("textarea").focusout(function () {
                this.value = this.value.toLocaleUpperCase();
            });
        });

        $(document).on('click', '.delete', function (event) {
            var closestTR = this.closest('tr');
            closestTR.remove();
            hitungAddCost();

        });

        var deletedValues = [];

        $(document).on('click', '.deleteParent', function (event) {

            var closestTR = this.closest('tr');
            closestTR.remove();

            deletedValues.push(this.value);
            $('#deleted').val(deletedValues);
            // $('#deleted').val(deletedValues.join(','));
            cekPisahInvoice();
            calculateGrandTotal();
        });

        $(document).on('keyup', '.hitungBiaya', function (event) {
            hitungAddCost();
        });

        function hitung(){ // hitung tarif + addcost - diskon 
            var id_sewa = $('#key').val();
            var tarif = parseFloat($('#tarif').val().replace(/,/g, ''));
            var addcost = parseFloat($('#addcost').val().replace(/,/g, ''));
            var diskon = $('#diskon').val() == null || $('#diskon').val() == 0? 0:parseFloat($('#diskon').val().replace(/,/g, ''));

            if (diskon > (tarif + addcost) ){
                diskon = (tarif + addcost);
                $('#diskon').val(diskon);
            } 

            var subtotal = tarif + addcost - diskon;
            calculateGrandTotal();
            $('#subtotal').val(moneyMask(subtotal));
        }

        function hitungAddCost(){
            let addCost = document.querySelectorAll('.hitungAddCost');
            let total_add_cost = 0;

            addCost.forEach(function(add_cost) {
                var id = add_cost.getAttribute('id_add_cost');
                var di_tagih = document.getElementById('addcost_is_ditagihkan_' + id);
                var di_pisah = document.getElementById('addcost_is_dipisahkan_' + id);
                var add_cost = $('#addcost_total_operasional_' + id).val();
                if(di_tagih.checked == true && di_pisah.checked == false){
                    total_add_cost += normalize(add_cost);
                }
            });
            let tarif = normalize($('#tarif').val());
            $('#addcost').val(moneyMask(total_add_cost));
            $('#subtotal').val( moneyMask(tarif+total_add_cost) );
        }

        function clearData(){ // clear data sebelum buka modal 
            $('#tanggal_berangkat').val('');
            $('#nama_tujuan').val('');
            $('#no_kontainer').val('');
            $('#no_seal').val('');
            $('#no_sj').val('');
            $('#catatan').val('');
            $('#tarif').val('');
            $('#addcost').val('');
            $('#subtotal').val('');
            $('#diskon').val('');

            $('#addcost_sewa').empty();
            $('#tabel_addcost tbody').empty(); // clear tabel detail addcost di dalam modal
        }
        
        cekPisahInvoice();
        function cekPisahInvoice(){ //pisah_invoice
            $('#is_pisah_invoice').val('FALSE');

            let add_costs = document.querySelectorAll('.cek_detail_addcost');
            add_costs.forEach(function(add_cost, index){
                let data = add_cost.value;
                if(data.length > 0){
                    let parsed = JSON.parse(data);
                    parsed.forEach((element, index)  => {
                        if(element.is_ditagihkan == 'Y' && element.is_dipisahkan == 'Y'){
                            $('#is_pisah_invoice').val('TRUE');
                        }
                    });
                }
            })
            
            let add_costs_baru = document.querySelectorAll('.cek_detail_addcost_baru');
            add_costs_baru.forEach(function(add_cost_baru, index){
                let data = add_cost_baru.value;
                if(data.length > 0){
                    let parsed = JSON.parse(data);
                    parsed.forEach((element, index)  => {
                        if(element.is_ditagihkan == 'Y' && element.is_dipisahkan == 'Y'){
                            $('#is_pisah_invoice').val('TRUE');
                            console.log('SECOND', data.length);
                        }
                    });
                }
            })
        }
    });
</script>

@endsection


