
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
    <div class="card radiusSendiri">
        <div class="card-header">

        </div>
        
        <div class="card-body">
            {{-- <div style="overflow: auto;"> --}}
                <table id="datatable" class="table table-bordered table-striped" width='100%'>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Supplier</th>
                            <th>No. BL</th>
                            <th>Catatan</th>
                            <th>Total</th>
                            <th style="width:30px"></th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $item->getCustomer->nama }}</td>
                                <td>{{ $item->getSupplier->nama }}</td>
                                <td>{{ $item->no_bl }}</td>
                                <td>{{ $item->catatan }}</td>
                                <td>{{ number_format($item->jaminan->nominal) }}
                                    <textarea id="data_{{ $item->id }}" rows="10">{{ $item[0] }}</textarea>
                                </td>
                                <td><button value="{{ $item->id }}" class="btn btn-primary radiusSendiri showModal"> <i class="fas fa-dollar-sign"> </i> <i class="fa fa-reply-all" ></i> </button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            {{-- </div> --}}
        </div>
    </div>


    <div class="modal fade" id="modal" tabindex='-1'>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id='save_pengembalian_jaminan'>
                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}
                        <div class='row'>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Customer</label>
                                        <input type="text" class="form-control" id="customer" readonly>
                                        <input type="hidden" id="id_jo" name="id_jo" readonly>
                                    </div>   

                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Supplier</label>
                                        <input type="text" class="form-control" id="supplier" readonly>
                                    </div>   

                                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                        <label for="">No. BL</label>
                                        <input type="text" class="form-control" id="no_kontainer" readonly> 
                                    </div>

                                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                        <label for="">Total Jaminan</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <input type="text" class="form-control" id="total_jaminan" readonly> 
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                        <label for="">Potongan</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <input type="text" name="potongan" class="form-control numaja uang" id="potongan" placeholder="" readonly> 
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><input type="checkbox" id="check_potongan" name="check_potongan"></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Total</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <input type="text" class="form-control" id="total" name="total" required>
                                        </div>
                                    </div>   
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Catatan</label>
                                        <input type="text" class="form-control" id="catatan" name="catatan">
                                    </div>   
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal"><b>BATAL</b></button>
                    <button type="button" class="btn btn-sm btn-success save_detail" id="" style='width:85px'><b>SIMPAN</b></button> 
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".showModal").click(function (event) {
            var data = $('#data_'+this.value).val();
            console.log('event.this.value', data);
            $("#modal").modal("show");
        });

        function clear(){
            $('#customer').val('');
            $('#supplier').val('');
            $('#no_bl').val('');
            $('#total_jaminan').val('');
            $('#potongan').val('');
            $('#total').val('');
            $('#catatan').val('');
        };
    });
</script>
@endsection