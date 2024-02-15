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
    .card-header:first-child {
        border-radius: inherit;
    }
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
<div class="container-fluid">
    <form action="{{ route('job_order.store') }}" id="save" method="POST">
        @csrf
        <div class="row ">
            <div class="col-12 radiusSendiri sticky-top " style="margin-bottom: -15px;">
                <div class="card radiusSendiri" style="">
                    <div class="card-header ">
                        <a href="{{ route('job_order.index') }}" class="btn btn-secondary radiusSendiri"><i
                                class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i
                                class="fa fa-fw fa-save"></i> Simpan</button>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card radiusSendiri">
                    {{-- <div class="card-header sticky-top radiusSendiri" style="background: #f7f7f7;">
                        <a href="{{ route('job_order.index') }}" class="btn btn-secondary radiusSendiri"><i
                                class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i
                                class="fa fa-fw fa-save"></i> Simpan</button>
                    </div> --}}
                    <div class="card-body">
                        {{-- <div class="card-body" style="overflow-y: scroll; max-height:675px;"> --}}
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Pengirim<span class="text-red">*</span></label>
                                        <select class="form-control selectpicker" name="customer" id="customer"
                                            data-live-search="true" data-show-subtext="true" data-placement="bottom"
                                            required>
                                            <option value="">── Pilih Pengirim ──</option>
                                            @foreach ($dataCustomer as $cust)
                                            <option value="{{$cust->id}}" kode="{{$cust->kode}}">[{{ $cust->kode }}] {{
                                                $cust->nama }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id='kode_cust' name='kode_cust'>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label for="">Pelayaran<span class="text-red">*</span></label>
                                        <select class="form-control selectpicker" id='supplier' name="supplier"
                                            data-live-search="true" data-show-subtext="true" data-placement="bottom"
                                            required>
                                            <option value="">── Pilih Pelayaran ──</option>
                                            @foreach ($dataSupplier as $sup)
                                            <option value="{{$sup->id}}">{{ $sup->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group ">
                                        <label for="">No. BL<span class="text-red">*</span></label>
                                        <input required type="text" id="no_bl" name="no_bl" class="form-control"
                                            maxlength="25">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="tgl_sandar">Tanggal Sandar<span class="text-red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" name="tgl_sandar" autocomplete="off"
                                                class="date form-control" id="tgl_sandar" placeholder="dd-M-yyyy"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group ">
                                        <label for="">Nama Kapal<span class="text-red">*</span></label>
                                        <input required type="text" id="kapal" name="kapal" maxlength="30"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-12">
                                    <div class="form-group ">
                                        <label for="">Voyage<span class="text-red">*</span></label>
                                        <input required type="text" id="voyage" name="voyage" maxlength="5"
                                            class="form-control" placeholder="Max. 5 char">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Pelabuhan Muat<span class="text-red">*</span></label>
                                        <select class="form-control select2" name="pelabuhan_muat" id="pelabuhan_muat"
                                            data-live-search="true" data-show-subtext="true" data-placement="bottom"
                                            required>
                                            <option value="">── Pilih ──</option>
                                            @foreach ($kota as $city)
                                            <option value="{{ $city->nama }}">{{ $city->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Pelabuhan Bongkar<span class="text-red">*</span></label>
                                        <select class="form-control select2" name="pelabuhan_bongkar"
                                            id="pelabuhan_bongkar" data-live-search="true" data-show-subtext="true"
                                            data-placement="bottom" required>
                                            <option value="">── Pilih ──</option>
                                            @foreach ($kota as $city)
                                            <option value="{{ $city->nama }}">{{ $city->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <label for="tanggal_keluar">No. VA <sup style="opacity: 60%">(Centang jika virtual account)</sup></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <input type="checkbox" id="isVA" value="Y">
                                            </span>
                                        </div>
                                        <input required type="text" id="no_va" name="no_va" class="form-control numaja" readonly> 
                                    </div>
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <label for="">Atas Nama</label>
                                    <input required type="text" id="va_nama" name="va_nama" class="form-control" readonly>                         
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <label for="">Bank</label>
                                    <input required type="text" id="va_bank" name="va_bank" class="form-control" readonly>                         
                                </div>
                            </div>


                            <button type="button" id="addmore" class="btn btn-primary radiusSendiri mb-2 mt-2">
                                <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Kontainer
                            </button>

                            <table class="table table-bordered" id="sortable">
                                <thead>
                                    <tr>
                                        <th width="350">No. Kontainer</th>
                                        <th width="280">Seal</th>
                                        <th width="150">Tipe</th>
                                        <th width="150">Stripping</th>
                                        <th width="150">Pick Up</th>
                                        <th width="350">Tujuan</th>
                                        <th width="200">Tgl Booking</th>
                                        <th width="20" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="tb">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card radiusSendiri">
                        <div class="card-header">
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="d-flex justify-content-between" style="gap: 10px;">
                                <table class="table table-bordered card-outline card-primary" id="tabel_biaya_belum_dooring">
                                    <thead>
                                        <tr>
                                            <th colspan="3">BIAYA SEBELUM DOORING</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>
                                                <input type="checkbox" class="checkitem" name="checkbox_THC" id="thc_cekbox"> THC
                                            </th>
                                            <td>
                                                <input type="text" id="thc_null" class="form-control" value="0" readonly>
                                                <input type="text" name="total_thc" id="total_thc" value="0" class="form-control uang numaja" readonly hidden>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <input type="checkbox" class="checkitem" name="checkbox_LOLO" id="lolo_cekbox"> LOLO
                                            </th>
                                            <td>
                                                <input type="text" id="lolo_null" class="form-control" value="0" readonly>
                                                <input type="text" name="total_lolo" id="total_lolo" value="0" class="form-control uang numaja" readonly hidden>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <input type="checkbox" class="checkitem" name="checkbox_APBS" id="apbs_cekbox"> APBS
                                            </th>
                                            <td>
                                                <input type="text" id="apbs_null" class="form-control" value="0" readonly>
                                                <input type="text" name="total_apbs" id="total_apbs" value="0" class="form-control uang numaja" readonly hidden>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <input type="checkbox" class="checkitem" name="checkbox_CLEANING" id="cleaning_cekbox"> CLEANING
                                            </th>
                                            <td>
                                                <input type="text" id="cleaning_null" class="form-control" value="0" readonly>
                                                <input type="text" name="total_cleaning" id="total_cleaning" value="0" class="form-control uang numaja" readonly hidden>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <input type="checkbox" class="checkitem" name="checkbox_DOC_FEE" id="doc_fee_cekbox"> DOC FEE
                                            </th>
                                            <td>
                                                <input type="text" id="doc_fee_null" class="form-control" value="0" readonly>
                                                <input type="text" name="DOC_FEE" id="DOC_FEE" value="0" class="form-control uang numaja" readonly hidden>
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <td colspan="2">
                                                Biaya Pelayaran
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="">Deskripsi</label>
                                            </td>
                                            <td>
                                                <label for="">Biaya</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <div class="form-group">
                                                    <input type="text" id="deskripsi_1" name="data_lain[deskripsi][0]" class="form-control desksipsi_lain" >                         
                                                </div>
                                            </th>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" id="biaya_1" name="data_lain[biaya][0]" class="form-control biaya_lain uang numaja" >                         
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <div class="form-group">
                                                    <input type="text" id="deskripsi_2" name="data_lain[deskripsi][1]" class="form-control desksipsi_lain" >                         
                                                </div>
                                            </th>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" id="biaya_2" name="data_lain[biaya][1]" class="form-control biaya_lain uang numaja" >                         
                                                </div>
                                            </td>
                                        </tr> --}}
                                        <tr>
                                            <th>SUB TOTAL</th>
                                            <td>
                                                <input type="text" name="total_sblm_dooring" id="total_sblm_dooring" value="0" class="form-control uang numaja" readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <input type="hidden" id="indexBiayaLain" value="0">
                                                <button type="button" id="tambah_biaya_lain" class="btn btn-success float-right radiusSendiri mb-2 mt-2">
                                                    <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Biaya
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                                <table class="table table-bordered card-outline card-primary" id="sortable">
                                    <thead>
                                        <tr>
                                            <th colspan="2">BIAYA JAMINAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="height: 5px;">Tgl Bayar Jaminan</th>
                                            <td>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                class="far fa-calendar-alt"></i></span>
                                                    </div>
                                                    <input type="text" name="tgl_bayar_jaminan" autocomplete="off"
                                                        class="date form-control" id="tgl_bayar_jaminan"
                                                        placeholder="dd-M-yyyy" value="">
                                                    <input type="hidden" name="id_jaminan" value="">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="height: 5px;">Total Jaminan</th>
                                            <td>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><b>Rp.</b></span>
                                                    </div>
                                                    <input type="text" class="form-control uang numaja"
                                                        id="total_jaminan" name="total_jaminan" value="">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Catatan</th>
                                            <td>
                                                <textarea name="catatan" class="form-control" id="catatan" cols="50"
                                                    rows="10"></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
    </form>
</div>


{{-- sweet save --}}
<script>
    $(document).ready(function() {
        $('#save').submit(function(event) {
            event.preventDefault();

            var cekBookingTujuan = 1;
            $("[tgl_booking_check]").each(function() {
                var i = $(this).attr("tgl_booking_check");
                var tujuanVal = $("#tujuan" + i).val();
                var tglBookingVal = $("#tgl_booking" + i).val();

                if (tujuanVal != "" || tglBookingVal != "") {
                    console.log('tglBookingVal '+tglBookingVal);
                    console.log('tujuanVal '+tujuanVal);
                    if(tujuanVal == ""){
                        cekBookingTujuan = 0;
                    }
                }
            });
            if(cekBookingTujuan == 0){
                Swal.fire(
                    'Terjadi kesalahan!',
                    'Tujuan wajib diisi ketika ada tanggal booking!',
                    'warning'
                )
                event.preventDefault();
                return false;
            }
            // pop up confirmation
                Swal.fire({
                    title: 'Apakah Anda yakin data sudah benar?',
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
                        var cust = $('#customer').val();
                        var nobl = $('#no_bl').val();
                        var plmt = $('#pelabuhan_muat').val();
                        var plbn = $('#pelabuhan_bongkar').val();

                        if( cust == ''|| nobl == ''|| plmt == ''|| plbn == '' ){
                            Swal.fire(
                                'Data tidak lengkap!',
                                'Cek ulang data anda.',
                                'warning'
                            )
                            
                            event.preventDefault();
                            return false;
                        }else{
                            this.submit();
                        }
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
                        // return;
                    }
                })
            // pop up confirmation
        });

        
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        // master harga tipe
            var dataKeuangan = <?php echo json_encode($dataPengaturanKeuangan[0]); ?>;
            var harga20Ft = {
                'thcLuar': dataKeuangan.thc_20ft_luar,
                'thcDalam': dataKeuangan.thc_20ft_dalam,
                'loloLuar': dataKeuangan.lolo_20ft_luar,
                'loloDalam': dataKeuangan.lolo_20ft_dalam,
                'apbs': dataKeuangan.apbs_20ft,
                'cleaning': dataKeuangan.cleaning_20ft,
            };
            var harga40Ft = {
                'thcLuar': dataKeuangan.thc_40ft_luar,
                'thcDalam': dataKeuangan.thc_40ft_dalam,
                'loloLuar': dataKeuangan.lolo_40ft_luar,
                'loloDalam': dataKeuangan.lolo_40ft_dalam,
                'apbs': dataKeuangan.apbs_40ft,
                'cleaning': dataKeuangan.cleaning_40ft,
            };
            // console.log('harga20Ft '+ JSON.stringify(harga20Ft));
            // console.log('harga40Ft '+ JSON.stringify(harga40Ft));
        // end of master harga tipe
    
        // handling tanggal
            $('#tgl_sandar').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language:'en',
                orientation: 'bottom',
            });
            $('#tgl_bayar_jaminan').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
            });
            $(document).on('focus', '.tgl_booking', function() {
                $(this).datepicker({
                    autoclose: true,
                    format: "dd-M-yyyy",
                    todayHighlight: true,
                    language: 'en',
                });
            });
        // end of handling tgl
        
        $(document).on('click', '#isVA', function(event) {
            $('#no_va').prop('readonly', !this.checked);
            $('#va_nama').prop('readonly', !this.checked);
            $('#va_bank').prop('readonly', !this.checked);

            if(this.checked == false){
                $('#no_va').val('');
                $('#va_nama').val('');
                $('#va_bank').val('');
            }
        });
        $(document).on('change', '#customer', function(event) {
            // Get selected value from #customer
            var selectedValue = this.value;

            // get kode customer
                var selectElement = document.getElementById('customer');
                var selectedOption = selectElement.options[selectElement.selectedIndex];
                var kodeValue = selectedOption.getAttribute('kode');
                $('#kode_cust').val(kodeValue.trim());
            //
            var baseUrl = "{{ asset('') }}";
                
            $.ajax({
                url: `${baseUrl}booking/getTujuan/${selectedValue}`,
                method: 'GET',
                success: function(response) { 
                    // get semua data dropdown dengan class ini trus di kosongin
                    $('.form-control.selectpicker.tujuanC').empty().append('<option value="">── Pilih Tujuan ──</option>');

                    response.forEach(tujuan => {
                    var option = new Option(tujuan.nama_tujuan, tujuan.id);
                        $('.form-control.selectpicker.tujuanC').append('<option value="'+tujuan.id+'">'+tujuan.nama_tujuan+'</option>');
                    });
                    $('.form-control.selectpicker.tujuanC').selectpicker({
                    });
                    noneSelectedText: "── Pilih Tujuan ──"
                    $(".form-control.selectpicker.tujuanC").selectpicker("refresh");
                },
                error: function(xhr, status, error) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
                        timer: 3000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan.'
                    })
                    console.error(error); // Handle errors if necessary
                }
            });
        });

        $(document).on('click', '#tambah_biaya_lain', function(event) {
        // $('#tambah_biaya_lain').on('click', function () {
            // Append a new row after the specified <tr>
            var counter= $("#indexBiayaLain").val();
            counter++;
            var newRow = `<tr id='${counter}'>
                            <th>
                                <div class="form-group">
                                    <input type="text" id="deskripsi_${counter}" name="data_lain[${counter}][deskripsi]" class="form-control desksipsi_lain" >                         
                                </div>
                            </th>
                            <td>
                                <div class="form-group">
                                    <input type="text" id="biaya_${counter}" name="data_lain[${counter}][biaya]" class="form-control biaya_lain uang numaja" >                         
                                </div>
                            </td>
                            <td>
                                <button type="button" data-toggle="tooltip" data-placement="right" title="Hapus" data-row="${counter}" class="btn btn-sm btn-danger deleteRow">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>`
            $("#indexBiayaLain").val(counter);
            // Append the new row after the specified <tr>
            $('#tabel_biaya_belum_dooring tbody tr').eq(4).append().after(newRow);
        });
          // Delete button click event
        $(document).on('click', '.deleteRow', function () {
            // // Get the data-row attribute to identify the row
            // var rowToDelete = $(this).data('row');
            
            // // Remove the corresponding row
            // $('#sortable tbody tr').eq(rowToDelete).remove();
            var counter = $('#indexBiayaLain').val();
            $(this).closest('tr').remove();
            hitungTotal();
            if($(this).closest('tr').attr('id') == counter)
            {
                counter--;
                $("#indexBiayaLain").val(counter);

            }
            $('#maxIndex').val(counter);
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
                icon: 'success',
                title: 'Data biaya dihapus'
            })
        });
        

        $("#addmore").on("click",function(event){
            var customerId = $("#customer").val();
            if(customerId == 0 || customerId == null || customerId == ''){
                Swal.fire(
                    '',
                    'Harap isi data pengirim dahulu.',
                    'error'
                );
                return false;
            }

            var selectedValue = customerId;
            let dataOption = ''; // Initialize as an array

            // logic kasih id di row
                // Ambil semua elemen <tr> dengan ID yang dimulai dengan "row"
                var rows = document.querySelectorAll('tr[id^="row"]');
                console.log(rows);

                // Cari ID terbesar dengan format "rowX" dan ambil nilai X-nya
                var maxID = -1;
                for (var i = 0; i < rows.length; i++) {
                    //misal row1 jadi 1 doang yang diambil
                    var idStr = rows[i].id.replace('row', ''); // Ambil nilai X dari "rowX"
                    console.log(idStr);

                    var idNum = parseInt(idStr); // Konversi menjadi angka
                    //terus kalau 1 >-1
                    if (idNum > maxID) {
                        //maka maxID = 1
                        maxID = idNum;
                    }
                }   

                // Hasilkan ID terakhir dengan format "rowX+1"
                //misal maxid = 1, pad diappend jadi 1+1=2
                var lastID = (maxID + 1);

                if(lastID != 0){
                    var i = lastID-1;
                }else{
                    var i = 0;
                }
                var length;
            // end of logic

            // get tujuan
            var baseUrl = "{{ asset('') }}";
            $.ajax({
                url:  `${baseUrl}booking/getTujuan/${selectedValue}`,
                method: 'GET',
                success: function(response) {
                    response.forEach(tujuan => {
                        const option = document.createElement('option');
                        var xxx = `<option value="${tujuan.id}">${tujuan.nama_tujuan}</option>`;
                        // store data ke dataOption buat di fetch ketika tambah data
                        dataOption += xxx;
                    });
                    i++;

                    $('#tb').append(
                        `<tr id="row`+i+`">
                            <td>
                                <input type="text" id="no_kontainer" name="detail[${i}][no_kontainer]"class="form-control no_kontainerx" maxlength="20" value="">
                            </td>
                            <td>
                                <input type="text" id="seal" name="detail[${i}][seal]"class="form-control" maxlength="10" value="">
                            </td>
                            <td>
                                <select class="form-control selectpicker tipeKontainer" name="detail[${i}][tipe]" id="tipe${i}" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                    <option value='20' selected>20"</option>
                                    <option value='40' >40"</option>
                                </select>
                                <input type="hidden" readonly id="h_thc${i}" class="THC" THC_${i} name="detail[${i}][biaya][THC]" value="">
                                <input type="hidden" readonly id="h_lolo${i}" class="LOLO" LOLO_${i} name="detail[${i}][biaya][LOLO]" value="">
                                <input type="hidden" readonly id="h_apbs${i}" class="APBS" APBS_${i} name="detail[${i}][biaya][APBS]" value="">
                                <input type="hidden" readonly id="h_cleaning${i}" class="CLEANING" CLEANING_${i} name="detail[${i}][biaya][CLEANING]" value="">
                                
                            </td>
                            <td>
                                <div class="form-group mb-0">
                                    <div class="icheck-primary">
                                        <input id="thcLuar${i}" dataId="${i}" class="stripping" type="radio" name="detail[${i}][stripping]" value="luar" checked>
                                        <label class="form-check-label" for="thcLuar${i}">Luar</label>
                                    </div>
                                    <div class="icheck-primary mt-3">
                                        <input id="thcDalam${i}" dataId="${i}" class="stripping" type="radio" name="detail[${i}][stripping]" value="dalam" >
                                        <label class="form-check-label" for="thcDalam${i}">Dalam</label><br>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select class="form-control selectpicker pick_up" name="detail[${i}][pick_up]" id="pick_up${i}" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                    <option value="">─ Pick Up ─</option>
                                    <option value="TTL">TTL</option>
                                    <option value="TPS">TPS</option>
                                    <option value="DEPO">DEPO</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-control selectpicker tujuanC" tujuan_check="${i}" name="detail[${i}][tujuan]" id="tujuan${i}" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                    <option value="">── Pilih Tujuan ──</option>
                                    `+dataOption+`
                                </select>
                            </td>
                            <td>
                                <input type="text"  name="detail[${i}][tgl_booking]" id="tgl_booking${i}" tgl_booking_check="${i}" autocomplete="off" class="date form-control tgl_booking" placeholder="dd-M-yyyy" value="">     
                            </td>
                            <td align="center" class="text-danger">
                                <button type="button" data-toggle="tooltip" data-placement="right" title="Hapus" class="btn btn-sm btn-danger hapus">
                                    <i class="fa fa-fw fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>`
                    );

                    getThc(i);
                    uncheck();
                    calculateTotalHarga();
                    hitungTotal();

                    $('input[type="text"]').on("input", function () {
                        var inputValue = $(this).val();
                        var uppercaseValue = inputValue.toUpperCase();
                        $(this).val(uppercaseValue);
                    });

                    uncheck();
                    $('.selectpicker').selectpicker('refresh');

                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

            // $('#save').removeAttr('hidden',true);
        });

        $( document ).on( 'click', '.hapus', function (event) {
            $(this).closest('tr').remove();
            
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
                title: 'Data dihapus'
            })
        });

        // handling checkbox biaya dibawah
            var tmpTot = 0;
            $('#thc_cekbox').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#thc_null').prop('hidden', true);
                    $('#total_thc').prop('hidden', false);
                    hitungTotal();
                } else {
                    $('#thc_null').prop('hidden', false);
                    $('#total_thc').prop('hidden', true);
                    hitungTotal();
                }
            });
            $('#lolo_cekbox').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#lolo_null').prop('hidden', true);
                    $('#total_lolo').prop('hidden', false);
                    hitungTotal();
                } else {
                    $('#lolo_null').prop('hidden', false);
                    $('#total_lolo').prop('hidden', true);
                    hitungTotal();
                }
            });
            $('#apbs_cekbox').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#apbs_null').prop('hidden', true);
                    $('#total_apbs').prop('hidden', false);
                    hitungTotal();
                } else {
                    $('#apbs_null').prop('hidden', false);
                    $('#total_apbs').prop('hidden', true);
                    hitungTotal();
                }
            });
            $('#cleaning_cekbox').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#cleaning_null').prop('hidden', true);
                    $('#total_cleaning').prop('hidden', false);
                    hitungTotal();
                } else {
                    $('#cleaning_null').prop('hidden', false);
                    $('#total_cleaning').prop('hidden', true);
                    hitungTotal();
                }
            });
            $('#doc_fee_cekbox').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#doc_fee_null').prop('hidden', true);
                    $('#DOC_FEE').prop('hidden', false);
                    hitungTotal();
                } else {
                    $('#doc_fee_null').prop('hidden', false);
                    $('#DOC_FEE').prop('hidden', true);
                    hitungTotal();
                }
            });
        // end of it

        // logic hitung biaya
            $( document ).on( 'change', '.tipeKontainer', function (event) {
                // biar datanya ga ke get 2x ketika di get val
                event.stopPropagation();
                
                var selectedId = $(this).attr('id');
                var id = selectedId.replace('tipe', ''); // Remove 'thc' from the beginning
                
                getThc(id);
                uncheck();
                // tiap ada perubahan di class tipekontainer, di akhir akan di hitung total harganya
                calculateTotalHarga();
                hitungTotal();
            });

            function getThc(id){
                var tipeKontainer = $('#tipe'+id).val();
                var stripping = $("input[name='detail[" + id + "][stripping]']:checked").val();
                
                console.log('tipeKontainer '+ tipeKontainer);
                console.log('stripping '+ stripping);
                console.log('harga40Ft.thcLuar '+ harga40Ft.thcLuar);

                if(tipeKontainer == '20'){
                    $('#h_thc'+id).val(stripping == 'luar' ? harga20Ft.thcLuar : harga20Ft.thcDalam);
                    $('#h_lolo'+id).val(stripping == 'luar' ? harga20Ft.loloLuar : harga20Ft.loloDalam);
                }else if(tipeKontainer == '40'){
                    $('#h_thc'+id).val(stripping == 'luar' ? harga40Ft.thcLuar : harga40Ft.thcDalam);
                    $('#h_lolo'+id).val(stripping == 'luar' ? harga40Ft.loloLuar : harga40Ft.loloDalam);
                }else{
                    $('#h_thc'+id).val(0);
                    $('#h_lolo'+id).val(0);
                }
                $('#h_apbs' + id).val(tipeKontainer === '20' || tipeKontainer === '40' ? (tipeKontainer === '20' ? harga20Ft.apbs : harga40Ft.apbs) : 0);
                $('#h_cleaning' + id).val(tipeKontainer === '20' || tipeKontainer === '40' ? (tipeKontainer === '20' ? harga20Ft.cleaning : harga40Ft.cleaning) : 0);
            }

            $( document ).on( 'change', '.stripping', function (event) {
                var dataId = $(this).attr('dataId');
                var tk = $(`#tipe${dataId}`).val();

                getThc(dataId);
                uncheck();
                calculateTotalHarga();
                hitungTotal();
            });

            
            $( document ).on( 'click', '.hapus', function (event) {
                // ketika hapus data, di hitung lagi total harganya
                uncheck();
                calculateTotalHarga();
                hitungTotal();
            });

            $( document ).on( 'keyup', '.biaya_lain', function (event) {
                hitungTotal();
            });
            function hitungTotal(){
                var total_thc = parseFloat(($('#thc_cekbox').prop('checked')) ? parseFloat($('#total_thc').val().replace(/,/g, '')) : 0);
                var total_lolo = parseFloat(($('#lolo_cekbox').prop('checked')) ? parseFloat($('#total_lolo').val().replace(/,/g, '')) : 0);
                var total_apbs = parseFloat(($('#apbs_cekbox').prop('checked')) ? parseFloat($('#total_apbs').val().replace(/,/g, '')) : 0);
                var total_cleaning = parseFloat(($('#cleaning_cekbox').prop('checked')) ? parseFloat($('#total_cleaning').val().replace(/,/g, '')) : 0);
                var DOC_FEE = parseFloat(($('#doc_fee_cekbox').prop('checked')) ? parseFloat($('#DOC_FEE').val().replace(/,/g, '')) : 0);
                
                var total_biaya_lain = 0;
                $('.biaya_lain').each(function () {
                    var biaya_lain_value = $(this).val() ? parseFloat($(this).val().replace(/,/g, '')) : 0;
                    total_biaya_lain += biaya_lain_value;
                });

                var total = parseFloat(total_thc + total_lolo + total_apbs + total_cleaning + DOC_FEE + total_biaya_lain);

                var total_sblm_dooring = $('#total_sblm_dooring').val(total.toLocaleString());
            }

            function uncheck(){
                // $('.checkitem').each(function() {
                //     this.checked = false; 
                // });  

                // $('#thc_null').prop('hidden', false);
                // $('#total_thc').prop('hidden', true);

                // $('#lolo_null').prop('hidden', false);
                // $('#total_lolo').prop('hidden', true);
   
                // $('#apbs_null').prop('hidden', false);
                // $('#total_apbs').prop('hidden', true);

                // $('#cleaning_null').prop('hidden', false);
                // $('#total_cleaning').prop('hidden', true);

                // $('#doc_fee_null').prop('hidden', false);
                // $('#DOC_FEE').prop('hidden', true);
            }

            function calculateTotalHarga() {
                var totalTHC = 0;
                var totalLOLO = 0;
                var totalAPBS = 0;
                var totalCLEANING = 0;
                var totalhargaDocFee = 0;

                $('#total_thc').val(totalTHC);
                $('#total_lolo').val(totalLOLO);
                $('#total_apbs').val(totalAPBS);
                $('#total_cleaning').val(totalCLEANING);
                $('#DOC_FEE').val(dataKeuangan.doc_fee.toLocaleString());
                
                $('.THC').each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    totalTHC += value;
                    $('#total_thc').val(totalTHC.toLocaleString());
                });
                $('.LOLO').each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    totalLOLO += value;
                    $('#total_lolo').val(totalLOLO.toLocaleString());
                });
                $('.APBS').each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    totalAPBS += value;
                    $('#total_apbs').val(totalAPBS.toLocaleString());
                });
                $('.CLEANING').each(function() {
                    var value = parseFloat($(this).val()) || 0;
                    totalCLEANING += value;
                    $('#total_cleaning').val(totalCLEANING.toLocaleString());
                });
            }
        // end of logic hitung biaya
    });
</script>

@endsection