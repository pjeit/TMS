@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')

@endsection

@section('content')
@include('sweetalert::alert')

<style>

.besarin_gambar {
    transform: scale(3.5);
    transition: transform 0.5s ease; /* Adjust the transition duration and easing as needed */
}
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
                    <table id="tabel_dokumen" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Tanggal Pembayaran</th>
                                <th>Kas</th>
                                <th>Jenis Dokumen</th>
                                <th>Nominal Pembayaran</th>
                                <th>Catatan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($dataPembayaranKendaraan))
                                @foreach ($dataPembayaranKendaraan as $item)
                                <tr>
                                    <td>{{ date("d-M-Y", strtotime($item->tanggal_bayar))}}</td>
                                    <td>{{ $item->kas_dokumen_bayar->nama }}</td>
                                    <td>{{ $item->jenis_dokumen }}</td>
                                    <td>Rp. {{number_format($item->nominal_bayar)  }} </td>
                                    <td>{{ $item->catatan }}</td>
                                    <td>
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{route('pembayaran_dokumen_kendaraan.edit',[$item->id])}}" class="dropdown-item ">
                                                    <span class="fas fa-edit mr-3"></span> Edit
                                                </a>
                                                <a href="{{ route('pembayaran_dokumen_kendaraan.destroy', [$item->id]) }}" class="dropdown-item" data-confirm-delete="true">
                                                    <span class="fas fa-trash mr-3"></span> Hapus
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
<div class="modal fade" id="modal" >
        <div class="modal-dialog modal-lg ">
            <form action="{{ route('pembayaran_dokumen_kendaraan.store') }}" id="post_data" method="POST" enctype="multipart/form-data">
            @csrf
                <div class="modal-content radiusSendiri">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        </div>
                    <div class="modal-body">
                
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
                            {{-- data --}}
                            <div class='row'>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label for="tanggal_pembayaran">Tanggal Pembayaran<span style="color:red">*</span></label>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" autocomplete="off" name="tanggal_pembayaran" class="form-control date @error('tanggal_pembayaran') is-invalid @enderror" id="tanggal_pembayaran" placeholder="dd-M-yyyy" value="{{old('tanggal_pembayaran',date('d-M-Y'))}}">
                                                @error('tanggal_pembayaran')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label for="total_nominal">Total Nominal Pembayaran<span style="color:red">*</span></label>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="text" name="total_nominal" class="form-control numaja uang @error('total_nominal') is-invalid @enderror" id="total_nominal" placeholder="" value="{{old('total_nominal')}}" readonly>
                                                @error('total_nominal')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Jenis Dokumen<span class="text-red">*</span></label>
                                            <select class="form-control select2  @error('select_dokumen') is-invalid @enderror" style="width: 100%;" id='select_dokumen' name="select_dokumen">
                                                <option value="STNK" {{old('select_dokumen')=='STNK'?'selected':''}}>STNK</option>
                                                <option value="KIR" {{old('select_dokumen')=='KIR'?'selected':''}}>KIR</option>
                                                <option value="PAJAK" {{old('select_dokumen')=='PAJAK'?'selected':''}}>PAJAK</option>
                                            </select>
                                            @error('select_dokumen')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror  
                                        </div> 
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label for="pembayaran">Pilih Kas/Bank</label>
                                            <select class="form-control select2" name="pembayaran" id="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                                @foreach ($dataKas as $kb)
                                                    <option value="{{$kb->id}}"  >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                            <label for="">Catatan Pembayaran</label>
                                            <input type="text" class="form-control catatan_pembayaran @error('catatan_pembayaran') is-invalid @enderror" id="catatan_pembayaran" name="catatan_pembayaran" value="{{old('catatan_pembayaran')}}">
                                            @error('catatan_pembayaran')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>  
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <input type="hidden" id="maxID" value="0">
                                    <button class="btn btn-primary radiusSendiri mt-2 mb-2" type="button" id="btn_tambah">
                                        <i class="fa fa-plus-circle"> </i> Tambah Kendaraan
                                    </button>
                                    <table class="table table-bordered" id="tabel_kendaraan_parent">
                                        <thead>
                                            <tr>
                                                <th>Kendaraan</th>
                                                <th>Nominal</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabel_kendaraan">
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- end data --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger radiusSendiri p-2" style='width:85px' data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-success radiusSendiri p-2" id="" style='width:85px'><i class="fa fa-fw fa-save"></i> Simpan</button> 
                    </div>
                </div>
            </form> 
        </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    $('#tabel_dokumen').DataTable({
                    // order: [
                    //     [0, 'asc'],
                    // ],
                    // rowGroup: {
                    //     dataSrc: [0] // kalau mau grouping pake ini
                    // },
                    columnDefs: [
                        // {
                        //     targets: [0],
                        //     visible: false
                        // },
                        // { orderable: true, targets: 0 }, // Enable ordering for the first column (index 0)
                        { orderable: false, targets: '_all' } // Disable ordering for all other columns
                    ],
                    info: false,
                    searching: true,
                    paging: true,
                    language: {
                        emptyTable: "Data tidak ditemukan."
                    }
        });
    $('#tanggal_pembayaran').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            startDate: "0d"
        });
    var Toast = Swal.mixin({
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
    
   
    //=======buat buka modal=========
    $(".bukakModalCreate").click(function () {
        $("#modal").modal("show");
    });
    var cekerror= <?php echo json_encode($errors->any()); ?>;
    
    if (cekerror) {
            $("#modal").modal("show");
        
    }
    //======= end buat buka modal=========

    $('body').on('click','#btn_tambah',function()
    {
        var maxID = $('#maxID').val();
        $('#tabel_kendaraan').append(
            `
                <tr id="${maxID}">
                    <td>
                        <div class="form-group">
                            <select class="form-control select_kendaraan select2 @error('select_kendaraan') is-invalid @enderror" style="width: 100%;" id='select_kendaraan_${maxID}' name="kendaraan[${maxID}][select_kendaraan]">
                                <option value="">Pilih Kendaraan</option>
                                @foreach ($dataKendaraan as $kendaraan)
                                    <option value="{{$kendaraan->kendaraanId}}"
                                        noPol='{{$kendaraan->no_polisi}}'
                                        kategoriKendaraan='{{$kendaraan->kategoriKendaraan}}'
                                        tipeKontainerKendaraanDariChassis = '{{$kendaraan->tipeKontainerKendaraanDariChassis}}'
                                        id_counter = '${maxID}'
                                        >{{ $kendaraan->no_polisi }} ({{$kendaraan->kategoriKendaraan}})</option>
                                @endforeach
                            </select>
                            @error('select_kendaraan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <input type="hidden" id="no_polisi_${maxID}" name="kendaraan[${maxID}][no_polisi]" value="" placeholder="no_polisi">
                        </div>  
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control nominal numaja uang @error('nominal') is-invalid @enderror" id="nominal_${maxID}" name="kendaraan[${maxID}][nominal]" value="{{old('nominal')}}">
                            @error('nominal')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>  
                    </td>
                    <td align="center" class="text-danger"><button type="button" data-toggle="tooltip" data-placement="right" title="Click To Remove"  class="btn btn-danger radiusSendiri btnDelete"><i class="fa fa-fw fa-trash-alt"></i></button></td>
                </tr>
            `
        )
        $('.select2').select2();
        maxID++;
        $('#maxID').val(maxID);
    });
    $(document).on('click','.btnDelete',function(){
        var maxID = $('#maxID').val();
        $(this).closest('tr').remove();
        hitungTotal();

        if($(this).closest('tr').attr('id') == maxID)
        {
            maxID--;
        }
        $('#maxID').val(maxID);
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
            icon: 'success',
            title: 'Data dihapus'
        })
        // cekCheckbox();
    });

  
    $('body').on('change','.select_kendaraan',function()
    {
        var idKendaraan = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var nopol = selectedOption.attr('noPol');
        var id_counter = selectedOption.attr('id_counter');
        $('#no_polisi_'+id_counter).val(nopol);

    });
    $('body').on('change','#select_mekanik',function()
    {
        var selectedOption = $(this).find('option:selected');
        var nama_driver = selectedOption.attr('nama_driver');

    });
    $( document ).on( 'keyup', '.nominal', function (event) {
                hitungTotal();
    });
    function hitungTotal(){
     
        var nominal = 0;
        $('.nominal').each(function () {
            var value_nominal = $(this).val() ? parseFloat($(this).val().replace(/,/g, '')) : 0;
            nominal += value_nominal;
        });
        var total = parseFloat(nominal);
        $('#total_nominal').val(total.toLocaleString());
    }
   
    $('#post_data').submit(function(event) {
            
        if($("#tanggal_pembayaran").val()=='')
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `TANGGAL PEMBAYARAN BELUM DIISI!`,
            })
            
            return;
        }
        
        if($("#total_nominal").val()=='' || normalize($("#total_nominal").val())==0)
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `Total pembayaran harus diisi`,
            })
            
            return;
        }
        
        let barisTabel = $("#tabel_kendaraan_parent > tbody tr");
        console.log(barisTabel.length + 'baris tabel');
        if (barisTabel.length == 0) {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `Detail kendaraan Tidak boleh Kosong!`,
            })
            return;
            
        }
        var flagError = false;
        for (var i = 0; i < $(".catatan").length; i++) {
            var indexFoto = $(".catatan").eq(i);
            var row = indexFoto.closest('tr');
            var select_kendaraan=row.find('.select_kendaraan').val();

            
            if(select_kendaraan=="")
            {
                flagError = true;
                break; 
            }

        }
        if (flagError) {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `Kendaraan tidak boleh kosong`,
            })
            return;
            
        }
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
                }, 20); // 2000 milliseconds = 2 seconds
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


