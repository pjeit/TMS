
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
<div class="container-fluid">
    {{-- <div class="radiusSendiri sticky-top " style="margin-bottom: -15px;">
        <div class="card radiusSendiri" style="">
            <div class="p-3">
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
        </div>
    </div> --}}
    <div class="card radiusSendiri">
        <form id="save" action="{{ route('revisi_biaya_operasional.store') }}" method="POST">
            @csrf
            <div class="card-header ">
                <div class="card-header" style="border: 2px solid #bbbbbb;">
                    <ul class="list-inline">
                        <div class="row">
                            {{-- <li class="list-inline-item"> --}}
                                <div class="col-sm-12 col-md-3 col-lg-3 bg-white pb-3">
                                    <div class="form-group">
                                        <label for="">Jenis Biaya</label> 
                                        <select class="form-control selectpicker" required name="item" id="item" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                            <option value="">­­— PILIH DATA —</option>
                                            <option value="TALLY">TALLY</option>
                                            <option value="SEAL PELAYARAN">SEAL PELAYARAN</option>
                                            <option value="OPERASIONAL">ALAT</option>
                                            <option value="TIMBANG">TIMBANG</option>
                                            <option value="BURUH">BURUH</option>
                                            <option value="LEMBUR">LEMBUR</option>
                                            <option value="KARANTINA">KARANTINA</option>
                                        </select>
                                    </div>
                                </div>
                  
                                <div class="col-sm-12 col-md-5 col-lg-5 bg-white pb-3">
                                    <div class="form-group">
                                        <label for="">&nbsp; </label>
                                        <button type="submit" class="btn btn-success ml-4" id="bttonBayar"><i class="fa fa-save" aria-hidden="true" ></i> Simpan</button>
                                    </div>
                                </div>
                            {{-- </li> --}}
                        </div>
                    </ul>
                </div>
            </div>
        
            <div class="card-body">
                <section class="col-lg-12" id="show_report">
                    <table id="rowGroup" class="table table-bordered table-hover" width="100%">
                        <thead id="theadId">
                            <tr>
                                <th>Revisi Biaya Operasional</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyId">

                        </tbody>
                    </table>
                </section>
            </div>
        </form>
    </div>
</div>


