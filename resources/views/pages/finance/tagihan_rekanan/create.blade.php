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
    <form action="{{ route('tagihan_rekanan.store') }}" id="save" method="POST">
        @csrf
        <div class="radiusSendiri sticky-top" style="margin-bottom: -15px;">
            <div class="card radiusSendiri" style="">
                <div class="card-header radiusSendiri">
                    <a href="{{ route('tagihan_rekanan.index') }}" class="btn btn-secondary radiusSendiri"><i
                            class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i
                            class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        <div class="card radiusSendiri">
            <div class="card-body radiusSendiri">
                <div class="row">
                    <div class="bg-gray-light radiusSendiri col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">Supplier<span class="text-red">*</span> </label>
                                    <select name="supplier" class="select2" style="width: 100%" id="supplier" required>
                                        <option value="">── PILIH SUPPLIER ──</option>
                                        @foreach ($supplier as $item)
                                        @if ($item->getSupplier)
                                        <option n value="{{ $item->getSupplier->id }}">{{ $item->getSupplier->nama }}
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="">No. Nota<span style="color:red">*</span></label>
                                    <input type="text" name="no_nota" id="no_nota" maxlength="25" class="form-control"
                                        required>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="">Tanggal Nota<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="tgl_nota" class="form-control date" value="{{ date("d-M-Y", strtotime(now())) }}" id="tgl_nota" required>
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
                                        <input type="text" autocomplete="off" name="jatuh_tempo"
                                            class="form-control date" id="jatuh_tempo" required>
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
                                    <input type="text" id="tagihan" name="tagihan" class="form-control uang numaja"
                                        value="" readonly required>
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

        <div style="overflow: auto;">
            <table class="table table-hover table-bordered table-striped " width='100%' id="tabel_tagihan">
                <thead>
                    <tr>
                        <th>Data Sewa</th>
                        <th style="width: 200px;">Tarif</th>
                        <th style="width: 200px;">Ditagihkan</th>
                        <th>Catatan</th>
                        <th style="width: 50px;" class='text-center' style="text-align:center">
                            {{-- <input type="checkbox" class="check_all"> --}}
                        </th>
                    </tr>
                </thead>
                <tbody id="hasil">

                </tbody>
            </table>
        </div>
    </form>
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
        $(document).on('change', '#supplier', function(){
            if(this.value != null){
                showTable(this.value);
            }
        });
        
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

        function showTable(supplier){
            var baseUrl = "{{ asset('') }}";
            var url = `${baseUrl}tagihan_rekanan/load_data/${supplier}`;
    
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    var data = response;
                    console.log('data', data);
                    $('#tabel_tagihan').DataTable().clear().destroy();

                    for (var i = 0; i < data.length; i++) {
                        var row = $("<tr></tr>");
                        row.append(`<td>${data[i].get_customer.kode} - ${data[i].nama_tujuan} - (${ dateMask(data[i].tanggal_berangkat)})</td>`);
                        row.append(`<td>${moneyMask(data[i].harga_jual)}
                            </td>`)
                        row.append(`<td>
                                <input type="hidden" id="hidden_harga_jual_${data[i].id_sewa}" value="${data[i].harga_jual}" />
                                <input type="text" class="form-control ditagihkan uang numaja" name="data[${data[i].id_sewa}][ditagihkan]" id="${data[i].id_sewa}" readonly/>
                                    </td>`)
                        row.append(`<td><input type="text" readonly name="data[${data[i].id_sewa}][catatan]" class="form-control" id="catatan_${data[i].id_sewa}" /></td>`)
                        row.append(`<td class='text-center' style="text-align:center">
                                        <input type="checkbox" class="checkHitung check_item" value="${data[i].id_sewa}">
                                    </td>`);
                        $("#hasil").append(row);
                    }

                    $('input[type="text"]').on("input", function () {
                        var inputValue = $(this).val();
                        var uppercaseValue = inputValue.toUpperCase();
                        $(this).val(uppercaseValue);
                    });
                
                },error: function (xhr, status, error) {
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
                        icon: 'error',
                        title: 'Terjadi kesalahan: '+error
                    })
                }
            });
        };    

        $(document).on('click', '.check_all', function (event) {
            $(".check_item").prop('checked', this.checked);
        });

        $(document).on('click', '.checkHitung', function (event) {
            const harga_jual = document.getElementById('hidden_harga_jual_'+this.value);
            const inputElement = document.getElementById(this.value);
            const catatanElement = document.getElementById("catatan_"+this.value);
            
            if (this.checked) {
                inputElement.value = moneyMask(harga_jual.value);
                inputElement.removeAttribute("readonly");
                catatanElement.removeAttribute("readonly");
            } else {
                inputElement.value = ""; // Set the value to "0"
                inputElement.setAttribute("readonly", "readonly");

                catatanElement.value = ""; // Set the value to "0"
                catatanElement.setAttribute("readonly", "readonly");
            }
            hitung();
        });  

        $(document).on('keyup', '.ditagihkan', function (event) {
            validation(this)
            hitung();
        });

        // buat make sure agar lebih akurat
        $(document).on('change', '.ditagihkan', function (event) {
            validation(this)
            hitung();
        });

        function validation(data){
            var id = data.getAttribute("id");
            var hiddenValue = $('#hidden_harga_jual_'+id).val();
            if (normalize(data.value) > parseFloat(hiddenValue)) {
                data.value = moneyMask(parseFloat(hiddenValue)); 
            }
        }

        function hitung(){
            const elements = document.querySelectorAll(".ditagihkan");
            let totalValue = 0;

            elements.forEach(element => {
                const value = normalize(element.value);
                if (!isNaN(value)) {
                    totalValue += value;
                }
            });

            $('#tagihan').val(moneyMask(totalValue));
        }
    });
</script>

@endsection