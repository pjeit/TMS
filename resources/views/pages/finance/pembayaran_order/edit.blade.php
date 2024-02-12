
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
   .tinggi{
    height: 20px;
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
    <form action="{{ route('pembayaran_jo.update',[$pembayaran_jo->id]) }}" method="POST" id="form">
      @csrf
        @method('PUT')

        <div class="container-fluid">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{ route('pembayaran_jo.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                </div>
                <div class="card-body" >
                    <div class="row">
                        <div class="col-lg-7 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-6" > 
                                        <div class="form-group">
                                            <label for="">Pengirim<span class="text-red">*</span></label>
                                                <select class="form-control selectpicker"  id='customer' name="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled>
                                                @foreach ($dataCustomer as $cust)
                                                    <option value="{{$cust->id}}" {{$cust->id == $pembayaran_jo->id_customer? 'selected':''}}>{{ $cust->nama }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group ">
                                            <label for="">Pelayaran</label>
                                            <select class="form-control selectpicker"  id='pelayaran' name="supplier" data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled>
                                                @foreach ($dataSupplier as $sup)
                                                    <option value="{{$sup->id}}" {{$sup->id == $pembayaran_jo->id_supplier? 'selected':''}}>{{ $sup->nama }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group ">
                                            <label for="">No. BL<span class="text-red">*</span></label>
                                            <input required type="text" name="nama_pic" class="form-control" value="{{$pembayaran_jo->no_bl}}" readonly>
                                        </div>  
                                </div>
                                <div class="col-6"> 
                                    <div class="form-group">
                                            <label for="tgl_sandar">Tanggal Sandar</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" name="tgl_sandar" autocomplete="off" class="date form-control" id="tgl_sandar" placeholder="dd-M-yyyy" value="{{\Carbon\Carbon::parse($pembayaran_jo->tgl_sandar)->format('d-M-Y')}}" disabled>     
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label for="">Pelabuhan Muat<span class="text-red">*</span></label>
                                        <input required type="text" name="pelabuhan_muat" class="form-control" value="{{$pembayaran_jo->pelabuhan_muat}}" readonly>
                                    </div> 
                                    <div class="form-group">
                                        <label for="">Pelabuhan Bongkar<span class="text-red">*</span></label>
                                        <input required type="text" name="pelauhan_bongkar" class="form-control" value="{{$pembayaran_jo->pelabuhan_bongkar}}" readonly>
                                    </div>  
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-6 col-sm-12">
                            <h4 class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-primary"><b>Total Biaya</b></span>
                                {{-- <span class="badge bg-primary rounded-pill">3</span> --}}
                            </h4>
                            <ul class="list-group mb-3 ">
                                <li class="border-primary list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                    <h6 class="my-0">Biaya Sebelum Dooring</h6>
                                    {{-- <small class="text-muted">total</small> --}}
                                    </div>
                                    <span class="">Rp. {{number_format($TotalBiayaRev)}}</span>
                                </li>
                                <li class="border-primary list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                    <h6 class="my-0">Biaya Jaminan</h6>
                                    {{-- <small class="text-muted">total</small> --}}
                                    </div>
                                     @if($dataJaminan)
                                    <span class="">Rp. {{number_format($dataJaminan->nominal)}}</span>
                                    @else
                                    <span class="">Rp. {{number_format(0)}}</span>
                                    
                                    @endif
                                </li>
                                <li class="border-primary list-group-item d-flex justify-content-between">
                                    <span>Total (IDR)</span>
                                     <input type="hidden" name="total_sblm_dooring" value="{{$TotalBiayaRev}}">
                                    @if($dataJaminan)
                                         <strong><b>Rp. {{number_format($TotalBiayaRev+$dataJaminan->nominal)}}</b></strong>
                                    @else
                                         <strong><b>Rp. {{number_format($TotalBiayaRev)}}</b></strong>
                                    @endif

                                   
                                </li>
                            </ul>
                            <div class="input-group">
                                <select class="form-control select2"  id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                    <option value="">--PILIH PEMBAYARAN--</option>
                                    @foreach ($dataKas as $data)
                                        <option value="{{$data->id}}" {{ $data->id == 1? 'selected':'' }}>{{ $data->nama }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-success ml-3" id="bttonBayar"><i class="fa fa-credit-card" aria-hidden="true" ></i> Bayar</button>
                                {{-- <a href="{{ route('pembayaran_jo.index') }}"class="btn btn-success"><i class="fa fa-credit-card" aria-hidden="true"></i> Bayar</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
           
            <div class="d-flex justify-content-between" style="gap: 10px;">
                <table class="table" id="sortable" style="background: #fff">
                    <thead>
                        <tr>
                            <th colspan="2">Biaya Sebelum Dooring</th>
                        </tr>
                    </thead>
                    <tbody > 
                        <tr>
                            <th><span> <input disabled type="checkbox" name="thc_cekbox" id="thc_cekbox" {{$pembayaran_jo->thc!=0?'checked':''}}></span> THC</th>
                            <td name="total_thc"><input type="text" id="total_thc" class="form-control" value="Rp. {{number_format($pembayaran_jo->thc)}}" readonly></td>
                        </tr>
                        <tr>
                            <th><span> <input disabled type="checkbox" name="lolo_cekbox" id="lolo_cekbox" {{$pembayaran_jo->lolo!=0?'checked':''}}></span> LOLO</th>
                            <td name="total_lolo"><input type="text" id="total_lolo" class="form-control" value="Rp. {{number_format($pembayaran_jo->lolo)}}" readonly></td>
                        </tr>
                        <tr>
                            <th><span> <input disabled type="checkbox" name="apbs_cekbox" id="apbs_cekbox" {{$pembayaran_jo->apbs!=0?'checked':''}}></span> APBS</th>
                            <td name="total_apbs"><input type="text" id="total_apbs" class="form-control" value="Rp. {{number_format($pembayaran_jo->apbs)}}" readonly></td>
                        </tr>
                        <tr>
                            <th><span> <input disabled type="checkbox" name="cleaning_cekbox" id="cleaning_cekbox" {{$pembayaran_jo->cleaning!=0?'checked':''}}></span> CLEANING</th>
                            <td name="total_cleaning"><input type="text" id="total_cleaning" class="form-control" value="Rp. {{number_format($pembayaran_jo->cleaning)}}" readonly></td>
                        </tr>
                        <tr>
                            <th><span> <input disabled type="checkbox" name="doc_fee_cekbox" id="doc_fee_cekbox" {{$pembayaran_jo->doc_fee!=0?'checked':''}}></span> DOC FEE</th>
                            <td name="total_doc_fee"><input type="text" id="total_doc_fee" class="form-control" value="Rp. {{number_format($pembayaran_jo->doc_fee)}}" readonly></td>
                        </tr>
                        @if (isset($JobOrderBiaya))
                            @foreach ($JobOrderBiaya as $value)
                                <tr>
                                    <th>{{$value->deskripsi}}</th>
                                    <td >
                                        <input type="text" class="form-control" value="Rp. {{number_format($value->biaya)}}" readonly>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <th class="text-blue">SUB TOTAL</th>
                            <th name="total_sblm_dooring" id="total_sblm_dooring" > <input type="text" class="form-control" readonly value="Rp. {{number_format($TotalBiayaRev)}}"> </th>
                        </tr>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>

                @if($dataJaminan)
                    <table class="table" id="sortable" style="background: #fff">
                        <thead>
                            <tr>
                                <th colspan="2">Biaya Jaminan</th>
                            </tr>
                        </thead>
                        <tbody > 
                            <tr class="tinggi">
                                <th>Tgl Bayar Jaminan</th>
                                <td><input type="text" name="" class="form-control" value="{{\Carbon\Carbon::parse($dataJaminan->tgl_bayar)->format('d-M-Y')}}" readonly></td>
                            </tr>
                            <tr class="tinggi">
                                <th>Total Jaminan</th>
                                <th><input type="text" class="form-control" disabled value="Rp. {{number_format($dataJaminan->nominal)}}"></th>
                            </tr>
                            <tr>
                                <th>Catatan</th>
                                <th><input type="text" class="form-control" disabled value="{{ $pembayaran_jo->catatan }}"></th>
                            </tr>
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>
 
    </form>
<script type="text/javascript">
    $(document).ready(function() {
        // $('body').on('click','#bttonBayar',function (event) {
        //     var pembayaran = $('#pembayaran').val();
        //     console.log(pembayaran);
        //         if (pembayaran == '' || pembayaran == null) {
        //             // event.preventDefault(); 
        //             Swal.fire({
        //                 icon: 'error',
        //                 text: 'KAS PEMBAYARAN WAJIB DIPILIH!',
        //             })
        //             return;
        //         }

        // });
        $('body').on('click','#bttonBayar', function (event) {
            var pembayaran = $('#pembayaran').val();
            console.log(pembayaran);
                if (pembayaran == '' || pembayaran == null) {
                    // event.preventDefault(); 
                    Swal.fire({
                        icon: 'error',
                        text: 'KAS PEMBAYARAN WAJIB DIPILIH!',
                    })
                    return;
                }
                // event.preventDefault();
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
                            // const Toast = Swal.mixin({
                            //     toast: true,
                            //     position: 'top',
                            //     timer: 2500,
                            //     showConfirmButton: false,
                            //     timerProgressBar: true,
                            //     didOpen: (toast) => {
                            //         toast.addEventListener('mouseenter', Swal.stopTimer)
                            //         toast.addEventListener('mouseleave', Swal.resumeTimer)
                            //     }
                            // })

                            // Toast.fire({
                            //     icon: 'Sukses',
                            //     title: 'Data Pembayaran Berhasil Disimpan'
                            // })
                            $("#form").submit();
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
                        // event.preventDefault();
                        // return;
                    }
                })
            // pop up confirmation
        });

    });
</script>

@endsection


