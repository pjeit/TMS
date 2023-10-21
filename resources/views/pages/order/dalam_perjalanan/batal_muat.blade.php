
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
    <form action="{{ route('dalam_perjalanan.update', [1]) }}" id="post_data" method="POST" >
        @csrf @method('PUT')
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('dalam_perjalanan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                <a href="{{ route('dalam_perjalanan.index') }}" class="btn btn-success radiusSendiri"><i class="fa fa-save"></i> Simpan</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                        <label>No. Sewa</label>
                        <input type="text" class="form-control" name='no_sewa' value="{{ $data['no_sewa'] }} ({{ date("d-M-Y", strtotime($data['tanggal_berangkat'])) }})" readonly>
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                        <label>Customer</label>
                        <input type="text" class="form-control" name='customer' value="{{ $data->getCustomer->kode }} - {{ $data->getCustomer->nama }}" readonly>
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                        <label>Tujuan</label>
                        <input type="text" class="form-control" name='tujuan' value="{{ $data['nama_tujuan'] }}" readonly>
                    </div>
                </div>
                <div class="grid">
                   <div class="form-group col-lg-4 col-md-4 col-sm-12">
                       <label>No. Sewa</label>
                       <input type="text" class="form-control" name='no_sewa' value="{{ $data['no_sewa'] }} ({{ date("d-M-Y", strtotime($data['tanggal_berangkat'])) }})" readonly>
                   </div>
                   <div class="form-group col-lg-4 col-md-4 col-sm-12">
                       <label>Customer</label>
                       <input type="text" class="form-control" name='customer' value="{{ $data->getCustomer->kode }} - {{ $data->getCustomer->nama }}" readonly>
                   </div>
               </div>
                <div class="grid">
                   <div class="form-group col-lg-4 col-md-4 col-sm-12">
                       <label>No. Sewa</label>
                       <input type="text" class="form-control" name='no_sewa' value="{{ $data['no_sewa'] }} ({{ date("d-M-Y", strtotime($data['tanggal_berangkat'])) }})" readonly>
                   </div>
                   <div class="form-group col-lg-4 col-md-4 col-sm-12">
                       <label>Customer</label>
                       <input type="text" class="form-control" name='customer' value="{{ $data->getCustomer->kode }} - {{ $data->getCustomer->nama }}" readonly>
                   </div>
               </div>
            </div>
        </div> 
    </form>
</div>


         
 
<script type="text/javascript">
    $(document).ready(function() {
       
    });
</script>

@endsection


