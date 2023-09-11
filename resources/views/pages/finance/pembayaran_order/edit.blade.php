
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
    <form action="{{ route('pembayaran_jo.update',[$pembayaran_jo->id]) }}" method="POST" id="form">
      @csrf
        @method('PUT')

        <div class="row m-2">
        
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('pembayaran_jo.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                    </div>
                    <div class="card-body" >
                        <div class="row">
                            <div class="col-6">
                                    <div class="row">
                                    <div class="col-6" > 
                                            <div class="form-group">
                                                <label for="">Pengirim<span class="text-red">*</span></label>
                                                    <select class="form-control selectpicker"  id='customer' name="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled>
                                                    {{-- <option value="0">PT. Pasifik Global Makmur</option> --}}
                                                    @foreach ($dataCustomer as $cust)
                                                        <option value="{{$cust->id}}" {{$cust->id == $pembayaran_jo->id_customer?'checked':''}}>{{ $cust->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group ">
                                                <label for="">Pelayaran</label>
                                                <select class="form-control selectpicker"  id='pembayaran' name="supplier" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled>
                                                    {{-- <option value="0">PT. TANTO INTI LINE</option> --}}
                                                    @foreach ($dataSupplier as $sup)
                                                        <option value="{{$sup->id}}" {{$sup->id == $pembayaran_jo->id_supplier?'checked':''}}>{{ $sup->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group ">
                                                <label for="">No. BL<span class="text-red">*</span></label>
                                                <input required type="text" name="nama_pic" class="form-control" value="{{$pembayaran_jo->no_bl}}" readonly>
                                            </div>  
                                    </div>
                                    <div class="col-6"> 
                                        <div class="form-group">
                                                <label for="tgl_sandar">Tanggal Sandar</label>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" name="tgl_sandar" autocomplete="off" class="date form-control" id="tgl_sandar" placeholder="dd-M-yyyy" value="{{\Carbon\Carbon::parse($pembayaran_jo->tgl_sandar)->format('d-M-Y')}}" disabled>     
                                            </div>
                                        </div>  
                                        <div class="form-group">
                                            <label for="">Pelabuhan Muat<span class="text-red">*</span></label>
                                            <input required type="text" name="pelabuhan_muat" class="form-control" value="{{$pembayaran_jo->pelabuhan_muat}}" readonly>
                                        </div> 
                                        <div class="form-group">
                                            <label for="">Pelabuhan Bongkar<span class="text-red">*</span></label>
                                            <input required type="text" name="pelauhan_bongkar" class="form-control" value="{{$pembayaran_jo->pelabuhan_bongkar}}" readonly>
                                        </div>  
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-primary">Total Biaya</span>
                                    {{-- <span class="badge bg-primary rounded-pill">3</span> --}}
                                </h4>
                                <ul class="list-group mb-3">
                                    <li class="list-group-item d-flex justify-content-between lh-sm">
                                        <div>
                                        <h6 class="my-0">Biaya Sebelum Dooring</h6>
                                        {{-- <small class="text-muted">total</small> --}}
                                        </div>
                                        <span class="text-muted">Rp. {{number_format($TotalBiayaRev,2)}}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between lh-sm">
                                        <div>
                                        <h6 class="my-0">Biaya Jaminan</h6>
                                        {{-- <small class="text-muted">total</small> --}}
                                        </div>
                                        <span class="text-muted">Rp. {{number_format($dataJaminan->nominal,2)}}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Total (IDR)</span>
                                        <input type="hidden" name="total_sblm_dooring" value="{{$TotalBiayaRev}}">
                                        <strong>Rp. {{number_format($TotalBiayaRev+$dataJaminan->nominal,2)}}</strong>
                                    </li>
                                </ul>
                                <div class="input-group">
                                    <select class="form-control selectpicker"  id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                        <option value="">--PILIH PEMBAYARAN--</option>
                                        @foreach ($dataKas as $data)
                                            <option value="{{$data->id}}">{{ $data->nama }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-success" id="bttonBayar"><i class="fa fa-credit-card" aria-hidden="true" ></i> Bayar</button>

                                    {{-- <a href="{{ route('pembayaran_jo.index') }}"class="btn btn-success"><i class="fa fa-credit-card" aria-hidden="true"></i> Bayar</a> --}}

                                </div>
                            </div>
                        </div>
                    
                    </div>
                </div> 
            </div>
            <div class="col-12">
                <div class="d-flex justify-content-between" style="gap: 10px;">
                            <table class="table" id="sortable" >
                                <thead>
                                    <tr>
                                        <th colspan="2">Biaya Sebelum Dooring</th>
                                    </tr>
                                </thead>
                                <tbody > 
                                    <tr>
                                        <th><span> <input disabled type="checkbox" name="thc_cekbox" id="thc_cekbox" {{$pembayaran_jo->thc!=0?'checked':''}}></span> THC</th>
                                        <td name="total_thc"><input type="text" id="total_thc" class="form-control" value="Rp. {{number_format($pembayaran_jo->thc,2)}}" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input disabled type="checkbox" name="lolo_cekbox" id="lolo_cekbox" {{$pembayaran_jo->lolo!=0?'checked':''}}></span> LOLO</th>
                                        <td name="total_lolo"><input type="text" id="total_lolo" class="form-control" value="Rp. {{number_format($pembayaran_jo->lolo,2)}}" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input disabled type="checkbox" name="apbs_cekbox" id="apbs_cekbox" {{$pembayaran_jo->apbs!=0?'checked':''}}></span> APBS</th>
                                        <td name="total_apbs"><input type="text" id="total_apbs" class="form-control" value="Rp. {{number_format($pembayaran_jo->apbs,2)}}" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input disabled type="checkbox" name="cleaning_cekbox" id="cleaning_cekbox" {{$pembayaran_jo->cleaning!=0?'checked':''}}></span> CLEANING</th>
                                        <td name="total_cleaning"><input type="text" id="total_cleaning" class="form-control" value="Rp. {{number_format($pembayaran_jo->cleaning,2)}}" readonly></td>
                                    </tr>
                                    <tr>
                                        <th><span> <input disabled type="checkbox" name="doc_fee_cekbox" id="doc_fee_cekbox" {{$pembayaran_jo->doc_fee!=0?'checked':''}}></span> DOC FEE</th>
                                        <td name="total_doc_fee"><input type="text" id="total_doc_fee" class="form-control" value="Rp. {{number_format($pembayaran_jo->doc_fee,2)}}" readonly></td>
                                    </tr>
                                    <tr>
                                        <th>SUB TOTAL</th>
                                        <th name="total_sblm_dooring" id="total_sblm_dooring" >Rp. {{number_format($TotalBiayaRev,2)}}</th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                            <table class="table" id="sortable">
                                <thead>
                                    <tr>
                                        <th colspan="2">Biaya Jaminan</th>
                                    </tr>
                                </thead>
                                <tbody > 
                                    <tr class="tinggi">
                                        <th>Tgl Bayar Jaminan</th>
                                        <td><input type="text" name="" class="form-control" value="{{\Carbon\Carbon::parse($dataJaminan[0]->tgl_bayar)->format('d-M-Y')}}" readonly></td>
                                    </tr>
                                    <tr>
                                        <th>Total Jaminan</th>
                                        <th>Rp. {{number_format($dataJaminan->nominal,2)}}</th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                            
                        </div>
            </div>
        
        </div>
 
    </form>
<script type="text/javascript">
    $(document).ready(function() {
         $('body').on('click','#bttonBayar', function (event) {
                event.preventDefault();
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
                            var pembayaran = $('#pembayaran').val();
                            if( pembayaran == '' ){
                                Swal.fire(
                                    'Pembayaran Belum Dipilih!',
                                    'Silahkan pilih pembayaran terlebih dahulu',
                                    'warning'
                                )
                                event.preventDefault();
                                return false;
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
                                    icon: 'Sukses',
                                    title: 'Data Pembayaran Berhasil Disimpan'
                                })
                                $("#form").submit();
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

        // var dataKeuangan = <?php echo json_encode($dataPengaturanKeuangan[0]); ?>;
        // var harga20Ft = {
        //     'thc': dataKeuangan.thc_20ft,
        //     'lolo': dataKeuangan.lolo_20ft,
        //     'apbs': dataKeuangan.apbs_20ft,
        //     'cleaning': dataKeuangan.cleaning_20ft,
        //     'doc_fee': dataKeuangan.doc_fee_20ft,
        // };
        // // console.log(harga20Ft.thc)
        // var harga40Ft = {
        //     'thc': dataKeuangan.thc_40ft,
        //     'lolo': dataKeuangan.lolo_40ft,
        //     'apbs': dataKeuangan.apbs_40ft,
        //     'cleaning': dataKeuangan.cleaning_40ft,
        //     'doc_fee': dataKeuangan.doc_fee_40ft,
        // };
        
        // $('input[type="text"]').on('input', function() {
        //     var inputValue = $(this).val();
        //     var uppercaseValue = inputValue.toUpperCase();
        //     $(this).val(uppercaseValue);
        // });

        // $('#tgl_sandar').datepicker({
        //         autoclose: true,
        //         format: "dd-M-yyyy",
        //         todayHighlight: true,
        //         language:'en',
        //         endDate: "0d"
        // });

        // $(document).on('focus', '.tgl_booking', function() {
        //     $(this).datepicker({
        //         autoclose: true,
        //         format: "dd-M-yyyy",
        //         todayHighlight: true,
        //         language: 'en',
        //         endDate: "0d"
        //     });
        // });

        // $("#addmore").on("click",function(event){
        //     var customerId = $("#customer").val();
        //     if(customerId == 0 || customerId == null || customerId == ''){
        //         Swal.fire(
        //             '',
        //             'Harap isi data pengirim dahulu.',
        //             'error'
        //         );
        //         return false;
        //     }

        //     var selectedValue = customerId;
        //     let dataOption = ''; // Initialize as an array

        //     $.ajax({
        //         url: '/booking/getTujuan/' + selectedValue,
        //         method: 'GET',
        //         success: function(response) {
        //             response.forEach(tujuan => {
        //                 const option = document.createElement('option');
        //                 var xxx = `<option id="${tujuan.id}">${tujuan.nama_tujuan}</option>`;
        //                 dataOption += xxx;
        //                 // console.log('xxx '+xxx);
        //             });
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error:', error);
        //         }
        //     });
        //     console.log('dataOption '+dataOption);


        //     $('#tb').append(
        //         `<tr>
        //             <td>
        //                 <input type="text" id="no_kontainer" name="no_kontainer[]"class="form-control no_kontainerx" value="">
        //             </td>
        //             <td>
        //                 <input type="text" id="seal" name="seal[]"class="form-control" value="">
        //             </td>
        //             <td>
        //                 <select class="form-control selectpicker tipeKontainer" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
        //                     <option value="">--Pilih Tipe Kontainer--</option>
        //                     <option value="20">20Ft</option>
        //                     <option value="40">40Ft</option>
        //                 </select>
        //                 <input type="text" readonly class="hargaThc" name="hargaThc[]" value="">
        //                 <input type="text" readonly class="hargaLolo" name="hargaLolo[]" value="">
        //                 <input type="text" readonly class="hargaApbs" name="hargaApbs[]" value="">
        //                 <input type="text" readonly class="hargaCleaning" name="hargaCleaning[]" value="">
        //                 <input type="text" readonly class="hargaDocFee" name="hargaDocFee[]" value="">
        //             </td>
        //             <td>
        //                 <select class="form-control selectpicker" name="tujuan[]" id="tujuan" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
        //                     <option value="">--Pilih Tujuan--</option>
        //                 </select>
        //             </td>
        //             <td>
        //                 <input type="text" name="tgl_booking[]" autocomplete="off" class="date form-control tgl_booking" placeholder="dd-M-yyyy" value="{{old('tgl_booking','')}}">     
        //             </td>
        //             <td align="center" class="text-danger">
        //                 <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" onclick="if(confirm('Anda yakin ingin Menghapus data kontainer ini?')){ $(this).closest('tr').remove(); }" class="btn btn-danger radiusSendiri hapus">
        //                     <i class="fa fa-fw fa-trash-alt"></i>
        //                 </button>
        //             </td>
        //         </tr>`
        //     );
        //     $('.selectpicker').selectpicker('refresh');
        //     // $('#save').removeAttr('hidden',true);
        // });

        // // logic hitung biaya
        //     $( document ).on( 'change', '.tipeKontainer', function (event) {
        //         // ini buat biar klik sesuatu di anaknya, tdnya ga keeksekusi
        //         event.stopPropagation();
        //         var selectedValue = $(event.target).val();
        //         var selectedValue = $(this).val();

        //         //closest itu misal 
        //         // <td dia nyarik ini closestnya kan parent>
        //         //     <select class="form-control selectpicker tipeKontainer" name="tipe[]" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
        //         //         <option value="">--Pilih Tipe Kontainer--</option>
        //         //         <option value="20">20Ft</option>
        //         //         <option value="40">40Ft</option>
        //         //     </select> trs nyarik anak" nya
        //         //     <input type="text" readonly class="hargaThc" name="hargaThc[]" value="">
        //         //     <input type="text" readonly class="hargaLolo" name="hargaLolo[]" value="">
        //         //     <input type="text" readonly class="hargaApbs" name="hargaApbs[]" value="">
        //         //     <input type="text" readonly class="hargaCleaning" name="hargaCleaning[]" value="">
        //         //     <input type="text" readonly class="hargaDocFee" name="hargaDocFee[]" value="">
        //         // </td>

        //         var parentTd = $(this).closest('td');
        //         parentTd.find('.hargaThc').val(selectedValue == '20' ? harga20Ft.thc : harga40Ft.thc);
        //         parentTd.find('.hargaLolo').val(selectedValue == '20' ? harga20Ft.lolo : harga40Ft.lolo);
        //         parentTd.find('.hargaApbs').val(selectedValue == '20' ? harga20Ft.apbs : harga40Ft.apbs);
        //         parentTd.find('.hargaCleaning').val(selectedValue == '20' ? harga20Ft.cleaning : harga40Ft.cleaning);
        //         parentTd.find('.hargaDocFee').val(selectedValue == '20' ? harga20Ft.doc_fee : harga40Ft.doc_fee);

        //         calculateTotalHarga();
        //     });
            
        //     $( document ).on( 'click', '.hapus', function (event) {
        //         calculateTotalHarga();
        //     });

        //     function calculateTotalHarga() {
        //         var totalhargaThc = 0;
        //         var totalhargaLolo = 0;
        //         var totalhargaApbs = 0;
        //         var totalhargaCleaning = 0;
        //         var totalhargaDocFee = 0;

        //         $('#total_thc').val(totalhargaThc);
        //         $('#total_lolo').val(totalhargaLolo);
        //         $('#total_apbs').val(totalhargaApbs);
        //         $('#total_cleaning').val(totalhargaCleaning);
        //         $('#total_doc_fee').val(totalhargaDocFee);
                
        //         $('.hargaThc').each(function() {
        //             var value = parseFloat($(this).val()) || 0;
        //             totalhargaThc += value;
        //             $('#total_thc').val(totalhargaThc);
        //         });
        //         $('.hargaLolo').each(function() {
        //             var value = parseFloat($(this).val()) || 0;
        //             totalhargaLolo += value;
        //             $('#total_lolo').val(totalhargaLolo);
        //         });
        //         $('.hargaApbs').each(function() {
        //             var value = parseFloat($(this).val()) || 0;
        //             totalhargaApbs += value;
        //             $('#total_apbs').val(totalhargaApbs);
        //         });
        //         $('.hargaCleaning').each(function() {
        //             var value = parseFloat($(this).val()) || 0;
        //             totalhargaCleaning += value;
        //             $('#total_cleaning').val(totalhargaCleaning);
        //         });
        //         $('.hargaDocFee').each(function() {
        //             var value = parseFloat($(this).val()) || 0;
        //             totalhargaDocFee += value;
        //             $('#total_doc_fee').val(totalhargaDocFee);
        //         });
                
        //     }
        // end of logic hitung biaya

    });
</script>

@endsection


