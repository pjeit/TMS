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
    /* [data-select2-id="56"]{ */
    [aria-labelledby="select2-bank-container"]{
        border: 3px solid #00ff6a !important;
    }
</style>
    <form action="{{ route('pembayaran_invoice.update', [ $data['id'] ]) }}" id="save" method="POST" >
        @csrf @method('PUT')
        <div class="container-fluid">
            <div class="card radiusSendiri">
                <div class="card-header ">
                    <a href="{{ route('pembayaran_invoice.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
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
                                    </div>
                                    <input type="hidden" id="total_pisah" name="total_pisah" class="form-control uang numajaMinDesimal" value="" placeholder="total_pisah" readonly>                         
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
                                                        @foreach ($dataCust as $cust)
                                                            <option value="{{ $cust->id }}" kode="{{ $cust->kode }}" ketentuan_bayar="{{ $cust->ketentuan_bayar }}" {{ $cust->id == $data['billing_to']? 'selected':'' }}> {{ $cust->kode }} - {{ $cust->nama }}</option>
                                                        @endforeach
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
            <button type="button" class="btn btn-primary mb-2" id="tambah_sewa"> <i class="fa fa-plus-square"></i> Tambah Sewa</button>
            <table class="table table-hover table-bordered table-striped" width='100%' id="table_invoice">
                <thead>
                    <tr class="bg-white">
                        <th style="text-align:center">Customer</th>
                        <th style="text-align:center">Tujuan</th>
                        <th style="text-align:center">Driver</th>
                        <th style="text-align:center">
                            <span style="font-size: 0.8em;">
                                @if ($checkLTL)
                                <span class="is_ltl"><b>No. Koli &amp; SJ</b></span>
                                @else
                                <span class="is_ftl"><b>Kontainer &amp; Segel</b></span>
                                @endif
                            </span>
                            <input type="hidden" name="" id="checkTL" value="{{ $checkLTL }}">
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
                    $iteration = 0;
                @endphp
                {{-- @dd($data->invoiceDetails); --}}
                @isset($data)
                    @foreach ($data->invoiceDetails as $key => $item_raw)
                        @php
                            $item = $item_raw->sewa;
                        @endphp
                        <tr id="{{ $iteration }}">
                            <td> 
                                <span id="text_customer_{{ $item->id_sewa }}">{{ $item->getCustomer->nama }} </span>
                                <input type="hidden" id="hidden_id_sewa_{{ $item->id_sewa }}" name="detail[{{ $item->id_sewa }}][id_sewa]" class="all_id_sewa" value="{{ ($item->id_sewa) }}" />
                                <input type="hidden" id="hidden_id_customer_{{ $item->id_sewa }}" name="detail[{{ $item->id_sewa }}][id_customer]" value="{{ ($item->id_customer) }}" />
                                <input type="hidden" id="hidden_id_jo_{{ $item->id_sewa }}" name="detail[{{ $item->id_sewa }}][id_jo_hidden]" value="{{ $item->id_jo }}" />
                                <input type="hidden" id="hidden_id_jo_detail_{{ $item->id_sewa }}" name="detail[{{ $item->id_sewa }}][id_jo_detail_hidden]" value="{{ $item->id_jo_detail }}" />
                            </td>
                            <td> <span id="text_nama_tujuan_{{  $item->id_sewa  }}">{{ $item->nama_tujuan }}</span> <br> 
                                (<span id="text_tgl_berangkat_{{  $item->id_sewa  }}">{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}</span>)
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][nama_tujuan]' id='hidden_nama_tujuan_{{ $item->id_sewa }}' value="{{ $item->nama_tujuan }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][tgl_berangkat]' id='hidden_tgl_berangkat_{{ $item->id_sewa }}' value="{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}">
                            </td>
                            <td>
                                @php
                                    $driver = '';
                                    if($item->id_supplier){
                                        $driver = 'DRIVER REKANAN ' . '('.$item->namaSupplier.')';
                                    }else{
                                        $driver = $item->no_polisi . ' ('.$item->getKaryawan->nama_panggilan.')'; 
                                    }
                                @endphp
                                    <span id="text_driver_{{ $item->id_sewa }}">{{ $driver }}</span>
                                    <input type="hidden" name='detail[{{ $item->id_sewa }}][driver]' id='hidden_driver_{{ $item->id_sewa }}' value="{{ $driver }}">
                            </td>
                            <td>
                                <span id="text_no_kontainer_{{ $item->id_sewa }}">{{ isset($item->id_jo_detail)? $item->getJOD->no_kontainer:$item->no_kontainer }}</span> <br> 
                                <span id='text_seal_pelayaran_{{ $item->id_sewa }}'>{{ $item->seal_pelayaran }}</span> 
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][no_kontainer]' id='hidden_no_kontainer_{{ $item->id_sewa }}' value="{{ $item->no_kontainer }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][no_seal]' id='hidden_no_seal_{{ $item->id_sewa }}' value="{{ $item->seal_pelayaran }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][no_sj]' id='hidden_no_sj_{{ $item->id_sewa }}' value="{{ $item->no_surat_jalan }}">
                            </td>
                            <td style="text-align:right" id="muatan_satuan_{{ $item->id_sewa }}">
                                <span id="text_jumlah_muatan_{{ $item->id_sewa }}">{{ (isset($item->jumlah_muatan ))? number_format($item->jumlah_muatan,2)  : "-" }}</span>
                                <input type="hidden" class="muatan_satuan" name='detail[{{ $item->id_sewa }}][muatan_satuan]' id='muatan_satuan_{{ $item->id_sewa }}' value="{{(isset($item->jumlah_muatan ))?$item->jumlah_muatan : 0}}">
                            </td>
                            <td style="text-align:right">
                                <span id="text_tarif_{{ $item->id_sewa }}">{{ number_format($item->total_tarif) }}</span> 
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][tarif]' id='hidden_tarif_{{ $item->id_sewa }}' value="{{ $item->total_tarif }}">
                            </td>
                            <td style="text-align:right">
                                @php
                                    $total_addcost = 0;
                                    $total_addcost_pisah = 0;
                                @endphp
                                @foreach ($item->sewaOperasional as $i => $oprs)
                                    @if ($oprs->is_aktif == 'Y' && $oprs->is_ditagihkan == 'Y' && $oprs->is_dipisahkan == 'N')
                                        <input type="hidden" class="addcost_{{ $item->id_sewa }} {{ $oprs->deskripsi }}" value="{{ $oprs->total_dicairkan }}">
                                        @php
                                            $total_addcost += $oprs->total_dicairkan;
                                        @endphp
                                        {{-- SUDAH DICAIRKAN --}}
                                        {{-- TAGIHKAN DI INVOICE --}}
                                    @elseif ($oprs->is_aktif == 'Y' && $oprs->is_ditagihkan == 'Y' && $oprs->is_dipisahkan == 'Y')
                                        @php
                                            $total_addcost_pisah += $oprs->total_dicairkan;
                                        @endphp
                                    @endif
                                @endforeach
                                <span id="text_addcost_{{ $item->id_sewa }}">{{ number_format($total_addcost) }}</span>
                                <input type="hidden" class="cek_detail_addcost" id_sewa="{{ $item->id_sewa }}" name="detail[{{ $item->id_sewa }}][addcost_details]" id="detail_addcost_{{ $item->id_sewa }}" value="{{ json_encode($item->sewaOperasional) }}" />
                                <input type="hidden" class="cek_detail_addcost_baru" id_sewa="{{ $item->id_sewa }}" name="detail[{{ $item->id_sewa }}][addcost_baru]" id="detail_addcost_baru_{{ $item->id_sewa }}" value="" />
                                <input type="hidden" class="addcost_{{ $item->id_sewa }} " name='detail[{{ $item->id_sewa }}][addcost]' id='addcost_hidden_{{ $item->id_sewa }}' value="{{ $total_addcost }}">
                                <input type="hidden" class="addcost_pisah addcost_pisah_{{ $item->id_sewa }} {{ $item->deskripsi }}" name='detail[{{ $item->id_sewa }}][addcost_pisah]' id='addcost_pisah_hidden_{{ $item->id_sewa }}' value="{{ $total_addcost_pisah }}">
                            </td>
                            <td style="text-align:right">
                                <span id='text_diskon_{{ $item->id_sewa }}'>{{ number_format($item_raw->diskon) }}</span>
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][diskon]' id='hidden_diskon_{{ $item_raw->id_sewa }}' value="{{ $item_raw->diskon }}">
                            </td>
                            <td style="text-align:right">
                                <span id='text_subtotal_{{ $item->id_sewa }}'>{{ number_format($total_addcost+$item->total_tarif) }}</span>
                                <input type="hidden" class="hitung_subtotal subtotal_hidden_{{ $item->id_sewa }} {{ $item->deskripsi }}" name='detail[{{ $item->id_sewa }}][subtotal]' id='subtotal_hidden_{{ $item->id_sewa }}' value="{{ $total_addcost+$item->total_tarif }}">
                            </td>
                            {{-- edwin --}}
                            <td>
                                <span id="text_catatan_{{ $item->id_sewa }}">{{ $item->catatan }}</span>
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][catatan]' id='hidden_catatan_{{ $item->id_sewa }}' value="{{ $item->catatan }}">
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="button" name="detail" id="detail_{{$item->id_sewa}}" class="detail dropdown-item" value="{{ $item->id_sewa }}"> 
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
                        @php
                            $iteration++;
                        @endphp
                    @endforeach
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
                        <input type="hidden" id="key"> {{--* dipakai buat simpen id_sewa --}}

                        <div class='row'>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="sewa">Sewa <span style="color:red;">*</span></label>
                                        <select class="select2" style="width: 100%" id="addcost_sewa">
                                        </select>
                                        <input type="hidden" id="is_berubah" placeholder="is berubah">
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
                                        @if ($checkLTL)
                                            <label class="is_ltl">No. Koli</label>
                                        @else
                                            <label class="is_ftl">No. Kontainer</label>
                                        @endif
                                        <input type="text" class="form-control" maxlength="25" id="no_kontainer"> 
                                    </div>
                                    @if ($checkLTL)
                                       <div class="form-group col-lg-6 col-md-6 col-sm-12 is_ltl">
                                            <label for="">Surat Jalan</label>
                                            <input  type="text" class="form-control" maxlength="25" id="no_sj"> 
                                        </div>
                                    @else
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12 is_ftl">
                                            <label for="">Seal Pelayaran</label>
                                            <input  type="text" class="form-control" maxlength="25" id="no_seal"> 
                                        </div>
                                    @endif
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
                                        <input type="hidden" class="form-control numaja uang" id="addcost_pisah" >
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
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div id="is_bank" class="mb-2 ">
                                                <select name="bank" class="select2" style="width: 200px; border: 3px solid #f239;" id="bank">
                                                    <option value="">─ Pilih Kas ─</option>
                                                    @foreach ($bank as $item)
                                                        <option value="{{ $item->id }}" {{ $item->id == 1? 'selected':'' }}>{{ $item->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <button type="button" id="tambah" class="btn btn-primary btn-sm mb-2 ml-3"> <i class="fa fa-plus-circle"></i> Tambah Add Cost</button>
                                            </div>
                                        </div>
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
                                        <tbody id="hasil_addcost">
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                    <button type="button" class="btn btn-sm btn-success save_detail" id="save_detail" style='width:85px'>OK</button> 
                    <button type="button" class="btn btn-sm btn-success save_sewa_baru" id="save_sewa_baru" style='width:85px'>Simpan</button> 
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
            console.log('total', total);
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
            $('#is_berubah').val(''); // key di clear dulu
            var button_id = $(this).attr("id"); // get value id
            var key = button_id.replace("detail_", ""); // hapus teks detail_
            var hidden_id_sewa = $('#hidden_id_sewa_'+this.value).val(); 
            let thisVal = this.value.toString();

            $('#key').val(key); // id key buat nge get data yg di hidden, key = id_sewa
            $("#save_detail").show();
            $("#save_sewa_baru").hide();

            $('#tanggal_berangkat').val( $('#hidden_tgl_berangkat_'+key).val() );
            $('#nama_tujuan').val( $('#hidden_nama_tujuan_'+key).val() ); 
            $('#no_kontainer').val( $('#hidden_no_kontainer_'+key).val() ); 
            $('#no_seal').val( $('#hidden_no_seal_'+key).val() ); 
            $('#no_sj').val( $('#hidden_no_sj_'+key).val() ); 
            $('#catatan').val( $('#hidden_catatan_'+key).val() ); 
            $('#tarif').val( moneyMask($('#hidden_tarif_'+key).val()) ); 
            $('#addcost').val( moneyMask($('#addcost_hidden_'+key).val()) ); 
            $('#addcost_pisah').val( moneyMask($('#addcost_pisah_hidden_'+key).val()) ); 
            $('#diskon').val( !isNaN($('#hidden_diskon_'+key).val())? moneyMask($('#hidden_diskon_'+key).val()):'' ); 

            let all_id_sewa = [];

            $('.all_id_sewa').each(function() {
                all_id_sewa.push($(this).val());
            });
            
            dataSewa.forEach(function(item, index) {
                var option = $('<option>');
                option.text(item.no_sewa + ' - ' + item.nama_tujuan + ' - (' + dateMask(item.tanggal_berangkat) + ')');
                option.val(item.id_sewa);
                option.attr('index', index); // Adding the 'index' attribute with the value of the index variable
                if ( all_id_sewa.includes( item.id_sewa.toString() ) ) {
                    option.prop('disabled', true);
                }
                if (item.id_sewa == hidden_id_sewa) {
                    option.prop('selected', true);
                    option.prop('disabled', false);
                }
                
                $('#addcost_sewa').append(option);
            });
            // $("#addcost_sewa").prop("disabled", true); // instead of $("select").enable(false);

            showAddcostDetails(key, 'detail');
            showAddcostDetailsBaru(key);
            hitung();
            $('#modal_detail').modal('show');
        });

        $(document).on('change', '#addcost_sewa', function(){ 
            let index = $("#addcost_sewa option:selected").attr('index');
            let selected_sewa = $("#addcost_sewa").val();
            let key = $('#key').val();
            if(key != selected_sewa){
                $('#is_berubah').val('TRUE');
            }else{
                $('#is_berubah').val('FALSE');
            }

            $('#tabel_addcost tbody').empty();

            updateDetail(index);
        });

        function updateDetail(index){
            const data = dataSewa[index];

            $('#tanggal_berangkat').val( dateMask(data.tanggal_berangkat) );
            $('#nama_tujuan').val( data.nama_tujuan ); 
            $('#no_kontainer').val( data.no_kontainer ); 
            $('#no_seal').val( data.seal_pelayaran ); 
            $('#no_sj').val( data.no_surat_jalan ); 
            $('#catatan').val( data.catatan ); 
            $('#tarif').val( moneyMask(data.total_tarif) ); 
            $('#addcost').val(''); 
            $('#diskon').val(''); 
            if(data.sewa_operasional.length > 0){
                showAddcostDetails(index, 'update');
            }
        }

        $(document).on('click', '.save_detail', function(event){ // save detail
            var key = $('#key').val(); 
            var is_berubah = $('#is_berubah').val(); 
            var addcost_sewa = $('#addcost_sewa').val();

            if(addcost_sewa != key){
                save_sewa('edit', key);
            }
            var selectedOption = $('#billingTo').find('option:selected');
            var ketentuan_bayar = selectedOption.attr('ketentuan_bayar');
            
            updateSewa(key);
            updateAddCost(key); //update data addcost yg berubah

            if(ketentuan_bayar==undefined){
                getDate(0);
                addCostPisah(0);
            }else{
                getDate(parseFloat(ketentuan_bayar) );
                addCostPisah(parseFloat(ketentuan_bayar));
            }

            calculateGrandTotal(); // pas load awal langsung hitung grand total
            cekPisahInvoice();
            clearData();

            $('#modal_detail').modal('hide'); // close modal
        });

        $(document).on('click', '#tambah_sewa', function (event) {
            clearData(); // execute clear data dulu tiap open modal
            $('#key').val(''); // key di clear dulu
            $('#is_berubah').val(''); // key di clear dulu
            $("#save_detail").hide();
            $("#save_sewa_baru").show();

            var option = $('<option>');
            option.text('─ Pilih Sewa ─');
            option.val('');
            option.prop('selected', true);
            $('#addcost_sewa').append(option);

            let all_id_sewa = [];
            $('.all_id_sewa').each(function() {
                all_id_sewa.push($(this).val());
            });

            dataSewa.forEach(function(item, index) {
                var option = $('<option>');
                option.text(item.no_sewa + ' - ' + item.nama_tujuan + ' - (' + dateMask(item.tanggal_berangkat) + ')');
                option.val(item.id_sewa);
                option.attr('index', index); // Adding the 'index' attribute with the value of the index variable
                if ( all_id_sewa.includes( item.id_sewa.toString() ) ) {
                    option.prop('disabled', true);
                }
                
                $('#addcost_sewa').append(option);
            });

            hitung();
            $('#modal_detail').modal('show');
        });

        $(document).on('click', '#save_sewa_baru', function(event){ // save detail
            save_sewa('baru', null);

            $('#modal_detail').modal('hide'); // close modal
        });

        function save_sewa(jenis, key){
            let index = $("#addcost_sewa option:selected").attr('index');
            const data = dataSewa[index];
            let id = 0;

            if(jenis == 'baru'){
                let lastRow = $("#table_invoice > tbody tr:last");
                
                if (lastRow.length >= 0) {
                    id = lastRow.attr("id");
                    if(id == undefined){
                        id = 0;
                    }else{
                        id++;
                    }
                }

                // var appendedTable = $('#table_invoice > tbody:last-child');
            }else{
                var closestTR = $('#hidden_id_sewa_'+key).closest('tr');
                var attr = closestTR.attr('id');
                closestTR.empty();

                // const trElement = $('#'+attr);
                // const newTd = $('<td>New Data</td>');
                // trElement.append(newTd);
            }
            
            var td = `
                        <td> 
                            <span id="text_customer_${data.id_sewa}">CV. SINAR TERANG </span>
                            <input type="hidden" id="hidden_id_sewa_${data.id_sewa}" name="detail[${data.id_sewa}][id_sewa]" class="all_id_sewa" value="${data.id_sewa}">
                            <input type="hidden" id="hidden_id_customer_${data.id_sewa}" name="detail[${data.id_sewa}][id_customer]" value="${data.id_customer}">
                            <input type="hidden" id="hidden_id_jo_${data.id_sewa}" name="detail[${data.id_sewa}][id_jo_hidden]" value="${data.id_jo}">
                            <input type="hidden" id="hidden_id_jo_detail_${data.id_sewa}" name="detail[${data.id_sewa}][id_jo_detail_hidden]" value="${data.id_jo_detail}">
                        </td>
                        <td> <span id="text_nama_tujuan_${data.id_sewa}">${data.nama_tujuan}</span> <br> 
                            ( <span id="text_tgl_berangkat_${data.id_sewa}">${dateMask(data.tanggal_berangkat)}</span> )
                            <input type="hidden" name="detail[${data.id_sewa}][nama_tujuan]" id="hidden_nama_tujuan_${data.id_sewa}" value="${data.nama_tujuan}">
                            <input type="hidden" name="detail[${data.id_sewa}][tgl_berangkat]" id="hidden_tgl_berangkat_${data.id_sewa}" value="${dateMask(data.tanggal_berangkat)}">
                        </td>
                        <td>
                            <span id="text_driver_${data.id_sewa}">${data.nama_driver}</span>
                            <input type="hidden" name="detail[${data.id_sewa}][driver]" id="hidden_driver_${data.id_sewa}" value="${data.nama_driver}">
                        </td>
                        <td>
                            <span id="text_no_kontainer_${data.id_sewa}">${data.no_kontainer}</span> <br> 
                            <span id="text_seal_pelayaran_${data.id_sewa}">${data.seal_pelayaran}</span> 
                            <input type="hidden" name="detail[${data.id_sewa}][no_kontainer]" id="hidden_no_kontainer_${data.id_sewa}" value="${data.no_kontainer}">
                            <input type="hidden" name="detail[${data.id_sewa}][no_seal]" id="hidden_no_seal_${data.id_sewa}" value="${data.seal_pelayaran}">
                            <input type="hidden" name="detail[${data.id_sewa}][no_sj]" id="hidden_no_sj_${data.id_sewa}" value="${data.no_surat_jalan}">
                        </td>
                        <td style="text-align:right" id="muatan_satuan_${data.id_sewa}">
                            <span id="text_jumlah_muatan_${data.id_sewa}">${ data.jumlah_muatan != null? data.jumlah_muatan:'-' }</span>
                            <input type="hidden" class="muatan_satuan" name="detail[${data.id_sewa}][muatan_satuan]" id="muatan_satuan_${data.id_sewa}" value="${data.jumlah_muatan != null? data.jumlah_muatan:'-'}">
                        </td>
                        <td style="text-align:right">
                            <span id="text_tarif_${data.id_sewa}">${moneyMask(data.total_tarif)}</span> 
                            <input type="hidden" name="detail[${data.id_sewa}][tarif]" id="hidden_tarif_${data.id_sewa}" value="${data.total_tarif}">
                        </td>
                        <td style="text-align:right">
                            <span id="text_addcost_${data.id_sewa}">${ $('#addcost').val() }</span>
                            <input type="hidden" class="cek_detail_addcost" id_sewa="${data.id_sewa}" name="detail[${data.id_sewa}][addcost_details]" id="detail_addcost_${data.id_sewa}" value="">
                            <input type="hidden" class="cek_detail_addcost_baru" id_sewa="${data.id_sewa}" name="detail[${data.id_sewa}][addcost_baru]" id="detail_addcost_baru_${data.id_sewa}" value="">
                            <input type="hidden" class="addcost_${data.id_sewa}" name='detail[${data.id_sewa}][addcost]' id='addcost_hidden_${data.id_sewa}' value="">
                            <input type="hidden" class="addcost_pisah addcost_pisah_${data.id_sewa}" name="detail[${data.id_sewa}][addcost_pisah]" id="addcost_pisah_hidden_${data.id_sewa}" value="">
                        </td>
                        <td style="text-align:right">
                            <span id="text_diskon_${data.id_sewa}">${ $('#diskon').val() }</span>
                            <input type="hidden" name="detail[${data.id_sewa}][diskon]" id="hidden_diskon_${data.id_sewa}">
                        </td>
                        <td style="text-align:right">
                            <span id="text_subtotal_${data.id_sewa}">${ $('#subtotal').val() }</span>
                            <input type="hidden" class="hitung_subtotal subtotal_hidden_${data.id_sewa} " name="detail[${data.id_sewa}][subtotal]" id="subtotal_hidden_${data.id_sewa}" value="">
                        </td>
                        <td><span id="text_catatan_${data.id_sewa}"> ${ $('#catatan').val() } </span>
                            <input type="hidden" name="detail[${data.id_sewa}][catatan]" id="hidden_catatan_${data.id_sewa}" value="">
                        </td>
                        <td>
                            <div class="btn-group dropleft">
                                <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-list"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <button type="button" name="detail" id="detail_${data.id_sewa}" class="detail dropdown-item" value="${data.id_sewa}"> 
                                        <span class="fas fa-edit mr-3"></span> Detail
                                    </button>
                                    <button type="button" class="dropdown-item deleteParent" value="${data.id_sewa}">
                                        <span class="fas fa-trash mr-3"></span> Delete
                                    </button>
                                </div>
                            </div>
                        </td>
            `;
    
            if(jenis == 'baru'){
                $('#table_invoice > tbody:last-child').append(
                    `
                        <tr id="${id}">
                            `+
                            td
                            +`
                        </tr>
                    `
                );
            }else{
                const trElement = $('#'+attr);
                trElement.append(td);
            }

            $('#addcost_hidden_'+data.id_sewa).val( normalize($('#addcost').val()) );
            $('#addcost_pisah_hidden_'+data.id_sewa).val( normalize($('#addcost_pisah').val()) );
            
            let total = 0;
            $(".addcost_pisah").each(function() {
                var value = parseFloat($(this).val()) || 0;
                total += value;
            });
            $("#total_pisah").val(total);

            updateAddCost(data.id_sewa); //update data addcost yg berubah
            calculateGrandTotal(); // pas load awal langsung hitung grand total
            cekPisahInvoice();
            clearData();
        }

        function updateSewa(key){
            let selected_sewa = $('#addcost_sewa').val();
            let selected_index = $("#addcost_sewa option:selected").attr('index');
            let data = dataSewa[selected_index];

            document.getElementById("text_nama_tujuan_"+key).textContent = data.nama_tujuan;
            document.getElementById("text_tgl_berangkat_"+key).textContent = dateMask(data.tanggal_berangkat);
            document.getElementById("text_driver_"+key).textContent = data.nama_driver != null? data.no_polisi + ' ' + data.nama_driver:'DRIVER REKANAN';
            document.getElementById("text_no_kontainer_"+key).textContent = data.no_kontainer;
            document.getElementById("text_seal_pelayaran_"+key).textContent = data.seal_pelayaran;
            document.getElementById("text_jumlah_muatan_"+key).textContent = data.jumlah_muatan != null? data.jumlah_muatan:'-';
            document.getElementById("text_addcost_"+key).textContent = $('#addcost').val();
            document.getElementById("text_diskon_"+key).textContent = $('#diskon').val();
            document.getElementById("text_subtotal_"+key).textContent = $('#subtotal').val();
            document.getElementById("text_catatan_"+key).textContent = $('#catatan').val();

            $('#addcost_hidden_'+key).val( normalize($('#addcost').val()) );
            $('#addcost_pisah_hidden_'+key).val( normalize($('#addcost_pisah').val()) );
            $('#hidden_id_sewa_'+key).val( data.id_sewa );
            $('#hidden_id_jo_'+key).val( data.id_jo );
            $('#hidden_id_jo_detail_'+key).val( data.id_jo_detail );
            $('#subtotal_hidden_'+key).val( normalize($('#subtotal').val()) );
            $('#muatan_satuan_'+key).val( data.jumlah_muatan != null? data.jumlah_muatan:'0' );
            $('#hidden_nama_tujuan_'+key).val( data.nama_tujuan );
            $('#hidden_driver_'+key).val( data.nama_driver != null? data.no_polisi + ' ' + data.nama_driver:'DRIVER REKANAN' );
            $('#hidden_tgl_berangkat_'+key).val( dateMask(data.tanggal_berangkat) );
            $('#hidden_no_kontainer_'+key).val( data.no_kontainer );
            $('#hidden_no_seal_'+key).val( data.seal_pelayaran );
            $('#hidden_tarif_'+key).val( data.total_tarif );
            $('#hidden_catatan_'+key).val( $('#catatan').val() );
            $('#hidden_diskon_'+key).val( !isNaN(parseFloat($('#diskon').val()))? normalize($('#diskon').val()):0 );
            
        } 
        
        function updateAddCost(key){
            let id_sewa = $('#hidden_id_sewa_'+key).val();
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
                                    "id_sewa":${JSON.stringify(id_sewa)},
                                    "deskripsi":${JSON.stringify( $('#addcost_deskripsi_'+id).val() )},
                                    "total_dicairkan":${JSON.stringify( normalize($('#addcost_total_dicairkan_'+id).val()) )},
                                    "is_ditagihkan":${JSON.stringify( tagih )},
                                    "is_dipisahkan":${JSON.stringify( pisah )},
                                    "catatan":${JSON.stringify( $('#addcost_catatan_'+id).val() )}
                                }`; 

                        if(is_new == true){
                            var obj = JSON.parse(myjson);
                            array_add_cost_baru.push(obj);
                        }else{
                            var obj = JSON.parse(myjson);
                            array_add_cost.push(obj);
                        }
                    }
                });

                $('#detail_addcost_baru_'+key).val(JSON.stringify(array_add_cost_baru));
                $('#detail_addcost_'+key).val(JSON.stringify(array_add_cost));
                // $('#uang_jalan_'+key).val(total_biaya.toLocaleString());
            }
        }

        function showAddcostDetails(key, type){
            const data = dataSewa[key];
            let details = '';
            let oprs = '';

            if(type == 'detail'){
                details = $('#detail_addcost_'+key).val(); 
                oprs = JSON.parse(details);
            }else if(type == 'update'){
                details = data.sewa_operasional; 
                oprs = details;
            }

            if (details && (details != null)) { // cek apakah ada isi detail addcost
                oprs.forEach(function(item, index) {
                    let is_readonly = '';
                    let exclude_array = ['TL', 'ALAT', 'TALLY', 'SEAL PELAYARAN'];
                    if(exclude_array.includes(item.deskripsi)){
                        is_readonly = 'readonly';
                    }
                    let isDisabledLTL = item.deskripsi == 'ALAT'? 'disabled':'';
                    $('#tabel_addcost > tbody:last-child').append(
                        `
                            <tr id="${index}" id_addcost="${item.id}">
                                <td>
                                    <input type="text" id="addcost_deskripsi_${item.id}" value="${item.deskripsi}" title="${item.deskripsi}" class="form-control" readonly/>
                                </td>
                                <td>
                                    <input type="text" id="addcost_total_dicairkan_${item.id}" id_add_cost="${item.id}" value="${moneyMask(item.total_dicairkan)}" class="form-control numaja uang hitungBiaya hitungAddCost" ${is_readonly} />
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" class="check_tagih" id="addcost_is_ditagihkan_${item.id}" id_tagih="${item.id}" name="addcost_is_ditagihkan_${item.id}" value="TAGIH_${item.id}" ${item.is_ditagihkan == 'Y'? 'checked':''} ${isDisabledLTL} >
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" class="check_pisah" id="addcost_is_dipisahkan_${item.id}" id_pisah="${item.id}" name="addcost_is_dipisahkan_${item.id}" value="PISAH_${item.id}" ${item.is_dipisahkan == 'Y'? 'checked':''} ${item.is_ditagihkan == 'N'? 'disabled':''}>
                                </td>
                                <td>
                                    <input type="text" id="addcost_catatan_${item.id}" value="${item.catatan == null? '':item.catatan}" class="form-control w-auto" title="${item.catatan != null? item.catatan:''}" ${item.catatan == 'PENCAIRAN DI UANG JALAN'? 'readonly':''}/>
                                </td>
                                <td>
                                </td>
                            </tr>
                        `
                    );
                });
                hitungAddCost();
            }
        }

        function showAddcostDetailsBaru(key){
            var details = $('#detail_addcost_baru_'+key).val(); 
            if (details && (details != null)) { // cek apakah ada isi detail addcost
                JSON.parse(details).forEach(function(item, index) {
                    $('#tabel_addcost > tbody:last-child').append(
                        `
                            <tr id="${item.id.substring(2)}" id_addcost="${item.id}">
                                <td>
                                    <input type="text" id="addcost_deskripsi_${item.id}" value="${item.deskripsi}" title="${item.deskripsi}" class="form-control" />
                                </td>
                                <td>
                                    <input type="text" id="addcost_total_dicairkan_${item.id}" id_add_cost="${item.id}" value="${moneyMask(item.total_dicairkan)}" class="form-control numaja uang hitungBiaya hitungAddCost"  />
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" class="check_tagih" id="addcost_is_ditagihkan_${item.id}" id_tagih="${item.id}" name="addcost_is_ditagihkan_${item.id}" value="TAGIH_${item.id}" ${item.is_ditagihkan == 'Y'? 'checked':''} >
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" class="check_pisah" id="addcost_is_dipisahkan_${item.id}" id_pisah="${item.id}" name="addcost_is_dipisahkan_${item.id}" value="PISAH_${item.id}" ${item.is_dipisahkan == 'Y'? 'checked':''} >
                                </td>
                                <td>
                                    <input type="text" id="addcost_catatan_${item.id}" value="${item.catatan == null? '':item.catatan}" class="form-control w-auto" title="${item.catatan != null? item.catatan:''}" ${item.catatan == 'PENCAIRAN DI UANG JALAN'? 'readonly':''}/>
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
                            <input type="text" id="addcost_total_dicairkan_x_${id}" id_add_cost="x_${id}" class="form-control numaja uang hitungBiaya hitungAddCost" />
                        </td>
                        <td style="text-align:center;">
                            <input type="checkbox" class="check_tagih" id="addcost_is_ditagihkan_x_${id}" id_tagih="x_${id}" name="addcost_is_ditagihkan_x_${id}" >
                        </td>
                        <td style="text-align:center;">
                            <input type="checkbox" class="check_pisah" id="addcost_is_dipisahkan_x_${id}" id_pisah="x_${id}" name="addcost_is_dipisahkan_x_${id}" disabled>
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
            let total_add_cost_pisah = 0;

            addCost.forEach(function(add_cost) {
                var id = add_cost.getAttribute('id_add_cost');
                var di_tagih = document.getElementById('addcost_is_ditagihkan_' + id);
                var di_pisah = document.getElementById('addcost_is_dipisahkan_' + id);
                var add_cost = $('#addcost_total_dicairkan_' + id).val();
                if(di_tagih.checked == true && di_pisah.checked == false){
                    total_add_cost += normalize(add_cost);
                }
                if(di_tagih.checked == true && di_pisah.checked == true){
                    total_add_cost_pisah += normalize(add_cost);
                }
            });
            let tarif = normalize($('#tarif').val());
            $('#addcost').val(moneyMask(total_add_cost));
            $('#addcost_pisah').val(moneyMask(total_add_cost_pisah));
            $('#subtotal').val( moneyMask(tarif+total_add_cost) );
        }

        function clearData(){ // clear data sebelum buka modal 
            $('#tanggal_berangkat').val('');
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
                        }
                    });
                }
            })
        }
    });
</script>

@endsection


