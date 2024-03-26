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
    <form action="{{ route('tagihan_gabungan.bayar_save') }}" id="save" method="POST" >
        @csrf
        <div class="radiusSendiri sticky-top" style="margin-bottom: -15px;">
            <div class="card radiusSendiri" style="">
                <div class="card-header radiusSendiri">
                    <a href="{{ route('tagihan_gabungan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
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
                                        {{-- <option value="">── PILIH SUPPLIER ──</option> --}}
                                        @if ($data_tagihan[0]->getSupplier)
                                            <option value="{{ $data_tagihan[0]->getSupplier->id }}" >{{ $data_tagihan[0]->getSupplier->nama }}</option>
                                        @endif
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
                                        <option value="">── PILIH KAS ──</option>
                                        @foreach ($dataKas as $item)
                                            <option value="{{ $item->id }}" {{ $item->id == 1? 'selected':'' }}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="1"></textarea>
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
            <button type="button" class="btn btn-primary mb-2" id="tambah_nota"> <i class="fa fa-plus-square"></i> Tambah Nota Gabungan</button>
            <table class="table table-hover table-bordered " width="100%" id="tabel_tagihan">
                <thead >
                    <tr >
                        <th style="width: 100px;">No. Nota</th>
                        <th style="width: 600px;">Data Sewa</th>
                        <th style="width: 150px; text-align: center;"><span style="font-size: 1.3em;"><sup>Nominal Tagihan</sup></span></th>
                        <th style="width: 100px; text-align: center">PPh23</th>
                        <th style="width: 150px; text-align: center">Total Bayar</th>
                        <th style="width: 150px; text-align: left">Bukti Potong</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="hasil">
                    @foreach ($data_tagihan as $key => $tagihan)
                        <tr style="background: #ffffffc0" id="{{$key}}">
                            <input type="hidden" id="id_nota_{{ $tagihan->id }}" value="{{ $tagihan->id }}" name="data[{{ $tagihan->id }}][id_nota]" class="all_id_nota">
                            <input type="hidden" id="no_nota_{{ $tagihan->id }}" value="{{ $tagihan->no_nota }}" name="data[{{ $tagihan->id }}][no_nota]">
                            <input type="hidden" id="bukti_potong_{{ $tagihan->id }}" name="data[{{ $tagihan->id }}][bukti_potong]">
                            <input type="hidden" id="total_tagihan_{{ $tagihan->id }}" value="{{ $tagihan->total_tagihan }}" name="data[{{ $tagihan->id }}][total_tagihan]">
                            <input type="hidden" class="sisa_tagihan" id="sisa_tagihan_{{ $tagihan->id }}" value="{{ $tagihan->sisa_tagihan }}" name="data[{{ $tagihan->id }}][sisa_tagihan]">
                            <input type="hidden" class="pph23" id="pph23_{{ $tagihan->id }}" name="data[{{ $tagihan->id }}][pph]">
                            <input type="hidden" class="total_bayar" id="total_bayar_{{ $tagihan->id }}" name="data[{{ $tagihan->id }}][total_bayar]" value="0">

                            {{-- <td>{{ $tagihan->no_nota }}</td> --}}
                            <td colspan="2">{{ $tagihan->no_nota }}</td>
                            <td style="text-align: right;"  class="font-weight-bold">{{ number_format($tagihan->total_tagihan) }}</td>

                            <td style="text-align: right;" class="font-weight-bold text-red text_pph23_{{ $tagihan->id }}">0</td>
                            <td style="text-align: right;" class="font-weight-bold text-success text_tagihan_{{ $tagihan->id }}">0</td>
                            <td class="text_bukti_potong_{{ $tagihan->id }}"></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        <button class="btn dropdown-item openDetail" value="{{ $tagihan->id }}">
                                            <span class="fas fa-sticky-note mr-3"></span> Edit
                                        </button>
                                        <button type="button" name="hapus" id="hapus_{{$key}}" class="dropdown-item deleteParent"> 
                                            <span class="fas fa-trash mr-3"></span> Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @foreach ($tagihan['getDetails'] as $item)
                            <tr style="background: #ffffff" class="detail_nota_{{$key}}" id="{{$key}}">
                                <td></td>
                                <td>{{ $item->getSewa->getCustomer->kode }} - {{ $item->getSewa->nama_tujuan }} ({{ date("d-M-Y", strtotime($item->getSewa->tanggal_berangkat)) }})</td>
                                <td style="text-align: right;">{{ number_format($item->total_tagihan) }}</td>
                                <td colspan="4"></td>
                            </tr>
                        @endforeach
                        {{-- <tr style="background: #ffffff90">
                            <td colspan="3"></td>
                            <td style="text-align: right;">0</td>
                            <td style="text-align: right;">0</td>
                            <td colspan="2"></td>
                        </tr> --}}
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
                <input type="hidden" name="key" id="key">
                <input type="hidden"  id="jenis"> 
                <input type="hidden"  id="hidden_detailnota_modal"> 
                <input type="hidden"  id="modal_no_nota"> 

                <div class='row'>
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">No. Nota</label>
                                {{-- <input type="text" id="modal_no_nota" class="form-control" readonly> --}}
                                <select name="supplier" class="select2" style="width: 100%" id="modal_select_no_nota">
                                </select>

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
            <button type="button" class="btn btn-sm btn-success save_detail"  id="save_detail" style='width:85px'>OK</button> 
            <button type="button" class="btn btn-sm btn-success save_data_baru" id="save_data_baru" style='width:85px'>Tambah</button> 
        </div>
    </div>
    </div>
