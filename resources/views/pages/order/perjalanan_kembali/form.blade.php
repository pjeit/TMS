
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
    <form action="{{ route('perjalanan_kembali.update',[$sewa->id_sewa]) }}" id="post_data" method="POST" >
      @csrf
        @method('PUT')


        <div class="row m-2">
        
            <div class="col-12">
                <div class="card radiusSendiri ">
                    <div class="card-header ">
                        <a href="{{ route('perjalanan_kembali.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        <span style="font-size:11pt;" class="badge bg-dark float-right m-2">{{$sewa->jenis_order}} ORDER {{$sewa->jenis_tujuan}}</span>
                    </div>
                    <div class="card-body" >
                        {{-- <div class="d-flex" style="gap: 20px;width:100%;"> --}}
                            <div class="row">
                                <div class="col-6" style=" border-right: 1px solid rgb(172, 172, 172);">
                                   <div class="form-group ">
                                       <label for="tanggal_pencairan">Tanggal Berangkat<span style="color:red">*</span></label>
                                       <div class="input-group mb-0">
                                           <div class="input-group-prepend">
                                           <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                           </div>
                                           <input disabled type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse($sewa->tanggal_berangkat)->format('d-M-Y')}}">
                                       </div>
                                   </div>  
   
                                   <div class="form-group ">
                                       <label for="no_akun">Customer</label>
                                       <input type="text" id="customer" name="customer" class="form-control" value="{{$sewa->nama_cust}}" readonly>                         
                                   </div>  
   
                                   <div class="form-group ">
                                       <label for="no_akun">Tujuan</label>
                                       <input type="text" id="tujuan" name="tujuan" class="form-control" value="{{$sewa->nama_tujuan}}" readonly>                         
                                   </div>  
   
                                    <div class="form-group ">
                                       <label for="no_akun">Catatan</label>
                                       <input type="text" id="catatan" name="catatan" class="form-control" value="{{$sewa->catatan}}" >                         
                                   </div> 


                                </div>
                                <div class="col-6">
                                       <div class="row">
                                           {{-- <div class="form-group col-12">
                                               Data Kendaraan
                                            <hr>
           
                                           </div> --}}
                                           <div class="form-group col-6">
                                               <label for="no_akun">Kendaraan</label>
                                               <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{$sewa->no_polisi}}" readonly>                         
                                           </div>  
           
                                           {{-- @if ($sewa->supir) --}}
                                           <div class="form-group col-6">
                                               <label for="no_akun">Driver</label>
                                               <input type="text" id="driver" name="driver" class="form-control" value="{{$sewa->supir}} ({{$sewa->telpSupir}})" readonly>     
                                               <input type="hidden" name="id_karyawan" id="id_karyawan">                    
                                           </div> 
                                           {{-- @endif --}}
    
                                       </div>
                                       
                                        <div class="form-group">
                                            <label for="no_akun">No. Kontainer</label>
                                            @if ($sewa->no_kontainer_jod&&$sewa->jenis_order =="INBOUND")
                                                <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" readonly value="{{$sewa->no_kontainer_jod}}" >                         
                                            @else
                                                <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" value="{{$sewa->no_kontainer}}" >                         

                                            @endif
                                        </div> 
                                         @if ($sewa->seal_pelayaran_jod&&$sewa->jenis_order =="INBOUND")
                                            <div class="form-group ">
                                                <label for="seal">Segel Kontainer</label>
                                                <input readonly type="text" id="seal" name="seal" class="form-control"value="{{$sewa->seal_pelayaran_jod}}" >
                                            </div> 
                                        @endif
                                       
                                        <div class="form-group">
                                            <label for="tanggal_pencairan">Tgl. Kembali Surat Jalan<span style="color:red">*</span></label>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                     <span class="input-group-text"><input {{$sewa->is_kembali=='N'?'':'checked'}} type="checkbox" name="check_is_kembali" id="check_is_kembali"></span>
                                                </div>
                                                <input type="hidden" id="is_kembali" name='is_kembali' value="{{$sewa->is_kembali}}">
                                                <input {{$sewa->is_kembali=='N'?'disabled':''}} type="text" autocomplete="off" name="tanggal_kembali" class="form-control date" id="tanggal_kembali" placeholder="dd-M-yyyy" value="{{$sewa->is_kembali=='Y'?\Carbon\Carbon::parse($sewa->tanggal_kembali)->format('d-M-Y'):''}}">
                                            </div>
                                        </div> 
        
                                        <div class="form-group">
                                            <label for="no_akun">No. Surat Jalan</label>
                                            <input type="text" id="surat_jalan" name="surat_jalan" class="form-control" value="{{$sewa->no_surat_jalan}}" >                         
                                        </div> 
                                        <input type="hidden" name="id_jo_detail_hidden" id="id_jo_detail_hidden" value="{{$sewa->id_jo_detail}}">
                                        <input type="hidden" name="add_cost_hidden" id="add_cost_hidden">
                                        <input type="hidden" id='jenis_tujuan' value='{{$sewa->jenis_tujuan}}'>

                                        @if ($sewa->jenis_order =="OUTBOUND")
                                        <div class="row" name="div_segel" id="div_segel">
                                            <div class="form-group col-6">
                                                <label for="seal">Seal</label>
                                                <input type="text" id="seal" name="seal" class="form-control"value="{{$sewa->seal_pelayaran}}" >
                                            </div> 
            
                                            <div class="form-group col-6">
                                                <label for="seal_pje">Seal PJE<span style="color:red">*</span></label>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                            <span class="input-group-text"><input {{$sewa->seal_pje?'checked':''}}type="checkbox" name="cek_seal_pje" id="cek_seal_pje"></span>
                                                    </div>
                                                <input readonly {{$sewa->seal_pje?'':'readonly'}}type="text" name="seal_pje" class="form-control" id="seal_pje" value="{{$sewa->seal_pje}}">
                                                </div>
                                            </div> 
                                        </div> 

                                        @endif

                                        <div class="row" name="lcl_selected" id="lcl_selected" >
                                            <div class="col-4 col-md-12 col-lg-4">
                                                <label for="muatan_ltl">Jumlah Muatan<span style="color:red;">*</span></label>
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        <input readonly type="text" class="form-control numajaCheckDesimal" name="muatan_ltl"
                                                            id="muatan_ltl">
                                                        <div class="input-group-append">
                                                            <div class="input-group-text">Kg</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-8 col-md-12 col-lg-8">
                                                <label for="total_harga_lcl">Total Harga</label>
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp.</span>
                                                        </div>
                                                        <input type="text" class="form-control numaja uang" name="total_harga_lcl"
                                                            id="total_harga_lcl" readonly>
                                                        <input type="hidden" id="min_muatan"
                                                            value='{{isset($sewa->min_muatan)?$sewa->min_muatan:''}}'>
                                                        <input type="hidden" id="harga_per_kg"
                                                            value='{{isset($sewa->harga_per_kg)?$sewa->harga_per_kg:''}} '>
                                                    </div>
                                                </div>
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
                                <span class="badge badge-success">Data Yang Tersimpan</span>
                                <span class="badge badge-danger">Data Template</span>
                                <span class="badge badge-primary">Data Lain-lain</span>
                                <span class="badge badge-warning">Data S/D/T</span>
                                <span class="badge badge-warning">Data Tujuan Biaya</span>


                        <button type="button" id="btnTmbh" class="btn btn-primary radiusSendiri float-right">Tambah Biaya <i class="fa fa-fw fa-plus"></i> </button></br>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered card-outline card-primary table-hover" id="sortable" >
                              <thead>
                                  <tr>
                                      <th colspan="7">BIAYA PERJALANAN</th>
                                  </tr>
                                <tr>
                                    <th style="width: 30px;">
                                        {{-- <div class="icheck-success d-inline">
                                            <input type="checkbox" id="checkboxSemua" class="centang_cekbox_semua" >
                                            <label for="checkboxSemua"></label>
                                        </div> --}}
                                    </th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah</th>
                                    <th>Ditagihkan</th>
                                    <th>Dipisahkan</th>
                                    <th>Catatan</th>
                                    <th></th>
                                </tr>
                              </thead>
                              <tbody id="tampunganTabel">
                                @php
                                    $index=0;
                                    $flagCleaning=false;
                                    $flagInap=false;
                                @endphp
                                @if (isset($dataOpreasional))
                                @php
                                     foreach ($dataOpreasional as $key => $value) {
                                         if( $value->deskripsi== 'CLEANING/REPAIR' )
                                            {
                                                //FLAG KALO KETEMU KELUAR LOOPING
                                                $flagCleaning = true;
                                                break;
                                            }
                                    }
                                     foreach ($dataOpreasional as $key => $value) {
                                         if( $value->deskripsi== 'INAP' )
                                            {
                                                //FLAG KALO KETEMU KELUAR LOOPING
                                                $flagInap = true;
                                                break;
                                            }
                                    }
                                @endphp 
                                  
                                @foreach ($dataOpreasional as $key => $value)
                                        <tr id="{{$index}}">
                                            <td>
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="checkboxPrimary_{{$index}}" class="centang_cekbox" value="" name="data[{{$index}}][masuk_db]">
                                                    <label for="checkboxPrimary_{{$index}}"></label>
                                                </div>
                                            </td>
                                            <td id="id_sewa_operasional_tabel_{{$index}}" hidden="">
                                                <input type="hidden" id="id_sewa_operasional_data_{{$index}}"  class="id_operasional" name="data[{{$index}}][id_sewa_operasional_data]" value="{{$value->id}}">
                                            </td>
                                            @if($value->deskripsi =='INAP'|| $value->deskripsi == 'CLEANING/REPAIR')
                                                <td id="deskripsi_tabel_{{$index}}" >
                                                    <input type="text" name="data[{{$index}}][deskripsi_data]" id="deskripsi_data_{{$index}}" value="{{$value->deskripsi}}" class="form-control deskripsi_hardcode" readonly>
                                                    <span class="badge badge-success">Data Yang Tersimpan</span>
                                               
                                                </td>
                                                <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{$index}}">
                                                    <input type="text" name="data[{{$index}}][nominal_data]" id="nominal_data_{{$index}}" value="{{number_format($value->total_operasional,2) }}" class="form-control uang numaja nominal_hardcode">
                                                </td>
                                            @endif
                                            @if (
                                                $value->deskripsi =='STORAGE'||
                                                $value->deskripsi =='DEMURAGE'||
                                                $value->deskripsi =='DETENTION'||
                                                $value->deskripsi =='SEAL PELAYARAN'||
                                                $value->deskripsi =='SEAL PJE'||
                                                $value->deskripsi =='PLASTIK'||
                                                $value->deskripsi =='TALLY'||
                                                $value->deskripsi =='TIMBANGAN'||
                                                $value->deskripsi =='BURUH'
                                            )
                                                <td id="deskripsi_tabel_{{$index}}" >
                                                    <input type="text" name="data[{{$index}}][deskripsi_data]" id="deskripsi_data_{{$index}}" value="{{$value->deskripsi}}" class="form-control deskripsi" readonly>
                                                    <span class="badge badge-success">Data Yang Tersimpan</span>
                                                
                                                </td>
                                                <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{$index}}">
                                                    <input type="text" name="data[{{$index}}][nominal_data]" id="nominal_data_{{$index}}" value="{{number_format($value->total_operasional,2) }}" class="form-control uang numaja" readonly>
                                                </td>
                                            @endif
                                            @if(
                                                 $value->deskripsi !='STORAGE'&&
                                                $value->deskripsi !='DEMURAGE'&&
                                                $value->deskripsi !='DETENTION'&&
                                                $value->deskripsi !='SEAL PELAYARAN'&&
                                                $value->deskripsi !='SEAL PJE'&&
                                                $value->deskripsi !='PLASTIK'&&
                                                $value->deskripsi !='TALLY'&&
                                                $value->deskripsi !='TIMBANGAN'&&
                                                $value->deskripsi !='BURUH'&&
                                                $value->deskripsi !='INAP'&& 
                                                $value->deskripsi != 'CLEANING/REPAIR'
                                            )
                                                <td id="deskripsi_tabel_{{$index}}" >
                                                    <input type="text" name="data[{{$index}}][deskripsi_data]" id="deskripsi_data_{{$index}}" value="{{$value->deskripsi}}" class="form-control deskripsi_lain" >
                                                    <span class="badge badge-success">Data Yang Tersimpan</span>
                                                
                                                </td>
                                                <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{$index}}">
                                                    <input type="text" name="data[{{$index}}][nominal_data]" id="nominal_data_{{$index}}" value="{{number_format($value->total_operasional,2) }}" class="form-control uang numaja nominal_lain" >
                                                </td>

                                            @endif
                                            <td style="width:1px; white-space: nowrap; text-align:center;" id="ditagihkan_tabel_{{$index}}" >
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="checkTagih_data_{{$index}}" class="cek_tagih" name="data[{{$index}}][ditagihkan_data]" {{$value->is_ditagihkan=='Y'?'checked':''}} >
                                                    <label for="checkTagih_data_{{$index}}"></label>
                                                    <input type="hidden" class="value_cek_tagih" name="data[{{$index}}][ditagihkan_data_value]"  value="{{$value->is_ditagihkan}}">
                                                    {{-- for label sama id harus sama, kalo nggk gabisa di klik --}}
                                                </div>
                                            </td>
                                            <td style="width:1px; white-space: nowrap; text-align:center;" id="dipisahkan_tabel_{{$index}}" >
                                                <div class="icheck-success d-inline">
                                                    <input type="checkbox" id="checkPisah_data_{{$index}}" class="cek_pisah" name="data[{{$index}}][dipisahkan_data]" {{$value->is_dipisahkan=='Y'?'checked':''}}  >
                                                    <label for="checkPisah_data_{{$index}}"></label>
                                                    <input type="hidden" class="value_cek_dipisahkan_data" name="data[{{$index}}][dipisahkan_data_value]"  value="{{$value->is_dipisahkan}}">

                                                    {{-- for label sama id harus sama, kalo nggk gabisa di klik --}}
                                                </div>
                                            </td>
                                            <td id="catatan_tabel_{{$index}}">
                                                <input type="text" name="data[{{$index}}][catatan_data]" id="catatan_data_{{$index}}"  value="{{$value->catatan}}" class="form-control catatan">
                                            </td>
                                        </tr>
                                        @php
                                        $index+=1;
                                         @endphp
                                    @endforeach
                                @endif
                                  @if  (!$flagCleaning && $sewa->jenis_order=="INBOUND")
                                            <tr id="{{ $index}}">
                                                <td>
                                                    <div class="icheck-danger d-inline">
                                                        <input type="checkbox" id="checkboxPrimary_{{ $index}}" class="centang_cekbox" value="N" name="data_hardcode[{{$index}}][masuk_db]">
                                                        <label for="checkboxPrimary_{{ $index}}"></label>
                                                    </div>
                                                </td>
                                                <td id="id_sewa_operasional_tabel_{{ $index}}" hidden="">
                                                    <input type="hidden" id="id_sewa_operasional_data_{{ $index}}"  class="id_operasional" name="data_hardcode[{{$index}}][id_sewa_operasional_data]" value="">
                                                </td>
                                                <td id="deskripsi_tabel_{{ $index}}" >
                                                        <input type="text" name="data_hardcode[{{ $index}}][deskripsi_data]" id="deskripsi_data_{{ $index}}" value="CLEANING/REPAIR" class="form-control uang numaja" readonly>
                                                    <span class="badge badge-danger">Data Template</span>
                                                
                                                </td>
                                                <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{ $index}}">
                                                        <input type="text" name="data_hardcode[{{ $index}}][nominal_data]" id="nominal_data_{{ $index}}" value="" class="form-control uang numaja nominal_hardcode" readonly>
                                                </td>
                                                <td style="width:1px; white-space: nowrap; text-align:center;" id="ditagihkan_tabel_{{ $index}}" >
                                                    <div class="icheck-danger d-inline">
                                                        <input type="checkbox" id="checkTagih_data_{{ $index}}" class="cek_tagih" name="data_hardcode[{{ $index}}][ditagihkan_data]" >
                                                        <label for="checkTagih_data_{{ $index}}"></label>
                                                        <input type="hidden" class="value_cek_tagih" name="data_hardcode[{{ $index}}][ditagihkan_data_value]"  value="N">
                                                        {{-- for label sama id harus sama, kalo nggk gabisa di klik --}}
                                                    </div>
                                                </td>
                                                <td style="width:1px; white-space: nowrap; text-align:center;" id="dipisahkan_tabel_{{ $index}}" >
                                                    <div class="icheck-danger d-inline">
                                                        <input type="checkbox" id="checkPisah_data_{{ $index}}" class="cek_pisah" name="data_hardcode[{{ $index}}][dipisahkan_data]" >
                                                        <label for="checkPisah_data_{{ $index}}"></label>
                                                        <input type="hidden" class="value_cek_dipisahkan_data" name="data_hardcode[{{ $index}}][dipisahkan_data_value]"  value="N">

                                                        {{-- for label sama id harus sama, kalo nggk gabisa di klik --}}
                                                    </div>
                                                </td>
                                                <td id="catatan_tabel_{{ $index}}">
                                                    <input type="text" name="data_hardcode[{{ $index}}][catatan_data]" id="catatan_data_{{ $index}}"  value="" class="form-control catatan">
                                                </td>
                                            </tr>
                                            @php
                                            $index+=1;
                                            @endphp
                                            
                                    @endif
                                    @if (!$flagInap)
                                        <tr id="{{ $index}}">
                                            <td>
                                                <div class="icheck-danger d-inline">
                                                    <input type="checkbox" id="checkboxPrimary_{{ $index}}" class="centang_cekbox" value="N" name="data_hardcode[{{$index}}][masuk_db]">
                                                    <label for="checkboxPrimary_{{ $index}}"></label>
                                                </div>
                                            </td>
                                            <td id="id_sewa_operasional_tabel_{{ $index}}" hidden="">
                                                <input type="hidden" id="id_sewa_operasional_data_{{ $index}}"  class="id_operasional" name="data_hardcode[{{ $index}}][id_sewa_operasional_data]" value="">
                                            </td>
                                            <td id="deskripsi_tabel_{{ $index}}" >
                                                
                                                    <input type="text" name="data_hardcode[{{ $index}}][deskripsi_data]" id="deskripsi_data_{{ $index}}" value="INAP" class="form-control uang numaja" readonly>
                                                    <span class="badge badge-danger">Data Template</span>
                                            </td>
                                            <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{ $index}}">
                                                    <input type="text" name="data_hardcode[{{ $index}}][nominal_data]" id="nominal_data_{{ $index}}" value="" class="form-control uang numaja nominal_hardcode" readonly>
                                            </td>
                                            <td style="width:1px; white-space: nowrap; text-align:center;" id="ditagihkan_tabel_{{ $index}}" >
                                                <div class="icheck-danger d-inline">
                                                    <input type="checkbox" id="checkTagih_data_{{ $index}}" class="cek_tagih" name="data_hardcode[{{ $index}}][ditagihkan_data]" >
                                                    <label for="checkTagih_data_{{ $index}}"></label>
                                                    <input type="hidden" class="value_cek_tagih" name="data_hardcode[{{ $index}}][ditagihkan_data_value]"  value="N">
                                                    {{-- for label sama id harus sama, kalo nggk gabisa di klik --}}
                                                </div>
                                            </td>
                                            <td style="width:1px; white-space: nowrap; text-align:center;" id="dipisahkan_tabel_{{ $index}}" >
                                                <div class="icheck-danger d-inline">
                                                    <input type="checkbox" id="checkPisah_data_{{ $index}}" class="cek_pisah" name="data_hardcode[{{ $index}}][dipisahkan_data]" >
                                                    <label for="checkPisah_data_{{ $index}}"></label>
                                                    <input type="hidden" class="value_cek_dipisahkan_data" name="data_hardcode[{{ $index}}][dipisahkan_data_value]"  value="N">

                                                    {{-- for label sama id harus sama, kalo nggk gabisa di klik --}}
                                                </div>
                                            </td>
                                            <td id="catatan_tabel_{{ $index}}">
                                                <input type="text" name="data_hardcode[{{ $index}}][catatan_data]" id="catatan_data_{{ $index}}"  value="" class="form-control catatan">
                                            </td>
                                        </tr>
                                        @php
                                        $index+=1;
                                        @endphp
                                        
                                    @endif
                                @if(isset($array_inbound))
                                    @if ($sewa->jenis_order == "INBOUND")

                                        @foreach ($array_inbound as $key => $value)
                                            <tr id="{{$index}}">
                                                <td >
                                                    <div class="icheck-warning d-inline">
                                                        <input type="checkbox" id="checkboxPrimary_{{$index}}" class="centang_cekbox" value="N" name="dataMaster[{{$index}}][masuk_db]">
                                                        <label for="checkboxPrimary_{{$index}}"></label>
                                                    </div>
                                                    
                                                </td>
                                                <td id="id_sewa_operasional_tabel_{{$index}}" hidden="">
                                                    <input type="hidden" id="id_sewa_operasional_data_{{$index}}"  class="id_operasional" name="dataMaster[{{$index}}][id_sewa_operasional_data]" value="">
                                                </td>
                                                <td id="deskripsi_tabel_{{$index}}" >
                                                    <input type="text" name="dataMaster[{{$index}}][deskripsi_data]" id="deskripsi_data_{{$index}}" value="{{$value['deskripsi']}}" class="form-control uang numaja" readonly>
                                                    <span class="badge badge-warning">Data S/D/T</span>
                                                
                                                </td>
                                                <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{$index}}">
                                                        <input type="text" name="dataMaster[{{$index}}][nominal_data]" id="nominal_data_{{$index}}" value="{{number_format($value['biaya'],2) }}" class="form-control uang numaja" readonly>
                                                </td>
                                                <td style="width:1px; white-space: nowrap; text-align:center;" id="ditagihkan_tabel_{{$index}}" >
                                                    <div class="icheck-warning d-inline">
                                                        <input type="checkbox" id="checkTagih_data_{{$index}}" class="cek_tagih" name="dataMaster[{{$index}}][ditagihkan_data]"  >
                                                        <label for="checkTagih_data_{{$index}}"></label>
                                                        <input type="hidden" class="value_cek_tagih" name="dataMaster[{{$index}}][ditagihkan_data_value]"  value="">
                                                        {{-- for label sama id harus sama, kalo nggk gabisa di klik --}}
                                                    </div>
                                                </td>
                                                <td style="width:1px; white-space: nowrap; text-align:center;" id="dipisahkan_tabel_{{$index}}" >
                                                    <div class="icheck-warning d-inline">
                                                        <input type="checkbox" id="checkPisah_data_{{$index}}" class="cek_pisah" name="dataMaster[{{$index}}][dipisahkan_data]"   >
                                                        <label for="checkPisah_data_{{$index}}"></label>
                                                        <input type="hidden" class="value_cek_dipisahkan_data" name="dataMaster[{{$index}}][dipisahkan_data_value]"  value="">

                                                        {{-- for label sama id harus sama, kalo nggk gabisa di klik --}}
                                                    </div>
                                                </td>
                                                <td id="catatan_tabel_{{$index}}">
                                                    <input type="text" name="dataMaster[{{$index}}][catatan_data]" id="catatan_data_{{$index}}"   class="form-control catatan">
                                                </td>
                                            </tr>
                                            @php
                                            $index+=1;
                                            @endphp
                                        @endforeach
                                        
                                    @endif
                                @endif
                                 @if(isset($array_outbond))
                                    @if ($sewa->jenis_order == "OUTBOUND")

                                        @foreach ($array_outbond as $key => $value)
                                            <tr id="{{$index}}">
                                                <td>
                                                    <div class="icheck-warning d-inline">
                                                        <input type="checkbox" id="checkboxPrimary_{{$index}}" class="centang_cekbox" value="N" name="dataMaster[{{$index}}][masuk_db]">
                                                        <label for="checkboxPrimary_{{$index}}"></label>
                                                    </div>
                                                    
                                                </td>
                                                <td id="id_sewa_operasional_tabel_{{$index}}" hidden="">
                                                    <input type="hidden" id="id_sewa_operasional_data_{{$index}}"  class="id_operasional" name="dataMaster[{{$index}}][id_sewa_operasional_data]" value="">
                                                </td>
                                                <td id="deskripsi_tabel_{{$index}}" >
                                                        <input type="text" name="dataMaster[{{$index}}][deskripsi_data]" id="deskripsi_data_{{$index}}" value="{{$value['deskripsi']}}" class="form-control uang numaja" readonly>
                                                    <span class="badge badge-warning">Data Tujuan Biaya</span>
                                                
                                                </td>
                                                <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{$index}}">
                                                        <input type="text" name="dataMaster[{{$index}}][nominal_data]" id="nominal_data_{{$index}}" value="{{number_format($value['biaya'],2) }}" class="form-control uang numaja" readonly>
                                                </td>
                                                <td style="width:1px; white-space: nowrap; text-align:center;" id="ditagihkan_tabel_{{$index}}" >
                                                    <div class="icheck-warning d-inline">
                                                        <input type="checkbox" id="checkTagih_data_{{$index}}" class="cek_tagih" name="dataMaster[{{$index}}][ditagihkan_data]"  >
                                                        <label for="checkTagih_data_{{$index}}"></label>
                                                        <input type="hidden" class="value_cek_tagih" name="dataMaster[{{$index}}][ditagihkan_data_value]"  value="">
                                                        {{-- for label sama id harus sama, kalo nggk gabisa di klik --}}
                                                    </div>
                                                </td>
                                                <td style="width:1px; white-space: nowrap; text-align:center;" id="dipisahkan_tabel_{{$index}}" >
                                                    <div class="icheck-warning d-inline">
                                                        <input type="checkbox" id="checkPisah_data_{{$index}}" class="cek_pisah" name="dataMaster[{{$index}}][dipisahkan_data]"   >
                                                        <label for="checkPisah_data_{{$index}}"></label>
                                                        <input type="hidden" class="value_cek_dipisahkan_data" name="dataMaster[{{$index}}][dipisahkan_data_value]"  value="">

                                                        {{-- for label sama id harus sama, kalo nggk gabisa di klik --}}
                                                    </div>
                                                </td>
                                                <td id="catatan_tabel_{{$index}}">
                                                    <input type="text" name="dataMaster[{{$index}}][catatan_data]" id="catatan_data_{{$index}}"   class="form-control catatan">
                                                </td>
                                            </tr>
                                            @php
                                            $index+=1;
                                            @endphp
                                        @endforeach
                                        
                                    @endif
                                @endif
                                <input type="hidden" id="maxIndex" value="{{ $index}}">
                                
                                
                              </tbody>
                              <tfoot>
                              </tfoot>
                        </table>

                    </div>
                </div>
                
            </div>
           
        </div> 
         
 
    </form>