{{-- modal edit --}}
<div class="modal fade" id="modal_delete" tabindex='-1'>
    <div class="modal-dialog modal-sm">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Konfirmasi hapus data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
        </div>
        <form id="save" action="{{ route('revisi_biaya_operasional.delete') }}" method="POST">
            @csrf 
            <div class="modal-body">
                <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}
                <input type="hidden" name="modal_item" id="modal_item"> {{--* dipakai buat simpen item --}}

                <div class='row'>
                    <div class="col-12">
                        <div class="form-group">
                            <span>Apakah anda yakin ingin menghapus data ini?</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Alasan<span style="color:red">*</span></label>
                            <textarea name="alasan" class="form-control" id="alasan" rows="2" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-sm btn-success save_detail" id="simpanBuktiPotong" style='width:85px'>OK</button> 
            </div>
        </form>

    </div>
    <!-- /.modal-content -->
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('change', '#item', function(e) {  
            var item = $('#item').val();
            if(item != ''){
                showTable(item);
            }else{
                var table = $('#rowGroup').DataTable();
                table.clear().draw();
            }
		});        
        
        function showTable(item){
            $.ajax({
                method: 'GET',
                url: `revisi_biaya_operasional/load_data/${item}`,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    $("#rowGroup").dataTable().fnDestroy();
                    $("th").remove();
                    $("#tbodyId").empty();

                    var item = $('#item').val();
                    var data = response.data;

                    console.log('data', data);
                    if(data != ''){
                        if(item == 'KARANTINA'){
                            $("thead tr").append(`  <th>Grup</th>
                                                    <th>Customer</th>
                                                    <th>No. BL</th>
                                                    <th>Kapal / Voyage</th>
                                                    <th>Biaya</th>
                                                    <th>Dicairkan</th>
                                                    <th>Catatan</th>    
                                                    <th class='text-center' style='width: 30px;'></th>
                                                    <th class='text-center' style='width: 30px;'></th>
                                                `); // <input id='check_all' type='checkbox'>

                            for (var i = 0; i <data.length; i++) {
                                var row = $("<tr></tr>");
                                row.append(`<td style='background: #efefef'>
                                                    <b> 
                                                        <span> ${data[i].get_customer.get_grup.nama_grup}</span> 
                                                    </b>
                                            </td>`);
                                row.append(`<td style='background: #efefef'>
                                                    <b> 
                                                        <span>► ${data[i].get_customer.nama}</span> 
                                                    </b>
                                            </td>`);
                                row.append(`<td> ${data[i].get_j_o.no_bl} </td>`);
                                row.append(`<td> <b>${data[i].get_j_o.kapal} / ${data[i].get_j_o.voyage}</b> </td>`);
                                row.append(`<td> 
                                                ${ data[i].total_operasional.toLocaleString() } 
                                                <input type="text" class="uang numaja form-control" id='total_operasional_${data[i].id}' name='data[${data[i].id}][total_operasional]' value='${data[i].total_operasional == null? 0:data[i].total_operasional}' />
                                            </td>`); 
                                row.append(`<td> 
                                                <input type="text" class="uang numaja dicairkan form-control" id='open_${data[i].id}' idOprs="${data[i].id}" name='data[${data[i].id}][dicairkan]' value='${data[i].total_dicairkan == null? 0:data[i].total_dicairkan.toLocaleString()}' />
                                                <input type="text" class="uang numaja form-control" id='hidden_open_${data[i].id}' name='data[${data[i].id}][dicairkan_old]' value='${data[i].total_dicairkan == null? 0:data[i].total_dicairkan}' />
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input class="form-control" name='data[${data[i].id}][catatan]' id="catatan_${data[i].id}" value="${data[i].catatan != null? data[i].catatan:''}" type="text"/> 
                                                <input class="form-control" id="hidden_catatan_${data[i].id}" value="${data[i].catatan != null? data[i].catatan:''}" type="text" readonly /> 
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <button type="button" class="btn btn-sm btn-danger delete" value="${data[i].id}"> <span class="fa fa-trash-alt"></span> </button>
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input type='checkbox' class="check_per_item" name="data[${data[i].id}][check]" value="${data[i].id}">
                                            </td>`);
    
                                $("#tbodyId").append(row);
                            }

                            new DataTable('#rowGroup', {
                                order: [
                                    [0, 'asc'], // 0 = grup
                                    [1, 'asc'] // 1 = customer
                                ],
                                rowGroup: {
                                    dataSrc: [0, 1] // di order grup dulu, baru customer
                                },
                                columnDefs: [
                                    {
                                        targets: [0, 1], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                                        visible: false
                                    },
                                    {
                                        targets: [-1, -2],
                                        orderable: false, // matiin sortir kolom centang
                                    },
                                ],
                            });

                        }else{
                            $("thead tr").append(`<th>Grup<th> <th>Tujuan</th><th>Keterangan</th>`);
                            $("thead tr").append("<th>Total</th>");
                            $("thead tr").append(`  <th>Dicairkan</th>
                                                    <th>Catatan</th>
                                                    <th class='text-center' style='width: 30px;'></th>
                                                    <th class='text-center' style='width: 30px;'></th>
                                                `); // <input id='check_all' type='checkbox'>
                            // if(item == 'OPERASIONAL' || item == 'TALLY' || item == 'SEAL PELAYARAN'){
                            // }else{
                                for (var i = 0; i <data.length; i++) {
                                    var row = $("<tr></tr>");
                                    row.append(`<td style='background: #efefef'>
                                                        <b> 
                                                            <span> ${data[i].get_sewa.get_tujuan.get_grup.nama_grup}</span> 
                                                        </b>
                                                </td>`);
                                    row.append(`<td style='background: #efefef'>
                                                        <b> 
                                                            <span>► ${data[i].get_sewa.get_customer.nama}</span> 
                                                        </b>
                                                </td>`);
                                    row.append(`<td> ${data[i].get_sewa.nama_tujuan} ${ data[i].get_sewa.no_polisi != null? ' / '+data[i].get_sewa.no_polisi:'' } / ${data[i].get_sewa.nama_panggilan? data[i].get_sewa.nama_panggilan:'DRIVER REKANAN '+ data[i].get_sewa.get_customer.nama} </td>`);
                                    row.append(`<td> ${data[i].get_sewa.tipe_kontainer != null? data[i].get_sewa.tipe_kontainer+'"':''}<b> ${data[i].get_sewa.jenis_order} </b> ${ data[i].get_sewa.pick_up == null? '':'('+data[i].get_sewa.pick_up+')'} </td>`);
                                    row.append(`<td> 
                                                    ${ data[i].total_operasional.toLocaleString() } 
                                                    <input type="text" class="uang numaja form-control" id='total_operasional_${data[i].id}' name='data[${data[i].id}][total_operasional]' value='${data[i].total_operasional == null? 0:data[i].total_operasional}' />
                                                </td>`); 
                                    var driver = (data[i].get_sewa.get_supplier != undefined)? data[i].get_sewa.get_supplier.nama:data[i].get_sewa.nama_driver;
                                    var keterangan = data[i].nama_tujuan+'/'+data[i].no_polisi+'/'+driver;
                                    var tambahanUJ = '';
                                    row.append(`<td> 
                                                    <input type="text" class="uang numaja dicairkan form-control" id='open_${data[i].id}' idOprs="${data[i].id}" name='data[${data[i].id}][dicairkan]' value='${data[i].total_dicairkan == null? 0:data[i].total_dicairkan.toLocaleString()}' readonly />
                                                    <input type="text" class="uang numaja form-control" id='hidden_open_${data[i].id}' name='data[${data[i].id}][dicairkan_old]' value='${data[i].total_dicairkan == null? 0:data[i].total_dicairkan}' />
                                                </td>`);
        
                                    row.append(`<td class='text-center'> 
                                                    <input class="form-control" name='data[${data[i].id}][catatan]' id="catatan_${data[i].id}" value="${data[i].catatan != null? data[i].catatan:''}" type="text" readonly /> 
                                                    <input class="form-control" id="hidden_catatan_${data[i].id}" value="${data[i].catatan != null? data[i].catatan:''}" type="text" readonly /> 
                                                </td>`);
                                    row.append(`<td class='text-center'> 
                                                    <button type="button" class="btn btn-sm btn-danger delete" value="${data[i].id}"> <span class="fa fa-trash-alt"></span> </button>
                                                </td>`);
                                    row.append(`<td class='text-center'> 
                                                    <input type='checkbox' class="check_per_item" name="data[${data[i].id}][check]" value="${data[i].id}">
                                                </td>`);
        
                                    $("#tbodyId").append(row);
                                }
                            // }
                                
                            new DataTable('#rowGroup', {
                                order: [
                                    [0, 'asc'], // 0 = grup
                                    [1, 'asc'] // 1 = customer
                                ],
                                rowGroup: {
                                    dataSrc: [0, 1] // di order grup dulu, baru customer
                                },
                                columnDefs: [
                                    {
                                        targets: [0, 1], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                                        visible: false
                                    },
                                    {
                                        targets: [-1, -2],
                                        orderable: false, // matiin sortir kolom centang
                                    },
                                ],
                            });
                        }
                    }else{
                        console.log('else');
                        $("thead tr").append(`<th>Revisi Biaya Operasional</th>`);
                        // $("#rowGroup").dataTable();
                        $('#rowGroup').DataTable().draw();

                        // $('#rowGroup').DataTable().clear().draw();
                    }

                },error: function (xhr, status, error) {
                    if ( xhr.responseJSON.result == 'error') {
                        console.log("Error:", xhr.responseJSON.message);
                        console.log("XHR status:", status);
                        console.log("Error:", error);
                        console.log("Response:", xhr.responseJSON);
                    } else {
                        toastr.error("Terjadi kesalahan saat menerima data. " + error);
                    }
                }
            });
        }

        $(document).on('click', '.check_per_item', function(e){
            let id = this.value;
            console.log('this.checked', this.checked);
            
            if(this.checked == true){
                $('#open_'+id).prop('readonly', false);
                $('#catatan_'+id).prop('readonly', false);

            }else{
                $('#open_'+id).prop('readonly', true);
                $('#catatan_'+id).prop('readonly', true);

                $('#open_'+id).val( moneyMask($('#hidden_open_'+id).val()) );
                $('#catatan_'+id).val( $('#hidden_catatan_'+id).val() );
            }
        });

        $(document).on('click', '.delete', function(e){
            $('#key').val('');
            $('#modal_item').val('');
            $('#key').val(this.value);
            $('#modal_item').val( $('#item').val() );
            $('#modal_delete').modal('show');
        });

        $(document).on('keyup', '.dicairkan', function(){
            var idOprs = $(this).attr('idOprs');
            console.log('idOprs', idOprs);
            var inputed = parseFloat(this.value.replace(/,/g, ''));
            var max = $('#total_operasional_'+idOprs).val();

            if (inputed > max ) {
                $('#open_'+idOprs).val(parseFloat(max).toLocaleString()); 
            }
        });

        function caps(){
            $("input").focusout(function () {
                this.value = this.value.toLocaleUpperCase();
            });
        }
    });
</script>
@endsection