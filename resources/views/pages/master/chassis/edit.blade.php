
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
<li class="breadcrumb-item">Edit</li>

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
    <form data-action="{{ route('chassis.update', ['chassis' => $data->id]) }}" method="POST" enctype="multipart/form-data" id="chassis_save">
    @method('PUT')
    @csrf

    <div class="row">
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{ route('chassis.index') }}"class="btn btn-secondary radiusSendiri float-left"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                    <button type="submit" class="btn btn-success radiusSendiri float-right"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
                    <div class="card-body">
                       <div class="form-group">
                        <label for="">Cabang<span class="text-red">*</span></label>
                        <select class="form-control select2" style="width: 100%;" id='cabang' name="cabang" required>
                            @foreach ($cabang as $cbg)
                                <option value="{{$cbg->id}}" {{$cbg->id==$data->cabang_id}}>{{ $cbg->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Kode*</label>
                        <input required type="text"  name="kode" class="form-control" value="{{ $data->kode }}" >                         
                    </div>
                  
                    <div class="form-group">
                        <label for="">Karoseri</label>
                        <input required type="text"  name="karoseri" class="form-control" value="{{$data->karoseri}}" >                         
                    </div>
                    <div class="form-group">
                        <label for="">Tahun Buat</label>
                        <input required type="text"  name="taun_buat" class="form-control" value="{{old('taun_buat',$data->taun_buat)}}" >                         
                    </div>
                    <div class="form-group">
                        <label for="">Model</label>
                        <select class="form-control select2" style="width: 100%;" id='model_id' name="model_id">
                            @foreach ($model_chassis as $model)
                                <option value="{{$model->id}}" <?= ($data->model_id == $model->id)? 'Selected':''; ?> >{{ $model->nama }}</option>
                            @endforeach
                        </select>
                    </div>
            

                </div>
                {{-- <div class="card-footer">
                 
                        <button type="submit" class="btn btn-success float-left"><strong>Simpan</strong></button>
                    
                </div> --}}
            </div>
        </div>
 
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card radiusSendiri">
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
                                    <?php if(!empty($dokumen)){
                                        foreach($dokumen as $key=>$value){ ?>
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
                                            <td id='jenis_<?=$key?>' class="jenisCek"><?=$value->jenis_chassis?></td>
                                            <td id='nomor_<?=$key?>'><?=$value->nomor?></td>
                                            <td id='berlaku_hingga_<?=$key?>'><?= date("d-M-Y", strtotime($value->berlaku_hingga)) ?></td>
                                            
                                            @if ($value->is_reminder == 'Y')
                                                <td id='reminder_deskripsi_<?=$key?>'>Ya (<?=$value->reminder_hari?> hari)</td>
                                            @else
                                                <td id='reminder_deskripsi_<?=$key?>'></td>
                                            @endif
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
                    {{-- <div class="form-group">
                        <label for="berlaku_hingga">Berlaku hingga</label>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                          </div>
                          <input type="text" name="berlaku_hingga" autocomplete="off" class="date form-control" id="berlaku_hingga" placeholder="dd/mm/yyyy"> 
                        </div>
                    </div> --}}
                    <div class="form-group">
                        <label for="">Berlaku hingga</label>
                        <input type="text" class="form-control" id="berlaku_hinggaDisplay" placeholder="DD-MMM-YYYY" >
                        <input type="hidden" id="berlaku_hingga" name="berlaku_hingga">
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
    
    </form>


    <script>

$(document).ready(function(e){
    // Submit form data via Ajax
    $("#chassis_save").on('submit', function(e){
            var array_dokumen=[];
            var formData = new FormData(this);
            
            var myjson;
            
            $('#table_dokumen > tbody  > tr').each(function(idx) {
                var id=$(this).attr('id');
                if(typeof id !== 'undefined') {
                    // ini semua kolom yang dikirim (untuk kebutuhan procedure)
                    myjson='{"dokumen_id":'+JSON.stringify($('#dokumen_id_'+id).text())+',"jenis":'+JSON.stringify($('#jenis_'+id).text())+', "nomor":'+JSON.stringify($('#nomor_'+id).text())+',  "berlaku_hingga":'+JSON.stringify($('#berlaku_hingga_'+id).text())+', "is_reminder":'+JSON.stringify($('#is_reminder_'+id).text())+', "reminder_hari":'+JSON.stringify($('#reminder_hari_'+id).text())+'}';
                    var obj=JSON.parse(myjson);
                    array_dokumen.push(obj);
                }
            });

            console.log(array_dokumen);
            formData.append('dokumen', JSON.stringify(array_dokumen));
            var url = $(this).attr('data-action');

            // var formData = $(this).serialize();
            // var url = $(this).attr('data-action');

            e.preventDefault();
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
                    $.ajax({
                        method: 'POST',
                        url: url,
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        cache: false,
                        processData:false,
                        success: function(response) {
                            if (response.hasOwnProperty('id')) {
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

                                window.location.href = '{{ route("chassis.index") }}';
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Terjadi kesalahan saat mengirim data.');
                        }
                    });
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
            // $('#berlaku_hingga').val('');
            $('#is_reminder').val('N');
            $('#check_is_reminder').attr('checked',false);
            $('#reminder_hari').val('');
            $('#dokumen_id').val('');
            $('#berlaku_hingga').val('');
            $('input[id="berlaku_hinggaDisplay"]').daterangepicker({
                opens: 'center',
                drops: "up",
                singleDatePicker: true,
                showDropdowns: true,
                autoApply: false,
                locale: {
                    format: 'DD-MMM-YYYY',
                }
            }, function(start, end, label) {
                const formattedDate = start.format('DD-MMM-YYYY');
                $('#berlaku_hinggaDisplay').val(formattedDate);
                $('#berlaku_hingga').val(start.format('YYYY-MM-DD'));
            });
        }else{
            var idx=key;
            let cek = $('#is_reminder_'+idx).text();
            console.log('cek '+cek);
            $('#jenis').val($('#jenis_'+idx).text());
            $('#nomor').val($('#nomor_'+idx).text());

            // $('#berlaku_hingga').datepicker('setDate',$('#berlaku_hingga_'+idx).text());
            $('input[id="berlaku_hinggaDisplay"]').daterangepicker({
                opens: 'center',
                drops: "up",
                singleDatePicker: true,
                showDropdowns: true,
                autoApply: false,
                startDate: $('#berlaku_hingga_'+idx).text(), // Set the initial date to today
                locale: {
                    format: 'DD-MMM-YYYY',
                }
            }, function(start, end, label) {
                const formattedDate = start.format('DD-MMM-YYYY');
                $('#berlaku_hinggaDisplay').val(formattedDate);
                $('#berlaku_hingga').val(start.format('YYYY-MM-DD'));
            });

            $('#is_reminder').val($('#is_reminder_'+idx).text());
            if($('#is_reminder_'+idx).text()=='N'){
                $('#reminder_hari_'+idx).val(0);
                $('#check_is_reminder_'+idx).prop("checked", false);

                $('#check_is_reminder').prop('checked',false);
                $('#reminder_hari').attr('readonly',true);
                $('#reminder_hari').val('');
            }else{
                $('#check_is_reminder').prop("checked", true);
                $('#is_reminder_'+idx).val('Y');
                $('#reminder_hari').attr('readonly',false);
                $('#reminder_hari').val($('#reminder_hari_'+idx).text());
            }
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

        const inputValue = $('#jenis').val(); // Convert element text to lowercase
        const jenisCekElements = document.querySelectorAll('.jenisCek');
        const textArray = []; // Initialize an array to store the text content

        jenisCekElements.forEach(element => {
            const elementText = element.textContent; // Get the element's text content
            textArray.push(elementText); // Push the text content to the array
        });
        if (textArray.includes(inputValue)) {
            toastr.error('Jenis dokumen sudah ada');return;
            console.log('Input value is already in the textArray.');
        } else {
            if(typeof exist === 'undefined') {
                var new_row='<tr id="'+key+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail('+key+')"><span class="fas fa-edit"></span> Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail('+key+')"><span class="fas fa-eraser"></span> Hapus</a></li></ul></div></td><td id="dokumen_id_'+key+'" hidden>'+$('#dokumen_id').val()+'</td><td id="jenis_'+key+'" class="jenisCek">'+$('#jenis').val()+'</td><td id="nomor_'+key+'">'+$('#nomor').val()+'</td><td id="berlaku_hingga_'+key+'">'+$('#berlaku_hinggaDisplay').val()+'</td><td id="reminder_deskripsi_'+key+'">'+reminder_desc+'</td><td id="is_reminder_'+key+'" hidden>'+$('#is_reminder').val()+'</td><td id="reminder_hari_'+key+'" hidden>'+$('#reminder_hari').val()+'</td></tr>';
                $('#table_dokumen > tbody:last-child').append(new_row);
            }else{
                $('#jenis_'+key).text($('#jenis').val());
                $('#nomor_'+key).text($('#nomor').val());
                // $('#berlaku_hingga_'+key).text($('#berlaku_hingga').val());
                $('#is_reminder_'+key).text($('#is_reminder').val());
                $('#reminder_hari_'+key).text($('#reminder_hari').val());
                $('#berlaku_hingga_'+key).text($('#berlaku_hinggaDisplay').val());
                $('#dokumen_id_'+key).text($('#dokumen_id').val());
                $('#reminder_deskripsi_'+key).text(reminder_desc);
            }
        }
        

        $('#dokumen_dialog').modal('hide');
    }

    function check_reminder(row){
        if($(row).is(":checked")){
            $('#is_reminder').val('Y');
            $('#reminder_hari').attr('readonly',false);

        }else if($(row).is(":not(:checked)")){
            $('#is_reminder').val('N');
            $('#reminder_hari').attr('readonly',true);

        }
    }

    function delete_detail(id_tombol){
        let cek = $('#dokumen_id_'+id_tombol);
        
        if(cek != undefined){
            if (confirm('Apakah anda yakin?')) {
                $('#'+id_tombol).remove();
            }
        }
    }
    
    </script>
</div>
@endsection
