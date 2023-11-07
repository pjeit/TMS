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
    <form action="{{ route('pembayaran_invoice_karantina.bayar') }}" method="GET" enctype="multipart/form-data">
    @csrf @method('GET')
        <div class="card radiusSendiri">
            <div class="card-header">
                <button type="submit" class="btn btn-primary btn-responsive radiusSendiri" id="bayarInvoice">
                    <i class="fa fa-credit-card"></i> Bayar
                </button>
            </div>
            <div class="card-body">
                {{-- <div style="overflow: auto;"> --}}
                    <table id="tabel_pembayaran_karantina" class="table table-bordered" width='100%'>
                        <thead id="thead">
                            <tr style="margin-right: 0px;">
                                <th>Customer</th>
                                <th>No. Invoice</th>
                                <th>Tgl Invoice</th>
                                <th>Sisa Tagihan</th>
                                <th style="width: 30px;"></th>
                            </tr>
                        </thead>
                
                        <tbody id="hasil">
                            @if (isset($data))
                                @foreach($data as $key => $item)
                                    <tr >
                                        <td>
                                            <div class="d-flex justify-content-between" style="margin-right: -13px;">
                                                <div>{{ $item->getCustomer->nama }}</div>
                                                <div style="width: 55px; text-align: center">                                            
                                                    <input type="checkbox" name="idCustomer[]" class="sewa_centang">
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item->no_invoice_k }}</td>
                                        <td>{{ date("d-M-Y", strtotime($item->tgl_invoice)) }}</td>
                                        <td style="text-align: right;">{{ number_format($item->sisa_tagihan) }}</td>
                                        <td style="text-align: center; width: 30px;"> 
                                            @if ($item->sisa_tagihan != 0)
                                                <input type="checkbox" name="idInvoice[]" value="{{ $item->id }}" class="sewa_centang">
                                            @else
                                                <btn class="btn btn-primary btn-sm radiusSendiri" id='input_bukti' > <span class="fa fa-sticky-note mr-1"></span> Input Bukti Potong</btn>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                {{-- </div> --}}
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        new DataTable('#tabel_pembayaran_karantina', {
            ordering: false,
            order: [
                [0, 'asc'],
            ],
            rowGroup: {
                dataSrc: [0]
            },
            columnDefs: [
                {
                    targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                    visible: false
                },
            ],
        });
       
    });
</script>
@endsection
