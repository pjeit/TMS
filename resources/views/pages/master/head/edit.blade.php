
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('head.index')}}">Head</a></li>
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

    <form data-action="{{ route('head.update', ['head' => $data->id]) }}" id="grup_forms" enctype="multipart/form-data" method="POST">

    @method('PUT')
    @csrf
    {{-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div> --}}
     
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{ route('head.index') }}"class="btn btn-secondary radiusSendiri float-left"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                    <button type="submit" class="btn btn-success radiusSendiri ml-3"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                            <label for="">Kategori Kendaraan</label>
                            <select class="form-control select2" style="width: 100%;" id='kategori' name="kategori">
                                @foreach ($kategoriTruck as $k)
                                    <option value="{{$k->id}}" {{($k->id == $data->id_kategori)? 'selected':'';}}>{{$k->nama}}</option>
                                @endforeach
                            </select>
                        </div> 
                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                            <label for="">Letak Kendaraan</label>
                            <select class="form-control select2" style="width: 100%;" id='kota' name="kota">
                                @foreach ($kota as $k)
                                    <option value="{{$k->id}}" {{($k->id == $data->kota_id)? 'selected':'';}}>{{$k->nama}}</option>
                                @endforeach
                            </select>
                        </div> 
                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                            <label for="">No. Polisi<span class="text-red">*</span></label>
                            <input required type="text"  name="no_polisi" id="no_polisi" class="form-control" value="{{ $data->no_polisi }}" >                         
                        </div>
                    </div>
                     <div class="row">
                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                            <label for="">Tahun Pembuatan</label>
                            <input required type="text" name="tahun_pembuatan" maxlength="4" class="form-control" value="{{$data->tahun_pembuatan}}" >
                        </div>          
                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                            <label for="">Warna</label>
                            <input required type="text" name="warna" class="form-control" value="{{$data->warna}}" >
                        </div>  
                        <div class="form-group col-sm-12 col-md-4 col-lg-4">
                            <label for="">Driver (Optional)</label>
                            <select class="form-control select2" style="width: 100%;" id='driver_id' name="driver_id">
                                <option value="">-- PILIH DRIVER --</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{$driver->id}}" {{($driver->id == $data->driver_id)? 'selected':'';}}>{{$driver->nama_lengkap}}</option>
                                @endforeach
                            </select>
                        </div>   
                    </div> 
                    <div class="row">
                        <div class="form-group col-sm-12 col-md-5 col-lg-5">
                            <label for="">No. Mesin</label>
                            <input required type="text" name="no_mesin" class="form-control" value="{{$data->no_mesin}}" >
                        </div>           
                        <div class="form-group col-sm-12 col-md-5 col-lg-4">
                            <label for="">No. Rangka</label>
                            <input required type="text"name="no_rangka" class="form-control" value="{{$data->no_rangka}}" >
                        </div>           
                        <div class="form-group col-sm-12 col-md-2 col-lg-3">
                            <label for="">Merk & Model</label>
                            <input required type="text" name="merk_model" class="form-control" value="{{$data->merk_model}}" >
                        </div>     
                    </div>
                   
                </div>
            </div>
        </div>
        
    </div>

    <div class='row' >
        <div class="col-lg-6 col-md-6 col-12">
            <button type="button" class="btn btn-sm btn-primary" onclick="open_detail('')"><i class='fas fa-plus-circle'></i><b style="font-size:16px">&nbsp; DOKUMEN</b></button>
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
                            <?php $data_berkas = json_decode($data['berkas']); ?>
                            <?php if(!empty($data_berkas)){
                                foreach( $data_berkas as $key => $value){ ?>
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
                                    <td id='dokumen_id_<?=$key?>' hidden><?=$value->id?></td>
                                    <td id='is_reminder_<?=$key?>' hidden><?=$value->is_reminder?></td>
                                    <td id='reminder_hari_<?=$key?>' hidden><?=$value->reminder_hari?></td>
                                    <td id='jenis_<?=$key?>'><?=$value->jenis?></td>
                                    <td id='nomor_<?=$key?>'><?=$value->nomor?></td>
                                    <td id='berlaku_hingga_<?=$key?>'><?= date("d-M-Y", strtotime($value->berlaku_hingga)) ?></td>
                                    <td id='reminder_hari_text_<?=$key?>'><?=$value->reminder_hari?></td>
                                </tr>
                            <?php }
                            }?>
                        </tbody>
                      </table>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

