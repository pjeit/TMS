
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
    <form action="{{ route('revisi_biaya_operasional.update', [ $data->id ]) }}" method="POST" id="post_data">
        @csrf 
        @method('PUT')
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('revisi_biaya_operasional.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body">
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
                        <div class="col-12">
                            <div class="row">
                                
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Tanggal Transaksi <span style="opacity: 40%">(Tidak bisa diubah)</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="tgl_dicairkan" class="form-control date" id="tgl_dicairkan" value="{{date('d-M-Y',strtotime($data->tgl_dicairkan))}}" disabled>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="refund">Kas Bank<span style="color:red">*</span></label>
                                    <select class="form-control select2" name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom" width="100">
                                        {{-- <option value="kasbon" {{ $data->id_kas_bank == null? 'selected':''; }}>KEMBALI SEBAGAI KASBON OPERASIONAL</option> --}}
                                        @foreach ($dataKas as $kb)
                                            <option value="{{$kb->id}}" {{ $kb->id == $data->id_kas_bank? 'selected':''; }} >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="refund">Alasan Revisi<span style="color:red">*</span></label>
                                    <textarea name="alasan"  class="form-control" id="alasan" rows="8" value="" required></textarea>
                                </div>

                            </div>
                        </div>
                        <div class="col-12">
                            <table class="table table-bordered card-outline card-primary table-hover" id="sortable" >
                                <thead>
                                    <tr>
                                        <th colspan="5">Rincian Pembayaran</th>
                                    </tr>
                                    <tr>
                                       
                                        <th>Deskripsi</th>
                                        <th>Total Dicairkan</th>
                                        <th>Catatan</th>
                                        <th>Orderan</th>
                                        <th>
                                            {{-- @if (!$kembaliCek)
                                                <div class="icheck-primary d-inline">
                                                    <input type="checkbox" id="cekbox_semua" class="cekbox_semua" value="N" name="cekbox_semua">
                                                    <label for="cekbox_semua"></label>
                                                </div>
                                            @endif --}}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tampunganTabel">
                                    @php
                                        $index=0;
                                    @endphp
                                    @foreach ($data->getOperasionalDetail as $key => $value)
                                        <tr id="{{$index}}" style="background-color: {{$value->id_kasbon_kembali||$value->id_stok||$value->id_refund?'rgba(255, 0, 0, 0.329)':''}}">
                                           
                                            <td id="deskripsi_tabel_{{$index}}" >
                                                <input type="text" name="data[{{$index}}][deskripsi_data]" id="deskripsi_data_{{$index}}" value="{{$value->deskripsi}}" class="form-control deskripsi_hardcode ambil_text_deskripsi" readonly>
                                                @if ($value->id_kasbon_kembali||$value->id_stok||$value->id_refund)
                                                    <span class="badge badge-black">    
                                                        @if ($value->id_refund)
                                                               Sudah kembali kas : {{number_format($value->total_refund) }}   

                                                        @elseif ($value->id_kasbon_kembali)
                                                               Sudah kembali kasbon :{{number_format($value->total_kasbon_kembali)}}   
                                                        @else
                                                                Kembali stok :1
                                                        @endif
                                                    </span>
                                                    @endif
                                            </td>
                                            <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{$index}}">
                                                <input type="hidden" name="data[{{$index}}][is_aktif]" id="is_aktif_{{$index}}" value="{{$value->is_aktif }}" class="form-control uang numaja is_aktif">
                                                <input type="text" name="data[{{$index}}][total_dicairkan]" id="total_dicairkan_{{$index}}" value="{{number_format($value->total_dicairkan) }}" class="form-control uang numaja total_dicairkan">
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <input type="text" id="catatan" name="data[{{$index}}][catatan]" class="form-control" value="{{$value->catatan}}">  
                                                    {{-- <textarea name="data[{{$index}}][catatan]"  class="form-control" id="catatan" rows="8" value=""></textarea> --}}
                                                </div>  
                                            </td>
                                            <td>
                                                <input type="hidden" class="no_pol_driver" name="data[{{$index}}][no_pol]" value="{{$value->getSewaDetail->no_polisi}} ({{$value->getSewaDetail->getKaryawan ? $value->getSewaDetail->getKaryawan->nama_panggilan : 'REKANAN'}})" readonly>
                                                <input type="hidden" class="no_sewa" name="data[{{$index}}][no_sewa]" value="{{$value->getSewaDetail->no_sewa}}" readonly>
                                                <input type="hidden" class="customer" name="data[{{$index}}][customer]" value="{{$value->getSewaDetail->getCustomer->nama}}" readonly>
                                                <input type="hidden" class="tujuan" name="data[{{$index}}][tujuan]" value="{{$value->getSewaDetail->nama_tujuan}}" readonly>

                                                <span class="badge badge-primary"> {{$value->getSewaDetail->no_polisi}} ({{$value->getSewaDetail->getKaryawan ? $value->getSewaDetail->getKaryawan->nama_panggilan : 'REKANAN'}})  </span><br>
                                                <span class="badge badge-secondary"> {{$value->getSewaDetail->tanggal_berangkat}} </span>
                                                <span class="badge badge-success"> {{$value->getSewaDetail->no_sewa}} </span>
                                                <span class="badge badge-warning">({{$value->getSewaDetail->getCustomer->nama}}) -> {{$value->getSewaDetail->nama_tujuan}} </span>
                                            </td>
                                            <td id="id_sewa_operasional_tabel_{{$index}}" >
                                                {{-- @if (!$value->id_kasbon_kembali&&!$value->id_stok&&!$value->id_refund)
                                                    <div class="icheck-primary d-inline">
                                                        <input type="checkbox" id="checkboxPrimary_{{$index}}" class="centang_cekbox" value="N" name="data[{{$index}}][is_kembali]">
                                                        <label for="checkboxPrimary_{{$index}}"></label>
                                                    </div>
                                                @endif --}}
                                                <button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove" class="btn btn-danger radiusSendiri hapus">
                                                    <i class="fa fa-fw fa-trash-alt"></i>
                                                </button>
                                                    <input type="hidden" class="id_sewa" name="data[{{$index}}][id_sewa]" value="{{$value->id_sewa}}" readonly>
                                                    <input type="hidden" class="id_operasional" name="data[{{$index}}][id_pembayaran_detail]" value="{{$value->id}}" readonly>
                                                    <input type="hidden" class="id_pembayaran_operasional" name="data[{{$index}}][id_pembayaran_operasional]" value="{{$value->id_pembayaran}}" readonly>
                                            </td>
                                        </tr>
                                        @php
                                        $index+=1;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td >Total Pencairan</td>
                                        <td>
                                            <input type="text" name="total_pembayaran"value="{{number_format($data->total_dicairkan/*-$data->total_refund-$data->total_kasbon*/) }}" class="form-control uang numaja total_akhir"readonly>
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
     
    </form>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#tanggal_refund').datepicker({
        autoclose: true,
        format: "dd-M-yyyy",
        todayHighlight: true,
        startDate: "0d",
        language:'en',
    });
    

    $( document ).on( 'keyup', '.total_dicairkan', function (event) {
            hitungTotal();
        });
    function hitungTotal(){
        
        var total_dicairkan = 0;
        $('.total_dicairkan').each(function () {
            var biaya_total_dicairkan = $(this).val() ? parseFloat($(this).val().replace(/,/g, '')) : 0;
            total_dicairkan += biaya_total_dicairkan;
        });
        var total = parseFloat(total_dicairkan);
        $('.total_akhir').val(total.toLocaleString());
        console.log(total);
    }
    $(document).on('click', '.hapus', function () {
        var id_tr_table = $(this).closest('tr').attr('id');
        $(this).closest('tr').hide();
        $("#is_aktif_"+id_tr_table).val('N');
        $("#total_dicairkan_"+id_tr_table).val(0);
        hitungTotal();
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
    $( document ).on( 'click', '.hapus', function (event) {
            $(this).closest('tr').hide();
            hitungTotal();
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
                title: 'Data dihapus'
            })

        });
    $(document).on('click', '.cekbox_semua', function () {
        var isChecked = $(this).prop("checked");
        $('.centang_cekbox').prop("checked", isChecked).val(isChecked ? 'Y' : 'N');
        $('.centang_cekbox').prop("disabled", isChecked);

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
    $('#post_data').submit(function(event) {
            event.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin dengan revisi ini?',
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


