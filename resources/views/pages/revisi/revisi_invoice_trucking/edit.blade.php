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
<section class="container-fluid">
    <form action="{{ route('revisi_tagihan_pembelian.update', ['revisi_tagihan_pembelian' => $data->id]) }}" id="save" method="POST" >
        @csrf @method('PUT')
        <div class="radiusSendiri sticky-top" style="margin-bottom: -15px;">
            <div class="card radiusSendiri" style="">
                <div class="card-header radiusSendiri">
                    <a href="{{ route('revisi_tagihan_pembelian.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        <div class="card radiusSendiri">
            <div class="card-body" >
                <div class="row">
                    <div class="bg-gray-light radiusSendiri col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">Supplier<span class="text-red">*</span> </label>
                                    <select name="supplier" class="select2" style="width: 100%" id="supplier" required disabled>
                                        <option value="">── PILIH SUPPLIER ──</option>
                                        @foreach ($supplier as $item)
                                            <option value="{{ $item->id }}" {{ $item->id == $data->id_supplier? 'selected':'' }}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="id_supplier" value="{{ $data->id_supplier }}">
                                    <input type="hidden" name="nama_supplier" value="{{ $data->getSupplier->nama }}">
                                    <input type="hidden" name="data_deleted" id="data_deleted">
                                </div>  
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Tanggal Bayar<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="tgl_bayar" class="form-control date" id="tgl_bayar" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Pilih Kas<span style="color:red">*</span></label>
                                    <select class="select2" style="width: 100%" id="id_kas" required disabled>
                                        <option value="">── PILIH SUPPLIER ──</option>
                                        @foreach ($dataKas as $item)
                                            <option value="{{ $item->id }}" {{ $item->id == $data->id_kas? 'selected':'' }}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="id_kas" value="{{ $data->id_kas }}">
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Alasan Revisi<span style="color:red">*</span></label>
                                <textarea name="catatan" class="form-control" rows="1" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="" class="">Total Tagihan</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" id="tagihan" name="total_tagihan" class="form-control uang numaja" value="" readonly>                         
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Total Bayar</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" id="total_bayar" name="total_bayar" class="form-control uang numaja" readonly>                         
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">PPh 23</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" id="pph23" name="pph" class="form-control uang numaja" readonly>                         
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
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
                    </div>

                </div>
            </div>
        </div> 
        {{-- <div style="overflow: auto;" > --}}
            <table class="table table-hover table-bordered " width="100%" id="tabel_tagihan">
                <thead >
                    <tr >
                        <th style="width: 200px;">No. Nota</th>
                        <th style="width: 500px;">Detail</th>
                        <th style="width: 150px;">Tagihan</th>
                        {{-- <th style="width: 150px; text-align: center;"><span style="font-size: 1.3em;"><sup>Tagihan</sup>/<sub>Sewa</sub></span></th> --}}
                        <th style="width: 100px; text-align: center">PPh23</th>
                        <th style="width: 150px; text-align: center">Total Bayar</th>
                        <th style="width: 150px; text-align: left">Bukti Potong</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="hasil">
                    @foreach ($data->getPembelian as $key => $pembelian)
                        <tr style="background: #ffffffc0" class="tr_{{ $pembelian->id }}">
                            <td>{{ $pembelian->no_nota }}
                                <input type="hidden" id="no_nota_{{ $key }}" value="{{ $pembelian->no_nota }}" name="data[{{ $pembelian->id }}][no_nota]">
                                <input type="hidden" id="bukti_potong_{{ $key }}" name="data[{{ $pembelian->id }}][bukti_potong]" value="{{ $pembelian->bukti_potong }}">
                                <input type="hidden" class="pph23" id="pph23_{{ $key }}" value="{{ $pembelian->pph }}" name="data[{{ $pembelian->id }}][pph]">
                                <input type="hidden" class="biaya_admin" id="biaya_admin_{{ $key }}" value="{{ $pembelian->biaya_admin }}" name="data[{{ $pembelian->id }}][biaya_admin]">
                                <input type="hidden" class="total_tagihan" id="total_tagihan_{{ $key }}" value="{{ $pembelian->total_tagihan }}" name="data[{{ $pembelian->id }}][total_tagihan]">
                                <input type="hidden" class="tagihan_dibayarkan" id="tagihan_dibayarkan_{{ $key }}" value="{{ $pembelian->tagihan_dibayarkan }}" name="data[{{ $pembelian->id }}][tagihan_dibayarkan]">
                            </td>
                            <td colspan="2"></td>
                            <td style="text-align: right;" class="font-weight-bold text-red text_pph23_{{ $key }}">{{ number_format($pembelian->pph) }}</td>
                            <td style="text-align: right;" class="font-weight-bold text-success text_tagihan_dibayarkan_{{ $key }}">{{ number_format($pembelian->tagihan_dibayarkan) }}</td>
                            <td class="text_bukti_potong_{{ $key }}">{{ $pembelian->bukti_potong }}</td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        <button class="btn dropdown-item openDetail" value="{{ $key }}">
                                            <span class="fas fa-sticky-note mr-3"></span> Edit
                                        </button>
                                        <button type="button" class="btn dropdown-item delete" value="{{ $pembelian->id }}">
                                            <span class="fas fa-trash-alt mr-3"></span> Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @foreach ($pembelian['getDetails'] as $item)
                            <tr style="background: #ffffff" class="tr_{{ $pembelian->id }}">
                                <td></td>
                                <td>{{ $item->deskripsi }} - {{ $item->jumlah }} - {{ $item->satuan }}</td>
                                <td style="text-align: right;" class="total_tagihan">{{ number_format($item->total_tagihan) }}</td>
                                <td colspan="4"></td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        {{-- </div> --}}
    </form>
