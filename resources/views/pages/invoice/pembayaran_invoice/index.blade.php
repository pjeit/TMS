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
    <div class="card radiusSendiri">
        <div class="card-header">
            <button type="submit" class="btn btn-primary btn-responsive radiusSendiri" id="bayarInvoice">
                <i class="fa fa-credit-card"></i> Bayar
            </button>
        </div>
        <div class="card-body">
            {{-- <div class="col-sm-12 col-md-4 col-lg-4 ">
                <div class="form-group">
                    <label for="">Status Invoice</label> 
                    <select class="form-control selectpicker" required name="status" id="status" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
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
                                <tr >
                                    <td>{{ $item->nama_grup }}</td>
                                    <td>{{ $item->nama_cust }} 
                                        @if ($item->total_sisa != 0)
                                            <span class="float-right">
                                                <input type="checkbox" style="margin-right: 0.9rem;" class="customer_centang" id_customer="{{ $item->billing_to }}" id_customer_grup="{{ $item->id_grup }}">
                                            </span> 
                                        @endif
                                    </td>
                                    <td>{{ $item->no_invoice }}
                                        <input type="hidden" id="id_{{ $item->id }}" value="{{ $item->id }}" >
                                        <input type="hidden" id="invoice_{{ $item->id }}" value="{{ $item->no_invoice }}" >
                                        <input type="hidden" id="bukti_potong_{{ $item->id }}" value="{{ $item->no_bukti_potong }}" >
                                        <input type="hidden" id="catatan_{{ $item->id }}" value="{{ $item->catatan }}" >
                                    </td>
                                    <td>{{ date("d-M-Y", strtotime($item->tgl_invoice)) }}</td>
                                    <td>{{ date("d-M-Y", strtotime($item->jatuh_tempo)) }}</td>
                                    <td class="float-right">{{ number_format($item->total_sisa) }}
                                    <td>{{ $item->catatan }}
                                    </td>
                                    <td style="text-align: right;"> 
                                        @if ($item->total_sisa != 0)
                                            <input type="checkbox" name="idInvoice[]" class="sewa_centang float-right" custId="{{ $item->billing_to }}" grupId="{{ $item->id_grup }}" value="{{ $item->id }}">
                                        @else
                                            <btn class="btn btn-primary btn-sm radiusSendiri" id='input_bukti' idInvoice="{{ $item->id }}"> <span class="fa fa-sticky-note mr-1"></span> Input Bukti Potong</btn>
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

{{-- modal edit --}}
<div class="modal fade" id="modal_detail" tabindex='-1'>
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Update Resi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
        </div>
        <form action="{{ route('pembayaran_invoice.updateResi') }}" method="POST" enctype="multipart/form-data" id="updResi">
            <div class="modal-body">
                <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id --}}
                <div class='row'>
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="sewa">No. Invoice</label>
                                <input type="text" class="form-control" id="modal_no_invoice" name="no_invoice" readonly> 
                                <input type="hidden" class="form-control" id="modal_id_invoice" name="id_invoice" readonly> 
                                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                            </div>   

                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">No. Resi</label>
                                <input  type="text" class="form-control" id="modal_resi" name="resi" > 
                            </div>

                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Jatuh Tempo</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" mplete="off"  class="form-control date" id="modal_jatuh_tempo" name="jatuh_tempo" > 
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Catatan</label>
                                <input  type="text" class="form-control" id="modal_catatan" name="catatan" > 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-sm btn-success save_detail" id="simpanResi" style='width:85px'>OK</button> 
            </div>
        </form>

    </div>
    <!-- /.modal-content -->
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
            console.log('idInvoice', idInvoice);
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
            }).datepicker("setDate", dateMask(jatuh_tempo));

            $('#modal_detail').modal('show');
        });
        
        // $('#simpanResi').click(function () {
        //     var id = $('#modal_id_invoice').val();
        //     var noInvoice = $('#modal_no_invoice').val();
        //     var resi = $('#modal_resi').val();
        //     var jatuhTempo = $('#modal_jatuh_tempo').val();
        //     var catatan = $('#modal_catatan').val();

        //     var token = $('meta[name="csrf-token"]').attr('content');
        //     var baseUrl = "{{ asset('') }}";
        //     var url = '/pembayaran_invoice/update_resi/'+id;
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });

        //     var baseUrl = "{{ asset('') }}";
        //     var url = `${baseUrl}pembayaran_invoice/update_resi/` + id;
        //     $.ajax({
        //         url: url,
        //         method: 'POST',
        //         data: {
        //             id: id,
        //             noInvoice: noInvoice,
        //             resi: resi,
        //             jatuhTempo: jatuhTempo,
        //             catatan: catatan,
        //             _token: $('meta[name="csrf-token"]').attr('content'),
        //         },
        //         success: function (response) {
        //             $('#modal-loading').modal('show');
        //             console.log(response);
        //             window.location.href = '{{ route("pembayaran_invoice.bayar") }}';
        //         },
        //         error: function (xhr, status, error) {
        //             console.error('Error:', error);
        //         }
        //     });

        //     // $.ajax({
        //     //     type: 'PUT', // Use POST method
        //     //     header:{
        //     //         'X-CSRF-TOKEN': token
        //     //     },
        //     //     url: url, // Replace with the actual URL and someId
        //     //     data: {
        //     //         _method: 'POST', // Specify the HTTP method as PUT
        //     //         id: id,
        //     //         noInvoice: noInvoice,
        //     //         resi: resi,
        //     //         jatuhTempo: jatuhTempo,
        //     //         catatan: catatan,
        //     //         _token: token,
        //     //     },
        //     //     success: function (response) {
        //     //         console.log('Response: ' + response.status);
        //     //         if(response.status == 'success'){
        //     //             $('#modal_detail').modal('hide');

        //     //             const Toast = Swal.mixin({
        //     //                 toast: true,
        //     //                 position: 'top',
        //     //                 timer: 2500,
        //     //                 showConfirmButton: false,
        //     //                 timerProgressBar: true,
        //     //                 didOpen: (toast) => {
        //     //                     toast.addEventListener('mouseenter', Swal.stopTimer)
        //     //                     toast.addEventListener('mouseleave', Swal.resumeTimer)
        //     //                 }
        //     //             })
    
        //     //             Toast.fire({
        //     //                 icon: 'success',
        //     //                 title: 'Data tersimpan'
        //     //             })

        //     //             setTimeout(function() {
        //     //                 location.reload(true);
        //     //             }, 700); // 500 milliseconds (0.5 seconds)
        //     //         }else{
        //     //             $('#modal_detail').modal('hide');
        //     //             const Toast = Swal.mixin({
        //     //                 toast: true,
        //     //                 position: 'top',
        //     //                 timer: 2500,
        //     //                 showConfirmButton: false,
        //     //                 timerProgressBar: true,
        //     //                 didOpen: (toast) => {
        //     //                     toast.addEventListener('mouseenter', Swal.stopTimer)
        //     //                     toast.addEventListener('mouseleave', Swal.resumeTimer)
        //     //                 }
        //     //             })

        //     //             Toast.fire({
        //     //                 icon: 'danger',
        //     //                 title: 'Terjadi kesalahan'
        //     //             })
        //     //         }
        //     //     },
        //     //     error: function (xhr, status, error) {
        //     //         console.log('response: ' +error);
        //     //         $('#modal_detail').modal('hide');
        //     //         const Toast = Swal.mixin({
        //     //             toast: true,
        //     //             position: 'top',
        //     //             timer: 2500,
        //     //             showConfirmButton: false,
        //     //             timerProgressBar: true,
        //     //             didOpen: (toast) => {
        //     //                 toast.addEventListener('mouseenter', Swal.stopTimer)
        //     //                 toast.addEventListener('mouseleave', Swal.resumeTimer)
        //     //             }
        //     //         })

        //     //         Toast.fire({
        //     //             icon: 'danger',
        //     //             title: 'Terjadi kesalahan'
        //     //         })
        //     //     }
        //     // });
        // });

        $('body').on('click','.customer_centang',function() {
            var idCustParent= $(this);
        
            $('.customer_centang[type=checkbox]').each(function(idx) {
                var id_percust_semua = $(this);
                
                // if(id_percust_semua.attr('id_customer_grup')==idCustParent.attr('id_customer_grup'))
                // {
                    if(id_percust_semua.attr('id_customer')==idCustParent.attr('id_customer'))
                    {
                        if (idCustParent.is(":checked")) {
                        id_percust_semua.prop('checked', true);
                        } else if (!idCustParent.is(":checked")) {
                            id_percust_semua.prop('checked', false);
                        }
                    }
                // }
                else
                {
                    id_percust_semua.prop('checked', false);
                }
            });
            
            
            $('.sewa_centang[type=checkbox]').each(function(idx) {
                var id_cust_sewa = $(this);
                // if(id_cust_sewa.attr('grupId')==idCustParent.attr('id_customer_grup'))
                // {
                    if(id_cust_sewa.attr('custId')==idCustParent.attr('id_customer'))
                    {
                        if (idCustParent.is(":checked")) {
                            id_cust_sewa.prop('checked', true);
    
                        } else if (!idCustParent.is(":checked")) {
                            
                            id_cust_sewa.prop('checked', false);
                        }
                    }
                // }
                else
                {
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

        // $(document).on('change', '#status', function(e) {  
        //     showTable(this.value)
		// }); 

        function showTable(status){
            $.ajax({
                method: 'GET',
                url: `pembayaran_invoice/loadData/${status}`,
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
                                    <th width="5px;"></th>
                                </tr>
                    `;

                var thead = document.getElementById("thead");

                if (thead) {
                    thead.innerHTML = newHeader;
                }

                    for (var i = 0; i < data.length; i++) {
                        var row = $("<tr></tr>");
                        let btn_edit = '';
                        if(data[i].total_sisa == data[i].total_tagihan){
                            btn_edit = `<a href="pembayaran_invoice/${data[i].id}/edit" class="dropdown-item">
                                                <span class="fas fa-pencil-alt mr-3"></span> Edit Invoice
                                            </a>`;
                        }

                        row.append(`<td>${data[i].nama_grup}</td>`);
                        row.append(`<td>• ${data[i].nama_cust}</td>`);
                        row.append(`<td>${data[i].no_invoice}
                                        <input type="hidden" placeholder='id' id="id_${data[i].id}" value="${data[i].id}" >
                                        <input type="hidden" placeholder='noInvoice' id="no_invoice_${data[i].id}" value="${data[i].no_invoice}" >
                                        <input type="hidden" placeholder='jatuhTempo' id="jatuh_tempo_${data[i].id}" value="${data[i].jatuh_tempo}" >
                                        <input type="hidden" placeholder='resi' id="resi_${data[i].id}" value="${data[i].resi}" >
                                        <input type="hidden" placeholder='catatan' id="catatan_${data[i].id}" value="${data[i].catatan}" >
                                    </td>`);
                        row.append(`<td>${dateMask(data[i].tgl_invoice)}</td>`);
                        row.append(`<td>${dateMask(data[i].jatuh_tempo)}</td>`);
                        row.append(`<td> ${ data[i].total_sisa.toLocaleString()}</td>`);
                        row.append(`<td>${data[i].catatan == null? '':data[i].catatan}</td>`);
                        var jenis =  `<input type="checkbox" name="idInvoice[]" class="sewa_centang float-right" custId="${data[i].billing_to}" grupId="${data[i].id_grup}" value="${data[i].id}">`;
                        row.append(`<td class='text-center' style="text-align:center">
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        
                                        `+
                                            btn_edit
                                        +`
                                        <button class="dropdown-item update_resi" value="${data[i].id}">
                                            <span class="fas fa-sticky-note mr-3"></span> Update Resi
                                        </button>
                                    </div>
                                </div>
                            </td>`);
                        row.append(`<td class='text-center' style="text-align:center">${jenis}</td>`);
                        $("#hasil").append(row);
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
                                targets: [7, 8],
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