</div>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('#save').submit(function(event) {
            // cek total_dibayar
                var total_dibayar = $('#total_bayar').val();
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
        var data_tagihan_all = <?=json_encode($data_tagihan_from_supplier)?>;

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
        $(document).on('click', '#tambah_nota', function (event) {
            clear(); // execute clear data dulu tiap open modal
            $('#jenis').val('baru'); // key di clear dulu
            $("#save_detail").hide();
            $("#save_data_baru").show();
          
            var option = $('<option>');
            option.text('── Pilih Nota ──');
            option.val('');
            option.prop('selected', true);
            $('#modal_select_no_nota').append(option);

            let all_id_nota = [];
            $('.all_id_nota').each(function() {
                all_id_nota.push($(this).val());
            });

            data_tagihan_all.forEach(function(item, index) {
                var option = $('<option>');
                option.text(item.no_nota + '(' + dateMask(item.tgl_nota) + ')');
                option.val(item.id);
                option.attr('index', index); 
                option.attr('total_tagihan', item.total_tagihan); 
                option.attr('sisa_tagihan', item.sisa_tagihan); 
                option.attr('no_nota', item.no_nota); 
                option.attr('bukti_potong', item.bukti_potong); 
                option.attr('detail_nota', JSON.stringify(item.get_details)); 


                if ( all_id_nota.includes( item.id.toString() ) ) {
                    option.prop('disabled', true);
                }
                
                $('#modal_select_no_nota').append(option);
            });
            $("#modal_select_no_nota").attr('disabled',false);
            $('#modal_select_no_nota').select2();
            $('#modal_detail').modal('show');
        });
        $('body').on('change','#modal_select_no_nota',function()
        {
            var jenis = $("#jenis").val();
            var id = $(this).val();
            var selectedOption = $(this).find('option:selected');

            if (jenis == "baru") {
                var total_tagihan=selectedOption.attr('total_tagihan');
                var sisa_tagihan=selectedOption.attr('sisa_tagihan');
                var catatan=selectedOption.attr('catatan');
                var no_nota=selectedOption.attr('no_nota');
                var detail_nota=selectedOption.attr('detail_nota');
                // console.log(JSON.parse(detail_nota));

                if(id)
                {
                    $('#key').val(id); // key di clear dulu
                    $('#modal_no_nota').val( no_nota );
                    $('#modal_catatan').val( catatan );
                    $('#modal_total_tagihan').val( moneyMask(total_tagihan) );
                    $('#modal_sisa_invoice').val( moneyMask(sisa_tagihan) );
                    $('#hidden_detailnota_modal').val( detail_nota );
                    
                }
                else
                {
                    $('#key').val(''); // key di clear dulu
                    $('#modal_no_nota').val('');
                    $('#modal_catatan').val( '' );
                    $('#modal_total_tagihan').val( '' );
                    $('#modal_sisa_invoice').val( '' );
                    $('#hidden_detailnota_modal').val( '' );

                }
            }
        });
        $(document).on('click', '#save_data_baru', function (event) {   
        
            let lastRow = $("#tabel_tagihan > tbody tr:last");
            let id = lastRow.attr("id");
            
            if (lastRow.length >= 0) {
                // id = lastRow.attr("id");
                if(id == undefined){
                    id = 0;
                }else{
                    id++;
                }
            }
            var id_nota = $('#key').val();
            var no_nota = $('#modal_no_nota').val();
            var no_bukti_potong =  $('#modal_bukti_potong').val();
            var total_tagihan = $('#modal_total_tagihan').val();
            var total_sisa = $('#modal_sisa_invoice').val();
            var total_pph23 =$('#modal_pph23').val();
            var total_dibayar = $('#modal_bayar').val();
            var detail_nota = $('#hidden_detailnota_modal').val();

            // console.log(detail_nota);

            if(id_nota == ''){
                Swal.fire(
                    'Error',
                    'Data Nota masih kosong!',
                    'error'
                )
                return false;
            }
            if(id_nota){
                if(total_dibayar == '' || total_dibayar ==0){
                    Swal.fire(
                        'Error',
                        'Total dibayar harus diisi!',
                        'error'
                    )
                    return false;
                }
            }
            var table = `
            <tr style="background: #ffffff88" id="${id}">
                            <input type="hidden" id="id_nota_${id_nota}" value="${id_nota}" name="data[${id_nota}][id_nota]" class="all_id_nota">
                            <input type="hidden" id="no_nota_${id_nota}" value="${no_nota}" name="data[${id_nota}][no_nota]">
                            <input type="hidden" id="bukti_potong_${id_nota}" name="data[${id_nota}][bukti_potong]" value=${no_bukti_potong}>
                            <input type="hidden" id="total_tagihan_${id_nota}" value="${normalize(total_tagihan)}" name="data[${id_nota}][total_tagihan]">
                            <input type="hidden" class="sisa_tagihan" id="sisa_tagihan_${id_nota}" value="${normalize(total_sisa)}" name="data[${id_nota}][sisa_tagihan]">
                            <input type="hidden" class="pph23" id="pph23_${id_nota}" name="data[${id_nota}][pph]" value="${normalize(total_pph23)}">
                            <input type="hidden" class="total_bayar" id="total_bayar_${id_nota}" name="data[${id_nota}][total_bayar]" value="${normalize(total_dibayar)}">

                            <td colspan="2">${no_nota}</td>
                            <td style="text-align: right;"  class="font-weight-bold">${total_tagihan}</td>

                            <td style="text-align: right;" class="font-weight-bold text-red text_pph23_${id_nota}">${total_pph23}</td>
                            <td style="text-align: right;" class="font-weight-bold text-success text_tagihan_${id_nota}">${total_dibayar}</td>
                            <td class="text_bukti_potong_${id_nota}">${no_bukti_potong}</td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        <button class="btn dropdown-item openDetail" value="${id_nota}">
                                            <span class="fas fa-sticky-note mr-3"></span> Edit
                                        </button>
                                        <button type="button" name="hapus" id="hapus_${id}" class="dropdown-item deleteParent"> 
                                            <span class="fas fa-trash mr-3"></span> Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                            ${JSON.parse(detail_nota).map(item => `
                                <tr style="background: #ffffff" class="detail_nota_${id}" id="${id}">
                                <td></td>
                                <td>${item.get_sewa.get_customer.kode} ${item.get_sewa.nama_tujuan} (${dateMask(item.get_sewa.tanggal_berangkat)})</td>
                                <td style="text-align: right;">${moneyMask(item.total_tagihan)}</td>
                                <td colspan="4"></td>
                            </tr>
                            `).join('')}
                        </tr>
                       
            `
            // @foreach ($tagihan['getDetails']  as $item)
            //                 <tr style="background: #ffffff">
            //                     <td></td>
            //                     <td>{{ $item->deskripsi }}</td>
            //                     <td style="text-align: right;font-size: 0.9em;">{{ number_format($item->total_tagihan) }}</td>
            //                     <td colspan="4"></td>
            //                 </tr>
            //             @endforeach
            // console.log(table);
            $('#tabel_tagihan > tbody:last-child').append(table);
            hitung();
            clear();
            $('#modal_detail').modal('hide'); // close modal
        });

        $(document).on('click', '.openDetail', function (event){
            clear();
            $('#jenis').val('lama'); // key di clear dulu
            $("#save_detail").show();
            $("#save_data_baru").hide();
            event.preventDefault();
            var id = this.value;

            var option = $('<option>');
            option.text('── Pilih Nota ──');
            option.val('');
            option.prop('selected', true);
            $('#modal_select_no_nota').append(option);

            let all_id_nota = [];
            $('.all_id_nota').each(function() {
                all_id_nota.push($(this).val());
            });

            data_tagihan_all.forEach(function(item, index) {
                var option = $('<option>');
                option.text(item.no_nota + '(' + dateMask(item.tgl_nota) + ')');
                option.val(item.id);
                option.attr('index', index); 
                option.attr('total_tagihan', item.total_tagihan); 
                option.attr('sisa_tagihan', item.sisa_tagihan); 
                option.attr('no_nota', item.no_nota); 
                option.attr('bukti_potong', item.bukti_potong); 
                option.attr('detail_nota', JSON.stringify(item.get_details)); 


                if ( all_id_nota.includes( item.id.toString() ) ) {
                    option.prop('disabled', true);
                }
                if(item.id==id)
                {
                    option.prop('selected', true);
                }
                
                $('#modal_select_no_nota').append(option);
            });
            $("#modal_select_no_nota").attr('disabled',true);
            $('#modal_select_no_nota').select2();
            $('#key').val(id);
            $('#modal_no_nota').val( $('#no_nota_'+id).val() );
            $('#modal_bukti_potong').val( $('#bukti_potong_'+id).val() );
            $('#modal_pph23').val( moneyMask($('#pph23_'+id).val()) );
            $('#modal_total_tagihan').val( moneyMask($('#total_tagihan_'+id).val()) );
            $('#modal_sisa_invoice').val( moneyMask($('#sisa_tagihan_'+id).val()) );
            var bayar = moneyMask($('#total_bayar_'+id).val()) == 0 || moneyMask($('#total_bayar_'+id).val()) == ''? '':moneyMask($('#total_bayar_'+id).val());
            $('#modal_bayar').val( bayar );

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

            document.querySelector('.text_pph23_' + id).textContent = $('#modal_pph23').val();
            document.querySelector('.text_tagihan_' + id).textContent = $('#modal_bayar').val();
            document.querySelector('.text_bukti_potong_' + id).textContent = $('#modal_bukti_potong').val();

            var elements = document.getElementsByClassName('bukti_pot_'+id);
            for(var i = 0; i < elements.length; i++){
                elements[i].textContent = $('#modal_bukti_potong').val();
            }

            $('#modal_detail').modal('hide'); // close modal
            hitung();
        });

        $(document).on('click', '.deleteParent', function (event) {
            var closestTR = $(this).closest('tr');
            var id = closestTR.attr('id');
            $('.detail_nota_'+id).remove();
            closestTR.remove();
            hitung();
        });
        $(document).on('keyup', '#biaya_admin', function (event) {
            hitung();
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
            var modal_sisa_invoice = !isNaN(normalize($('#modal_sisa_invoice').val()))? normalize($('#modal_sisa_invoice').val()):0;
            var modal_pph23 = !isNaN(normalize($('#modal_pph23').val()))? normalize($('#modal_pph23').val()):0;
            var modal_bayar = !isNaN(normalize($('#modal_bayar').val()))? normalize($('#modal_bayar').val()):0;

            console.log('modal_sisa_invoice', modal_sisa_invoice);
            if( (modal_bayar+modal_pph23) > modal_sisa_invoice){
                $('#modal_pph23').val( 0 );
                $('#modal_bayar').val( moneyMask(modal_sisa_invoice) );
            }
        });

        $(document).on('keyup', '#modal_pph23', function (event) {
            var modal_sisa_invoice = normalize($('#modal_sisa_invoice').val());
            var modal_pph23 = normalize($('#modal_pph23').val());
            var modal_bayar = normalize($('#modal_bayar').val());
            
            let val = modal_pph23>modal_sisa_invoice? modal_sisa_invoice:modal_pph23;
            if(isNaN(val)){
                val = 0;
            }
            $('#modal_bayar').val( moneyMask(modal_sisa_invoice-val) );
            console.log('val', val);
            this.value = val;
        });


        hitung();
        function hitung(){
            const sisa_tagihan = document.querySelectorAll(".sisa_tagihan");
            const pph23 = document.querySelectorAll(".pph23");
            const total_bayar = document.querySelectorAll(".total_bayar");
            let total_sisa_tagihan = 0;
            let total_bayar_all = 0;
            let total_pph23 = 0;

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


            var biaya_admin = !isNaN(normalize($('#biaya_admin').val()))? normalize($('#biaya_admin').val()):0;

            $('#total_bayar').val(moneyMask(total_bayar_all - biaya_admin));
            $('#pph23').val(moneyMask(total_pph23));
            $('#tagihan').val(moneyMask(total_sisa_tagihan));
        }

        function clear(){
            $('#key').val('');
            $('#modal_no_nota').val('');
            $('#modal_bukti_potong').val('');
            $('#modal_total_tagihan').val('');
            $('#modal_sisa_invoice').val('');
            $('#modal_pph23').val('');
            $('#modal_bayar').val('');
            $('#modal_select_no_nota').empty(); 

        }
    });
</script>

@endsection

