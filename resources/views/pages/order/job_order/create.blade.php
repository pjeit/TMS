
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
  
@endsection

@section('content')
<style>
   
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
    <form action="{{ route('job_order.store') }}" method="POST" >
      @csrf
<<<<<<< HEAD
        <div class="row m-2">
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('job_order.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6" >
                                <div class="form-group">
                                    <label for="">Pengirim<span class="text-red">*</span></label>
                                        <select class="form-control selectpicker"  id='customer' name="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                        <option value="0">--Pilih Pengirim--</option>
                                        @foreach ($dataCustomer as $cust)
                                            <option value="{{$cust->id}}">{{ $cust->nama }}</option>
                                        @endforeach
                                    </select>
=======
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card radiusSendiri">
                        <div class="card-header">
                            <a href="{{ route('job_order.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                            <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6" >
                                    <div class="form-group">
                                        <label for="">Pengirim<span class="text-red">*</span></label>
                                            <select class="form-control selectpicker"  id='customer' name="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                            <option value="0">--Pilih Pengirim--</option>
                                            @foreach ($dataCustomer as $cust)
                                                <option value="{{$cust->id}}">{{ $cust->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
>>>>>>> 434012c5445d1964c3d69548228a436b9cd2d1d7
                                </div>
                                <div class="col-6" >
                                    <div class="form-group ">
                                        <label for="">Pelayaran</label>
                                        <select class="form-control selectpicker"  id='supplier' name="supplier" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                            <option value="0">--Pilih Pelayaran--</option>
                                            @foreach ($dataSupplier as $sup)
                                                <option value="{{$sup->id}}">{{ $sup->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label for="">No. BL<span class="text-red">*</span></label>
                                        <input required type="text" name="nama_pic" class="form-control" value="{{old('nama_pic','')}}" >
                                    </div>           
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                            <label for="tgl_sandar">Tanggal Sandar</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" name="tgl_sandar" autocomplete="off" class="date form-control" id="tgl_sandar" placeholder="dd-M-yyyy" value="{{old('tgl_sandar','')}}">     
                                        </div>
                                    </div>           
                                </div>
                            </div>
<<<<<<< HEAD
                        </div>  
                            <!-- <div class="card radiusSendiri">
                        <div class="card-header"> -->
                            <button type="button" id="addmore" class="btn btn-primary radiusSendiri mb-2"><i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Kontainer</button>
                        <!-- </div> -->
                        <!-- <div class="card-body"> -->
                            <table class="table" id="sortable">
                                <thead>
                                    <tr>
                                        <th width="250">No. Kontainer</th>
                                        <th width="250">Seal</th>
                                        <th width="250">Tipe</th>
                                        <th width="250">Tujuan</th>
                                        <th width="250">Tgl Planning</th>
                                        <th width="20" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tb"> 
                                <tr>
                                    <td>
                                        <input type="text" id="no_kontainer" name="no_kontainer[]"class="form-control" value="">
                                    </td>
                                    <td>
                                        <input type="text" id="seal" name="seal[]"class="form-control" value="">
                                    </td>
                                    <td>
                                        <select class="form-control selectpicker" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                            <option value="">--Pilih Tipe Kontainer--</option>
                                            <option value="20">20Ft</option>
                                            <option value="40">40Ft</option>
                                        </select>
                                        <input type="hidden" name="hargaThc[]" value="">
                                        <input type="hidden" name="hargaLolo[]" value="">
                                        <input type="hidden" name="hargaApbs[]" value="">
                                        <input type="hidden" name="hargaCleaning[]" value="">
                                        <input type="hidden" name="hargaDocFee[]" value="">

                                    </td>
                                    <td>
                                        <select class="form-control selectpicker" name="tujuan[]" id="tujuan" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                            <option value="">--Pilih Tujuan--</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="tgl_brngkt_booking[]" autocomplete="off" class="date form-control tgl_booking"  placeholder="dd-M-yyyy" value="{{old('tgl_booking','')}}">     
                                    </td>
                                    <td align="center" class="text-danger">
                                        <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger radiusSendiri">
                                            <i class="fa fa-fw fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                
                                </tfoot>
                            </table>
                        <!-- </div>
                    </div> -->  
                    </div>
                </div> 
            </div>
            
            <div class="col-12">
                    <div class="card radiusSendiri">
                        <div class="card-header">
                            <h3 class="card-title">Keterangan Biaya</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                       <div class="card-body" >
                        <div class="d-flex justify-content-between" style="gap: 10px;">
                            <table class="table table-bordered" id="sortable" >
                                <thead>
                                    <tr>
                                        <th colspan="2">Biaya Sebelum Dooring</th>
                                    </tr>
                                </thead>
                                <tbody > 
                                    <tr>
                                        <th><span> <input type="checkbox" name="thc_cekbox" id="thc_cekbox"></span> THC</th>
                                        <td name="total_thc" id="total_thc">Harga</td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" name="lolo_cekbox" id="lolo_cekbox"></span> LOLO</th>
                                        <td name="total_lolo" id="total_lolo">Harga</td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" name="apbs_cekbox" id="apbs_cekbox"></span> APBS</th>
                                        <td name="total_apbs" id="total_apbs">Harga</td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" name="cleaning_cekbox" id="cleaning_cekbox"></span> CLEANING</th>
                                        <td name="total_cleaning" id="total_cleaning">Harga</td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" name="doc_fee_cekbox" id="doc_fee_cekbox"></span> DOC FEE</th>
                                        <td name="total_doc_fee" id="total_doc_fee">Harga</td>
                                    </tr>
                                    <tr>
                                        <th>SUB TOTAL</th>
                                        <th name="total_sblm_dooring" id="total_sblm_dooring">Harga</th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>

                            <table class="table table-bordered" id="sortable">
                                <thead>
                                    <tr>
                                        <th colspan="2">Biaya Setelah Dooring</th>
                                    </tr>
                                </thead>
                                <tbody > 
                                    <tr>
                                        <th>Tgl Bayar Jaminan</th>
                                        <td>Harga</td>
                                    </tr>
                                    <tr>
                                        <th>Total Jaminan</th>
                                        <td>Harga</td>
                                    </tr>
                                    <tr>
                                        <th>Potongan Jaminan</th>
                                        <td>Harga</td>
                                    </tr>
                                    <tr >
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <th> Nominal Kembali Jaminan </th>
                                        <td> Harga</td>
                                    </tr>
                                    <tr>
                                        <th> Tgl Jaminan Kembali </th>
                                        <td>Harga</td>
                                    </tr>
                                    
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                           
                         
                          
=======
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Pelabuhan Muat<span class="text-red">*</span></label>
                                        <input required type="text" name="pelabuhan_muat" class="form-control" value="{{old('pelabuhan_muat','')}}" >
                                    </div>     
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Pelabuhan Bongkar<span class="text-red">*</span></label>
                                        <input required type="text" name="pelauhan_bongkar" class="form-control" value="{{old('pelauhan_bongkar','')}}" >
                                    </div>              
                                </div>
                            </div>  
                                <!-- <div class="card radiusSendiri">
                            <div class="card-header"> -->
                                <button type="button" id="addmore" class="btn btn-primary radiusSendiri mb-2"><i class="fa fa-plus-circle" aria-hidden="true"></i>Tambah Kontainer</button>
                            <!-- </div> -->
                            <!-- <div class="card-body"> -->
                                <table class="table" id="sortable">
                                    <thead>
                                        <tr>
                                            <th width="250">No. Kontainer</th>
                                            <th width="250">Seal</th>
                                            <th width="250">Tipe</th>
                                            <th width="250">Tujuan</th>
                                            <th width="250">Tgl Booking</th>
                                            <th width="20" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tb"> 
                                    <tr>
                                        <td>
                                            <input type="text" id="no_kontainer" name="no_kontainer[]"class="form-control" value="">
                                        </td>
                                        <td>
                                            <input type="text" id="seal" name="seal[]"class="form-control" value="">
                                        </td>
                                        <td>
                                            <select class="form-control selectpicker" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                <option value="">--Pilih Tipe Kontainer--</option>
                                                <option value="20">20Ft</option>
                                                <option value="40">40Ft</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control selectpicker" name="tujuan[]" id="tujuan" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                <option value="">--Pilih Tujuan--</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="tgl_booking[]" autocomplete="off" class="date form-control tgl_booking"  placeholder="dd-M-yyyy" value="{{old('tgl_booking','')}}">     
                                        </td>
                                        <td align="center" class="text-danger">
                                            <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger radiusSendiri">
                                                <i class="fa fa-fw fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    
                                    </tfoot>
                                </table>
                            <!-- </div>
                        </div> -->  
                        </div>
                    </div> 
                </div>
                
                <div class="col-12">
                        <div class="card radiusSendiri">
                            <div class="card-header">
                                <h3 class="card-title">Keterangan Biaya</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button> -->
                                </div>
                            </div>
                           <div class="card-body" >
                               
                               <div class="row">
                                   
                                   <div class="col-6">
                                        <table class="table table-bordered" id="sortable">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">Total Biaya sebelum dooring</th>
                                                </tr>
                                            </thead>
                                            <tbody > 
                                                <tr>
                                                    <th>THC</th>
                                                    <td>Harga</td>
                                                </tr>
                                                <tr>
                                                    <th>LOLO</th>
                                                    <td>Harga</td>
                                                </tr>
                                                <tr>
                                                    <th>APBS</th>
                                                    <td>Harga</td>
                                                </tr>
                                                <tr>
                                                    <th>CLEANING</th>
                                                    <td>Harga</td>
                                                </tr>
                                                <tr>
                                                    <th>DOC FEE</th>
                                                    <td>Harga</td>
                                                </tr>
                                                <tr>
                                                    <th>SUB TOTAL</th>
                                                    <th>Harga</th>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-6">
                                        <table class="table table-bordered" id="sortable">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">Total Biaya Setelah Dooring</th>
                                                </tr>
                                            </thead>
                                            <tbody > 
                                                <tr>
                                                    <th>Tgl Bayar Jaminan</th>
                                                    <td>Harga</td>
                                                </tr>
                                                <tr>
                                                    <th>Total Jaminan</th>
                                                    <td>Harga</td>
                                                </tr>
                                                <tr>
                                                    <th>Potongan Jaminan</th>
                                                    <td>Harga</td>
                                                </tr>
                                                <tr >
                                                    <td colspan="2"></td>
                                                </tr>
                                                <tr>
                                                    <th> Nominal Kembali Jaminan </th>
                                                    <td> Harga</td>
                                                </tr>
                                                <tr>
                                                    <th> Tgl Jaminan Kembali </th>
                                                    <td>Harga</td>
                                                </tr>
                                                
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                    </div>
                               </div>
                              
                           </div>
>>>>>>> 434012c5445d1964c3d69548228a436b9cd2d1d7
                       </div>
                </div> 
                <!-- <div class="col-6">
                        <div class="card radiusSendiri">
                           <div class="card-header">
                           </div>
                           <div class="card-body">
                               <table class="table table-bordered" id="sortable">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Total Biaya Setelah Dooring</th>
                                        </tr>
                                    </thead>
                                    <tbody > 
                                        <tr>
                                            <th>Tgl Bayar Jaminan</th>
                                            <td>Harga</td>
                                        </tr>
                                        <tr>
                                            <th>Total Jaminan</th>
                                            <td>Harga</td>
                                        </tr>
                                        <tr>
                                            <th>Potongan Jaminan</th>
                                            <td>Harga</td>
                                        </tr>
                                        <tr >
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <th> Nominal Kembali Jaminan </th>
                                            <td> Harga</td>
                                        </tr>
                                        <tr>
                                            <th> Tgl Jaminan Kembali </th>
                                            <td>Harga</td>
                                        </tr>
                                        
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                               </table>
                           </div>
                       </div>
                </div>  -->
                
            </div>
        </div>
    </form>

<script type="text/javascript">
    $(document).ready(function() {
      // Listen for input events on all input fields

       // encode ubah array jadi json
    //decode ubah json jadi array
        var dataKeuangan = <?php echo json_encode($dataPengaturanKeuangan[0]); ?>;
        var harga20Ft = {
            'thc': dataKeuangan.thc_20ft,
            'lolo': dataKeuangan.lolo_20ft,
            'apbs': dataKeuangan.apbs_20ft,
            'cleaning': dataKeuangan.cleaning_20ft,
            'doc_fee': dataKeuangan.doc_fee_20ft,
        };
        // console.log(harga20Ft.thc)
        var harga40Ft = {
            'thc': dataKeuangan.thc_40ft,
            'lolo': dataKeuangan.lolo_40ft,
            'apbs': dataKeuangan.apbs_40ft,
            'cleaning': dataKeuangan.cleaning_40ft,
            'doc_fee': dataKeuangan.doc_fee_40ft,
        };
      

        // total_thc
        // total_lolo
        // total_apbs
        // total_cleaning
        // total_doc_fee
        // total_sblm_dooring

        setInterval(function() {
            //  $('#tb select[name="tipe[]"]').each(function() {
            //     var selectedValue = $(this).val();
            //     selectedValues.push(selectedValue);
            // });
            // console.log(selectedValues);
        }, 2000);
    //    <input type="hidden" name="hargaThc[]" value="">
    //     <input type="hidden" name="hargaLolo[]" value="">
    //     <input type="hidden" name="hargaApbs[]" value="">
    // <input type="hidden" name="hargaCleaning[]" value="">
    //     <input type="hidden" name="hargaDocFee[]" value="">
        // thc_cekbox
        // lolo_cekbox
        // apbs_cekbox
        // cleaning_cekbox
        // doc_fee_cekbox


        // harga20Ft.thc
        // harga20Ft.lolo
        // harga20Ft.apbs
        // harga20Ft.cleaning
        // harga20Ft.doc_fee
        var tampunganValueThc2040 = [];
        $('#thc_cekbox').on('change', function() {
            if ($(this).prop('checked')) {
                $('#tb select[name="tipe[]"]').each(function(i) {
                    var valueCombobox = $(this).val();
                    $('#tb input[name="hargaThc[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.thc : harga40Ft.thc);
                 });
            } else {
                tampunganValueThc2040 = [];
                 $('#tb input[name="hargaThc[]"]').each(function(i) {
                      $(this).val('');
                });
                console.log('Checkbox is not checked');
            }
        });
         var tampunganValueLolo2040 = [];
         $('#lolo_cekbox').on('change', function() {
            if ($(this).prop('checked')) {

                 $('#tb select[name="tipe[]"]').each(function(i) {
                    var valueCombobox = $(this).val();
                    $('#tb input[name="hargaLolo[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.lolo : harga40Ft.lolo);
            
                 });
                
            } else {
                tampunganValueLolo2040 = [];
                 $('#tb input[name="hargaLolo[]"]').each(function(i) {
                      $(this).eq(i).val('');
                });
                console.log('Checkbox is not checked');
            }
        });
        var tampunganValueApbs2040 = [];
         $('#apbs_cekbox').on('change', function() {
            if ($(this).prop('checked')) {

                 $('#tb select[name="tipe[]"]').each(function(i) {
                    var valueCombobox = $(this).val();
                    $('#tb input[name="hargaApbs[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.apbs : harga40Ft.apbs);
                 });
                
            } else {
                tampunganValueApbs2040 = [];
                $('#tb input[name="hargaApbs[]"]').each(function(i) {
                      $(this).eq(i).val('');
                });
                console.log('Checkbox is not checked');
            }
        });
        var tampunganValueCleaning2040 = [];
         $('#cleaning_cekbox').on('change', function() {
            if ($(this).prop('checked')) {

                $('#tb select[name="tipe[]"]').each(function(i) {
                    var valueCombobox = $(this).val();
                    $('#tb input[name="hargaCleaning[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.cleaning : harga40Ft.cleaning);
             
                 });
                
            } else {
                tampunganValueCleaning2040 = [];
                 $('#tb input[name="hargaCleaning[]"]').each(function(i) {
                      $(this).eq(i).val('');
                });
                console.log('Checkbox is not checked');
            }
        });
        var tampunganValueDocfee2040 = [];
         $('#doc_fee_cekbox').on('change', function() {
            if ($(this).prop('checked')) {

                 $('#tb select[name="tipe[]"]').each(function(i) {
                    var valueCombobox = $(this).val();
                    $('#tb input[name="hargaDocFee[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.doc_fee : harga40Ft.doc_fee);
                 });
            } else {
                tampunganValueDocfee2040 = [];
                  $('#tb input[name="hargaDocFee[]"]').each(function(i) {
                        $(this).eq(i).val('');

                });
                console.log('Checkbox is not checked');
            }
        });
//     $('#tb input[name="hargaThc[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.thc : harga40Ft.thc);
// $('#tb input[name="hargaLolo[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.lolo : harga40Ft.lolo);
// $('#tb input[name="hargaApbs[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.apbs : harga40Ft.apbs);
// $('#tb input[name="hargaCleaning[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.cleaning : harga40Ft.cleaning);
// $('#tb input[name="hargaDocFee[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.doc_fee : harga40Ft.doc_fee);
//                         $('#tb input[name="hargaThc[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.thc : harga40Ft.thc);
        function updateId()
        {
            $('#tb select[name="tipe[]"]').each(function(i) {   
                // console.log(i);     
                $(this).on('change', function() {

                    if ($('#thc_cekbox').prop('checked')) {
                       
                        //this val ini ambil value dari combobox bukan dari checkbox
                        var valueCombobox = $(this).val();
                        $('#tb input[name="hargaThc[]"]').each(function(index) {
                             $('#tb input[name="hargaThc[]"]').eq(i).val(valueCombobox == '20' ? harga20Ft.thc : harga40Ft.thc);
                            if(valueCombobox=='20')
                            {
                                $(this)[index].value=harga20Ft.thc;
                            }
                            else
                            {
                                $(this)[index].value=harga40Ft.thc;

                            }
                        });

                    }   
                    if ($('#lolo_cekbox').prop('checked')) {
                          var valueCombobox = $(this).val();
                            $('#tb input[name="hargaLolo[]"]').each(function(index) {
                                if(valueCombobox=='20')
                                {
                                    $(this)[index].value=harga20Ft.lolo;
                                }
                                else
                                {
                                    $(this)[index].value=harga40Ft.lolo;

                                }
                            });
                    }  
                    if ($('#apbs_cekbox').prop('checked')) {
                       
                          var valueCombobox = $(this).val();
                            $('#tb input[name="hargaApbs[]"]').each(function(index) {
                                if(valueCombobox=='20')
                                {
                                    $(this)[index].value=harga20Ft.apbs;
                                }
                                else
                                {
                                    $(this)[index].value=harga40Ft.apbs;

                                }
                            });
                        
                    }        
                    if ($('#cleaning_cekbox').prop('checked')) {
                        
                          var valueCombobox = $(this).val();
                            $('#tb input[name="hargaCleaning[]"]').each(function(index) {
                                if(valueCombobox=='20')
                                {
                                    $(this)[index].value=harga20Ft.cleaning;
                                }
                                else
                                {
                                    $(this)[index].value=harga40Ft.cleaning;

                                }
                            });
                    }
                    if ($('#doc_fee_cekbox').prop('checked')) {
                         var valueCombobox = $(this).val();
                        $('#tb input[name="hargaDocFee[]"]').each(function(index) {
                            if(valueCombobox=='20')
                            {
                                $(this)[index].value=harga20Ft.doc_fee;
                            }
                            else
                            {
                                $(this)[index].value=harga40Ft.doc_fee;

                            }
                        });
                    }
                    // console.log('Thc:'+tampunganValueThc2040);
                    // console.log('Lolo:'+tampunganValueLolo2040);
                    // console.log('Apbs:'+tampunganValueApbs2040);
                    // console.log('Cleaning:'+tampunganValueCleaning2040);
                    // console.log('Docfee:'+tampunganValueDocfee2040);
                });  
                    
            });

        }
        updateId();
        $('input[type="text"]').on('input', function() {
            var inputValue = $(this).val();
            var uppercaseValue = inputValue.toUpperCase();
            $(this).val(uppercaseValue);
        });

        $('#tgl_sandar').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language:'en',
                endDate: "0d"
            });
        $(document).on('focus', '.tgl_booking', function() {
            $(this).datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                endDate: "0d"
            });
        });
    $("#addmore").on("click",function(){
        $('#tb').append(
            `<tr>
                <td>
                    <input type="text" id="no_kontainer" name="no_kontainer[]"class="form-control" value="">
                </td>
                <td>
                    <input type="text" id="seal" name="seal[]"class="form-control" value="">
                </td>
                <td>
                    <select class="form-control selectpicker" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                        <option value="">--Pilih Tipe Kontainer--</option>
                        <option value="20">20Ft</option>
                        <option value="40">40Ft</option>
                    </select>
                    <input type="hidden" name="hargaThc[]" value="">
                    <input type="hidden" name="hargaLolo[]" value="">
                    <input type="hidden" name="hargaApbs[]" value="">
                    <input type="hidden" name="hargaCleaning[]" value="">
                    <input type="hidden" name="hargaDocFee[]" value="">
                </td>
                <td>
                    <select class="form-control selectpicker" name="tujuan[]" id="tujuan" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                        <option value="">--Pilih Tujuan--</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="tgl_booking[]" autocomplete="off" class="date form-control tgl_booking" placeholder="dd-M-yyyy" value="{{old('tgl_booking','')}}">     
                </td>
                <td align="center" class="text-danger">
                    <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger radiusSendiri">
                        <i class="fa fa-fw fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`
        );
        updateId();
        $('.selectpicker').selectpicker('refresh');
        // $('#save').removeAttr('hidden',true);
    });
  
    });
</script>
@endsection


