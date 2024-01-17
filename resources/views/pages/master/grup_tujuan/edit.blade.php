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
    .card-header:first-child{
        border-radius:inherit;
    }
    /* #modal_detail.modal-lg .modal-dialog {
  width: 1516px !important;
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
    
    {{-- <form action="{{ route('grup_tujuan.update',[$id]) }}" id='post_tujuan' method="POST" > --}}
        {{-- @csrf
        @method('PUT') --}}

        <div class="row">
            {{-- <div class="col-12 position-fixed">
                <div class="card radiusSendiri">
                    <div class="card-header">
                            <a href="{{ route('grup_tujuan.index') }}"class="btn btn-secondary radiusSendiri float-left"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                            <button type="submit" class="btn btn-success radiusSendiri float-left ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                            <button type="button" name="add" id="add" class="btn btn-primary radiusSendiri float-right"><i class="fa fa-plus-circle"></i> <strong >Tambah Tujuan</strong></button> 
                        </div>
                </div>
            </div> --}}
            
            {{-- sticky header --}}
            <div class="col-12 radiusSendiri sticky-top " style="margin-bottom: -15px;">
                <div class="card radiusSendiri" style="">
                    <div class="card-header ">
                        <a href="{{ route('grup_tujuan.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        {{-- <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button> --}}

                        <button type="button" name="add" id="add" class="btn btn-primary radiusSendiri float-right"><i class="fa fa-plus-circle"></i> <strong >Tambah Tujuan</strong></button> 
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card radiusSendiri">
                    {{-- <div class="card-header">
                        <a href="{{ route('grup_tujuan.index') }}"class="btn btn-secondary radiusSendiri float-left"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" class="btn btn-success radiusSendiri float-left ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    
                        <button type="button" name="add" id="add" class="btn btn-primary radiusSendiri float-right"><i class="fa fa-plus-circle"></i> <strong >Tambah Tujuan</strong></button> 
                    </div> --}}
                    <div class="card-body" >
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
                                                <th style="white-space: nowrap; text-align:center; justify-content: center; align-items: center; width: 110px;">Komisi Customer</th>
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
                                                            <input type="text" name="data[tujuan][{{$key}}][tarif]" id="tarif_{{$key}}" value="{{ number_format($item->tarif) }}" class="form-control numaja uang tarif" readonly/>
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
                                                        <input type="hidden" id="tujuan_id_{{$key}}" value="{{$item->id}}" >
                                                        <input type="hidden" name="data[tujuan][{{$key}}][id_tujuan]" id="id_tujuan_{{$key}}" value="{{$item->id}}" >
                                                        <input type="hidden" name="data[tujuan][{{$key}}][pic]" id="pic_hidden_{{$key}}" value="{{$item->pic}}" >
                                                        <input type="hidden" name="data[tujuan][{{$key}}][alamat_hidden]" id="alamat_hidden_{{$key}}" value="{{$item->alamat}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][uang_jalan_hidden]" id="uang_jalan_hidden_{{$key}}" value="{{$item->uang_jalan}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][komisi_hidden]" id="komisi_hidden_{{$key}}" value="{{$item->komisi}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][komisi_driver_hidden]" id="komisi_driver_hidden_{{$key}}" value="{{number_format($item->komisi_driver)}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][harga_per_kg_hidden]" id="harga_per_kg_hidden_{{$key}}" value="{{number_format($item->harga_per_kg)}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][min_muatan_hidden]" id="min_muatan_hidden_{{$key}}" value="{{$item->min_muatan}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][grup_hidden]" id="grup_hidden_{{$key}}" value="{{$item->grup_id}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][marketing_hidden]" id="marketing_hidden_{{$key}}" value="{{$item->marketing_id}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][obj_biaya]" id="obj_biaya{{$key}}" value="{{$item->detail_uang_jalan}}">
                                                        <input type="hidden" name="data[tujuan][{{$key}}][seal_pelayaran_hidden]" id="seal_pelayaran_hidden_{{$key}}" value="{{isset($item->seal_pelayaran)? number_format($item->seal_pelayaran):''}}" >
                                                        <input type="hidden" name="data[tujuan][{{$key}}][seal_pje_hidden]" id="seal_pje_hidden_{{$key}}" value="{{isset($item->seal_pje)? number_format($item->seal_pje):''}}" >
                                                        <input type="hidden" name="data[tujuan][{{$key}}][tally_hidden]" id="tally_hidden_{{$key}}" value="{{isset($item->tally)? number_format($item->tally):''}}" >
                                                        <input type="hidden" name="data[tujuan][{{$key}}][plastik_hidden]" id="plastik_hidden_{{$key}}" value="{{isset($item->plastik)? number_format($item->plastik):''}}" >
                                                        <input type="hidden" name="data[tujuan][{{$key}}][kargo_hidden]" id="kargo_hidden_{{$key}}" value="{{ isset($item->kargo) ? $item->kargo : '' }}">

                                                        <td>
                                                            @can('DELETE_GRUP_TUJUAN')
                                                                <button type="button" name="remove" id="{{$key}}" class="btn btn-danger btn_remove"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{-- </form> --}}
    
    <div class="modal fade" id="modal_detail" tabindex='-1' >
        <form action="{{ route('grup_tujuan.update_tujuan') }}" id='post_tujuan' method="POST" >
            @csrf
            
            <div class="modal-dialog modal-lg" style="min-width:70%;">
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
                        <input type="hidden" name="grup_id" id="grup_id" value="{{ $id }}">
                        <div class='row'>
                            <div class="form-group col-lg-6 col-md-6 col-6">
                                <label for="grup">Grup <span style="color:red;">*</span></label>
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
                                {{-- <input required type="text" class="form-control" maxlength="50" name="nama_tujuan" id="nama_tujuan" placeholder="Singkatan 20 Karakter">  --}}
                                <textarea required class="form-control" maxlength="50" name="nama_tujuan" id="nama_tujuan" rows="2" placeholder="Singkatan 20 Karakter"></textarea>
                            </div>
                
                            <div class="form-group col-lg-6 col-md-6 col-6">
                                <label for="alamat">Alamat</label>
                                <textarea class="form-control" name="alamat" id="alamat" rows="2"></textarea>
                            </div>
                            
                            <div class="form-group class='col-lg-6 col-md-6 col-12'">
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

                            <div class="form-group col-12 col-12 col-lg-6">
                                <label for="tarif">Tarif <span class="text-red">*</span></label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp.</span>
                                </div>
                                <input type="text" name="tarif" class="form-control numaja uang" id="tarif" placeholder=""> 
                                </div>
                            </div>

                            <div class="form-group col-12 col-12 col-lg-6">
                                <label for="tarif">PIC</label>
                                <input type="text" name="pic" class="form-control " maxlength="25" id="pic" placeholder="Opsional"> 
                            </div>

                            <div class="col-12 col-md-12 col-lg-6">
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
                            <div class="col-12 col-md-12 col-lg-6">
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
                            <div class="form-group col-lg-3 col-md-6 col-sm-6">
                                <label for="uang_jalan">Uang Jalan Driver</label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="uang_jalan" class="form-control numaja uang" id="uang_jalan" placeholder="" readonly> 
                                </div>
                            </div>
                            <div class="form-group col-lg-3 col-md-6 col-sm-6">
                                <label for="komisi">Komisi Customer</label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="komisi" class="form-control numaja uang" id="komisi" placeholder=""> 
                                </div>
                            </div>
                            <div class="form-group col-lg-3 col-md-6 col-sm-6">
                                <label for="komisi">Komisi Driver</label>
                                <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="text" name="komisi_driver" class="form-control numaja uang" id="komisi_driver" placeholder=""> 
                                </div>
                            </div>
                            <div class="form-group col-lg-3 col-md-6 col-sm-6">
                                <label for="">Kargo</label>
                                <input type="text" name="kargo_pje" class="form-control" id="kargo_pje" placeholder=""> 
                            </div>
                        
                            <div class="form-group col-lg-3 col-md-6 col-sm-6">
                                <label for="">Seal Pelayaran</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="seal_pelayaran" class="form-control numaja uang" id="seal_pelayaran" placeholder="" readonly> 
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><input type="checkbox" id="check_is_seal_pelayaran" name="is_seal_pelayaran"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-3 col-md-6 col-sm-6">
                                <label for="">Seal PJE</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="seal_pje" class="form-control numaja uang" id="seal_pje" placeholder="" readonly> 
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><input type="checkbox" id="check_is_seal_pje" name="is_seal_pje"></span>
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group col-lg-3 col-md-6 col-sm-6">
                                <label for="">Tally</label>
                                
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="tally_pje" class="form-control numaja uang" id="tally_pje" placeholder="" > 
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><input type="checkbox" id="check_is_tally" name="is_tally"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-3 col-md-6 col-sm-6">
                                <label for="">Plastik</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp.</span>
                                    </div>
                                    <input type="text" name="plastik_pje" class="form-control numaja uang" id="plastik_pje" placeholder="" > 
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><input type="checkbox" id="check_is_plastik" name="is_plastik"></span>
                                    </div>

                                </div>
                            </div>
                            <input type="hidden" name="total_tarif" class="form-control numaja uang" id="total_tarif" placeholder="" readonly> 
                            <input type="hidden" name="delete_biaya" class="form-control" id="delete_biaya"> 
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
                    @can('EDIT_GRUP_TUJUAN')
                        <button type="submit" class="btn btn-sm btn-success save_detailx" style='width:85px'>SIMPAN</button> 
                    @endcan
                </div>
            </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="modal_hapus" tabindex='-1' >
        <form action="{{ route('grup_tujuan.delete_tujuan') }}" id='post_tujuan' method="POST" >
            @csrf
            
            <div class="modal-dialog modal-sm" >
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <h5>Apakah anda yakin ingin menghapus data ini?</h5>
                        <input type="hidden" id="delete_tujuan" name="delete_tujuan">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-sm btn-success save_detailx" style='width:85px'>HAPUS</button> 
                    </div>
                </div>
            </div>
        </form>
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
                    text: 'Tarif harus lebih besar daripada uang jalan!',
                })
            }

            // let cekKolom = $('#dynamic_field > tbody > tr');
            // if (cekKolom.length <= 0) {
            //     Swal.fire(
            //     'Gagal menyimpan!',
            //     'Tidak ada data untuk disimpan.',
            //     'error'
            //     )
            //     event.preventDefault();
            //     return false;
            // }

            event.preventDefault();
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
                    this.submit();
                }else{
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
                    })

                    Toast.fire({
                        icon: 'warning',
                        title: 'Batal Disimpan'
                    })
                    event.preventDefault();
                    // return;
                }
            })
        });
    });
</script>

<script>
    $(document).ready(function(){
         // master harga tipe
        var master = <?php echo json_encode($dataPengaturanKeuangan[0]); ?>;
            var uang = {
                'seal_pje': master.seal_pje,
                'seal_pelayaran': master.seal_pelayaran,
                'tally': master.tally,
                'plastik': master.plastik,
            };
        // end of master harga tipe
        $('#check_is_seal_pje').click(function(){
            if($(this).is(":checked")){
                $('#seal_pje').val(uang['seal_pje'].toLocaleString());
                // console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#seal_pje').val('');
                $('#seal_pje').attr('readonly',true);
                // console.log("Checkbox is unchecked.");
            }
            totalTarif();
        });
        
        $('#check_is_seal_pelayaran').click(function(){
            if($(this).is(":checked")){
                $('#seal_pelayaran').val(uang['seal_pelayaran'].toLocaleString());
            
                // console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#seal_pelayaran').val('');
                $('#seal_pelayaran').attr('readonly',true);
                // console.log("Checkbox is unchecked.");
            }
            totalTarif();
        });
        
        $('#check_is_tally').click(function(){
            if($(this).is(":checked")){
                $('#tally_pje').val(uang['tally'].toLocaleString());
                // $('#tally_pje').attr('readonly',false);
                // console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#tally_pje').val('');
                // $('#tally_pje').attr('readonly',true);
                // console.log("Checkbox is unchecked.");
            }
            totalTarif();
        });

        $('#check_is_plastik').click(function(){
            if($(this).is(":checked")){
                $('#plastik_pje').val(uang['plastik'].toLocaleString());
                $('#plastik_pje').attr('readonly',false);
                // console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#plastik_pje').val('');
                $('#plastik_pje').attr('readonly',true);
                // console.log("Checkbox is unchecked.");
            }
            totalTarif();
        });
        // deklarasi golbal id yg didelete
        var deleted_tujuan = [];
        var deleted_biaya = [];

        // logic get id row table
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
        //

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
                        <input style="margin: auto; display: block;" type="text" name="biaya[x_${j}][deskripsi]" class="form-control" id="deskripsi${j}">
                    </td>
                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                        <input type="hidden" name="biaya[x_${j}][biaya_id]" id="biaya_id${j}" class="form-control"/>
                        <input type="text" id="biaya${j}" name="biaya[x_${j}][biaya]" class="form-control numaja uang biaya hitungBiaya" />
                    </td>
                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                        <input type="text" id="catatan_biaya${j}" name="biaya[x_${j}][catatan]" class="form-control"/>
                    </td>
                    <td>
                        <button type="button" id="${j}" class="btn btn-danger btn_remove_biaya"><i class="fa fa-trash" aria-hidden="true"></i></button>
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

            $('#tujuan_id').val($('#tujuan_id_'+key).val());

            if($('#tarif_'+key).val() != ''){
                $('#tarif').val($('#tarif_'+key).val());
            }
            if($('#komisi_'+key).val() != ''){
                $('#komisi').val($('#komisi_'+key).val());
            }
            if($('#komisi_driver_hidden_'+key).val() != ''){
                $('#komisi_driver').val($('#komisi_driver_hidden_'+key).val());
            }
            
            if($('#alamat_hidden_'+key).val() != ''){
                $('#alamat').val($('#alamat_hidden_'+key).val());
            }

            if($('#pic_hidden_'+key).val() != ''){
                $('#pic').val($('#pic_hidden_'+key).val());
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
            if($('#seal_pje_hidden_'+key).val() != 0){
                $('#seal_pje').val($('#seal_pje_hidden_'+key).val());
                $('#check_is_seal_pje').prop('checked',true);
            }
            if($('#seal_pelayaran_hidden_'+key).val() != 0){
                $('#seal_pelayaran').val($('#seal_pelayaran_hidden_'+key).val());
                $('#check_is_seal_pelayaran').prop('checked',true);
            }

            if($('#tally_hidden_'+key).val() != 0){
                $('#tally_pje').val($('#tally_hidden_'+key).val());
                $('#check_is_tally').prop('checked',true);
                // $('#tally_pje').attr('readonly',false);
            }
            if($('#plastik_hidden_'+key).val() != 0){
                $('#plastik_pje').val($('#plastik_hidden_'+key).val());
                $('#check_is_plastik').prop('checked',true);
                // $('#plastik_pje').attr('readonly',false);
            }
            if($('#kargo_hidden_'+key).val() != ''){
                $('#kargo_pje').val($('#kargo_hidden_'+key).val());
            }
            
            // cek apakah ada isi detail biaya
            var cekBiaya = $('#obj_biaya'+key).val();
            if (cekBiaya) {
                // var jsonData = JSON.parse(jsonString);
                if(cekBiaya != null || cekBiaya != ''){
                    console.log('cekBiaya', cekBiaya);
                    JSON.parse(cekBiaya).forEach(function(item, index) {
                        $('#tabel_biaya > tbody:last-child').append(
                            `
                                <tr id="row_biaya${index}">
                                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                        <input type="hidden" name="biaya[${item.id}][biaya_id]" id="biaya_id${index}" value="${item.id}">
                                        <input style="margin: auto; display: block;" type="text" class="form-control" id="deskripsi${index}" name="biaya[${item.id}][deskripsi]" value="${item.deskripsi}">
                                    </td>
                                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                        <input type="text" id="biaya${index}" value="${item.biaya.toLocaleString()}" name="biaya[${item.id}][biaya]" class="form-control numaja uang hitungBiaya" />
                                    </td>
                                    <td style="padding: 5px; text-align: center; vertical-align: middle;">
                                        <input type="text" id="catatan_biaya${index}" name="biaya[${item.id}][catatan]" value="${item.catatan == 'null' || item.catatan == undefined? '':item.catatan}" class="form-control"/>
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
            totalTarif();
            $('input[type="text"]').on("input", function () {
                var inputValue = $(this).val();
                var uppercaseValue = inputValue.toUpperCase();
                $(this).val(uppercaseValue);
            });
            $('#modal_detail').modal('show');
        });

        // change total tarif
        $('#tarif').on('keyup', function(event){
            totalTarif();
        });

        function totalTarif(){
            var t_tarif_input = $('#tarif').val();
            var t_tarif_parsed = parseFloat(t_tarif_input.replace(/,/g, '')) || 0;

            var t_seal_pje_input = $('#seal_pje').val();
            var t_seal_pje_parsed = parseFloat(t_seal_pje_input.replace(/,/g, '')) || 0;

            var t_seal_pelayaran_input = $('#seal_pelayaran').val();
            var t_seal_pelayaran_parsed = parseFloat(t_seal_pelayaran_input.replace(/,/g, '')) || 0;

            var t_tally_input = $('#tally_pje').val();
            var t_tally_parsed = parseFloat(t_tally_input.replace(/,/g, '')) || 0;

            var t_plastik_input = $('#plastik_pje').val();
            var t_plastik_parsed = parseFloat(t_plastik_input.replace(/,/g, '')) || 0;
            
            var t_total = t_tarif_parsed + t_seal_pje_parsed + t_tally_parsed + t_plastik_parsed + t_seal_pelayaran_input;
            var formattedTotal = t_total.toLocaleString(); // Format with commas
            $('#total_tarif').val(formattedTotal);
        }
        
        // $('.hitungBiaya').on('keyup', function(event){
        //     // totalBiaya();
        // });
        $(document).on('keyup', '.hitungBiaya', function(){
            totalBiaya();
        });

        function totalBiaya(){
            var sum = 0;
            $('.hitungBiaya').each(function() {
                var t_biaya = parseFloat($(this).val().replace(/,/g, '')) || 0;
                sum += t_biaya;
            });
            $('#uang_jalan').val(sum.toLocaleString());
        }

        
        $(document).on('click', '.save_detail', function(event){
            var key=$('#key').val().trim();
            var namTuj = $('#nama_tujuan').val();
            var mrkId = $('#marketing').val();

            // console.log('namTuj '+namTuj+' mrkId '+mrkId);
            if(namTuj == '' || mrkId  == '' || mrkId == null){
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    text: 'Marketing dan Nama Tujuan wajib diisi!',
                })
                return false;
            }else{
                // simpan ke bawah
                if(key != ''){
                    var key=$('#key').val().trim();
                    var selJns = $("input[name='select_jenis_tujuan']:checked").val();
                    if(selJns == 'FTL'){
                        var cekTarif = $('#tarif').val();
                        // console.log("Tarif value:", cekTarif);
                        if(cekTarif == '' || cekTarif == null){
                            event.preventDefault(); // Prevent form submission
                            Swal.fire({
                                icon: 'error',
                                text: 'Tarif wajib diisi!',
                            })
                            return false;
                        }
                    }else{
                        var cekHrg = $('#harga_per_kg').val();
                        var cekMinMtn = $('#min_muatan').val();
                        if(cekHrg == '' || cekHrg == null || cekMinMtn == '' || cekMinMtn == null){
                            event.preventDefault(); // Prevent form submission
                            Swal.fire({
                                icon: 'error',
                                text: 'Harga/KG dan minimun muatan wajib diisi!',
                            })
                            return false;
                        }
                    }
                    //  var kargoID = $('#kargo_pje').val();    

                    // simpan ke tampilan depan
                    $('#tarif_'+key).val($('#tarif').val());
                    $('#nama_tujuan_'+key).val($('#nama_tujuan').val());
                    $('#uang_jalan_'+key).val(parseFloat($('#uang_jalan').val()));
                    $('#uang_jalan_hidden_'+key).val(escapeComma($('#uang_jalan').val()));
                    $('#komisi_'+key).val($('#komisi').val());
                    $('#komisi_driver_hidden_'+key).val($('#komisi_driver').val());
                    $('#catatan_'+key).val($('#catatan').val());
                    $('#alamat_hidden_'+key).val($('#alamat').val());
                    $('#jenis_tujuan_'+key).val(selJns);
                    $('#harga_per_kg_hidden_'+key).val($('#harga_per_kg').val());
                    $('#min_muatan_hidden_'+key).val($('#min_muatan').val());
                    $('#grup_hidden_'+key).val($('#grup').val());
                    $('#marketing_hidden_'+key).val($('#marketing').val());
                    $('#seal_pje_hidden_'+key).val($('#seal_pje').val());
                    $('#seal_pelayaran_hidden_'+key).val($('#seal_pelayaran').val());
                    $('#tally_hidden_'+key).val($('#tally_pje').val());
                    $('#plastik_hidden_'+key).val($('#plastik_pje').val());
                    $('#kargo_hidden_'+key).val($('#kargo_pje').val());
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
        
                            $('#obj_biaya'+key).val(JSON.stringify(array_detail_biaya));
                        });
                        // ini ngitung semua uangnya
                        $('#uang_jalan_'+key).val(total_biaya.toLocaleString());
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

                    var selJns = $("input[name='select_jenis_tujuan']:checked").val();
                    if(selJns == 'FTL'){
                        var cekTarif = $('#tarif').val();
                        // console.log("Tarif value:", cekTarif);
                        if(cekTarif == '' || cekTarif == null){
                            event.preventDefault(); // Prevent form submission
                            Swal.fire({
                                icon: 'error',
                                text: 'Tarif wajib diisi!',
                            })
                            return false;
                        }
                    }else{
                        var cekHrg = $('#harga_per_kg').val();
                        var cekMinMtn = $('#min_muatan').val();
                        if(cekHrg == '' || cekHrg == null || cekMinMtn == '' || cekMinMtn == null){
                            event.preventDefault(); // Prevent form submission
                            Swal.fire({
                                icon: 'error',
                                text: 'Harga/KG dan minimun muatan wajib diisi!',
                            })
                            return false;
                        }
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
                                <input value="${total_biaya.toLocaleString()}" name="data[tujuan][${i}][uang_jalan]" id="uang_jalan_${i}" class="form-control numaja uang uangJalan" type="text" readonly/>
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
                            <input value="${escapeComma($('#komisi').val())}" name="data[tujuan][${i}][komisi_hidden]" id="komisi_hidden_${i}" type="hidden" >
                            <input value="${$('#komisi_driver').val()}" type="hidden" name="data[tujuan][${i}][komisi_driver_hidden]" id="komisi_driver_hidden_${i}" >
                            <input value="${$('#harga_per_kg').val()}" name="data[tujuan][${i}][harga_per_kg_hidden]" id="harga_per_kg_hidden_${i}" type="hidden" >
                            <input value="${$('#min_muatan').val()}" name="data[tujuan][${i}][min_muatan_hidden]" id="min_muatan_hidden_${i}" type="hidden" >
                            <input value="${$('#grup').val()}" name="data[tujuan][${i}][grup_hidden]" id="grup_hidden_${i}" type="hidden"  placeholder="">
                            <input value="${$('#marketing').val()}" name="data[tujuan][${i}][marketing_hidden]" id="marketing_hidden_${i}" type="hidden" placeholder="">
                            <input value="${escapeComma($('#seal_pje').val())}" type="hidden" name="data[tujuan][${i}][seal_pje_hidden]" id="seal_pje_hidden_${i}" placeholder="">
                            <input value="${escapeComma($('#seal_pelayaran').val())}" type="hidden" name="data[tujuan][${i}][seal_pelayaran_hidden]" id="seal_pelayaran_hidden_${i}" placeholder="">
                            <input value="${escapeComma($('#tally_pje').val())}" type="hidden" name="data[tujuan][${i}][tally_hidden]" id="tally_hidden_${i}" placeholder="">
                            <input value="${escapeComma($('#plastik_pje').val())}" type="hidden" name="data[tujuan][${i}][plastik_hidden]" id="plastik_hidden_${i}" placeholder="">
                            <input value="${$('#kargo_pje').val()}" type="hidden" name="data[tujuan][${i}][kargo_hidden]" id="kargo_hidden_${i}" >
                            <input value='${JSON.stringify(array_detail_biaya)}' name="data[tujuan][${i}][obj_biaya]" id="obj_biaya${i}" type="hidden" placeholder="">
                            <td><button type="button" name="remove" id="${i}" class="btn btn-danger btn_remove"><i class="fa fa-trash" aria-hidden="true"></i></button></td></tr>);  
                        </tr>
                    `;

                    $('#dynamic_field > tbody:last-child').append(newRow);
                
                }
            }
            // clear biar ga nyantol data lama
            $('#tabel_biaya tbody').html('');
            $('#deleted_biaya').val($('#deleted_biaya_temp').val());
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
            $('#delete_tujuan').val('');
            $('#jenis').val('');
            $('#nama_tujuan').val('');
            $('#alamat').val('');
            $('#tarif').val('');
            $('#uang_jalan').val('');
            $('#komisi').val('');
            $('#komisi_driver').val('');
            $('#catatan').val('');
            $('#seal_pje').val('');
            $('#seal_pelayaran').val('');
            $('#tally_pje').val('');
            $('#plastik_pje').val('');
            $('#total_tarif').val('');
            $('#tujuan_id').val('');
            $('#pic').val('');
            $('#delete_biaya').val('');
            $('#kargo_pje').val('');
            $('#check_is_seal_pje').prop('checked',false);
            $('#check_is_seal_pelayaran').prop('checked',false);
            $('#check_is_tally').prop('checked',false);
            $('#check_is_plastik').prop('checked',false);
            $('#seal_pje').attr('readonly',true);
            $('#seal_pelayaran').attr('readonly',true);
        }
        
        $(document).on('click', '.btn_remove_biaya', function(){  
            var button_id = $(this).attr("id");

            // get id yg dihapus
            var row = $(this).closest("tr");
            // var biayaIdInput = row.find("input[name='biaya_id"+button_id+"']");
            var biayaIdInput = row.find("input[id]");
            var biaya_id_value = biayaIdInput.val();

            // push ke array global
            if(biaya_id_value) {
                deleted_biaya.push(biaya_id_value);
                $("#delete_biaya").val(deleted_biaya.join(","));
                // $("#deleted_biaya_temp").val(deleted_biaya.join(","));
            }
            $('#row_biaya'+button_id+'').remove();  
            totalBiaya();
        });


        $(document).on('click', '.btn_remove', function(){  
            clearModal(); // clear dulu data sebelum open modal, baru get data ( biar clean )
            var button_id = $(this).attr("id");             
            var row = $(this).closest("tr");
            var hiddenInput = row.find("input[name^='data['][name$='[id_tujuan]']");
            var deleted = hiddenInput.val();
            $('#delete_tujuan').val(deleted);

            $('input[type="text"]').on("input", function () {
                var inputValue = $(this).val();
                var uppercaseValue = inputValue.toUpperCase();
                $(this).val(uppercaseValue);
            });

            $('#modal_hapus').modal('show');
            // // get id button
            // var button_id = $(this).attr("id");             
            
            // // get id yg dihapus
            // var row = $(this).closest("tr");
            // var hiddenInput = row.find("input[name^='data['][name$='[id_tujuan]']");
            // var deleted = hiddenInput.val();

            // // push ke array global
            // if(deleted_tujuan) {
            //     deleted_tujuan.push(deleted);
            //     $("#deleted_tujuan").val(deleted_tujuan.join(","));
            // }

            // // remove dari tabel
            // $('#row'+button_id+'').remove();  
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
    });
		
</script>

@endsection
