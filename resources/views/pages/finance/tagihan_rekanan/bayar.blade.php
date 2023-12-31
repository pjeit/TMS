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
    <form action="{{ route('tagihan_rekanan.bayar_save') }}" id="save" method="POST" >
        @csrf
        <div class="radiusSendiri sticky-top" style="margin-bottom: -15px;">
            <div class="card radiusSendiri" style="">
                <div class="card-header radiusSendiri">
                    <a href="{{ route('tagihan_rekanan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        <div class="card radiusSendiri">
            <div class="card-body" >
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="" class="text-success">Total Tagihan</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" id="tagihan" name="total_tagihan" class="form-control uang numaja" value="" readonly>                         
                                </div>
                            </div>
                            {{-- <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="" class="text-danger">Diskon</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div> --}}
                                    <input type="hidden" id="diskon" name="diskon" class="form-control uang numaja" value="" readonly>                         
                                {{-- </div>
                            </div> --}}
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
                       
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="1"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-light col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">Supplier<span class="text-red">*</span> </label>
                                    <select name="supplier" class="select2" style="width: 100%" id="supplier" required disabled>
                                        <option value="">── PILIH SUPPLIER ──</option>
                                        @foreach ($supplier as $item)
                                            <option value="{{ $item->getSupplier->id }}" {{ $item->getSupplier->id == $data_tagihan[0]->id_supplier? 'selected':'' }}>{{ $item->getSupplier->nama }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="id_supplier" value="{{ $data_tagihan[0]->id_supplier }}">
                                    <input type="hidden" name="nama_supplier" value="{{ $data_tagihan[0]->getSupplier->nama }}">
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
                                    <select name="id_kas" class="select2" style="width: 100%" id="id_kas" required>
                                        <option value="">── PILIH SUPPLIER ──</option>
                                        @foreach ($dataKas as $item)
                                            <option value="{{ $item->id }}" {{ $item->id == 1? 'selected':'' }}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
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
            <table class="table table-hover table-bordered table-striped " width="100%" id="tabel_tagihan">
                <thead>
                    <tr>
                        <th>No. Nota</th>
                        <th style="width: 150px; text-align: left">No. Sewa</th>
                        <th style="width: 150px; text-align: left">Tagihan per Sewa</th>
                        {{-- <th style="width: 150px; text-align: center">Total Tagihan</th> --}}
                        {{-- <th style="width: 150px; text-align: center">PPh 23</th> --}}
                        {{-- <th style="width: 150px; text-align: center">Total Sisa</th> --}}
                        {{-- <th style="width: 150px; text-align: center">Total Bayar</th> --}}
                        {{-- <th style="width: 150px; text-align: center">Bukti Potong</th> --}}
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="hasil">
                    @foreach ($data_tagihan as $key => $tagihan)
                        @foreach ($tagihan['getDetails'] as $item)
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between ">
                                        <div>
                                            <span class="font-weight-normal align-content-end">No. Nota: </span> {{ $tagihan->no_nota }}  
                                            <input type="hidden" id="no_nota_{{ $tagihan->id }}" value="{{ $tagihan->no_nota }}" name="data[{{ $tagihan->id }}][no_nota]">
                                            <input type="hidden" id="bukti_potong_{{ $tagihan->id }}" name="data[{{ $tagihan->id }}][bukti_potong]">
                                            <input type="hidden" id="total_tagihan_{{ $tagihan->id }}" value="{{ $tagihan->total_tagihan }}" name="data[{{ $tagihan->id }}][total_tagihan]">
                                            <input type="hidden" class="sisa_tagihan" id="sisa_tagihan_{{ $tagihan->id }}" value="{{ $tagihan->sisa_tagihan }}" name="data[{{ $tagihan->id }}][sisa_tagihan]">
                                            {{-- <input type="hidden" class="ppn" id="ppn_{{ $tagihan->id }}" value="{{ $tagihan->ppn }}" name="data[{{ $tagihan->id }}][ppn]"> --}}
                                            <input type="hidden" class="ppn" id="ppn_{{ $tagihan->id }}" value="{{ $tagihan->ppn }}" name="data[{{ $tagihan->id }}][ppn]">
                                            <input type="hidden" class="pph23" id="pph23_{{ $tagihan->id }}" name="data[{{ $tagihan->id }}][pph]">
                                            <input type="hidden" class="diskon" id="diskon_{{ $tagihan->id }}" value="{{ $tagihan->diskon }}" name="data[{{ $tagihan->id }}][diskon]">
                                            <input type="hidden" class="total_bayar" id="total_bayar_{{ $tagihan->id }}" name="data[{{ $tagihan->id }}][total_bayar]" value="0">
                                        </div>

                                        <div class="btn-group dropleft">
                                            <button class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu" >
                                                <button class="btn dropdown-item openDetail" value="{{ $tagihan->id }}">
                                                    <span class="fas fa-sticky-note mr-3"></span> Edit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="">{{ $item->getSewa->no_sewa }} ({{ date("Y-m-d", strtotime($item->getSewa->tanggal_berangkat)) }})</td>
                                <td style="text-align: left;">{{ number_format($item->total_tagihan) }}</td>
                                {{-- <td style="text-align: right;">{{ number_format($tagihan->total_tagihan) }}</td> --}}
                                {{-- <td style="text-align: right;"><span class="tot_ppn_{{ $tagihan->id }}">{{ number_format($tagihan->ppn) }}</span></td> --}}
                                {{-- <td style="text-align: right;">{{ number_format($tagihan->sisa_tagihan) }}</td> --}}
                                {{-- <td style="text-align: right;"><span class="tot_bayar_{{ $tagihan->id }}"></span></td> --}}
                                {{-- <td style="text-align: left;"><span class="bukti_pot_{{ $tagihan->id }}"></span></td> --}}
                                <td style="text-align: left;"></td>
                                {{-- <td class='text-center' style="text-align:center">
                                    <div class="btn-group dropleft">
                                        <button class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu" >
                                            <a class="btn dropdown-item">
                                                <span class="fas fa-sticky-note mr-3"></span> Edit
                                            </a>
                                        </div>
                                    </div>
                                </td> --}}
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
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Total Tagihan</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="modal_total_tagihan" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Total Sisa</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="modal_sisa_invoice" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Diskon</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="modal_diskon" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">PPN</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="modal_ppn" placeholder="" readonly>
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

{{-- logic save --}}
<script type="text/javascript">
    // $(document).ready(function() {
    //     $('#save').submit(function(event) {
    //         // cek total_dibayar
    //             var total_dibayar = $('#total_dibayar').val();
    //             console.log('total_dibayar', total_dibayar);
    //             if(escapeComma(total_dibayar) == 0 || escapeComma(total_dibayar) == ''){
    //                 Swal.fire(
    //                     'Data tidak valid',
    //                     'Total bayar masih 0, harap periksa kembali data anda!',
    //                     'warning'
    //                 )
    //                 return false;
    //             }
    //         //

    //         event.preventDefault(); // Prevent form submission
    //         Swal.fire({
    //             title: 'Apakah Anda yakin data sudah benar ?',
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
    //                 this.submit();
    //             }else{
    //                 const Toast = Swal.mixin({
    //                     toast: true,
    //                     position: 'top',
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
    //             }
    //         })
    //     });
    // });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        // toogle check biaya admin
        $("#BiayaAdminCheck").change(function() {
            if(this.checked) {
                $("#biaya_admin").removeAttr("readonly");
            } else {
                $("#biaya_admin").val('');
                $("#biaya_admin").attr("readonly", true);
            }
            hitung();
        });
        // 

        var today = new Date();
        $('#tgl_bayar').val(dateMask(today));

        $('#tabel_tagihan').DataTable( {
            searching: false, paging: false, info: false, ordering: false,
            rowGroup: {
                dataSrc: [0] // di order grup dulu, baru customer
            },
            columnDefs: [
                {
                    targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                    visible: false
                },
            ],
        });

        $(document).on('click', '.openDetail', function (event){
            clear();
            event.preventDefault();
            var id = this.value;

            $('#key').val(id);
            $('#modal_no_nota').val( $('#no_nota_'+id).val() );
            $('#modal_bukti_potong').val( $('#bukti_potong_'+id).val() );
            $('#modal_diskon').val( moneyMask($('#diskon_'+id).val()) );
            $('#modal_pph23').val( moneyMask($('#pph23_'+id).val()) );
            $('#modal_total_tagihan').val( moneyMask($('#total_tagihan_'+id).val()) );
            $('#modal_sisa_invoice').val( moneyMask($('#sisa_tagihan_'+id).val()) );
            var ppn = moneyMask($('#ppn_'+id).val()) == 0 || moneyMask($('#ppn_'+id).val()) == ''? '':moneyMask($('#ppn_'+id).val());
            $('#modal_ppn').val( ppn );
            var bayar = moneyMask($('#total_bayar_'+id).val()) == 0 || moneyMask($('#total_bayar_'+id).val()) == ''? '':moneyMask($('#total_bayar_'+id).val());
            $('#modal_bayar').val( bayar );

            // if(ppn != 0){
            //     hitungPPh(ppn);
            // }

            $('#modal_detail').modal('show');
        });

        $(document).on('click', '.save_detail', function (event){
            var id = $('#key').val();
            $('#no_nota_'+id).val( $('#modal_no_nota').val() );
            $('#bukti_potong_'+id).val( $('#modal_bukti_potong').val() );
            $('#total_tagihan_'+id).val( normalize($('#modal_total_tagihan').val()) );
            $('#sisa_tagihan_'+id).val( normalize($('#modal_sisa_invoice').val()) );
            $('#pph23_'+id).val( normalize($('#modal_pph23').val()) );
            $('#total_bayar_'+id).val( normalize($('#modal_bayar').val()) );

            var elements = document.getElementsByClassName('bukti_pot_'+id);
            for(var i = 0; i < elements.length; i++){
                elements[i].textContent = $('#modal_bukti_potong').val();
            }

            // var tot_bayars = document.getElementsByClassName('tot_bayar_'+id);
            // for(var i = 0; i < tot_bayars.length; i++){
            //     tot_bayars[i].textContent = $('#modal_bayar').val();
            // }

            // var tot_pph23 = document.getElementsByClassName('tot_ppn_'+id);
            // for(var i = 0; i < tot_ppn.length; i++){
            //     tot_pph23[i].textContent = $('#modal_pph23').val();
            // }


            $('#modal_detail').modal('hide'); // close modal
            hitung();
        });



        $(document).on('keyup', '#biaya_admin', function (event) {
            hitung();
        });

        $(document).on('keyup', '#modal_pph23', function (event) {
            hitungPPh(this.value);
        });
        function hitungPPh(val){
            var modal_sisa_invoice = normalize($('#modal_sisa_invoice').val());

            if( normalize(val) > modal_sisa_invoice){
                val = modal_sisa_invoice;
                $('#modal_bayar').val(moneyMask(modal_sisa_invoice - normalize(val)));
                return val;
            }else{
                $('#modal_bayar').val(moneyMask(modal_sisa_invoice - normalize(val)));
            }
        }

        $(document).on('keyup', '#modal_bayar', function (event) {
            hitungBayar(this.value);
        });

        function hitungBayar(val){
            var modal_sisa_invoice = normalize($('#modal_sisa_invoice').val());

            if( normalize(val) > modal_sisa_invoice){
                val = modal_sisa_invoice;
                $('#modal_pph23').val(moneyMask(modal_sisa_invoice - normalize(val)));
                return val;
            }else{
                $('#modal_pph23').val(moneyMask(modal_sisa_invoice - normalize(val)));
            }
        }

        hitung();
        function hitung(){
            const sisa_tagihan = document.querySelectorAll(".sisa_tagihan");
            const pph23 = document.querySelectorAll(".pph23");
            const total_bayar = document.querySelectorAll(".total_bayar");
            const total_diskon = document.querySelectorAll(".diskon");
            let total_sisa_tagihan = 0;
            let total_bayar_all = 0;
            let total_pph23 = 0;
            let total_diskon_all = 0;

            sisa_tagihan.forEach(element => {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    total_sisa_tagihan += value;
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

            total_diskon.forEach(element => {
                const value = parseFloat(element.value);
                if (!isNaN(value)) {
                    total_diskon_all += value;
                }
            });

            var biaya_admin = !isNaN(normalize($('#biaya_admin').val()))? normalize($('#biaya_admin').val()):0;

            $('#total_bayar').val(moneyMask(total_bayar_all - biaya_admin));
            $('#pph23').val(moneyMask(total_pph23));
            $('#tagihan').val(moneyMask(total_sisa_tagihan));
            $('#diskon').val(moneyMask(total_diskon_all));
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


