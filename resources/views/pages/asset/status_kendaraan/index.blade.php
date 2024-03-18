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
                        <th>No.Polisi</th>
                        <th>Tgl.Mulai</th>
                        <th>Detail Perawatan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($dataStatusKendaraan))
                        @foreach ($dataStatusKendaraan as $item)
                        <tr>
                            <td>{{$item->no_polisi}}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-M-Y')}}</td>
                            <td>{{$item->detail_perawatan}}</td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @can('EDIT_STATUS_KENDARAAN')
                                            <a href="{{route('status_kendaraan.edit',[$item->id])}}" class="dropdown-item ">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                        @endcan
                                        @can('DELETE_STATUS_KENDARAAN')
                                            <a href="{{ route('status_kendaraan.destroy', [$item->id]) }}" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Hapus
                                            </a>
                                        @endcan
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
</div>

<div class="modal fade" id="modal_tambah" >
    <div class="modal-dialog modal-lg ">
        <form action="{{ route('status_kendaraan.store') }}" id="post_data" method="POST" >
            @csrf
            <div class="modal-content radiusSendiri">
                <div class="modal-header">
                    <h5 class="modal-title">Form Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                            <label for="tanggal_mulai">Tanggal Mulai<span style='color:red'>*</span></label>
                            <div class="input-group mb-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" autocomplete="off" name="tanggal_mulai" class="form-control" id="tanggal_mulai" placeholder="dd-M-yyyy" value="">
                            </div>
                        </div>
                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                            <label for="tanggal_selesai">Tanggal Selesai</label>
                            <div class="input-group mb-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><input type="checkbox" id="check_is_selesai" ></span>
                            </div>
                            <input type="hidden" id="is_selesai" name='is_selesai' value="N">
                            <input type="text" autocomplete="off" name="tanggal_selesai" class="form-control" id="tanggal_selesai" placeholder="dd-M-yyyy"  readonly value="">
                            </div>
                        </div>
                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
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
                        <div class="form-group col-lg-6 col-md-6 col-sm-12" id="kir_form">
                            <label for="select_driver">Kir ?</label>
                            <br>
                            <div class="icheck-primary d-inline">
                                <input id="kir_Y" type="radio" name="kir" value="Y" >
                                <label class="form-check-label" for="kir_Y">Ya</label>
                            </div>
                            <div class="icheck-primary d-inline ml-5">
                                <input id="kir_N" type="radio" name="kir" value="N" checked>
                                <label class="form-check-label" for="kir_N">Tidak</label><br>
                            </div>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                            <label for="detail_perawatan">Detail Perawatan<span style='color:red'>*</span></label>
                            <textarea rows="4" name="detail_perawatan" class="form-control" id="detail_perawatan" placeholder=""></textarea> 
                        </div>
                    </div>
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

    if($("#select_kendaraan").val()=='')
        {
            event.preventDefault(); 
            Toast.fire({
                icon: 'error',
                text: `KENDARAAN BELUM DIPILIH!`,
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


