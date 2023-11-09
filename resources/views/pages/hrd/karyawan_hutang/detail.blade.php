@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')

@endsection

@section('content')
@include('sweetalert::alert')

<style>
#preview_foto_nota {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}

#preview_foto_barang {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}
 .dataTables_length {
    display: none;
}
</style>
<div class="container-fluid">
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
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{ route('karyawan_hutang.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                    <button class="btn btn-primary btn-responsive float-right radiusSendiri " data-toggle="modal" data-target="#modal_tambah" style="z-index: 10">
                        <i class="fa fa-plus-circle"> </i> Tambah Data
                    </button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row ">
                        <div class="col-lg-3">
                            <label>Karyawan</label>
                            <input type="text" class="form-control" id="default_karyawan_name" value="{{$dataKaryawanHutang->nama_panggilan}}" readonly="">
                            
                        </div>
                        <div class="col-lg-2">
                            <label>Total Hutang</label>
                            <div class="input-group mb-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" class="form-control" id="default_total_hutang" value="{{number_format($dataKaryawanHutang->total_hutang)}}" readonly="">
                            </div>
                        </div>
                    </div>
                    <table id="tabelDetail" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Tgl. Transaksi</th>
                                <th>Jenis</th>
                                <th>Debit</th>
                                <th>Kredit</th>
                                <th>Kas/Bank</th>
                                <th>Catatan</th>
                                <th></th>
                            </tr>
                          </thead>
                        <tbody>
                            @if (isset($dataDetailHutang))
                                @foreach ($dataDetailHutang as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y')}}</td>
                                    <td>{{$item->jenis}}</td>
                                    <td>Rp. {{number_format($item->debit)}}</td>
                                    <td>Rp. {{number_format($item->kredit)}}</td>
                                    <td>{{$item->nama_bank}}</td>
                                    <td>{{$item->catatan}}</td>
                                    <td>
                                        {{-- @if ($item->jenis!='POTONG') --}}
                                            <div class="btn-group dropleft">
                                                <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    
                                                    <button type="button" onclick="open_detail({{$item->id_kht}})"class="dropdown-item" {{--data-toggle="modal" data-target="#modal_edit_edit" --}}>
                                                            <span class="fas fa-edit mr-3"></span> Edit
                                                    </button>
                                                    {{-- <a href="{{route('karyawan_hutang.edit',[$item->id_kht])}}" class="dropdown-item ">
                                                        <span class="fas fa-edit mr-3"></span> Edit
                                                    </a> --}}
                                                    <a href="{{ route('karyawan_hutang.destroy', [$item->id_kht]) }}" class="dropdown-item" data-confirm-delete="true">
                                                        <span class="fas fa-trash mr-3"></span> Hapus
                                                    </a>
                                                    
                                                </div>
                                            </div>
                                        {{-- @else
                                            -
                                        @endif --}}
                                        <input type="hidden" id="hidden_jenis_transaksi_{{$item->id_kht}}" value="{{$item->jenis}}">
                                        <input type="hidden" id="hidden_tanggal_transaksi_{{$item->id_kht}}" value="{{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y')}}">
                                        <input type="hidden" id="hidden_total_hutang_{{$item->id_kht}}" value="{{$item->total_hutang}}">
                                        <input type="hidden" id="hidden_nominal_{{$item->id_kht}}" value="{{$item->nominal}}">
                                        <input type="hidden" id="hidden_catatan_{{$item->id_kht}}" value="{{$item->catatan}}">
                                        <input type="hidden" id="hidden_kas_bank_{{$item->id_kht}}" value="{{$item->kas_bank_id}}">
                                        
                                    </td>
                                </tr>
                                {{-- <div class="modal fade" id="modal_edit_{{$item->id_kht}}" >
                                        <div class="modal-dialog modal-lg ">
                                            <form action="{{ route('karyawan_hutang.update',[$item->id_kht]) }}" id="post_data" method="POST" >
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content radiusSendiri">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Form Data {{$item->id_kht}}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class='row'>
                                                            <div class='col-lg-12 col-md-12 col-12'>
                                                                <div class="form-group">
                                                                    <label for="tipe">Jenis Transaksi</label>
                                                                    <br>
                                                                    <div class="icheck-primary d-inline">
                                                                        <input id="jenis_hutang_{{$item->id_kht}}" type="radio" name="jenis" value="Hutang" {{$item->debit || $item->debit!=0?'checked':''}}>
                                                                        <label class="form-check-label" for="jenis_hutang_{{$item->id_kht}}">Kas Bon / Hutang</label>
                                                                    </div>
                                                                    <div class="icheck-primary d-inline ml-4">
                                                                        <input id="jenis_bayar_{{$item->id_kht}}" type="radio" name="jenis" value="Bayar"  {{$item->kredit|| $item->kredit!=0?'checked':''}}>
                                                                        <label class="form-check-label" for="jenis_bayar_{{$item->id_kht}}">Bayar Hutang / Cicilan</label>
                                                                    </div>
                                                                </div>  
                                                                <div class="form-group">
                                                                    <label for="tanggal">Tanggal Transaksi<span style='color:red'>*</span></label>
                                                                    <div class="input-group mb-0">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                                    </div>
                                                                    <input type="text" name="tanggal" class="date form-control" id="tanggal" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y')}}">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class='row'>
                                                                        <div class="form-group col-6 col-md-6 col-lg-6">
                                                                            <label for="select_karyawan">Karyawan<span style="color:red">*</span></label>
                                                                                <select disabled class="form-control select2  @error('select_karyawan') is-invalid @enderror" style="width: 100%;" id='select_karyawan' name="select_karyawan">
                                                                                    <option 
                                                                                    value="{{$dataKaryawanHutang->idKaryawan}}" 
                                                                                    {{old('select_karyawan')==$dataKaryawanHutang->id?'selected':''}} 
                                                                                    karyawan_hutang = "{{$dataKaryawanHutang->total_hutang}}"
                                                                                    >{{ $dataKaryawanHutang->nama_panggilan }} ({{$dataKaryawanHutang->namaPosisi}})</option>
                                                                            </select>
                                                                            @error('select_karyawan')
                                                                                <div class="invalid-feedback">
                                                                                    {{ $message }}
                                                                                </div>
                                                                            @enderror   
                                                                            <input type='hidden' id='karyawan_id_edit{{$item->id_kht}}' name='karyawan_id' value="">
                                                                        </div>                                              
                                                                        <div class='col-6 col-md-6 col-lg-6'>
                                                                            <label for="total_hutang">Total Hutang</label>
                                                                            <div class="input-group mb-0">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            <input type="text" name="total_hutang" class="form-control numaja uang" id="total_hutang_edit{{$item->id_kht}}" readonly placeholder="" value="{{number_format($item->total_hutang)}}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class='row'>
                                                                        <div class='col-6 col-md-6 col-lg-6'>
                                                                            <label for="potong_hutang">Nominal</label>
                                                                            <div class="input-group mb-0">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text">Rp</span>
                                                                            </div>
                                                                            @if ($item->debit || $item->debit!=0)
                                                                            <input type="text" name="nominal" class="form-control numaja uang" id="nominal_{{$item->id_kht}}" placeholder="" value="{{number_format( $item->debit)}}">
                                                                                
                                                                            @elseif($item->kredit || $item->kredit!=0)
                                                                            <input type="text" name="nominal" class="form-control numaja uang" id="nominal_{{$item->id_kht}}" placeholder="" value="{{number_format( $item->kredit)}}">
                                                                                
                                                                            @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class='col-6 col-md-6 col-lg-6'>
                                                                            <label for="catatan">Catatan</label>
                                                                            <input type="text" name="catatan" class="form-control" id="catatan_{{$item->id_kht}}" placeholder="" value="{{$item->catatan}}"> 
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="kas_bank_id">Kas / Bank<span style='color:red'>*</span></label>
                                                                    <select class="form-control select2  @error('select_kas_bank_edit_{{$item->id_kht}}') is-invalid @enderror" style="width: 100%;" id='select_kas_bank_edit_{{$item->id_kht}}' name="select_kas_bank_edit">
                                                                        @foreach ($dataKas as $data)
                                                                            <option 
                                                                            value="{{$data->id}}" 
                                                                            {{$item->kas_bank_id==$data->id?'selected':''}} 
                                                                            >{{ $data->nama }} </option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('select_kas_bank_edit_{{$item->id_kht}}')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror   
                                                                </div> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-sm btn-danger radiusSendiri p-2" style='width:85px' data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-sm btn-success radiusSendiri p-2" id="" style='width:85px'><i class="fa fa-fw fa-save"></i> Simpan</button> 
                                                    </div>
                                                </div>
                                            </form> 
                                        </div>
                                </div> --}}
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
<div class="modal fade" id="modal_tambah" >
        <div class="modal-dialog modal-lg ">
             <form action="{{ route('karyawan_hutang.store') }}" id="post_data" method="POST" >
              @csrf
                <div class="modal-content radiusSendiri">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                         <div class='row'>
                            <div class='col-lg-12 col-md-12 col-12'>
                                <input type="hidden" name="dariIndex" value="{{$dariIndex}}">

                                {{-- <div class="form-group">
                                    <label>Jenis Transaksi</label>
                                    <div class='row'>
                                        <div class="col-6">
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" id="jenis_hutang" name="jenis" value='Hutang' checked>
                                                <label style='font-weight:normal' for="jenis_hutang" class="custom-control-label">Kas Bon / Hutang</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" value='Bayar' id="jenis_bayar" name="jenis">
                                                <label style='font-weight:normal' for="jenis_bayar" class="custom-control-label">Bayar Hutang / Cicilan</label>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                 <div class="form-group">
                                    <label for="tipe">Jenis Transaksi</label>
                                    <br>
                                    
                                    <div class="icheck-primary d-inline">
                                        <input id="jenis_hutang" type="radio" name="jenis" value="Hutang" checked>
                                        <label class="form-check-label" for="jenis_hutang">Kas Bon / Hutang</label>
                                    </div>
                                    <div class="icheck-primary d-inline ml-4">
                                        <input id="jenis_bayar" type="radio" name="jenis" value="Bayar" >
                                        <label class="form-check-label" for="jenis_bayar">Bayar Hutang / Cicilan</label>
                                    </div>
                                 
                                </div>  
                                <div class="form-group">
                                    <label for="tanggal">Tanggal Transaksi<span style='color:red'>*</span></label>
                                    <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" name="tanggal" class="date form-control" id="tanggal"  placeholder="dd-M-yyyy" value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class='row'>
                                        {{-- <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="karyawan_id">Karyawan<span style='color:red'>*</span></label>
                                            <select id="select_karyawan" style="width:100%" data-placeholder="Pilih Karyawan">
                                                <option value=''></option>
                                            </select>
                                            <input type='hidden' id='karyawan_id' name='karyawan_id' value="">
                                        </div> --}}
                                        
                                        <div class="form-group col-6 col-md-6 col-lg-6">
                                            <label for="select_karyawan">Karyawan<span style="color:red">*</span></label>
                                                <select disabled class="form-control select2  @error('select_karyawan') is-invalid @enderror" style="width: 100%;" id='select_karyawan' name="select_karyawan">
                                                    <option 
                                                    value="{{$dataKaryawanHutang->idKaryawan}}" 
                                                    {{old('select_karyawan')==$dataKaryawanHutang->id?'selected':''}} 
                                                    karyawan_hutang = "{{$dataKaryawanHutang->total_hutang}}"
                                                    >{{ $dataKaryawanHutang->nama_panggilan }} ({{$dataKaryawanHutang->namaPosisi}})</option>
                                            </select>
                                            @error('select_karyawan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror   
                                            <input type='hidden' id='karyawan_id' name='karyawan_id' value="">
                                        </div>                                              
                                        <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="total_hutang">Total Hutang</label>
                                            <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" name="total_hutang" class="form-control numaja uang" id="total_hutang" readonly placeholder="" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class='row'>
                                        <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="potong_hutang">Nominal</label>
                                            <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" name="nominal" class="form-control numaja uang" id="nominal" placeholder="" value="">
                                            </div>
                                        </div>
                                        <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="catatan">Catatan</label>
                                            <input type="text" name="catatan" class="form-control" id="catatan" placeholder="" value=""> 
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="kas_bank_id">Kas / Bank<span style='color:red'>*</span></label>
                                    <select id="select_kas_bank" style="width:100%" data-placeholder="Pilih Kas / Bank">
                                        <option value=''></option>
                                    </select>
                                    <input type='hidden' id='kas_bank_id' name='kas_bank_id' value="">
                                </div> --}}
                                <div class="form-group ">
                                    <label for="kas_bank_id">Kas / Bank<span style='color:red'>*</span></label>
                                    <select class="form-control select2  @error('select_kas_bank') is-invalid @enderror" style="width: 100%;" id='select_kas_bank' name="select_kas_bank">
                                        <option value="">Pilih Kas / Bank</option>
                                        @foreach ($dataKas as $data)
                                            <option 
                                            value="{{$data->id}}" 
                                            {{1==$data->id?'selected':''}} 
                                            >{{ $data->nama }} </option>
                                        @endforeach
                                    </select>
                                    @error('select_kas_bank')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror   
                                </div> 
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="tanggal_mulai">Tanggal Mulai<span style='color:red'>*</span></label>
                                <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" autocomplete="off" name="tanggal_mulai" class="form-control" id="tanggal_mulai" placeholder="dd-M-yyyy" value="">
                                </div>
                            </div>
                           
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="select_kendaraan">Kendaraan<span style="color:red">*</span></label>
                                    <select class="form-control select2  @error('select_kendaraan') is-invalid @enderror" style="width: 100%;" id='select_kendaraan' name="select_kendaraan">
                                    <option value="">Pilih Jenis Kendaraan</option>
                                    @foreach ($dataKendaraan as $data)
                                        <option value="{{$data->id}}" {{old('select_kendaraan')==$data->id?'selected':''}} >{{ $data->no_polisi }} ({{$data->kategoriKendaraan}})</option>
                                    @endforeach
                                </select>
                                @error('select_kendaraan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror   
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="detail_perawatan">Detail Perawatan<span style='color:red'>*</span></label>
                                <textarea rows="4" name="detail_perawatan" class="form-control" id="detail_perawatan" placeholder=""></textarea> 
                            </div>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger radiusSendiri p-2" style='width:85px' data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-success radiusSendiri p-2" id="" style='width:85px'><i class="fa fa-fw fa-save"></i> Simpan</button> 
                    </div>
                </div>
              </form> 
        </div>
</div>
<div class="modal fade" id="modal_edit" >
        <div class="modal-dialog modal-lg ">
            <form action="" id="post_data" method="POST" >
            <input type="hidden" name="key" id="key">
            {{-- <form action="{{ route('karyawan_hutang.update',[$item->id_kht]) }}" id="post_data" method="POST" > --}}
                @csrf
                @method('PUT')
                <div class="modal-content radiusSendiri">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Data </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class='row'>
                            <div class='col-lg-12 col-md-12 col-12'>
                                <div class="form-group">
                                    <label for="tipe">Jenis Transaksi</label>
                                    <br>
                                    <div class="icheck-primary d-inline">
                                        <input id="jenis_hutang_edit" type="radio" name="jenis_edit" value="Hutang" >
                                        <label class="form-check-label" for="jenis_hutang_edit">Kas Bon / Hutang</label>
                                    </div>
                                    <div class="icheck-primary d-inline ml-4">
                                        <input id="jenis_bayar_edit" type="radio" name="jenis_edit" value="Bayar">
                                        <label class="form-check-label" for="jenis_bayar_edit">Bayar Hutang / Cicilan</label>
                                    </div>
                                </div>  
                                <div class="form-group">
                                    <label for="tanggal">Tanggal Transaksi<span style='color:red'>*</span></label>
                                    <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" name="tanggal_edit" class="date form-control" id="tanggal_edit" placeholder="dd-M-yyyy" value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class='row'>
                                        <div class="form-group col-6 col-md-6 col-lg-6">
                                            <label for="select_karyawan_edit">Karyawan<span style="color:red">*</span></label>
                                                <select disabled class="form-control select2  @error('select_karyawan_edit') is-invalid @enderror" style="width: 100%;" id='select_karyawan_edit' name="select_karyawan_edit">
                                                    <option 
                                                    value="{{$dataKaryawanHutang->idKaryawan}}" 
                                                    {{old('select_karyawan_edit')==$dataKaryawanHutang->id?'selected':''}} 
                                                    karyawan_hutang = "{{$dataKaryawanHutang->total_hutang}}"
                                                    >{{ $dataKaryawanHutang->nama_panggilan }} ({{$dataKaryawanHutang->namaPosisi}})</option>
                                            </select>
                                            @error('select_karyawan_edit')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror   
                                            <input type='hidden' id='karyawan_id_edit' name='karyawan_id_edit' value="">
                                        </div>                                              
                                        <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="total_hutang">Total Hutang</label>
                                            <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" name="total_hutang_edit" class="form-control numaja uang" id="total_hutang_edit" readonly placeholder="" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class='row'>
                                        <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="potong_hutang">Nominal</label>
                                            <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                                <input type="text" name="nominal_edit" class="form-control numaja uang" id="nominal_edit" placeholder="" value="">
                                            </div>
                                        </div>
                                        <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="catatan">Catatan</label>
                                            <input type="text" name="catatan_edit" class="form-control" id="catatan_edit" placeholder="" value=""> 
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="select_kas_bank_edit">Kas / Bank<span style='color:red'>*</span></label>
                                    <select class="form-control select2  @error('select_kas_bank_edit') is-invalid @enderror" style="width: 100%;" id='select_kas_bank_edit' name="select_kas_bank_edit">
                                        <option value="">Pilih Kas / Bank</option>
                                        @foreach ($dataKas as $data)
                                            <option 
                                            value="{{$data->id}}">{{ $data->nama }} </option>
                                        @endforeach
                                    </select>
                                    @error('select_kas_bank_edit')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror   
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger radiusSendiri p-2" style='width:85px' data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-success radiusSendiri p-2" id="" style='width:85px'><i class="fa fa-fw fa-save"></i> Simpan</button> 
                    </div>
                </div>
            </form> 
        </div>
</div>
<script type="text/javascript">
function open_detail(key){
        
            var idx=key;
            $('#select_kas_bank_edit').val($('#hidden_kas_bank_'+idx).val()).trigger('change');
            console.log($('#hidden_jenis_transaksi_'+idx).val());
            if ($('#hidden_jenis_transaksi_'+idx).val()=='BAYAR') {
                 $('#jenis_bayar_edit').prop('checked',true);
            } else {
                 $('#jenis_hutang_edit').prop('checked',true);
            }
            $('#gaji_detail_id').val($('#hidden_jenis_transaksi_'+idx).val());
            $('#tanggal_edit').val($('#hidden_tanggal_transaksi_'+idx).val());
            $('#total_hutang_edit').val(addPeriod($('#hidden_total_hutang_'+idx).val(),','));
            $('#nominal_edit').val(addPeriod($('#hidden_nominal_'+idx).val(),','));
            $('#catatan_edit').val($('#hidden_catatan_'+idx).val());
        // }
        $('#key').val(idx);
        $('#modal_edit').modal('show');
    }
$(document).ready(function () {

        var cekerror= <?php echo json_encode($errors->any()); ?>;
        if (cekerror) {
                $("#modal_tambah").modal("show");
        }
       
        var id_karyawan = $('#select_karyawan').val();
        var selectedOption = $('#select_karyawan').find('option:selected');
        var karyawan_hutang = selectedOption.attr('karyawan_hutang');

        $('#karyawan_id').val(id_karyawan);
        $('#total_hutang').val(karyawan_hutang?addPeriod(karyawan_hutang,','):0);
        $('#karyawan_id_edit').val(id_karyawan);

        $('#tabelDetail').dataTable({
            // scrollX: true,
            "aaSorting": [],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
        $('#tanggal').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
           // endDate: "0d"
        });
        $('#tanggal_edit').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
           // endDate: "0d"
        });
        $('#check_is_selesai').click(function(){
            if($(this).is(":checked")){
                $('#is_selesai').val('Y');
                $('#tanggal_selesai').attr('readonly',false);
                $('#tanggal_selesai').datepicker({
                    autoclose: true,
                    format: "dd-M-yyyy",
                    todayHighlight: true,
                    language:'en'
                });
				$('#tanggal_selesai').val(get_date_now);
				// console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#is_selesai').val('N');
                $('#tanggal_selesai').val('');
                $('#tanggal_selesai').attr('readonly',true);
                $('#tanggal_selesai').datepicker('destroy');
                // console.log("Checkbox is unchecked.");
            }
        });
    
    $('#post_data').submit(function(event) {
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
                    });

    if($("#select_kendaraan").val()=='')
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `KENDARAAN BELUM DIPILIH!`,
            })
            
            return;
        }
        
        event.preventDefault();
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
                    icon: 'success',
                    title: 'Data Disimpan'
                })
                setTimeout(() => {
                    this.submit();
                }, 200); // 2000 milliseconds = 2 seconds
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
            }
        })
    });
});
</script>
@endsection


