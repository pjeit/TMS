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
    <form action="{{ route('invoice_resi.update',[$resi->id]) }}" id="save" method="POST">
        @csrf
        @method('PUT')
        <div class="card radiusSendiri">
            <div class="card-header radiusSendiri sticky-top">
                <a href="{{ route('invoice_resi.index') }}" class="btn btn-secondary radiusSendiri"><i
                        class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i
                        class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body radiusSendiri">
                    <div class="row">
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="">Tanggal Resi<span style="color:red">*</span></label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" autocomplete="off" name="tgl_resi" class="form-control date" value="{{date('d-M-Y',strtotime($resi->tanggal_resi)) }}" id="tgl_nota" required>
                            </div>
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="refund">Kurir</label>
                            <select class="form-control select2" name="jenis_pengiriman" data-live-search="true" data-show-subtext="true" data-placement="bottom" width="100">
                                <option value="tiki" {{$resi->jenis_pengiriman?'tiki':'selected'}}>TIKI</option>
                                <option value="lion" {{$resi->jenis_pengiriman?'lion':'selected'}}>LION PARCEL</option>
                                <option value="jne" {{$resi->jenis_pengiriman?'jne':'selected'}}>JNE</option>
                                <option value="jnt" {{$resi->jenis_pengiriman?'jnt':'selected'}}>JNT</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="">No. Resi<span style="color:red">*</span></label>
                            <input type="text" name="no_resi" id="no_resi" maxlength="12" class="form-control numaja"
                                required value="{{$resi->no_resi}}">
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
                                <th>Tanggal Invoice</th>
                                <th>Billing to</th>
                                <th>Total Tagihan</th>
                                <th>Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody id="hasil">
                            @if (isset($data_gabung))
                                @foreach ($data_gabung as $key => $value)
                                    <tr id="{{$key}}">
                                        <td >
                                            @if ($value->id_detail)
                                                <div class="icheck-success d-inline">
                                            @else
                                                <div class="icheck-danger d-inline">
                                            @endif

                                                <input type="checkbox" id="checkboxPrimary_{{$key}}" class="centang_cekbox" value="{{$value->id_detail?'Y':'N'}}" name="detail[{{$key}}][is_resi]" {{$value->id_detail?'checked':''}}>
                                                <label for="checkboxPrimary_{{$key}}"></label>
                                                @if ($value->id_detail)
                                                    <span class="badge badge-success">Data yang tersimpan</span>
                                                @else
                                                    <span class="badge badge-danger">Data invoice</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{$value->no_invoice}}</td>
                                        <td>{{date('d-M-Y',strtotime($value->tgl_invoice))}}</td>
                                        <td>{{$value->nama_customer}}</td>
                                        <td>
                                            {{ number_format($value->total_tagihan)}}
                                            <input type="hidden" id="hidden_id_detail_{{$key}}" value="{{$value->id_detail}}" name="detail[{{$key}}][id_resi_detail]"/>
                                            <input type="hidden" id="hidden_id_invoice_{{$key}}" value="{{$value->id_invoice}}" name="detail[{{$key}}][id_invoice]"/>
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
            var flag_cek = false;
            $('.cekbox_semua').each(function() {
                if ($(this).is(':checked')) {
                    flag_cek = true;
                    return false; 
                }
            });

            if (!flag_cek) {
                event.preventDefault(); 
                Swal.fire({
                    icon: 'error',
                    text: 'Harap Pilih sertidaknya 1 invoice',
                });
                return;
            }
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
        // $('#tabel_tagihan').DataTable({
        //             // order: [
        //             //     [0, 'asc'],
        //             // ],
        //             // rowGroup: {
        //             //     dataSrc: [0] // kalau mau grouping pake ini
        //             // },
        //             // columnDefs: [
        //             //     // {
        //             //     //     targets: [0],
        //             //     //     visible: false
        //             //     // },
        //             //     // { orderable: true, targets: 0 }, // Enable ordering for the first column (index 0)
        //             //     { orderable: false, targets: '_all' } // Disable ordering for all other columns
        //             // ],
        //             info: false,
        //             searching: true,
        //             paging: false,
        //             language: {
        //                 emptyTable: "Data tidak ditemukan."
        //             }
        // });
       
    });
</script>

@endsection