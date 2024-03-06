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
#preview_foto_nota {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}

#preview_foto_barang {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
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
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <button class="btn btn-primary btn-responsive float-left radiusSendiri" data-toggle="modal" data-target="#modal_tambah">
                        <i class="fa fa-plus-circle"> </i> Tambah Data
                    </button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="datatable" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Telp</th>
                                <th>Posisi</th>
                                <th>Tgl. Bergabung</th>
                                <th>Total Hutang</th>
                                <th></th>
                            </tr>
                          </thead>
                        <tbody>
                            @if (isset($dataKaryawanHutang))
                                @foreach ($dataKaryawanHutang as $item)
                                <tr>
                                    <td>{{$item->nama_panggilan}}</td>
                                    <td>{{$item->telp1}}</td>
                                    <td>{{$item->namaPosisi}}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggalBergabung)->format('d-M-Y')}}</td>
                                    <td>Rp. {{number_format($item->total_hutang)}}</td>
                                    <td>
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{route('karyawan_hutang.edit',[$item->idKaryawan])}}" class="dropdown-item ">
                                                    <span class="fas fa-edit mr-3"></span> Detail
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
<div class="modal fade" id="modal_tambah" >
        <div class="modal-dialog modal-lg ">
             <form action="{{ route('karyawan_hutang.store') }}" id="post_data" method="POST" >
              @csrf
                <div class="modal-content radiusSendiri">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Data</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                         <div class='row'>
                            <div class='col-lg-12 col-md-12 col-12'>
                                <input type="hidden" name="dariIndex" value="{{$dariIndex}}">
                                {{-- <div class="form-group">
                                    <label>Jenis Transaksi</label>
                                    <div class='row'>
                                        <div class="col-6">
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" id="jenis_hutang" name="jenis" value='Hutang' checked>
                                                <label style='font-weight:normal' for="jenis_hutang" class="custom-control-label">Kas Bon / Hutang</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" value='Bayar' id="jenis_bayar" name="jenis">
                                                <label style='font-weight:normal' for="jenis_bayar" class="custom-control-label">Bayar Hutang / Cicilan</label>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                 <div class="form-group">
                                    <label for="tipe">Jenis Transaksi</label>
                                    <br>
                                    <div class="icheck-primary d-inline">
                                        <input id="jenis_hutang" type="radio" name="jenis" value="HUTANG" checked>
                                        <label class="form-check-label" for="jenis_hutang">Kas Bon / Hutang</label>
                                    </div>
                                    <div class="icheck-primary d-inline ml-4">
                                        <input id="jenis_bayar" type="radio" name="jenis" value="BAYAR" >
                                        <label class="form-check-label" for="jenis_bayar">Bayar Hutang / Cicilan</label>
                                    </div>
                                 
                                </div>  
                                <div class="form-group">
                                    <label for="tanggal">Tanggal Transaksi<span style='color:red'>*</span></label>
                                    <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" name="tanggal" class="date form-control" id="tanggal" autocomplete="off" placeholder="dd-M-yyyy" value="" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class='row'>
                                        {{-- <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="karyawan_id">Karyawan<span style='color:red'>*</span></label>
                                            <select id="select_karyawan" style="width:100%" data-placeholder="Pilih Karyawan">
                                                <option value=''></option>
                                            </select>
                                            <input type='hidden' id='karyawan_id' name='karyawan_id' value="">
                                        </div> --}}
                                        <div class="form-group col-6 col-md-6 col-lg-6">
                                            <label for="select_karyawan">Karyawan<span style="color:red">*</span></label>
                                                <select class="form-control select2  @error('select_karyawan') is-invalid @enderror" style="width: 100%;" id='select_karyawan' name="select_karyawan">
                                                <option value="">Pilih Karyawan</option>
                                                @foreach ($dataKaryawanHutang as $data)
                                                    <option 
                                                    value="{{$data->idKaryawan}}" 
                                                    {{old('select_karyawan')==$data->id?'selected':''}} 
                                                    karyawan_hutang = "{{$data->total_hutang}}"
                                                    >{{ $data->nama_panggilan }} ({{$data->namaPosisi}})</option>
                                                @endforeach
                                            </select>
                                            @error('select_karyawan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror   
                                            <input type='hidden' id='karyawan_id' name='karyawan_id' value="">
                                        </div>                                              
                                        <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="total_hutang">Total Hutang</label>
                                            <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" name="total_hutang" class="form-control numaja uang" id="total_hutang" readonly placeholder="" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class='row'>
                                        <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="potong_hutang">Nominal</label>
                                            <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" name="nominal" class="form-control numaja uang" id="nominal" placeholder="" value="">
                                            </div>
                                        </div>
                                        <div class='col-6 col-md-6 col-lg-6'>
                                            <label for="catatan">Catatan</label>
                                            <input type="text" name="catatan" class="form-control" id="catatan" placeholder="" value=""> 
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="kas_bank_id">Kas / Bank<span style='color:red'>*</span></label>
                                    <select id="select_kas_bank" style="width:100%" data-placeholder="Pilih Kas / Bank">
                                        <option value=''></option>
                                    </select>
                                    <input type='hidden' id='kas_bank_id' name='kas_bank_id' value="">
                                </div> --}}
                                <div class="form-group ">
                                    <label for="kas_bank_id">Kas / Bank<span style='color:red'>*</span></label>
                                    <select class="form-control select2  @error('select_kas_bank') is-invalid @enderror" style="width: 100%;" id='select_kas_bank' name="select_kas_bank">
                                        <option value="">Pilih Kas / Bank</option>
                                        @foreach ($dataKas as $data)
                                            <option 
                                            value="{{$data->id}}" 
                                            {{-- {{1==$data->id?'selected':''}}  --}}
                                            >{{ $data->nama }} </option>
                                        @endforeach
                                    </select>
                                    @error('select_kas_bank')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror   
                                </div> 
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label for="tanggal_mulai">Tanggal Mulai<span style='color:red'>*</span></label>
                                <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" autocomplete="off" name="tanggal_mulai" class="form-control" id="tanggal_mulai" placeholder="dd-M-yyyy" value="">
                                </div>
                            </div>
                           
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="select_kendaraan">Kendaraan<span style="color:red">*</span></label>
                                    <select class="form-control select2  @error('select_kendaraan') is-invalid @enderror" style="width: 100%;" id='select_kendaraan' name="select_kendaraan">
                                    <option value="">Pilih Jenis Kendaraan</option>
                                    @foreach ($dataKendaraan as $data)
                                        <option value="{{$data->id}}" {{old('select_kendaraan')==$data->id?'selected':''}} >{{ $data->no_polisi }} ({{$data->kategoriKendaraan}})</option>
                                    @endforeach
                                </select>
                                @error('select_kendaraan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror   
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="detail_perawatan">Detail Perawatan<span style='color:red'>*</span></label>
                                <textarea rows="4" name="detail_perawatan" class="form-control" id="detail_perawatan" placeholder=""></textarea> 
                            </div>
                        </div> --}}
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

        var cekerror= <?php echo json_encode($errors->any()); ?>;
        if (cekerror) {
                $("#modal_tambah").modal("show");
        }
        $('body').on('change','#select_karyawan',function()
        {
            var id_karyawan = $(this).val();
            var selectedOption = $(this).find('option:selected');
            var karyawan_hutang = selectedOption.attr('karyawan_hutang');
            
            $('#karyawan_id').val(id_karyawan);
            $('#total_hutang').val(karyawan_hutang?addPeriod(karyawan_hutang,','):0);
        });
        $('#tanggal_mulai').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
           // endDate: "0d"
        });
        $('#check_is_selesai').click(function(){
            if($(this).is(":checked")){
                $('#is_selesai').val('Y');
                $('#tanggal_selesai').attr('readonly',false);
                $('#tanggal_selesai').datepicker({
                    autoclose: true,
                    format: "dd-M-yyyy",
                    todayHighlight: true,
                    language:'en'
                });
				$('#tanggal_selesai').val(get_date_now);
				// console.log("Checkbox is checked.");
            }else if($(this).is(":not(:checked)")){
                $('#is_selesai').val('N');
                $('#tanggal_selesai').val('');
                $('#tanggal_selesai').attr('readonly',true);
                $('#tanggal_selesai').datepicker('destroy');
                // console.log("Checkbox is unchecked.");
            }
        });
    // new DataTable('#TabelKlaim', {
    //     order: [
    //         [0, 'asc'],
    //     ],
    //     rowGroup: {
    //         dataSrc: [0]
    //     },
    //     columnDefs: [
    //         {
    //             targets: [0],
    //             visible: false
    //         },
    //         {
    //             "orderable": false,
    //             "targets": [0,1,2,3,4,5,6]
    //         }
    
    //     ],
    // }); 
    $('#post_data').submit(function(event) {
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
        var jenis_bayar = $("input[name='jenis']:checked").val();
                    
        if(jenis_bayar=='')
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `JENIS WAJIB DIPILIH!`,
            })
            
            return;
        }

        if(jenis_bayar=='BAYAR')
        {
            if (normalize($("#nominal").val())>normalize($("#total_hutang").val())) {
                
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `Pembayaran nominal hutang tidak boleh melebihi jumlah hutang karyawan!`,
                })
                
                return;
            }
        }
        if($("#tanggal").val()=='')
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `TANGGAL TRANSAKSI BELUM DIISI!`,
            })
            
            return;
        }
        if($("#karyawan_id").val()=='')
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `KARYAWAN BELUM DIPILIH!`,
            })
            
            return;
        }
        if($("#nominal").val()=='')
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `NOMINAL BELUM DIISI`,
            })
            
            return;
        }
        //  if($("#select_kas_bank").val()=='')
        // {
        //     event.preventDefault(); 
        //     Toast.fire({
        //         icon: 'error',
        //         text: `KAS BANK BELUM DIPILIH`,
        //     })
            
        //     return;
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
                }, 200); // 2000 milliseconds = 2 seconds
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


