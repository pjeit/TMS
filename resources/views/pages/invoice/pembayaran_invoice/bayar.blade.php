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
                                            {{-- @foreach ($dataCust as $cust)
                                                <option value="{{ $cust->id }}" kode="{{ $cust->kode }}" {{ $cust->id == $customer? 'selected':'' }}> {{ $cust->kode }} - {{ $cust->nama }}</option>
                                            @endforeach --}}
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
                                        <input type="text" maxlength="100" id="total_tagihan" name="total_tagihan" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Pph 23</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_dibayar" name="total_dibayar" class="form-control uang numajaMinDesimal" value="" readonly>                         
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
                                    <label for="">Pilih Kas</label>
                                    <select name="kas" class="select2" style="width: 100%" id="kas" required>
                                        <option value="">── PILIH KAS ──</option>
                                        <option value="1">BESAR</option>
                                        <option value="2">KECIL</option>
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
                                    <select name="jenis_badmin" class="select2" style="width: 100%" id="jenis_badmin" required disabled>
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
                                    <input type="text" name="no_cek" id='no_cek' class="form-control">
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
                        <th>Total Invoice</th>
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
                        <tr id="0">
                            <td> 
                                {{ $item->getCustomer->nama }} 
                                <input type="hidden" name="detail[{{ $item->id_sewa }}][id_customer]" value="{{ ($item->id_customer) }}" />
                            </td>
                            <td> {{ $item->nama_tujuan }} </td>
                            <td> {{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }} <br> {{ $item->no_polisi }} ({{ $item->getKaryawan->nama_panggilan }}) </td>
                            <td> <span id="no_kontainer_text_{{ $item->id_sewa }}">{{ isset($item->id_jo_detail)? $item->getJOD->no_kontainer:'(OUTBOUND)' }}</span> <br> <span id='no_surat_jalan_text_{{ $item->id_sewa }}'>{{ $item->no_surat_jalan }}</span> </td>
                            <td>-</td>
                            <td style="text-align:right" id="tarif_{{ $key }}">{{ number_format($item->total_tarif) }}</td>
                            <td style="text-align:right">
                                @php
                                    $total_addcost = 0;
                                @endphp
                                @foreach ($item->sewaOperasional as $i => $oprs)
                                    @if ($oprs->is_aktif == 'Y' && $oprs->status == 'SUDAH DICAIRKAN')
                                        <input type="hidden" class="addcost_{{ $item->id_sewa }} {{ $oprs->deskripsi }}" value="{{ $oprs->total_operasional }}">
                                        @php
                                            $total_addcost += $oprs->total_operasional;
                                        @endphp
                                    @endif
                                @endforeach
                                {{ number_format($total_addcost) }}
                                <input type="hidden" name="detail[{{ $item->id_sewa }}][addcost_details]" id="detail_addcost_{{ $item->id_sewa }}" value="{{ json_encode($item->sewaOperasional) }}" />
                                <input type="hidden" class="addcost_{{ $item->id_sewa }} {{ $item->deskripsi }}" name='detail[{{ $item->id_sewa }}][addcost]' id='addcost_hidden_{{ $item->id_sewa }}' value="{{ $total_addcost }}">
                            </td>
                            <td style="text-align:right">
                                <span id='diskon_text_{{ $item->id_sewa }}'></span>
                            </td>
                            <td style="text-align:right">
                                <span id='subtotal_text_{{ $item->id_sewa }}'>{{ number_format($total_addcost+$item->total_tarif) }}</span>
                                <input type="hidden" class="hitung_subtotal subtotal_hidden_{{ $item->id_sewa }} {{ $item->deskripsi }}" name='detail[{{ $item->id_sewa }}][subtotal]' id='subtotal_hidden_{{ $item->id_sewa }}' value="{{ $total_addcost+$item->total_tarif }}">
                            </td>
                            <td>
                                <span id="catatan_text_{{ $item->id_sewa }}">{{ $item->catatan }}</span>
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][nama_tujuan]' id='nama_tujuan_hidden_{{ $item->id_sewa }}' value="{{ $item->nama_tujuan }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][tgl_berangkat]' id='tgl_berangkat_hidden_{{ $item->id_sewa }}' value="{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][no_kontainer]' id='no_kontainer_hidden_{{ $item->id_sewa }}' value="{{ $item->no_kontainer }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][no_surat_jalan]' id='no_surat_jalan_hidden_{{ $item->id_sewa }}' value="{{ $item->no_surat_jalan }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][tarif]' id='tarif_hidden_{{ $item->id_sewa }}' value="{{ $item->total_tarif }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][catatan]' id='catatan_hidden_{{ $item->id_sewa }}' value="{{ $item->catatan }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][diskon]' id='diskon_hidden_{{ $item->id_sewa }}' >
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="button" name="detail" id="detail_{{$item->id_sewa}}" class="detail dropdown-item"> 
                                            <span class="fas fa-edit mr-3"></span> Edit
                                        </button>
                                        <a href="{{ route('invoice.destroy', ['invoice' => $item->id_sewa]) }}" class="dropdown-item" data-confirm-delete="true">
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
                <h5 class="modal-title">Detail</h5>
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
                                        <label for="sewa">Sewa <span style="color:red;">*</span></label>
                                        <select class="select2" style="width: 100%" id="addcost_sewa">
                                        </select>
                                    </div>   

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Tanggal Berangkat <span style="color:red;">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" autocomplete="off" class="form-control" id="tanggal_berangkat" placeholder="" readonly value="">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Tujuan <span style="color:red;">*</span></label>
                                        <input  type="text" class="form-control" id="nama_tujuan" readonly> 
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">No. Kontainer</label>
                                        <input  type="text" class="form-control" maxlength="50" id="no_kontainer"> 
                                    </div>
    
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">No. Surat Jalan</label>
                                        <input  type="text" class="form-control" maxlength="50" id="no_surat_jalan"> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="tarif">Tarif</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="tarif" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Add Cost</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="addcost" placeholder="" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Diskon</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="diskon" placeholder="" >
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Subtotal</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="subtotal" placeholder="" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Catatan</label>
                                        <input type="text" class="form-control" maxlength="255" id="catatan"> 
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='row'>
                            <div class="table-responsive p-0 mx-3">
                                <form name="add_addcost_detail" id="add_addcost_detail">
                                    <label for="">Detail Add Cost</label>
                                    <input type="hidden" id="deleted_temp" name="deleted_temp" placeholder="deleted_temp">
                                    <table class="table table-hover table-bordered table-striped text-nowrap" id="tabel_addcost">
                                        <thead>
                                            <tr class="">
                                                <th style="">Deskripsi</th>
                                                <th style="">Jumlah</th>
                                                <th style="">Ditagihkan</th>
                                                <th style="">Dipisahkan</th>
                                                <th style="">Catatan</th>
                                                {{-- <th style="text-align: center; vertical-align: middle;">#</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </form>
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
            // set value kode_customer
                var kodeValue = $('#billingTo option:selected').attr('kode');
                $('#kode_customer').val(kodeValue);
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
            startDate: today,
        }).datepicker("setDate", today);

        $("#jenis_badmin").select2({
            placeholder: "Pilih Metode",
            initSelection: function(element, callback) {                   
            }
        });

        // cara_pembayaran
            $("#showTransfer, #showCek, #showTunai").hide();
            $("input[name='cara_pembayaran']").change(function() {
                var selectedValue = $(this).val();
                // Hide all content divs
                $("#showTransfer, #showCek, #showTunai").hide();

                // Show the relevant content div based on the selected value
                clear();
                if (selectedValue === "transfer") {
                    $("#showTransfer").show();
                } else if (selectedValue === "tunai") {
                    $("#showTunai").show();
                } else if (selectedValue === "cek") {
                    $("#showCek").show();
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

        
        function clear(){
            $("#biaya_admin").val('');
            $("#no_cek").val('');
            $("#jenis_badmin").val('').trigger('change');
            $("#jenis_badmin").attr("disabled", "disabled");
            $("#biaya_admin").attr("disabled", "disabled");
            $("#BiayaAdminCheck").prop("checked", false);
        }

    });
</script>

@endsection


