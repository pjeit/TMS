@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('grup.index')}}">Grup</a></li>
<li class="breadcrumb-item">Create</li>

@endsection

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

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
    
    <form action="{{ route('grup.store') }}" method="POST" >
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Data</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Nama Grup<span class="text-red">*</span></label>
                            <input required type="text" name="nama_grup" class="form-control" value="{{old('nama_grup','')}}" >
                        </div>
                        <div class="form-group">
                            <label for="">Total Kredit</label>
                            <input type="text" name="total_kredit" class="form-control numaja uang" value="{{ old('total_kredit', '') }}" id="total_kredit">

                        </div>
                        <div class="form-group">
                            <label for="">Total Max Kredit</label>
                            <input required type="text" name="total_max_kredit" class="form-control numaja uang" value="{{old('total_max_kredit','')}}" id='total_max_kredit' >
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">PIC</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Nama PIC<span class="text-red">*</span></label>
                            <input required type="text" name="nama_pic" class="form-control" value="{{old('nama_pic','')}}" >
                        </div>           
                        <div class="form-group">
                            <label for="">Email<span class="text-red">*</span></label>
                            <input required type="email" name="email" class="form-control" value="{{old('email','')}}" >
                        </div>           
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="">Telp 1<span class="text-red">*</span></label>
                                <input required type="text" name="telp1" class="form-control" value="{{old('telp1','')}}" >
                            </div>          
                            <div class="form-group col-6">
                                <label for="">Telp 2</label>
                                <input type="text" name="telp2" class="form-control" value="{{old('telp2','')}}" >
                            </div>          
                        </div>    

                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class='row' >
                        <div class="col-lg-12 col-md-12 col-12">
                            
                            <button type="button" class="btn btn-sm btn-primary" onclick="open_detail('') "><i class='fas fa-plus-circle'></i><b style="font-size:16px">&nbsp; DAFTAR TUJUAN & TARIF</b>
                            </button>
                
                            <div class="row" style='margin-top:5px;'>
                                <div class='col-12 table-responsive'>
                                    <table class="table table-hover table-bordered table-striped text-nowrap" id='table_tujuan'>
                                        <thead>
                                            <tr>
                                                <th style="width:30px"></th>
                                                <th>Tujuan</th>
                                                <th style="width:1px; white-space: nowrap; text-align:center;">Jenis Tujuan</th>
                                                <th style="width:1px; white-space: nowrap; text-align:right;">Tarif</th>
                                                <th style="width:1px; white-space: nowrap; text-align:right;">Harga Per Kg</th>
                                                <th style="width:1px; white-space: nowrap; text-align:right;">Minimum Muatan(Kg)</th>
                                                <th style="width:1px; white-space: nowrap; text-align:right;">Uang Jalan</th>
                                                <th style="width:1px; white-space: nowrap; text-align:right;">Komisi</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        
    </form>
    
    <div class="modal fade" id="tujuan_dialog" tabindex='-1'>
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
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
                                <input type="text" name="nama_tujuan" class="form-control" id="nama_tujuan" placeholder="Singkatan (Max. 10 karakter)"> 
                            </div>
                            <div class="form-group">
                                <label for="alamat_tujuan">Alamat</label>
                                <input type="text" name="alamat_tujuan" class="form-control" id="alamat_tujuan" placeholder=""> 
                            </div>
                            <div class="form-group">
                                <label for="select_jenis_tujuan">Jenis Tujuan <span style="color:red;">*</span></label>
                                <select name="select_jenis_tujuan" id="select_jenis_tujuan" style="width:100%" data-placeholder="Pilih Customer">
                                    <option value="LCL">Less Container Load (LCL)</option>
                                    <option value="FCL">Full Container Load (FCL)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="catatan_tujuan">Catatan</label>
                                <input type="text" name="catatan_tujuan" class="form-control" id="catatan_tujuan" placeholder=""> 
                            </div>
                            
                        </div>
                        <div class='col-lg-6 col-md-6 col-12'>
                            <div id="fcl_selected" name="fcl_selected" class="row" style="display: none;">
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
                            <div name="lcl_selected" id="lcl_selected" class="row">
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
                                    <label for="min_muatan">Muatan Min. <span style="color:red;">*</span></label>
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
                
                            <!-- <label for="lcl_selected">Minimimum Muatan dan Harga per KG</label> -->
                            <!-- <div name="lcl_selected" id="lcl_selected" class="row">
                                <div class="col-8 col-md-12 col-lg-8">
                                    <label for="harga_per_kg">Harga per KG <span style="color:red;">*</span></label>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp. </span>
                                            </div>
                                            <input type="text" class="form-control uang" name="harga_per_kg" id="harga_per_kg">
                                            <div class="input-group-append">
                                                <span class="input-group-text">/Kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 col-md-12 col-lg-4">
                                    <label for="min_muatan">Muatan Min. <span style="color:red;">*</span></label>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control numaja" name="min_muatan" id="min_muatan">
                                            <div class="input-group-append">
                                                <div class="input-group-text">Kg</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class='row'>
                        <div class="table-responsive p-0">
                            <div class='col-12'>
                                <?php //if($akses_id != 3){ ?>
                                    <!-- <button type="button" class="btn btn-sm btn-primary" onclick="open_detail_uang_jalan('')"><i class='fas fa-plus-circle'></i><b style="font-size:16px">&nbsp; BIAYA UANG JALAN</b></button> -->
                                <?php //} ?>
    
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
              <?php //if($akses_id != 3){ ?>
                <!-- <button type="button" class="btn btn-sm btn-success" style='width:85px' onclick='save_detail()'>OK</button> -->
              <?php //} ?>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</div>

