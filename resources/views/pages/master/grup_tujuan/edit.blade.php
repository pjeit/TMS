@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
<meta http-equiv="X-UA-Compatible" content="IE=edge">

@section('content')
<style>
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
    
    <form action="{{ route('grup_tujuan.update',[$id]) }}" method="POST">
        @method('PUT')
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
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Nama Tujuan</th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Jenis</th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Tarif</th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center">Uang Jalan</th>
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center; width: 110px;">Komisi</th>
                                                <th style="">Catatan</th>
                                                <th style="width:30px;">Detail</th>
                                                <th style="width:30px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($data))
                                                @foreach ($data as $item)
                                                    <tr id="row${i}">
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="margin: auto; display: block;" type="text" name="data[${i}][nama_tujuan]" id="nama_tujuan_${i}" maxlength="10" class="form-control" readonly>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="margin: auto; display: block;" type="text" name="data[${i}][jenis_tujuan]" id="jenis_tujuan_${i}" class="form-control" readonly>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="" type="text" name="data[${i}][tarif]" id="tarif_${i}" class="form-control numaja uang" readonly/>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="" type="text" name="data[${i}][uang_jalan]" id="uang_jalan_${i}" class="form-control numaja uang" readonly/>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="" type="text" name="data[${i}][komisi]" id="komisi_${i}" class="form-control numaja uang" readonly/>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="" type="text" name="data[${i}][catatan]" id="catatan_${i}" class="form-control" readonly/>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <button type="button" name="detail" id="detail_${i}" class="btn btn-info detail"><i class="fa fa-list-ul"></i></button>
                                                        </td>  
                                                        <input type="hidden" name="data[${i}][id_tujuan]" id="id_tujuan_${i}">
                                                        <input type="hidden" name="data[${i}][alamat_hidden]" id="alamat_hidden_${i}">
                                                        <input type="hidden" name="data[${i}][uang_jalan_hidden]" id="uang_jalan_hidden_${i}">
                                                        <input type="hidden" name="data[${i}][komisi_hidden]" id="komisi_hidden_${i}">
                                                        <input type="hidden" name="data[${i}][harga_per_kg_hidden]" id="harga_per_kg_hidden_${i}">
                                                        <input type="hidden" name="data[${i}][min_muatan_hidden]" id="min_muatan_hidden_${i}">
                                                        <input type="hidden" name="data[${i}][grup_hidden]" id="grup_hidden_${i}" placeholder="">
                                                        <input type="hidden" name="data[${i}][marketing_hidden]" id="marketing_hidden_${i}" placeholder="">
                                                        <input type="hidden" name="data[${i}][obj_biaya]" id="obj_biaya${i}" placeholder="">
                                                        <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>);  
                                                    </tr>
                                                @endforeach
                                            @endif
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
                            <div class="form-group col-lg-6 col-md-6 col-6">
                                <label for="grup">Grup<span style="color:red;">*</span></label>
                                <input type="text" class="form-control" name="nama_grup" id="nama_grup" value="{{ $data->nama_grup }}" readonly>
                                <input type="hidden" name="grup" id="grup" value="{{ $data->id }}">
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-6">
                                <label for="marketing">Marketing <span style="color:red;">*</span></label>
                                <select name="marketing[]" class="select2" style="width: 100%" id="marketing" required>
                             
                                </select>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-6">
                                <label for="nama_tujuan">Nama Tujuan <span style="color:red;">*</span></label>
                                <input required ="text" class="form-control" maxlength="10" name="nama_tujuan" id="nama_tujuan" placeholder="Singkatan 10 Karakter"> 
                            </div>
                
                            <div class="form-group col-lg-6 col-md-6 col-6">
                                <label for="alamat">Alamat</label>
                                <input type="text" name="alamat" class="form-control" id="alamat" placeholder=""> 
                            </div>
                            
                            <div class="form-group class='col-lg-6 col-md-6 col-12'">
                                <label for="select_jenis_tujuan">Jenis Tujuan <span style="color:red;">*</span></label>
                                <select name="select_jenis_tujuan[]" class="select2" style="width: 100%" id="select_jenis_tujuan" required>
                                    <option value="FTL">Full Trucking Load (FTL)</option>
                                    <option value="LTL">Less Trucking Load (LTL)</option>
                                </select>
                            </div>

                            <div class="form-group col-lg-6 col-md-6 col-12">
                                <label for="Catatan">Catatan</label>
                                <input type="text" name="catatan" class="form-control" id="catatan" placeholder=""> 
                            </div>

                            <div class="form-group col-12 col-12-6 col-lg-4">
                                <label for="tarif">Tarif</label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp.</span>
                                </div>
                                <input type="text" name="tarif" class="form-control numaja uang" id="tarif" placeholder=""> 
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-5">
                                <label for="harga_per_kg">Harga per KG</label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" class="form-control uang" name="harga_per_kg" id="harga_per_kg" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">/Kg</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="min_muatan">Muatan Min.</label>
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control numaja uang" name="min_muatan" id="min_muatan" readonly>
                                        <div class="input-group-append">
                                            <div class="input-group-text">Kg</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-12">
                                <label for="uang_jalan">Uang Jalan Driver</label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="uang_jalan" class="form-control numaja uang" id="uang_jalan" placeholder="" readonly> 
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-12">
                                <label for="komisi">Komisi</label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="komisi" class="form-control numaja uang" id="komisi" placeholder=""> 
                                </div>
                            </div>
                    </div>
                    <div class='row'>
                        <div class="table-responsive p-0 mx-3">
                            <form name="add_biaya_detail" id="add_biaya_detail">
                                <button type="button" name="add_biaya" id="add_biaya" class="btn btn-primary my-1"><i class="fa fa-plus-circle"></i> <strong >Tambah Biaya</strong></button> 
                                <table class="table table-hover table-bordered table-striped text-nowrap" id="tabel_biaya">
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
            <button type="button" class="btn btn-sm btn-success save_detail" style='width:85px'>OK</button> 
            </div>
        </div>
        <!-- /.modal-content -->
        </div>
    </div>

