
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
<div class="row m-2">
    <div class="col-12">
        <div class="card radiusSendiri ">
            <div class="card-header ">
                <a href="{{ route('laporan_sales.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                {{-- <a href="{{ $sewa->status=='PROSES DOORING'?route('dalam_perjalanan.index'):route('belum_invoice.create') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a> --}}
                {{-- <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button> --}}
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
                                <input type="hidden" id="jenis_tujuan" name="jenis_tujuan" class="form-control" value="{{$sewa->jenis_tujuan}}" readonly>                         

                            </div>  

                            <div class="form-group ">
                                <label for="no_akun">Catatan</label>
                                <input type="text" id="catatan" name="catatan" class="form-control" value="{{$sewa->catatan}}" disabled>                         
                            </div> 


                        </div>
                        <div class="col-6">
                                <div class="row">
                                    {{-- <div class="form-group col-12">
                                        Data Kendaraan
                                    <hr>
    
                                    </div> --}}
                                    <div class="form-group col-4">
                                        <label for="no_akun">Kendaraan</label>
                                        <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{$sewa->no_polisi}}" readonly>                         
                                    </div>  
    
                                    {{-- @if ($sewa->supir) --}}
                                    <div class="form-group col-8">
                                        <label for="no_akun">Driver</label>
                                        @if ($sewa->id_supplier)
                                        <input type="text" id="driver" name="driver" class="form-control" value="DRIVER REKANAN ({{$sewa->namaSupplier}})" readonly>     
                                            
                                        @else
                                        <input type="text" id="driver" name="driver" class="form-control" value="{{$sewa->supir}} ({{$sewa->telpSupir}})" readonly>     
                                            
                                        @endif
                                        <input type="hidden" name="id_karyawan" id="id_karyawan">                    
                                    </div> 
                                    {{-- @endif --}}

                                </div>
                                
                                {{-- @if($sewa->jenis_tujuan=='FTL') --}}
                                    <div class="form-group">
                                        @if ($sewa->jenis_tujuan=='FTL')
                                            <label for="no_akun">No. Kontainer</label>
                                        @else
                                            <label for="no_akun">No. Koli</label>
                                        @endif
                                        @if ($sewa->no_kontainer_jod&&$sewa->jenis_order =="INBOUND")
                                            <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" readonly value="{{$sewa->no_kontainer_jod}}" >                         
                                        @else
                                            <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" readonly value="{{$sewa->no_kontainer}}" >               
                                        @endif
                                    </div> 
                                {{-- @endif --}}
                                
                                    @if ($sewa->seal_pelayaran_jod&&$sewa->jenis_order =="INBOUND")
                                    <div class="form-group ">
                                        <label for="seal">Segel Kontainer</label>
                                        <input readonly type="text" id="seal" name="seal" readonly class="form-control"value="{{$sewa->seal_pelayaran_jod}}" >
                                    </div> 
                                @endif
                                
                                @if ($sewa->is_kembali=='N')
                                    <div class="form-group">
                                        <label for="tanggal_pencairan">Tgl. Kembali Surat Jalan<span style="color:red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><input readonly {{$sewa->is_kembali=='N'?'':'checked'}} type="checkbox" name="check_is_kembali" id="check_is_kembali"></span>
                                            </div>
                                            <input type="hidden" id="is_kembali" name='is_kembali' value="{{$sewa->is_kembali}}">
                                            <input readonly type="text" autocomplete="off" name="tanggal_kembali" class="form-control date" id="tanggal_kembali" placeholder="dd-M-yyyy" value="{{$sewa->is_kembali=='Y'?\Carbon\Carbon::parse($sewa->tanggal_kembali)->format('d-M-Y'):''}}">
                                        </div>
                                    </div> 
                                @endif

                                {{-- @if($sewa->jenis_tujuan=='FTL') --}}
                                <div class="form-group">
                                    <label for="no_akun">No. Surat Jalan</label>
                                    <input type="text" id="surat_jalan" name="surat_jalan" class="form-control" value="{{$sewa->no_surat_jalan}}" readonly>                         
                                </div> 
                                {{-- @endif --}}

                                
                                <input type="hidden" name="id_jo_detail_hidden" id="id_jo_detail_hidden" value="{{$sewa->id_jo_detail}}">
                                <input type="hidden" name="id_jo_hidden" id="id_jo_detail_hidden" value="{{$sewa->id_jo}}">

                                <input type="hidden" name="add_cost_hidden" id="add_cost_hidden">
                                <input type="hidden" name='jenis_tujuan' id='jenis_tujuan' value='{{$sewa->jenis_tujuan}}'>

                                @if ($sewa->jenis_order =="OUTBOUND")
                                <div class="row" name="div_segel" id="div_segel">
                                    <div class="form-group col-6">
                                        <label for="seal">Seal</label>
                                        <input type="text" id="seal" name="seal" class="form-control"value="{{$sewa->seal_pelayaran}}" readonly>
                                    </div> 
    
                                    <div class="form-group col-6">
                                        <label for="seal_pje">Seal PJE<span style="color:red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                    <span class="input-group-text"><input {{$sewa->seal_pje?'checked':''}} readonly type="checkbox" name="cek_seal_pje" id="cek_seal_pje"></span>
                                            </div>
                                        <input readonly  type="text" name="seal_pje" class="form-control" id="seal_pje" value="{{$sewa->seal_pje}}">
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
                                        <label for="total_harga_ltl">Total Harga</label>
                                        <div class="form-group">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp.</span>
                                                </div>
                                                <input type="text" class="form-control numaja uang" name="total_harga_ltl"
                                                    id="total_harga_ltl" readonly>
                                                <input type="hidden" id="min_muatan"
                                                    value='{{isset($sewa->min_muatan)?$sewa->min_muatan:''}}'>
                                                <input type="hidden" id="harga_per_kg"
                                                    value='{{isset($sewa->harga_per_kg)?$sewa->harga_per_kg:''}} '>
                                            </div>
                                        </div>
                                        <span style="font-size:11pt;" class="badge bg-danger float-right m-2">Minimal muatan: {{isset($sewa->min_muatan)?$sewa->min_muatan:''}} Kg, Harga per Kg: {{isset($sewa->harga_per_kg)?number_format($sewa->harga_per_kg):''}}</span>
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
                        <span class="badge badge-info">Data Inbound</span>
                        <span class="badge badge-warning">Data Outbound</span>
                {{-- <button type="button" id="btnTmbh" class="btn btn-primary radiusSendiri float-right">Tambah Biaya <i class="fa fa-fw fa-plus"></i> </button></br> --}}
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
                        @foreach ($dataOpreasional as $key => $value)
                                <tr id="{{$index}}">
                                    <td>
                                        <div class="icheck-success d-inline">
                                            <input type="checkbox" id="checkboxPrimary_{{$index}}" class="centang_cekbox" value="" name="data[{{$index}}][masuk_db]"disabled>
                                            <label for="checkboxPrimary_{{$index}}"></label>
                                        </div>
                                    </td>
                                    <td id="id_sewa_operasional_tabel_{{$index}}" hidden="">
                                        <input type="hidden" id="id_sewa_operasional_data_{{$index}}"  class="id_operasional" name="data[{{$index}}][id_sewa_operasional_data]" value="{{$value->id}}"disabled>
                                    </td>
                                        <td id="deskripsi_tabel_{{$index}}" >
                                            <input type="text" name="data[{{$index}}][deskripsi_data]" id="deskripsi_data_{{$index}}" value="{{$value->deskripsi}}" class="form-control deskripsi_hardcode ambil_text_deskripsi" readonly>
                                            <span class="badge badge-success">Data Yang Tersimpan</span>
                                        </td>
                                        <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{$index}}">
                                            <input type="text" name="data[{{$index}}][nominal_data]" id="nominal_data_{{$index}}" value="{{number_format($value->total_operasional) }}" class="form-control uang numaja nominal_hardcode"disabled>
                                        </td>
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
                                        <input type="text" name="data[{{$index}}][catatan_data]" id="catatan_data_{{$index}}"  value="{{$value->catatan}}" class="form-control catatan" disabled>
                                    </td>
                                </tr>
                                @php
                                $index+=1;
                                @endphp
                            @endforeach
                     
                        
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div> 
<script type="text/javascript">
    
    function ubahTanggal(dateString) {
        var dateObject = new Date(dateString);
        var day = dateObject.getDate();
        var month = dateObject.toLocaleString('default', { month: 'short' });
        var year = dateObject.getFullYear();

        return day + '-' + month + '-' + year;
    }
        

    $(document).ready(function() {
        $('#tanggal_kembali').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            endDate: "0d"
        });
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

                
                if (id_operasional && value_cek_tagih == 'Y') {
                    checkbox.prop('checked', true);
                    checkbox.val('Y');

                } else {
                    checkbox.prop('checked', false);
                    checkbox.val('N');

                }

                if (checkbox.is(":checked")) {
                    row.find('.cek_tagih').prop('disabled', true);
                    // if (value_cek_tagih == "Y") {
                        row.find('.cek_pisah').prop('disabled', false);
                    // } else {
                        row.find('.cek_pisah').prop('disabled', true);
                    // }
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
                        row.find('.cek_tagih').prop('disabled', true);
                        row.find('.deskripsi_lain').prop('readonly', false);
                        row.find('.nominal_lain').prop('readonly', false);

                        // if (value_cek_tagih == "Y") {
                            // row.find('.cek_pisah').prop('disabled', false);
                        // } else {
                            row.find('.cek_pisah').prop('disabled', true);
                        // }
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
       
        function hitung_total_harga_dari_muatan(jum_muatan) {
            let harga_total = 0;
            let harga_per_kg = normalize($('#harga_per_kg').val());
            let min_muatan = normalize($('#min_muatan').val());
            jum_muatan = normalize(jum_muatan);
            console.log(jum_muatan);
            if (jum_muatan != '') {
                if (min_muatan < jum_muatan) {
                    harga_total = harga_per_kg * jum_muatan;
                } else {
                    harga_total = harga_per_kg * min_muatan;
                }
            }
            // console.log(harga_total);
            return harga_total;
        }
        
        if ($('#jenis_tujuan').val() != "LTL") {
            $('#lcl_selected').css('display', 'none');
            $('#div_segel').show();
        } else {
            $('#lcl_selected').css('display', '');
            $('#div_segel').hide();
            
        }
        
        function getDate(){
            var today = new Date();

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
        
        if ($('#check_is_kembali').is(":checked")) {

            $('#is_kembali').val('Y');
            $('#tanggal_kembali').attr('disabled', false);
            $('#muatan_ltl').attr('readonly', false);
            // getDate();
            $('#tanggal_kembali').val(get_date_now());

        };
    });
</script>

@endsection


