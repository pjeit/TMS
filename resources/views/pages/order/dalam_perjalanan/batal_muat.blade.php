
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
    <form action="{{ route('dalam_perjalanan.save_batal_muat', [ $data['id_sewa'] ]) }}" method="POST" >
        @csrf
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('dalam_perjalanan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                <button type='submit' class="btn btn-success radiusSendiri"><i class="fa fa-save"></i> Simpan</button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="select_sewa">No. Sewa</label>
                            <input type="text" class="form-control" name="no_sewa" value="{{ $data['no_sewa'] }} ({{ date('d-M-Y', strtotime($data['tanggal_berangkat'])) }})" readonly>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="customer">Customer</label>
                            <input type="text" class="form-control" id="customer" readonly="" value="[{{ $data->getCustomer->kode }}] - {{ $data->getCustomer->nama }}">
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <div class="form-group">
                            <label for="tujuan">Tujuan</label>
                            <input type="text" class="form-control" id="tujuan" readonly="" value="{{ $data->nama_tujuan }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="no_kontainer">No. Kontainer<span style="color:red">*</span></label>
                            <input type="text" required name="no_kontainer" class="form-control" id="no_kontainer" placeholder="" value="{{ $data['no_kontainer'] }}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="no_surat_jalan">No. Surat Jalan<span style="color:red">*</span></label>
                            <input type="text" required name="no_surat_jalan" class="form-control" id="no_surat_jalan" placeholder="" value="{{ $data['no_surat_jalan'] }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="tanggal_cancel">Tanggal Batal Muat<span style="color:red">*</span></label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" autocomplete="off" placeholder="dd-M-yyyy" class="form-control date" id="tanggal_cancel" name="tanggal_cancel" value="">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="form-group">
                            <label for="tanggal_kembali">Tgl. Kembali<span style="color:red">*</span></label>
                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" autocomplete="off" name="tanggal_kembali" class="form-control date" id="tanggal_kembali" placeholder="dd-M-yyyy" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7 col-md-7 col-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-4 col-md-4 col-lg-4">
                                            <label for="kendaraan">Kendaraan</label>
                                            <input type="text" class="form-control" name="kendaraan" readonly="" value="{{ $data['no_polisi'] }}">
                                        </div>
                                        <div class="col-8 col-md-8 col-lg-8">
                                            <label for="driver">Driver</label>
                                            <input type="text" class="form-control" name="driver" readonly="" value="{{ $data['nama_driver'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="kas_bank_id">Kas / Bank<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='kasbank' name="kasbank" required>
                                        @foreach ($kasbank as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="kas_bank_id">Jenis<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='jenis' name="jenis" required>
                                        <option value="">── PILIH JENIS ──</option>
                                        <option value="BELUM TRANSFER">BELUM TRANSFER</option>
                                        <option value="SUDAH TRANSFER">SUDAH TRANSFER</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 col-lg-6">
                                <div class="form-group">
                                    <label for="">Total Tarif</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input readonly="" type="text" name="total_tarif" class="form-control numaja uang" id="total_tarif" placeholder="" value="{{ number_format($data['total_tarif']) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-6">
                                <div class="form-group">
                                    <label for="">Total Tarif Yang Ditagihkan<span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input autocomplete="off" type="text" required name="total_tarif_tagih" class="form-control numaja uang" id="total_tarif_tagih" placeholder="" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="total_uang_jalan">Total Uang Jalan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input readonly="" type="text" name="total_uang_jalan" class="form-control numaja uang" id="total_uang_jalan" placeholder="" value="{{ number_format($data['total_uang_jalan']) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="total_uang_jalan_kembali">Total Uang Jalan Kembali<span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input autocomplete="off" type="text" required name="total_uang_jalan_kembali" class="form-control numaja uang" id="total_uang_jalan_kembali" placeholder="" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="total_uang_jalan">Riwayat Potong Hutang</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input readonly="" type="text" name="riwayat_potong_hutang" class="form-control numaja uang" id="riwayat_potong_hutang" placeholder="" value="{{ number_format($riwayatPotongHutang['potong_hutang']) }}">
                                        <input type="hidden" name="id_riwayat_pot_hutang" value="{{ $riwayatPotongHutang['id'] }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="total_uang_jalan_kembali">Potong Hutang Dikembalikan<span style="color: red;">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input autocomplete="off" type="text" required name="potong_hutang_dikembalikan" class="form-control numaja uang" id="potong_hutang_dikembalikan" placeholder="" value="">
                                    </div>
                                </div>
                            </div>
                          
                          
                         
                        </div>
                    </div>

                    <div class="col-lg-5 col-md-5 col-12">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <label for="alasan_cancel">Alasan Batal Muat<span style="color: red;">*</span></label>
                                    <textarea name="alasan_cancel" required class="form-control" id="alasan_cancel" rows="15" value=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
           
                </div>

            </div>
        </div> 
     
    </form>
</div>


         
 
<script type="text/javascript">
    $(document).ready(function() {
        var today = new Date();
        // var tomorrow = new Date(today);
        // tomorrow.setDate(today.getDate() + 1);

        $('#tanggal_cancel').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            // startDate: today,
        }).datepicker("setDate", today);
        $('#tanggal_kembali').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            // startDate: today,
        }).datepicker("setDate", today);

        $(document).on('keyup', '#total_tarif_tagih', function(){ 
            var total_tarif = $('#total_tarif').val();
            if(parseFloat(escapeComma(this.value)) > parseFloat(escapeComma(total_tarif))){
                console.log('total_tarif', total_tarif);
                $('#total_tarif_tagih').val(total_tarif);
            }
            $(document).on('focusout', '#total_tarif_tagih', function(){ 
                check();
            });
        });

        $(document).on('keyup', '#total_uang_jalan_kembali', function(){ 
            var total_uang_jalan = $('#total_uang_jalan').val();
            if(parseFloat(escapeComma(this.value)) > parseFloat(escapeComma(total_uang_jalan))){
                $('#total_uang_jalan_kembali').val(total_uang_jalan);
            }
            $(document).on('focusout', '#total_uang_jalan_kembali', function(){ 
                check();
            });
        });
        function check(){
            var total_tarif = parseFloat(escapeComma($('#total_tarif').val()));
            var total_tarif_tagih = parseFloat(escapeComma($('#total_tarif_tagih').val()));
            var total_uang_jalan = parseFloat(escapeComma($('#total_uang_jalan').val()));
            var total_uang_jalan_kembali = parseFloat(escapeComma($('#total_uang_jalan_kembali').val()));
            if(total_tarif_tagih > total_tarif){
                $('#total_tarif_tagih').val(moneyMask(total_tarif));
            }
            if(total_uang_jalan_kembali > total_uang_jalan){
                $('#total_uang_jalan_kembali').val(moneyMask(total_uang_jalan));
            }
        }
    });
</script>

@endsection


