
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')

@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="card">
        {{-- <div class="row"> --}}
            <div class="card-header ">
                {{-- <div class="" style="position: relative; left: 0px; top: 0px; background-color:#edf4fc;"> --}}
                    <div class="card-header" style="border: 2px solid #bbbbbb;">
                            <form id="form_report" action="{{ route('job_order.unloading_plan') }}" method="GET">
                                <div class="row" >
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Status JO<span class="text-red">*</span></label>
                                            <select class="form-control selectpicker" name="tipe" id="tipe" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                                {{-- @foreach ($kasBank as $kb)
                                                    <option value="{{$kb->id}}" <?= $request['tipe'] == $kb->id ? 'selected':''; ?> >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                                @endforeach --}}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label for="">&nbsp;</label>
                                        <div class="d-flex justify-content-start" style="gap: 5px;">
                                               <button type="submit" class=" btn btn-primary radiusSendiri " onclick=""><i class="fas fa-search"></i> <b> Tampilkan Data</b></button>
                                               <button type="button" class=" btn btn-success radiusSendiri " onclick=""><i class="fas fa-file-excel"></i> <b> Export Excel</b></button>
                                        </div>
                                    </div>
                                    
                                </div>
                               
                            </form>
                            <div class="form-group">
                                {{-- <button type="button" class="btn btn-sm btn-success" onclick="download_report()"><i class="fas fa-file-excel"></i> Export to Excel</button> --}}
                            </div>
                    </div><!-- /.card-header -->
                {{-- </div> --}}
            </div>
            
            <div class="card-body">
               <section class="col-lg-12" id="show_report">

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                        <th style="width:30px"><div class="btn-group"></div></th>
                        <th>No. Kontainer</th>
                        <th>Pengirim</th>
                        <th>Pelayaran</th>
                        <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($dataJO))
                           
                            @foreach($dataJO as $item)
                                @php
                                    $cekDetail = true; 
                                @endphp
                                @foreach($dataJODetail as $item1)
                                    @if($item->id != $item1->id_jo)
                                        @php
                                            $cekDetail = false; 
                                        @endphp
                                    @endif
                                @endforeach
                                   
                                @if($cekDetail)
                                    <tr>
                                        <td colspan="6">{{ $item->no_jo }} - {{ $item->status }}</td>
                                    </tr>
                                 @foreach($dataJODetail as $item1)
                                    @if($item->id == $item1->id_jo)
                                    
                                    <tr>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button>
                                                <ul class="dropdown-menu" style="">
                                                    <li><a class="dropdown-item" href="{{ route('job_order.edit', [$item->id]) }}"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li>
                                                    <li><a class="dropdown-item" href="{{ route('job_order.destroy', $item->id) }}"><span class="fas fa-trash" style="width:24px"></span>Hapus</a></li>
                                                    <li><a class="dropdown-item" href="https://testapps.pjexpress.co.id/index.php/c_cetak_invoice/cetak/4891"><span class="fas fa-print" style="width:24px"></span>Cetak</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>{{ $item1->no_kontainer }}</td>
                                        <td>{{ $item->kode }} - {{ $item->nama_cust }}</td>
                                        <td>{{ $item->nama_supp }}</td>
                                        <td>{{ $item1->status }}</td>
                                    </tr>
                                    @endif
                                 @endforeach
                                @endif

                            @endforeach
                        @endif

                    </tbody>
                </table>
               </section>
            </div>
        {{-- </div> --}}
    </div>
</div>
<script>
    $(document).ready(function() {
         function delete_data(table,id){
                    $('#form_delete').attr('action', "https://testapps.pjexpress.co.id/index.php/c_lap_invoice/delete_data");
                    $('#form_delete').find('#id').attr('name','invoice_id');
                    $('#form_delete').find('#id').val(id);
                    $('#form_delete').find('#table').val(table);
                    
                    $('#confirm_dialog').modal('show');
                }
        $('#tanggal_awal').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
        });
        $('#tanggal_akhir').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
        });
    });

</script>
@endsection
