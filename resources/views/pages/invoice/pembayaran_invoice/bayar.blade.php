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
<section class="m-2">
    <form action="{{ route('pembayaran_invoice.store') }}" id="save" method="POST" >
        @csrf
        {{-- sticky header --}}
        <div class="col-12 radiusSendiri sticky-top " style="margin-bottom: -15px;">
            <div class="card radiusSendiri" style="">
                <div class="card-header radiusSendiri">
                    <a href="{{ route('pembayaran_invoice.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    {{-- <button type="button" name="add" id="add" class="btn btn-primary radiusSendiri float-right"><i class="fa fa-plus-circle"></i> <strong >Tambah Tujuan</strong></button>  --}}
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-body" >
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-12">
                                        <div class="form-group">
                                        <label for="tanggal_pembayaran">Tanggal Pembayaran<span style="color:red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" autocomplete="off" name="tanggal_pembayaran" class="form-control date" id="tanggal_pembayaran" placeholder="dd-M-yyyy" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Billing To</label>
                                        <select name="billingTo" class="select2" style="width: 100%" id="billingTo" required>
                                            <option value="">── BILLING TO ──</option>
                                            @foreach ($dataCustomers as $cust)
                                                <option value="{{ $cust->id }}" kode="{{ $cust->kode }}" {{ $cust->id == $idCust? 'selected':'' }}> {{ $cust->kode }} - {{ $cust->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>  
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Diterima</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total PPh 23</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_pph23" name="total_pph23" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Total Bayar</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_dibayar" name="total_dibayar" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="tipe">Cara Pembayaran</label>
                                        <br>
                                        <div class="icheck-primary d-inline">
                                            <input id="transfer" type="radio" name="cara_pembayaran" value="transfer" checked>
                                            <label class="form-check-label" for="transfer">Transfer</label>
                                        </div>
                                        <div class="icheck-primary d-inline ml-4">
                                            <input id="tunai" type="radio" name="cara_pembayaran" value="tunai">
                                            <label class="form-check-label" for="tunai">Tunai</label>
                                        </div>
                                        <div class="icheck-primary d-inline ml-4">
                                            <input id="cek" type="radio" name="cara_pembayaran" value="cek">
                                            <label class="form-check-label" for="cek">Cek</label><br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Pilih Kas<span class="text-red">*</span> </label>
                                    <select name="kas" class="select2" style="width: 100%" id="kas" required>
                                        <option value="">── PILIH KAS ──</option>
                                        @foreach ($dataKas as $kas)
                                            <option value="{{ $kas->id }}">{{ $kas->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row" id="showTransfer">
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="BiayaAdminCheck" value="ya">
                                        <label class="form-check-label" for=""><b>Biaya Admin</b></label>
                                    </div>
                                </div>
                                <div class="form-group col-lg-8 col-md-8 col-sm-12">
                                    <select name="jenis_badmin" class="select2" style="width: 100%" id="jenis_badmin" disabled>
                                        <option value="">Pilih Metode</option>
                                        <option value="kliring">Kliring</option>
                                        <option value="RTGS">RTGS (Real Time Gross Settlement)</option>
                                        <option value="RTO">RTO (Real Time Online)</option>
                                        <option value="BIfast">BI Fast</option>
                                    </select>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="biaya_admin" name="biaya_admin" class="form-control uang numajaMinDesimal" value="" disabled>                         
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="showTunai">
                               
                            </div>
                            <div class="row" id="showCek">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">No Cek</label>
                                    <input type="text" name="no_cek" id='no_cek' class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Catatan</label>
                                    <input type="text" name="catatan" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>

        <div style="overflow: auto;">
            <table class="table table-hover table-bordered table-striped " width='100%' id="table_invoice">
                <thead>
                    <tr class="bg-white">
                        <th>No Invoice</th>
                        <th>Total Tagihan</th>
                        <th>Sisa Invoice</th>
                        <th>Diterima</th>
                        <th>PPh 23</th>
                        <th>Dibayar</th>
                        <th>Catatan</th>
                        <th style="width:30px"></th>
                    </tr>
                </thead>
                <tbody>
                @isset($data)   
                    @foreach ($data as $key => $item)
                        <tr id='{{ $item->id }}'>
                            <td> 
                                <span id="text_no_invoice">{{ $item->no_invoice }}</span>
                                <input type="hidden" id="no_invoice_{{ $item->id }}" name="detail[{{ $item->id }}][no_invoice]" value="{{ $item->no_invoice }}">
                                <input type="hidden" id="no_bukti_potong_{{ $item->id }}" name="detail[{{ $item->id }}][no_bukti_potong]" value="{{ $item->no_bukti_potong }}">
                            </td>
                            <td> 
                                <span id="text_total_tagihan_{{ $item->id }}">{{ number_format($item->total_tagihan) }}</span>
                                <input type="hidden" class="total_tagihan" id="total_tagihan_{{ $item->id }}" name="detail[{{ $item->id }}][total_tagihan]" value="{{ $item->total_tagihan }}">
                            </td>
                            <td> 
                                <span id="text_total_sisa_{{ $item->id }}">{{ number_format($item->total_sisa) }}</span>
                                <input type="hidden" class="total_sisa" id="total_sisa_{{ $item->id }}" name="detail[{{ $item->id }}][total_sisa]" value="{{ $item->total_sisa }}">
                            </td>
                            <td>
                                <span id="text_diterima_{{ $item->id }}"></span>
                                <input type="hidden" class="total_diterima" id="total_diterima_{{ $item->id }}" name="detail[{{ $item->id }}][diterima]" value="{{ $item->diterima }}">
                            </td>
                            <td>
                                <span id="text_pph23_{{ $item->id }}"></span>
                                <input type="hidden" class="total_pph23" id="total_pph23_{{ $item->id }}" name="detail[{{ $item->id }}][pph23]" value="{{ $item->pph23 }}">
                            </td>
                            <td>
                                <span id="text_dibayar_{{ $item->id }}"></span>
                                <input type="hidden" class="total_dibayar" id="total_dibayar_{{ $item->id }}" name="detail[{{ $item->id }}][dibayar]" value="{{ $item->dibayar }}">
                            </td>
                            <td>
                                <span id="text_catatan_{{ $item->id }}">{{ $item->catatan }}</span>
                                <input type="hidden" id="catatan_{{ $item->id }}" name="detail[{{ $item->id }}][catatan]" value="{{ $item->catatan }}">
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="button" name="detail" id="detail_{{$item->id}}" class="detail dropdown-item"> 
                                            <span class="fas fa-edit mr-3"></span> Edit
                                        </button>
                                        <a href="{{ route('pembayaran_invoice.destroy', ['pembayaran_invoice' => $item->id]) }}" class="dropdown-item" data-confirm-delete="true">
                                            <span class="fas fa-trash mr-3"></span> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endisset
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="modal_detail" tabindex='-1'>
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Detail Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id='form_add_detail'>
                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}

                        <div class='row'>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="sewa">No. Invoice <span style="color:red;">*</span></label>
                                        <select class="select2" style="width: 100%" id="modal_no_invoice">
                                            <option value="">── Pilih Invoice ──</option>
                                            @foreach ($dataInvoices as $inv)
                                                <option value="{{ $inv->id }}">{{ $inv->no_invoice }} ({{ date("d-M-Y", strtotime($inv->tgl_invoice)) }}) - {{ number_format($inv->total_tagihan) }} </option>
                                            @endforeach
                                        </select>
                                    </div>   

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">No. Bukti Potong</label>
                                        <input  type="text" class="form-control" id="modal_no_bukti_potong" > 
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Catatan</label>
                                        <input  type="text" class="form-control" id="modal_catatan" > 
                                    </div>
                                </div>
                                
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="tarif">Total Tagihan</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_total_invoice" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Sisa Invoice</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_sisa_invoice" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="tarif">Diterima</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_diterima" placeholder="" >
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">PPh 23</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_pph23" placeholder="" >
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Dibayar</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_dibayar" placeholder="" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                    <button type="button" class="btn btn-sm btn-success save_detail" id="" style='width:85px'>OK</button> 
                </div>
            </div>
            <!-- /.modal-content -->
            </div>
        </div>
    </form>
</section>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('#save').submit(function(event) {
            // cek total_dibayar
                var total_dibayar = $('#total_dibayar').val();
                if(escapeComma(total_dibayar) == 0 || escapeComma(total_dibayar) == ''){
                    Swal.fire(
                        'Data tidak valid',
                        'Total bayar masih 0, harap periksa kembali data anda!',
                        'warning'
                    )
                    return false;
                }
            //

            event.preventDefault(); // Prevent form submission
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
                        position: 'center',
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

<script type="text/javascript">
    $(document).ready(function() {
        // set value default tgl invoice
        var today = new Date();
        $('#tanggal_pembayaran').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            // startDate: today,
        }).datepicker("setDate", today);

        $("#jenis_badmin").select2({
            placeholder: "Pilih Metode",
            initSelection: function(element, callback) {                   
            }
        });

        hitungAll();

        $(document).on('click', '.detail', function(){ // open detail 
            clearModal(); // execute clear data dulu tiap open modal
            $('#key').val(''); // key di clear dulu
            var button_id = $(this).attr("id"); // get value id
            var key = button_id.replace("detail_", ""); // hapus teks detail_
            $('#key').val(key); // id key buat nge get data yg di hidden, key = id_sewa
            
            // set default selected select2
            var $selectElement = $("#modal_no_invoice");
            $selectElement.val(key).trigger("change.select2");

            $('#modal_diterima').val( moneyMask($('#total_diterima_'+key).val()) );
            $('#modal_pph23').val( moneyMask($('#total_pph23_'+key).val()) );
            $('#modal_dibayar').val( moneyMask($('#total_dibayar_'+key).val()) );
            $('#modal_catatan').val( $('#catatan_'+key).val() );
            $('#modal_no_bukti_potong').val( $('#no_bukti_potong_'+key).val() );
            $('#modal_total_invoice').val( moneyMask($('#total_tagihan_'+key).val()) );
            $('#modal_sisa_invoice').val( moneyMask($('#total_sisa_'+key).val()) );

            $(document).on('keyup', '#modal_pph23', function(){ // kalau berubah, hitung total 
                hitungPPh(); // execute fungsi hitung tiap perubahan value diskon, (tarif + addcost - diskon)
            });

            $(document).on('keyup', '#modal_diterima', function(){ // kalau berubah, hitung total 
                var sisaInvoice = parseFloat(escapeComma($('#modal_sisa_invoice').val()));
                sisaInvoice = (sisaInvoice !== null && !isNaN(sisaInvoice) && sisaInvoice !== "") ? sisaInvoice : 0;
                var diterima = parseFloat(escapeComma($('#modal_diterima').val()));
                diterima = (diterima !== null && !isNaN(diterima) && diterima !== "") ? diterima : 0;
                if(diterima > sisaInvoice){
                    $('#modal_diterima').val(moneyMask(sisaInvoice));
                    $('#modal_pph23').val(0);
                }
                dibayar();
            });

            $('#modal_detail').modal('show');
        });

        $(document).on('click', '.save_detail', function(){ // save
            var key = $('#key').val(); // id key buat nge get data yg di hidden, key = id_sewa

            $('#catatan_'+key).val( escapeComma($('#modal_catatan').val()) );
            document.getElementById("catatan_"+key).textContent = $('#modal_catatan').val();
            
            $('#no_bukti_potong_'+key).val( escapeComma($('#modal_no_bukti_potong').val()) );
            document.getElementById("no_bukti_potong_"+key).textContent = $('#modal_no_bukti_potong').val();
            
            $('#total_diterima_'+key).val( escapeComma($('#modal_diterima').val()) );
            document.getElementById("text_diterima_"+key).textContent = $('#modal_diterima').val();

            $('#total_pph23_'+key).val( escapeComma($('#modal_pph23').val()) );
            document.getElementById("text_pph23_"+key).textContent = $('#modal_pph23').val();

            $('#total_dibayar_'+key).val( escapeComma($('#modal_dibayar').val()) );
            document.getElementById("text_dibayar_"+key).textContent = $('#modal_dibayar').val();

            $('#catatan_'+key).val( escapeComma($('#modal_catatan').val()) );
            document.getElementById("text_catatan_"+key).textContent = $('#modal_catatan').val();

            hitungAll();
            $('#modal_detail').modal('hide'); // close modal
        });


        $(document.body).on("change","#jenis_badmin",function(){
            if(this.value == 'kliring'){
                $('#biaya_admin').val('5,000');
            }else if(this.value == 'RTGS'){
                $('#biaya_admin').val('25,000');
            }else if(this.value == 'RTO'){
                $('#biaya_admin').val('6,500');
            }else if(this.value == 'BIfast'){
                $('#biaya_admin').val('2,500');
            }
            uang();
        });

        // cara_pembayaran
            $("#showTransfer, #showCek, #showTunai").hide();
            $("input[name='cara_pembayaran']").change(function() {
                var selectedValue = $(this).val();
                // Hide all content divs
                $("#showTransfer, #showCek, #showTunai").hide().find(':input').attr('required', false);

                // Show the relevant content div based on the selected value
                clear();
                if (selectedValue === "transfer") {
                    $("#showTransfer").show();
                } else if (selectedValue === "tunai") {
                    $("#showTunai").show();
                } else if (selectedValue === "cek") {
                    $("#showCek").show().find(':input').attr('required', true);
                }
            });

            // Trigger the change event initially to show the correct div
            $("input[name='cara_pembayaran']:checked").change();
        //

        // toogle check biaya admin
            var biayaAdminCheckbox = $("#BiayaAdminCheck");
            var jenisBadminSelect = $("#jenis_badmin");
            var biayaAdmin = $("#biaya_admin");

            biayaAdminCheckbox.change(function() {
                if (biayaAdminCheckbox.is(":checked")) {
                    // If BiayaAdminCheck is checked, remove the 'disabled' attribute
                    jenisBadminSelect.removeAttr("disabled");
                    biayaAdmin.removeAttr("disabled");
                } else {
                    // If BiayaAdminCheck is unchecked, add the 'disabled' attribute
                    jenisBadminSelect.attr("disabled", "disabled");
                    $("#jenis_badmin").val('').trigger('change')
                    biayaAdmin.val('');
                    biayaAdmin.attr("disabled", "disabled");

                }
            });

            // Trigger the change event initially to set the initial state
            biayaAdminCheckbox.change();
        // 

        function hitungAll(){
            var total_diterima = total_pph23 = 0;
            var diterima = document.getElementsByClassName("total_diterima");
            var pph23 = document.getElementsByClassName("total_pph23");

            for (var i = 0; i < diterima.length; i++) {
                var value = parseFloat(diterima[i].value); 
                if (!isNaN(value)) {
                    total_diterima += value;
                }
            }
            for (var i = 0; i < pph23.length; i++) {
                var value = parseFloat(pph23[i].value); 
                if (!isNaN(value)) {
                    total_pph23 += value;
                }
            }

            $('#total_diterima').val(moneyMask(total_diterima));
            $('#total_pph23').val(moneyMask(total_pph23));
            $('#total_dibayar').val(moneyMask(total_diterima+total_pph23));
        }
        
        function hitungPPh(){
            var sisaInvoice = parseFloat(escapeComma($('#modal_sisa_invoice').val()));
            sisaInvoice = (sisaInvoice !== null && !isNaN(sisaInvoice) && sisaInvoice !== "") ? sisaInvoice : 0;
            var diterima = parseFloat(escapeComma($('#modal_diterima').val()));
            diterima = (diterima !== null && !isNaN(diterima) && diterima !== "") ? diterima : 0;

            var pph = parseFloat(escapeComma($('#modal_pph23').val()));
            pph = (pph !== null && !isNaN(pph) && pph !== "") ? pph : 0;
            
            diterima = (sisaInvoice - pph);
            if(pph > sisaInvoice){
                pph = sisaInvoice;
                $('#modal_pph23').val(moneyMask(pph));
            }
            $('#modal_diterima').val(moneyMask(diterima));
            dibayar();
        }

        function dibayar(){
            var pph = parseFloat(escapeComma($('#modal_pph23').val()));
            pph = (pph !== null && !isNaN(pph) && pph !== "") ? pph : 0;
            var diterima = parseFloat(escapeComma($('#modal_diterima').val()));
            diterima = (diterima !== null && !isNaN(diterima) && diterima !== "") ? diterima : 0;

            var dibayar = pph+diterima;
            $('#modal_dibayar').val(moneyMask(dibayar));
        }

        function clearModal(){
            $('#modal_catatan').val('');
            $('#modal_total_invoice').val('');
            $('#modal_sisa_invoice').val('');
            $('#modal_pph23').val('');
            $('#modal_diterima').val('');
        }

        function clear(){
            $("#biaya_admin").val('');
            $("#no_cek").val('');
            $("#jenis_badmin").val('').trigger('change');
            $("#jenis_badmin").attr("disabled", "disabled");
            $("#biaya_admin").attr("disabled", "disabled");
            $("#BiayaAdminCheck").prop("checked", false);
            $('#modal_dibayar').val('');
        }

        function uang(){
            $(document).on("keypress", ".numajaMinDesimal", function (e) {
                if (e.keyCode == 9) {
                    $(this).select();
                }
                if (
                    (e.charCode >= 48 && e.charCode <= 57) ||
                    e.charCode == 0 ||
                    e.charCode == 46 ||
                    e.charCode == 45
                )
                    return true;
                else return false;
            });

            $(document).on("blur", ".numajaMinDesimal", function (e) {
                if ($(this).val() != "") {
                    var value = removePeriod($(this).val(), ",");
                    var hasil = parseFloat(value).toFixed(2);
                    $(this).val(addPeriodDesimal(hasil, ","));
                }
                if (e.keyCode == 9) {
                    $(this).select();
                }
            });
        }
    });
</script>

@endsection


