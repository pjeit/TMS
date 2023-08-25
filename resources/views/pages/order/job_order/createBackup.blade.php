
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
                                </div>
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
                           
                         
                          
                       </div>
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
    </form>

<script type="text/javascript">
    $(document).ready(function() {
      // Listen for input events on all input fields

       // encode ubah array jadi json
    //decode ubah json jadi array
        var dataKeuangan = <?php echo json_encode($dataPengaturanKeuangan[0]); ?>;
        console.log(dataKeuangan.apbs_20ft)

        var hargaApbs = {
            '20': dataKeuangan.apbs_20ft,
            '40': dataKeuangan.apbs_40ft
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
    //     <input type="hidden" name="hargaDocFee[]" value="">
        // thc_cekbox
        // lolo_cekbox
        // apbs_cekbox
        // cleaning_cekbox
        // doc_fee_cekbox
        var tampunganValueThc2040 = [];
        $('#thc_cekbox').on('change', function() {
            if ($(this).prop('checked')) {

                $('#tb select[name="tipe[]"]').each(function() {
                    var valueCombobox = $(this).val();
                    $('#tb input[name="hargaThc[]"]').each(function(i) {
                        if(valueCombobox=='20')
                        {
                            $(this)[i].val(20000);

                        }
                    });
                    // tampunganValueThc2040.push(valueCombobox);
                 });
                console.log('Thc:'+tampunganValueThc2040);
                $(tampunganValueThc2040).each(function(i) {
                    var valueCombobox = $(this).val();
                    tampunganValueThc2040.push(valueCombobox);
                 });
                
            } else {
                tampunganValueThc2040 = [];
                console.log('Thc Kosong:'+tampunganValueThc2040);
                console.log('Checkbox is not checked');
            }
        });
         var tampunganValueLolo2040 = [];
         $('#lolo_cekbox').on('change', function() {
            if ($(this).prop('checked')) {

                $('#tb select[name="tipe[]"]').each(function() {
                    var valueCombobox = $(this).val();
                    tampunganValueLolo2040.push(valueCombobox);
                 });
                console.log('Lolo:'+tampunganValueLolo2040);
                
            } else {
                tampunganValueLolo2040 = [];
                console.log('Lolo Kosong:'+tampunganValueLolo2040);
                console.log('Checkbox is not checked');
            }
        });
        var tampunganValueApbs2040 = [];
         $('#apbs_cekbox').on('change', function() {
            if ($(this).prop('checked')) {

                $('#tb select[name="tipe[]"]').each(function() {
                    var valueCombobox = $(this).val();
                    tampunganValueApbs2040.push(valueCombobox);
                 });
                console.log('Apbs:'+tampunganValueApbs2040);
                
            } else {
                tampunganValueApbs2040 = [];
                console.log('Apbs Kosong:'+tampunganValueApbs2040);
                console.log('Checkbox is not checked');
            }
        });
        var tampunganValueCleaning2040 = [];
         $('#cleaning_cekbox').on('change', function() {
            if ($(this).prop('checked')) {

                $('#tb select[name="tipe[]"]').each(function() {
                    var valueCombobox = $(this).val();
                    tampunganValueCleaning2040.push(valueCombobox);
                 });
                console.log('Cleaning:'+tampunganValueCleaning2040);
                
            } else {
                tampunganValueCleaning2040 = [];
                console.log('Cleaning Kosong:'+tampunganValueCleaning2040);
                console.log('Checkbox is not checked');
            }
        });
        var tampunganValueDocfee2040 = [];
         $('#doc_fee_cekbox').on('change', function() {
            if ($(this).prop('checked')) {

                $('#tb select[name="tipe[]"]').each(function() {
                    var valueCombobox = $(this).val();
                    tampunganValueDocfee2040.push(valueCombobox);
                 });
                console.log('DocFee:'+tampunganValueDocfee2040);
                
            } else {
                tampunganValueDocfee2040 = [];
                console.log('DocFee Kosong:'+tampunganValueDocfee2040);
                console.log('Checkbox is not checked');
            }
        });
    
        function updateId()
        {
            $('#tb select[name="tipe[]"]').each(function(i) {   
                // console.log(i);     
                $(this).on('change', function() {

                    if ($('#thc_cekbox').prop('checked')) {
                        if(tampunganValueThc2040.length > 0)
                        {
                            //this val ini ambil value dari combobox bukan dari checkbox
                            tampunganValueThc2040[i] = $(this).val(); 
                        }    
                    }   
                    if ($('#lolo_cekbox').prop('checked')) {
                         
                        if(tampunganValueLolo2040.length > 0)
                        {
                            tampunganValueLolo2040[i] = $(this).val(); 
                        }
                    }  
                    if ($('#apbs_cekbox').prop('checked')) {
                        if(tampunganValueApbs2040.length > 0)
                        {
                            tampunganValueApbs2040[i] = $(this).val(); 
                        }
                        
                    }        
                    if ($('#cleaning_cekbox').prop('checked')) {
                        if(tampunganValueCleaning2040.length > 0)
                        {
                            tampunganValueCleaning2040[i] = $(this).val(); 
                        }
                    }
                    if ($('#doc_fee_cekbox').prop('checked')) {
                         
                        if(tampunganValueDocfee2040.length > 0)
                        {
                            tampunganValueDocfee2040[i] = $(this).val(); 
                        }
                    }
                    console.log('Thc:'+tampunganValueThc2040);
                    console.log('Lolo:'+tampunganValueLolo2040);
                    console.log('Apbs:'+tampunganValueApbs2040);
                    console.log('Cleaning:'+tampunganValueCleaning2040);
                    console.log('Docfee:'+tampunganValueDocfee2040);
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


