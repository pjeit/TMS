
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
        $('.selectpicker').selectpicker('refresh');
        // $('#save').removeAttr('hidden',true);
    });
  
    });
</script>
@endsection