<script type="text/javascript">
    
    function ubahTanggal(dateString) {
        var dateObject = new Date(dateString);
        var day = dateObject.getDate();
        var month = dateObject.toLocaleString('default', { month: 'short' });
        var year = dateObject.getFullYear();

        return day + '-' + month + '-' + year;
    }
        

    $(document).ready(function() {
        // console.log($('#select_sewa').val());
        
        $('#tanggal_kembali').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            endDate: "0d"
        });
        // var centangCheckbox = $('.centang_cekbox');
        var cek_tagih = $('.cek_tagih');
        var cek_pisah = $('.cek_pisah');
       
       function cekCheckbox(){
        var centangCheckboxes = $('.centang_cekbox');
        for (var i = 0; i < centangCheckboxes.length; i++) {
            var checkbox = centangCheckboxes.eq(i);
            var row = checkbox.closest('tr');
            var index = row.attr('id');
            var cekTagih = row.find('.cek_tagih');
            var cekPisah = row.find('.cek_pisah');
            var value_cek_tagih = row.find('.value_cek_tagih').val();
            var value_cek_dipisahkan_data = row.find('.value_cek_dipisahkan_data').val();
            var id_operasional = row.find('.id_operasional').val();

            
            if (id_operasional) {
                checkbox.prop('checked', true);
                checkbox.val('Y');

            } else {
                checkbox.prop('checked', false);
                checkbox.val('N');

            }

            if (checkbox.is(":checked")) {
                row.find('.cek_tagih').prop('disabled', false);
                if (value_cek_tagih == "Y") {
                    row.find('.cek_pisah').prop('disabled', false);
                } else {
                    row.find('.cek_pisah').prop('disabled', true);
                }
                row.find('.catatan').prop('readonly', false);
            } else if (!checkbox.is(":checked")) {
                row.find('.cek_tagih').prop('checked', false);
                row.find('.cek_tagih').prop('disabled', true);
                row.find('.cek_pisah').prop('checked', false);
                row.find('.cek_pisah').prop('disabled', true);
                row.find('.catatan').prop('readonly', true);
                row.find('.catatan').val('');

            }
        }

       
       }
        function cekCheckboxBaru(){
            var centangCheckboxes = $('.centang_cekbox');
            for (var i = 0; i < centangCheckboxes.length; i++) {
                var checkbox = centangCheckboxes.eq(i);
                var row = checkbox.closest('tr');
                var index = row.attr('id');
                var cekTagih = row.find('.cek_tagih');
                var cekPisah = row.find('.cek_pisah');
                var value_cek_tagih = row.find('.value_cek_tagih').val();
                var value_cek_dipisahkan_data = row.find('.value_cek_dipisahkan_data').val();

                if (checkbox.is(":checked")) {
                    row.find('.cek_tagih').prop('disabled', false);
                    row.find('.deskripsi_lain').prop('readonly', false);
                    row.find('.nominal_lain').prop('readonly', false);

                    if (value_cek_tagih == "Y") {
                        row.find('.cek_pisah').prop('disabled', false);
                    } else {
                        row.find('.cek_pisah').prop('disabled', true);
                    }
                    row.find('.catatan').prop('readonly', false);
                } else if (!checkbox.is(":checked")) {
                    row.find('.cek_tagih').prop('checked', false);
                    row.find('.cek_tagih').prop('disabled', true);
                    row.find('.cek_pisah').prop('checked', false);
                    row.find('.cek_pisah').prop('disabled', true);
                    row.find('.catatan').prop('readonly', true);
                    row.find('.catatan').val('');

                    row.find('.deskripsi_lain').prop('readonly', true);
                    row.find('.nominal_lain').prop('readonly', true);
                    // deskripsi_lain.prop('readonly', false);
                    // nominal_lain.prop('readonly', false);
                }
            }

       
       }
       cekCheckbox();
       
        $(document).on('click', '.centang_cekbox', function () {
             var row = $(this).closest('tr'); 
            var index = row.attr('id'); 
            var value_cek_tagih = row.find('.value_cek_tagih');
            var value_cek_dipisahkan_data = row.find('.value_cek_dipisahkan_data');
            var id_operasional = row.find('.id_operasional');
            // console.log(id_operasional.val());
            var deskripsi_lain = row.find('.deskripsi_lain');
            var nominal_lain = row.find('.nominal_lain');
            // $('.centang_cekbox_semua').prop('checked', false);


            if ($(this).is(":checked")) {
                $(this).val('Y');
                row.find('.deskripsi_lain').prop('readonly', false);
                row.find('.nominal_lain').prop('readonly', false);

                if(row.find('.cek_tagih').is(":checked"))
                {
                    row.find('.cek_pisah').prop('disabled', false);
                }
                else
                {
                    row.find('.cek_pisah').prop('disabled', true);
                }
                row.find('.cek_tagih').prop('disabled', false);
                row.find('.catatan').prop('readonly', false);
                row.find('.nominal_hardcode').prop('readonly', false);

            
            } else if ($(this).is(":not(:checked)")) {  
                $(this).val('N')
                row.find('.cek_tagih').prop('checked', false);
                row.find('.cek_tagih').prop('disabled', true);
                row.find('.cek_pisah').prop('checked', false);
                row.find('.cek_pisah').prop('disabled', true);
                row.find('.catatan').prop('readonly', true);
                row.find('.catatan').val('');

                value_cek_tagih.val('');
                value_cek_dipisahkan_data.val('');
                // id_operasional.val('HAPUS');
                 row.find('.deskripsi_lain').prop('readonly', true);
                row.find('.nominal_lain').prop('readonly', true);

                row.find('.nominal_hardcode').prop('readonly', true);


            }
              
        });
        
        
        $(document).on('click', '.cek_tagih', function () {
             var row = $(this).closest('tr'); 
            var index = row.attr('id'); 
            var cekPisah = row.find('.cek_pisah');
            var value_cek_tagih = row.find('.value_cek_tagih');
            var value_cek_dipisahkan_data = row.find('.value_cek_dipisahkan_data');

            // console.log(cekPisah.attr('id'));
            if ($(this).is(":checked")) {
                cekPisah.prop('disabled', false);
                value_cek_tagih.val('Y')
            } else if ($(this).is(":not(:checked)")) {  

                cekPisah.prop('disabled', true);
                cekPisah.prop('checked', false);

                value_cek_tagih.val('N')
                value_cek_dipisahkan_data.val('N')


            }
            // console.log(cekPisah.attr('id'));
        });

        $(document).on('click', '.cek_pisah', function () {
         var row = $(this).closest('tr'); 
            var index = row.attr('id'); 
            var checkTagih = row.find('.cek_tagih');
            // console.log(checkTagih.attr('id'));
            var value_cek_tagih = row.find('.value_cek_tagih');
            var value_cek_dipisahkan_data = row.find('.value_cek_dipisahkan_data');
            if ($(this).is(":checked")) {

                value_cek_dipisahkan_data.val('Y')

            
            } else if ($(this).is(":not(:checked)")) {  

                $(this).prop('disabled', false);
                value_cek_dipisahkan_data.val('N')


            }
        });

        $('#muatan_ltl').keyup(function(e) {
            let muatan = e.target.value;
            const temp = muatan.split(".");
            muatan = parseFloat(muatan);
            if(temp.length > 1 ){
                if(temp[1].length > 2){
                    console.log(parseFloat(muatan.toFixed(2)));
                    $('#muatan_ltl').val(muatan.toFixed(2));
                }
            }

            let total_harga = hitung_total_harga_dari_muatan(muatan.toFixed(2));
            $('#total_harga_lcl').val(total_harga);

        });
        if ($('#jenis_tujuan').val() != "LTL") {
            $('#lcl_selected').css('display', 'none');
            $('#div_segel').show();
        } else {
            $('#lcl_selected').css('display', '');
            $('#div_segel').hide();
            // $('#div_foto_segel_pelayaran_1').hide();
            // $('#div_foto_segel_pelayaran_2').hide();
            // $('#div_foto_segel_pje').hide();
        }

        function getDate(){
            var today = new Date();
            // var tomorrow = new Date(today);
            // tomorrow.setDate(today.getDate()+1);

            //  $('#tanggal_kembali').datepicker({
            //     autoclose: true,
            //     format: "dd-M-yyyy",
            //     todayHighlight: true,
            //     language:'en',
            //     endDate: "0d"
            // });

            $('#tanggal_kembali').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                startDate: today,
            }).datepicker("setDate", today);
        }
        function get_date_now(){
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.toLocaleString('default', { month: 'short' })).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            today = dd + '-' + mm + '-' + yyyy;
            return today;
        }
        
         $('#check_is_kembali').click(function() {
            if ($(this).is(":checked")) {

                $('#is_kembali').val('Y');
                $('#tanggal_kembali').attr('disabled', false);
                $('#muatan_ltl').attr('readonly', false);
                // getDate();
                $('#tanggal_kembali').val(get_date_now());


            } else if ($(this).is(":not(:checked)")) {

                $('#is_kembali').val('N');
                $('#muatan_ltl').attr('readonly', true);
                $('#tanggal_kembali').attr('disabled', true);
                $('#tanggal_kembali').val('');
            }
        });

        if ($('#check_is_kembali').is(":checked")) {

            $('#is_kembali').val('Y');
            $('#tanggal_kembali').attr('disabled', false);
            $('#muatan_ltl').attr('readonly', false);
            // getDate();
            $('#tanggal_kembali').val(get_date_now());

        };

        $('#cek_seal_pje').click(function() {
            if ($(this).is(":checked")) {

                $('#seal_pje').prop('readonly', false);
          
            } else if ($(this).is(":not(:checked)")) {
        
                $('#seal_pje').prop('readonly', true);
            }
        });
        if ($('#cek_seal_pje').is(":checked")) {

            $('#seal_pje').prop('readonly', false);

        };
       
        // var baseUrl = "{{ asset('') }}";
        // var array_add_cost = [];
        // $.ajax({
        //     url: `${baseUrl}truck_order/getDetailJOBiaya/${$('#id_jo_detail_hidden').val()}`, 
        //     method: 'GET', 
        //     success: function(response) {
        //         if(!response)
        //         {
        //             array_add_cost = [];
        //         }
        //         else
        //         {
        //             for (var i in response) {
        //                 if(response[i].storage || response[i].storage!=0)
        //                 {
        //                     var objSTORAGE = {
        //                             deskripsi: 'STORAGE',
        //                             biaya: response[i].storage,
        //                         };
        //                     array_add_cost.push(objSTORAGE);
        //                 } 
        //                 if(response[i].demurage||response[i].demurage!=0)
        //                 {
        //                     var objDEMURAGE = {
        //                             deskripsi: 'DEMURAGE',
        //                             biaya: response[i].demurage,
        //                         };
        //                     array_add_cost.push(objDEMURAGE);
        //                 } 
        //                 if(response[i].detention||response[i].detention!=0)
        //                 {
        //                     var objDETENTION = {
        //                             deskripsi: 'DETENTION',
        //                             biaya: response[i].detention,
        //                         };
        //                     array_add_cost.push(objDETENTION);
        //                 } 
                            
        //             }
        //             $('#add_cost_hidden').val(JSON.stringify(array_add_cost));
        //             console.log('array_add_cost '+array_add_cost);

        //         }
        //     },
        //     error: function(xhr, status, error) {
        //         console.error('Error:', error);
        //     }
        // });


        $('body').on('click','#btnTmbh',function()
		{
            var maxID = $('#maxIndex').val();
            $('#tampunganTabel').append(
                `
                    <tr id="${maxID}">
                        <td>
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="checkboxPrimary_${maxID}" class="centang_cekbox" value="N" name="dataLain[${maxID}][masuk_db]">
                                <label for="checkboxPrimary_${maxID}"></label>
                            </div>
                            
                        </td>
                        <td id="id_sewa_operasional_tabel_${maxID}" hidden="">
                            <input type="hidden" id="id_sewa_operasional_data_${maxID}"  class="id_operasional" name="dataLain[${maxID}][id_sewa_operasional_data]" value="">
                        </td>
                        <td id="deskripsi_tabel_${maxID}" >
                                <input type="text" readonly name="dataLain[${maxID}][deskripsi_data]" id="deskripsi_data_${maxID}" value="" class="form-control deskripsi_lain">
                                <span class="badge badge-primary">Data Lain-lain</span>
                        
                        </td>
                        <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_${maxID}">
                                <input type="text" readonly name="dataLain[${maxID}][nominal_data]" id="nominal_data_${maxID}" value="" class="form-control uang numaja nominal_lain">
                        </td>
                        <td style="width:1px; white-space: nowrap; text-align:center;" id="ditagihkan_tabel_${maxID}" >
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="checkTagih_data_${maxID}" class="cek_tagih" name="dataLain[${maxID}][ditagihkan_data]"  >
                                <label for="checkTagih_data_${maxID}"></label>
                                <input type="hidden" class="value_cek_tagih" name="dataLain[${maxID}][ditagihkan_data_value]"  value="N">
                            </div>
                        </td>
                        <td style="width:1px; white-space: nowrap; text-align:center;" id="dipisahkan_tabel_${maxID}" >
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="checkPisah_data_${maxID}" class="cek_pisah" name="dataLain[${maxID}][dipisahkan_data]"  >
                                <label for="checkPisah_data_${maxID}"></label>
                                <input type="hidden" class="value_cek_dipisahkan_data" name="dataLain[${maxID}][dipisahkan_data_value]"  value="N">

                            </div>
                        </td>
                        <td id="catatan_tabel_${maxID}">
                            <input type="text" name="dataLain[${maxID}][catatan_data]" id="catatan_data_${maxID}"  value="" class="form-control catatan">
                        </td>
                     <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove"  class="btn btn-danger radiusSendiri btnDelete"><i class="fa fa-fw fa-trash-alt"></i></button></td>

                    </tr>
                `
            )
            maxID++;
            $('#maxIndex').val(maxID);
            cekCheckboxBaru();
		});
        $(document).on('input', '.deskripsi_lain', function () {
            $(this).val($(this).val().toUpperCase());

            // for (var i = 0; i < centangCheckboxes.length; i++) {
            //     var checkbox = centangCheckboxes.eq(i);
            //     var row = checkbox.closest('tr');
                
            // }
            
        });
        $(document).on('input', '.catatan', function () {
            $(this).val($(this).val().toUpperCase());

            // for (var i = 0; i < centangCheckboxes.length; i++) {
            //     var checkbox = centangCheckboxes.eq(i);
            //     var row = checkbox.closest('tr');
                
            // }
            
        });

       $(document).on('click', '.centang_cekbox_semua', function () {
            $('.centang_cekbox').prop('checked', false).trigger('click');
        });
      

        $(document).on('click','.btnDelete',function(){
            var maxID = $('#maxIndex').val();
            $(this).closest('tr').remove();

            if($(this).closest('tr').attr('id') == maxID)
            {
                maxID--;
            }
            $('#maxIndex').val(maxID);
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
                title: 'Data dihapus'
            })
            // cekCheckbox();

        });
       
      
        $('#post_data').submit(function(event) {
            var deskripsi = $('.deskripsi_lain');
            var nominal_lain = $('.nominal_lain');
            var nominal_hardcode = $('.nominal_hardcode');

            var flagDeskripsi = false;
            var flagDeskripsiPrevent = false;

            var flagNominal = false;
            var flagNominalHardcode = false;


            for (var i = 0; i < deskripsi.length; i++) {
                var desTextbox = deskripsi.eq(i);
                var row = desTextbox.closest('tr');
                var index = row.attr('id');
                var trimTextbox = desTextbox.val().trim();
                var simpanData=row.find('.centang_cekbox').val();

                if(simpanData=="Y")
                {
                    if (trimTextbox === '') {
                        flagDeskripsi = true;
                        break; 
                    }
                }
            }

            for (var i = 0; i < deskripsi.length; i++) {
                var desTextbox = deskripsi.eq(i);
                var row = desTextbox.closest('tr');
                var index = row.attr('id');
                var trimTextbox = desTextbox.val().trim();
                var simpanData=row.find('.centang_cekbox').val();

                if(simpanData=="Y")
                {
                    if (
                        trimTextbox ==='STORAGE'||
                        trimTextbox ==='DEMURAGE'||
                        trimTextbox ==='DETENTION'||
                        trimTextbox ==='SEAL PELAYARAN'||
                        trimTextbox ==='SEAL PJE'||
                        trimTextbox ==='PLASTIK'||
                        trimTextbox ==='TALLY'||
                        trimTextbox ==='TIMBANGAN'||
                        trimTextbox ==='BURUH'||
                        trimTextbox ==='INAP'|| 
                        trimTextbox === 'CLEANING/REPAIR'

                    ) {
                        flagDeskripsiPrevent = true;
                        break; 
                    }
                }
            }

            for (var i = 0; i < nominal_lain.length; i++) {
                var NominalTextbox = nominal_lain.eq(i);
                var row = NominalTextbox.closest('tr');
                var index = row.attr('id');
                var trimNominal = NominalTextbox.val().trim();
                var simpanData=row.find('.centang_cekbox').val();

                if(simpanData=="Y")
                {
                    if (trimNominal === '') {
                        flagNominal = true;
                        break; 
                    }
                }
            }

            for (var i = 0; i < nominal_hardcode.length; i++) {
                var NominalHardCodeTextbox = nominal_hardcode.eq(i);
                var row = NominalHardCodeTextbox.closest('tr');
                var index = row.attr('id');
                var trimNominal = NominalHardCodeTextbox.val().trim();
                var simpanData=row.find('.centang_cekbox').val();

                if(simpanData=="Y")
                {
                    if (trimNominal === '') {
                        flagNominalHardcode = true;
                        break; 
                    }
                }
            }

            if (flagDeskripsi) {
                event.preventDefault(); 
                Swal.fire({
                    icon: 'error',
                    text: 'DESKRIPSI BIAYA WAJIB DIISI!',
                });
                return;
            }
            if (flagNominal||flagNominalHardcode) {
                event.preventDefault(); 
                Swal.fire({
                    icon: 'error',
                    text: 'NOMINAL BIAYA WAJIB DIISI!',
                });
                return;
            }
            if (flagDeskripsiPrevent) {
                event.preventDefault(); 
                Swal.fire({
                    icon: 'error',
                    title: 'BIAYA LAIN-LAIN TIDAK BOLEH SAMA!',
                    text: 'Pilih Deskripsi Lain Selain Dari Data Template / Data Tersimpan / Data SDT / Data Tujuan Biaya / Data Yang Tersimpan',
                });
                return;
            }

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
                    }, 800); // 2000 milliseconds = 2 seconds
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

@endsection


