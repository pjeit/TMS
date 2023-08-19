@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif

@section('content')
<style>
    /* .table > tbody > tr > td {
        padding: 0px;
    } */
    .col-7{
        padding-right: 1px;
    }
    .col-5{
        padding-left: 1px;
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
    
    <form data-action="{{ route('grup.store') }}" id="grup_forms" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                   
                </div>
            </div>
            
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        {{-- <button type="button" class="btn btn-sm btn-primary mx-4 my-3" onclick="open_detail('') "><i class='fas fa-plus-circle'></i><b style="font-size:16px">&nbsp; DAFTAR TUJUAN & TARIF</b></button> --}}
                        <button type="button" name="add" id="add" class="btn btn-primary mx-4 my-3"><i class="fa fa-plus-circle"></i> <strong >Tambah Tujuan</strong></button> 

                        <div class="table-responsive p-0">
                                <form name="add_name" id="add_name">
                                    <table class="table table-hover table-bordered table-striped text-nowrap" id="dynamic_field">
                                        <thead>
                                            <tr class="">
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Nama Tujuan<span class="text-red">*</span></th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Jenis<span class="text-red">*</span></th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Tarif</th>
                                                {{-- <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center; width: 20px;">Harga/KG & Min Muatan(KG)<span class="text-red">*</span></th> --}}
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Uang Jalan</th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center; width: 110px;">Komisi</th>
                                                <th style="">Catatan</th>
                                                <th style="width:30px;">Detail</th>
                                                <th style="width:30px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- <tr>
                                                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                    <input style="margin: auto; display: block;" type="text" name="nama_tujuan[]" maxlength="10" class="form-control" id="nama_tujuan" placeholder="Singkatan 10 Karakter">
                                                </td>
                                                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                    <select name="select_jenis_tujuan[]" class="select2" style="width: 100%" id="jenis_tujuan[]">
                                                        <option value="LTL">LTL</option>
                                                        <option value="FTL">FTL</option>
                                                    </select>
                                                </td>
                                                <td style="padding: 5px; text-align: center; vertical-align: middle;"><input style="" type="text" name="tarif[]" id="tarif[]" class="form-control numaja uang"/></td>
                                                <td style="padding: 5px; text-align: center; vertical-align: middle;"><input style="" type="text" name="uang_jalan[]" id="uang_jalan[]" class="form-control numaja uang" readonly/></td>
                                                <td style="padding: 5px; text-align: center; vertical-align: middle;"><input style="" type="text" name="komisi[]" id="komisi[]" class="form-control numaja uang"/></td>
                                                <td style="padding: 5px; text-align: center; vertical-align: middle;"><input style="" type="text" name="catatan" class="form-control"/></td>
                                                <td style="padding: 5px; text-align: center; vertical-align: middle;"><button type="button" name="detail" id="detail" class="btn btn-info"><i class="fa fa-list-ul" ></i></button></td>  
                                                <td style="padding: 5px; text-align: center; vertical-align: middle;"><button type="button" name="add" id="add" class="btn btn-primary"><i class="fa fa-plus-square"></i></button></td>  
                                            </tr> --}}
                                        </tbody>
                                    </table>
                                </form>
                        </div>

                        <div class="form-group">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </form>
    
    <div class="modal fade" id="modal_detail" tabindex='-1'>
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Detail</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id='form_add_detail'>
                    <input type="hidden" name="key" id="key">
                    <input type="hidden" name="tujuan_id" id="tujuan_id">
                    <div class='row'>
                        <div class='col-lg-6 col-md-6 col-12'>
                            <div class="form-group">
                                <label for="alamat_tujuan">Alamat</label>
                                <input type="text" name="alamat_tujuan" class="form-control" id="alamat_tujuan" placeholder=""> 
                            </div>
                            <div class="form-group">
                                <label for="select_jenis_tujuan">Jenis Tujuan <span style="color:red;">*</span></label>
                                <select name="select_jenis_tujuan[]" class="select2" style="width: 100%" id="select_jenis_tujuan" >
                                    <option value="FTL">FTL</option>
                                    <option value="LTL">LTL</option>
                                </select>
                            </div>
                            <div id="ftl_selected" name="ftl_selected" class="row" >
                                <div class="col-12 col-md-12 col-lg-12">
                                    <label for="tarif">Tarif</label>
                                    <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="tarif" class="form-control numaja uang" id="tarif" placeholder=""> 
                                    </div>
                                </div>
                            </div>
                            <div name="ltl_selected" id="ltl_selected" class="row" style="display: none;">
                                <div class="col-12 col-md-12 col-lg-7">
                                    <label for="harga_per_kg">Harga per KG <span style="color:red;">*</span></label>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <input type="text" class="form-control uang" name="harga_per_kg" id="harga_per_kg" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">/Kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-12 col-lg-5">
                                    <label for="min_muatan">Muatan Min.<span style="color:red;">*</span></label>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control numaja uang" name="min_muatan" id="min_muatan" required>
                                            <div class="input-group-append">
                                                <div class="input-group-text">Kg</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-lg-6 col-md-6 col-12'>
                     
                            <div class="form-group">
                                <label for="uang_jalan">Uang Jalan Driver</label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="uang_jalan" class="form-control numaja uang" id="uang_jalan" placeholder="" readonly> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="komisi">Komisi</label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="komisi" class="form-control numaja uang" id="komisi" placeholder=""> 
                                </div>
                            </div>
                
                        </div>
                    </div>
                    <div class='row'>
                        <div class="table-responsive p-0 mx-3">
                            <form name="add_biaya_detail" id="add_biaya_detail">
                                <button type="button" name="add_biaya" id="add_biaya" class="btn btn-primary my-1"><i class="fa fa-plus-circle"></i> <strong >Biaya</strong></button> 
                                <table class="table table-hover table-bordered table-striped text-nowrap" id="dynamic_biaya">
                                    <thead>
                                        <tr class="">
                                            <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Deskripsi</th>
                                            <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Biaya</th>
                                            <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Catatan</th>
                                            <th style="width:30px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                    </tbody>
                                </table>
                            </form>
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
    </div>

</div>

<script>
$(document).ready(function(){
   
    var i = 0;
    var j = 1;
    var length;
 
    $("#add").click(function(){
        
        <!-- var rowIndex = $('#dynamic_field').find('tr').length;	 -->
        <!-- console.log('rowIndex: ' + rowIndex); -->
        <!-- console.log('amount: ' + addamount); -->
        <!-- var currentAmont = rowIndex * 700; -->
        <!-- console.log('current amount: ' + currentAmont); -->
        <!-- addamount += currentAmont; -->
        
        i++;
        // $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="text" name="name[]" placeholder="Enter your Name" class="form-control name_list"/></td><td><input type="text" name="email[]" placeholder="Enter your Email" class="form-control name_email"/></td>	<td><input type="text" name="amount[]" value="700" placeholder="Enter your Money" class="form-control total_amount"/></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');  
        $('#dynamic_field').append(
        `
            <tr id="row${i}">
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="margin: auto; display: block;" type="text" name="nama_tujuan[]" maxlength="10" class="form-control" id="nama_tujuan${i}" placeholder="Singkatan 10 Karakter">
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="margin: auto; display: block;" type="text" name="jenis_tujuan[]" class="form-control" id="jenis_tujuan${i}">
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="tarif[]" id="tarif${i}" class="form-control numaja uang" readonly/>
                </td>
                `+
                // <td style="padding: 5px; vertical-align: middle;">
                //     <div class="row">
                //         <div class="col-7" style="padding-right: 1px">
                //             <input  type="text" name="harga_per_kg[]" id="harga_per_kg${i}" class="form-control numaja uang "/>
                //         </div>
                //         <div class="col-5 style="padding-left: 1px">
                //             <input  type="text" name="min_muatan[]" id="min_muatan${i}" class="form-control numaja uang "/>
                //         </div>
                //     </div>
                // </td>
                `<td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="uang_jalan[]" id="uang_jalan${i}" class="form-control numaja uang" readonly/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="komisi[]" id="komisi${i}" class="form-control numaja uang" readonly/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="catatan" class="form-control"/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <button type="button" name="detail" id="detail${i}" class="btn btn-info detail"><i class="fa fa-list-ul"></i></button>
                </td>  
                <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>);  
            </tr>
        `);

        $('.select2').select2();
    });
   
    $("#add_biaya").click(function(){

        j++;
        $('#dynamic_biaya').append(
        `
            <tr id="row_biaya${j}">
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="margin: auto; display: block;" type="text" name="deskripsi_biaya${j}" class="form-control" id="deskripsi_biaya${j}">
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="biaya[]" id="biaya${j}" class="form-control numaja uang" />
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="catatan_biaya[]" id="catatan_biaya${j}" class="form-control"/>
                </td>
                <td><button type="button" name="del_biaya" id="${j}" class="btn btn-danger btn_remove_biaya"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>);  
            </tr>
        `);

        $('.select2').select2();
    });

    $('#select_jenis_tujuan').on('select2:select', function (e){
			if(e.params.data.id == "FTL"){
                $('#ltl_selected').css('display','none');
                $('#ftl_selected').css('display','');
			}else{
                $('#ltl_selected').css('display','');
                $('#ftl_selected').css('display','none');
			}
		});

    $(document).on('click', '.detail', function(){  
        var button_id = $(this).attr("id");     

        // if(key===''){
        //     var last_id=($('#table_dokumen tr:last').attr('id'));
        //     if(typeof last_id === 'undefined') {
        //         var last_id=0;
        //     }else{
        //         var last_id=parseInt(last_id)+1
        //     }
        //     var idx=last_id;
        //     $('#jenis').val('');
        //     $('#nomor').val('');
        //     // $('#berlaku_hingga').val('');
        //     $('#is_reminder').val('N');
        //     $('#check_is_reminder').attr('checked',false);
        //     $('#reminder_hari').val('');
        //     $('#dokumen_id').val('');
        //     $('#berlaku_hingga').val('');

        //     $('input[id="berlaku_hinggaDisplay"]').daterangepicker({
        //         opens: 'center',
        //         drops: "up",
        //         singleDatePicker: true,
        //         showDropdowns: true,
        //         autoApply: false,
        //         locale: {
        //             format: 'DD-MMM-YYYY',
        //         }
        //     }, function(start, end, label) {
        //         const formattedDate = start.format('DD-MMM-YYYY');
        //         $('#berlaku_hinggaDisplay').val(formattedDate);
        //         $('#berlaku_hingga').val(start.format('YYYY-MM-DD'));
        //         // $('#berlaku_hingga').datepicker('setDate',$('#berlaku_hingga_'+idx).text());

        //     });
        // }else{
        //     var idx=key;
        //     let cek = $('#is_reminder_'+idx).text();
        //     console.log('cek '+cek);
        //     $('#jenis').val($('#jenis_'+idx).text());
        //     $('#nomor').val($('#nomor_'+idx).text());
        //     $('#berlaku_hingga').datepicker('setDate',$('#berlaku_hingga_'+idx).text());
        //     $('#berlaku_hingga').val($('#berlaku_hingga_'+idx).text());
        //     $('#is_reminder').val($('#is_reminder_'+idx).text());
        //     if($('#is_reminder_'+idx).text()=='N'){
        //         $('#reminder_hari_'+idx).val(0);
        //         $('#check_is_reminder_'+idx).prop("checked", false);

        //         $('#check_is_reminder').prop('checked',false);
        //         $('#reminder_hari').attr('readonly',true);
        //         $('#reminder_hari').val('');
        //     }else{
        //         $('#check_is_reminder').prop("checked", true);
        //         $('#is_reminder_'+idx).val('Y');
        //         $('#reminder_hari').attr('readonly',false);
        //         $('#reminder_hari').val($('#reminder_hari_'+idx).text());
        //     }
        //     $('#dokumen_id').val($('#dokumen_id_'+idx).text());
        // }
        
        // $('#key').val(idx);
        alert(button_id);
        $('.select2').select2();
        $('#modal_detail').modal('show');
    });

    $(document).on('click', '.btn_remove', function(){  
        var button_id = $(this).attr("id");     
        $('#row'+button_id+'').remove();  
    });
     
    $(document).on('click', '.btn_remove_biaya', function(){  
        var button_id = $(this).attr("id");     
        $('#row_biaya'+button_id+'').remove();  
    });
     
 
    $("#submit").on('click',function(event){
     var formdata = $("#add_name").serialize();
       console.log(formdata);
       
       event.preventDefault()
       
       $.ajax({
         url   :"action.php",
         type  :"POST",
         data  :formdata,
         cache :false,
         success:function(result){
           alert(result);
           $("#add_name")[0].reset();
         }
       });
       
    });
});

		
</script>

@endsection
