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
<section class="container-fluid">
    <form action="{{ route('tagihan_pembelian.update', ['tagihan_pembelian' => $tagihan->id]) }}" id="save" method="POST" >
        @csrf @method('PUT')
        <div class="radiusSendiri sticky-top" style="margin-bottom: -15px;">
            <div class="card radiusSendiri" style="">
                <div class="card-header radiusSendiri">
                    <a href="{{ route('tagihan_pembelian.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        <div class="card radiusSendiri">
            <div class="card-body" >
                <div class="row">
                    <div class="bg-gray-light col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">Supplier<span class="text-red">*</span> </label>
                                    <select name="supplier" class="select2" style="width: 100%" id="supplier" required disabled>
                                        <option value="">── PILIH SUPPLIER ──</option>
                                        @foreach ($supplier as $item)
                                            <option value="{{ $item->id }}" {{ $item->id == $tagihan->id_supplier? 'selected':'' }}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="id_supplier" value="{{ $tagihan->id_supplier }}">
                                    <input type="hidden" name="data_deleted" id="data_deleted">
                                </div>  
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">No. Nota<span style="color:red">*</span></label>
                                    <input type="text" name="no_nota" id="no_nota" maxlength="25" class="form-control" value="{{ $tagihan->no_nota }}">
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Tanggal Nota<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="tgl_nota" class="form-control date" id="tgl_nota" required value="{{ date("d-M-Y", strtotime($tagihan->tgl_nota)) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Jatuh Tempo<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="jatuh_tempo" class="form-control date" id="jatuh_tempo" required value="{{ date("d-M-Y", strtotime($tagihan->jatuh_tempo)) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="" class="text-success">Total Tagihan</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" id="tagihan" name="tagihan" class="form-control uang numaja" value="{{ number_format($tagihan->total_tagihan) }}" readonly>                         
                                    <input type="hidden" id="id_tagihan" name="id_tagihan" value="{{ $tagihan->id }}">
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="1">{{ $tagihan->catatan }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        <div style="overflow: auto;" >

            <a href="#" class="btn btn-primary mb-2" id="addData"><i class="fa fa-plus-square"></i> Tambah Data</a>
            <table class="table table-hover table-bordered table-striped " width='100%' id="tabel_tagihan">
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Subtotal</th>
                        <th style="width: 50px;"></th>
                        
                    </tr>
                </thead>
                </thead>
                <tbody id="hasil">
                    @foreach ($tagihan->getDetails as $key => $item)
                        <tr id="{{ $key }}">
                            <td id="text_deskripsi_{{ $key }}">{{ $item->deskripsi }}</td>
                            <td id="text_jumlah_{{ $key }}">{{ $item->jumlah }}</td>
                            <td id="text_satuan_{{ $key }}">{{ $item->satuan }}</td>
                            <td class="subtotal" id="text_subtotal_{{ $key }}">{{ number_format($item->total_tagihan) }}</td>
                            <td class='text-center' style="text-align:center; width: 50px;">
                                <input type="hidden" id="item_key_{{ $key }}" name="data[{{ $item->id }}][key]" value="{{ $item->id }}" />
                                <input type="hidden" id="item_deskripsi_{{ $key }}" name="data[{{ $item->id }}][deskripsi]" value="{{ $item->deskripsi }}" />
                                <input type="hidden" id="item_jumlah_{{ $key }}" name="data[{{ $item->id }}][jumlah]" value="{{ $item->jumlah }}" />
                                <input type="hidden" id="item_satuan_{{ $key }}" name="data[{{ $item->id }}][satuan]" value="{{ $item->satuan }}" />
                                <input type="hidden" id="item_subtotal_{{ $key }}" name="data[{{ $item->id }}][total_tagihan]" value="{{ $item->total_tagihan }}" />
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        <button type="button" class="dropdown-item edit" value="{{ $item->id }}">
                                            <span class="fas fa-pen-alt mr-3"></span> Edit
                                        </button>
                                        <button type="button" class="dropdown-item delete" value="{{ $item->id }}">
                                            <span class="fas fa-trash-alt mr-3"></span> Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>

    <div class="modal fade" id="modal_detail" tabindex='-1'>
        <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Tambah Data</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id='form_add_detail'>
                    {{--* dipakai buat simpen id_sewa --}}
                    <input type="hidden" name="key" id="key" value="{{ count($tagihan->getDetails)-1 }}">  
                    <input type="hidden" id="id_tr">  
    
                    <div class="row">
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                            <label for="">Deskripsi<span class="text-red">*</span> </label>
                            <input type="text" id="modal_deskripsi" class="form-control">
                        </div>
                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                            <label for="">Jumlah<span class="text-red">*</span> </label>
                            <input type="text" id="modal_jumlah" class="form-control numaja" maxlength="5" >
                        </div>
                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                            <label for="">Satuan</label>
                            <input type="text" id="modal_satuan" class="form-control" >
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                            <label for="">Subtotal<span class="text-red">*</span> </label>
                            <div class="input-group mb-0">
                              <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                              </div>
                              <input type="text" name="modal_subtotal" class="form-control numaja uang" id="modal_subtotal"> 
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                <button type="button" class="btn btn-sm btn-success save_detail" style='width:85px'>OK</button> 
            </div>
        </div>
        </div>
    </div>
</section>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('#save').submit(function(event) {
            // cek total_dibayar
                var total_dibayar = $('#total_dibayar').val();
                console.log('total_dibayar', total_dibayar);
                if(escapeComma(total_dibayar) == 0 || escapeComma(total_dibayar) == ''){
                    Swal.fire(
                        'Data tidak valid',
                        'Total bayar masih 0, harap periksa kembali data anda!',
                        'warning'
                    )
                    return false;
                }
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
        var today = new Date();
        $('#tgl_nota').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            // startDate: today,
        });
        $('#jatuh_tempo').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            // startDate: today,
        });

        $(document).on('click', '#addData', function(event){
            clear();

            $('#modal_detail').modal('show');
        });

        $(document).on('click', '.save_detail', function(event){
            const deskripsi = $('#modal_deskripsi').val();
            const jumlah    = $('#modal_jumlah').val();
            const satuan    = $('#modal_satuan').val();
            const subtotal  = $('#modal_subtotal').val();
            const id_tr     = $('#id_tr').val();
            console.log('id_tr', id_tr)
            if(id_tr != '' && id_tr != null && id_tr >= 0){
                // update
                $('#item_deskripsi_'+id_tr).val( $('#modal_deskripsi').val() );
                $('#item_jumlah_'+id_tr).val( $('#modal_jumlah').val() );
                $('#item_satuan_'+id_tr).val( $('#modal_satuan').val() );
                $('#item_subtotal_'+id_tr).val( $('#modal_subtotal').val() );

                document.getElementById('text_deskripsi_'+id_tr).textContent = $('#modal_deskripsi').val();
                document.getElementById('text_jumlah_'+id_tr).textContent = $('#modal_jumlah').val();
                document.getElementById('text_satuan_'+id_tr).textContent = $('#modal_satuan').val();
                document.getElementById('text_subtotal_'+id_tr).textContent = $('#modal_subtotal').val();
            }else{
                // save baru
                let key = $('#key').val();
                if(key == '' || key == null){
                    $('#key').val(0);
                }
                key++;

                var row = $(`<tr id="${key}"></tr>`);
                row.append(`<td>${deskripsi}</td>`);
                row.append(`<td>${jumlah}</td>`)
                row.append(`<td>${satuan}</td>`)
                row.append(`<td class="subtotal">${subtotal}</td>`);
                row.append(`<td>
                                <input type="hidden" id="item_deskripsi_${key}" name="data_baru[${key}][deskripsi]" value="${deskripsi}" />
                                <input type="hidden" id="item_jumlah_${key}" name="data_baru[${key}][jumlah]" value="${jumlah}" />
                                <input type="hidden" id="item_satuan_${key}" name="data_baru[${key}][satuan]" value="${satuan}" />
                                <input type="hidden" id="item_subtotal_${key}" name="data_baru[${key}][subtotal]" value="${subtotal}" />
                                <div class="btn-group dropleft">
                                <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu" >
                                            <button type="button" class="dropdown-item edit">
                                                <span class="fas fa-pen-alt mr-3"></span> Edit
                                            </button>
                                            <button type="button" class="dropdown-item delete">
                                                <span class="fas fa-trash-alt mr-3"></span> Delete
                                            </button>
                                        </div>
                                    </div>
                            </td>`);
                $("#hasil").append(row);
                $('#key').val(key);
            }

            hitung()
            $('#id_tr').val('');
            $('#modal_detail').modal('hide');
        });

        $(document).on('click', '.edit', function(event){
            clear();
            var row = this.closest("tr");
            id = row.id;
            $('#id_tr').val(id);

            $('#modal_deskripsi').val( $('#item_deskripsi_'+id).val() );
            $('#modal_jumlah').val( $('#item_jumlah_'+id).val() );
            $('#modal_satuan').val( $('#item_satuan_'+id).val() );
            $('#modal_subtotal').val( moneyMask($('#item_subtotal_'+id).val()) );

            $('#modal_detail').modal('show');
        });

        $(document).on('click', '.delete', function(event){
            let id = this.value;
            let deleted = $('#data_deleted').val();
            
            if(id > 0){
                if(deleted != ''){
                    id = deleted + ','+id;
                }
                $('#data_deleted').val(id);
            }

            var row = this.closest("tr");
            row.remove();

            hitung()
        });

        function clear(){
            $('#modal_deskripsi').val('');
            $('#modal_jumlah').val('');
            $('#modal_satuan').val('');
            $('#modal_subtotal').val('');
        }

        function hitung(){
            let total = 0;
            var subtotalElements = document.querySelectorAll(".subtotal");
            subtotalElements.forEach(function (element) {
                total += normalize(element.textContent);
            });

            $("#tagihan").val(moneyMask(total));
        }
    });
</script>

@endsection


