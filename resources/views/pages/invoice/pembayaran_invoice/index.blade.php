@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('content')
@include('sweetalert::alert')
<meta name="csrf-token" content="{{ csrf_token() }}" />

<style>

</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <div class="">
                          <button type="submit" class="btn btn-primary btn-responsive radiusSendiri" id="bayarInvoice">
                             <i class="fa fa-credit-card"></i> Bayar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-sm-12 col-md-3 col-lg-3 ">
                        <div class="form-group">
                            <label for="">Status</label> 
                            <select class="form-control selectpicker" required name="status_tl" id="status_tl" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                <option value="BELUM LUNAS">Belum Lunas</option>
                                <option value="LUNAS">Lunas</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <table id="tabelInvoice" class="table table-bordered" width='100%'>
                        <thead>
                            <tr style="margin-right: 0px;">
                                <th >Grup</th>
                                <th >Customer</th>
                                <th>No. Invoice</th>
                                <th width='100'>Tgl Invoice</th>
                                <th width='100'>Jatuh Tempo</th>
                                <th>Sisa Tagihan</th>
                                <th>Catatan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="hasil">
                            @if (isset($data))
                                @foreach($data as $item)
                                    <tr >
                                        <td>{{ $item->nama_grup }}</td>
                                        {{-- <td>{{ $item->nama_grup }} <span class="float-right"><input type="checkbox" style="margin-right: 0.9rem;" class="grup_centang" id_grup="{{ $item->id_grup }}"></span> </td> --}}
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
                                                <btn class="btn btn-primary btn-sm radiusSendiri" id='input_bukti' idInvoice="{{ $item->id }}"> <span class="fa fa-inbox mr-2"></span> Input Bukti Potong</btn>
                                            @endif
                                            <input type="hidden" name="idGrup[]" id="idGrup">
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
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
            <h5 class="modal-title">Detail Invoice</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id='form_add_detail'>
                    <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}
    
                    <div class='row'>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="sewa">No. Invoice</label>
                                    <input  type="text" class="form-control" id="modal_no_invoice" name="no_invoice" readonly> 
                                    <input  type="hidden" class="form-control" id="modal_id_invoice" name="id_invoice" readonly> 
                                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                </div>   
    
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">No. Bukti Potong</label>
                                    <input  type="text" class="form-control" id="modal_no_bukti_potong" name="no_bukti_potong" > 
                                </div>
    
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Catatan</label>
                                    <input  type="text" class="form-control" id="modal_catatan" name="catatan" > 
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                <button type="button" class="btn btn-sm btn-success save_detail" id="simpanBuktiPotong" style='width:85px'>OK</button> 
            </div>
        </div>
        <!-- /.modal-content -->
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
            
            $('body').on('click','.customer_centang',function()
            {
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
            $('body').on('click','.sewa_centang',function()
            {
                var sewa_cekbox= $(this);
             
                $('.customer_centang[type=checkbox]').each(function(idx) {
                    var id_percust_semua = $(this);
                    
                    // if(id_percust_semua.attr('id_customer_grup')==sewa_cekbox.attr('grupId'))
                    // {
                        if(id_percust_semua.attr('id_customer')==sewa_cekbox.attr('custId'))
                        {
                            if (id_percust_semua.is(":checked")) {
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
                    // if(id_cust_sewa.attr('grupId')!=sewa_cekbox.attr('grupId'))
                    // {
                        if(id_cust_sewa.attr('custId')!=sewa_cekbox.attr('custId'))
                        {
                            if (id_cust_sewa.is(":checked")) {
                                id_cust_sewa.prop('checked', false);
        
                            } 
                            
                        }
                        // else
                        // {
                        //     id_cust_sewa.prop('checked', false);
                        // }
                    // }
                    
                });
            });
            $('body').on('click','#bayarInvoice',function()
            {
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
            
                }
                else
                {

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

            $('body').on('click', '#input_bukti', function () {
                clear();
                var idInvoice = $(this).attr('idInvoice');
                
                $('#modal_id_invoice').val( $('#id_'+idInvoice).val() );
                $('#modal_no_invoice').val( $('#invoice_'+idInvoice).val() );
                $('#modal_no_bukti_potong').val( $('#bukti_potong_'+idInvoice).val() );
                $('#modal_catatan').val( $('#catatan_'+idInvoice).val() );
                
                $('#modal_detail').modal('show');
            });

            $('#simpanBuktiPotong').click(function () {
                var id = $('#modal_id_invoice').val();
                var noInvoice = $('#modal_no_invoice').val();
                var noBuktiPotong = $('#modal_no_bukti_potong').val();
                var catatan = $('#modal_catatan').val();
                var token = $('meta[name="csrf-token"]').attr('content');
                console.log('token', token);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST', // Use POST method
                    header:{
                      'X-CSRF-TOKEN': token
                    },
                    url: '/pembayaran_invoice/update_bukti_potong/' + id, // Replace with the actual URL and someId
                    data: {
                        _method: 'POST', // Specify the HTTP method as PUT
                        no_invoice: noInvoice,
                        no_bukti_potong: noBuktiPotong,
                        catatan: catatan,
                        _token: token,
                    },
                    success: function (response) {
                        // Handle success response
                        console.log(response);
                    },
                    error: function (xhr, status, error) {
                        // Handle error
                        console.log(error);
                    }
                });


            });


            function clear(){
                $('#modal_no_invoice').val('');
                $('#modal_no_bukti_potong').val('');
                $('#modal_catatan').val('');
            }
            
        new DataTable('#tabelInvoice', {
            order: [
                [0, 'asc'],
                [1, 'asc']
            ],
            rowGroup: {
                dataSrc: [0, 1]
            },
            columnDefs: [
                {
                    targets: [0, 1],
                    visible: false
                },
                {
                    "orderable": false,
                    "targets": [0,1,2,3,4,5,6,7]
                }
        
            ],
        });

    });
</script>
@endsection
