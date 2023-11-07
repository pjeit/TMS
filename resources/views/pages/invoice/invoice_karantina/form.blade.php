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
    table {
        /* display: block;
        overflow-x: auto;
        white-space: nowrap; */
    }
</style>
<div class="container-fluid">
    <form action="{{ route('invoice_karantina.store') }}" id="save" method="POST" >
        @csrf
        <div class="card radiusSendiri">
            <div class="card-header ">
                <a href="{{ route('belum_invoice.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" class="btn btn-success radiusSendiri" id="btnSimpan">
                    <i class="fa fa-fw fa-save"></i> Simpan    
                </button>
            </div>
            <div class="card-body" >
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Grup</label>
                                <input type="text" class="form-control" value="{{ $data[0]->getCustomer->getGrup->nama_grup }}" readonly>                         
                                <input type="hidden" id="grup_id" name="grup_id" class="form-control" value="{{ null }}" readonly>                         
                                <input type="hidden" id="no_invoice" name="no_invoice" class="form-control" value="" placeholder="otomatis" readonly>   
                            </div>  
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Tanggal Invoice<span style="color:red">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input readonly type="text" autocomplete="off" name="tanggal_invoice" class="form-control date" id="tanggal_invoice" placeholder="dd-M-yyyy" value="">
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Jatuh Tempo<span style="color:red">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input name="jatuh_tempo" id="jatuh_tempo" class="form-control date" required type="text" autocomplete="off" placeholder="dd-M-yyyy">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Catatan</label>
                                <textarea type="text" id="catatan_invoice" name="catatan_invoice" class="form-control" rows="1"></textarea>                     
                            </div>  
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Total Tagihan</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" maxlength="100" id="total_tagihan" name="total_tagihan" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                </div>
                            </div>
                        </div>
                    
                        {{-- <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Total Dibayar</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" maxlength="100" id="total_dibayar" name="total_dibayar" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                </div>
                            </div>

                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Total Sisa</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" id="total_sisa" name="total_sisa" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    <input type="hidden" id="total_pisah" name="total_pisah" class="form-control uang numajaMinDesimal" value="" placeholder="total_pisah" readonly>                         
                                </div>
                            </div>
                        </div> --}}
                        
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <ul class="list-group ">
                                    <li class="list-group-item bg-light text-primary border-primary"><span class="font-weight-bold">BILLING TO</span></li>
                                    <li class="list-group-item bg-light border-primary">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <span class="text-bold">Grand Total</span>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <span id="total_tagihan_text" class="text-bold">Rp. 0</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item bg-light border-primary">
                                        <div class="row">
                                            <div class="col-12">
                                                <select name="billingTo" class="select2" style="width: 100%" id="billingTo" required>
                                                    <option value="">── BILLING TO ──</option>
                                                    @foreach ($dataCust as $cust)
                                                        <option value="{{ $cust->id }}" kode="{{ $cust->kode }}" {{ $cust->id == $customer? 'selected':'' }}> {{ $cust->kode }} - {{ $cust->nama }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="kode_customer" id="kode_customer">
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div> 
    </form>


    <div class="" style="overflow-x:auto; overflow-y:hidden">
        <table class="table table-hover table-bordered table-striped" width='100%' id="table_invoice">
            <thead>
                <tr class="bg-white">
                    <th>Customer</th>
                    <th>No. BL</th>
                    <th>Kapal / Voyage</th>
                    <th>No. Kontainer</th>
                    <th>Total</th>
                    <th style="width:30px"></th>
                </tr>
            </thead>
            <tbody>
            @isset($data)
                @foreach ($data as $key => $item)
                    <tr id="0">
                        <td> {{ $item->getCustomer->nama }} </td>
                        <td> {{ $item->getJO->no_bl }} </td>
                        <td> {{ $item->getJO->kapal }} ( {{ $item->getJO->voyage }} ) </td>
                        <td> @foreach ($item->details as $value)
                                {{ '#'.$value->getJOD->no_kontainer }} <br>
                             @endforeach
                        </td>
                        <td> {{ number_format($item->total_dicairkan) }} </td>
                        <td>
                            <input type="hidden" id="dicairkan_{{ $item->id }}" class="dicairkan" value="{{ $item->total_dicairkan }}">
                            <div class="btn-group dropleft">
                                <button type="button" class="btn btn-sm btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-list"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <button type="button" name="detail" id="detail_{{$item->id}}" class="detail dropdown-item"> 
                                        <span class="fas fa-edit mr-3"></span> Detail
                                    </button>
                                    {{-- <a href="{{ route('invoice_karantina.destroy', [$item->id]) }}" class="dropdown-item" data-confirm-delete="true">
                                        <span class="fas fa-trash mr-3"></span> Delete
                                    </a> --}}
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endisset
            </tbody>
        </table>
    </div>
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
                <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}

                <div class='row'>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Sewa <span style="color:red;">*</span></label>
                                <select class="select2" style="width: 100%" id="addcost_sewa">
                                </select>
                            </div>   

                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Tanggal Berangkat <span style="color:red;">*</span></label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" autocomplete="off" class="form-control" id="tanggal_berangkat" placeholder="" readonly value="">
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Tujuan <span style="color:red;">*</span></label>
                                <input  type="text" class="form-control" id="nama_tujuan" readonly> 
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">No. Kontainer</label>
                                <input  type="text" class="form-control" maxlength="50" id="no_kontainer"> 
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Surat Jalan</label>
                                <input  type="text" class="form-control" maxlength="50" id="no_sj"> 
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Tarif</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="tarif" placeholder="" readonly>
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="">Add Cost</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="addcost" placeholder="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Diskon</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="diskon" placeholder="" >
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Subtotal</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control numaja uang" id="subtotal" placeholder="" readonly>
                                </div>
                            </div>
                            
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Catatan</label>
                                <input type="text" class="form-control" maxlength="255" id="catatan"> 
                            </div>
                        </div>
                    </div>
                </div>

                <div class='row'>
                    <div class="table-responsive p-0 mx-3">
                        <form name="add_addcost_detail" id="add_addcost_detail">
                            <label for="">Detail Add Cost</label>
                            <input type="hidden" id="deleted_temp" name="deleted_temp" placeholder="deleted_temp">
                            <table class="table table-hover table-bordered table-striped text-nowrap" id="tabel_addcost">
                                <thead>
                                    <tr class="">
                                        <th style="">Deskripsi</th>
                                        <th style="">Jumlah</th>
                                        <th style="">Ditagihkan</th>
                                        <th style="">Dipisahkan</th>
                                        <th style="">Catatan</th>
                                        {{-- <th style="text-align: center; vertical-align: middle;">#</th> --}}
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
            <button type="button" class="btn btn-sm btn-success save_detail" id="" style='width:85px'>OK</button> 
        </div>
    </div>
    <!-- /.modal-content -->
    </div>
</div>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('#save').submit(function(event) {
            // set value kode_customer
                var kodeValue = $('#billingTo option:selected').attr('kode');
                $('#kode_customer').val(kodeValue);
            //

            event.preventDefault(); // Prevent form submission
            Swal.fire({
                title: 'Apakah Anda yakin data sudah benar ?',
                text: "Periksa kembali data anda",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }else{
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'warning',
                        title: 'Batal Disimpan'
                    })
                    event.preventDefault();
                }
            })
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        // set value default tgl invoice
        var today = new Date();
         $('#tanggal_invoice').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            startDate: today,
        }).datepicker("setDate", today);

        hitung();

        function hitung(){
            let classDicairkan = document.querySelectorAll('.dicairkan');
            let text = document.getElementById('total_tagihan_text');

            let dicairkan = 0;  

            for (var i = 0; i < classDicairkan.length; i++) {
                dicairkan += parseFloat(classDicairkan[i].value);
            }
            $('#total_tagihan').val(moneyMask(dicairkan));
            text.textContent = moneyMask(dicairkan);
            // dicairkan
        }
        
        $(document).on('click', '.detail', function(){ // open detail 
            clearData(); // execute clear data dulu tiap open modal
            $('#key').val(''); // key di clear dulu
            var button_id = $(this).attr("id"); // get value id
            var key = button_id.replace("detail_", ""); // hapus teks detail_
            $('#key').val(key); // id key buat nge get data yg di hidden, key = id_sewa
            console.log('first', key);
           
            $('#modal_detail').modal('show');
        });

        $(document).on('click', '.save_detail', function(event){ // save detail
            var key = $('#key').val(); 

            $('#no_kontainer_hidden_'+key).val( $('#no_kontainer').val() );
            $('#no_seal_hidden_'+key).val( $('#no_seal').val() );
            $('#no_sj_hidden_'+key).val( $('#no_sj').val() );
            $('#catatan_hidden_'+key).val( $('#catatan').val() );
            $('#diskon_hidden_'+key).val( $('#diskon').val() );
            $('#subtotal_hidden_'+key).val( escapeComma($('#subtotal').val()) );

            // Set text content using JavaScript
            var elementIds = ["no_kontainer", "no_seal", "no_sj","catatan", "diskon", "subtotal"];
            elementIds.forEach(function (id) {
                document.getElementById(id + '_text_' + key).textContent = $('#' + id).val();
            });

            calculateGrandTotal(); // pas load awal langsung hitung grand total
            $('#modal_detail').modal('hide'); // close modal
        });

        function getDate(hari){
            var today = new Date();
            var set_hari = new Date(today);
            set_hari.setDate(today.getDate() + hari);

            $('#jatuh_tempo').datepicker({
                autoclose: false,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                // endDate: '+0d',
                startDate: today,
            }).datepicker("setDate", set_hari);
        }
        
        function clearData(){ // clear data sebelum buka modal 
            $('#tanggal_berangkat').val('');
            $('#nama_tujuan').val('');
            $('#no_kontainer').val('');
            $('#no_seal').val('');
            $('#no_sj').val('');
            $('#catatan').val('');
            $('#tarif').val('');
            $('#addcost').val('');
            $('#subtotal').val('');
            $('#diskon').val('');

            $('#addcost_sewa').empty();
            $('#tabel_addcost tbody').empty(); // clear tabel detail addcost di dalam modal
        }
    });
</script>

@endsection


