
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('chassis.index')}}">Chassis</a></li>
<li class="breadcrumb-item">Create</li>

@endsection

@section('content')
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
    <form action="{{ route('chassis.store') }}" method="POST" >
    @csrf
    <div class="row">
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="">Kode*</label>
                        <input required type="text"  name="kode" class="form-control" value="{{old('kode','')}}" >                         
                    </div>
                  
                    <div class="form-group">
                        <label for="">Karoseri</label>
                        <input required type="text"  name="karoseri" class="form-control" value="{{old('karoseri','')}}" >                         
                    </div>

                    <div class="form-group">
                        <label for="">Model</label>
                        <select class="form-control select2" style="width: 100%;" id='model_id' name="model_id">
                            @foreach ($model_chassis as $model)
                                <option value="{{$model->id}}">{{ $model->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col">
                        <button type="submit" class="btn btn-primary"><strong>Simpan</strong></button>
                    </div>
                </div>
            </div>
        </div>
 
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                 
                    <button type="button" class="btn btn-primary" onclick="open_detail('')">
                        <i class="fas fa-plus-circle"></i> <strong>DOKUMEN</strong>
                    </button>          
                </div>
                <div class="card-body">
                    
                    <div class="row" style='margin-top:5px;'>
                        <div class='col-12 table-responsive'>
                            <table class="table table-hover table-bordered table-striped text-nowrap" id='table_dokumen'>
                                <thead>
                                    <tr>
                                        <th style="width:30px"></th>
                                        <th>Jenis</th>
                                        <th>Nomor</th>
                                        <th>Berlaku</th>
                                        <th>Pengingat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($load_main_detail['data'])){
                                        foreach($load_main_detail['data'] as $key=>$value){ ?>
                                         <tr id='<?=$key?>'>
                                            <td>
                                                <div class="btn-group">
                                                  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                  </button>
                                                  <ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail(<?=$key?>)"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail(<?=$key?>)"><span class="fas fa-eraser" style="width:24px"></span>Hapus</a></li>
                                                  </ul>
                                                </div>
                                            </td>
                                            <td id='dokumen_id_<?=$key?>' hidden><?=$value->dokumen_id?></td>
                                            <td id='is_reminder_<?=$key?>' hidden><?=$value->is_reminder?></td>
                                            <td id='reminder_hari_<?=$key?>' hidden><?=$value->reminder_hari?></td>
                                            <td id='jenis_<?=$key?>'><?=$value->jenis?></td>
                                            <td id='nomor_<?=$key?>'><?=$value->nomor?></td>
                                            <td id='berlaku_hingga_<?=$key?>'><?=$value->berlaku_hingga?></td>
                                            <td id='reminder_deskripsi_<?=$key?>'><?=$value->reminder_deskripsi?></td>
                                        </tr>
                                    <?php } } ?>
                                </tbody>
                              </table>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
    </form>

    <div class="modal fade" id="dokumen_dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Dokumen</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id='form_add_detail'>
                    <input type="hidden" name="key" id="key">
                    <input type="hidden" name="ekor_id" id="ekor_id">  
                    <div class="form-group">
                        <label for="jenis">Jenis<span style='color:red'>*</span></label>
                        <input type="text" name="jenis" class="form-control" id="jenis" placeholder=""> 
                    </div>
                    <div class="form-group">
                        <label for="nomor">Nomor<span style='color:red'>*</span></label>
                        <input type="text" name="nomor" class="form-control" id="nomor" placeholder=""> 
                    </div>
                    <div class="form-group">
                        <label for="berlaku_hingga">Berlaku hingga</label>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                          </div>
                          <input type="text" name="berlaku_hingga" autocomplete="off" class="date form-control" id="berlaku_hingga" placeholder="dd/mm/yyyy"> 
                        </div>
                    </div>
                    <div class='form-group'>
                        <label for="pengingat">Pengingat (H-)</label>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text"><input type="checkbox" id="check_is_reminder" onclick='check_reminder(this)'></span>
                          </div>
                          <input type="hidden" id="is_reminder" name='is_reminder' value="N">
                          <input type="text" name="reminder_hari" id="reminder_hari" class="form-control numaja" placeholder='Berapa hari sebelumnya'readonly>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
              <button type="button" class="btn btn-sm btn-success" style='width:85px' onclick='save_detail()'>OK</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    

    <script>
         
        function open_detail(key){
            if(key===''){
                var last_id=($('#table_dokumen tr:last').attr('id'));
                if(typeof last_id === 'undefined') {
                    var last_id=0;
                }else{
                    var last_id=parseInt(last_id)+1
                }
                var idx=last_id;
                $('#jenis').val('');
                $('#nomor').val('');
                $('#berlaku_hingga').val('');
                $('#is_reminder').val('N');
                $('#check_is_reminder').attr('checked',false);
                $('#reminder_hari').val('');
                $('#dokumen_id').val('');
            }else{
                var idx=key;
                $('#jenis').val($('#jenis_'+idx).text());
                $('#nomor').val($('#nomor_'+idx).text());
                $('#berlaku_hingga').datepicker('setDate',$('#berlaku_hingga_'+idx).text());
                // $('#berlaku_hingga').val($('#berlaku_hingga_'+idx).text());
                $('#is_reminder').val($('#is_reminder_'+idx).text());
                if($('#is_reminder_'+idx).text()=='Y'){
                    $('#check_is_reminder').attr('checked',true);
                    $('#reminder_hari').attr('readonly',false);
                }else{
                    $('#check_is_reminder').attr('checked',false);
                    $('#reminder_hari').attr('readonly',true);
                    $('#reminder_hari').val('');
                }
                $('#reminder_hari').val($('#reminder_hari_'+idx).text());
                $('#dokumen_id').val($('#dokumen_id_'+idx).text());
            }
            
            $('#key').val(idx);
            $('#dokumen_dialog').modal('show');
        }

       
       
    </script>
</div>
@endsection
