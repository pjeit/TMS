
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
    <form action="{{ route('pembayaran_jo.store') }}" method="POST" >
      @csrf
        <div class="row m-2">
             <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('pembayaran_jo.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <a href="{{ route('pembayaran_jo.index') }}"class="btn btn-success radiusSendiri"><i class="fa fa-check" aria-hidden="true"></i> Setujui</a>

                        {{-- <button type="submit" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button> --}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6" >
                                <div class="form-group">
                                    <label for="">Pengirim<span class="text-red">*</span></label>
                                        <select class="form-control selectpicker"  id='customer' name="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled>
                                        <option value="0">PT. Pasifik Global Makmur</option>
                                        {{-- @foreach ($dataCustomer as $cust)
                                            <option value="{{$cust->id}}">{{ $cust->nama }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                 <div class="form-group ">
                                    <label for="">Pelayaran</label>
                                    <select class="form-control selectpicker"  id='supplier' name="supplier" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled>
                                        <option value="0">PT. TANTO INTI LINE</option>
                                        {{-- @foreach ($dataSupplier as $sup)
                                            <option value="{{$sup->id}}">{{ $sup->nama }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <div class="form-group ">
                                    <label for="">No. BL<span class="text-red">*</span></label>
                                    <input required type="text" name="nama_pic" class="form-control" value="{{old('nama_pic','23450023929BL')}}" readonly>
                                </div>  
                                  <div class="form-group">
                                    <label for="">Free Time<span class="text-red">*</span></label>
                                    <input required type="text" name="pelauhan_bongkar" class="form-control" value="{{old('freetime','5')}}" readonly>
                                </div>  
                            </div>
                             <div class="col-6">
                                <div class="form-group">
                                        <label for="tgl_sandar">Tanggal Sandar</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" name="tgl_sandar" autocomplete="off" class="date form-control" id="tgl_sandar" placeholder="dd-M-yyyy" value="{{old('tgl_sandar','20-aug-2023')}}" readonly>     
                                    </div>
                                </div>  
                                <div class="form-group">
                                    <label for="">Pelabuhan Muat<span class="text-red">*</span></label>
                                    <input required type="text" name="pelabuhan_muat" class="form-control" value="{{old('pelabuhan_muat','Medan')}}" readonly>
                                </div> 
                                <div class="form-group">
                                    <label for="">Pelabuhan Bongkar<span class="text-red">*</span></label>
                                    <input required type="text" name="pelauhan_bongkar" class="form-control" value="{{old('pelauhan_bongkar','SBY')}}" readonly>
                                </div>  
                               
                        </div>
                        </div>
                       
                            <!-- <div class="card radiusSendiri">
                        <div class="card-header"> -->
                            {{-- <button type="button" id="addmore" class="btn btn-primary radiusSendiri mb-2"><i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Kontainer</button> --}}
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
                                        {{-- <th width="20" class="text-center">Aksi</th> --}}
                                    </tr>
                                </thead>
                                <tbody id="tb"> 
                                    <tr>
                                        <td>
                                            <input type="text" id="no_kontainer" name="no_kontainer[]"class="form-control no_kontainerx" value="TAKU-233333-3" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="seal" name="seal[]"class="form-control" value="F334433" readonly>
                                        </td>
                                        <td>
                                            <select class="form-control selectpicker tipeKontainer" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled >
                                                {{-- <option value="">--Pilih Tipe Kontainer--</option> --}}
                                                <option value="20">20Ft</option>
                                                {{-- <option value="40">40Ft</option> --}}
                                            </select>
                                            {{-- <input type="text" readonly class="hargaThc" name="hargaThc[]" value="">
                                            <input type="text" readonly class="hargaLolo" name="hargaLolo[]" value="">
                                            <input type="text" readonly class="hargaApbs" name="hargaApbs[]" value="">
                                            <input type="text" readonly class="hargaCleaning" name="hargaCleaning[]" value="">
                                            <input type="text" readonly class="hargaDocFee" name="hargaDocFee[]" value=""> --}}
                                        </td>
                                        <td>
                                            <select class="form-control selectpicker" name="tujuan[]" id="tujuan" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled >
                                                {{-- <option value="">--Pilih Tujuan--</option> --}}
                                                <option value="20">Pt.Yanasurya</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="tgl_brngkt_booking[]" autocomplete="off" class="date form-control tgl_booking"  placeholder="dd-M-yyyy" value="18-Aug-2023" readonly>     
                                        </td>
                                        {{-- <td align="center" class="text-danger">
                                           <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger hapus radiusSendiri">
                                                <i class="fa fa-fw fa-trash-alt"></i>
                                            </button> 
                                        </td> --}}
                                    </tr>
                                         <tr>
                                        <td>
                                            <input type="text" id="no_kontainer" name="no_kontainer[]"class="form-control no_kontainerx" value="TAKU-233333-3" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="seal" name="seal[]"class="form-control" value="F334433" readonly>
                                        </td>
                                        <td>
                                            <select class="form-control selectpicker tipeKontainer" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled >
                                                {{-- <option value="">--Pilih Tipe Kontainer--</option> --}}
                                                <option value="20">20Ft</option>
                                                {{-- <option value="40">40Ft</option> --}}
                                            </select>
                                            {{-- <input type="text" readonly class="hargaThc" name="hargaThc[]" value="">
                                            <input type="text" readonly class="hargaLolo" name="hargaLolo[]" value="">
                                            <input type="text" readonly class="hargaApbs" name="hargaApbs[]" value="">
                                            <input type="text" readonly class="hargaCleaning" name="hargaCleaning[]" value="">
                                            <input type="text" readonly class="hargaDocFee" name="hargaDocFee[]" value=""> --}}
                                        </td>
                                        <td>
                                            <select class="form-control selectpicker" name="tujuan[]" id="tujuan" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled >
                                                {{-- <option value="">--Pilih Tujuan--</option> --}}
                                                <option value="20">Pt.Yanasurya</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="tgl_brngkt_booking[]" autocomplete="off" class="date form-control tgl_booking"  placeholder="dd-M-yyyy" value="18-Aug-2023" readonly>     
                                        </td>
                                        {{-- <td align="center" class="text-danger">
                                           <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger hapus radiusSendiri">
                                                <i class="fa fa-fw fa-trash-alt"></i>
                                            </button> 
                                        </td> --}}
                                    </tr>
                                         <tr>
                                        <td>
                                            <input type="text" id="no_kontainer" name="no_kontainer[]"class="form-control no_kontainerx" value="TAKU-233333-3" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="seal" name="seal[]"class="form-control" value="F334433" readonly>
                                        </td>
                                        <td>
                                            <select class="form-control selectpicker tipeKontainer" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled >
                                                {{-- <option value="">--Pilih Tipe Kontainer--</option> --}}
                                                <option value="20">20Ft</option>
                                                {{-- <option value="40">40Ft</option> --}}
                                            </select>
                                            {{-- <input type="text" readonly class="hargaThc" name="hargaThc[]" value="">
                                            <input type="text" readonly class="hargaLolo" name="hargaLolo[]" value="">
                                            <input type="text" readonly class="hargaApbs" name="hargaApbs[]" value="">
                                            <input type="text" readonly class="hargaCleaning" name="hargaCleaning[]" value="">
                                            <input type="text" readonly class="hargaDocFee" name="hargaDocFee[]" value=""> --}}
                                        </td>
                                        <td>
                                            <select class="form-control selectpicker" name="tujuan[]" id="tujuan" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled >
                                                {{-- <option value="">--Pilih Tujuan--</option> --}}
                                                <option value="20">Pt.Yanasurya</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="tgl_brngkt_booking[]" autocomplete="off" class="date form-control tgl_booking"  placeholder="dd-M-yyyy" value="18-Aug-2023" readonly>     
                                        </td>
                                        {{-- <td align="center" class="text-danger">
                                           <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger hapus radiusSendiri">
                                                <i class="fa fa-fw fa-trash-alt"></i>
                                            </button> 
                                        </td> --}}
                                    </tr>
                                         <tr>
                                        <td>
                                            <input type="text" id="no_kontainer" name="no_kontainer[]"class="form-control no_kontainerx" value="TAKU-233333-3" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="seal" name="seal[]"class="form-control" value="F334433" readonly>
                                        </td>
                                        <td>
                                            <select class="form-control selectpicker tipeKontainer" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled >
                                                {{-- <option value="">--Pilih Tipe Kontainer--</option> --}}
                                                <option value="20">20Ft</option>
                                                {{-- <option value="40">40Ft</option> --}}
                                            </select>
                                            {{-- <input type="text" readonly class="hargaThc" name="hargaThc[]" value="">
                                            <input type="text" readonly class="hargaLolo" name="hargaLolo[]" value="">
                                            <input type="text" readonly class="hargaApbs" name="hargaApbs[]" value="">
                                            <input type="text" readonly class="hargaCleaning" name="hargaCleaning[]" value="">
                                            <input type="text" readonly class="hargaDocFee" name="hargaDocFee[]" value=""> --}}
                                        </td>
                                        <td>
                                            <select class="form-control selectpicker" name="tujuan[]" id="tujuan" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled >
                                                {{-- <option value="">--Pilih Tujuan--</option> --}}
                                                <option value="20">Pt.Yanasurya</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="tgl_brngkt_booking[]" autocomplete="off" class="date form-control tgl_booking"  placeholder="dd-M-yyyy" value="18-Aug-2023" readonly>     
                                        </td>
                                        {{-- <td align="center" class="text-danger">
                                           <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger hapus radiusSendiri">
                                                <i class="fa fa-fw fa-trash-alt"></i>
                                            </button> 
                                        </td> --}}
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
                                        <th><span> <input disabled type="checkbox" name="thc_cekbox" id="thc_cekbox" checked></span> THC</th>
                                        <td name="total_thc"><input type="text" id="total_thc" class="form-control" value="Rp.550.000,00" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input disabled type="checkbox" name="lolo_cekbox" id="lolo_cekbox" checked></span> LOLO</th>
                                        <td name="total_lolo"><input type="text" id="total_lolo" class="form-control" value="Rp.650.000,00" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input disabled type="checkbox" name="apbs_cekbox" id="apbs_cekbox"></span> APBS</th>
                                        <td name="total_apbs"><input type="text" id="total_apbs" class="form-control" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input disabled type="checkbox" name="cleaning_cekbox" id="cleaning_cekbox"></span> CLEANING</th>
                                        <td name="total_cleaning"><input type="text" id="total_cleaning" class="form-control" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input disabled type="checkbox" name="doc_fee_cekbox" id="doc_fee_cekbox"></span> DOC FEE</th>
                                        <td name="total_doc_fee"><input type="text" id="total_doc_fee" class="form-control" readonly></td>
                                    </tr>
                                    <tr>
                                        <th>SUB TOTAL</th>
                                        <th name="total_sblm_dooring" id="total_sblm_dooring">Rp.1.200.000,00</th>
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
                                        <td><input type="text" name="" class="form-control" value="24-aug-2023" readonly></td>
                                    </tr>
                                    <tr>
                                        <th>Total Jaminan</th>
                                        <td><input type="text" name="" class="form-control numaja" value="Rp. 2.500.00,00" readonly></td>
                                    </tr>
                                    {{-- <tr>
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
                                    </tr> --}}
                                    
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
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

<script>
</script>
<script type="text/javascript">
    $(document).ready(function() {

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

        $("#addmore").on("click",function(event){
            var customerId = $("#customer").val();
            if(customerId == 0 || customerId == null || customerId == ''){
                Swal.fire(
                    '',
                    'Harap isi data pengirim dahulu.',
                    'error'
                );
                return false;
            }

            var selectedValue = customerId;
            let dataOption = ''; // Initialize as an array

            $.ajax({
                url: '/booking/getTujuan/' + selectedValue,
                method: 'GET',
                success: function(response) {
                    response.forEach(tujuan => {
                        const option = document.createElement('option');
                        var xxx = `<option id="${tujuan.id}">${tujuan.nama_tujuan}</option>`;
                        dataOption += xxx;
                        // console.log('xxx '+xxx);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
            console.log('dataOption '+dataOption);


            $('#tb').append(
                `<tr>
                    <td>
                        <input type="text" id="no_kontainer" name="no_kontainer[]"class="form-control no_kontainerx" value="">
                    </td>
                    <td>
                        <input type="text" id="seal" name="seal[]"class="form-control" value="">
                    </td>
                    <td>
                        <select class="form-control selectpicker tipeKontainer" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                            <option value="">--Pilih Tipe Kontainer--</option>
                            <option value="20">20Ft</option>
                            <option value="40">40Ft</option>
                        </select>
                        <input type="text" readonly class="hargaThc" name="hargaThc[]" value="">
                        <input type="text" readonly class="hargaLolo" name="hargaLolo[]" value="">
                        <input type="text" readonly class="hargaApbs" name="hargaApbs[]" value="">
                        <input type="text" readonly class="hargaCleaning" name="hargaCleaning[]" value="">
                        <input type="text" readonly class="hargaDocFee" name="hargaDocFee[]" value="">
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
                        <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger radiusSendiri hapus">
                            <i class="fa fa-fw fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>`
            );
            $('.selectpicker').selectpicker('refresh');
            // $('#save').removeAttr('hidden',true);
        });

        // logic hitung biaya
            $( document ).on( 'change', '.tipeKontainer', function (event) {
                // ini buat biar klik sesuatu di anaknya, tdnya ga keeksekusi
                event.stopPropagation();
                var selectedValue = $(event.target).val();
                var selectedValue = $(this).val();

                //closest itu misal 
                // <td dia nyarik ini closestnya kan parent>
                //     <select class="form-control selectpicker tipeKontainer" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                //         <option value="">--Pilih Tipe Kontainer--</option>
                //         <option value="20">20Ft</option>
                //         <option value="40">40Ft</option>
                //     </select> trs nyarik anak" nya
                //     <input type="text" readonly class="hargaThc" name="hargaThc[]" value="">
                //     <input type="text" readonly class="hargaLolo" name="hargaLolo[]" value="">
                //     <input type="text" readonly class="hargaApbs" name="hargaApbs[]" value="">
                //     <input type="text" readonly class="hargaCleaning" name="hargaCleaning[]" value="">
                //     <input type="text" readonly class="hargaDocFee" name="hargaDocFee[]" value="">
                // </td>

                var parentTd = $(this).closest('td');
                parentTd.find('.hargaThc').val(selectedValue == '20' ? harga20Ft.thc : harga40Ft.thc);
                parentTd.find('.hargaLolo').val(selectedValue == '20' ? harga20Ft.lolo : harga40Ft.lolo);
                parentTd.find('.hargaApbs').val(selectedValue == '20' ? harga20Ft.apbs : harga40Ft.apbs);
                parentTd.find('.hargaCleaning').val(selectedValue == '20' ? harga20Ft.cleaning : harga40Ft.cleaning);
                parentTd.find('.hargaDocFee').val(selectedValue == '20' ? harga20Ft.doc_fee : harga40Ft.doc_fee);

                calculateTotalHarga();
            });
            
            $( document ).on( 'click', '.hapus', function (event) {
                calculateTotalHarga();
            });

            function calculateTotalHarga() {
                var totalhargaThc = 0;
                var totalhargaLolo = 0;
                var totalhargaApbs = 0;
                var totalhargaCleaning = 0;
                var totalhargaDocFee = 0;

                $('#total_thc').val(totalhargaThc);
                $('#total_lolo').val(totalhargaLolo);
                $('#total_apbs').val(totalhargaApbs);
                $('#total_cleaning').val(totalhargaCleaning);
                $('#total_doc_fee').val(totalhargaDocFee);
                
                $('.hargaThc').each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    totalhargaThc += value;
                    $('#total_thc').val(totalhargaThc);
                });
                $('.hargaLolo').each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    totalhargaLolo += value;
                    $('#total_lolo').val(totalhargaLolo);
                });
                $('.hargaApbs').each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    totalhargaApbs += value;
                    $('#total_apbs').val(totalhargaApbs);
                });
                $('.hargaCleaning').each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    totalhargaCleaning += value;
                    $('#total_cleaning').val(totalhargaCleaning);
                });
                $('.hargaDocFee').each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    totalhargaDocFee += value;
                    $('#total_doc_fee').val(totalhargaDocFee);
                });
                
            }
        // end of logic hitung biaya

    });
</script>

@endsection


