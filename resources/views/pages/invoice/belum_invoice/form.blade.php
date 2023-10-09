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
<section class="m-2">
    <form action="{{ route('belum_invoice.store') }}" id="save" method="POST" >
        @csrf
        {{-- sticky header --}}
        {{-- <div class="col-12 radiusSendiri sticky-top " style="margin-bottom: -15px;">
            <div class="card radiusSendiri" style="">
                <div class="card-header ">
                    <a href="{{ route('invoice.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    <button type="button" name="add" id="add" class="btn btn-primary radiusSendiri float-right"><i class="fa fa-plus-circle"></i> <strong >Tambah Tujuan</strong></button> 
                </div>
            </div>
        </div> --}}
        
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header ">
                    <a href="{{ route('belum_invoice.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                     <button type="submit" class="btn btn-success radiusSendiri">
                        <i class="fa fa-fw fa-save"></i> Simpan    
                    </button>
                </div>
                <div class="card-body" >
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
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
                                            <input readonly type="text" autocomplete="off" name="tanggal_invoice" class="form-control date" id="tanggal_invoice" placeholder="dd-M-yyyy" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="">Grup</label>
                                <input type="text" class="form-control" value="{{ $dataCust[0]->getGrup->nama_grup }}" readonly>                         
                                <input type="hidden" id="grup_id" name="grup_id" class="form-control" value="{{ $dataCust[0]->grup_id }}" readonly>                         
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
                                <input type="text" id="catatan_invoice" name="catatan_invoice" class="form-control" value="">                         
                            </div>  
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Tagihan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_tagihan" name="total_tagihan" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Dibayar</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_dibayar" name="total_dibayar" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Jumlah Muatan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Kg</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_jumlah_muatan" name="total_jumlah_muatan" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
    
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Sisa</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_sisa" name="total_sisa" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <ul class="list-group">
                                        <li class="list-group-item text-primary"><b>BILLING TO</b></li>
                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <span><b>Grand Total</b></span>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <b><span id="total_tagihan_text">Rp. 0</span></b>
                                                    {{-- <input type="hidden" name="total_tagihan" id="total_tagihan"> --}}
                                                </div>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="col-10">
                                                    <select name="billingTo" class="select2" style="width: 100%" id="billingTo" required>
                                                        <option value="">── BILLING TO ──</option>
                                                        @foreach ($dataCust as $cust)
                                                            <option value="{{ $cust->id }}" kode="{{ $cust->kode }}" ketentuan_bayar="{{ $cust->ketentuan_bayar }}" {{ $cust->id == $customer? 'selected':'' }}> {{ $cust->kode }} - {{ $cust->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="kode_customer" id="kode_customer">
                                                </div>
                                                <div class="col-2">
                                                   
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
        </div>
        <div style="overflow: auto;">
            <table class="table table-hover table-bordered table-striped " width='100%' id="table_invoice">
                <thead>
                    <tr class="bg-white">
                        <th>Customer</th>
                        <th>Tujuan</th>
                        <th>Sewa</th>
                        <th><small><b>Kontainer &amp; SJ</b></small></th>
                        <th style="width:1px; white-space: nowrap; text-align:right">Muatan</th>
                        <th style="width:1px; white-space: nowrap; text-align:right">Tarif</th>
                        <th style="width:1px; white-space: nowrap; text-align:right">Add Cost</th>
                        <th style="width:1px; white-space: nowrap; text-align:right">Diskon</th>
                        <th style="width:1px; white-space: nowrap; text-align:right">Subtotal</th>
                        <th>Catatan</th>
                        <th style="width:30px"></th>
                    </tr>
                </thead>
                <tbody>
                @isset($data)
                    @foreach ($data as $key => $item)
                        <tr id="0">
                            <td> 
                                {{ $item->getCustomer->nama }} 
                                <input type="hidden" name="detail[{{ $item->id_sewa }}][id_customer]" value="{{ ($item->id_customer) }}" />
                                <input type="hidden" name="detail[{{ $item->id_sewa }}][id_jo_hidden]" value="{{ $item->id_jo }}" />
                                <input type="hidden" name="detail[{{ $item->id_sewa }}][id_jo_detail_hidden]" value="{{ $item->id_jo_detail }}" />

                            </td>
                            <td> {{ $item->nama_tujuan }} </td>
                            <td> {{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }} <br> {{ $item->no_polisi }} ({{ $item->getKaryawan->nama_panggilan }}) </td>
                            <td> <span id="no_kontainer_text_{{ $item->id_sewa }}">{{ isset($item->id_jo_detail)? $item->getJOD->no_kontainer:'(OUTBOUND)' }}</span> <br> <span id='no_surat_jalan_text_{{ $item->id_sewa }}'>{{ $item->no_surat_jalan }}</span> </td>
                            <td>-</td>
                            <td style="text-align:right" id="tarif_{{ $key }}">{{ number_format($item->total_tarif) }}</td>
                            <td style="text-align:right">
                                @php
                                    $total_addcost = 0;
                                @endphp
                                @foreach ($item->sewaOperasional as $i => $oprs)
                                    @if ($oprs->is_aktif == 'Y' && $oprs->status == 'SUDAH DICAIRKAN' &&$oprs->is_ditagihkan == 'Y'&&$oprs->is_dipisahkan == 'N')
                                        <input type="hidden" class="addcost_{{ $item->id_sewa }} {{ $oprs->deskripsi }}" value="{{ $oprs->total_operasional }}">
                                        @php
                                            $total_addcost += $oprs->total_operasional;
                                        @endphp
                                    @endif
                                @endforeach
                                {{ number_format($total_addcost) }}
                                <input type="hidden" name="detail[{{ $item->id_sewa }}][addcost_details]" id="detail_addcost_{{ $item->id_sewa }}" value="{{ json_encode($item->sewaOperasional) }}" />
                                <input type="hidden" class="addcost_{{ $item->id_sewa }} {{ $item->deskripsi }}" name='detail[{{ $item->id_sewa }}][addcost]' id='addcost_hidden_{{ $item->id_sewa }}' value="{{ $total_addcost }}">
                            </td>
                            <td style="text-align:right">
                                <span id='diskon_text_{{ $item->id_sewa }}'></span>
                            </td>
                            <td style="text-align:right">
                                <span id='subtotal_text_{{ $item->id_sewa }}'>{{ number_format($total_addcost+$item->total_tarif) }}</span>
                                <input type="hidden" class="hitung_subtotal subtotal_hidden_{{ $item->id_sewa }} {{ $item->deskripsi }}" name='detail[{{ $item->id_sewa }}][subtotal]' id='subtotal_hidden_{{ $item->id_sewa }}' value="{{ $total_addcost+$item->total_tarif }}">
                            </td>
                            <td>
                                <span id="catatan_text_{{ $item->id_sewa }}">{{ $item->catatan }}</span>
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][nama_tujuan]' id='nama_tujuan_hidden_{{ $item->id_sewa }}' value="{{ $item->nama_tujuan }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][tgl_berangkat]' id='tgl_berangkat_hidden_{{ $item->id_sewa }}' value="{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][no_kontainer]' id='no_kontainer_hidden_{{ $item->id_sewa }}' value="{{ $item->no_kontainer }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][no_surat_jalan]' id='no_surat_jalan_hidden_{{ $item->id_sewa }}' value="{{ $item->no_surat_jalan }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][tarif]' id='tarif_hidden_{{ $item->id_sewa }}' value="{{ $item->total_tarif }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][catatan]' id='catatan_hidden_{{ $item->id_sewa }}' value="{{ $item->catatan }}">
                                <input type="hidden" name='detail[{{ $item->id_sewa }}][diskon]' id='diskon_hidden_{{ $item->id_sewa }}' >
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="button" name="detail" id="detail_{{$item->id_sewa}}" class="detail dropdown-item"> 
                                            <span class="fas fa-edit mr-3"></span> Edit
                                        </button>
                                        <a href="{{ route('belum_invoice.destroy', ['belum_invoice' => $item->id_sewa]) }}" class="dropdown-item" data-confirm-delete="true">
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
                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}

                        <div class='row'>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="sewa">Sewa <span style="color:red;">*</span></label>
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
                                        <label for="">No. Surat Jalan</label>
                                        <input  type="text" class="form-control" maxlength="50" id="no_surat_jalan"> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="tarif">Tarif</label>
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
    </form>
