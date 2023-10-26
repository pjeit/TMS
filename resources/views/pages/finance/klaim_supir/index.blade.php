@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('karyawan.index')}}">Karyawan</a></li>
@endsection

@section('content')
@include('sweetalert::alert')

<style>
#preview_foto_nota {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}

#preview_foto_nota:hover {
  transform: scale(3.5); /* Increase the scale factor to make it bigger on hover */
  z-index: 10;

}

#preview_foto_barang {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}

#preview_foto_barang:hover {
  transform: scale(3.5); /* Increase the scale factor to make it bigger on hover */
  z-index: 10;
}
/* #preview_foto_barang:hover
{
  transform: scale(5);
  border-radius: 5px;
  padding: auto;
  margin: auto;
} */
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">

                    <button class="btn btn-primary btn-responsive float-left radiusSendiri bukakModalCreate">
                        <i class="fa fa-plus-circle"> </i> Tambah Data

                    </button>
                    {{-- <a href="{{route('karyawan.create')}}" >
                    </a>  --}}
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="TabelKlaim" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Supir</th>
                                <th>Jenis Klaim</th>
                                <th>Tanggal Klaim</th>
                                <th>Jumlah Klaim</th>
                                <th>Status Klaim</th>
                                <th>Keterangan</th>
                                <th></th>
                            </tr>
                          </thead>
                        <tbody>
                            @if (isset($dataKlaimSupir))
                                @foreach ($dataKlaimSupir as $item)
                                <tr>
                                    <td>{{ $item->nama_supir}} ({{ $item->telp}})</td>
                                    <td>{{ $item->jenis_klaim}}</td>
                                    <td>{{ $item->tanggal_klaim }}</td>
                                    <td>{{ $item->total_klaim }}</td>
                                    <td>{{ $item->status_klaim }}</td>
                                    <td>{{ $item->keterangan_klaim }}</td>
                                    <td>
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{route('customer.edit',[$item->id])}}" class="dropdown-item ">
                                                    <span class="fas fa-edit mr-3"></span> Edit
                                                </a>
                                                <a href="{{ route('customer.destroy', $item->id) }}" class="dropdown-item" data-confirm-delete="true">
                                                    <span class="fas fa-trash mr-3"></span> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                           
                        </tbody>
                        
                    </table>
                  

                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
