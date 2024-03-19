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
    <form action="{{ route('invoice_resi.store') }}" id="save" method="POST">
        @csrf
        {{-- <div class="radiusSendiri sticky-top" style="margin-bottom: -15px;">
            <div class="card radiusSendiri" style="">
                <div class="card-header radiusSendiri">
                    <a href="{{ route('invoice_resi.index') }}" class="btn btn-secondary radiusSendiri"><i
                            class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i
                            class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div> --}}
        <div class="card radiusSendiri">
            <div class="card-header radiusSendiri sticky-top">
                <a href="{{ route('invoice_resi.index') }}" class="btn btn-secondary radiusSendiri"><i
                        class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i
                        class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body radiusSendiri">
                    <div class="row">
                        {{-- <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="">Customer<span class="text-red">*</span> </label>
                                <select name="billing_to" class="select2" style="width: 100%" id="billing_to" required>
                                    <option value="">── PILIH BILLING TO ──</option>
                                    @foreach ($data_billingto_invoice as $item)
                                    @if ($item->getBillingTo)
                                        <option value="{{ $item->getBillingTo->id }}">{{ $item->getBillingTo->nama }}
                                        </option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="">Tanggal Resi<span style="color:red">*</span></label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" autocomplete="off" name="tgl_resi" class="form-control date" value="{{ date("d-M-Y", strtotime(now())) }}" id="tgl_nota" required>
                            </div>
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="refund">Kurir</label>
                            <select class="form-control select2" name="jenis_pengiriman" data-live-search="true" data-show-subtext="true" data-placement="bottom" width="100">
                                <option value="tiki">TIKI</option>
                                <option value="lion">LION PARCEL</option>
                                <option value="jne">JNE</option>
                                <option value="jnt">JNT</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="">No. Resi<span style="color:red">*</span></label>
                            <input type="text" name="no_resi" id="no_resi" maxlength="12" class="form-control numaja"
                                required>
                        </div>
                       
                        
                    </div>
                <div style="overflow: auto;">
                    <table class="table table-hover table-bordered table-striped " width='100%' id="tabel_tagihan">
                        <thead>
                            <tr>
                                <th>
                                    <div class="icheck-primary d-inline">
                                        <input type="checkbox" id="cekbox_semua" class="cekbox_semua" value="N" name="cekbox_semua">
                                        <label for="cekbox_semua"></label>
                                    </div>
                                </th>
                                <th>Nomor Invoice</th>
                                <th>Billing to</th>
                                <th>Total Tagihan</th>
                                <th>Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody id="hasil">
                            @if (isset($data_invoice))
                                @foreach ($data_invoice as $key => $value)
                                    <tr>
                                        <td >
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkboxPrimary_{{$key}}" class="centang_cekbox" value="N" name="detail[{{$key}}][is_resi]">
                                                <label for="checkboxPrimary_{{$key}}"></label>
                                            </div>
                                        </td>
                                        <td>{{$value->no_invoice}}</td>
                                        <td>{{$value->getBillingTo->nama}}</td>
                                        <td>
                                            {{ number_format($value->total_tagihan)}}
                                            <input type="hidden" id="hidden_id_invoice_{{$key}}" value="{{$value->id}}" name="detail[{{$key}}][id_invoice]"/>
                                            <input type="hidden" id="hidden_no_invoice_{{$key}}" value="{{$value->no_invoice}}" name="detail[{{$key}}][no_invoice]"/>
                                            <input type="hidden" id="hidden_jatuh_tempo_lama_{{$key}}" value="{{$value->jatuh_tempo}}" name="detail[{{$key}}][jatuh_tempo_lama]"/>
                                        </td>
                                        <td>{{date('d-M-Y',strtotime($value->jatuh_tempo))}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</section>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.cekbox_semua', function () {
            var isChecked = $(this).prop("checked");
            $('.centang_cekbox').prop("checked", isChecked).val(isChecked ? 'Y' : 'N');
            // $('.centang_cekbox').prop("disabled", isChecked);

            console.log(isChecked);
            if ($(this).is(":checked")) {
                $(this).val('Y');
            
            } else if ($(this).is(":not(:checked)")) {  
                $(this).val('N')
            }
                
        });
        $(document).on('click', '.centang_cekbox', function () {
            var isChecked = $(this).prop("checked");
            $('.cekbox_semua').prop("checked", false).val('N');
            if ($(this).is(":checked")) {
                $(this).val('Y');
            
            } else if ($(this).is(":not(:checked)")) {  
                $(this).val('N')
            }
                
        });
        $('#save').submit(function(event) {

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
        // $(document).on('change', '#supplier', function(){
        //     if(this.value != null){
        //         showTable(this.value);
        //     }
        //     $('#tagihan').val(0);

        // });
        
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
        $('#tabel_tagihan').DataTable({
                    // order: [
                    //     [0, 'asc'],
                    // ],
                    // rowGroup: {
                    //     dataSrc: [0] // kalau mau grouping pake ini
                    // },
                    columnDefs: [
                        // {
                        //     targets: [0],
                        //     visible: false
                        // },
                        // { orderable: true, targets: 0 }, // Enable ordering for the first column (index 0)
                        { orderable: false, targets: '_all' } // Disable ordering for all other columns
                    ],
                    info: false,
                    searching: true,
                    paging: false,
                    language: {
                        emptyTable: "Data tidak ditemukan."
                    }
        });
        // function showTable(supplier){
        //     var baseUrl = "{{ asset('') }}";
        //     var url = `${baseUrl}tagihan_rekanan/load_data/${supplier}`;
    
        //     $.ajax({
        //         method: 'GET',
        //         url: url,
        //         dataType: 'JSON',
        //         contentType: false,
        //         cache: false,
        //         processData:false,
        //         success: function(response) {
        //             var data = response;
        //             console.log('data', data);
        //             $('#tabel_tagihan').DataTable().clear().destroy();
        //             for (var i = 0; i < data.length; i++) {
        //                 var row = $("<tr></tr>");
        //                 row.append(`<td>${data[i].get_customer.kode} - ${data[i].nama_tujuan} - (${ dateMask(data[i].tanggal_berangkat)})</td>`);
        //                 row.append(`<td>${moneyMask(data[i].harga_jual)}
        //                     </td>`)
        //                 row.append(`<td>
        //                         <input type="hidden" id="hidden_harga_jual_${data[i].id_sewa}" value="${data[i].harga_jual}" />
        //                         <input type="text" class="form-control ditagihkan uang numaja" name="data[${data[i].id_sewa}][ditagihkan]" id="${data[i].id_sewa}" readonly/>
        //                             </td>`)
        //                 row.append(`<td><input type="text" readonly name="data[${data[i].id_sewa}][catatan]" class="form-control" id="catatan_${data[i].id_sewa}" /></td>`)
        //                 row.append(`<td class='text-center' style="text-align:center">
        //                                 <input type="checkbox" class="checkHitung check_item" value="${data[i].id_sewa}">
        //                             </td>`);
        //                 $("#hasil").append(row);
        //             }

                
        //         },error: function (xhr, status, error) {
        //             const Toast = Swal.mixin({
        //                 toast: true,
        //                 position: 'top',
        //                 timer: 2500,
        //                 showConfirmButton: false,
        //                 timerProgressBar: true,
        //                 didOpen: (toast) => {
        //                     toast.addEventListener('mouseenter', Swal.stopTimer)
        //                     toast.addEventListener('mouseleave', Swal.resumeTimer)
        //                 }
        //             })

        //             Toast.fire({
        //                 icon: 'error',
        //                 title: 'Terjadi kesalahan: '+error
        //             })
        //         }
        //     });
        // };    

        // $(document).on('click', '.check_all', function (event) {
        //     $(".check_item").prop('checked', this.checked);
        // });

        // $(document).on('click', '.checkHitung', function (event) {
        //     const harga_jual = document.getElementById('hidden_harga_jual_'+this.value);
        //     const inputElement = document.getElementById(this.value);
        //     const catatanElement = document.getElementById("catatan_"+this.value);
            
        //     if (this.checked) {
        //         inputElement.value = moneyMask(harga_jual.value);
        //         inputElement.removeAttribute("readonly");
        //         catatanElement.removeAttribute("readonly");
        //     } else {
        //         inputElement.value = ""; // Set the value to "0"
        //         inputElement.setAttribute("readonly", "readonly");

        //         catatanElement.value = ""; // Set the value to "0"
        //         catatanElement.setAttribute("readonly", "readonly");
        //     }
        //     hitung();
        // });  

        // $(document).on('keyup', '.ditagihkan', function (event) {
        //     validation(this)
        //     hitung();
        // });

        // // buat make sure agar lebih akurat
        // $(document).on('change', '.ditagihkan', function (event) {
        //     validation(this)
        //     hitung();
        // });

        // function validation(data){
        //     var id = data.getAttribute("id");
        //     var hiddenValue = $('#hidden_harga_jual_'+id).val();
        //     if (normalize(data.value) > parseFloat(hiddenValue)) {
        //         data.value = moneyMask(parseFloat(hiddenValue)); 
        //     }
        // }

        // function hitung(){
        //     const elements = document.querySelectorAll(".ditagihkan");
        //     let totalValue = 0;

        //     elements.forEach(element => {
        //         const value = normalize(element.value);
        //         if (!isNaN(value)) {
        //             totalValue += value;
        //         }
        //     });

        //     $('#tagihan').val(moneyMask(totalValue));
        // }
    });
</script>

@endsection