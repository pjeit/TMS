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
    <form action="{{ route('tagihan_pembelian.store') }}" id="save" method="POST" >
        @csrf
        <div class="radiusSendiri sticky-top" style="margin-bottom: -15px;">
            <div class="card radiusSendiri" style="">
                <div class="card-header radiusSendiri">
                    <a href="{{ route('tagihan_pembelian.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        <div class="card radiusSendiri">
            <div class="card-body radiusSendiri" >
                <div class="row">
                    <div class="bg-gray-light radiusSendiri col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">Supplier<span class="text-red">*</span> </label>
                                    <select name="supplier" class="select2" style="width: 100%" id="supplier" required>
                                        <option value="">── PILIH SUPPLIER ──</option>
                                        @foreach ($supplier as $item)
                                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>  
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">No. Nota<span style="color:red">*</span></label>
                                    <input type="text" name="no_nota" id="no_nota" maxlength="25" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Tanggal Nota<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="tgl_nota" class="form-control date" id="tgl_nota" required>
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
                                        <input type="text" autocomplete="off" name="jatuh_tempo" class="form-control date" id="jatuh_tempo" required>
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
                                    <input type="text" id="tagihan" name="tagihan" class="form-control uang numaja" value="" readonly required>                         
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="1"></textarea>
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
                <tbody id="hasil">
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
                    <input type="hidden" name="key" id="key"> 
    
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
            
            let key = $('#key').val();
            if(key == '' || key == null){
                $('#key').val(0);
            }
            key++;

            var row = $("<tr></tr>");
            row.append(`<td>${deskripsi}</td>`);
            row.append(`<td>${jumlah}</td>`)
            row.append(`<td>${satuan}</td>`)
            row.append(`<td class="subtotal">${subtotal}</td>`);
            row.append(`<td>
                            <input type="hidden" name="data[${key}][deskripsi]" value="${deskripsi}" />
                            <input type="hidden" name="data[${key}][jumlah]" value="${jumlah}" />
                            <input type="hidden" name="data[${key}][satuan]" value="${satuan}" />
                            <input type="hidden" name="data[${key}][subtotal]" value="${subtotal}" />
                            <button class="delete btn btn-danger"><i class="fa fa-trash-alt"></i></button>
                        </td>`);
            $("#hasil").append(row);
            $('#key').val(key);

            hitung()
            $('#modal_detail').modal('hide');
        });

        $(document).on('click', '.delete', function(event){
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