</section>

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
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">No. Nota</label>
                                <input type="text" id="modal_no_nota" class="form-control" readonly>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Bukti Potong</label>
                                <input type="text" id="modal_bukti_potong" class="form-control" >
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-lg-7 col-md-7 col-sm-12">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Total Tagihan</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="modal_total_tagihan" placeholder="" readonly>
                                </div>
                            </div>
                            {{-- <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Total Sisa</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="modal_sisa_invoice" placeholder="" readonly>
                                </div>
                            </div> --}}
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
                                <label for="tarif">Total Bayar</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="modal_bayar">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
            <button type="button" class="btn btn-sm btn-success save_detail" style='width:85px'>OK</button> 
        </div>
    </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // toogle check biaya admin
        $("#BiayaAdminCheck").change(function() {
            if(this.checked) {
                $("#biaya_admin").removeAttr("readonly");
            } else {
                $("#biaya_admin").val('');
                $("#biaya_admin").attr("readonly", true);

                const cek_admin = $('#biaya_admin_0').val();
                const cek_dibayarkan = $('#tagihan_dibayarkan_0').val();
                if(parseFloat(cek_admin) != 0){
                    $('#biaya_admin_0').val(0);
                    $('#tagihan_dibayarkan_0').val(parseFloat(cek_dibayarkan)+parseFloat(cek_admin))
                    document.querySelector('.text_tagihan_dibayarkan_0').textContent = moneyMask($('#tagihan_dibayarkan_0').val());
                }
            }
            hitung();
        });
        // 

        var today = new Date();
        $('#tgl_bayar').val(dateMask(today));
        // $('#tabel_tagihan').DataTable( {
        //     searching: false, paging: false, info: false, ordering: false,
        //     rowGroup: {
        //         dataSrc: [0] // di order grup dulu, baru customer
        //     },
        //     columnDefs: [
        //         {
        //             targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
        //             visible: false
        //         },
        //     ],
        // });

        $(document).on('click', '.openDetail', function (event){
            clear();
            event.preventDefault();
            var id = this.value;
            var biaya_admin = !isNaN(parseFloat($('#biaya_admin_'+id).val()))? parseFloat($('#biaya_admin_'+id).val()):0;

            $('#key').val(id);
            $('#modal_no_nota').val( $('#no_nota_'+id).val() );
            $('#modal_bukti_potong').val( $('#bukti_potong_'+id).val() );
            $('#modal_pph23').val( moneyMask($('#pph23_'+id).val()) );
            $('#modal_total_tagihan').val( moneyMask($('#total_tagihan_'+id).val()) );
            var bayar = parseFloat($('#tagihan_dibayarkan_'+id).val()) + biaya_admin; 
            $('#modal_bayar').val( moneyMask( bayar ) );
            $('#modal_detail').modal('show');
        });

        $(document).on('click', '.save_detail', function (event){
            var id = $('#key').val();
            $('#no_nota_'+id).val( $('#modal_no_nota').val() );
            $('#bukti_potong_'+id).val( $('#modal_bukti_potong').val() );
            $('#total_tagihan_'+id).val( normalize($('#modal_total_tagihan').val()) );
            $('#pph23_'+id).val( normalize($('#modal_pph23').val()) );
            $('#tagihan_dibayarkan_'+id).val( normalize($('#modal_bayar').val()) );

            document.querySelector('.text_pph23_' + id).textContent = $('#modal_pph23').val();
            document.querySelector('.text_tagihan_dibayarkan_' + id).textContent = $('#modal_bayar').val();
            document.querySelector('.text_bukti_potong_' + id).textContent = $('#modal_bukti_potong').val();

            var elements = document.getElementsByClassName('bukti_pot_'+id);
            for(var i = 0; i < elements.length; i++){
                elements[i].textContent = $('#modal_bukti_potong').val();
            }

            if(id == 0){
                const cek_admin = $('#biaya_admin_0').val();
                if(parseFloat(cek_admin) != 0){
                    $('#biaya_admin_0').val(0);
                    document.getElementById("BiayaAdminCheck").checked = false;
                    $("#biaya_admin").val('');
                    $("#biaya_admin").attr("readonly", true);
                }
            }

            $('#modal_detail').modal('hide'); // close modal
            hitung();
        });

        $(document).on('click', '.delete', function(event){
            id = this.value;
            
            var trElements = document.querySelectorAll('tr.tr_'+id);
            for (var i = 0; i < trElements.length; i++) {
                trElements[i].remove();
            }

            let deleted = $('#data_deleted').val();
            if(deleted != ''){
                id = deleted + ','+id;
            }
            $('#data_deleted').val(id);

            hitung()
        });

        $(document).on('keyup', '#biaya_admin', function (event) {
            const tagihan = parseFloat($('#total_tagihan_0').val());
            const pph = parseFloat($('#pph23_0').val());
            $('#biaya_admin_0').val(normalize(this.value));

            $('#tagihan_dibayarkan_0').val( (parseFloat(tagihan) - pph) - normalize(this.value) )
            document.querySelector('.text_tagihan_dibayarkan_0').textContent = moneyMask($('#tagihan_dibayarkan_0').val());

            hitung();
        });

        $(document).on('keyup', '#modal_bayar', function (event) {
            var modal_total_tagihan = normalize($('#modal_total_tagihan').val());
            var modal_pph23 = normalize($('#modal_pph23').val());
            var modal_bayar = normalize($('#modal_bayar').val());

            let val = modal_bayar > modal_total_tagihan? modal_total_tagihan:modal_bayar;
            $('#modal_pph23').val( moneyMask(modal_total_tagihan-val) );
            this.value = val;
        });

        $(document).on('keyup', '#modal_pph23', function (event) {
            var modal_total_tagihan = normalize($('#modal_total_tagihan').val());
            var modal_pph23 = normalize($('#modal_pph23').val());
            var modal_bayar = normalize($('#modal_bayar').val());
            
            let val = modal_pph23 > modal_total_tagihan? modal_total_tagihan:modal_pph23;
            $('#modal_bayar').val( moneyMask(modal_total_tagihan-val) );
            this.value = val;
        });

        hitung_awal();
        cek_admin();

        function hitung_awal(){
            const total_tagihan = document.querySelectorAll(".total_tagihan");
            const pph23 = document.querySelectorAll(".pph23");
            const total_bayar = document.querySelectorAll(".tagihan_dibayarkan");
            let total_tagihan_all = 0;
            let total_bayar_all = 0;
            let total_pph23 = 0;

            total_tagihan.forEach(element => {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    total_tagihan_all += value;
                }
            });

            pph23.forEach(element => {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    total_pph23 += value;
                }
            });

            total_bayar.forEach(element => {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    total_bayar_all += value;
                }
            });

            var biaya_admin = !isNaN(normalize($('#biaya_admin').val()))? normalize($('#biaya_admin').val()):0;
            $('#total_bayar').val(moneyMask(total_bayar_all - biaya_admin));
            $('#pph23').val(moneyMask(total_pph23));
            $('#tagihan').val(moneyMask(total_tagihan_all));
        }
        
        function hitung(){
            const total_tagihan = document.querySelectorAll(".total_tagihan");
            const pph23 = document.querySelectorAll(".pph23");
            const total_bayar = document.querySelectorAll(".tagihan_dibayarkan");
            let total_tagihan_all = 0;
            let total_bayar_all = 0;
            let total_pph23 = 0;

            total_tagihan.forEach(element => {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    total_tagihan_all += value;
                }
            });

            pph23.forEach(element => {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    total_pph23 += value;
                }
            });

            total_bayar.forEach(element => {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    total_bayar_all += value;
                }
            });

            var biaya_admin = !isNaN(normalize($('#biaya_admin').val()))? normalize($('#biaya_admin').val()):0;
          
            $('#total_bayar').val(moneyMask(total_bayar_all));
            $('#pph23').val(moneyMask(total_pph23));
            $('#tagihan').val(moneyMask(total_tagihan_all));
        }

        function cek_admin(){
            const total_admin = document.querySelectorAll(".biaya_admin");
            let total_biaya_admin = 0;

            total_admin.forEach(element => {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    total_biaya_admin += value;
                }
            });
            if(total_biaya_admin > 0){
                document.getElementById("BiayaAdminCheck").checked = true;
                $("#biaya_admin").val(moneyMask(total_biaya_admin)).removeAttr("readonly");
            }
        }

        function clear(){
            $('#key').val('');
            $('#modal_no_nota').val('');
            $('#modal_bukti_potong').val('');
            $('#modal_total_tagihan').val('');
            $('#modal_sisa_invoice').val('');
            $('#modal_pph23').val('');
            $('#modal_bayar').val('');
        }
    });
</script>

@endsection

