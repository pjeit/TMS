
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
  
@endsection

@section('content')
<style >
</style>
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $error }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endforeach
    @endif
    <form action="{{ route('pencairan_uang_jalan_ftl.store') }}" id="post_data" method="POST" >
      @csrf
        <section class="m-2">
            {{-- sticky header --}}
            <div class="col-12 radiusSendiri sticky-top " style="margin-bottom: -15px;">
                <div class="card radiusSendiri" style="">
                    <div class="card-header ">
                        <a href="{{ route('invoice.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        {{-- <button type="button" name="add" id="add" class="btn btn-primary radiusSendiri float-right"><i class="fa fa-plus-circle"></i> <strong >Tambah Tujuan</strong></button>  --}}
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-body" >
                        <div class="row">
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="no_akun">No. Invoice</label>
                                            <input type="text" id="no_invoice" name="no_invoice" class="form-control" value="" placeholder="otomatis" readonly>   
                                        </div>  
                                    </div>
                                    <div class="col-6">
                                            <div class="form-group">
                                            <label for="tanggal_invoice">Tanggal Invoice<span style="color:red">*</span></label>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input disabled type="text" autocomplete="off" name="tanggal_invoice" class="form-control date" id="tanggal_invoice" placeholder="dd-M-yyyy" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Customer</label>
                                    <input type="text" id="customer" name="customer" class="form-control" value="{{ $data[0]->getCustomer->nama }}" readonly>                         
                                </div>  

                                <div class="form-group">
                                    <label for="tanggal_pencairan">Jatuh Tempo<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input name="jatuh_tempo" id="jatuh_tempo" class="form-control date" type="text" autocomplete="off" placeholder="dd-M-yyyy" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Catatan</label>
                                    <input type="text" id="catatan" name="catatan" class="form-control" value="">                         
                                </div>  
                            </div>
    
                            <div class="col-6">
                                <div class="form-group ">
                                    <label for="total_hutang">Total Tagihan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_hutang" name="total_hutang" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="potong_hutang">Total Dibayar</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" onkeyup="cek_potongan_hutang();hitung_total();" maxlength="100" id="potong_hutang" name="potong_hutang" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                            
                                <div class="form-group ">
                                    <label for="total_diterima">Total Jumlah Muatan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Kg</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="total_diterima">Total Sisa</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div style="overflow: auto;">
                <table class="table table-hover table-bordered table-striped " width='100%' id="table_invoice">
                    <thead>
                        <tr class="bg-white">
                            <th>Tujuan</th>
                            <th>Sewa</th>
                            <th><small><b>Kontainer &amp; SJ</b></small></th>
                            <th style="width:1px; white-space: nowrap; text-align:right">Jumlah Muatan</th>
                            <th style="width:1px; white-space: nowrap; text-align:right">Tarif</th>
                            <th style="width:1px; white-space: nowrap; text-align:right">Add Cost/Inap</th>
                            <th style="width:1px; white-space: nowrap; text-align:right">Diskon</th>
                            <th style="width:1px; white-space: nowrap; text-align:right">Subtotal</th>
                            <th>Catatan</th>
                            <th style="width:30px"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @isset($data)
                        @foreach ($data as $item)
                            <tr id="0">
                                <td id="nama_tujuan">{{ $item->nama_tujuan }}</td>
                                <td id="sewa">{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }} <br> {{ $item->no_polisi }} ({{ $item->getKaryawan->nama_panggilan }})</td>
                            <td id="nokontainer_sj"> {{ isset($item->id_jo_detail)? $item->getJOD->no_kontainer:'(OUTBOUND)' }} <br> {{ $item->no_surat_jalan }}</td>
                                <td id="jumlah_muatan">-</td>
                                <td style="text-align:right" id="tarif_0">{{ number_format($item->total_tarif) }}</td>
                                <td style="text-align:right" id="total_reimburse_tidak_dipisahkan_0">0</td>
                                <td style="text-align:right" id="diskon_0">0</td>
                                <td style="text-align:right" id="subtotal_0">800,000</td>
                                <td id="catatan_0"></td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button type="button" name="detail" id="detail_{{$item->id_sewa}}" class="detail dropdown-item"> 
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </button>
                                            <a href="{{ route('invoice.destroy', ['invoice' => $item->id_sewa]) }}" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endisset
                    </tbody>
                </table>
            </div>

            <div class="modal fade" id="modal_detail" tabindex='-1'>
                <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form id='form_add_detail'>
                            <input type="hidden" name="key" id="key">
                            <input type="hidden" name="tujuan_id" id="tujuan_id">
                            <div class='row'>
                                    <div class="form-group col-lg-6 col-md-6 col-6">
                                        <label for="grup">Grup <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" name="nama_grup" id="nama_grup" value="" readonly>
                                        <input type="hidden" name="grup" id="grup" value="">
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-6">
                                        <label for="marketing">Marketing <span style="color:red;">*</span></label>
                                        <select name="marketing[]" class="select2" style="width: 100%" id="marketing" required>
                                     
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-6">
                                        <label for="nama_tujuan">Nama Tujuan <span style="color:red;">*</span></label>
                                        <input required type="text" class="form-control" maxlength="50" name="nama_tujuan" id="nama_tujuan" placeholder="Singkatan 20 Karakter"> 
                                    </div>
                        
                                    <div class="form-group col-lg-6 col-md-6 col-6">
                                        <label for="alamat">Alamat</label>
                                        <input type="text" name="alamat" class="form-control" id="alamat" placeholder=""> 
                                    </div>
                                    
                                    <div class="form-group col-lg-6 col-md-6 col-12">
                                        <label for="Catatan">Catatan</label>
                                        <input type="text" name="catatan" class="form-control" id="catatan" placeholder=""> 
                                    </div>
                                 
                                    <input type="hidden" name="total_tarif" class="form-control numaja uang" id="total_tarif" placeholder="" readonly> 
                            </div>
                            <div class='row'>
                                <div class="table-responsive p-0 mx-3">
                                    <form name="add_biaya_detail" id="add_biaya_detail">
                                        <input type="hidden" id="deleted_biaya_temp" name="deleted_biaya_temp" placeholder="deleted_biaya_temp">
                                        <table class="table table-hover table-bordered table-striped text-nowrap" id="tabel_biaya">
                                            <thead>
                                                <tr class="">
                                                    <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Deskripsi</th>
                                                    <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Biaya</th>
                                                    <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Catatan</th>
                                                    <th style="width:30px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
        
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                    <button type="button" class="btn btn-sm btn-success save_detail" style='width:85px'>OK</button> 
                    </div>
                </div>
                <!-- /.modal-content -->
                </div>
            </div>
        
        </section>
 
</form>
<script type="text/javascript">
    $(document).ready(function() {
        var today = new Date();
        $('#tanggal_invoice').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            startDate: today,
        }).datepicker("setDate", today);

        // open detail
        $(document).on('click', '.detail', function(){  
            $('#key').val('');
            var button_id = $(this).attr("id");     
            var key = button_id.replace("detail_", "");
            $('#key').val(key);

            // dropdownJenis();
            $('#modal_detail').modal('show');
        });
    });
</script>

@endsection


