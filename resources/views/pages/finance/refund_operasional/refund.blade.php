
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
<div class="container-fluid">
    <form action="{{ route('refund_biaya_operasional.update', [ $data->id ]) }}" method="POST" id="post_data">
        @csrf 
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('refund_biaya_operasional.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body w-50" >
                <div class="row">
                        {{-- <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="tanggal_berangkat">Tanggal Berangkat<span style="color:red">*</span></label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input disabled type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse($data->tanggal_berangkat)->format('d-M-Y')}}">
                            </div>
                            <input type="hidden" name="id_sewa_hidden" value="{{$id_sewa}}">
                            <input type="hidden" name="no_sewa" value="{{$data->no_sewa}}">
                        </div> 
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="no_akun">Kendaraan</label>
                            <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{$data->no_polisi}}" readonly>                         
                        </div>  
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <label for="no_akun">Driver</label>
                            @if ($data->id_supplier==null)
                                <input type="text" id="driver" name="driver" class="form-control" value="{{$data->nama_driver}}" readonly>     
                                <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{$data->id_karyawan}}"> 
                            @else
                                <input type="text" class="form-control" readonly="" name="driver" value="DRIVER REKANAN {{ $supplier->nama }}">
                            @endif
                        </div> 
                         <div class="form-group col-lg-6 col-md-6 col-sm-12">
                            <label for="no_akun">Customer</label>
                            <input type="text" id="customer" name="customer" class="form-control" value="[{{$data->getCustomer->kode}}] {{$data->getCustomer->nama}}" readonly>                         
                        </div>  

                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                            <label for="no_akun">Tujuan</label>
                            <input type="text" id="tujuan" name="tujuan" class="form-control" value="{{$data->nama_tujuan}}" readonly>                         
                        </div>   --}}
                        {{-- <div class="col-6"> --}}
                            <div class="form-group ">
                                <label for="refund">Kembali sebagai</label>
                                <select class="form-control select2" name="kembali" data-live-search="true" data-show-subtext="true" data-placement="bottom" width="100">
                                    <option value="kasbon" {{ $data->id_kas_bank == null? 'selected':''; }}>KEMBALI SEBAGAI KASBON</option>
                                    @foreach ($dataKas as $kb)
                                        <option value="{{$kb->id}}" {{ $kb->id == $data->id_kas_bank? 'selected':''; }} >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                {{-- <input type="text" id="catatan" name="data[{{$index}}][catatan]" class="form-control" value="">   --}}
                                <label for="catatan">Catatan Refund</label>
                                <textarea name="catatan"  class="form-control" id="catatan" rows="8" value=""></textarea>
                            </div>  
                            
                        {{-- </div> --}}
                        {{-- <div class="col-6"> --}}
                            <table class="table table-bordered card-outline card-primary table-hover" id="sortable" >
                                <thead>
                                    <tr>
                                        <th colspan="4">Rincian Pembayaran</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>Deskripsi</th>
                                        <th>Total Dicairkan</th>
                                        <th>Orderan</th>
                                    </tr>
                                </thead>
                                <tbody id="tampunganTabel">
                                    @php
                                        $index=0;
                                    @endphp
                                    @foreach ($data->getOperasionalDetail as $key => $value)
                                        <tr id="{{$index}}">
                                            <td id="id_sewa_operasional_tabel_{{$index}}" >
                                                <div class="icheck-primary d-inline">
                                                    <input type="checkbox" id="checkboxPrimary_{{$index}}" class="centang_cekbox" value="N" name="data[{{$index}}][is_kembali]">
                                                    <label for="checkboxPrimary_{{$index}}"></label>
                                                </div>
                                                    <input type="hidden" id="id_sewa_operasional_data_{{$index}}"  class="id_operasional" name="data[{{$index}}][id_sewa_operasional_data]" value="{{$value->id_sewa}}" readonly>
                                                    <input type="hidden" id="id_operasional_data_{{$index}}"  class="id_operasional" name="data[{{$index}}][id_operasional_data]" value="{{$value->id}}" readonly>
                                                    <input type="hidden" id="id_pembayaran_operasional_{{$index}}"  class="id_pembayaran_operasional" name="data[{{$index}}][id_pembayaran_operasional]" value="{{$value->id_pembayaran}}" readonly>
                                            </td>
                                            <td id="deskripsi_tabel_{{$index}}" >
                                                <input type="text" name="data[{{$index}}][deskripsi_data]" id="deskripsi_data_{{$index}}" value="{{$value->deskripsi}}" class="form-control deskripsi_hardcode ambil_text_deskripsi" readonly>
                                            </td>
                                            <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{$index}}">
                                                <input type="text" name="data[{{$index}}][total_dicairkan]" id="total_dicairkan_{{$index}}" value="{{number_format($value->total_dicairkan) }}" class="form-control uang numaja nominal_hardcode"readonly>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary"> {{$value->getSewaDetail->no_polisi}} ({{$value->getSewaDetail->getKaryawan ? $value->getSewaDetail->getKaryawan->nama_panggilan : 'REKANAN'}})  </span><br>
                                                <span class="badge badge-secondary"> {{$value->getSewaDetail->tanggal_berangkat}} </span>
                                                <span class="badge badge-success"> {{$value->getSewaDetail->no_sewa}} </span>
                                                <span class="badge badge-warning">({{$value->getSewaDetail->getCustomer->nama}}) -> {{$value->getSewaDetail->nama_tujuan}} </span>
                                            </td>
                                        </tr>
                                        @php
                                        $index+=1;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td colspan="2">Total Pencairan</td>
                                        <td>
                                            <input type="text" name="total_dicairkan"value="{{number_format($data->total_dicairkan) }}" class="form-control uang numaja nominal_hardcode"readonly>
                                        </td>
                                    </tr>
                                    
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        {{-- </div> --}}
                </div>
             
            </div>
        </div> 
     
    </form>
</div>

<script type="text/javascript">
$(document).ready(function() {

    $(document).on('click', '.centang_cekbox', function () {
        if ($(this).is(":checked")) {
            $(this).val('Y');
        
        } else if ($(this).is(":not(:checked)")) {  
            $(this).val('N')
        }
            
    });
    $('#post_data').submit(function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin bahwa ada pengembalian uang dari data yang tersedia?',
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

@endsection


