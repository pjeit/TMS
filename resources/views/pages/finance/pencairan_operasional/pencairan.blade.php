
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
<form action="{{ route('pencairan_operasional.update', ['pencairan_operasional' => $customers[0]->grup_id ]) }}" id='save' method="POST" >
@method('PUT')
@csrf
<div class="row m-2">
    {{-- <div class="col-12 radiusSendiri sticky-top " style="margin-bottom: -15px;">
        <div class="card radiusSendiri" style="">
            <div class="card-header ">
                <a href="{{ route('pencairan_operasional.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
            </div>
        </div>
    </div> --}}
    <div class="col-12">
        <div class="card radiusSendiri">
            <div class="card-header ">
                <a href="{{ route('pencairan_operasional.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6" >
                        <div class="form-group" style="pointer-events: none;" >
                            <label for="">Grup </label>
                            <input type="text" id="no_kontainer" class="form-control" value="{{$customers[0]->getGrup->nama_grup}}" readonly>
                        </div>
                    </div>
                    <div class="col-6" >
                        <div class="form-group" style="pointer-events: none;" >
                            <label for="">Tanggal Pencairan</label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" name="tgl_dicairkan" autocomplete="off" class="date form-control" id="tgl_dicairkan" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse(now())->format('d-M-Y') }}" readonly>     
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
                            <input type="hidden" id='t_tally' placeholder="t_tally">
                            <input type="hidden" id='t_buruh' placeholder="t_buruh">
                            <input type="hidden" id='t_timbang' placeholder="t_timbang">
                            <input type="hidden" id='t_operasional' placeholder="t_operasional">
                            <input type="hidden" id='t_total' placeholder="t_total">
                            @if ($customers)
                                @php
                                    $tally = $operasional = $timbang = $buruh = 0;
                                @endphp
                                @foreach ($customers as $i => $cust)
                                    <tr class="group-row bg-gray-light">
                                        <td colspan="7"><b>{{ $cust->nama }}</b></td>
                                    </tr>
                                    @foreach ($cust->sewa as $key => $sewa)
                                        @foreach ($sewa->sewaOperasional as $item)
                                            @if ($item->total_operasional != null || $item->total_operasional != 0)
                                                @if ($item->status != 'SUDAH DICAIRKAN')
                                                    <tr id="row{{ $item->id }}">
                                                        @php
                                                            $item->deskripsi
                                                        @endphp
                                                        <td>{{ $sewa->nama_tujuan }}</td>
                                                        <td>{{ $sewa->no_polisi }}</td>
                                                        <td>{{ $sewa->getKaryawan->nama_panggilan }}</td>
                                                        <td>{{ $item->deskripsi }}</td>
                                                        <td width="200">
                                                            <input type="text" id="detail[{{ $item->id }}][total_operasional]" name="detail[{{ $item->id }}][total_operasional]" value="{{ number_format($item->total_operasional) }}" class="form-control" readonly>
                                                            <input type="hidden" id="biaya_{{ $item->id }}" value="{{$item->total_operasional}}" class="form-control" readonly>
                                                        </td>
                                                        <td width="200">
                                                            <input type="text" id="detail[{{ $item->id }}][total_dicairkan]" name="detail[{{ $item->id }}][total_dicairkan]" sewaOprs='{{$item->id}}' class="form-control sewa_oprs save_biaya_{{$item->id}} {{ substr($item->deskripsi, 0, 11) == 'OPERASIONAL'? substr($item->deskripsi, 0, 11):$item->deskripsi}} uang numaja">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="detail[{{ $item->id }}][catatan]" class="form-control" value="{{$item->catatan}}">
                                                            <input type="hidden" name="detail[{{ $item->id }}][jenis]" class="form-control" value="{{$item->deskripsi}}">
                                                        </td>
                                                    </tr>
                                                    @php
                                                        if($item->deskripsi == 'TALLY'){
                                                            $tally += $item->total_operasional; 
                                                        }else if($item->deskripsi == 'TIMBANG'){
                                                            $timbang += $item->total_operasional; 
                                                        }else if($item->deskripsi == 'BURUH'){
                                                            $buruh += $item->total_operasional; 
                                                        }else if(substr($item->deskripsi, 0, 11) == 'OPERASIONAL'){
                                                            $operasional += $item->total_operasional; 
                                                        }
                                                    @endphp
                                                @endif
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endif
                            @php
                                $total = $tally + $operasional + $timbang + $buruh;
                            @endphp
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
                </div>
            </div>
            <div class="card-body" >
                <div class="d-flex justify-content-between" style="gap: 10px;">
                    <div class="col-6">
                        &nbsp;
                    </div>
                    <div class="col-6 card-outline card-primary">
                        <h4 class="d-flex justify-content-between align-items-center mt-2 mb-3">
                            <span class="text-primary">Total Biaya</span>
                        </h4>
                        <ul class="list-group mb-3">
                            @if ($tally != 0)
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Total Tally</h6>
                                    </div>
                                    <span class="text-muted t_tally">Rp. 0</span>
                                </li>
                            @endif
                            @if ($operasional != 0)
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Total Operasional</h6>
                                    </div>
                                    <span class="text-muted t_operasional">Rp. 0</span>
                                </li>
                            @endif
                            @if ($buruh != 0)
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Total Buruh</h6>
                                    </div>
                                    <span class="text-muted t_buruh">Rp. 0</span>
                                </li>
                            @endif
                            @if ($timbang != 0)
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Total Timbang</h6>
                                    </div>
                                    <span class="text-muted t_timbang">Rp. 0</span>
                                </li>
                            @endif
                            <li class="list-group-item d-flex justify-content-between">
                                <span><b>GRAND TOTAL</b></span>
                                <strong><span class="t_total">Rp. 0</span></strong>
                            </li>
                        </ul>
                        <div class="input-group ">
                            <select class="form-control selectpicker"  id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                <option value="">── PILIH PEMBAYARAN ──</option>
                                @foreach ($dataKas as $kas)
                                    <option value="{{$kas->id}}">{{ $kas->nama }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-success" id="bttonBayar"><i class="fa fa-credit-card" aria-hidden="true" ></i> Bayar</button>
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
        // end of master harga tipe

        // $(document).on('change', '.sewa_oprs', function(e) {
        $(document).on('keyup', '.sewa_oprs', function(){

            var idOprs = $(this).attr('sewaOprs');
            var inputed = parseFloat(this.value.replace(',', ''));
            var max = $('#biaya_'+idOprs).val();
            
            if (inputed > max) {
                $('.save_biaya_'+idOprs).val(parseFloat(max).toLocaleString()); // Explicitly specify the locale
            }

            var tallyInputs = document.querySelectorAll('.TALLY');
            var buruhInputs = document.querySelectorAll('.BURUH');
            var timbangInputs = document.querySelectorAll('.TIMBANG');
            var operasionalInputs = document.querySelectorAll('.OPERASIONAL');
            var totaltally = totaloperasional = totaltimbang = totalburuh = 0;
            
            for (var i = 0; i < tallyInputs.length; i++) {
                var inputTally = parseFloat(tallyInputs[i].value.replace(',', '')) || 0; // Convert to a number or use 0 if NaN
                totaltally += inputTally;
            }
            for (var i = 0; i < buruhInputs.length; i++) {
                var inputBuruh = parseFloat(buruhInputs[i].value.replace(',', '')) || 0; // Convert to a number or use 0 if NaN
                totalburuh += inputBuruh;
            }
            for (var i = 0; i < timbangInputs.length; i++) {
                var inputTimbang = parseFloat(timbangInputs[i].value.replace(',', '')) || 0; // Convert to a number or use 0 if NaN
                totaltimbang += inputTimbang;
            }
            for (var i = 0; i < operasionalInputs.length; i++) {
                var inputOperasional = parseFloat(operasionalInputs[i].value.replace(',', '')) || 0; // Convert to a number or use 0 if NaN
                totaloperasional += inputOperasional;
            }

            var tallyElement = document.querySelector('.t_tally');
            if(tallyElement != null){
                tallyElement.textContent = "Rp. "+totaltally.toLocaleString(); 
            } 
            var buruhElement = document.querySelector('.t_buruh');
            if(buruhElement != null){
                buruhElement.textContent = "Rp. "+totalburuh.toLocaleString(); 
            } 
            var timbangElement = document.querySelector('.t_timbang');
            if(timbangElement != null){
                timbangElement.textContent = "Rp. "+totaltimbang.toLocaleString(); 
            } 
            var operasionalElement = document.querySelector('.t_operasional');
            if(operasionalElement != null){
                operasionalElement.textContent = "Rp. "+totaloperasional.toLocaleString(); 
            } 
            var totalElement = document.querySelector('.t_total');
            totalElement.textContent = "Rp. "+(totaloperasional+totaltimbang+totalburuh+totaltally).toLocaleString(); 

            $('#t_tally').val(totaltally);
            $('#t_buruh').val(totalburuh);
            $('#t_timbang').val(totaltimbang);
            $('#t_operasional').val(totaloperasional);
            $('#t_total').val((totaloperasional+totaltimbang+totalburuh+totaltally));
        });
    });
</script>

@endsection


