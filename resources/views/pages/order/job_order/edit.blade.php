
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
                                            <option value="{{$cust->id}}" <?= $data->id_customer == $cust->id ? 'selected':''; ?> >{{ $cust->nama }}</option>
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
                                            <option value="{{$sup->id}}" <?= $data->id_supplier == $sup->id? 'selected':''; ?> >{{ $sup->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="">No. BL<span class="text-red">*</span></label>
                                    <input required type="text" name="no_bl" class="form-control" value="{{$data->no_bl}}" >
                                </div>           
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tgl_sandar">Tanggal Sandar</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" name="tgl_sandar" autocomplete="off" class="date form-control" id="tgl_sandar" placeholder="dd-M-yyyy" value="{{$data->tgl_sandar}}">     
                                    </div>
                                </div>           
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Pelabuhan Muat<span class="text-red">*</span></label>
                                    <input required type="text" name="pelabuhan_muat" class="form-control" value="{{$data->pelabuhan_muat}}">
                                </div>     
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Pelabuhan Bongkar<span class="text-red">*</span></label>
                                    <input required type="text" name="pelabuhan_bongkar" class="form-control" value="{{$data->pelabuhan_bongkar}}">
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
                                        <th width="350">No. Kontainer</th>
                                        <th width="280">Seal</th>
                                        <th width="150">Tipe</th>
                                        <th width="150">THC</th>
                                        <th width="350">Tujuan</th>
                                        <th width="200">Tgl Booking</th>
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
                                                <option value="">--Pilih Tipe--</option>
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
                                            <input type="text" name="tgl_brngkt_booking[]" autocomplete="off" class="date form-control tgl_booking"  placeholder="dd-M-yyyy" value="{{old('tgl_booking','')}}">     
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
                                        <th><span> <input type="checkbox" class="checkitem" name="thc_cekbox" id="thc_cekbox"></span> THC</th>
                                        <td name="">
                                            <input type="text" id="thc_null" class="form-control" value="0" readonly>
                                            <input type="text" name="total_thc" id="total_thc" class="form-control" readonly hidden>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" class="checkitem" name="lolo_cekbox" id="lolo_cekbox"></span> LOLO</th>
                                        <td name="">
                                            <input type="text" id="lolo_null" class="form-control" value="0" readonly>
                                            <input type="text" name="total_lolo" id="total_lolo" class="form-control" readonly hidden>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" class="checkitem" name="apbs_cekbox" id="apbs_cekbox"></span> APBS</th>
                                        <td name="">
                                            <input type="text" id="apbs_null" class="form-control" value="0" readonly>
                                            <input type="text" name="total_apbs" id="total_apbs" class="form-control" readonly hidden>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" class="checkitem" name="cleaning_cekbox" id="cleaning_cekbox"></span> CLEANING</th>
                                        <td name="">
                                            <input type="text" id="cleaning_null" class="form-control" value="0" readonly>
                                            <input type="text" name="total_cleaning" id="total_cleaning" class="form-control" readonly hidden>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" class="checkitem" name="doc_fee_cekbox" id="doc_fee_cekbox"></span> DOC FEE</th>
                                        <td name="">
                                            <input type="text" id="doc_fee_null" class="form-control" value="0" readonly>
                                            <input type="text" name="total_doc_fee" id="total_doc_fee" class="form-control" readonly hidden>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>SUB TOTAL</th>
                                        <td>
                                            <input type="text" id="total_sblm_dooring_null" class="form-control" value="0" readonly>
                                            <input type="text" name="total_sblm_dooring" id="total_sblm_dooring" class="form-control" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>

                            <table class="table table-bordered" id="sortable">
                                <thead>
                                    <tr>
                                        <th colspan="2">Biaya Jaminan</th>
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
                                                <input type="text" name="tgl_bayar_jaminan" autocomplete="off" class="date form-control" id="tgl_bayar_jaminan" placeholder="dd-M-yyyy" >     
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

        // master harga tipe
            var dataKeuangan = <?php echo json_encode($dataPengaturanKeuangan[0]); ?>;
            var harga20Ft = {
                'thcLuar': dataKeuangan.thc_20ft_luar,
                'thcDalam': dataKeuangan.thc_20ft_dalam,
                'loloLuar': dataKeuangan.lolo_20ft_luar,
                'loloDalam': dataKeuangan.lolo_20ft_dalam,
                'apbs': dataKeuangan.apbs_20ft,
                'cleaning': dataKeuangan.cleaning_20ft,
                'doc_fee': dataKeuangan.doc_fee_20ft,
            };
            var harga40Ft = {
                'thcLuar': dataKeuangan.thc_40ft_luar,
                'thcDalam': dataKeuangan.thc_40ft_dalam,
                'loloLuar': dataKeuangan.lolo_40ft_luar,
                'loloDalam': dataKeuangan.lolo_40ft_dalam,
                'apbs': dataKeuangan.apbs_40ft,
                'cleaning': dataKeuangan.cleaning_40ft,
                'doc_fee': dataKeuangan.doc_fee_40ft,
            };
        // end of master harga tipe
        // console.log('harga20Ft '+JSON.stringify(harga20Ft));
        // console.log('harga40Ft '+JSON.stringify(harga40Ft));

        // handling tanggal
            $('#tgl_sandar').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language:'en',
            });
            $('#tgl_bayar_jaminan').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
            });
            $(document).on('focus', '.tgl_booking', function() {
                $(this).datepicker({
                    autoclose: true,
                    format: "dd-M-yyyy",
                    todayHighlight: true,
                    language: 'en',
                });
            });
        // end of handling tgl

        $(document).on('change', '#customer', function(event) {
            // Get selected value from #customer
            var selectedValue = this.value;

            $.ajax({
                url: '/booking/getTujuan/' + selectedValue,
                method: 'GET',
                success: function(response) { 
                    // get semua data dropdown dengan class ini trus di kosongin
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
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan.'
                    })
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

            // logic kasih id di row
                // Ambil semua elemen <tr> dengan ID yang dimulai dengan "row"
                var rows = document.querySelectorAll('tr[id^="row"]');

                // Cari ID terbesar dengan format "rowX" dan ambil nilai X-nya
                var maxID = -1;
                for (var i = 0; i < rows.length; i++) {
                    var idStr = rows[i].id.replace('row', ''); // Ambil nilai X dari "rowX"
                    var idNum = parseInt(idStr); // Konversi menjadi angka
                    if (idNum > maxID) {
                        maxID = idNum;
                    }
                }   

                // Hasilkan ID terakhir dengan format "rowX+1"
                var lastID = (maxID + 1);

                if(lastID != 0){
                    var i = lastID-1;
                }else{
                    var i = 0;
                }
                var length;
            // end of logic

            // get tujuan
            $.ajax({
                url: '/booking/getTujuan/' + selectedValue,
                method: 'GET',
                success: function(response) {
                    response.forEach(tujuan => {
                        const option = document.createElement('option');
                        var xxx = `<option value="${tujuan.id}">${tujuan.nama_tujuan}</option>`;
                        // store data ke dataOption buat di fetch ketika tambah data
                        dataOption += xxx;
                    });
                    i++;

                    $('#tb').append(
                        `<tr id="row`+i+`">
                            <td>
                                <input type="text" id="no_kontainer" name="detail[${i}][no_kontainer]"class="form-control no_kontainerx" maxlength="20" value="">
                            </td>
                            <td>
                                <input type="text" id="seal" name="detail[${i}][seal]"class="form-control" maxlength="10" value="">
                            </td>
                            <td>
                                <select class="form-control selectpicker tipeKontainer" name="detail[${i}][tipe]" id="tipe${i}" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                    <option value="">--Pilih Tipe--</option>
                                    <option value="20">20Ft</option>
                                    <option value="40">40Ft</option>
                                </select>
                                <input type="hidden" readonly class="hargaThc" hargaThc_${i} name="detail[${i}][hargaThc]" value="">
                                <input type="hidden" readonly class="hargaLolo" hargaLolo_${i} name="detail[${i}][hargaLolo]" value="">
                                <input type="hidden" readonly class="hargaApbs" hargaApbs_${i} name="detail[${i}][hargaApbs]" value="">
                                <input type="hidden" readonly class="hargaCleaning" hargaCleaning_${i} name="detail[${i}][hargaCleaning]" value="">
                                <input type="hidden" readonly class="hargaDocFee" hargaDocFee_${i} name="detail[${i}][hargaDocFee]" value="">
                            </td>
                            <td>
                                <div class="form-group mb-0">
                                    <div class="icheck-primary">
                                        <input id="thcLuar${i}" dataId="${i}" class="thcc" type="radio" name="detail[${i}][thcLD]" value="luar" checked>
                                        <label class="form-check-label" for="thcLuar${i}">Luar</label>
                                    </div>
                                    <div class="icheck-primary mt-3">
                                        <input id="thcDalam${i}" dataId="${i}" class="thcc" type="radio" name="detail[${i}][thcLD]" value="dalam" >
                                        <label class="form-check-label" for="thcDalam${i}">Dalam</label><br>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select class="form-control selectpicker tujuanC" name="detail[${i}][tujuan]" id="tujuan" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                    <option value="">--Pilih Tujuan--</option>
                                    `+dataOption+`
                                </select>
                            </td>
                            <td>
                                <input type="text" name="detail[${i}][tgl_booking]" autocomplete="off" class="date form-control tgl_booking" placeholder="dd-M-yyyy" value="{{old('tgl_booking','')}}">     
                            </td>
                        
                            <td align="center" class="text-danger">
                                <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" class="btn btn-danger radiusSendiri hapus">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>`
                    );
                    uncheck();
                    $('.selectpicker').selectpicker('refresh');

                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        
            // $('#save').removeAttr('hidden',true);
        });

        $( document ).on( 'click', '.hapus', function (event) {
            $(this).closest('tr').remove();
                 
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

            // pop up confirmation
                // Swal.fire({
                //     title: 'Apakah Anda yakin?',
                //     text: "Data kan di hapus",
                //     icon: 'warning',
                //     showCancelButton: true,
                //     cancelButtonColor: '#d33',
                //     confirmButtonColor: '#3085d6',
                //     cancelButtonText: 'Batal',
                //     confirmButtonText: 'Ya',
                //     reverseButtons: true
                // }).then((result) => {
                //     if (result.isConfirmed) {
                //         $(this).closest('tr').remove();

                //         const Toast = Swal.mixin({
                //             toast: true,
                //             position: 'top-end',
                //             timer: 2500,
                //             showConfirmButton: false,
                //             timerProgressBar: true,
                //             didOpen: (toast) => {
                //                 toast.addEventListener('mouseenter', Swal.stopTimer)
                //                 toast.addEventListener('mouseleave', Swal.resumeTimer)
                //             }
                //         })

                //         Toast.fire({
                //             icon: 'success',
                //             title: 'Data dihapus'
                //         })
                //     }
                // })
            // pop up confirmation

        });

        // handling checkbox biaya dibawah
            var tmpTot = 0;
            $('#thc_cekbox').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#thc_null').prop('hidden', true);
                    $('#total_thc').prop('hidden', false);
                    hitungTotal();
                    // tmpTot = tmpTot+parseFloat($('#total_thc').val());
                    // $('#total_sblm_dooring').val(tmpTot);
                } else {
                    $('#thc_null').prop('hidden', false);
                    $('#total_thc').prop('hidden', true);
                    hitungTotal();
                    // tmpTot = tmpTot-parseFloat($('#total_thc').val());
                    // $('#total_sblm_dooring').val(tmpTot);
                }
            });
            $('#lolo_cekbox').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#lolo_null').prop('hidden', true);
                    $('#total_lolo').prop('hidden', false);
                    hitungTotal();
                } else {
                    $('#lolo_null').prop('hidden', false);
                    $('#total_lolo').prop('hidden', true);
                    hitungTotal();
                }
            });
            $('#apbs_cekbox').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#apbs_null').prop('hidden', true);
                    $('#total_apbs').prop('hidden', false);
                    hitungTotal();
                } else {
                    $('#apbs_null').prop('hidden', false);
                    $('#total_apbs').prop('hidden', true);
                    hitungTotal();
                }
            });
            $('#cleaning_cekbox').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#cleaning_null').prop('hidden', true);
                    $('#total_cleaning').prop('hidden', false);
                    hitungTotal();
                } else {
                    $('#cleaning_null').prop('hidden', false);
                    $('#total_cleaning').prop('hidden', true);
                    hitungTotal();
                }
            });
            $('#doc_fee_cekbox').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#doc_fee_null').prop('hidden', true);
                    $('#total_doc_fee').prop('hidden', false);
                    hitungTotal();
                } else {
                    $('#doc_fee_null').prop('hidden', false);
                    $('#total_doc_fee').prop('hidden', true);
                    hitungTotal();
                }
            });
        // end of it

        // logic hitung biaya
            $( document ).on( 'change', '.tipeKontainer', function (event) {
                // biar datanya ga ke get 2x ketika di get val
                event.stopPropagation();
                
                var selectedId = $(this).attr('id');
                var id = selectedId.replace('tipe', ''); // Remove 'thc' from the beginning

                var selectedValue = $(this).val();

                var parentTd = $(this).closest('td');
                if(selectedValue == '20'){
                    var thcldVal = $("input[name='detail[" + id + "][thcLD]']:checked").val();
                    parentTd.find('.hargaThc').val(thcldVal == 'luar' ? harga20Ft.thcLuar : harga20Ft.thcDalam);
                    parentTd.find('.hargaLolo').val(thcldVal == 'luar' ? harga20Ft.loloLuar : harga20Ft.loloDalam);
                }else{
                    var thcldVal = $("input[name='detail[" + id + "][thcLD]']:checked").val();
                    parentTd.find('.hargaThc').val(thcldVal == 'luar' ? harga40Ft.thcLuar : harga40Ft.thcDalam);
                    parentTd.find('.hargaLolo').val(thcldVal == 'luar' ? harga40Ft.loloLuar : harga40Ft.loloDalam);
                }

                parentTd.find('.hargaApbs').val(selectedValue == '20' ? harga20Ft.apbs : harga40Ft.apbs);
                parentTd.find('.hargaCleaning').val(selectedValue == '20' ? harga20Ft.cleaning : harga40Ft.cleaning);
                parentTd.find('.hargaDocFee').val(selectedValue == '20' ? harga20Ft.doc_fee : harga40Ft.doc_fee);
                
           
                uncheck();
                // tiap ada perubahan di class tipekontainer, di akhir akan di hitung total harganya
                calculateTotalHarga();
                hitungTotal();
            });

            $( document ).on( 'change', '.thcc', function (event) {
                // var selectedValue = $("input[name='detail[" + i + "][thcLD]']:checked").val();
                var selectedId = $(this).attr('id');
                // console.log('Selected ID:', selectedId);

                var selectedValue = $(this).val();
                // console.log('selectedValue '+selectedValue);

                var dataId = $(this).attr('dataId');
                // console.log("dataId: -"+dataId+"-");
                
                var tk = $(`#tipe${dataId}`).val();
                // console.log('tk '+tk);
                if(tk == '20'){
                    const thc_change = document.querySelector(`input[hargaThc_${dataId}]`);
                    var valueThc = selectedValue == 'luar' ? harga20Ft.thcLuar : harga20Ft.thcDalam;
                    thc_change.value = valueThc;

                    const lolo_change = document.querySelector(`input[hargaLolo_${dataId}]`);
                    var valueLolo = selectedValue == 'luar' ? harga20Ft.loloLuar : harga20Ft.loloDalam;
                    lolo_change.value = valueLolo;
                }else{
                    const thc_change = document.querySelector(`input[hargaThc_${dataId}]`);
                    var valueThc = selectedValue == 'luar' ? harga40Ft.thcLuar : harga40Ft.thcDalam;
                    thc_change.value = valueThc;

                    const lolo_change = document.querySelector(`input[hargaLolo_${dataId}]`);
                    var valueLolo = selectedValue == 'luar' ? harga40Ft.loloLuar : harga40Ft.loloDalam;
                    lolo_change.value = valueLolo;
                    // document.querySelector(`input[hargaThc_${dataId}]`).val(selectedValue == 'luar' ? harga40Ft.thcLuar : harga40Ft.thcDalam);
                    // document.querySelector(`input[hargaLolo_${dataId}]`).val(selectedValue == 'luar' ? harga40Ft.loloLuar : harga40Ft.loloDalam);
                }

                uncheck();
                calculateTotalHarga();
                hitungTotal();
            });

            
            $( document ).on( 'click', '.hapus', function (event) {
                // ketika hapus data, di hitung lagi total harganya
                uncheck();
                calculateTotalHarga();
                hitungTotal();
            });

            function hitungTotal(){
                var total_thc = parseFloat(($('#thc_cekbox').prop('checked')) ? $('#total_thc').val() : 0);
                var total_lolo = parseFloat(($('#lolo_cekbox').prop('checked')) ? $('#total_lolo').val() : 0);
                var total_apbs = parseFloat(($('#apbs_cekbox').prop('checked')) ? $('#total_apbs').val() : 0);
                var total_cleaning = parseFloat(($('#cleaning_cekbox').prop('checked')) ? $('#total_cleaning').val() : 0);
                var total_doc_fee = parseFloat(($('#doc_fee_cekbox').prop('checked')) ? $('#total_doc_fee').val() : 0);
                
                var total = parseFloat(total_thc + total_lolo + total_apbs + total_cleaning + total_doc_fee);

                var total_sblm_dooring = $('#total_sblm_dooring').val(total);
            }

            function uncheck(){
                // $('.checkitem').each(function() {
                //     this.checked = false; 
                // });  

                // $('#thc_null').prop('hidden', false);
                // $('#total_thc').prop('hidden', true);

                // $('#lolo_null').prop('hidden', false);
                // $('#total_lolo').prop('hidden', true);
   
                // $('#apbs_null').prop('hidden', false);
                // $('#total_apbs').prop('hidden', true);

                // $('#cleaning_null').prop('hidden', false);
                // $('#total_cleaning').prop('hidden', true);

                // $('#doc_fee_null').prop('hidden', false);
                // $('#total_doc_fee').prop('hidden', true);
            }

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


