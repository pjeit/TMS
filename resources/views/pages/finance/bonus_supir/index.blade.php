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
        <div class="card-body">
            <table id="tabel_transaksi_lain" class="table table-bordered table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Nama Supir</th>
                        <th>Tgl. Transaksi</th>
                        <th>Kas/Bank</th>
                        <th>Nominal Bonus Dicairkan</th>
                        <th>Catatan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($dataBonusSupir))
                        @foreach ($dataBonusSupir as $item)
                        <tr>
                            <td>{{ $item->karyawanIndex->nama_lengkap }} ({{ $item->karyawanIndex->nama_panggilan }})</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y')}} </td>
                            <td>
                                @foreach ($dataKas as $kas)
                                    @if ($kas->id == $item->id_kas_bank)
                                        {{ $kas->nama}}
                                    @endif
                                @endforeach
                            </td>
                            <td>Rp. {{number_format($item->total_pencairan,2)  }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{route('bonus_supir.edit',[$item->id])}}" class="dropdown-item ">
                                            <span class="fas fa-edit mr-3"></span> Edit
                                        </a>
                                        <a href="{{ route('bonus_supir.destroy', [$item->id]) }}" class="dropdown-item" data-confirm-delete="true">
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
    </div>
</div>
<div class="modal fade" id="modal_tambah" >
    <div class="modal-dialog modal-lg ">
        <form action="{{ route('bonus_supir.store') }}" id="post_data" method="POST" >
            @csrf
            <div class="modal-content radiusSendiri">
                <div class="modal-header">
                    <h5 class="modal-title">Form Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                <div class="modal-body">

                    <div class="tab-content">
                        {{-- @if ($errors->any())
                            @foreach ($errors->all() as $error)
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $error }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endforeach

                        @endif --}}

                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}
                        <div class='row'>
                            <div class="col-lg-12">
                                <div class="row">
                        
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="tanggal_pencairan">Tanggal Transaksi<span style="color:red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" autocomplete="off" name="tanggal_pencairan" class="form-control date @error('tanggal_pencairan') is-invalid @enderror" id="tanggal_pencairan" placeholder="dd-M-yyyy" value="{{old('tanggal_pencairan')}}">
                                            @error('tanggal_pencairan')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="select_driver">Pilih Driver<span style="color:red">*</span></label>
                                        <select class="form-control select2 @error('select_driver') is-invalid @enderror" style="width: 100%;" id='select_driver' name="select_driver">
                                            <option value="">Pilih Driver</option>
                                            @foreach ($dataDriver as $data)
                                                <option value="{{$data->id}}" nama_driver="{{$data->nama_lengkap}}({{$data->nama_panggilan}})"
                                                    {{old('select_driver')==$data->id?'selected':''}} >
                                                    {{ $data->nama_lengkap }} ( {{$data->telp1}} )
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="nama_driver_hidden" value="" id="nama_driver_hidden">
                                        @error('select_driver')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror   
                                    </div>
                                
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="select_bank">Kas/Bank<span style="color:red">*</span></label>
                                            <select class="form-control select2  @error('select_bank') is-invalid @enderror" style="width: 100%;" id='select_bank' name="select_bank">
                                            <option value="">Pilih Kas/Bank</option>
                                            @foreach ($dataKas as $data)
                                                <option value="{{$data->id}}" {{1==$data->id?'selected':''}} >{{ $data->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('select_bank')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror   
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="total">Nominal Bonus<span style="color:red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" name="total" class="form-control numaja uang @error('total') is-invalid @enderror" id="total" placeholder="" value="{{old('total')}}">
                                            @error('total')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>  
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="keterangan_klaim">Catatan</label>
                                        <input type="text" class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" value="{{old('catatan')}}">
                                        @error('catatan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>  
                                </div>
                            </div>
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
    // var table = $('#tabel_transaksi_lain').DataTable({
    //         processing: true,
    //         serverSide: true,
    //         ajax: "{{ route('transaksi_lain.index_server') }}",
    //         columns: [
    //             {data: 'tgl_transaksi', name: 'tgl_transaksi'},
    //             {data: 'jenis', name: 'jenis'},
    //             {data: 'kas_bank', name: 'kas_bank'},
    //             {data: 'total_nominal', name: 'total_nominal'},
    //             {data: 'catatan', name: 'catatan'},
    //             {
    //                 data: 'action', 
    //                 name: 'action', 
    //                 orderable: false, 
    //                 searchable: false
    //             },
    //         ],
    //         //  order: [
    //         //         [0, 'asc'],
    //         //     ],
    //         // rowGroup: {
    //         //     dataSrc: ['Supir']//grouping per supir pake nama datanya, kalo bukan serverside nembak index
    //         // },
    //         // columnDefs: [
    //         //     {
    //         //         targets: [0],
    //         //         visible: false
    //         //     },
    //         //     {
    //         //         "orderable": false,
    //         //         "targets": [0,1,2,3,4,5,6,7]
    //         //     }
        
    //         // ],
    //     });
    $('#tanggal_pencairan').datepicker({
        autoclose: true,
        format: "dd-M-yyyy",
        todayHighlight: true,
        language:'en',
        endDate: "0d",
    });
    $('body').on('change','#select_driver',function()
    {
        var selectedOption = $(this).find('option:selected');
        var nama_driver = selectedOption.attr('nama_driver');

        console.log(nama_driver);
        $('#nama_driver_hidden').val(nama_driver);

    });
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

            if($("#tanggal_pencairan").val().trim()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `TANGGAL PENCAIRAN WAJIB DI ISI!`,
                })
                
                return;
            }
            
            if($("#select_bank").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `BANK WAJIB DI PILIH!`,
                })
                return;
            }
            
            if($("#total").val().trim()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `TOTAL NOMINAL WAJIB DI ISI!`,
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