{{-- <script src="{{asset('assets/dist/js/formatUang.js')}}"></script>
<script src="{{asset('assets/dist/js/keyPressuang.js')}}"></script> --}}
<script>
     function open_detail(key){
        if(key===''){
            var last_id=($('#table_tujuan tr:last').attr('id'));
            if(typeof last_id === 'undefined') {
                var last_id=0;
            }else{
                var last_id=parseInt(last_id)+1
            }
            var idx=last_id;
            $('#nama_tujuan').val('');
            $('#label-nama-tujuan').text('');
            $('#alamat_tujuan').val('');
            $('#catatan_tujuan').val('');
            $('#tarif').val('');
			$('#harga_per_kg').val('');
			$('#min_muatan').val('');
            $('#uang_jalan').val('');
            $('#komisi').val('');
            $('#table_uang_jalan tbody').html('');
            $('#tujuan_id').val('');
			$('#select_jenis_tujuan').val('LCL').trigger('change');
			$('#lcl_selected').css('display','');
			$('#fcl_selected').css('display','none');
        }else{
            var idx=key;
            $('#nama_tujuan').val($('#nama_'+idx).text());
            $('#label-nama-tujuan').text($('#nama_'+idx).text());
            $('#alamat_tujuan').val($('#alamat_'+idx).text());
            $('#catatan_tujuan').val($('#catatan_'+idx).text());
            $('#uang_jalan').val($('#uang_jalan_'+idx).text());
            $('#komisi').val($('#komisi_'+idx).text());
            $('#tujuan_id').val($('#tujuan_id_'+idx).text());
			
			if($('#jenis_tujuan_'+idx).text() == 'LCL')
			{
				$('#select_jenis_tujuan').val('LCL').trigger('change');

				$('#lcl_selected').css('display','');
				$('#fcl_selected').css('display','none');

				$('#tarif').val('');
				$('#min_muatan').val($('#min_muatan_'+idx).text());
				$('#harga_per_kg').val($('#harga_per_kg_'+idx).text());
			}
			else{
				$('#select_jenis_tujuan').val('FCL').trigger('change');

				$('#lcl_selected').css('display','none');
				$('#fcl_selected').css('display','');

				$('#tarif').val($('#tarif_'+idx).text());
				$('#min_muatan').val('');
				$('#harga_per_kg').val('');
			}
            var detail_uang_jalan=$('#detail_uang_jalan_'+idx).text();
            var array_detail_uang_jalan=JSON.parse(detail_uang_jalan);
            console.log(array_detail_uang_jalan);
            var row_uang_jalan='';

			// var akses_id = <?= isset($akses_id)?$akses_id:''?>;
			
			// if(akses_id == 3){
			// 	for(var keys in array_detail_uang_jalan){
			// 		row_uang_jalan+='<tr id="key_'+keys+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail_uang_jalan('+keys+')"><span class="fas fa-edit" style="width:24px"></span>Lihat</a></li></ul></div></td><td id="biaya_id_'+keys+'" hidden>'+array_detail_uang_jalan[keys].biaya_id+'</td><td id="deskripsi_'+keys+'">'+array_detail_uang_jalan[keys].deskripsi+'</td><td id="biaya_'+keys+'">'+addPeriod(array_detail_uang_jalan[keys].biaya, ',')+'</td><td id="catatan_biaya_'+keys+'" hidden>'+(array_detail_uang_jalan[keys].catatan==null?'':array_detail_uang_jalan[keys].catatan)+'</td></tr>';
			// 	}
			// }
			// else{
			// 	for(var keys in array_detail_uang_jalan){
			// 		row_uang_jalan+='<tr id="key_'+keys+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail_uang_jalan('+keys+')"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail_uang_jalan('+keys+')"><span class="fas fa-eraser" style="width:24px"></span>Hapus</a></li></ul></div></td><td id="biaya_id_'+keys+'" hidden>'+array_detail_uang_jalan[keys].biaya_id+'</td><td id="deskripsi_'+keys+'">'+array_detail_uang_jalan[keys].deskripsi+'</td><td id="biaya_'+keys+'">'+addPeriod(array_detail_uang_jalan[keys].biaya, ',')+'</td><td id="catatan_biaya_'+keys+'" hidden>'+(array_detail_uang_jalan[keys].catatan==null?'':array_detail_uang_jalan[keys].catatan)+'</td></tr>';
			// 	}
			// }

			for(var keys in array_detail_uang_jalan){
				row_uang_jalan+='<tr id="key_'+keys+'"><td><div class="btn-group"><button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false"></button><ul class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(-22px, -84px, 0px); top: 0px; left: 0px; will-change: transform;"><li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail_uang_jalan('+keys+')"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li><li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail_uang_jalan('+keys+')"><span class="fas fa-eraser" style="width:24px"></span>Hapus</a></li></ul></div></td><td id="biaya_id_'+keys+'" hidden>'+array_detail_uang_jalan[keys].biaya_id+'</td><td id="deskripsi_'+keys+'">'+array_detail_uang_jalan[keys].deskripsi+'</td><td id="biaya_'+keys+'">'+addPeriod(array_detail_uang_jalan[keys].biaya, ',')+'</td><td id="catatan_biaya_'+keys+'" hidden>'+(array_detail_uang_jalan[keys].catatan==null?'':array_detail_uang_jalan[keys].catatan)+'</td></tr>';
			}
            
            
            $('#table_uang_jalan tbody').html(row_uang_jalan);
        }
        
        $('#key').val(idx);
        $('#tujuan_dialog').modal('show');
     };
</script>
    
{{-- script isian detail tujuan --}}
<script>
    // $(document).ready(function(e){
    //     $(document.body).on("change","#select_jenis_tujuan",function(val){
    //         if(this.value == "LTL"){
    //             $('#ltl_selected').css('display','');
    //             $('#ftl_selected').css('display','none');
    //         } else {
    //             $('#ltl_selected').css('display','none');
    //             $('#ftl_selected').css('display','');
    //         }

    //     });
    // });
</script>
@endsection