<div class="modal fade" id="confirm_dialog_detail">
    <div class="modal-dialog modal-sm">
      <div class="modal-content bg-warning">
        <div class="modal-header">
          <h5 class="modal-title">Apakah anda yakin akan menghapus data ini?</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-footer">
            <form id='form_delete_detail'>
                <input type='hidden' id='dokumen_id' name='dokumen_id'>
                <input type='hidden' id='id_tombol'>
            </form>
          <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">TIDAK</button>
          <button type="button" class="btn btn-sm btn-success" style='width:85px' onclick='delete_datadetail()'>YA</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

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
                      <input type="text" name="berlaku_hingga" autocomplete="off" class="date form-control" id="berlaku_hingga" placeholder="dd/M/yyyy"> 
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
    $(document).ready(function(e){
        $('#berlaku_hingga').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
        });
         $('#no_polisi').keyup(function() {
            let inputValue = $(this).val();
            let outputValue = inputValue.replace(/\s+/g, '-');
            console.log(outputValue);
            $(this).val(outputValue);
        });
        // Submit form data via Ajax
        $("#grup_forms").on('submit', function(e){
            var array_dokumen=[];
            
            var formData = new FormData(this);
            
            var myjson;
            
            $('#table_dokumen > tbody  > tr').each(function(idx) {
                var id=$(this).attr('id');
                if(typeof id !== 'undefined') {
                    myjson='{"dokumen_id":'+JSON.stringify($('#dokumen_id_'+id).text())+',"jenis":'+JSON.stringify($('#jenis_'+id).text())+', "nomor":'+JSON.stringify($('#nomor_'+id).text())+',  "berlaku_hingga":'+JSON.stringify($('#berlaku_hingga_'+id).text())+', "is_reminder":'+JSON.stringify($('#is_reminder_'+id).text())+', "reminder_hari":'+JSON.stringify($('#reminder_hari_'+id).text())+'}';
                    var obj=JSON.parse(myjson);
                    array_dokumen.push(obj);
                }
            });

            console.log(array_dokumen);
            formData.append('dokumen', JSON.stringify(array_dokumen));
            var url = $(this).attr('data-action');
            
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response){
                    if (response.hasOwnProperty('id')) {
                        toastr.success(response.message);
                        window.location.href = '{{ route("head.index") }}';
                    } else {
                        toastr.error(response.message);
                    } 
                }
            });
        });
    });
    function check_reminder(row){
        if($(row).is(":checked")){
            $('#is_reminder').val('Y');
            $('#reminder_hari').attr('readonly',false);
            //$('#reminder_hari').val('');
            // console.log("Checkbox is checked.");
        }else if($(row).is(":not(:checked)")){
            $('#is_reminder').val('N');
            //$('#reminder_hari').val('');
            $('#reminder_hari').attr('readonly',true);
            // console.log("Checkbox is unchecked.");
        }
    }
    function check_first(){
        $.ajax({
            type:"POST",
            url:"",
            data:$("#c_kendaraan_forms").serialize(),
            dataType:"json",
            success:function (data) {
                if( !$.isArray(data) ||  !data.length ) {
                    $("#c_kendaraan_forms").submit();
                }else{
                    for(var i in data){
                        toastr.error(data[i]);
                    }
                }
            }
        });   
    }
  
    function delete_data(){
        if($('#kendaraan_id').val()!=''){
            $('#form_delete').attr('action', "");
            $('#form_delete').find('#id').attr('name','kendaraan_id');
            $('#form_delete').find('#id').val($('#kendaraan_id').val());
            $('#form_delete').find('#table').val('karyawan');
            
            $('#confirm_dialog').modal('show');
        }else{
            $('#confirm_dialog_reset').modal('show');
        }
    }
    
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
    
    function save_detail(){
        var key=$('#key').val();
        if($('#jenis').val()==''){toastr.error('Jenis dokumen harus diisi');return;}
        if($('#nomor').val()==''){toastr.error('Nomor dokumen harus diisi');return;}
        if($('#is_reminder').val()=='Y'){
            var reminder_desc='Ya ('+$('#reminder_hari').val()+' hari)';
        }else{
            var reminder_desc='Tidak';
        }
        var exist=$('#table_dokumen tbody').find('#'+key).attr('id');
        if(typeof exist === 'undefined') {
            
            var new_row='<tr id="'+key+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail('+key+')"><span class="fas fa-edit"></span> Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail('+key+')"><span class="fas fa-eraser"></span> Hapus</a></li></ul></div></td><td id="dokumen_id_'+key+'" hidden>'+$('#dokumen_id').val()+'</td><td id="jenis_'+key+'">'+$('#jenis').val()+'</td><td id="nomor_'+key+'">'+$('#nomor').val()+'</td><td id="berlaku_hingga_'+key+'">'+$('#berlaku_hingga').val()+'</td><td id="reminder_hari_text_'+key+'">'+reminder_desc+'</td><td id="is_reminder_'+key+'" hidden>'+$('#is_reminder').val()+'</td><td id="reminder_hari_'+key+'" hidden>'+$('#reminder_hari').val()+'</td></tr>';
            
            $('#table_dokumen > tbody:last-child').append(new_row);
        }else{
            $('#jenis_'+key).text($('#jenis').val());
            $('#nomor_'+key).text($('#nomor').val());
            $('#berlaku_hingga_'+key).text($('#berlaku_hingga').val());
            $('#is_reminder_'+key).text($('#is_reminder').val());
            $('#reminder_hari_'+key).text($('#reminder_hari').val());
            $('#dokumen_id_'+key).text($('#dokumen_id').val());
            $('#reminder_hari_text_'+key).text(reminder_desc);
            console.log($('#reminder_hari_'+key).text()+'asdas');    
        }
        $('#dokumen_dialog').modal('hide');
    }
    
    function delete_detail(id_tombol){
        if($('#dokumen_id_'+id_tombol).text()!=''){
            $('#form_delete_detail').find('#dokumen_id').val($('#dokumen_id_'+id_tombol).text());
            $('#form_delete_detail').find('#id_tombol').val(id_tombol);
            
            $('#confirm_dialog_detail').modal('show');
        }else{
            $('#'+id_tombol).remove();
        }
    }
    function delete_datadetail(){
        var id_delete=$("#form_delete_detail").find('#id_tombol').val();
        $('#confirm_dialog_detail').modal('hide');
        $('#'+id_delete).remove();
    }
</script>


@endsection
