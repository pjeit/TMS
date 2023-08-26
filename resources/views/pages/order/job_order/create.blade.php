
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Pelabuhan Muat<span class="text-red">*</span></label>
                                    <input required type="text" name="pelabuhan_muat" class="form-control" value="{{old('pelabuhan_muat','')}}" >
                                </div>     
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Pelabuhan Bongkar<span class="text-red">*</span></label>
                                    <input required type="text" name="pelauhan_bongkar" class="form-control" value="{{old('pelauhan_bongkar','')}}" >
                                </div>              
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Free Time<span class="text-red">*</span></label>
                                    <input required type="text" name="pelauhan_bongkar" class="form-control" value="{{old('freetime','')}}" >
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
                                    {{-- <tr >
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
                                            <input type="text" name="tgl_brngkt_booking[]" autocomplete="off" class="date form-control tgl_planning"  placeholder="dd-M-yyyy" value="{{old('tgl_planning','')}}">     
                                        </td>
                                        <td align="center" class="text-danger">
                                            <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger hapus radiusSendiri">
                                                <i class="fa fa-fw fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr> --}}
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
                                        <td name="total_thc"><input type="text" id="total_thc" class="form-control" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" name="lolo_cekbox" id="lolo_cekbox"></span> LOLO</th>
                                        <td name="total_lolo"><input type="text" id="total_lolo" class="form-control" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" name="apbs_cekbox" id="apbs_cekbox"></span> APBS</th>
                                        <td name="total_apbs"><input type="text" id="total_apbs" class="form-control" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" name="cleaning_cekbox" id="cleaning_cekbox"></span> CLEANING</th>
                                        <td name="total_cleaning"><input type="text" id="total_cleaning" class="form-control" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" name="doc_fee_cekbox" id="doc_fee_cekbox"></span> DOC FEE</th>
                                        <td name="total_doc_fee"><input type="text" id="total_doc_fee" class="form-control" readonly></td>
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
                                        <td>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" name="tgl_bayar_jaminan" autocomplete="off" class="date form-control" id="tgl_bayar_jaminan" placeholder="dd-M-yyyy" value="">     
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Total Jaminan</th>
                                        <td>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><b>Rp.</b></span>
                                                </div>
                                                <input type="text" class="form-control uang numaja" id="total_jaminan" name="total_jaminan">
                                            </div>
                                        </td>
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

        // master harga tipe
            var dataKeuangan = <?php echo json_encode($dataPengaturanKeuangan[0]); ?>;
            var harga20Ft = {
                'thc': dataKeuangan.thc_20ft,
                'lolo': dataKeuangan.lolo_20ft,
                'apbs': dataKeuangan.apbs_20ft,
                'cleaning': dataKeuangan.cleaning_20ft,
                'doc_fee': dataKeuangan.doc_fee_20ft,
            };
            var harga40Ft = {
                'thc': dataKeuangan.thc_40ft,
                'lolo': dataKeuangan.lolo_40ft,
                'apbs': dataKeuangan.apbs_40ft,
                'cleaning': dataKeuangan.cleaning_40ft,
                'doc_fee': dataKeuangan.doc_fee_40ft,
            };
        // end of master harga tipe

        // handling tanggal
            $('#tgl_sandar').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language:'en',
                endDate: "0d"
            });
            $('#tgl_bayar_jaminan').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
            });
            $(document).on('focus', '.tgl_planning', function() {
                $(this).datepicker({
                    autoclose: true,
                    format: "dd-M-yyyy",
                    todayHighlight: true,
                    language: 'en',
                    endDate: "0d"
                });
            });
        // end of handling tgl

        $(document).on('change', '#customer', function(event) {
            // Clear existing options
            console.log('aaa '+$('.tujuanC').length);
            // Get selected value from #customer
            var selectedValue = this.value;
            // const id_tujuanSelect = document.getElementById('tujuan');
            // var selectElement = document.querySelector('.tujuan');

            $.ajax({
                url: '/booking/getTujuan/' + selectedValue,
                method: 'GET',
                success: function(response) { 
                    $('.form-control.selectpicker.tujuanC').empty().append('<option value="">--Pilih Tujuan--</option>');

                    response.forEach(tujuan => {
                    var option = new Option(tujuan.nama_tujuan, tujuan.id);
                        $('.form-control.selectpicker.tujuanC').append('<option value="'+tujuan.id+'">'+tujuan.nama_tujuan+'</option>');
                    });
                    $('.form-control.selectpicker.tujuanC').selectpicker({
                        noneSelectedText: "--Pilih Tujuan--"
                    });
                    $(".form-control.selectpicker.tujuanC").selectpicker("refresh");
                },
                error: function(xhr, status, error) {
                    console.error(error); // Handle errors if necessary
                }
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

            // get tujuan
            $.ajax({
                url: '/booking/getTujuan/' + selectedValue,
                method: 'GET',
                success: function(response) {
                    response.forEach(tujuan => {
                        const option = document.createElement('option');
                        var xxx = `<option id="${tujuan.id}">${tujuan.nama_tujuan}</option>`;
                        // store data ke dataOption buat di fetch ketika tambah data
                        dataOption += xxx;
                    });

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
                                <input type="hidden" readonly class="hargaThc" name="hargaThc[]" value="">
                                <input type="hidden" readonly class="hargaLolo" name="hargaLolo[]" value="">
                                <input type="hidden" readonly class="hargaApbs" name="hargaApbs[]" value="">
                                <input type="hidden" readonly class="hargaCleaning" name="hargaCleaning[]" value="">
                                <input type="hidden" readonly class="hargaDocFee" name="hargaDocFee[]" value="">
                            </td>
                            <td>
                                <select class="form-control selectpicker tujuanC" name="tujuan[]" id="tujuan" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                    <option value="">--Pilih Tujuan--</option>
                                    `+dataOption+`
                                </select>
                            </td>
                            <td>
                                <input type="text" name="tgl_planning[]" autocomplete="off" class="date form-control tgl_planning" placeholder="dd-M-yyyy" value="{{old('tgl_planning','')}}">     
                            </td>
                            <td align="center" class="text-danger">
                                <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger radiusSendiri hapus">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>`
                    );
                    $('.selectpicker').selectpicker('refresh');

                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        
            // $('#save').removeAttr('hidden',true);
        });

        // logic hitung biaya
            $( document ).on( 'change', '.tipeKontainer', function (event) {
                // biar datanya ga ke get 2x ketika di get val
                event.stopPropagation();
                var selectedValue = $(event.target).val();
                var selectedValue = $(this).val();

                var parentTd = $(this).closest('td');
                parentTd.find('.hargaThc').val(selectedValue == '20' ? harga20Ft.thc : harga40Ft.thc);
                parentTd.find('.hargaLolo').val(selectedValue == '20' ? harga20Ft.lolo : harga40Ft.lolo);
                parentTd.find('.hargaApbs').val(selectedValue == '20' ? harga20Ft.apbs : harga40Ft.apbs);
                parentTd.find('.hargaCleaning').val(selectedValue == '20' ? harga20Ft.cleaning : harga40Ft.cleaning);
                parentTd.find('.hargaDocFee').val(selectedValue == '20' ? harga20Ft.doc_fee : harga40Ft.doc_fee);

                // tiap ada perubahan di class tipekontainer, di akhir akan di hitung total harganya
                calculateTotalHarga();
            });
            
            $( document ).on( 'click', '.hapus', function (event) {
                // ketika hapus data, di hitung lagi total harganya
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


