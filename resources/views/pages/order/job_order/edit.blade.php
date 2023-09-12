
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
    .card-header:first-child{
        border-radius:inherit;
    }
    /* .tabelJO {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #ddd;
    } */
    
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
    <form action="{{ route('job_order.update', ['job_order' => $data['JO'] ]) }}" id='save' method="POST" >
        @method('PUT')
        @csrf
        <div class="row m-2">
            <div class="col-12 radiusSendiri sticky-top " style="margin-bottom: -15px;">
                <div class="card radiusSendiri" style="">
                    <div class="card-header ">
                        <a href="{{ route('job_order.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    </div>
                </div>
            </div>
             <div class="col-12">
                <div class="card radiusSendiri">
                    {{-- <div class="card-header">
                        <a href="{{ route('job_order.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" id='submitButton' class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    </div> --}}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6" >
                                <div class="form-group" style="pointer-events: none;" >
                                    <label for="">Pengirim<span class="text-red">*</span></label>
                                        <select class="form-control selectpicker" readonly style="pointer-events: none;" id='customer' name="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                        <option value="0">--Pilih Pengirim--</option>
                                        @foreach ($dataCustomer as $cust)
                                            <option value="{{$cust->id}}" kode="{{$cust->kode}}" <?= $data['JO']->id_customer == $cust->id ? 'selected':''; ?> >{{ $cust->nama }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id='kode_cust' name='kode_cust' >
                                </div>
                            </div>
                            <div class="col-6" >
                                <div class="form-group" style="pointer-events: none;" >
                                    <label for="">Pelayaran</label>
                                    <select class="form-control selectpicker" readonly disabled id='supplier' name="supplier" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                        <option value="0">--Pilih Pelayaran--</option>
                                        @foreach ($dataSupplier as $sup)
                                            <option value="{{$sup->id}}" <?= $data['JO']->id_supplier == $sup->id? 'selected':''; ?> >{{ $sup->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="">No. BL<span class="text-red">*</span></label>
                                    <input required type="text" name="no_bl" class="form-control" value="{{$data['JO']->no_bl}}" readonly disabled >
                                </div>           
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tgl_sandar">Tanggal Sandar</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" name="tgl_sandar" autocomplete="off" class="date form-control" id="tgl_sandar" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse($data['JO']->tgl_sandar)->format('d-M-Y') }}" readonly disabled>     
                                    </div>
                                </div>           
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Pelabuhan Muat<span class="text-red">*</span></label>
                                    <input required type="text" name="pelabuhan_muat" class="form-control" value="{{$data['JO']->pelabuhan_muat}}" readonly>
                                </div>     
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Pelabuhan Bongkar<span class="text-red">*</span></label>
                                    <input required type="text" name="pelabuhan_bongkar" class="form-control" value="{{$data['JO']->pelabuhan_bongkar}}" readonly>
                                </div>              
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <input required type="text" name="status" class="form-control" value="{{$data['JO']->status}}" readonly>
                                </div>              
                            </div>
                        </div>  
                        <div class="table_wrapper">
                            <table id="tabelJO" class="tabelJO table table-striped hover" >
                                <thead>
                                    <tr>
                                        <th width="">No. Kontainer</th>
                                        <th width="">Seal</th>
                                        <th width="">Tipe</th>
                                        <th width="">Stripping</th>
                                        <th width="">Pick Up</th>
                                        <th width="">Tujuan</th>
                                        <th width="200">Tgl Booking</th>
                                    </tr>
                                </thead>
                                <tbody id="tb"> 
                                    @if ($data['detail'])
                                        @foreach (json_decode($data['detail']) as $key => $item)
                                            <tr id="row{{$key}}" >
    
                                                <td>
                                                    <input type="hidden" id="no_kontainer" name="detail[{{$key}}][no_kontainer]"class="form-control no_kontainerx" maxlength="20" value="{{$item->no_kontainer}}" readonly>
                                                    <span>{{$item->no_kontainer}}</span>
                                                </td>
                                                <td>
                                                    {{-- <input type="text" id="seal" name="detail[{{$key}}][seal]"class="form-control" maxlength="10" value="{{$item->seal}}" readonly> --}}
                                                    <span>{{$item->seal}}</span>
                                                </td>
                                                <td>
                                                    <select class="form-control selectpicker tipeKontainer" name="detail[{{$key}}][tipe]" id="tipe{{$key}}" data-live-search="true" data-show-subtext="true" data-placement="bottom" readonly disabled>
                                                        <option value="">── Pilih Tipe ──</option>
                                                        <option value="20" <?= $item->tipe_kontainer == '20' ? 'selected':''; ?> >20Ft </option>
                                                        <option value="40" <?= $item->tipe_kontainer == '40' ? 'selected':''; ?> >40Ft </option>
                                                    </select>
                                                    <input type="hidden" readonly name="detail[{{$key}}][id_detail]" value="{{$item->id}}">
                                                    <input type="hidden" readonly name="detail[{{$key}}][id_booking]" value="{{$item->id_booking}}">
                                                    <input type="hidden" readonly class="hargaThc" <?= 'hargaThc_'.$key ?> name="detail[{{$key}}][hargaThc]" value="">
                                                    <input type="hidden" readonly class="hargaLolo" <?= 'hargaLolo_'.$key ?> name="detail[{{$key}}][hargaLolo]" value="">
                                                    <input type="hidden" readonly class="hargaApbs" <?= 'hargaApbs_'.$key ?> name="detail[{{$key}}][hargaApbs]" value="">
                                                    <input type="hidden" readonly class="hargaCleaning" <?= 'hargaCleaning_'.$key ?> name="detail[{{$key}}][hargaCleaning]" value="">
                                                    <input type="hidden" readonly class="hargaDocFee" <?= 'hargaDocFee_'.$key ?> name="detail[{{$key}}][hargaDocFee]" value="">
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <div class="icheck-primary">
                                                            <input id="thcLuar{{$key}}" dataId="{{$key}}" class="thcc" type="radio" name="detail[{{$key}}][stripping]" value="luar" <?=  $item->stripping== 'luar'? 'checked':''; ?> readonly disabled>
                                                            <label class="form-check-label" for="thcLuar{{$key}}"><span class="opacit">Luar</span></label>
                                                        </div>
                                                        <div class="icheck-primary mt-3">
                                                            <input id="thcDalam{{$key}}" dataId="{{$key}}" class="thcc" type="radio" name="detail[{{$key}}][stripping]" value="dalam" <?=  $item->stripping== 'dalam'? 'checked':''; ?> readonly disabled>
                                                            <label class="form-check-label" for="thcDalam{{$key}}"><span class="opacit">Dalam</span></label><br>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <select class="form-control selectpicker pick_up" name="detail[{{$key}}][pick_up]" id="tipe{{$key}}" data-live-search="true" data-show-subtext="true" data-placement="bottom" readonly >
                                                        <option value="">── Pick Up ──</option>
                                                        <option value="TTL" <?= $item->pick_up == 'TTL' ? 'selected':''; ?> >TTL</option>
                                                        <option value="TPS" <?= $item->pick_up == 'TPS' ? 'selected':''; ?> >TPS</option>
                                                        <option value="DEPO" <?= $item->pick_up == 'DEPO' ? 'selected':''; ?> >DEPO</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control selectpicker tujuanC" name="detail[{{$key}}][tujuan]" tujuan_check="{{$key}}"  id="tujuan{{$key}}" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                                        <option value="">── Pilih Tujuan ──</option>
                                                        @if ($dataTujuan)
                                                            @foreach ($dataTujuan as $tuj)
                                                                <option value="{{$tuj->id}}"  <?= $item->id_grup_tujuan == $tuj->id ? 'selected':''; ?> >{{ $tuj->nama_tujuan }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </td>
                                                <td >
                                                    <div class="input-group mb-0">
                                                        <div class="input-group-prepend ">
                                                            <span class="input-group-text d-sm-none d-md-none d-lg-block"><i class="far fa-calendar-alt"></i></span>
                                                        </div>
                                                        <input type="text" name="detail[{{$key}}][tgl_booking]" id='tgl_booking{{$key}}' tgl_booking_check="{{$key}}" autocomplete="off" class="date form-control tgl_booking" placeholder="dd-M-yyyy" value="{{isset($item->tgl_booking)? \Carbon\Carbon::parse($item->tgl_booking)->format('d-M-Y'):NULL}}">     
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> 
            </div>
            
            <div class="col-12">
                    <div class="card radiusSendiri">
                        <div class="card-header">
                            <h3 class="card-title mt-2"><b>KETERANGAN BIAYA</b></h3>
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
                            <table class="table table-bordered" >
                                <thead>
                                    <tr>
                                        <th colspan="2" class="card-outline card-primary">BIAYA SEBELUM DOORING</th>
                                    </tr>
                                </thead>
                                <tbody > 
                                    <tr>
                                        <th><span> <input type="checkbox" class="checkitem" name="thc_cekbox" id="thc_cekbox" <?= ($data['JO']['thc'] == 0) ? '':'checked'; ?> disabled></span> THC</th>
                                        <td name="">
                                            <input type="text" name="total_thc" id="total_thc" class="form-control" value="{{number_format($data['JO']['thc'])}}" readonly >
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" class="checkitem" name="lolo_cekbox" id="lolo_cekbox" <?= $data['JO']['lolo'] == 0 ? '':'checked'; ?> disabled></span> LOLO</th>
                                        <td name="">
                                            <input type="text" name="total_lolo" id="total_lolo" class="form-control" value="{{number_format($data['JO']['lolo'])}}" readonly >
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" class="checkitem" name="apbs_cekbox" id="apbs_cekbox" <?= ($data['JO']['apbs'] == 0) ? '':'checked'; ?> disabled></span> APBS</th>
                                        <td name="">
                                            <input type="text" name="total_apbs" id="total_apbs" class="form-control" value="{{number_format($data['JO']['apbs'])}}" readonly >
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" class="checkitem" name="cleaning_cekbox" id="cleaning_cekbox" <?=  ($data['JO']['cleaning'] == 0) ? '':'checked'; ?> disabled></span> CLEANING</th>
                                        <td name="">
                                            <input type="text" name="total_cleaning" id="total_cleaning" class="form-control" value="{{number_format($data['JO']['cleaning'])}}" readonly >
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><span> <input type="checkbox" class="checkitem" name="doc_fee_cekbox" id="doc_fee_cekbox" <?= ($data['JO']['doc_fee'] == 0) ? '':'checked'; ?> disabled></span> DOC FEE</th>
                                        <td name="">
                                            <input type="text" name="total_doc_fee" id="total_doc_fee" class="form-control" value="{{number_format($data['JO']['doc_fee'])}}" readonly >
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>SUB TOTAL</th>
                                        <td>
                                            <input type="text" name="total_sblm_dooring" id="total_sblm_dooring" class="form-control" value="<?= number_format($data['JO']['thc']+$data['JO']['lolo']+$data['JO']['apbs']+$data['JO']['cleaning']+$data['JO']['doc_fee'] ,2) ?>" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>

                            <table class="table table-bordered" >
                                <thead>
                                    <tr>
                                        <th colspan="2" class="card-outline card-primary">BIAYA JAMINAN</th>
                                    </tr>
                                </thead>
                                <tbody > 
                                    <tr>
                                        <th style="height: 5px;">Tgl Bayar Jaminan</th>
                                        <td>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" name="tgl_bayar_jaminan" autocomplete="off" class="date form-control" id="tgl_bayar_jaminan" placeholder="dd-M-yyyy" value="{{ ($data['jaminan'] != null)? \Carbon\Carbon::parse($data['jaminan']['tgl_bayar'])->format('d-M-Y'):null }}" disabled>     
                                                <input type="hidden" name="id_jaminan" value="<?= ($data['jaminan'] != null)? $data['jaminan']['id']:NULL; ?>"  >     
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="height: 5px;">Total Jaminan</th>
                                        <td>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><b>Rp.</b></span>
                                                </div>
                                                <input type="text" class="form-control uang numaja" id="total_jaminan" name="total_jaminan" value="{{ $data['jaminan'] != null ? number_format($data['jaminan']['nominal'],2):null }}" disabled>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Catatan</th>
                                        <td>
                                           <textarea name="catatan" class="form-control" id="catatan" cols="50" rows="10" disabled >{{ $data['jaminan'] != null ? $data['jaminan']['catatan']:null }}</textarea>
                                        </td>
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
    </form>
{{-- sweet save --}}
<script>
    $(document).ready(function() {
        $('#save').submit(function(event) {
            event.preventDefault();

            var cekBookingTujuan = 1;
            $("[tgl_booking_check]").each(function() {
                var i = $(this).attr("tgl_booking_check");
                var tujuanVal = $("#tujuan" + i).val();
                var tglBookingVal = $("#tgl_booking" + i).val();
                // console.log('tujuanVal '+i +' '+ tujuanVal.length);
                // console.log('tglBookingVal '+i +' '+ tglBookingVal);                

                if (tujuanVal != "" || tglBookingVal != "") {
                    // console.log('tglBookingVal '+tglBookingVal);
                    // console.log('tujuanVal '+tujuanVal);
                    if(tujuanVal == ""){
                        cekBookingTujuan = 0;
                    }
                }
            });
            if(cekBookingTujuan == 0){
                Swal.fire(
                    'Terjadi kesalahan!',
                    'Tujuan wajib diisi ketika ada tanggal booking!',
                    'warning'
                )
                event.preventDefault();
                return false;
            }
            // pop up confirmation
                Swal.fire({
                    title: 'Apakah Anda yakin data sudah benar?',
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
                        var cust = $('#customer').val();
                        var nobl = $('#no_bl').val();
                        var plmt = $('#pelabuhan_muat').val();
                        var plbn = $('#pelabuhan_bongkar').val();

                        if( cust == ''|| nobl == ''|| plmt == ''|| plbn == '' ){
                            Swal.fire(
                                'Data tidak lengkap!',
                                'Cek ulang data anda.',
                                'warning'
                            )
                            
                            event.preventDefault();
                            return false;
                        }else{
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                timer: 800,
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
                            }, 1000); // 2000 milliseconds = 2 seconds
                        }
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
                        // return;
                    }
                })
            // pop up confirmation
        });

        
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        // get kode customer
            var selectElement = document.getElementById('customer');
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var kodeValue = selectedOption.getAttribute('kode');
            $('#kode_cust').val(kodeValue.trim());
        //
        // logic save old
            // $( document ).on( 'click', '#submitButton', function (event) {
            //     event.preventDefault();
            //     // pop up confirmation
            //         Swal.fire({
            //             title: 'Apakah Anda yakin data sudah benar?',
            //             text: "Periksa kembali data anda",
            //             icon: 'warning',
            //             showCancelButton: true,
            //             cancelButtonColor: '#d33',
            //             confirmButtonColor: '#3085d6',
            //             cancelButtonText: 'Batal',
            //             confirmButtonText: 'Ya',
            //             reverseButtons: true
            //         }).then((result) => {
            //             if (result.isConfirmed) {
            //                 const Toast = Swal.mixin({
            //                     toast: true,
            //                     position: 'top-end',
            //                     timer: 2500,
            //                     showConfirmButton: false,
            //                     timerProgressBar: true,
            //                     didOpen: (toast) => {
            //                         toast.addEventListener('mouseenter', Swal.stopTimer)
            //                         toast.addEventListener('mouseleave', Swal.resumeTimer)
            //                     }
            //                 })

            //                 Toast.fire({
            //                     icon: 'success',
            //                     title: 'Data Disimpan'
            //                 })

            //                 // form.submit();
            //                 $("#send").submit();
            //             }else{
            //                 const Toast = Swal.mixin({
            //                     toast: true,
            //                     position: 'top-end',
            //                     timer: 2500,
            //                     showConfirmButton: false,
            //                     timerProgressBar: true,
            //                     didOpen: (toast) => {
            //                         toast.addEventListener('mouseenter', Swal.stopTimer)
            //                         toast.addEventListener('mouseleave', Swal.resumeTimer)
            //                     }
            //                 })

            //                 Toast.fire({
            //                     icon: 'warning',
            //                     title: 'Batal Disimpan'
            //                 })
            //                 event.preventDefault();
            //                 // return;
            //             }
            //         })
            //     // pop up confirmation
            // });
        // 

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

        // handling tanggal
            $('#tgl_sandar').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language:'en',
            });
            $('.tgl_booking').datepicker({
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

                    $('#tb').append(`
                        <tr id="row`+i+`">
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
                                        <input id="thcLuar${i}" dataId="${i}" class="thcc" type="radio" name="detail[${i}][stripping]" value="luar" checked>
                                        <label class="form-check-label" for="thcLuar${i}">Luar</label>
                                    </div>
                                    <div class="icheck-primary mt-3">
                                        <input id="thcDalam${i}" dataId="${i}" class="thcc" type="radio" name="detail[${i}][stripping]" value="dalam" >
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
                                <input type="text" name="detail[${i}][tgl_booking]"  autocomplete="off" class="date form-control tgl_booking" placeholder="dd-M-yyyy" value="{{old('tgl_booking','')}}">     
                            </td>
                        
                            <td align="center" class="text-danger">
                                <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" class="btn btn-danger radiusSendiri hapus">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `);
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
                    var strippingVal = $("input[name='detail[" + id + "][stripping]']:checked").val();
                    parentTd.find('.hargaThc').val(strippingVal == 'luar' ? harga20Ft.thcLuar : harga20Ft.thcDalam);
                    parentTd.find('.hargaLolo').val(strippingVal == 'luar' ? harga20Ft.loloLuar : harga20Ft.loloDalam);
                }else{
                    var strippingVal = $("input[name='detail[" + id + "][stripping]']:checked").val();
                    parentTd.find('.hargaThc').val(strippingVal == 'luar' ? harga40Ft.thcLuar : harga40Ft.thcDalam);
                    parentTd.find('.hargaLolo').val(strippingVal == 'luar' ? harga40Ft.loloLuar : harga40Ft.loloDalam);
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
                // var selectedValue = $("input[name='detail[" + i + "][stripping]']:checked").val();
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


