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
        <form action="{{ route('tagihan_pembelian.bayar') }}" id="bayar" method="POST" >
            @csrf
            <div class="card-header">
                <a href="{{ route('tagihan_pembelian.create') }}"  class="btn btn-primary radiusSendiri"> <i class="fa fa-plus-circle"></i> Data Baru</a>
                <button type="submit" class="btn radiusSendiri btn-outline-dark ml-3" id="bayar_tagihan">
                    <i class="fa fa-credit-card"></i> Bayar
                </button>
            </div>
            <div class="card-body">
                <div style="overflow: auto;">
                    <table id="tabel_tagihan" class="table table-bordered" width='100%'>
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
                                    @if ($item->sisa_tagihan != 0)              
                                        <tr>
                                            <td>{{ $item->getSupplier->nama }} 
                                                <span class="float-right">
                                                    <input type="checkbox" style="margin-right: 0.9rem;" class="supplier_centang id_supplier_{{ $item->getSupplier->id }}" value="{{ $item->getSupplier->id }}" >
                                                </span> 
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
                                                       
                                                        @if ($item->tagihan_dibayarkan==0)
                                                            @can('EDIT_TAGIHAN_PEMBELIAN')
                                                                <a href="{{ route('tagihan_pembelian.edit', [$item->id]) }}" class="dropdown-item update_resi">
                                                                    <span class="fas fa-pen-alt mr-3"></span> Edit
                                                                </a>
                                                            @endcan
                                                            <a href="{{ route('tagihan_pembelian.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                                <span class="fas fa-trash mr-3"></span> Hapus
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="text-align: center; float: center;"> 
                                                <input type="checkbox" name="idTagihan[]" class="perItem item_{{ $item->getSupplier->id }}" idSupplier="{{ $item->getSupplier->id }}" value="{{ $item->id }}" >
                                            </td>
                                        </tr>
                                    @endif

                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
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
        $('#bayar').submit(function(event) {
            let checkboxes = document.querySelectorAll('input[name="idTagihan[]"]');
            let selectedValues = [];
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    selectedValues.push(checkbox.value);
                }
            });
            console.log('selectedValues', selectedValues);
            if(!selectedValues.length){
                Swal.fire(
                    'Data tidak valid',
                    'Harap pilih tagihan terlebih dahulu!',
                    'warning'
                )
                return false;
            }else{
                this.submit();
            }
        });

        $('#tabel_tagihan').DataTable( {
            order: [
                [0, 'asc'], // 0 = grup
            ],
            rowGroup: {
                dataSrc: [0] // di order grup dulu, baru customer
            },
            columnDefs: [
                {
                    targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                    visible: false
                },
                {
                    targets: [6, 7],
                    orderable: false, // matiin sortir kolom centang
                },
            ],
        });

        $(document).on('click', '.supplier_centang', function (event) {
            var id = this.value;

            if ($(this).is(':checked')) {
                $(".perItem.item_"+id).prop("checked", true);
            } else {
                $(".perItem.item_"+id).prop("checked", false);
            }
        });

        $(document).on('click', '.perItem', function (event) {
            var idSupplier = $(this).attr('idSupplier');
            console.log('first', idSupplier);

            if ($(this).is(':checked')) {
            }else{
                $(".id_supplier_"+idSupplier).prop("checked", false);
            }
        });
    });
</script>
@endsection