<div class="modal fade" id="modal" tabindex='-1'>
        <div class="modal-dialog modal-lg ">
            <div class="modal-content radiusSendiri">
                <div class="modal-header">
                <h5 class="modal-title">Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3 mt-3 nav-fill" id="justifyTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link nav-link-tab active" id="justify-data-tab" data-toggle="tab" href="#justify-data" role="tab" aria-controls="justify-data" aria-selected="true">Data</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-tab" id="justify-foto-tab" data-toggle="tab" href="#justify-foto" role="tab" aria-controls="justify-foto" aria-selected="false">Foto</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                    {{-- data --}}

                        <div class="tab-pane fade show active" id="justify-data" role="tabpanel" aria-labelledby="justify-data-tab">
                            <form action="{{ route('klaim_supir.store') }}" id="post_data" method="POST" >
                                <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}
                                <div class='row'>
                                    <div class="col-lg-12">
                                        <div class="row">
                                
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                <label for="tanggal_klaim">Tanggal Klaim<span style="color:red">*</span></label>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                    </div>
                                                    <input type="text" autocomplete="off" name="tanggal_klaim" class="form-control date" id="tanggal_klaim" placeholder="dd-M-yyyy" value="">
                                                </div>
                                                
                                            </div>

                                            
                                        </div>
                                        
                                        <div class="row">

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label for="select_kendaraan">Kendaraan<span style="color:red">*</span></label>
                                                <select class="form-control select2" style="width: 100%;" id='select_kendaraan' name="select_kendaraan" required>
                                                    <option value="">Pilih Kendaraan</option>

                                                    @foreach ($dataKendaraan as $kendaraan)
                                                    
                                                        <option value="{{$kendaraan->kendaraanId}}"
                                                            idChassis='{{$kendaraan->chassisId}}'
                                                            noPol='{{$kendaraan->no_polisi}}'
                                                            idDriver='{{$kendaraan->driver_id}}'
                                                            kategoriKendaraan='{{$kendaraan->kategoriKendaraan}}'
                                                            tipeKontainerKendaraanDariChassis = '{{$kendaraan->tipeKontainerKendaraanDariChassis}}'
                                                            >{{ $kendaraan->no_polisi }} ({{$kendaraan->kategoriKendaraan}})</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" id="kendaraan_id" name="kendaraan_id" value="" placeholder="kendaraan_id">
                                                <input type="hidden" id="no_polisi" name="no_polisi" value="" placeholder="no_polisi">
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label for="select_driver">Driver<span style="color:red">*</span></label>
                                                    <select class="form-control select2" style="width: 100%;" id='select_driver' name="select_driver" required>
                                                    <option value="">Pilih Driver</option>
                                                    @foreach ($dataDriver as $drvr)
                                                        <option value="{{$drvr->id}}" nama_driver="{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})">{{ $drvr->nama_panggilan }} - ({{ $drvr->telp1 }})</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" id="driver_nama" name="driver_nama" value="" placeholder="driver_nama">
                                            </div>

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label for="">Jenis Klaim<span class="text-red">*</span></label>
                                                <select class="form-control select2" style="width: 100%;" id='select_klaim' name="select_klaim">
                                                    <option value="">Pilih Jenis Klaim</option>
                                                    <option value="Ban">Ban</option>
                                                    <option value="CuciMobil">Cuci Mobil</option>
                                                    <option value="Sparepart">Sparepart</option>
                                                    <option value="Tol">Tol</option>
                                                    <option value="Lainlain">Lain-lain</option>

                                                    {{-- @foreach ($datajO as $jo)
                                                        <option value="{{$jo->id}}-{{$jo->id_customer}}">{{ $jo->no_bl }} / {{ $jo->getCustomer->kode }} / {{ $jo->getSupplier->nama }}</option>
                                                    @endforeach --}}
                                                </select>
                                            </div> 

                                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                <label for="total_reimburse">Total Klaim <span style="color:red">*</span></label>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" name="total_klaim" class="form-control numaja uang" id="total_klaim" placeholder="" value="">
                                                </div>
                                            </div>  
                                            
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                                <label for="keterangan_klaim">Keterangan Klaim</label>
                                                <input type="text" class="form-control" id="keterangan_klaim">
                                            </div>  
                                        </div>
                                    </div>

                                </div>
                            </form> 
                        </div>
                    {{-- end data --}}

                    {{-- foto --}}
                        <div class="tab-pane fade" id="justify-foto" role="tabpanel" aria-labelledby="justify-foto-tab">
                            <div class="row">
                                <div class=" col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group text-center">
                                        <a href="#" class="pop">
                                            <img src="{{asset('img/gambar_add.png')}}" class="img-fluid" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_nota">
                                        </a>
                                    </div>
                                    <div class="custom-file text-center" name="div_foto_nota" id="div_foto_nota">
                                        <input type="file" class="custom-file-input form-control" id="foto_nota" name="foto_nota" accept="image/jpeg" value="" hidden="">
                                        <label class="btn btn-outline-primary radiusSendiri" for="foto_nota" style="text-align: center">Pilih Foto Nota</label>
                                    </div>
                                </div>
                                <div class=" col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group text-center">
                                        <a href="#" class="pop">
                                            <img src="{{asset('img/gambar_add.png')}}" class="img-fluid" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_barang">
                                        </a>
                                    </div>
                                    <div class="custom-file text-center" name="div_foto_barang" id="div_foto_barang">
                                        <input type="file" class="custom-file-input form-control" id="foto_barang" name="foto_barang" accept="image/jpeg" value="" hidden="">
                                        <label class="btn btn-outline-primary radiusSendiri" for="foto_barang" style="text-align: center">Pilih Foto Barang</label>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    {{-- end foto --}}
                    
                    </div>
                    
                </div>
                <div class="modal-footer">
                   
                    <button type="button" class="btn btn-sm btn-danger radiusSendiri" style='width:85px' data-dismiss="modal">BATAL</button>
                    <button type="button" class="btn btn-sm btn-success radiusSendiri" id="" style='width:85px'>OK</button> 
                </div>
            </div>
        </div>
</div>
<script type="text/javascript">
$(document).ready(function () {

    $('#tanggal_klaim').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
        });
    function readURLNota(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#preview_foto_nota').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }
    function readURLBarang(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#preview_foto_barang').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }
    $('#div_foto_nota').show();
    $('#preview_foto_nota').hover(
        function () {
            $('#div_foto_nota').hide();
        },
        function () {
            $('#div_foto_nota').show();
        }
    );

    $('#div_foto_barang').show();
    $('#preview_foto_barang').hover(
        function () {
            $('#div_foto_barang').hide();
        },
        function () {
            $('#div_foto_barang').show();
        }
    );

    
    $("#foto_barang").change(function() {
        readURLBarang(this);
    });
    $("#foto_nota").change(function() {
        readURLNota(this);
    });
    $(".bukakModalCreate").click(function () {
            $("#modal").modal("show");
        });
    $('body').on('change','#select_kendaraan',function()
    {
        var idKendaraan = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var idChassis = selectedOption.attr('idChassis');
        var nopol = selectedOption.attr('noPol');
        var supir = selectedOption.attr('idDriver');
        
        $('#kendaraan_id').val(idKendaraan);
        $('#no_polisi').val(nopol);
        
        $('#select_chassis').val(idChassis).trigger('change');
        $('#select_driver').val(supir).trigger('change');

    });
    new DataTable('#TabelKlaim', {
        order: [
            [0, 'asc'],
        ],
        rowGroup: {
            dataSrc: [0]
        },
        columnDefs: [
            {
                targets: [0],
                visible: false
            },
            {
                "orderable": false,
                "targets": [0,1,2,3,4,5,6]
            }
    
        ],
    }); 
});
</script>
@endsection


