
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
    <form action="{{ route('pembayaran_gaji.store') }}" id="post_data" method="POST" >
        @csrf
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('pembayaran_gaji.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                 <button type="submit" class="btn btn-success radiusSendiri" id="btnSimpan">
                    <i class="fa fa-fw fa-save"></i> Simpan    
                 </button>
            </div>
            <div class="card-body" >
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">

                        <div class="row">
                             <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="select_bulan">Bulan<span style="color:red">*</span></label>
                                <select class="form-control select2 @error('select_bulan') is-invalid @enderror" style="width: 100%;" id='select_bulan' name="select_bulan">
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                @error('select_bulan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <label for="tahun_periode">Tahun<span style="color:red">*</span></label>
                                <input type="text" name="tahun_periode" class="form-control numaja" id="tahun_periode" maxlength="4" minlength="4" value=""> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="tanggal">Tanggal Bayar<span style='color:red'>*</span></label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" name="tanggal" autocomplete="off" class="date form-control" id="tanggal" placeholder="dd-M-yyyy" value="" onchange="">
                                </div>
                            </div>
                             <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="nama_periode">Periode<span style='color:red'>*</span></label>
                                <input type="text" name="nama_periode" class="form-control" id="nama_periode" placeholder="Periode Gajian" value=""> 
                            </div>      
                            {{-- <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="tanggal_catat">Tanggal Catat<span style='color:red'>*</span></label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" name="tanggal_catat" autocomplete="off" class="date form-control" id="tanggal_catat" placeholder="dd-M-yyyy" value="">
                                </div>
                            </div> --}}
                        </div>
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <input type="text" name="catatan" class="form-control" id="catatan" placeholder="" value=""> 
                        </div>  
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="total">Total</label>
                            <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="text" name="total" class="form-control numaja uang" id="total" placeholder="" readonly value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Pilih Kas<span class="text-red">*</span> </label>
                            <select name="kas" class="select2" style="width: 100%" id="kas" required>
                                <option value="">── PILIH KAS ──</option>
                                @foreach ($dataKas as $kas)
                                    <option value="{{ $kas->id }}" {{ $kas->id == 1? 'selected':''}}>{{ $kas->nama }}</option>
                                @endforeach
                            </select>
                        </div> 
                    </div>
                </div>
            </div>
        </div> 
        <div class='row' >
            <div class="col-lg-12 col-md-12 col-12">
                <button type="button" class="btn btn-sm btn-primary" onclick="open_detail('')"><i class='fas fa-plus-circle'></i><b style="font-size:16px">&nbsp; DETAIL KARYAWAN</b></button>
                <div class="row table-responsive p-0" style='margin-top:5px;'>
                    <div class='col-12'>
                        <table class="table table-hover table-bordered table-striped text-nowrap" id='table_gaji_karyawan'>
                            <thead>
                                <tr>
                                <th style="width:1px; white-space: nowrap;">Karyawan</th>
                                <th style="width:1px; white-space: nowrap; text-align:right;">Gaji</th>
                                <th style="width:1px; white-space: nowrap; text-align:right;">Pot. Hutang</th>
                                <th style="width:1px; white-space: nowrap; text-align:right;">Pend. Lain</th>
                                <th style="width:1px; white-space: nowrap; text-align:right;">Pot. Lain</th>
                                <th style="width:1px; white-space: nowrap; text-align:right;">Total Diterima</th>
                                <th style="width:1px; white-space: nowrap;">Catatan</th>
                                <th style="width:30px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($pembayaran_gaji_detail))

                                    @foreach ( $pembayaran_gaji_detail as $key => $item)
                                        
                                        <tr id="{{$key}}">
                                            <td id="gaji_detail_id_{{$key}}" hidden>{{$item->id}}</td>
                                            <td id="is_aktif_{{$key}}" hidden>{{$item->is_aktif}}</td>
                                            <td id="karyawan_id_{{$key}}" hidden></td>
                                            <td id="karyawan_name_{{$key}}"></td>
                                            <td style="text-align:right;" id="total_gaji_{{$key}}"></td>
                                            <td style="text-align:right;" id="total_hutang_{{$key}}" hidden></td>
                                            <td style="text-align:right;" id="potongan_hutang_{{$key}}"></td>
                                            <td style="text-align:right;" id="pendapatan_lain_{{$key}}"></td>
                                            <td style="text-align:right;" id="potongan_lain_{{$key}}"></td>
                                            <td style="text-align:right;" id="total_diterima_{{$key}}"></td>
                                            <td id="catatan_{{$key}}"></td>
                                            <td>
                                                <div class="btn-group dropleft">
                                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa fa-list"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="javascript:void(0)" onclick="open_detail({{$key}})"><span class="fas fa-edit"></span> Ubah</a>
                                                        <a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail({{$key}})"><span class="fas fa-eraser"></span> Hapus</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <input type="hidden" name="detail[{{$key}}][gaji_detail_id]" id="input_gaji_detail_id_{{$key}}" value="">
                                            <input type="hidden" name="detail[{{$key}}][is_aktif]" id="input_is_aktif_{{$key}}" value="">
                                            <input type="hidden" name="detail[{{$key}}][karyawan_id]" id="input_karyawan_id_{{$key}}" value="">
                                            <input type="hidden" name="detail[{{$key}}][karyawan_name]" id="input_karyawan_name_{{$key}}" value="">
                                            <input type="hidden" name="detail[{{$key}}][total_gaji]" id="input_total_gaji_{{$key}}" value="">
                                            <input type="hidden" name="detail[{{$key}}][total_hutang]" id="input_total_hutang_{{$key}}" value="">
                                            <input type="hidden" name="detail[{{$key}}][potongan_hutang]" id="input_potongan_hutang_{{$key}}" value="">
                                            <input type="hidden" name="detail[{{$key}}][pendapatan_lain]" id="input_pendapatan_lain_{{$key}}" value="">
                                            <input type="hidden" name="detail[{{$key}}][potongan_lain]" id="input_potongan_lain_{{$key}}" value="">
                                            <input type="hidden" name="detail[{{$key}}][total_diterima]" id="input_total_diterima_{{$key}}" value="">
                                            <input type="hidden" name="detail[{{$key}}][catatan_detail]" id="input_catatan_{{$key}}" value="">
                                        </tr>
                                    @endforeach
                                    
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- modal --}}
<div class="modal fade" id="detail_dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detail Karyawan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <form id='form_add_detail'>
                <input type="hidden" name="key" id="key">
                <input type="hidden" name="gaji_detail_id" id="gaji_detail_id">
                
                <div class="form-group">
                    <div class="form-group">
                        <label for="select_karyawan">Karyawan<span style='color:red'>*</span></label>
                       
                        <select name="select_karyawan" class="select2" style="width: 100%" id="select_karyawan" required>
                            <option value="">── PILIH KARYAWAN ──</option>
                            @foreach ($dataKaryawan as $data)
                                <option value="{{ $data->idKaryawan }}" 
                                    attr_nama="{{$data->nama_lengkap}}"
                                    attr_gaji="{{$data->gaji}}"
                                    attr_total_hutang="{{$data->total_hutang}}"
                                    >{{ $data->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="karyawan_id">
                        <input type="hidden" id="karyawan_name">
                        <input type="hidden" id="karyawan_gaji">
                        <input type="hidden" id="karyawan_hutang">


                    </div>
                </div>
                <div class="form-group">
                    <div class='row'>
                        <div class='col-6 col-md-6 col-lg-6'>
                            <label for="total_gaji">Total Gaji</label>
                            <div class="input-group mb-0">
                              <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                              </div>
                              <input type="text" class="form-control numaja uang" id="total_gaji" placeholder="" readonly>
                            </div>
                        </div>
                    </div>                                          
                </div>
                <div class="form-group">
                    <div class='row'>
                        <div class='col-6 col-md-6 col-lg-6'>
                            <label for="total_hutang">Total Hutang</label>
                            <div class="input-group mb-0">
                              <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                              </div>
                              <input type="text" class="form-control numaja uang" id="total_hutang" placeholder="" readonly>
                            </div>
                        </div>
                        <div class='col-6 col-md-6 col-lg-6'>
                            <label for="potongan_hutang">Potongan Hutang</label>
                            <div class="input-group mb-0">
                              <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                              </div>
                              <input type="text" class="form-control numaja uang" id="potongan_hutang" placeholder="" onkeyup="cek_potongan_hutang();hitung_total();">
                            </div>
                        </div>
                    </div>                                          
                </div>
                <div class="form-group">
                    <div class='row'>
                        <div class='col-6 col-md-6 col-lg-6'>
                            <label for="pendapatan_lain">Pendapatan Lain</label>
                            <div class="input-group mb-0">
                              <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                              </div>
                              <input type="text" class="form-control numaja uang" id="pendapatan_lain" placeholder="" onkeyup="hitung_total();">
                            </div>
                        </div>
                        <div class='col-6 col-md-6 col-lg-6'>
                            <label for="potongan_lain">Potongan Lain</label>
                            <div class="input-group mb-0">
                              <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                              </div>
                              <input type="text" class="form-control numaja uang" id="potongan_lain" placeholder="" onkeyup="hitung_total();">
                            </div>
                        </div>
                    </div>                      
                </div>
                <div class="form-group">
                    <label for="total_diterima">Total Diterima</label>
                    <div class="input-group mb-0">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                      </div>
                      <input type="text" class="form-control numaja uang" id="total_diterima" placeholder="" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label for="catatan">Catatan</label>
                    <input type="text" class="form-control" id="catatan_detail"> 
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
          <button type="button" class="btn btn-sm btn-success" style='width:85px' onclick='save_detail()'>Ok</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
{{-- modal --}}
<script type="text/javascript">
    // function tanggalcatat(kas){
	// 	let pos = kas.search(/besar/i);

	// 	if(pos > -1){
	// 		$('#tanggal_catat').attr('disabled', false);

	// 		if($('#tanggal').val() != ''){
	// 			$('#tanggal_catat').val($('#tanggal').val());
	// 		}
	// 	}else{
	// 		$('#tanggal_catat').attr('disabled', true);
	// 		$('#tanggal_catat').val('');
	// 	}
	// }
    function hitung_subtotal(){ 
        var total_diterima=0;
        $('#table_gaji_karyawan > tbody  > tr').each(function(idx) {
            if($(this).is(":visible")){
                var id=$(this).attr('id');
                console.log(id);
                if(typeof id !== 'undefined') {
                    total_diterima+=parseFloat(($('#input_total_diterima_'+id).val()==''?0:removePeriod($('#input_total_diterima_'+id).val(),',')));
                }
            }
        });
        $('#total').val(addPeriod(total_diterima,','));
    }
    function hitung_total(){
        
        if($('#total_gaji').val()!=''){
            var total_gaji=removePeriod($('#total_gaji').val(),',');
        }else{
            var total_gaji=0;
        }
        
        if($('#potongan_hutang').val()!=''){
            var potongan_hutang=removePeriod($('#potongan_hutang').val(),',');
        }else{
            var potongan_hutang=0;
        }
        
        if($('#pendapatan_lain').val()!=''){
            var pendapatan_lain=removePeriod($('#pendapatan_lain').val(),',');
        }else{
            var pendapatan_lain=0;
        }
        
        if($('#potongan_lain').val()!=''){
            var potongan_lain=removePeriod($('#potongan_lain').val(),',');
        }else{
            var potongan_lain=0;
        }
        
        var total_diterima=parseFloat(total_gaji)-parseFloat(potongan_hutang)+parseFloat(pendapatan_lain)-parseFloat(potongan_lain);
        if(total_diterima>0){
            $('#total_diterima').val(addPeriod(total_diterima,','));
        }else{
            $('#total_diterima').val(0);
        }
    }
    function cek_karyawan_id(karyawan_id){
        var allow_add=true;
        $('#table_gaji_karyawan > tbody  > tr').each(function(idx) {
            if($(this).is(":visible")){
                var id=$(this).attr('id');
                console.log(id);
                if(typeof id !== 'undefined') {
                    if($('#input_karyawan_id_'+id).val()==karyawan_id){
                        allow_add=false;
                    }
                }
            }
        });
        return allow_add;
    }
     function delete_detail(id_tombol){
        if($('#input_gaji_detail_id_'+id_tombol).text()!=''){
            $('#'+id_tombol).hide();
            $('#is_aktif_'+id_tombol).text('N');
            $('#input_is_aktif_'+id_tombol).val('N');

        }else{
            $('#'+id_tombol).remove();
        }
        hitung_subtotal()
    }
    function save_detail(){
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

      
        var key=$('#key').val();
        if($('#karyawan_id').val()==''){
            // toastr.error('Karyawan harus diisi');
            Toast.fire({
                    icon: 'error',
                    text: `Karyawan harus diisi`,
                })
            return;}
        if($('#total_diterima').val()=='' || $('#total_diterima').val()=='0'){toastr.error('Total diterima harus diisi');return;}
        var exist=$('#table_gaji_karyawan tbody').find('#'+key).attr('id');
        if(typeof exist === 'undefined') {
            if(cek_karyawan_id($('#karyawan_id').val())){
                var new_row=`
                <tr id="${key}">
                    
                    <td id="gaji_detail_id_${key}" hidden>${$('#gaji_detail_id').val()}</td>
                    <td id="is_aktif_${key}" hidden>Y</td>
                    <td id="karyawan_id_${key}" hidden>${$('#karyawan_id').val()}</td>
                    <td id="karyawan_name_${key}">${$('#karyawan_name').val()}</td>
                    <td style="text-align:right;" id="total_gaji_${key}">${$('#total_gaji').val()}</td>
                    <td style="text-align:right;" id="total_hutang_${key}" hidden>${$('#total_hutang').val()}</td>
                    <td style="text-align:right;" id="potongan_hutang_${key}">${$('#potongan_hutang').val()}</td>
                    <td style="text-align:right;" id="pendapatan_lain_${key}">${$('#pendapatan_lain').val()}</td>
                    <td style="text-align:right;" id="potongan_lain_${key}">${$('#potongan_lain').val()}</td>
                    <td style="text-align:right;" id="total_diterima_${key}">${$('#total_diterima').val()}</td>
                    <td id="catatan_${key}">${$('#catatan_detail').val()}</td>
                    <td>
                        <div class="btn-group dropleft">
                            <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-list"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="javascript:void(0)" onclick="open_detail(${key})"><span class="fas fa-edit"></span> Ubah</a>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail(${key})"><span class="fas fa-eraser"></span> Hapus</a>
                            </div>
                        </div>
                    </td>
                    <input type="hidden" name="detail[${key}][gaji_detail_id]" id="input_gaji_detail_id_${key}" value="${$('#gaji_detail_id').val()}">
                    <input type="hidden" name="detail[${key}][is_aktif]" id="input_is_aktif_${key}" value="Y">
                    <input type="hidden" name="detail[${key}][karyawan_id]" id="input_karyawan_id_${key}" value="${$('#karyawan_id').val()}">
                    <input type="hidden" name="detail[${key}][karyawan_name]" id="input_karyawan_name_${key}" value="${$('#karyawan_name').val()}">
                    <input type="hidden" name="detail[${key}][total_gaji]" id="input_total_gaji_${key}" value="${$('#total_gaji').val()}">
                    <input type="hidden" name="detail[${key}][total_hutang]" id="input_total_hutang_${key}" value="${$('#total_hutang').val()}">
                    <input type="hidden" name="detail[${key}][potongan_hutang]" id="input_potongan_hutang_${key}" value="${$('#potongan_hutang').val()}">
                    <input type="hidden" name="detail[${key}][pendapatan_lain]" id="input_pendapatan_lain_${key}" value="${$('#pendapatan_lain').val()}">
                    <input type="hidden" name="detail[${key}][potongan_lain]" id="input_potongan_lain_${key}" value="${$('#potongan_lain').val()}">
                    <input type="hidden" name="detail[${key}][total_diterima]" id="input_total_diterima_${key}" value="${$('#total_diterima').val()}">
                    <input type="hidden" name="detail[${key}][catatan_detail]" id="input_catatan_${key}" value="${$('#catatan_detail').val()}">

                </tr>`;
            }else{
                //   event.preventDefault(); 
                Toast.fire({
                    icon: 'warning',
                    text: `Karyawan sudah ditambahkan, pilih karyawan lainnya`,
                });
                
                // return;
                // toastr.warning('Karyawan sudah ditambahkan, pilih karyawan lainnya');
            }
            $('#table_gaji_karyawan > tbody:last-child').append(new_row);
        }else{
            $('#gaji_detail_id_'+key).text($('#gaji_detail_id').val());
            $('#karyawan_id_'+key).text($('#karyawan_id').val());
            $('#karyawan_name_'+key).text($('#karyawan_name').val());
            $('#total_gaji_'+key).text($('#total_gaji').val());
            $('#total_hutang_'+key).text($('#total_hutang').val());
            $('#potongan_hutang_'+key).text($('#potongan_hutang').val());
            $('#pendapatan_lain_'+key).text($('#pendapatan_lain').val());
            $('#potongan_lain_'+key).text($('#potongan_lain').val());
            $('#total_diterima_'+key).text($('#total_diterima').val());
            $('#catatan_'+key).text($('#catatan_detail').val());
            $('#is_aktif_'+key).text('Y');

            //yang input hidden buat simpen di db
            $('#input_gaji_detail_id_'+key).val($('#gaji_detail_id').val());
            $('#input_karyawan_id_'+key).val($('#karyawan_id').val());
            $('#input_karyawan_name_'+key).val($('#karyawan_name').val());
            $('#input_total_gaji_'+key).val($('#total_gaji').val());
            $('#input_total_hutang_'+key).val($('#total_hutang').val());
            $('#input_potongan_hutang_'+key).val($('#potongan_hutang').val());
            $('#input_pendapatan_lain_'+key).val($('#pendapatan_lain').val());
            $('#input_potongan_lain_'+key).val($('#potongan_lain').val());
            $('#input_total_diterima_'+key).val($('#total_diterima').val());
            $('#input_catatan_'+key).val($('#catatan_detail').val());
            $('#input_is_aktif_'+key).val('Y');
        }
        
        hitung_subtotal();
        $('#detail_dialog').modal('hide');
    }
    function open_detail(key){
        if(key===''){
            var last_id=($('#table_gaji_karyawan tr:last').attr('id'));
            if(typeof last_id === 'undefined') {
                var last_id=0;
            }else{
                var last_id=parseInt(last_id)+1
            }
            var idx=last_id;
            $('#select_karyawan').val(null).trigger('change');
            $('#gaji_detail_id').val('');
            $('#karyawan_id').val('');
            $('#karyawan_name').val('');
            $('#total_gaji').val('');
            $('#total_hutang').val('');
            $('#potongan_hutang').val('');
            $('#pendapatan_lain').val('');
            $('#potongan_lain').val('');
            $('#total_diterima').val('');
            $('#catatan_detail').val('');
        }else{
            var idx=key;
            // var option = new Option($('#karyawan_name_'+idx).text(), $('#karyawan_id_'+idx).text(), true, true);
            $('#select_karyawan').val($('#input_karyawan_id_'+idx).val()).trigger('change');
            $('#gaji_detail_id').val($('#input_gaji_detail_id_'+idx).val());
            $('#karyawan_id').val($('#input_karyawan_id_'+idx).val());
            $('#karyawan_name').val($('#input_karyawan_name_'+idx).val());
            $('#total_gaji').val($('#input_total_gaji_'+idx).val());
            $('#total_hutang').val($('#input_total_hutang_'+idx).val());
            $('#potongan_hutang').val($('#input_potongan_hutang_'+idx).val());
            $('#pendapatan_lain').val($('#input_pendapatan_lain_'+idx).val());
            $('#potongan_lain').val($('#input_potongan_lain_'+idx).val());
            $('#total_diterima').val($('#input_total_diterima_'+idx).val());
            $('#catatan_detail').val($('#input_catatan_'+idx).val());
        }
        $('#key').val(idx);
        $('#detail_dialog').modal('show');
        hitung_total();
    }
    function cek_potongan_hutang(){
        if($('#total_hutang').val()!=''){
            var total_hutang =removePeriod($('#total_hutang').val(),',');
        }else{
            var total_hutang =0;
        }
        var potong_hutang = removePeriod($('#potongan_hutang').val(),',');
        if(parseFloat(potong_hutang)>parseFloat(total_hutang)){
            $('#potongan_hutang').val(addPeriod(total_hutang,','));
        }else{
            $('#potongan_hutang').val(addPeriod(potong_hutang,','));
        }
    }
    $(document).ready(function() {
        $('#tahun_periode').val(new Date().getFullYear());
        $('#select_bulan').val(new Date().getMonth()+1).trigger('change');
        
        $('#tanggal').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            endDate: "0d"
        });
        $('#tanggal_catat').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
        });
        $('body').on('change','#select_karyawan',function()
        {
            var id_karyawan = $(this).val();
            var selectedOption = $(this).find('option:selected');
            var attr_nama = selectedOption.attr('attr_nama');
            var attr_gaji = selectedOption.attr('attr_gaji');
            var attr_total_hutang = selectedOption.attr('attr_total_hutang');
            
            $('#karyawan_id').val(id_karyawan);
            $('#karyawan_name').val(attr_nama);
            $('#karyawan_gaji').val(attr_gaji);
            $('#karyawan_hutang').val(attr_total_hutang);
            $('#total_gaji').val(attr_gaji?addPeriod(attr_gaji,','):0);
            $('#total_hutang').val(attr_total_hutang?addPeriod(attr_total_hutang,','):0);
            hitung_total();
            cek_potongan_hutang();
        });
        $('#post_data').submit(function(event) {
            var kas = $('#kas').val();
            if (kas == '' || kas == null) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    text: 'KAS PEMBAYARAN WAJIB DIPILIH!',
                })
                return;
            }
            if ($('#tanggal').val().trim() == '') {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    text: 'TANGGAL WAJIB DIISI!',
                })
                return;
            }
            if ($('#tahun_periode').val().trim() == '') {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    text: 'TAHUN PERIODE WAJIB DIISI!',
                })
                return;
            }
            if ($('#nama_periode').val().trim() == '') {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    text: 'PERIODE WAJIB DIISI!',
                })
                return;
            }
             if ($('#total').val().trim() == '' ||$('#total').val().trim() == 0) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    text: 'TIDAK ADA DATA PEMBAYARAN GAJI KARYAWAN!',
                })
                return;
            }

            
            event.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin data sudah benar ?',
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
                    }, 800); // 2000 milliseconds = 2 seconds
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


