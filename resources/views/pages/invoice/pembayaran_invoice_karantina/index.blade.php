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
            <div style="overflow: auto;">
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
                            @foreach($data as $item)
                                <tr >
                                    <td>{{ $item->getCustomer->nama }}</td>
                                    <td>{{ $item->no_invoice_k }}</td>
                                    <td>{{ date("d-M-Y", strtotime($item->tgl_invoice)) }}</td>
                                    <td style="text-align: right;">{{ number_format($item->sisa_tagihan) }}</td>
                                    <td style="text-align: center; width: 30px;"> 
                                        @if ($item->sisa_tagihan != 0)
                                            <input type="checkbox" name="idInvoice[]" class="sewa_centang">
                                        @else
                                            <btn class="btn btn-primary btn-sm radiusSendiri" id='input_bukti' > <span class="fa fa-sticky-note mr-1"></span> Input Bukti Potong</btn>
                                        @endif
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
                <h5 class="modal-title">Update Resi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
    </div>
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
