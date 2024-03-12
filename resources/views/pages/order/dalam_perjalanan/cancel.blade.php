
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
    <form action="{{ route('dalam_perjalanan.save_cancel', [ $data['id_sewa'] ]) }}" method="POST" id="post_data">
        @csrf 
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('dalam_perjalanan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            <div class="card-body">
                
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12" style=" border-right: 1px solid rgb(172, 172, 172);">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group ">
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
                            </div>
                            <div class="col-6">
                                <div class="form-group ">
                                    <label for="tanggal_cancel">Tanggal Cancel<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input disabled type="text" autocomplete="off"  class="form-control date" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse(now())->format('d-M-Y')}}">
                                        <input type="hidden" autocomplete="off" name="tanggal_cancel" class="form-control date" id="tanggal_cancel" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse(now())->format('d-M-Y')}}">
                                    </div>
                                </div> 
                            </div>
                        </div>
                        
                        <div class="form-group ">
                            <label for="no_akun">Customer</label>
                            <input type="text" id="customer" name="customer" class="form-control" value="[{{$data->getCustomer->kode}}] {{$data->getCustomer->nama}}" readonly>                         
                        </div>  

                        <div class="form-group ">
                            <label for="no_akun">Tujuan</label>
                            <input type="text" id="tujuan" name="tujuan" class="form-control" value="{{$data->nama_tujuan}}" readonly>                         
                        </div>  
                        {{-- <div class="form-group">
                            <label for="no_akun">{{$data->jenis_tujuan == "FTL"?'No. Kontainer':'No. Koli'}}</label>
                            @if ($data->no_kontainer_jod && $data->jenis_order =="INBOUND")
                                <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" readonly value="{{$data->no_kontainer_jod}}" >                         
                            @else
                                <input type="text" id="no_kontainer"  ="no_kontainer" class="form-control" value="{{$data->no_kontainer}}" >                         
                            @endif
                        </div> 
                            @if ($data->seal_pelayaran_jod&&$data->jenis_order =="INBOUND")
                            <div class="form-group ">
                                <label for="seal">Segel Kontainer</label>
                                <input readonly type="text" id="seal" name="seal" class="form-control"value="{{$data->seal_pelayaran_jod}}" >
                            </div> 
                        @endif
                        
                        <div class="form-group">
                            <label for="no_akun">No. Surat Jalan</label>
                            <input type="text" id="surat_jalan"  name="surat_jalan" class="form-control" value="{{$data->no_surat_jalan}}" >                         
                        </div>  --}}

                        <div class="form-group">
                            <label for="alasan_cancel">Alasan Cancel Perjalanan<span style="color: red;">*</span></label>
                            <textarea name="alasan_cancel" required class="form-control" id="alasan_cancel" rows="5" value=""></textarea>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="tanggal_pencairan">Tanggal Kembali</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input disabled type="text" autocomplete="off" class="form-control date" placeholder="dd-M-yyyy" value="{{\Carbon\Carbon::parse(now())->format('d-M-Y')}}">
                                    <input type="hidden" autocomplete="off" name="tanggal_kembali" class="form-control date" id="tanggal_kembali" placeholder="dd-M-yyyy" value="{{\Carbon\Carbon::parse(now())->format('d-M-Y')}}">
                                </div>
                            </div> 

                            <div class="form-group col-4">
                                <label for="no_akun">Kendaraan</label>
                                <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{$data->no_polisi}}" readonly>                         
                            </div>  

                            <div class="form-group col-8">
                                <label for="no_akun">Driver</label>
                                @if ($data->id_supplier==null)
                                    <input type="text" id="driver" name="driver" class="form-control" value="{{$data->nama_driver}}" readonly>     
                                    <input type="hidden" name="id_karyawan" id="id_karyawan" value="{{$data->id_karyawan}}"> 
                                @else
                                    <input type="text" class="form-control" readonly="" name="driver" value="DRIVER REKANAN {{ $supplier->nama }}">
                                @endif
                            </div> 

                            @if ($data->id_supplier==null)
                                @if (isset($ujr))

                                    <div class="form-group col-12">
                                        <label for="total_uang_jalan">Total Uang Jalan</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input readonly="" value="{{ number_format(($data->getUJRiwayat[0]->total_uang_jalan + $data->getUJRiwayat[0]->total_tl)-$data->getUJRiwayat[0]->potong_hutang ) }}" type="text" name="total_uang_jalan" class="form-control numaja uang" id="total_uang_jalan" placeholder="">
                                        </div>
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="">Uang Jalan Kembali<span class="text-red">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" name="uang_jalan_kembali" required id="uang_jalan_kembali" class="form-control numaja uang" value="{{ number_format(($data->getUJRiwayat[0]->total_uang_jalan + $data->getUJRiwayat[0]->total_tl)-$data->getUJRiwayat[0]->potong_hutang ) }}">
                                        </div>
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="">Kas / Bank<span class="text-red">*</span></label>
                                        <select class="form-control select2" name="pembayaran" id="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                            @foreach ($dataKas as $kb)
                                                <option value="{{$kb->id}}" >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                            @endforeach
                                                <option value="HUTANG KARYAWAN">HUTANG KARYAWAN</option>
                                        </select>
                                    </div>

                                @endif
                            @endif
                            
                        </div>
                    </div>
                </div>
                
            </div>
        </div> 
        <div class="card radiusSendiri w-75">
            <div class="card-header">
            </div>
            <div class="card-body">
                {{-- <table class="table table-bordered card-outline card-primary table-hover" id="sortable" >
                        <thead>
                            <tr>
                                <th colspan="7">BIAYA OPERASIONAL</th>
                            </tr>
                        <tr>
                            <th></th>
                            <th>Deskripsi</th>
                            <th>Total Dicairkan</th>
                            <th>Kas Kembali</th>
                        </tr>
                        </thead>
                        <tbody id="tampunganTabel">
                        @php
                            $index=0;
                        @endphp
                        @foreach ($dataOperasional as $key => $value)
                                <tr id="{{$index}}">
                                    <td id="id_sewa_operasional_tabel_{{$index}}" >
                                        @php
                                            $data_id_sewa = explode(',',$value->so_id_sewa);
                                            $id_operasional = explode(',',$value->so_id);
                                        @endphp
                                            <input type="hidden" id="id_sewa_operasional_data_{{$index}}"  class="id_operasional" name="data[{{$index}}][id_sewa_operasional_data]" value="{{$value->so_id_sewa}}" readonly>
                                            <input type="hidden" id="id_operasional_data_{{$index}}"  class="id_operasional" name="data[{{$index}}][id_operasional_data]" value="{{$value->so_id}}" readonly>
                                        <input type="hidden" id="id_pembayaran_operasional_{{$index}}"  class="id_pembayaran_operasional" name="data[{{$index}}][id_pembayaran_operasional]" value="{{$value->so_id_pembayaran}}" readonly>
                                    </td>
                                    <td id="deskripsi_tabel_{{$index}}" >
                                        <input type="text" name="data[{{$index}}][deskripsi_data]" id="deskripsi_data_{{$index}}" value="{{$value->so_deskripsi}}" class="form-control deskripsi_hardcode ambil_text_deskripsi" readonly>
                                    </td>
                                    <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{$index}}">
                                        <input type="text" name="data[{{$index}}][total_dicairkan]" id="total_dicairkan_{{$index}}" value="{{number_format($value->so_total_dicairkan) }}" class="form-control uang numaja nominal_hardcode"readonly>
                                        <input type="hidden" name="data[{{$index}}][rincian]" value="UANG KEMBALI ({{$value->counter_data}}X {{$value->so_deskripsi}})->KENDARAAN : [{{$value->sewa_kendaraan}}] - DRIVER:({{$value->sewa_driver}}) - SEWA :({{$value->no_sewa}})">
                                        <label for="">Rincian Biaya Operasional:</label>
                                        @foreach ($data_rincian as $rinci )
                                            @if ($value->so_id_pembayaran == $rinci['id_bayar'] && $value->so_deskripsi == $rinci['deskripsi'])
                                                <span class="badge badge-danger">[{{$rinci['kendaraan']}}] {{$rinci['driver']}} Rp.{{number_format($rinci['rincian'])}}</span> <br>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($value->so_id_pembayaran == null)
                                            <div class="form-group col-12">
                                                <input type="hidden" name="data[{{$index}}][kembali]" id="kembali_{{$index}}" value="DATA_DI_HAPUS" class="form-control" readonly>
                                                <span class="badge badge-warning">Data Dihapus !</span> <br>
                                                
                                            </div>
                                        @else
                                            <div class="form-group col-12">
                                                <select class="form-control select2" name="data[{{$index}}][kembali]" id="kembali_{{$index}}" data-live-search="true" data-show-subtext="true" data-placement="bottom" width="100">
                                                    @if ($value->so_deskripsi=="SEAL PELAYARAN"||$value->so_deskripsi=="PLASTIK")
                                                        <option value="KEMBALI_STOK" >KEMBALI SEBAGAI STOK</option>
                                                    @endif
                                                    @foreach ($dataKas as $kb)
                                                        <option value="{{$kb->id}}" {{ $kb->id == 2? 'selected':''; }} >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                                    @endforeach
                                                    
                                                </select>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                $index+=1;
                                @endphp
                            @endforeach
                    
                        
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table> --}}
                <table class="table table-bordered card-outline card-primary table-hover" id="sortable" >
                        <thead>
                            <tr>
                                <th colspan="7">BIAYA OPERASIONAL</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>Deskripsi</th>
                                <th>Total Dicairkan</th>
                                <th>Operasional Kembali</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="tampunganTabel">
                            @php
                                $index=0;
                            @endphp
                            @foreach ($dataOperasional as $key => $value)
                                <tr id="{{$index}}">
                                    <td id="id_sewa_operasional_tabel_{{$index}}" >
                                            <input type="hidden" id="id_sewa_operasional_data_{{$index}}"  class="id_sewa_operasional_data" name="data[{{$index}}][id_sewa_operasional_data]" value="{{$value->so_id_sewa}}" readonly>
                                            <input type="hidden" id="id_pembayaran_detail_{{$index}}"  class="id_operasional" name="data[{{$index}}][id_pembayaran_detail]" value="{{$value->so_id}}" readonly>
                                        <input type="hidden" id="id_pembayaran_operasional_{{$index}}"  class="id_pembayaran_operasional" name="data[{{$index}}][id_pembayaran_operasional]" value="{{$value->so_id_pembayaran}}" readonly>
                                    </td>
                                    <td id="deskripsi_tabel_{{$index}}" >
                                        <input type="text" name="data[{{$index}}][deskripsi_data]" id="deskripsi_data_{{$index}}" value="{{$value->so_deskripsi}}" class="form-control deskripsi_hardcode ambil_text_deskripsi" readonly>
                                    </td>
                                    <td style=" white-space: nowrap; text-align:right;" id="nominal_tabel_{{$index}}">
                                        <input type="text" name="data[{{$index}}][total_dicairkan]" id="total_dicairkan_{{$index}}" value="{{number_format($value->so_total_dicairkan) }}" class="form-control uang numaja nominal_hardcode"readonly>
                                        {{-- <input type="hidden" name="data[{{$index}}][rincian]" value="UANG KEMBALI (1X: {{$value->so_deskripsi}})->KENDARAAN : [{{$value->sewa_kendaraan}}] - DRIVER:({{$value->sewa_driver}}) - TUJUAN :({{$value->sewa_tujuan}}) - SEWA :({{$value->no_sewa}})"> --}}
                                        <input type="hidden" name="data[{{$index}}][rincian]" value="Pengembalian Operasional : {{$value->so_deskripsi}} 1 X >>[{{$data->getCustomer->kode}}] {{$data->getCustomer->nama}} - > {{$value->sewa_tujuan}} # {{$value->sewa_kendaraan}} ({{$value->sewa_driver}})">
                                    </td>
                                    <td>
                                        @if ($value->so_id_pembayaran == null && $value->so_id_kasbon)
                                            <div class="form-group col-12">
                                                <input type="hidden" name="data[{{$index}}][kembali]" id="kembali_{{$index}}" value="kasbon" class="form-control" readonly>
                                                <span class="badge badge-warning">Data kembali sebagai kasbon operasional</span><br>
                                            </div>
                                        @elseif($value->so_id_pembayaran == null && $value->so_id_stok)
                                            <div class="form-group col-12">
                                                <input type="hidden" name="data[{{$index}}][kembali]" id="kembali_{{$index}}" value="KEMBALI_STOK" class="form-control" readonly>
                                                <span class="badge badge-warning">Data kembali sebagai stok operasional</span><br>
                                            </div>
                                        @else
                                            <div class="form-group col-12">
                                                <select class="form-control select2" name="data[{{$index}}][kembali]" id="kembali_{{$index}}" data-live-search="true" data-show-subtext="true" data-placement="bottom" width="100">
                                                    {{-- @if ($value->so_deskripsi=="SEAL PELAYARAN"||$value->so_deskripsi=="PLASTIK")
                                                        <option value="KEMBALI_STOK" >KEMBALI SEBAGAI STOK</option>
                                                    @endif --}}
                                                    <option value="kasbon" {{ $value->id_kas_bank == null? 'selected':''; }}>KEMBALI SEBAGAI KASBON</option>
                                                    @foreach ($dataKas as $kb)
                                                        <option value="{{$kb->id}}" {{ $kb->id == $value->id_kas_bank? 'selected':''; }} >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            {{-- <input type="text" id="catatan" name="data[{{$index}}][catatan]" class="form-control" value="">   --}}
                                            <textarea name="data[{{$index}}][catatan]" required class="form-control" id="catatan" rows="5" value=""></textarea>
                                        </div>  
                                    </td>
                                </tr>
                                @php
                                $index+=1;
                                @endphp
                            @endforeach
                            
                        </tbody>
                        <tfoot>
                        </tfoot>
                </table>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function() {

    $(document).on('keyup', '#uang_jalan_kembali', function(){ 
        var total_uang_jalan = $('#total_uang_jalan').val();
        if(parseFloat(escapeComma(this.value)) > parseFloat(escapeComma(total_uang_jalan))){
            $('#uang_jalan_kembali').val(total_uang_jalan);
        }
    });
    $(document).on('focusout', '#uang_jalan_kembali', function(){ 
        check();
    });
    // document.getElementById("uang_jalan_kembali").addEventListener("focusout", myFunction);

    // function myFunction() {
    //     check();
    // }
    function check(){
        var total_uang_jalan = parseFloat(escapeComma($('#total_uang_jalan').val()));
        var uang_jalan_kembali = parseFloat(escapeComma($('#uang_jalan_kembali').val()));

        if(uang_jalan_kembali > total_uang_jalan){
            $('#uang_jalan_kembali').val(moneyMask(total_uang_jalan));
        }
    }
    
    $('#post_data').submit(function(event) {
            event.preventDefault();

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

@endsection


