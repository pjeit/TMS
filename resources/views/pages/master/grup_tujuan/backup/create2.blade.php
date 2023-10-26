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
                        <button type="button" class="btn btn-sm btn-primary mx-4 my-3" onclick="open_detail('') "><i class='fas fa-plus-circle'></i><b style="font-size:16px">&nbsp; DAFTAR TUJUAN & TARIF</b></button>

                        <div class="table-responsive p-0">
                                <form name="add_name" id="add_name">
                                    <table class="table table-hover table-bordered table-striped text-nowrap" id="dynamic_field">
                                        <thead>
                                            <tr class="">
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Nama Tujuan<span class="text-red">*</span></th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Jenis<span class="text-red">*</span></th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Tarif</th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Uang Jalan</th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Komisi</th>
                                                <th>Catatan</th>
                                                <th style="width:30px">Detail</th>
                                                <th style="width:30px"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr >
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
                                            </tr>
                                        </tbody>
                                    </table>
                                    <input type="submit" class="btn btn-success" name="submit" id="submit" value="Submit">
                                </form>
                        </div>

                        <div class="form-group">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </form>
    
    <div class="modal fade"  id="tujuan_dialog" tabindex='-1'>
        <div class="modal-dialog modal-lg" >
          <div class="modal-content" style="height: 90%; overflow: auto;">
            <div class="modal-header">
              <h5 class="modal-title">Detail Tujuan</h5>
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
                                <label for="nama_tujuan">Nama Tujuan<span style='color:red'>*</span></label>
                                <input type="text" name="nama_tujuan" maxlength="10" class="form-control" id="nama_tujuan" placeholder="Singkatan (Max. 10 karakter)"> 
                            </div>
                            <div class="form-group">
                                <label for="alamat_tujuan">Alamat</label>
                                <input type="text" name="alamat_tujuan" class="form-control" id="alamat_tujuan" placeholder=""> 
                            </div>
                            <div class="form-group">
                                <label for="select_jenis_tujuan">Jenis Tujuan <span style="color:red;">*</span></label>
                                <select name="select_jenis_tujuan" class="select2" id="select_jenis_tujuan" style="width:100%" data-placeholder="Pilih Customer">
                                    <option value="LTL">Less Trucking Load (LTL)</option>
                                    <option value="FTL">Full Trucking Load (FTL)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="catatan_tujuan">Catatan</label>
                                <input type="text" name="catatan_tujuan" class="form-control" id="catatan_tujuan" placeholder=""> 
                            </div>
                            
                        </div>
                        <div class='col-lg-6 col-md-6 col-12'>
                            <div id="ftl_selected" name="ftl_selected" class="row" style="display: none;">
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
                            <div name="ltl_selected" id="ltl_selected" class="row">
                                <div class="col-8 col-md-12 col-lg-8">
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
                                <div class="col-4 col-md-12 col-lg-4">
                                    <label for="min_muatan" style="font-size: 14px;">Muatan Min.<span style="color:red;">*</span></label>
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
                        <div class="table-responsive p-0">
                            <div class='col-12'>
                                <button type="button" class="btn btn-sm btn-primary" onclick="open_detail_uang_jalan('')"><i class='fas fa-plus-circle'></i><b style="font-size:16px">&nbsp; BIAYA UANG JALAN</b></button>
                                <table class="table table-hover table-bordered table-striped text-nowrap" id='table_uang_jalan' style='margin-top:5px;'>
                                    <thead>
                                        <tr>
                                          <th style="width:30px"></th>
                                          <th>Deskripsi</th>
                                          <th>Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
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

    <div class="modal fade" id="uang_jalan_dialog" tabindex='-1'>
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Biaya Uang Jalan</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id='form_add_detail'>
                    <input type="hidden" name="key_biaya" id="key_biaya">
                    <input type="hidden" name="biaya_id" id="biaya_id">  
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi<span style='color:red'>*</span></label>
                        <input type="text" name="deskripsi" class="form-control" id="deskripsi" placeholder=""> 
                    </div>
                    <div class="form-group">
                        <label for="biaya">Biaya<span style='color:red'>*</span></label>
                        <div class="input-group mb-3">
                          <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                          </div>
                          <input type="text" name="biaya" class="form-control numaja uang" id="biaya" placeholder=""> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="catatan_biaya">Catatan</label>
                        <input type="text" name="catatan_biaya" class="form-control" id="catatan_biaya" placeholder=""> 
                    </div>
                </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
              <button type="button" class="btn btn-sm btn-success" style='width:85px' onclick='save_detail_uang_jalan()'>OK</button>
              <?php //if($akses_id != 3){ ?>
                <!-- <button type="button" class="btn btn-sm btn-success" style='width:85px' onclick='save_detail_uang_jalan()'>OK</button> -->
              <?php //} ?>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</div>

