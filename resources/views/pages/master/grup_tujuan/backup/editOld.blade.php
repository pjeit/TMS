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
    
    <form action="{{ route('grup_tujuan.update',[$id]) }}" id='post_tujuan' method="POST" >
        @csrf
        @method('PUT')

        <div class="row">
        
            
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('grup_tujuan.index') }}"class="btn btn-secondary radiusSendiri float-left"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" class="btn btn-success radiusSendiri float-left ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    
                        <button type="button" name="add" id="add" class="btn btn-primary radiusSendiri float-right"><i class="fa fa-plus-circle"></i> <strong >Tambah Tujuan</strong></button> 
                    </div>
                    <div class="card-body">
                        {{-- <button type="button" class="btn btn-sm btn-primary mx-4 my-3" onclick="open_detail('') "><i class='fas fa-plus-circle'></i><b style="font-size:16px">&nbsp; DAFTAR TUJUAN & TARIF</b></button> --}}


                        <input type="hidden" id="deleted_tujuan" name="data[deleted_tujuan]" placeholder="deleted_tujuan">
                        <input type="hidden" id="deleted_biaya" name="data[deleted_biaya]" placeholder="deleted_biaya">
          
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
                                            @if (!empty($data['tujuan']))
                                                @foreach ($data['tujuan'] as $key => $item)
                                                    <tr id="row{{$key}}">
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="margin: auto; display: block;" type="text" name="data[tujuan][{{$key}}][nama_tujuan]" id="nama_tujuan_{{$key}}" value="{{$item->nama_tujuan}}" maxlength="20" class="form-control" readonly>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="margin: auto; display: block;" type="text" name="data[tujuan][{{$key}}][jenis_tujuan]" id="jenis_tujuan_{{$key}}" value="{{$item->jenis_tujuan}}" class="form-control" readonly>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="" type="text" name="data[tujuan][{{$key}}][tarif]" id="tarif_{{$key}}" value="{{ number_format($item->tarif) }}" class="form-control numaja uang tarif" readonly/>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="" type="text" name="data[tujuan][{{$key}}][uang_jalan]" id="uang_jalan_{{$key}}" value="{{ number_format($item->uang_jalan) }}" class="form-control numaja uang uangJalan" readonly/>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="" type="text" name="data[tujuan][{{$key}}][komisi]" id="komisi_{{$key}}" value="{{ number_format($item->komisi) }}" class="form-control numaja uang" readonly/>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <input style="" type="text" name="data[tujuan][{{$key}}][catatan]" id="catatan_{{$key}}" value="{{$item->catatan}}" class="form-control" readonly/>
                                                        </td>
                                                        <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                                            <button type="button" name="detail" id="detail_{{$key}}" class="btn btn-info detail"><i class="fa fa-list-ul"></i></button>
                                                        </td>  
                                                        <input type="hidden" name="data[tujuan][{{$key}}][id_tujuan]" id="id_tujuan_{{$key}}" value="{{$item->id}}" >
                                                        <input type="hidden" name="data[tujuan][{{$key}}][alamat_hidden]" id="alamat_hidden_{{$key}}" value="{{$item->alamat}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][uang_jalan_hidden]" id="uang_jalan_hidden_{{$key}}" value="{{$item->uang_jalan}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][komisi_hidden]" id="komisi_hidden_{{$key}}" value="{{$item->komisi}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][harga_per_kg_hidden]" id="harga_per_kg_hidden_{{$key}}" value="{{number_format($item->harga_per_kg)}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][min_muatan_hidden]" id="min_muatan_hidden_{{$key}}" value="{{$item->min_muatan}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][grup_hidden]" id="grup_hidden_{{$key}}" value="{{$item->grup_id}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][marketing_hidden]" id="marketing_hidden_{{$key}}" value="{{$item->marketing_id}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][obj_biaya]" id="obj_biaya{{$key}}" value="{{$item->detail_uang_jalan}}">
                                                        <td><button type="button" name="remove" id="{{$key}}" class="btn btn-danger btn_remove"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr> 
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
                                <input type="text" class="form-control" name="nama_grup" id="nama_grup" value="{{ $data['grup']['nama_grup'] }}" readonly>
                                <input type="hidden" name="grup" id="grup" value="{{ $data['grup']['id'] }}">
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-6">
                                <label for="marketing">Marketing <span style="color:red;">*</span></label>
                                <select name="marketing[]" class="select2" style="width: 100%" id="marketing" required>
                             
                                </select>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-6">
                                <label for="nama_tujuan">Nama Tujuan <span style="color:red;">*</span></label>
                                <input required type="text" class="form-control" maxlength="20" name="nama_tujuan" id="nama_tujuan" placeholder="Singkatan 20 Karakter"> 
                            </div>
                
                            <div class="form-group col-lg-6 col-md-6 col-6">
                                <label for="alamat">Alamat</label>
                                <input type="text" name="alamat" class="form-control" id="alamat" placeholder=""> 
                            </div>
                            
                            <div class="form-group class='col-lg-6 col-md-6 col-12'">
                                {{-- <label for="select_jenis_tujuan">Jenis Tujuan <span style="color:red;">*</span></label>
                                <select name="select_jenis_tujuan[]" class="select2" style="width: 100%" id="select_jenis_tujuan" required>
                                    <option value=""></option>
                                    <option value=""></option>
                                </select> --}}
                                <label for="tipe">Tipe</label>
                                <br>
                                <div class="icheck-primary d-inline">
                                    <input id="FTL" type="radio" name="select_jenis_tujuan" value="FTL" {{'1' == old('select_jenis_tujuan','')? 'checked' :'' }}>
                                    <label class="form-check-label" for="FTL">Full Trucking Load</label>
                                </div>
                                <div class="icheck-primary d-inline ml-3">
                                    <input id="LTL" type="radio" name="select_jenis_tujuan" value="LTL" {{'2'== old('select_jenis_tujuan','')? 'checked' :'' }}>
                                    <label class="form-check-label" for="LTL">Less Trucking Load</label><br>
                                </div>
                              
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
                                <input type="hidden" id="deleted_biaya_temp" name="deleted_biaya_temp" placeholder="deleted_biaya_temp">
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
    $(document).ready(function() {
        $('#post_tujuan').submit(function(event) {
            // Calculate totals
            var tarifTotal = 0;
            var uangJalanTotal = 0;

            // // Loop through each input field with class 'tarif'
            $('.tarif').each(function() {
                var inputValue = parseFloat($(this).val().replace(/[^0-9.-]+/g, "")); // Remove commas and convert to number
                tarifTotal += isNaN(inputValue) ? 0 : inputValue;
            });
            $('.uangJalan').each(function() {
                var inputValue = parseFloat($(this).val().replace(/[^0-9.-]+/g, "")); // Remove commas and convert to number
                uangJalanTotal += isNaN(inputValue) ? 0 : inputValue;
            });
            
            if (tarifTotal < uangJalanTotal) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: 'Tarif harus lebih besar daripada uang jalan!',
                    // footer: '<a href="">Why do I have this issue?</a>'
                })
            }
        });
    });
