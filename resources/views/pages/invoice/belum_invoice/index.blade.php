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
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <div class="">
                        <a href="{{route('invoice.create')}}" class="btn btn-primary btn-responsive radiusSendiri"  id="sewaAdd">
                            <i class="fa fa-plus-circle" aria-hidden="true"> </i> Buat Invoice
                        </a> 
                          <button type="button" class="btn btn-primary btn-responsive radiusSendiri" id="cobaSewa">
                             <i class="fa fa-plus-circle" aria-hidden="true"> </i>Coba sewa
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tabelInvoice" class="table table-bordered table-striped" width='100%'>
                        <thead>
                            <tr>
                                <th>Grup</th>
                                <th>Customer</th>
                                <th>No. Polisi Kendaraan</th>
                                <th>No. Sewa</th>
                                <th>Tgl Berangkat</th>
                                <th>Tujuan</th>
                                <th>Driver</th>
                                <th></th>
                                {{-- <th>Status</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($dataSewa))
                                @foreach($dataSewa as $item)
                                    <tr>
                                        <td >{{ $item->nama_grup }} <span class="float-right"><input type="checkbox" style="margin-right: 6px;" class="grup_centang" id_grup="{{ $item->id_grup }}"></span> </td>
                                        <td >{{ $item->nama_cust }} <span class="float-right"><input type="checkbox" style="margin-right: 6px;" class="customer_centang" id_customer="{{ $item->id_customer }}" id_customer_grup="{{ $item->id_grup }}"></span> </td>
                                        <td>{{ $item->no_polisi }}</td>
                                        <td>{{ $item->no_sewa }}</td>
                                        <td>{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}</td>
                                        <td>{{ $item->nama_tujuan }}</td>
                                        <td>{{ $item->supir }} ({{ $item->telpSupir }}) </td>
                                        {{-- <td>{{ $item->status }}</td> --}}
                                        <td style="text-align: center;"> <input type="checkbox" name="idSewa[]" class="sewa_centang" custId="{{ $item->id_customer }}" grupId="{{ $item->id_grup }}" value="{{ $item->idSewanya }}"></td>
                                        <input type="hidden" name="idCust[]" placeholder="idCust">
                                        <input type="hidden" name="idGrup[]" placeholder="idGrup">
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
 $(document).ready(function () {
    
        $('body').on('click','.grup_centang',function()
		{
            var idGrupParent= $(this);
            $('.grup_centang[type=checkbox]').each(function(idx) {
                var id_pergrup_semua_cekbox = $(this);
                // var idGrupCheckboxes = $(`.grup_centang[id_grup='${idGrupParent.attr('id_grup')}']`);
                // cek semua cekbox
                if (id_pergrup_semua_cekbox.is(":checked")) {
                    // kalau id cekbox ga sama dengan yang di centang sekarang, hapus cheknya
                    id_pergrup_semua_cekbox.not(idGrupParent).prop('checked', false);
                }
            });
             $('.customer_centang[type=checkbox]').each(function(idx) {
                var id_percust_semua = $(this);
                if (id_percust_semua.is(":checked")) {
                    id_percust_semua.not(idGrupParent).prop('checked', false);
                }
                if(id_percust_semua.attr('id_customer_grup')==idGrupParent.attr('id_grup'))
                {
                    if (idGrupParent.is(":checked")) {
                        id_percust_semua.prop('checked', true);
                    } else if (!idGrupParent.is(":checked")) {
                        id_percust_semua.prop('checked', false);
                    }
                }
                else
                {
                    id_percust_semua.prop('checked', false);
                }
            });
            $('.sewa_centang[type=checkbox]').each(function(idx) {
                var id_grup_sewa = $(this);
                if(id_grup_sewa.attr('grupId')==idGrupParent.attr('id_grup'))
                {
                    if (idGrupParent.is(":checked")) {
                         id_grup_sewa.prop('checked', true);

                    } else if (!idGrupParent.is(":checked")) {
                        id_grup_sewa.prop('checked', false);

                    }
                    // idGrupParent.prop('checked', true);
                }
                else
                {
                    id_grup_sewa.prop('checked', false);
                    // idGrup.prop('checked', false);
                }
            });
            
        });

        $('body').on('click','.customer_centang',function()
		{

            var idCustParent= $(this);
            $('.grup_centang[type=checkbox]').each(function(idx) {
                var id_grup_semua_cekbox = $(this);
                // cek semua cekbox
                if(id_grup_semua_cekbox.attr('id_grup')==idCustParent.attr('id_customer_grup'))
                {
                    if (id_grup_semua_cekbox.is(":checked")) {
                            id_grup_semua_cekbox.prop('checked', true);
    
                        } else if (!id_grup_semua_cekbox.is(":checked")) {
                            
                            id_grup_semua_cekbox.prop('checked', false);
                        }
                }
                else
                {
                    if(id_grup_semua_cekbox.attr('id_grup')!=idCustParent.attr('id_customer_grup'))
                    {
                        
                        id_grup_semua_cekbox.prop('checked', false);
                        // idCustParent.prop('checked', false);


                    }
                }
                // if (id_grup_semua_cekbox.is(":checked")) {
                //     // kalau id cekbox ga sama dengan yang di centang sekarang, hapus cheknya
                //     id_grup_semua_cekbox.not(idCustParent).prop('checked', false);
                // }
              
              
            });
            // var idCustCheckboxes = $(`.customer_centang[id_customer_grup='${idCustParent.attr('id_customer_grup')}']`);
             $('.customer_centang[type=checkbox]').each(function(idx) {
                var id_percust_semua = $(this);
                
                if(id_percust_semua.attr('id_customer_grup')==idCustParent.attr('id_customer_grup'))
                {
                    if(id_percust_semua.attr('id_customer')==idCustParent.attr('id_customer'))
                    {
                        if (idCustParent.is(":checked")) {
                        id_percust_semua.prop('checked', true);
                        } else if (!idCustParent.is(":checked")) {
                            id_percust_semua.prop('checked', false);
                        }
                    }
                    
                }
                else
                {
                        id_percust_semua.prop('checked', false);
                    
                }
            });
            
            
            $('.sewa_centang[type=checkbox]').each(function(idx) {
                var id_cust_sewa = $(this);
                if(id_cust_sewa.attr('grupId')==idCustParent.attr('id_customer_grup'))
                {
                    if(id_cust_sewa.attr('custId')==idCustParent.attr('id_customer'))
                    {
                        if (idCustParent.is(":checked")) {
                            id_cust_sewa.prop('checked', true);
    
                        } else if (!idCustParent.is(":checked")) {
                            
                            id_cust_sewa.prop('checked', false);
                        }
                    }
                    // else
                    // {
                    //     id_cust_sewa.prop('checked', false);
                    // }
                }
                else
                {
                    // if(id_cust_sewa.attr('custId')!=idCustParent.attr('id_customer'))
                    // {
                        
                        id_cust_sewa.prop('checked', false);
                        // if(id_cust_sewa.attr('grupId')!=idCustParent.attr('id_customer_grup'))
                        // {
                        //     idCustParent.prop('checked', false);
                        // }
                        


                    // }
                    

                }
            });
        });
        $('body').on('click','.sewa_centang',function()
		{
            var sewa_cekbox= $(this);
            $('.grup_centang[type=checkbox]').each(function(idx) {
                var id_grup_semua_cekbox = $(this);
                // cek semua cekbox
                if(id_grup_semua_cekbox.attr('id_grup')==sewa_cekbox.attr('grupId'))
                {
                    if (id_grup_semua_cekbox.is(":checked")) {
                            id_grup_semua_cekbox.prop('checked', false);
                        } 
                }
                else
                {
                    if(id_grup_semua_cekbox.attr('id_grup')!=sewa_cekbox.attr('grupId'))
                    {
                        
                        id_grup_semua_cekbox.prop('checked', false);
                        // idCustParent.prop('checked', false);
                    }
                }
            });
             $('.customer_centang[type=checkbox]').each(function(idx) {
                var id_percust_semua = $(this);
                
                if(id_percust_semua.attr('id_customer_grup')==sewa_cekbox.attr('grupId'))
                {
                    if(id_percust_semua.attr('id_customer')==sewa_cekbox.attr('custId'))
                    {
                        if (id_percust_semua.is(":checked")) {
                        id_percust_semua.prop('checked', false);
                        } 
                    }
                    
                }
                else
                {
                        id_percust_semua.prop('checked', false);
                    
                }
            });
            
            
            $('.sewa_centang[type=checkbox]').each(function(idx) {
                var id_cust_sewa = $(this);
                if(id_cust_sewa.attr('grupId')!=sewa_cekbox.attr('grupId'))
                {
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
                }
                
            });
        });
        $('body').on('click','#sewaAdd',function()
		{
            var selectedValues = [];
            var custId = [];
            var grupId = [];
            $(".sewa_centang[type=checkbox]:checked").each(function() {
                selectedValues.push($(this).val());
                custId.push($(this).attr('custId'));
                grupId.push($(this).attr('grupId'));
            });
            var baseUrl = "{{ asset('') }}";
            $.ajax({
                url: `${baseUrl}invoice/set_sewa_id`, 
                method: 'POST', 
                data: { 
                    idSewa: selectedValues ,
                    idCust: custId,
                    idGrup: grupId,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    if(response)
                    {
                        console.log(response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
           
		})
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
