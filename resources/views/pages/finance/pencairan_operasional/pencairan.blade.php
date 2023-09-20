
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
    .card-header:first-child{
        border-radius:inherit;
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
<form action="{{ route('pencairan_operasional.update', ['pencairan_operasional' => $data[0]->getSewa->id_customer ]) }}" id='save' method="POST" >
@method('PUT')
@csrf
<div class="row m-2">
    <div class="col-12 radiusSendiri sticky-top " style="margin-bottom: -15px;">
        <div class="card radiusSendiri" style="">
            <div class="card-header ">
                <a href="{{ route('pencairan_operasional.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
        </div>
    </div>
        <div class="col-12">
        <div class="card radiusSendiri">
            <div class="card-body">
                <div class="row">
                    <div class="col-6" >
                        <div class="form-group" style="pointer-events: none;" >
                            <label for="">Grup </label>
                            <input type="text" id="no_kontainer" class="form-control" value="{{$data[0]->getSewa->getCustomer->getGrup->nama_grup}}" readonly>
                        </div>
                    </div>
                    <div class="col-6" >
                        <div class="form-group" style="pointer-events: none;" >
                            <label for="">Tanggal Pencairan</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" name="tgl_pencairan" autocomplete="off" class="date form-control" id="tgl_pencairan" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse(now())->format('d-M-Y') }}" disabled>     
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <div class="table_wrapper">
                    <table id="tabelJO" class="tabelJO table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th width="">Tujuan</th>
                                <th width="">No. Polisi</th>
                                <th width="">Driver</th>
                                <th width="">Deskripsi</th>
                                <th width="">Nominal</th>
                                <th width="">Jumlah Dicairkan</th>
                                <th width="">Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="tb">
                            @if ($data)
                                @php
                                $currentCustomer = null;
                                @endphp
                                @foreach ($data as $key => $item)
                                    @php
                                    $customerName = $item->getSewa->getCustomer->nama;
                                    @endphp
                                    @if ($customerName != $currentCustomer)
                                        <tr class="group-row">
                                            <td colspan="7">{{ $customerName }}</td>
                                        </tr>
                                        @php
                                        $currentCustomer = $customerName;
                                        @endphp
                                    @endif
                                    <tr id="row{{ $key }}">
                                        <td>{{ $item->getSewa->getTujuan->nama_tujuan }}</td>
                                        <td>{{ $item->getSewa->no_polisi }}</td>
                                        <td>{{ $item->getSewa->getKaryawan->nama_panggilan }}</td>
                                        <td>{{ $item->deskripsi }}</td>
                                        <td width="200">
                                            <input type="text" id="detail[{{ $key }}][total_operasional]" name="detail[{{ $key }}][total_operasional]" value="{{ number_format($item->total_operasional) }}" class="form-control" readonly>
                                        </td>
                                        <td width="200">
                                            <input type="text" id="detail[{{ $key }}][total_dicairkan]" name="detail[{{ $key }}][total_dicairkan]" value="{{ $item->no_kontainer }}" class="form-control uang numaja">
                                        </td>
                                        <td>
                                            <input type="text" name="detail[{{ $key }}][catatan]" class="form-control">
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div> 
    </div>
    
    <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <h3 class="card-title mt-2"><b>KETERANGAN BIAYA</b></h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button> -->
                    </div>
                </div>
                <div class="card-body" >
                <div class="d-flex justify-content-between" style="gap: 10px;">
                    {{-- <table class="table table-bordered" >
                        <thead>
                            <tr>
                                <th colspan="2" class="card-outline card-primary">BIAYA SEBELUM DOORING</th>
                            </tr>
                        </thead>
                        <tbody > 
                            <tr>
                                <th><span> <input type="checkbox" class="checkitem" name="thc_cekbox" id="thc_cekbox" <?= ($data['JO']['thc'] == 0) ? '':'checked'; ?> disabled></span> THC</th>
                                <td name="">
                                    <input type="text" name="total_thc" id="total_thc" class="form-control" value="{{number_format($data['JO']['thc'])}}" readonly >
                                </td>
                            </tr>
                            <tr>
                                <th><span> <input type="checkbox" class="checkitem" name="lolo_cekbox" id="lolo_cekbox" <?= $data['JO']['lolo'] == 0 ? '':'checked'; ?> disabled></span> LOLO</th>
                                <td name="">
                                    <input type="text" name="total_lolo" id="total_lolo" class="form-control" value="{{number_format($data['JO']['lolo'])}}" readonly >
                                </td>
                            </tr>
                            <tr>
                                <th><span> <input type="checkbox" class="checkitem" name="apbs_cekbox" id="apbs_cekbox" <?= ($data['JO']['apbs'] == 0) ? '':'checked'; ?> disabled></span> APBS</th>
                                <td name="">
                                    <input type="text" name="total_apbs" id="total_apbs" class="form-control" value="{{number_format($data['JO']['apbs'])}}" readonly >
                                </td>
                            </tr>
                            <tr>
                                <th><span> <input type="checkbox" class="checkitem" name="cleaning_cekbox" id="cleaning_cekbox" <?=  ($data['JO']['cleaning'] == 0) ? '':'checked'; ?> disabled></span> CLEANING</th>
                                <td name="">
                                    <input type="text" name="total_cleaning" id="total_cleaning" class="form-control" value="{{number_format($data['JO']['cleaning'])}}" readonly >
                                </td>
                            </tr>
                            <tr>
                                <th><span> <input type="checkbox" class="checkitem" name="doc_fee_cekbox" id="doc_fee_cekbox" <?= ($data['JO']['doc_fee'] == 0) ? '':'checked'; ?> disabled></span> DOC FEE</th>
                                <td name="">
                                    <input type="text" name="total_doc_fee" id="total_doc_fee" class="form-control" value="{{number_format($data['JO']['doc_fee'])}}" readonly >
                                </td>
                            </tr>
                            <tr>
                                <th>SUB TOTAL</th>
                                <td>
                                    <input type="text" name="total_sblm_dooring" id="total_sblm_dooring" class="form-control" value="<?= number_format($data['JO']['thc']+$data['JO']['lolo']+$data['JO']['apbs']+$data['JO']['cleaning']+$data['JO']['doc_fee'] ,2) ?>" readonly>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table> --}}
                    <div class="col-6">

                    </div>

                    <div class="col-6 card-outline card-primary">
                        <h4 class="d-flex justify-content-between align-items-center mt-2 mb-3">
                            <span class="text-primary">Total Biaya</span>
                            {{-- <span class="badge bg-primary rounded-pill">3</span> --}}
                        </h4>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                <h6 class="my-0">Total Tally</h6>
                                </div>
                                <span class="text-muted">Rp. 15,000,000</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                <h6 class="my-0">Total Operasional</h6>
                                </div>
                                <span class="text-muted">Rp. 20,000,000</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                <h6 class="my-0">Total Buruh</h6>
                                </div>
                                <span class="text-muted">Rp. 1,500,000</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                <h6 class="my-0">Total Timbang</h6>
                                </div>
                                <span class="text-muted">Rp. 5,000,000</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total (IDR)</span>
                                 <input type="hidden" name="total_sblm_dooring" value="">
                                    <strong>Rp. 35,000,000</strong>
                            </li>
                        </ul>
                        <div class="input-group ">
                            <select class="form-control selectpicker"  id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                <option value="">--PILIH PEMBAYARAN--</option>
                                @foreach ($dataKas as $data)
                                    <option value="{{$data->id}}">{{ $data->nama }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-success" id="bttonBayar"><i class="fa fa-credit-card" aria-hidden="true" ></i> Bayar</button>

                            {{-- <a href="{{ route('pembayaran_jo.index') }}"class="btn btn-success"><i class="fa fa-credit-card" aria-hidden="true"></i> Bayar</a> --}}

                        </div>
                    </div>
                </div>
                </div>
        </div>                 
    </div>
</div>
</form>
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
                // console.log('tujuanVal '+i +' '+ tujuanVal.length);
                // console.log('tglBookingVal '+i +' '+ tglBookingVal);                

                if (tujuanVal != "" || tglBookingVal != "") {
                    // console.log('tglBookingVal '+tglBookingVal);
                    // console.log('tujuanVal '+tujuanVal);
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
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                timer: 800,
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
                            }, 1000); // 2000 milliseconds = 2 seconds
                        }
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
            var dataKeuangan = {!! json_encode($dataPengaturanKeuangan[0]) !!};
            var harga20Ft = {
                'thcLuar': dataKeuangan.thc_20ft_luar,
                'thcDalam': dataKeuangan.thc_20ft_dalam,
                'loloLuar': dataKeuangan.lolo_20ft_luar,
                'loloDalam': dataKeuangan.lolo_20ft_dalam,
                'apbs': dataKeuangan.apbs_20ft,
                'cleaning': dataKeuangan.cleaning_20ft,
                'doc_fee': dataKeuangan.doc_fee_20ft,
            };
            var harga40Ft = {
                'thcLuar': dataKeuangan.thc_40ft_luar,
                'thcDalam': dataKeuangan.thc_40ft_dalam,
                'loloLuar': dataKeuangan.lolo_40ft_luar,
                'loloDalam': dataKeuangan.lolo_40ft_dalam,
                'apbs': dataKeuangan.apbs_40ft,
                'cleaning': dataKeuangan.cleaning_40ft,
                'doc_fee': dataKeuangan.doc_fee_40ft,
            };
            console.log('harga40Ft '+harga40Ft);
        // end of master harga tipe

    });
</script>

@endsection


