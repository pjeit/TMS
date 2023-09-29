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
                                <th></th>
                                <th>Grup</th>
                                <th>Customer</th>
                                <th>No. Polisi Kendaraan</th>
                                <th>No. Sewa</th>
                                <th>Tgl Berangkat</th>
                                <th>Tujuan</th>
                                <th>Driver</th>
                                {{-- <th>Status</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($dataSewa))
                                @foreach($dataSewa as $item)
                                    <tr>
                                        <td style="text-align: center;"> <input type="checkbox" name="idSewa[]" class="sewa_centang" custId="{{ $item->id_customer }}" grupId="{{ $item->id_grup }}" value="{{ $item->idSewanya }}"></td>
                                        <input type="hidden" name="idCust[]" placeholder="idCust">
                                        <input type="hidden" name="idGrup[]" placeholder="idGrup">
                                        <td>{{ $item->nama_grup }} <span class="float-right"><input type="checkbox" name="" class="grup_centang" id_grup="{{ $item->id_grup }}"></span> </td>
                                        <td>{{ $item->nama_cust }} <span class="float-right"><input type="checkbox" name="" class="customer_centang" id_customer="{{ $item->id_customer }}"></span> </td>
                                        <td>{{ $item->no_polisi }}</td>
                                        <td>{{ $item->no_sewa }}</td>
                                        <td>{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}</td>
                                        <td>{{ $item->nama_tujuan }}</td>
                                        <td>{{ $item->supir }} ({{ $item->telpSupir }}) </td>
                                        {{-- <td>{{ $item->status }}</td> --}}
                                        
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
            var idGrup= $(this);
            $('.sewa_centang[type=checkbox]').each(function(idx) {
                var id_grup_sewa = $(this).attr('grupId');
                if(id_grup_sewa==idGrup.attr('id_grup'))
                {
                    $(this).prop('checked', true);
                    // idGrup.prop('checked', true);

                }
                else
                {
                    $(this).prop('checked', false);
                    // idGrup.prop('checked', false);

                }
            });
            // for (var i = 0; i < centangCheckboxes.length; i++) {
            //     var checkbox = centangCheckboxes.eq(i);
            //     var row = checkbox.closest('tr');
            //     if (checkbox.is(":checked")) {
            //         row.find('.cek_tagih').prop('disabled', false);
            //         if (value_cek_tagih == "Y") {
            //             row.find('.cek_pisah').prop('disabled', false);
            //         } else {
            //             row.find('.cek_pisah').prop('disabled', true);
            //         }
            //         row.find('.catatan').prop('readonly', false);
            //     } else if (!checkbox.is(":checked")) {
            //         row.find('.cek_tagih').prop('checked', false);
            //         row.find('.cek_tagih').prop('disabled', true);
            //         row.find('.cek_pisah').prop('checked', false);
            //         row.find('.cek_pisah').prop('disabled', true);
            //         row.find('.catatan').prop('readonly', true);
            //         row.find('.catatan').val('');

            //     }
            // }
            // var row = $(this).closest('tr'); 
            // var checkTagih = row.find('.cek_tagih');
            // var existingRow = $('#table_identitas tbody').find('td[id^="jenis_id_"]').filter(function() {
            //     return $(this).text().trim() === tampungahJenis[0];
            // }).closest('tr');
            // console.log( $('.sewa_centang').attr('grupId'));
            // $('.centang_cekbox').prop('checked', false).trigger('click');

            
        });
        $('body').on('click','#sewaAdd',function()
		{
            var selectedValues = [];
            var custId = [];
            var grupId = [];
            $("input[type='checkbox']:checked").each(function() {
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
            [1, 'asc'],
            [2, 'asc']
        ],
        rowGroup: {
            dataSrc: [1, 2]
        },
        columnDefs: [
            {
                targets: [1, 2],
                visible: false
            },
            {
                "orderable": false,
                "targets": 5,
            }
        ],
    });

});

</script>
@endsection