</section>

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
                        icon: 'success',
                        title: 'Data Disimpan'
                    })

                    setTimeout(() => {
                        this.submit();
                    }, 800); // 2000 milliseconds = 2 seconds
                }else{
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
        // $('#jatuh_tempo').datepicker({
        //     autoclose: true,
        //     format: "dd-M-yyyy",
        //     todayHighlight: true,
        //     language: 'en',
        //     startDate: today,
        // }).datepicker("setDate", today);
        var selectedOption = $('#billingTo').find('option:selected');
        var ketentuan_bayar = selectedOption.attr('ketentuan_bayar');
        
        console.log(ketentuan_bayar);
        if(ketentuan_bayar==undefined)
        {
            getDate(0);


        }
        else
        {
            getDate(parseFloat(ketentuan_bayar) );

        }

        calculateGrandTotal(); // pas load awal langsung hitung grand total

        $(document).on('click', '.detail', function(){ // open detail 
            clearData(); // execute clear data dulu tiap open modal
            $('#key').val(''); // key di clear dulu
            var button_id = $(this).attr("id"); // get value id
            var key = button_id.replace("detail_", ""); // hapus teks detail_
            $('#key').val(key); // id key buat nge get data yg di hidden, key = id_sewa

            $('#tanggal_berangkat').val( $('#tgl_berangkat_hidden_'+key).val() );
            $('#nama_tujuan').val( $('#nama_tujuan_hidden_'+key).val() ); 
            $('#no_kontainer').val( $('#no_kontainer_hidden_'+key).val() ); 
            $('#no_surat_jalan').val( $('#no_surat_jalan_hidden_'+key).val() ); 
            $('#catatan').val( $('#catatan_hidden_'+key).val() ); 
            $('#tarif').val( moneyMask($('#tarif_hidden_'+key).val()) ); 
            $('#addcost').val( moneyMask($('#addcost_hidden_'+key).val()) ); 
            $('#diskon').val( moneyMask($('#diskon_hidden_'+key).val()) ); 

            var dataSewa = <?php echo $dataSewa; ?>;

            dataSewa.forEach(function(item, index) {
                var option = $('<option>');
                option.text(item.no_sewa + ' - ' + item.nama_tujuan + ' - (' + dateMask(item.tanggal_berangkat) + ')');
                option.val(item.id_sewa);
                if (item.id_sewa == key) {
                    option.prop('selected', true);
                }
                $('#addcost_sewa').append(option);
            });

            showAddcostDetails(key);
            hitung();
            $('#modal_detail').modal('show');
        });

        $(document).on('click', '.save_detail', function(event){ // save detail
            var key = $('#key').val(); 

            $('#no_kontainer_hidden_'+key).val( $('#no_kontainer').val() );
            $('#no_surat_jalan_hidden_'+key).val( $('#no_surat_jalan').val() );
            $('#catatan_hidden_'+key).val( $('#catatan').val() );
            $('#diskon_hidden_'+key).val( $('#diskon').val() );
            $('#subtotal_hidden_'+key).val( escapeComma($('#subtotal').val()) );

            // Set text content using JavaScript
            var elementIds = ["no_kontainer", "no_surat_jalan", "catatan", "diskon", "subtotal"];
            elementIds.forEach(function (id) {
                document.getElementById(id + '_text_' + key).textContent = $('#' + id).val();
            });

            calculateGrandTotal(); // pas load awal langsung hitung grand total
            $('#modal_detail').modal('hide'); // close modal

            // var noKontainerText = document.getElementById("no_kontainer_text_"+key);
            // noKontainerText.textContent = $('#no_kontainer').val(); 
            // var noSJText = document.getElementById("no_surat_jalan_text_"+key);
            // noSJText.textContent = $('#no_surat_jalan').val(); 
            // var catatanText = document.getElementById("catatan_text_"+key);
            // catatanText.textContent = $('#catatan').val(); 
            // var diskonText = document.getElementById("diskon_text_"+key);
            // diskonText.textContent = $('#diskon').val(); 
            // var subtotalText = document.getElementById("subtotal_text_"+key);
            // subtotalText.textContent = $('#subtotal').val(); 
        });

        $(document).on('keyup', '#diskon', function(){ // kalau diskon berubah, hitung total 
            var id_sewa = $('#key').val();
            hitung(); // execute fungsi hitung tiap perubahan value diskon, (tarif + addcost - diskon)
        });

        $('body').on('change','#billingTo',function()
		{
            var selectedOption = $(this).find('option:selected');
            var ketentuan_bayar = selectedOption.attr('ketentuan_bayar');
            
            console.log(ketentuan_bayar);
            if(ketentuan_bayar==undefined)
            {
                getDate(0);


            }
            else
            {
                getDate(parseFloat(ketentuan_bayar) );

            }

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
        function calculateGrandTotal(){ // hitung grand total buat ditagihkan 
            var grandTotal = 0; 
            var grandTotalText = document.getElementById("total_tagihan_text");
            var subtotals = document.querySelectorAll('.hitung_subtotal');
            subtotals.forEach(function(subtotal) {
                grandTotal += parseFloat(subtotal.value); // Convert the value to a number
            });
            if(grandTotal && grandTotal >= 0){
                $('#total_tagihan').val(moneyMask(grandTotal));
                var total_dibayar = $('#total_dibayar').val() == 0 || $('#total_dibayar').val() == NULL? 0:$('#total_dibayar').val();
                var total_sisa = grandTotal - parseFloat( total_dibayar );
                $('#total_sisa').val( moneyMask(total_sisa) );
                grandTotalText.textContent = "Rp. " + moneyMask(grandTotal); // Change the text content of the span
            }
        }

        function showAddcostDetails(key){
            var details = $('#detail_addcost_'+key).val(); 
            if (details && (details != null || cekBiaya != '')) { // cek apakah ada isi detail addcost
                JSON.parse(details).forEach(function(item, index) {
                    if(item.is_aktif=="Y")
                    {
                        $('#tabel_addcost > tbody:last-child').append(
                            `
                                <tr id="row_addcost_${index}">
                                    <td>
                                        ${item.deskripsi == null? '':item.deskripsi} 
                                        <input type="hidden" id="addcost_deskripsi_${index}" value="${item.deskripsi}" class="form-control" readonly />
                                        <input type="hidden" name="sewa_operasional_id${index}" id="sewa_operasional_id${index}" value="${item.id}">
                                    </td>
                                    <td>
                                        ${item.total_operasional == null? '':moneyMask(item.total_operasional)}
                                        <input type="hidden" id="addcost_total_operasional_${index}" value="${item.total_operasional}" class="form-control numaja uang hitungBiaya" readonly />
                                    </td>
                                    <td>
                                        ${item.is_ditagihkan == null? '':item.is_ditagihkan}
                                        <input type="hidden" id="addcost_is_ditagihkan_${index}" value="${item.is_ditagihkan}" class="form-control" readonly />
                                    </td>
                                    <td>
                                        ${item.is_dipisahkan == null? '':item.is_dipisahkan}
                                        <input type="hidden" id="addcost_is_dipisahkan_${index}" value="${item.is_dipisahkan}" class="form-control" readonly />
                                    </td>
                                    <td>
                                        ${item.catatan == null? '':item.catatan}
                                        <input type="hidden" id="addcost_catatan_${index}" value="${item.catatan}" class="form-control w-auto" readonly />
                                    </td>
                                </tr>
                            `
                        );
                    }
                });
            }
        }

        function hitung(){ // hitung tarif + addcost - diskon 
            var id_sewa = $('#key').val();
            var tarif = parseFloat($('#tarif').val().replace(/,/g, ''));
            var addcost = parseFloat($('#addcost').val().replace(/,/g, ''));
            var diskon = $('#diskon').val() == null || $('#diskon').val() == 0? 0:parseFloat($('#diskon').val().replace(/,/g, ''));

            if (diskon > (tarif + addcost) ){
                diskon = (tarif + addcost);
                $('#diskon').val(diskon);
            } 

            var subtotal = tarif + addcost - diskon;
            calculateGrandTotal();
            $('#subtotal').val(moneyMask(subtotal));
        }

        function clearData(){ // clear data sebelum buka modal 
            $('#tanggal_berangkat').val('');
            $('#nama_tujuan').val('');
            $('#no_kontainer').val('');
            $('#no_surat_jalan').val('');
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