<script>
    $(document).ready(function(e){

		// $(document).on('keypress', ".numajaNpwp", function (e) {
		// 	if ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode == 0) || (e.charCode == 45) || (e.charCode == 46))
		// 		return true;
		// 	else
		// 		return false;
    	// });

		// $('#select_jenis_tujuan').select2();

        // if($('#maks_kredit').val() != 0){
        //     let maks_kredit = $('#maks_kredit').val();
        //     maks_kredit = addPeriod(maks_kredit,',');
        //     $('#maks_kredit').val(maks_kredit);
        // }

        // if($('#kredit_sekarang').val() != 0){
        //     let kredit_sekarang = $('#kredit_sekarang').val();
        //     kredit_sekarang = addPeriod(kredit_sekarang,',');
        //     $('#kredit_sekarang').val(kredit_sekarang);
        // }

		// $('#select_jenis_tujuan').on('select2:select', function (e){
		// 	if(e.params.data.id == "LTL")
		// 	{
		// 		$('#ltl_selected').css('display','');
		// 		$('#ftl_selected').css('display','none');
		// 	}
		// 	else
		// 	{
		// 		$('#ltl_selected').css('display','none');
		// 		$('#ftl_selected').css('display','');
		// 	}
		// });

        // $("#grup_forms").on('submit', function(e){
        //     var array_tujuan=[];
        //     var formData = new FormData(this);
        //     var myjson;
            
        //     $('#table_tujuan > tbody  > tr').each(function(idx) {
        //         var id=$(this).attr('id');
        //         if(typeof id !== 'undefined') {
        //             myjson='{"tujuan_id":'+JSON.stringify($('#tujuan_id_'+id).text())+', "nama":'+JSON.stringify($('#nama_'+id).text())+', "alamat":'+JSON.stringify($('#alamat_'+id).text())+', "jenis_tujuan":'+JSON.stringify($('#jenis_tujuan_'+id).text())+', "catatan":'+JSON.stringify($('#catatan_'+id).text())+', "harga_per_kg":'+JSON.stringify($('#harga_per_kg_'+id).text())+', "min_muatan":'+JSON.stringify($('#min_muatan_'+id).text())+', "tarif":'+JSON.stringify($('#tarif_'+id).text())+', "uang_jalan":'+JSON.stringify($('#uang_jalan_'+id).text())+', "komisi":'+JSON.stringify($('#komisi_'+id).text())+', "detail_uang_jalan":'+JSON.stringify($('#detail_uang_jalan_'+id).text())+'}';
        //             var obj=JSON.parse(myjson);
        //             array_tujuan.push(obj);
        //         }
        //     });
        //     console.log(array_tujuan);
        //     formData.append('tujuan', JSON.stringify(array_tujuan));

        //     console.log('formData : ' +formData);
        //     e.preventDefault();
        //     var url = $(this).attr('data-action');
        //     $.ajax({
        //         type: 'POST',
        //         url: url,
        //         data: formData,
        //         dataType: 'json',
        //         contentType: false,
        //         cache: false,
        //         processData:false,
        //         success: function(response){
        //             if (response.hasOwnProperty('id')) {
        //                 toastr.success('Sukses!');
        //                 window.location.href = '{{ route("grup.index") }}';
        //             } else {
		// 				$("#loadMe").modal("hide");
        //                 for(var i in response.message){
        //                     toastr.error(response.message[i]);
        //                 }
        //             } 
        //         }
        //     });
        // });
    });
  
    // function delete_data(){
    //     if($('#customer_id').val()!=''){
    //         $('#form_delete').attr('action', "c_customer/delete_data");
    //         $('#form_delete').find('#id').attr('name','customer_id');
    //         $('#form_delete').find('#id').val($('#customer_id').val());
    //         $('#form_delete').find('#table').val('customer');
            
    //         $('#confirm_dialog').modal('show');
    //     }else{
    //         $('#confirm_dialog_reset').modal('show');
    //     }
    // }
    
    // function open_detail(key){
    //     if(key===''){
    //         var last_id=($('#table_tujuan tr:last').attr('id'));
    //         if(typeof last_id === 'undefined') {
    //             var last_id=0;
    //         }else{
    //             var last_id=parseInt(last_id)+1
    //         }
    //         var idx=last_id;
    //         $('#nama_tujuan').val('');
    //         $('#label-nama-tujuan').text('');
    //         $('#alamat_tujuan').val('');
    //         $('#catatan_tujuan').val('');
    //         $('#tarif').val('');
	// 		$('#harga_per_kg').val('');
	// 		$('#min_muatan').val('');
    //         $('#uang_jalan').val('');
    //         $('#komisi').val('');
    //         $('#table_uang_jalan tbody').html('');
    //         $('#tujuan_id').val('');
	// 		$('#select_jenis_tujuan').val('LTL').trigger('change');
	// 		$('#ltl_selected').css('display','');
	// 		$('#ftl_selected').css('display','none');
    //     }else{
    //         var idx=key;
    //         $('#nama_tujuan').val($('#nama_'+idx).text());
    //         $('#label-nama-tujuan').text($('#nama_'+idx).text());
    //         $('#alamat_tujuan').val($('#alamat_'+idx).text());
    //         $('#catatan_tujuan').val($('#catatan_'+idx).text());
    //         $('#uang_jalan').val($('#uang_jalan_'+idx).text());
    //         $('#komisi').val($('#komisi_'+idx).text());
    //         $('#tujuan_id').val($('#tujuan_id_'+idx).text());
			
	// 		if($('#jenis_tujuan_'+idx).text() == 'LTL')
	// 		{
	// 			$('#select_jenis_tujuan').val('LTL').trigger('change');

	// 			$('#ltl_selected').css('display','');
	// 			$('#ftl_selected').css('display','none');

	// 			$('#tarif').val('');
	// 			$('#min_muatan').val($('#min_muatan_'+idx).text());
	// 			$('#harga_per_kg').val($('#harga_per_kg_'+idx).text());
	// 		}
	// 		else{
	// 			$('#select_jenis_tujuan').val('FTL').trigger('change');

	// 			$('#ltl_selected').css('display','none');
	// 			$('#ftl_selected').css('display','');

	// 			$('#tarif').val($('#tarif_'+idx).text());
	// 			$('#min_muatan').val('');
	// 			$('#harga_per_kg').val('');
	// 		}
    //         var detail_uang_jalan=$('#detail_uang_jalan_'+idx).text();
    //         var array_detail_uang_jalan=JSON.parse(detail_uang_jalan);
    //         console.log(array_detail_uang_jalan);
    //         var row_uang_jalan='';

	// 		for(var keys in array_detail_uang_jalan){
	// 			row_uang_jalan+='<tr id="key_'+keys+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail_uang_jalan('+keys+')"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail_uang_jalan('+keys+')"><span class="fas fa-eraser" style="width:24px"></span>Hapus</a></li></ul></div></td><td id="biaya_id_'+keys+'" hidden>'+array_detail_uang_jalan[keys].biaya_id+'</td><td id="deskripsi_'+keys+'">'+array_detail_uang_jalan[keys].deskripsi+'</td><td id="biaya_'+keys+'">'+addPeriod(array_detail_uang_jalan[keys].biaya, ',')+'</td><td id="catatan_biaya_'+keys+'" hidden>'+(array_detail_uang_jalan[keys].catatan==null?'':array_detail_uang_jalan[keys].catatan)+'</td></tr>';
	// 		}
            
            
    //         $('#table_uang_jalan tbody').html(row_uang_jalan);
    //     }
        
    //     $('#key').val(idx);
    //     $('#tujuan_dialog').modal('show');
    // }
    
    // function save_detail(){
    //     var key=$('#key').val();
    //     if($('#nama_tujuan').val()==''){toastr.error('Nama tujuan harus diisi');return;}
		
	// 	if($('#select_jenis_tujuan').val() == "LTL")
	// 	{
	// 		if($('#harga_per_kg').val() == ''){toastr.error('Harga Per Kg Harus Diisi');return;}
	// 		if($('#min_muatan').val() == ''){toastr.error('Minimum Muatan Harus Diisi');return;}
	// 	}

    //     console.log('harga_per_kg '+$('#harga_per_kg').val());
    //     console.log('min_muatan '+$('#min_muatan').val());

    //     var exist=$('#table_tujuan tbody').find('#'+key).attr('id');
    //     if(typeof exist === 'undefined') {
    //         var myjson;var array_detail_uang_jalan=[];
    //         $('#table_uang_jalan > tbody  > tr').each(function(idx) {
    //             var id=$(this).attr('id').split('_');
    //             if(typeof id !== 'undefined') {
    //                 myjson='{"biaya_id":'+JSON.stringify($('#biaya_id_'+id[1]).text())+', "biaya":'+JSON.stringify($('#biaya_'+id[1]).text())+', "deskripsi":'+JSON.stringify($('#deskripsi_'+id[1]).text())+', "catatan":'+JSON.stringify($('#catatan_biaya_'+id[1]).text())+'}';
    //                 var obj=JSON.parse(myjson);
    //                 array_detail_uang_jalan.push(obj);
    //             }
    //         });
	// 		if($('#select_jenis_tujuan').val()=="LTL")
	// 		{
	// 			var new_row='<tr id="'+key+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail('+key+')"><span class="fas fa-edit"></span> Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail('+key+')"><span class="fas fa-eraser"></span> Hapus</a></li></ul></div></td><td id="tujuan_id_'+key+'" hidden>'+$('#tujuan_id').val()+'</td><td id="nama_'+key+'" hidden>'+$('#nama_tujuan').val()+'</td><td id="alamat_'+key+'" hidden>'+$('#alamat_tujuan').val()+'</td><td id="tujuan_'+key+'">'+$('#nama_tujuan').val()+' ('+$('#alamat_tujuan').val()+')'+'</td><td id="jenis_tujuan_'+key+'">'+$('#select_jenis_tujuan').val()+'</td><td id="tarif_'+key+'">-</td><td id="harga_per_kg_'+key+'">'+$('#harga_per_kg').val()+'</td><td id="min_muatan_'+key+'">'+$('#min_muatan').val()+'</td><td id="uang_jalan_'+key+'">'+$('#uang_jalan').val()+'</td><td id="komisi_'+key+'">'+$('#komisi').val()+'</td><td id="catatan_'+key+'">'+$('#catatan_tujuan').val()+'</td><td id="detail_uang_jalan_'+key+'" hidden>'+JSON.stringify(array_detail_uang_jalan)+'</td></tr>';
	// 		}
	// 		else{
	// 			var new_row='<tr id="'+key+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail('+key+')"><span class="fas fa-edit"></span> Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail('+key+')"><span class="fas fa-eraser"></span> Hapus</a></li></ul></div></td><td id="tujuan_id_'+key+'" hidden>'+$('#tujuan_id').val()+'</td><td id="nama_'+key+'" hidden>'+$('#nama_tujuan').val()+'</td><td id="alamat_'+key+'" hidden>'+$('#alamat_tujuan').val()+'</td><td id="tujuan_'+key+'">'+$('#nama_tujuan').val()+' ('+$('#alamat_tujuan').val()+')'+'</td><td id="jenis_tujuan_'+key+'">'+$('#select_jenis_tujuan').val()+'</td><td id="tarif_'+key+'">'+$('#tarif').val()+'</td><td id="harga_per_kg_'+key+'">-</td><td id="min_muatan_'+key+'">-</td><td id="uang_jalan_'+key+'">'+$('#uang_jalan').val()+'</td><td id="komisi_'+key+'">'+$('#komisi').val()+'</td><td id="catatan_'+key+'">'+$('#catatan_tujuan').val()+'</td><td id="detail_uang_jalan_'+key+'" hidden>'+JSON.stringify(array_detail_uang_jalan)+'</td></tr>';
	// 		}
            
    //         $('#table_tujuan > tbody:last-child').append(new_row);
    //     }else{
    //         $('#tujuan_id_'+key).text($('#tujuan_id').val());
    //         $('#tujuan_'+key).text($('#nama_tujuan').val()+' ('+$('#alamat_tujuan').val()+')');
    //         $('#nama_'+key).text($('#nama_tujuan').val());
	// 		$('#jenis_tujuan_'+key).text($('#select_jenis_tujuan').val());
    //         $('#alamat_'+key).text($('#alamat_tujuan').val());
    //         $('#catatan_'+key).text($('#catatan_tujuan').val());
	// 		if($('#select_jenis_tujuan').val() == "LTL")
	// 		{
	// 			$('#tarif_'+key).text("-");
	// 			$('#harga_per_kg_'+key).text($('#harga_per_kg').val());
	// 			$('#min_muatan_'+key).text($('#min_muatan').val());
	// 		}
	// 		else
	// 		{
	// 			$('#tarif_'+key).text($('#tarif').val());
	// 			$('#harga_per_kg_'+key).text("-");
	// 			$('#min_muatan_'+key).text("-");
	// 		}
    //         $('#uang_jalan_'+key).text($('#uang_jalan').val());
    //         $('#komisi_'+key).text($('#komisi').val());
    //         var myjson;var array_detail_uang_jalan=[];
    //         $('#table_uang_jalan > tbody  > tr').each(function(idx) {
    //             var id=$(this).attr('id').split('_');
    //             if(typeof id !== 'undefined') {
    //                 myjson='{"biaya_id":'+JSON.stringify($('#biaya_id_'+id[1]).text())+', "biaya":'+JSON.stringify($('#biaya_'+id[1]).text())+', "deskripsi":'+JSON.stringify($('#deskripsi_'+id[1]).text())+', "catatan":'+JSON.stringify($('#catatan_biaya_'+id[1]).text())+'}';
    //                 var obj=JSON.parse(myjson);
    //                 array_detail_uang_jalan.push(obj);
    //             }
    //         });
    //         $('#detail_uang_jalan_'+key).text(JSON.stringify(array_detail_uang_jalan));
            
    //     }
    //     $('#tujuan_dialog').modal('hide');
    // }
    
    // function delete_detail(id_tombol){
    //     if($('#tujuan_id_'+id_tombol).text()!=''){
    //         $('#form_delete_detail').find('#tujuan_id').val($('#tujuan_id_'+id_tombol).text());
    //         $('#form_delete_detail').find('#id_tombol').val(id_tombol);
            
    //         $('#confirm_dialog_detail').modal('show');
    //     }else{
    //         $('#'+id_tombol).remove();
    //     }
    // }

    // function delete_datadetail(){
    //     var id_delete=$("#form_delete_detail").find('#id_tombol').val();
    //     $.ajax({
    //         type:"POST",
    //         url:"c_customer/check_data_tujuan",
    //         data:$("#form_delete_detail").serialize(),
    //         dataType:"json",
    //         success:function (data) {
    //             if( !$.isArray(data) ||  !data.length ) {
    //                 // toastr.error('TERHAPUS');
    //                 $.ajax({
    //                     type:"POST",
    //                     url:"c_customer/delete_data_tujuan",
    //                     data:$("#form_delete_detail").serialize(),
    //                     dataType:"json",
    //                     success:function (data) {
    //                         if( !$.isArray(data) ||  !data.length ) {
    //                             // toastr.error('TERHAPUS');
    //                             $('#confirm_dialog_detail').modal('hide');
    //                             $('#'+id_delete).remove();
    //                         }else{
    //                             for(var i in data){
    //                                 toastr.error(data[i]);
    //                             }
    //                         }
    //                     }
    //                 });
    //             }else{
    //                 for(var i in data){
    //                     toastr.error(data[i]);
    //                 }
    //                 $('#'+id_delete).remove();
    //                 $('#confirm_dialog_detail').modal('hide');
    //             }
    //         }
    //     });
    // }
    
    // function open_detail_uang_jalan(key){
    //     if(key===''){
    //         var last_id=($('#table_uang_jalan tr:last').attr('id'));
    //         if(typeof last_id === 'undefined') {
    //             var last_id=0;
    //         }else{
    //             var last_idx=last_id.split('_');
    //             var last_id=parseInt(last_idx[1])+1
    //         }
    //         var idx=last_id;
    //         $('#deskripsi').val('');
    //         $('#catatan_biaya').val('');
    //         $('#biaya').val('');
    //         $('#biaya_id').val('');
    //     }else{
    //         var idx=key;
    //         $('#deskripsi').val($('#deskripsi_'+idx).text());
    //         $('#catatan_biaya').val($('#catatan_biaya_'+idx).text());
    //         $('#biaya').val($('#biaya_'+idx).text());
    //         $('#biaya_id').val($('#biaya_id_'+idx).text());
    //     }
        
    //     $('#key_biaya').val(idx);
    //     $('#uang_jalan_dialog').modal('show');
    // }
    
    // function save_detail_uang_jalan(){
    //     var key=$('#key_biaya').val();
    //     if($('#deskripsi').val()!='' && $('#biaya').val()!=''){
    //         var exist=$('#table_uang_jalan tbody').find('#key_'+key).attr('id');
    //         console.log(exist);
    //         if(typeof exist === 'undefined') {
                
    //             var new_row='<tr id="key_'+key+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail_uang_jalan('+key+')"><span class="fas fa-edit"></span> Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail_uang_jalan('+key+')"><span class="fas fa-eraser"></span> Hapus</a></li></ul></div></td><td id="biaya_id_'+key+'" hidden>'+$('#biaya_id').val()+'</td><td id="deskripsi_'+key+'">'+$('#deskripsi').val()+'</td><td id="biaya_'+key+'">'+$('#biaya').val()+'</td><td id="catatan_biaya_'+key+'" hidden>'+$('#catatan_biaya').val()+'</td></tr>';
                
    //             $('#table_uang_jalan > tbody:last-child').append(new_row);
    //         }else{
    //             $('#biaya_id_'+key).text($('#biaya_id').val());
    //             $('#deskripsi_'+key).text($('#deskripsi').val());
    //             $('#biaya_'+key).text($('#biaya').val());
    //             $('#catatan_biaya_'+key).text($('#catatan_biaya').val());
    //         }
    //         $('#uang_jalan_dialog').modal('hide');
    //         hitung_uang_jalan();
    //     }else{
    //         toastr.error('Deskripsi dan biaya wajib diisi');
    //     }
    // }
    
    // function delete_detail_uang_jalan(id_tombol){
    //     if($('#biaya_id_'+id_tombol).text()!=''){
    //         $('#form_delete_detail_uang_jalan').find('#biaya_id').val($('#biaya_id_'+id_tombol).text());
    //         $('#form_delete_detail_uang_jalan').find('#id_tombol_uang_jalan').val(id_tombol);
            
    //         $('#confirm_dialog_detail_uang_jalan').modal('show');
    //     }else{
    //         $('#key_'+id_tombol).remove();
    //         hitung_uang_jalan();
    //     }
    // }
    
    // function delete_datadetail_uang_jalan(){
    //     var id_delete=$("#form_delete_detail_uang_jalan").find('#id_tombol_uang_jalan').val();
    //     $.ajax({
    //         type:"POST",
    //         url:"c_customer/check_data_biaya",
    //         data:$("#form_delete_detail_uang_jalan").serialize(),
    //         dataType:"json",
    //         success:function (data) {
    //             if( !$.isArray(data) ||  !data.length ) {
    //                 // toastr.error('TERHAPUS');
    //                 $.ajax({
    //                     type:"POST",
    //                     url:"c_customer/delete_data_biaya",
    //                     data:$("#form_delete_detail_uang_jalan").serialize(),
    //                     dataType:"json",
    //                     success:function (data) {
    //                         if( !$.isArray(data) ||  !data.length ) {
    //                             // toastr.error('TERHAPUS');
    //                             $('#confirm_dialog_detail_uang_jalan').modal('hide');
    //                             $('#key_'+id_delete).remove();
    //                             hitung_uang_jalan();
    //                         }else{
    //                             for(var i in data){
    //                                 toastr.error(data[i]);
    //                             }
    //                         }
    //                     }
    //                 });
    //             }else{
    //                 for(var i in data){
    //                     toastr.error(data[i]);
    //                 }
    //                 $('#'+id_delete).remove();
    //                 $('#confirm_dialog_detail').modal('hide');
    //             }
    //         }
    //     });
    // }
    
    // function hitung_uang_jalan(){
    //     var total_uang_jalan=0;
    //     $('#table_uang_jalan > tbody  > tr').each(function(idx) {
    //         var id=$(this).attr('id').toString().split('_');
    //         console.log(id[1]);
    //         if(typeof id !== 'undefined') {
    //             total_uang_jalan+=parseFloat(removePeriod($('#biaya_'+id[1]).text(),','));
    //         }
    //     });
    //     $('#uang_jalan').val(addPeriod(total_uang_jalan,','));
    // }
</script>

@endsection
