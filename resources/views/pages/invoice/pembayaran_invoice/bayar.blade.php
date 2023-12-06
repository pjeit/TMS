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
<section class="">
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
                                        <label for="">Billing To</label>
                                        <select name="billingToDisabled" class="select2" style="width: 100%" id="billingToDisabled" required disabled
                                            <option value="">── BILLING TO ──</option>
                                            @foreach ($dataCustomers as $cust)
                                                <option value="{{ $cust->id }}" kode="{{ $cust->kode }}" {{ $cust->id == $idCust? 'selected':'' }}> {{ $cust->kode }} - {{ $cust->nama }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="billingTo" value="{{ $idCust }}">
                                    </div>  
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="">Tanggal Pembayaran<span style="color:red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" autocomplete="off" name="tanggal_pembayaran" class="form-control date" id="tanggal_pembayaran" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="">No. Bukti Potong</label>
                                        <input type="text" name="no_bukti_potong" class="form-control" id="no_bukti_potong">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Catatan</label>
                                    <input type="text" name="catatan" class="form-control">
                                    <input type="hidden" id="firstId" value="{{ isset($data)? $data[0]['id']:NULL }}">
                                    <input type="hidden" class="form-control" id="di_potong_admin" placeholder="di_potong_admin"> 
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
                                            <option value="{{ $kas->id }}" {{ $kas->id == 1? 'selected':''}}>{{ $kas->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row" id="showTransfer">
                                <div class="form-group col-lg-4 col-md-5 col-sm-12">
                                    <label class="" for="flexCheckDefault">
                                        Biaya Admin
                                    </label>
                                    <input class="ml-3 form-check-input" type="checkbox" id="BiayaAdminCheck" value="ya">
                                </div>
                                <div class="form-group col-lg-8 col-md-7 col-sm-12">
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" id="biaya_admin" name="biaya_admin" class="form-control uang numaja" value="" readonly >
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
                                        <input type="text" id="total_pph23" name="total_pph23" class="form-control uang" readonly>                         
                                        <input type="hidden" id="total_dibayar" name="total_dibayar" class="form-control uang" readonly>                         
                                    </div>
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
                        <th>PPh 23</th>
                        <th>Diterima</th>
                        <th>Catatan</th>
                        <th style="width:30px"></th>
                    </tr>
                </thead>
                <tbody>
                @isset($data)   
                    @foreach ($data as $key => $item)
                        <tr id='{{ $key }}' id_sewa='{{ $item->id }}'>
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
                                <span id="text_pph23_{{ $item->id }}"></span>
                                <input type="hidden" class="total_pph23" id="total_pph23_{{ $item->id }}" name="detail[{{ $item->id }}][pph23]" value="{{ $item->pph23 }}">
                                <input type="hidden" class="total_dibayar" id="total_dibayar_{{ $item->id }}" name="detail[{{ $item->id }}][dibayar]" value="{{ $item->dibayar }}">
                            </td>
                            <td>
                                <span id="text_diterima_{{ $item->id }}"></span>
                                <input type="hidden" class="total_diterima" id="total_diterima_{{ $item->id }}" name="detail[{{ $item->id }}][diterima]" value="{{ $item->diterima }}">
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
                                        <select class="select2" style="width: 100%" id="modal_no_invoice" disabled>
                                            <option value="">── Pilih Invoice ──</option>
                                            @foreach ($dataInvoices as $inv)
                                                <option value="{{ $inv->id }}">{{ $inv->no_invoice }} ({{ date("d-M-Y", strtotime($inv->tgl_invoice)) }}) </option>
                                            @endforeach
                                        </select>
                                    </div>   
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Catatan</label>
                                        <textarea name="modal_catatan" class="form-control" id="modal_catatan" rows="4"></textarea>
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
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">PPh 23</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_pph23" placeholder="" >
                                            <input type="hidden" class="form-control numaja uang" id="modal_dibayar" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="tarif">Diterima<span class="text-red">*</span> </label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_diterima">
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
                console.log('total_dibayar', total_dibayar);
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
                    this.submit();
                }else{
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
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
            // $('#modal_no_bukti_potong').val( $('#no_bukti_potong_'+key).val() );
            $('#modal_total_invoice').val( moneyMask($('#total_tagihan_'+key).val()) );
            $('#modal_sisa_invoice').val( moneyMask($('#total_sisa_'+key).val()) );

            var checkReimburse = $('#no_invoice_'+key).val().substr(-2);
            if(checkReimburse == '/I'){
                $("#hide_bukti_potong").hide();
            }

            
            $('#modal_detail').modal('show');
        });

        $(document).on('click', '.save_detail', function(){ // save
            var key = $('#key').val(); // id key buat nge get data yg di hidden, key = id_sewa
            var firstId = $('#firstId').val();

            $('#catatan_'+key).val( escapeComma($('#modal_catatan').val()) );
            document.getElementById("catatan_"+key).textContent = $('#modal_catatan').val();
            
            // $('#no_bukti_potong_'+key).val( escapeComma($('#modal_no_bukti_potong').val()) );
            // document.getElementById("no_bukti_potong_"+key).textContent = $('#modal_no_bukti_potong').val();
            
            $('#total_diterima_'+key).val( escapeComma($('#modal_diterima').val()) );
            document.getElementById("text_diterima_"+key).textContent = $('#modal_diterima').val();

            $('#total_pph23_'+key).val( escapeComma($('#modal_pph23').val()) );
            document.getElementById("text_pph23_"+key).textContent = $('#modal_pph23').val();

            $('#total_dibayar_'+key).val( escapeComma($('#modal_dibayar').val()) );
            // document.getElementById("text_dibayar_"+key).textContent = $('#modal_dibayar').val();

            $('#catatan_'+key).val( escapeComma($('#modal_catatan').val()) );
            document.getElementById("text_catatan_"+key).textContent = $('#modal_catatan').val();

            hitungAll();
            $('#modal_detail').modal('hide'); // close modal
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
            $("#BiayaAdminCheck").change(function() {
                if(this.checked) {
                    $("#biaya_admin").removeAttr("readonly");
                } else {
                    $("#biaya_admin").val('');
                    $("#biaya_admin").attr("readonly", true);
                }
                hitungAll();
            });
        // 

        // hide_bukti_potong
        $(document).on('keyup', '#modal_pph23', function(){ // kalau berubah, hitung total 
            hitungPPh(); // execute fungsi hitung tiap perubahan value diskon, (tarif + addcost - diskon)
        });
        $(document).on('change', '#modal_pph23', function(){ // kalau berubah, hitung total 
            hitungPPh(); // execute fungsi hitung tiap perubahan value diskon, (tarif + addcost - diskon)
        });


        function hitungPPh(){
            var sisaInvoice = isNaN(normalize($('#modal_sisa_invoice').val())) ? 0 : normalize($('#modal_sisa_invoice').val());
            var pph = isNaN(normalize($('#modal_pph23').val())) ? 0 : normalize($('#modal_pph23').val());

            if(pph > sisaInvoice){
                pph = sisaInvoice;
                $('#modal_pph23').val(moneyMask(pph));
            }
            $('#modal_diterima').val(moneyMask(sisaInvoice-pph));
            dibayar();
        }

        $(document).on('keyup', '#modal_diterima', function(){ // kalau berubah, hitung total 
            var sisaInvoice = parseFloat(escapeComma($('#modal_sisa_invoice').val()));
            sisaInvoice = (sisaInvoice !== null && !isNaN(sisaInvoice) && sisaInvoice !== "") ? sisaInvoice : 0;
            var diterima = parseFloat(escapeComma($('#modal_diterima').val()));
            diterima = (diterima !== null && !isNaN(diterima) && diterima !== "") ? diterima : 0;
            var pph = parseFloat(escapeComma($('#modal_pph23').val()));
            pph = (pph !== null && !isNaN(pph) && pph !== "") ? pph : 0;
            if((diterima+pph) > sisaInvoice){
                $('#modal_diterima').val(moneyMask(sisaInvoice));
                $('#modal_pph23').val(0);
            }
            dibayar();
        });


        $(document).on('keyup', '#biaya_admin', function(){ // kalau berubah, hitung total 
            // hitungBiayaAdmin(); // execute fungsi hitung tiap perubahan value diskon, (tarif + addcost - diskon)
            hitungAll();
        });

        function hitungBiayaAdmin(){
            var biaya_admin = $('#biaya_admin').val();
            if(biaya_admin != 0 || biaya_admin != ''){
                console.log('biaya_admin', escapeComma(biaya_admin));
            }
        }

        function hitungAll(){
            var total_diterima = total_pph23 = 0;
            var diterima = document.getElementsByClassName("total_diterima");
            var pph23 = document.getElementsByClassName("total_pph23");
            var biaya_admin = escapeComma($("#biaya_admin").val());

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

            if(biaya_admin != 0 || biaya_admin != ''){
                total_diterima -= biaya_admin;
            }
            $('#total_diterima').val(moneyMask(total_diterima));
            $('#total_pph23').val(moneyMask(total_pph23));
            $('#total_dibayar').val(moneyMask(total_diterima+total_pph23));
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
            $("#hide_bukti_potong").show();
        }

        function clear(){
            $("#biaya_admin").val('');
            $("#no_cek").val('');
            $("#jenis_badmin").val('').trigger('change');
            $("#jenis_badmin").attr("disabled", "disabled");
            $("#biaya_admin").attr("readonly", "true");
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