</div>

<script>
$(document).ready(function(){

    var i = 0;
    var j = 0;
    var length;
 
    $("#add").click(function(){
        i++;

        var newRow = `
            <tr id="row${i}">
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="margin: auto; display: block;" type="text" name="data[${i}][nama_tujuan]" id="nama_tujuan_${i}" maxlength="10" class="form-control" readonly>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="margin: auto; display: block;" type="text" name="data[${i}][jenis_tujuan]" id="jenis_tujuan_${i}" class="form-control" readonly>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="data[${i}][tarif]" id="tarif_${i}" class="form-control numaja uang" readonly/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="data[${i}][uang_jalan]" id="uang_jalan_${i}" class="form-control numaja uang" readonly/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="data[${i}][komisi]" id="komisi_${i}" class="form-control numaja uang" readonly/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="data[${i}][catatan]" id="catatan_${i}" class="form-control" readonly/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <button type="button" name="detail" id="detail_${i}" class="btn btn-info detail"><i class="fa fa-list-ul"></i></button>
                </td>  
                <input type="hidden" name="data[${i}][id_tujuan]" id="id_tujuan_${i}">
                <input type="hidden" name="data[${i}][alamat_hidden]" id="alamat_hidden_${i}">
                <input type="hidden" name="data[${i}][uang_jalan_hidden]" id="uang_jalan_hidden_${i}">
                <input type="hidden" name="data[${i}][komisi_hidden]" id="komisi_hidden_${i}">
                <input type="hidden" name="data[${i}][harga_per_kg_hidden]" id="harga_per_kg_hidden_${i}">
                <input type="hidden" name="data[${i}][min_muatan_hidden]" id="min_muatan_hidden_${i}">
                <input type="hidden" name="data[${i}][grup_hidden]" id="grup_hidden_${i}" placeholder="">
                <input type="hidden" name="data[${i}][marketing_hidden]" id="marketing_hidden_${i}" placeholder="">
                <input type="hidden" name="data[${i}][obj_biaya]" id="obj_biaya${i}" placeholder="">
                <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>);  
            </tr>
        `;
        $('#dynamic_field > tbody:last-child').append(newRow);

        $('.select2').select2();
    });
   
    $("#add_biaya").click(function(){
        j++;      
        // var get_id_biaya = $(this).attr("id"); 
        var get_id_biaya = $(`#id_tujuan_${i}`).val();     
        var idBiaya = $('#key').val();
        // alert(idBiaya);
        $('#tabel_biaya > tbody:last-child').append(
        `
            <tr id="row_biaya${j}">
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="margin: auto; display: block;" type="text" name="deskripsi_biaya" class="form-control" id="deskripsi_biaya${j}">
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input type="hidden" name="biaya_id" id="biaya_id${j}" class="form-control"/>
                    <input style="" type="text" name="biaya" id="biaya${j}" class="form-control numaja uang biaya" />
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="catatan_biaya" id="catatan_biaya${j}" class="form-control"/>
                </td>
                <td>
                    <button type="button" name="del_biaya" id="${j}" class="btn btn-danger btn_remove_biaya"><i class="fa fa-trash" aria-hidden="true"></i></button>
                </td></tr>);  
            </tr>
        `
        );
        // hitung_uang_jalan();

        // $('#uang_jalan_'+key).val($('#uang_jalan').val());
        // $('#uang_jalan').val($('#biaya'${j}).val());


        $('.select2').select2();
    });

    $(document).on('click', '.detail', function(){  
        var button_id = $(this).attr("id");     
        var key = button_id.replace("detail_", "");
        $('#key').val(key);

        console.log('key: '+ key);
        console.log('sss: '+ $('#tarif_'+key).val());
        
        // dropdownJenis();
        if($('#jenis_tujuan_'+key).val() == 'FTL'){
            $('#ftl_selected').css('display','');
            $('#ltl_selected').css('display','none');
        }else if($('#jenis_tujuan_'+key).val() == 'LTL'){
            $('#ftl_selected').css('display','none');
            $('#ltl_selected').css('display','');
        }else{
            $('#ftl_selected').css('display','');
            $('#ltl_selected').css('display','none');
        }
        
        clearModal(); // clear dulu data sebelum open modal, baru get data ( biar clean )
        const marketingSelect = document.getElementById('marketing');
        const selectedValue = $('#grup').val();

        const selectedGroupId = selectedValue;

        var selected_marketing = null;
        if (selectedGroupId) {
            
            if($('#marketing_hidden_'+key).val() != ''){
                selected_marketing = $('#marketing_hidden_'+key).val();
            }
            fetch(`/grup_tujuan/getMarketing/${selectedGroupId}`)
                .then(response => response.json())
                .then(data => {
                    // marketingSelect.innerHTML = '<option value="">Pilih Marketing</option>';
                    data.forEach(marketing => {
                        const option = document.createElement('option');
                        option.value = marketing.id;
                        option.textContent = marketing.nama;
                        if (selected_marketing == marketing.id) {
                            option.selected = true;
                        }
                        marketingSelect.appendChild(option);
                    });
                });
        } else {
            marketingSelect.innerHTML = '<option value="">Pilih Marketing</option>';
        }

        if($('#tarif_'+key).val() != ''){
            $('#tarif').val($('#tarif_'+key).val());
        }
        if($('#komisi_'+key).val() != ''){
            $('#komisi').val($('#komisi_'+key).val());
        }
        if($('#alamat_hidden_'+key).val() != ''){
            $('#alamat').val($('#alamat_hidden_'+key).val());
        }
        if($('#alamat_hidden_'+key).val() != ''){
            $('#uang_jalan').val($('#uang_jalan_'+key).val());
        }
        if($('#nama_tujuan_'+key).val() != ''){
            $('#nama_tujuan').val($('#nama_tujuan_'+key).val());
        }
        if($('#catatan_'+key).val() != ''){
            $('#catatan').val($('#catatan_'+key).val());
        }
        
        // cek apakah ada isi detail biaya
        var cekBiaya = $('#obj_biaya'+key).val();
        // console.log('cekBiaya '+cekBiaya);
        if (cekBiaya) {
            // var jsonData = JSON.parse(jsonString);
            if(cekBiaya != null || cekBiaya != ''){
                JSON.parse(cekBiaya).forEach(function(item, index) {
                    $('#tabel_biaya > tbody:last-child').append(
                        `
                            <tr id="row_biaya${index+1}">
                                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                    <input style="margin: auto; display: block;" type="text" class="form-control" id="deskripsi_biaya${index+1}" value="${item.deskripsi_biaya}">
                                </td>
                                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                    <input style="" type="text" id="biaya${index+1}" value="${item.biaya}" class="form-control numaja uang" />
                                </td>
                                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                    <input style="" type="text" id="catatan_biaya${index+1}" value="${item.catatan_biaya}" class="form-control"/>
                                </td>
                                <td><button type="button" name="del_biaya" id="${index+1}" class="btn btn-danger btn_remove_biaya"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>);  
                            </tr>
                        `
                    );
                });
            }
        } else {
            // console.log('cekBiaya is null, undefined, or an empty string.');
        }
        // if(typeof exist === 'undefined') {

        $('.select2').select2();

        $('#modal_detail').modal('show');
    });
    
    function save_detailx(){
        var key=$('#key').val();
        if($('#nama_tujuan').val()==''){toastr.error('Nama tujuan harus diisi');return;}
		
		if($('#select_jenis_tujuan').val() == "LCL")
		{
			if($('#harga_per_kg').val() == ''){toastr.error('Harga Per Kg Harus Diisi');return;}
			if($('#min_muatan').val() == ''){toastr.error('Minimum Muatan Harus Diisi');return;}
		}
        
        var exist=$('#table_tujuan tbody').find('#'+key).attr('id');
        if(typeof exist === 'undefined') {
            var myjson;var array_detail_uang_jalan=[];
            $('#table_uang_jalan > tbody  > tr').each(function(idx) {
                var id=$(this).attr('id').split('_');
                if(typeof id !== 'undefined') {
                    myjson='{"biaya_id":'+JSON.stringify($('#biaya_id_'+id[1]).text())+', "biaya":'+JSON.stringify($('#biaya_'+id[1]).text())+', "deskripsi":'+JSON.stringify($('#deskripsi_'+id[1]).text())+', "catatan":'+JSON.stringify($('#catatan_biaya_'+id[1]).text())+'}';
                    var obj=JSON.parse(myjson);
                    array_detail_uang_jalan.push(obj);
                }
            });
			if($('#select_jenis_tujuan').val()=="LCL")
			{
				var new_row='<tr id="'+key+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail('+key+')"><span class="fas fa-edit"></span> Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail('+key+')"><span class="fas fa-eraser"></span> Hapus</a></li></ul></div></td><td id="tujuan_id_'+key+'" hidden>'+$('#tujuan_id').val()+'</td><td id="nama_'+key+'" hidden>'+$('#nama_tujuan').val()+'</td><td id="alamat_'+key+'" hidden>'+$('#alamat_tujuan').val()+'</td><td id="tujuan_'+key+'">'+$('#nama_tujuan').val()+' ('+$('#alamat_tujuan').val()+')'+'</td><td id="jenis_tujuan_'+key+'">'+$('#select_jenis_tujuan').val()+'</td><td id="tarif_'+key+'">-</td><td id="harga_per_kg_'+key+'">'+$('#harga_per_kg').val()+'</td><td id="min_muatan_'+key+'">'+$('#min_muatan').val()+'</td><td id="uang_jalan_'+key+'">'+$('#uang_jalan').val()+'</td><td id="komisi_'+key+'">'+$('#komisi').val()+'</td><td id="catatan_'+key+'">'+$('#catatan_tujuan').val()+'</td><td id="detail_uang_jalan_'+key+'" hidden>'+JSON.stringify(array_detail_uang_jalan)+'</td></tr>';
			}
			else{
				var new_row='<tr id="'+key+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail('+key+')"><span class="fas fa-edit"></span> Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail('+key+')"><span class="fas fa-eraser"></span> Hapus</a></li></ul></div></td><td id="tujuan_id_'+key+'" hidden>'+$('#tujuan_id').val()+'</td><td id="nama_'+key+'" hidden>'+$('#nama_tujuan').val()+'</td><td id="alamat_'+key+'" hidden>'+$('#alamat_tujuan').val()+'</td><td id="tujuan_'+key+'">'+$('#nama_tujuan').val()+' ('+$('#alamat_tujuan').val()+')'+'</td><td id="jenis_tujuan_'+key+'">'+$('#select_jenis_tujuan').val()+'</td><td id="tarif_'+key+'">'+$('#tarif').val()+'</td><td id="harga_per_kg_'+key+'">-</td><td id="min_muatan_'+key+'">-</td><td id="uang_jalan_'+key+'">'+$('#uang_jalan').val()+'</td><td id="komisi_'+key+'">'+$('#komisi').val()+'</td><td id="catatan_'+key+'">'+$('#catatan_tujuan').val()+'</td><td id="detail_uang_jalan_'+key+'" hidden>'+JSON.stringify(array_detail_uang_jalan)+'</td></tr>';
			}
            
            $('#table_tujuan > tbody:last-child').append(new_row);
        }else{
            $('#tujuan_id_'+key).text($('#tujuan_id').val());
            $('#tujuan_'+key).text($('#nama_tujuan').val()+' ('+$('#alamat_tujuan').val()+')');
            $('#nama_'+key).text($('#nama_tujuan').val());
			$('#jenis_tujuan_'+key).text($('#select_jenis_tujuan').val());
            $('#alamat_'+key).text($('#alamat_tujuan').val());
            $('#catatan_'+key).text($('#catatan_tujuan').val());
			if($('#select_jenis_tujuan').val() == "LCL")
			{
				$('#tarif_'+key).text("-");
				$('#harga_per_kg_'+key).text($('#harga_per_kg').val());
				$('#min_muatan_'+key).text($('#min_muatan').val());
			}
			else
			{
				$('#tarif_'+key).text($('#tarif').val());
				$('#harga_per_kg_'+key).text("-");
				$('#min_muatan_'+key).text("-");
			}
            $('#uang_jalan_'+key).text($('#uang_jalan').val());
            $('#komisi_'+key).text($('#komisi').val());
            var myjson;var array_detail_uang_jalan=[];
            $('#table_uang_jalan > tbody  > tr').each(function(idx) {
                var id=$(this).attr('id').split('_');
                if(typeof id !== 'undefined') {
                    myjson='{"biaya_id":'+JSON.stringify($('#biaya_id_'+id[1]).text())+', "biaya":'+JSON.stringify($('#biaya_'+id[1]).text())+', "deskripsi":'+JSON.stringify($('#deskripsi_'+id[1]).text())+', "catatan":'+JSON.stringify($('#catatan_biaya_'+id[1]).text())+'}';
                    var obj=JSON.parse(myjson);
                    array_detail_uang_jalan.push(obj);
                }
            });
            $('#detail_uang_jalan_'+key).text(JSON.stringify(array_detail_uang_jalan));
            
        }
        $('#tujuan_dialog').modal('hide');
    }

    $(document).on('click', '.save_detail', function(){
        var key=$('#key').val().trim();
        // simpan ke tampilan depan
        $('#tarif_'+key).val($('#tarif').val());
        $('#nama_tujuan_'+key).val($('#nama_tujuan').val());
        $('#uang_jalan_'+key).val($('#uang_jalan').val());
        $('#komisi_'+key).val($('#komisi').val());
        $('#catatan_'+key).val($('#catatan').val());
        $('#jenis_tujuan_'+key).val($('#select_jenis_tujuan').val());
        $('#alamat_hidden_'+key).val($('#alamat').val());
        $('#jenis_tujuan_'+key).val($('#select_jenis_tujuan').val());
        $('#harga_per_kg_hidden_'+key).val($('#harga_per_kg_hidden').val());
        $('#min_muatan_hidden_'+key).val($('#min_muatan').val());
        $('#grup_hidden_'+key).val($('#grup').val());
        $('#marketing_hidden_'+key).val($('#marketing').val());

        var myjson;
        var array_detail_biaya = [];

        // fungsi tambah uang jalan
        // cek apakah ada isinya apa tidak
        if($('#uang_jalan_'+key).val() == ''){
            // kalau ga, di deklarasikan 0
            var uang_jalan = 0;
        }else{
            // kalo ada ambil data sekarang
            var uang_jalan = parseFloat($('#uang_jalan_'+key).val());
        }

        // cek apakah ada detail biaya didalam modal
        let cekBiaya = $('#tabel_biaya > tbody > tr');
        var total_biaya = 0;
        if (cekBiaya.length > 0) {
            // kalau ada, datanya ditampilkan ke dalam tabel biaya
            $('#tabel_biaya > tbody > tr').each(function(idx) {
                var id=$(this).attr('id').replace("row_biaya", "");
                if(typeof id !== 'undefined') {
                    myjson='{"biaya_id":'+JSON.stringify($('#biaya_id'+id).val())+',"biaya":'+JSON.stringify($('#biaya'+id).val())+', "deskripsi_biaya":'+JSON.stringify($('#deskripsi_biaya'+id).val())+', "catatan_biaya":'+JSON.stringify($('#catatan_biaya'+id).val())+'}';
                    var obj=JSON.parse(myjson);

                    array_detail_biaya.push(obj);

                    //logic itung uang
                    total_biaya += parseFloat($('#biaya' + id).val().replace(/,/g, "")) || 0;
                }

                // ini buat di simpan (hidden), nanti dikirim waktu post
                $('#obj_biaya'+key).val(JSON.stringify(array_detail_biaya));
            });
            
            // ini ngitung semua uangnya
            $('#uang_jalan_'+key).val(total_biaya);
        } else {
            // kalau ga, delete semua
            $('#obj_biaya'+key).val('');
        }

        // clear biar ga nyantol data lama
        $('#tabel_biaya tbody').html('');


        $('#modal_detail').modal('hide');
    });

    function hitung_uang_jalan(){
        var total_uang_jalan=0;
        $('#tabel_biaya > tbody  > tr').each(function(idx) {
            var row = $(this);
            var biayaValue = row.find('.biaya').val();
            var id=$(this).attr('id').toString();
        });

    }

    function clearModal(){
        // set ke null semua
        $('#tabel_biaya tbody').html('');
        $('#marketing').empty();

        $('#alamat').val('');
        $('#jenis').val('');
        $('#nama_tujuan').val('');
        $('#alamat').val('');
        $('#tarif').val('');
        $('#uang_jalan').val('');
        $('#komisi').val('');
        $('#catatan').val('');
    }

    $(document).on('click', '.btn_remove_biaya', function(){  
        var button_id = $(this).attr("id");     
        $('#row_biaya'+button_id+'').remove();  
    });

    dropdownJenis();

    function dropdownJenis(){
        $('#select_jenis_tujuan').on('select2:select', function (e){

            const hargaInput = $('#harga_per_kg');
            const tarifInput = $('#tarif');
            const muatanInput = $('#min_muatan');
            if(e.params.data.id == "FTL"){
                hargaInput.prop('readonly', true);
                hargaInput.val('');
                muatanInput.prop('readonly', true);
                muatanInput.val('');
                tarifInput.prop('readonly', false);
            }else{
                hargaInput.prop('readonly', false);
                muatanInput.prop('readonly', false);
                tarifInput.prop('readonly', true);
                tarifInput.val('');
            }
        });
    }
     


    $(document).on('click', '.btn_remove', function(){  
        var button_id = $(this).attr("id");     
        $('#row'+button_id+'').remove();  
    });
 
    $(document).on('keypress', ".biaya", function (e) {
        // alert('xx');
        // let uj = 0;
        // if($('#uang_jalan').val() != ''){
        //     uj = $('#uang_jalan').val(); 
        // }
        // var ujnow = uj+$(this).val();
        // $('#uang_jalan').val(ujnow);
        
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
