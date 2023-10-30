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
            <a href="{{ route('tagihan_rekanan.create') }}"  class="btn btn-primary radiusSendiri"> <i class="fa fa-plus-circle"></i> Data Baru</a>
            <button type="submit" class="btn radiusSendiri btn-outline-dark ml-3" id="bayarInvoice">
                <i class="fa fa-credit-card"></i> Bayar
            </button>
        </div>
        <div class="card-body">
            <div style="overflow: auto;">
                <table id="tabelInvoice" class="table table-bordered" width='100%'>
                    <thead id="thead">
                        <tr style="margin-right: 0px;">
                            <th>Supplier</th>
                            <th>No. Nota</th>
                            <th>Tgl Nota</th>
                            <th>Jatuh Tempo</th>
                            <th>Sisa Tagihan</th>
                            <th>Catatan</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                        @if (isset($data))
                            @foreach($data as $key => $item)
                                <tr >
                                    <td>{{ $item->getSupplier->nama }} 
                                        @if ($item->total_sisa != 0)
                                            <span class="float-right">
                                                <input type="checkbox" style="margin-right: 0.9rem;" class="customer_centang" id_customer="{{ $item->billing_to }}" id_customer_grup="{{ $item->id_grup }}">
                                            </span> 
                                        @endif
                                    </td>
                                    <td>{{ $item->no_nota }}</td>
                                    <td>{{ date("d-M-Y", strtotime($item->tgl_nota)) }}</td>
                                    <td>{{ date("d-M-Y", strtotime($item->jatuh_tempo)) }}</td>
                                    <td><span style="float: right;">{{ number_format($item->sisa_tagihan) }}</span></td>
                                    <td>{{ $item->catatan }}</td>
                                    <td class='text-center' style="text-align:center; width: 50px;">
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu" >
                                                <a href="{{ route('tagihan_rekanan.edit', [$item->id]) }}" class="dropdown-item update_resi">
                                                    <span class="fas fa-pen-alt mr-3"></span> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center; float: center;"> 
                                        <input type="checkbox" name="idInvoice[]" class="sewa_centang" custId="{{ $item->billing_to }}" grupId="{{ $item->id_grup }}" value="{{ $item->id }}">
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
        
       
    });
</script>
@endsection
