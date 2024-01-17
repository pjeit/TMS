@extends('layouts.home_master')

@if(session()->has('message'))
<div class="alert alert-success alert-dismissible">
    {{ session()->get('message') }}
</div>
@endif

@section('content')
@include('sweetalert::alert')
<style>

</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">

    {{-- sticky header --}}
    <div class="sticky-top radiusSendiri" style="margin-bottom: -15px;">
        <div class="card radiusSendiri radiusSendiri" style="">
            <div class="card-header " style="border-bottom: none;">
                <button type="submit" class="btn btn-primary btn-responsive radiusSendiri" id="bayarInvoice">
                    <i class="fa fa-credit-card"></i> Bayar
                </button>
            </div>
        </div>
    </div>
    <div class="card radiusSendiri">
        <div class="card-body">
            {{-- <div class="col-sm-12 col-md-4 col-lg-4 ">
                <div class="form-group">
                    <label for="">Status Invoice</label>
                    <select class="form-control selectpicker" required name="status" id="status" data-live-search="true"
                        data-show-subtext="true" data-placement="bottom">
                        <option value="BELUM LUNAS" selected>Belum Dibayar</option>
                        <option value="LUNAS">Riwayat Pembayaran (Tanpa bukti potong)</option>
                    </select>
                </div>
            </div>
            <hr> --}}
            <div style="overflow: auto;">
                <table id="tabelInvoice" class="table table-bordered" width='100%'>
                    <thead id="thead">
                        <tr style="margin-right: 0px;">
                            <th>Grup</th>
                            <th>Customer</th>
                            <th>No. Invoice</th>
                            <th>Tgl Invoice</th>
                            <th>Jatuh Tempo</th>
                            <th>Sisa Tagihan</th>
                            <th>Catatan</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="hasil">

                    </tbody>
                    {{-- <tbody id="hasil">
                        @if (isset($data))
                        @foreach($data as $item)
                        <tr>
                            <td>{{ $item->nama_grup }}</td>
                            <td>{{ $item->nama_cust }}
                                @if ($item->total_sisa != 0)
                                <span class="float-right">
                                    <input type="checkbox" style="margin-right: 0.9rem;" class="customer_centang"
                                        id_customer="{{ $item->billing_to }}" id_customer_grup="{{ $item->id_grup }}">
                                </span>
                                @endif
                            </td>
                            <td>{{ $item->no_invoice }}
                                <input type="hidden" id="id_{{ $item->id }}" value="{{ $item->id }}">
                                <input type="hidden" id="invoice_{{ $item->id }}" value="{{ $item->no_invoice }}">
                                <input type="hidden" id="bukti_potong_{{ $item->id }}"
                                    value="{{ $item->no_bukti_potong }}">
                                <input type="hidden" id="catatan_{{ $item->id }}" value="{{ $item->catatan }}">
                            </td>
                            <td>{{ date("d-M-Y", strtotime($item->tgl_invoice)) }}</td>
                            <td>{{ date("d-M-Y", strtotime($item->jatuh_tempo)) }}</td>
                            <td class="float-right">{{ number_format($item->total_sisa) }}
                            <td>{{ $item->catatan }}
                            </td>
                            <td style="text-align: right;">
                                @if ($item->total_sisa != 0)
                                <input type="checkbox" name="idInvoice[]" class="sewa_centang float-right"
                                    custId="{{ $item->billing_to }}" grupId="{{ $item->id_grup }}"
                                    value="{{ $item->id }}">
                                @else
                                <btn class="btn btn-primary btn-sm radiusSendiri" id='input_bukti'
                                    idInvoice="{{ $item->id }}"> <span class="fa fa-sticky-note mr-1"></span> Input
                                    Bukti Potong</btn>
                                @endif
                                <input type="hidden" name="idGrup[]" id="idGrup">
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody> --}}
                </table>
            </div>
        </div>
    </div>
</div>