</script>

<script>
$(document).ready(function(){
    // deklarasi golbal id yg didelete
    var deleted_tujuan = [];
    var deleted_biaya = [];

    // Ambil semua elemen <tr> dengan ID yang dimulai dengan "row"
    var rows = document.querySelectorAll('tr[id^="row"]');

    // Cari ID terbesar dengan format "rowX" dan ambil nilai X-nya
    var maxID = -1;
    for (var i = 0; i < rows.length; i++) {
        var idStr = rows[i].id.replace('row', ''); // Ambil nilai X dari "rowX"
        var idNum = parseInt(idStr); // Konversi menjadi angka
        if (idNum > maxID) {
            maxID = idNum;
        }
    }

    // Hasilkan ID terakhir dengan format "rowX+1"
    var lastID = (maxID + 1);

    if(lastID != 0){
        var i = lastID-1;
    }else{
        var i = 0;
    }
    var length;
 
    $("#addOld").click(function(){
        i++;
        var newRow = `
            <tr id="row${i}">
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="margin: auto; display: block;" type="text" name="data[tujuan][${i}][nama_tujuan]" id="nama_tujuan_${i}" maxlength="10" class="form-control" readonly>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="margin: auto; display: block;" type="text" name="data[tujuan][${i}][jenis_tujuan]" id="jenis_tujuan_${i}" class="form-control" readonly>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="data[tujuan][${i}][tarif]" id="tarif_${i}" class="form-control numaja uang tarif" readonly/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="data[tujuan][${i}][uang_jalan]" id="uang_jalan_${i}" class="form-control numaja uang uangJalan" readonly/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="data[tujuan][${i}][komisi]" id="komisi_${i}" class="form-control numaja uang" readonly/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="" type="text" name="data[tujuan][${i}][catatan]" id="catatan_${i}" class="form-control" readonly/>
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <button type="button" name="detail" id="detail_${i}" class="btn btn-info detail"><i class="fa fa-list-ul"></i></button>
                </td>  
                <input type="hidden" name="data[tujuan][${i}][id_tujuan]" id="id_tujuan_${i}">
                <input type="hidden" name="data[tujuan][${i}][alamat_hidden]" id="alamat_hidden_${i}">
                <input type="hidden" name="data[tujuan][${i}][uang_jalan_hidden]" id="uang_jalan_hidden_${i}">
                <input type="hidden" name="data[tujuan][${i}][komisi_hidden]" id="komisi_hidden_${i}">
                <input type="hidden" name="data[tujuan][${i}][harga_per_kg_hidden]" id="harga_per_kg_hidden_${i}">
                <input type="hidden" name="data[tujuan][${i}][min_muatan_hidden]" id="min_muatan_hidden_${i}">
                <input type="hidden" name="data[tujuan][${i}][grup_hidden]" id="grup_hidden_${i}" placeholder="">
                <input type="hidden" name="data[tujuan][${i}][marketing_hidden]" id="marketing_hidden_${i}" placeholder="">
                <input type="hidden" name="data[tujuan][${i}][obj_biaya]" id="obj_biaya${i}" placeholder="">
                <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>);  
            </tr>
        `;
        $('#dynamic_field > tbody:last-child').append(newRow);

        $('.select2').select2();
    });

          
    $("#add_biaya").click(function(){
        var get_id_biaya = $(`#id_tujuan_${i}`).val();     
        var idBiaya = $('#key').val();

        // Get all elements with IDs starting with "row_biaya"
        var rows = document.querySelectorAll('tr[id^="row_biaya"]');

        // Find the maximum ID number
        var maxIDRB = -1;
        for (var i = 0; i < rows.length; i++) {
            var idStrRB = rows[i].id.replace('row_biaya', ''); // Extract the number part
            var idNumRB = parseInt(idStrRB); // Convert to number
            if (idNumRB > maxIDRB) {
                maxIDRB = idNumRB;
            }
        }

        // Generate the last ID with the next number
        var lastIDRB = (maxIDRB + 1);

        // var j = 0;
        if(lastIDRB != 0){
            var j = lastIDRB;
        }else{
            var j = 0;
        }
        $('#tabel_biaya > tbody:last-child').append(
        `
            <tr id="row_biaya${j}">
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input style="margin: auto; display: block;" type="text" name="deskripsi" class="form-control" id="deskripsi${j}">
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input type="hidden" name="biaya_id" id="biaya_id${j}" class="form-control"/>
                    <input type="text" name="biaya" id="biaya${j}" class="form-control numaja uang biaya" />
                </td>
                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                    <input type="text" name="catatan_biaya" id="catatan_biaya${j}" class="form-control"/>
                </td>
                <td>
                    <button type="button" name="del_biaya" id="${j}" class="btn btn-danger btn_remove_biaya"><i class="fa fa-trash" aria-hidden="true"></i></button>
                </td></tr>);  
            </tr>
        `
        );

        $('input[type="text"]').on('input', function() {
            var inputValue = $(this).val();
            var uppercaseValue = inputValue.toUpperCase();
            $(this).val(uppercaseValue);
        });

        $('.select2').select2();
    });

    $("#add").click(function(){
        clearModal(); // clear dulu data sebelum open modal, baru get data ( biar clean )
        $('#key').val('');
        // jenis tujuan handler
        const hargaInput = $('#harga_per_kg');
        const tarifInput = $('#tarif');
        const muatanInput = $('#min_muatan');

        const ftlRadioButton = document.getElementById('FTL');
        ftlRadioButton.checked = true;
        tarifInput.val('');
        hargaInput.val('');
        muatanInput.val('');
        hargaInput.prop('readonly', true);
        muatanInput.prop('readonly', true);
        tarifInput.prop('readonly', false);
        const radioButtons = document.querySelectorAll('input[name="select_jenis_tujuan"]');
    
        // Menambahkan event listener untuk setiap radio button
        radioButtons.forEach(radioButton => {
            radioButton.addEventListener('change', function() {
                if(this.value == 'LTL'){
                    hargaInput.prop('readonly', false);
                    muatanInput.prop('readonly', false);
                    tarifInput.prop('readonly', true);
                    tarifInput.val('');
                }else{
                    hargaInput.prop('readonly', true);
                    hargaInput.val('');
                    muatanInput.prop('readonly', true);
                    muatanInput.val('');
                    tarifInput.prop('readonly', false);
                }
            });
        });

        
       

        const marketingSelect = document.getElementById('marketing');
        const selectedValue = $('#grup').val();
        const selectedGroupId = selectedValue;
        var selected_marketing = null;
        if (selectedGroupId) {
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

        $('#modal_detail').modal('show');

        

    });

  

    // open detail
    $(document).on('click', '.detail', function(){  
        $('#key').val('');
        var button_id = $(this).attr("id");     
        var key = button_id.replace("detail_", "");
        $('#key').val(key);

        // dropdownJenis();
        // alert($('#jenis_tujuan_'+key).val());
        const hargaInput = $('#harga_per_kg');
        const tarifInput = $('#tarif');
        const muatanInput = $('#min_muatan');

        const ltlRadioButton = document.getElementById('LTL');
        const ftlRadioButton = document.getElementById('FTL');
        if(key != ''){
            let jenTuj = $("#jenis_tujuan_"+key).val();
            if(jenTuj == 'LTL'){
                ltlRadioButton.checked = true;
                hargaInput.prop('readonly', false);
                muatanInput.prop('readonly', false);
                tarifInput.prop('readonly', true);
                tarifInput.val('');
            }else{
                ftlRadioButton.checked = true;
                hargaInput.prop('readonly', true);
                hargaInput.val('');
                muatanInput.prop('readonly', true);
                muatanInput.val('');
                tarifInput.prop('readonly', false);
            }
        }
        

        const radioButtons = document.querySelectorAll('input[name="select_jenis_tujuan"]');
        // Menambahkan event listener untuk setiap radio button
        radioButtons.forEach(radioButton => {
            radioButton.addEventListener('change', function() {
                if(this.value == 'LTL'){
                    hargaInput.prop('readonly', false);
                    muatanInput.prop('readonly', false);
                    tarifInput.prop('readonly', true);
                    tarifInput.val('');
                }else{
                    hargaInput.prop('readonly', true);
                    hargaInput.val('');
                    muatanInput.prop('readonly', true);
                    muatanInput.val('');
                    tarifInput.prop('readonly', false);
                }
            });
        });
        
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
        if($('#uang_jalan_'+key).val() != ''){
            $('#uang_jalan').val($('#uang_jalan_'+key).val());
        }
        if($('#harga_per_kg_hidden_'+key).val() != ''){
            $('#harga_per_kg').val($('#harga_per_kg_hidden_'+key).val());
        }
        if($('#min_muatan_hidden_'+key).val() != ''){
            $('#min_muatan').val($('#min_muatan_hidden_'+key).val());
        }
        
        // cek apakah ada isi detail biaya
        var cekBiaya = $('#obj_biaya'+key).val();
        if (cekBiaya) {
            // var jsonData = JSON.parse(jsonString);
            if(cekBiaya != null || cekBiaya != ''){
                JSON.parse(cekBiaya).forEach(function(item, index) {
                    $('#tabel_biaya > tbody:last-child').append(
                        `
                            <tr id="row_biaya${index}">
                                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                    <input style="margin: auto; display: block;" type="text" class="form-control" id="deskripsi${index}" value="${item.deskripsi}">
                                    <input type="hidden" name="biaya_id${index}" id="biaya_id${index}" value="${item.id}">
                                </td>
                                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                    <input style="" type="text" id="biaya${index}" value="${item.biaya.toLocaleString()}" class="form-control numaja uang" />
                                </td>
                                <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                    <input style="" type="text" id="catatan_biaya${index}" value="${item.catatan}" class="form-control"/>
                                </td>
                                <td><button type="button" name="del_biaya" id="${index}" class="btn btn-danger btn_remove_biaya"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>);  
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
    
    $(document).on('click', '.save_detail', function(){
        var key=$('#key').val().trim();

        // simpan ke bawah
        if(key != ''){
            var key=$('#key').val().trim();
            // simpan ke tampilan depan
            $('#tarif_'+key).val($('#tarif').val());
            $('#nama_tujuan_'+key).val($('#nama_tujuan').val());
            $('#uang_jalan_'+key).val(parseFloat($('#uang_jalan').val()));
            $('#komisi_'+key).val($('#komisi').val());
            $('#catatan_'+key).val($('#catatan').val());
            $('#alamat_hidden_'+key).val($('#alamat').val());

            var selJns = $("input[name='select_jenis_tujuan']:checked").val();

            $('#jenis_tujuan_'+key).val(selJns);
            $('#harga_per_kg_hidden_'+key).val($('#harga_per_kg').val());
            $('#min_muatan_hidden_'+key).val($('#min_muatan').val());
            $('#grup_hidden_'+key).val($('#grup').val());
            $('#marketing_hidden_'+key).val($('#marketing').val());

            $('#deleted_biaya').val($('#deleted_biaya_temp').val());

            // cek apakah ada detail biaya didalam modal
            var myjson;
            var array_detail_biaya = [];
            let cekBiaya = $('#tabel_biaya > tbody > tr');
            var total_biaya = 0;
            if (cekBiaya.length > 0) {
                // kalau ada, datanya ditampilkan ke dalam tabel biaya
                $('#tabel_biaya > tbody > tr').each(function(idx) {
                    var id=$(this).attr('id').replace("row_biaya", "");
                    if(typeof id !== 'undefined') {
                        let biayaId = $('#biaya_id' + id).val() ?? '';
                        myjson='{"id":'+JSON.stringify(biayaId)+',"biaya":'+JSON.stringify($('#biaya'+id).val())+', "deskripsi":'+JSON.stringify($('#deskripsi'+id).val())+', "catatan":'+JSON.stringify($('#catatan_biaya'+id).val())+'}';
                        var obj=JSON.parse(myjson);

                        array_detail_biaya.push(obj);

                        //logic itung uang
                        total_biaya += parseFloat($('#biaya' + id).val().replace(/,/g, "")) || 0;
                    }

                    // ini buat di simpan (hidden), nanti dikirim waktu post
                    // $('#obj_biaya'+key).val('');
                    $('#obj_biaya'+key).val(JSON.stringify(array_detail_biaya));
                });
                alert(total_biaya);
                // ini ngitung semua uangnya
                $('#uang_jalan_'+key).val(total_biaya);
            } else {
                // kalau ga, delete semua
                $('#uang_jalan_'+key).val(0);
                $('#obj_biaya'+key).val('');
            }
        }else{
            i++;
            var selectedValue = $("input[name='select_jenis_tujuan']:checked").val();
            // cek apakah ada isinya apa tidak
            if($('#uang_jalan').val() == ''){
                // kalau ga, di deklarasikan 0
                var uang_jalan = 0;
            }else{
                // kalo ada ambil data sekarang
                var uang_jalan = parseFloat($('#uang_jalan').val());
            }

            var myjson;
            var array_detail_biaya = [];
            
            // cek apakah ada detail biaya didalam modal
            let cekBiaya = $('#tabel_biaya > tbody > tr');
            var total_biaya = 0;
            if (cekBiaya.length > 0) {
                // kalau ada, datanya ditampilkan ke dalam tabel biaya
                $('#tabel_biaya > tbody > tr').each(function(idx) {
                    var id=$(this).attr('id').replace("row_biaya", "");
                    if(typeof id !== 'undefined') {
                        let biayaId = $('#biaya_id' + id).val() ?? '';
                        myjson='{"id":'+JSON.stringify(biayaId)+',"biaya":'+JSON.stringify($('#biaya'+id).val())+', "deskripsi":'+JSON.stringify($('#deskripsi'+id).val())+', "catatan":'+JSON.stringify($('#catatan_biaya'+id).val())+'}';
                        var obj=JSON.parse(myjson);
                        array_detail_biaya.push(obj);
                        //logic itung uang
                        total_biaya += parseFloat($('#biaya' + id).val().replace(/,/g, "")) || 0;
                    }
                });
            } else {
                // kalau ga, delete semua
                $('#obj_biaya').val('');
            }

            var newRow = `
                <tr id="row${i}">
                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                        <input value="${$('#nama_tujuan').val()}" name="data[tujuan][${i}][nama_tujuan]" id="nama_tujuan_${i}" maxlength="10" class="form-control" type="text" style="margin: auto; display: block;" readonly>
                    </td>
                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                        <input value="${selectedValue}" name="data[tujuan][${i}][jenis_tujuan]" id="jenis_tujuan_${i}" class="form-control" type="text" style="margin: auto; display: block;" readonly>
                    </td>
                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                        <input value="${$('#tarif').val()}" name="data[tujuan][${i}][tarif]" id="tarif_${i}" class="form-control numaja uang tarif" type="text" readonly/>
                    </td>
                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                        <input value="${total_biaya}" name="data[tujuan][${i}][uang_jalan]" id="uang_jalan_${i}" class="form-control numaja uang uangJalan" type="text" readonly/>
                    </td>
                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                        <input value="${$('#komisi').val()}" name="data[tujuan][${i}][komisi]" id="komisi_${i}" class="form-control numaja uang" type="text" readonly/>
                    </td>
                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                        <input value="${$('#catatan').val()}" name="data[tujuan][${i}][catatan]" id="catatan_${i}" class="form-control" type="text" readonly/>
                    </td>
                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                        <button name="detail" id="detail_${i}" class="btn btn-info detail" type="button"><i class="fa fa-list-ul"></i></button>
                    </td>  
                    <input value="${$('#id_tujuan').val()}" name="data[tujuan][${i}][id_tujuan]" id="id_tujuan_${i}" type="hidden" >
                    <input value="${$('#alamat').val()}" name="data[tujuan][${i}][alamat_hidden]" id="alamat_hidden_${i}" type="hidden" >
                    <input value="${total_biaya}" name="data[tujuan][${i}][uang_jalan_hidden]" id="uang_jalan_hidden_${i}" type="hidden" >
                    <input value="${$('#komisi').val()}" name="data[tujuan][${i}][komisi_hidden]" id="komisi_hidden_${i}" type="hidden" >
                    <input value="${$('#harga_per_kg').val()}" name="data[tujuan][${i}][harga_per_kg_hidden]" id="harga_per_kg_hidden_${i}" type="hidden" >
                    <input value="${$('#min_muatan').val()}" name="data[tujuan][${i}][min_muatan_hidden]" id="min_muatan_hidden_${i}" type="hidden" >
                    <input value="${$('#grup').val()}" name="data[tujuan][${i}][grup_hidden]" id="grup_hidden_${i}" type="hidden"  placeholder="">
                    <input value="${$('#marketing').val()}" name="data[tujuan][${i}][marketing_hidden]" id="marketing_hidden_${i}" type="hidden" placeholder="">
                    <input value='${JSON.stringify(array_detail_biaya)}' name="data[tujuan][${i}][obj_biaya]" id="obj_biaya${i}" type="hidden" placeholder="">
                    <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>);  
                </tr>
            `;
        }
       
        $('#dynamic_field > tbody:last-child').append(newRow);

        // // clear biar ga nyantol data lama
        $('#tabel_biaya tbody').html('');

 
        $('#deleted_biaya').val($('#deleted_biaya_temp').val());
       
        $('#modal_detail').modal('hide');
    });

    $(document).on('click', '.edit_detail', function(){
        var key=$('#key').val().trim();
        // simpan ke tampilan depan
        alert(key);

        // var myjson;
        // var array_detail_biaya = [];

        // // fungsi tambah uang jalan
        // // cek apakah ada isinya apa tidak
        // if($('#uang_jalan_'+key).val() == ''){
        //     // kalau ga, di deklarasikan 0
        //     var uang_jalan = 0;
        // }else{
        //     // kalo ada ambil data sekarang
        //     var uang_jalan = parseFloat($('#uang_jalan_'+key).val());
        // }

        // // cek apakah ada detail biaya didalam modal
        // let cekBiaya = $('#tabel_biaya > tbody > tr');
        // var total_biaya = 0;
        // if (cekBiaya.length > 0) {
        //     // kalau ada, datanya ditampilkan ke dalam tabel biaya
        //     $('#tabel_biaya > tbody > tr').each(function(idx) {
        //         var id=$(this).attr('id').replace("row_biaya", "");
        //         if(typeof id !== 'undefined') {
        //             let biayaId = $('#biaya_id' + id).val() ?? '';
        //             myjson='{"id":'+JSON.stringify(biayaId)+',"biaya":'+JSON.stringify($('#biaya'+id).val())+', "deskripsi":'+JSON.stringify($('#deskripsi'+id).val())+', "catatan":'+JSON.stringify($('#catatan_biaya'+id).val())+'}';
        //             var obj=JSON.parse(myjson);

        //             array_detail_biaya.push(obj);

        //             //logic itung uang
        //             total_biaya += parseFloat($('#biaya' + id).val().replace(/,/g, "")) || 0;
        //         }

        //         // ini buat di simpan (hidden), nanti dikirim waktu post
        //         $('#obj_biaya'+key).val(JSON.stringify(array_detail_biaya));
        //     });
            
        //     // ini ngitung semua uangnya
        //     $('#uang_jalan_'+key).val(total_biaya);
        // } else {
        //     // kalau ga, delete semua
        //     $('#obj_biaya'+key).val('');
        // }

        // // clear biar ga nyantol data lama
        // $('#tabel_biaya tbody').html('');


        $('#modal_detail').modal('hide');
    });

    $(document).on('click', '.save_detailOld', function(){
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
        $('#harga_per_kg_hidden_'+key).val($('#harga_per_kg').val());
        $('#min_muatan_hidden_'+key).val($('#min_muatan').val());
        $('#grup_hidden_'+key).val($('#grup').val());
        $('#marketing_hidden_'+key).val($('#marketing').val());

        $('#deleted_biaya').val($('#deleted_biaya_temp').val());

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
                    let biayaId = $('#biaya_id' + id).val() ?? '';
                    myjson='{"id":'+JSON.stringify(biayaId)+',"biaya":'+JSON.stringify($('#biaya'+id).val())+', "deskripsi":'+JSON.stringify($('#deskripsi'+id).val())+', "catatan":'+JSON.stringify($('#catatan_biaya'+id).val())+'}';
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

        // get id yg dihapus
        var row = $(this).closest("tr");
        var biayaIdInput = row.find("input[name='biaya_id"+button_id+"']");
        var biaya_id_value = biayaIdInput.val();

        // push ke array global
        if(biaya_id_value) {
            deleted_biaya.push(biaya_id_value);
            $("#deleted_biaya_temp").val(deleted_biaya.join(","));
        }

        $('#row_biaya'+button_id+'').remove();  
    });


    $(document).on('click', '.btn_remove', function(){  
        // get id button
        var button_id = $(this).attr("id");             
        
        // get id yg dihapus
        var row = $(this).closest("tr");
        var hiddenInput = row.find("input[name^='data['][name$='[id_tujuan]']");
        var deleted = hiddenInput.val();

        // push ke array global
        if(deleted_tujuan) {
            deleted_tujuan.push(deleted);
            $("#deleted_tujuan").val(deleted_tujuan.join(","));
        }

        // remove dari tabel
        $('#row'+button_id+'').remove();  
    });
 
    dropdownJenis();

    function dropdownJenis(){
        // alert($('#select_jenis_tujuan').val());
        $('#select_jenis_tujuan').on('select2:select', function (e){
            // if($('#jenis_tujuan_'+key).val() == 'FTL'){

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
});

		
</script>

@endsection