{{-- modal loading --}}
<div class="modal" id="modal-loading" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="cv-spinner">
                    <span class="loader"></span>
                </div>
                <div>Harap Tunggu Sistem Sedang Memproses....</div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('body').on('click', '.update_resi', function () {
            clear();
            var idInvoice = this.value;
            var tambah_waktu = $('#ketentuan_bayar_'+idInvoice).val() != 'null'? parseFloat($('#ketentuan_bayar_'+idInvoice).val()):0;
            var no_invoice = $('#no_invoice_'+idInvoice).val() != 'null'? $('#no_invoice_'+idInvoice).val():'';
            var resi = $('#resi_'+idInvoice).val() != 'null'? $('#resi_'+idInvoice).val():'';
            var jatuh_tempo = $('#jatuh_tempo_'+idInvoice).val() != 'null'? $('#jatuh_tempo_'+idInvoice).val():'';
            var catatan = $('#catatan_'+idInvoice).val() != 'null'? $('#catatan_'+idInvoice).val():'';
            $('#modal_no_invoice').val( no_invoice );
            $('#modal_resi').val( resi );
            $('#modal_catatan').val( catatan );
            $('#modal_id_invoice').val( idInvoice );
            $('#modal_jatuh_tempo').datepicker({
                autoclose: true,
                todayHighlight: true,
                language: 'en',
                orientation: 'bottom auto',
            }).datepicker("setDate", dateMask(jatuh_tempo+tambah_waktu));

            $('#modal_detail').modal('show');
        });
        
        $('body').on('click','.customer_centang',function() {
            var idCustParent= $(this);
        
            $('.customer_centang[type=checkbox]').each(function(idx) {
                var id_percust_semua = $(this);
                
                if(id_percust_semua.attr('id_customer')==idCustParent.attr('id_customer')){
                    if (idCustParent.is(":checked")) {
                        id_percust_semua.prop('checked', true);
                    } else if (!idCustParent.is(":checked")) {
                        id_percust_semua.prop('checked', false);
                    }
                }else{
                    id_percust_semua.prop('checked', false);
                }
            });
            
            
            $('.sewa_centang[type=checkbox]').each(function(idx) {
                var id_cust_sewa = $(this);
                if(id_cust_sewa.attr('custId')==idCustParent.attr('id_customer')){
                    if (idCustParent.is(":checked")) {
                        id_cust_sewa.prop('checked', true);

                    } else if (!idCustParent.is(":checked")) {
                        
                        id_cust_sewa.prop('checked', false);
                    }
                }else{
                    id_cust_sewa.prop('checked', false);
                }
            });
        });

        $('body').on('click','.sewa_centang',function() {
            var sewa_cekbox= $(this);
            
            $('.customer_centang[type=checkbox]').each(function(idx) {
                var id_percust_semua = $(this);
                
                if(id_percust_semua.attr('id_customer')==sewa_cekbox.attr('custId'))
                {
                    if (id_percust_semua.is(":checked")) {
                    id_percust_semua.prop('checked', false);
                    } 
                }else                {
                    id_percust_semua.prop('checked', false);
                }
            });
            
            
            $('.sewa_centang[type=checkbox]').each(function(idx) {
                var id_cust_sewa = $(this);
                if(id_cust_sewa.attr('custId')!=sewa_cekbox.attr('custId'))
                {
                    if (id_cust_sewa.is(":checked")) {
                        id_cust_sewa.prop('checked', false);
                    } 
                }
        });
        });

        $('body').on('click','#bayarInvoice',function() {
            var selectedValues = [];
            var custId = '';
            var grupId = '';
            $(".sewa_centang[type=checkbox]:checked").each(function() {
                selectedValues.push($(this).val());
                custId = ($(this).attr('custId'));
                grupId = ($(this).attr('grupId'));
            });
            if (selectedValues.length === 0) {
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
                        icon: 'error',
                        title: 'Harap pilih invoice yang ingin dibayar!'
                    })
                    event.preventDefault();
        
            }else{

                var baseUrl = "{{ asset('') }}";
                $.ajax({
                    url: `${baseUrl}pembayaran_invoice/set_invoice_id`, 
                    method: 'POST', 
                    data: { 
                        idInvoice: selectedValues ,
                        idCust: custId,
                        idGrup: grupId,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(response) {
                        $('#modal-loading').modal('show');
                        console.log(response);
                        window.location.href = '{{ route("pembayaran_invoice.bayar") }}';
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        })

        // var status = $('#status').val();
        showTable("BELUM LUNAS");

        function showTable(status){
            var baseUrl = "{{ asset('') }}";
            var url = baseUrl+`pembayaran_invoice/loadData/${status}`;

            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    var table = $('#tabelInvoice').DataTable();
                    $('#hasil').empty();
                    table.clear().destroy();
                    var baseUrl = "{{ asset('') }}";
                    $("#loading-spinner").hide();
                    var data = response;
                    console.log('data', data);

                    if(status == 'BELUM LUNAS'){
                        var tagihBayar = `<th>Sisa Tagihan</th>`;
                    }else{
                        var tagihBayar = `<th>Total Dibayarkan</th>`;
                    }
                    var newHeader = `
                                <tr style="margin-right: 0px;">
                                    <th>Grup</th>
                                    <th>Customer</th>
                                    <th>No. Invoice</th>
                                    <th width='100'>Tgl Invoice</th>
                                    <th width='100'>Jatuh Tempo</th>
                                    `+
                                    tagihBayar
                                    +`
                                    <th >Catatan</th>
                                    
                                    <th width="5px;"></th>
                                </tr>
                    `;

                var thead = document.getElementById("thead");

                if (thead) {
                    thead.innerHTML = newHeader;
                }

                    for (var i = 0; i < data.length; i++) {
                        if(data[i].total_sisa > 0){
                            var row = $("<tr></tr>");
                            let btn_edit = '';
                            if(data[i].total_sisa == data[i].total_tagihan){
                                btn_edit = `<a href="pembayaran_invoice/${data[i].id}/edit" class="dropdown-item">
                                                    <span class="fas fa-pencil-alt mr-3"></span> Edit Invoice
                                                </a>`;
                            }
    
                            row.append(`<td>${data[i].nama_grup}</td>`);
                            row.append(`<td>• ${data[i].nama_cust}</td>`);
                            row.append(`<td>${data[i].no_invoice} </td>`);
                            row.append(`<td>${dateMask(data[i].tgl_invoice)}</td>`);
                            row.append(`<td>${dateMask(data[i].jatuh_tempo)}</td>`);
                            row.append(`<td> ${ data[i].total_sisa.toLocaleString()}</td>`);
                            row.append(`<td>${data[i].catatan == null? '':data[i].catatan}</td>`);
                            var jenis =  `<input type="checkbox" name="idInvoice[]" class="sewa_centang float-right" custId="${data[i].billing_to}" grupId="${data[i].id_grup}" value="${data[i].id}">`;
                            // row.append(`<td class='text-center' style="text-align:center">
                            //         <div class="btn-group dropleft">
                            //             <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            //                 <i class="fa fa-list"></i>
                            //             </button>
                            //             @can('EDIT_PEMBAYARAN_INVOICE')
                            //             <div class="dropdown-menu" >
                            //                 `+
                            //                     btn_edit
                            //                 +`
                            //             </div>
                            //             @endcan
                            //         </div>
                            //     </td>`);
                            row.append(`<td class='text-center' style="text-align:center">${jenis}</td>`);
                            $("#hasil").append(row);
                        }
                    }
                    new DataTable('#tabelInvoice', {
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
                                targets: [6, 7],
                                orderable: false, // matiin sortir kolom centang
                            },
                        ],
                    });
                },error: function (xhr, status, error) {
                    $("#loading-spinner").hide();
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
        };

        function clear() {
            $('#modal_id_invoice').val('');
            $('#modal_no_invoice').val('');
            $('#modal_resi').val('');
            $('#modal_jatuh_tempo').val('');
            $('#modal_catatan').val('');
        }

        $("#modal_jatuh_tempo" ).datepicker({
            format: 'dd-M-yyyy'
        });
       
    });
</script>
@endsection